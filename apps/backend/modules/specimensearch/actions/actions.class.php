<?php

/**
 * specimensearch actions.
 *
 * @package    darwin
 * @subpackage specimensearch
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class specimensearchActions extends DarwinActions
{
  protected $widgetCategory = 'specimensearch_widget';

  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new SpecimensFormFilter($request->getParameter('specimen_search_filters'),array('user' => $this->getUser()));

    $this->form->setDefault('rec_per_page',$this->getUser()->fetchRecPerPage());

    // if Parameter name exist, so the referer is mysavedsearch
    if ($request->getParameter('search_id','') != '')
    {
      $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('search_id'), $this->getUser()->getId()) ;
      $criterias = $saved_search->getUnserialRequest();

      $this->fields = $saved_search->getVisibleFieldsInResultStr();
      Doctrine::getTable('Specimens')->getRequiredWidget($criterias['specimen_search_filters'], $this->getUser()->getId(), 'specimensearch_widget');
      $this->form->bind($criterias['specimen_search_filters']) ;
    }
    else $this->form->addGtuTagValue(0);
    //loadwidget at the end because we possibliy need to update some widget visibility before showing it
    $this->loadWidgets();
  }

  /**
    * Action executed when searching a specimen - trigger by the click on the search button
    * It's also the same action that is used to open a saved search reopened, a list of pinned specimens
    * or when clicking on the back to criterias button
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeSearch(sfWebRequest $request)
  {
    $this->is_specimen_search = false;
    // Initialize the order by and paging values: order by collection_name here
    $this->setCommonValues('specimensearch', 'collection_name', $request);
    // Modify the s_url to call the searchResult action when on result page and playing with pager
    $this->s_url = 'specimensearch/search'.'?is_choose='.$this->is_choose;
    // Initialize filter
    $this->form = new SpecimensFormFilter(null,array('user' => $this->getUser()));
    // If the search has been triggered by clicking on the search button or with pinned specimens
    if(($request->isMethod('post') && $request->getParameter('specimen_search_filters','') !== '' ) || $request->hasParameter('pinned') )
    {
      // Store all post parameters
      $criterias = $request->getPostParameters();
      // If pinned specimens called
      if($request->hasParameter('pinned'))
      {
        // Get all ids pinned
        $ids = implode(',',$this->getUser()->getAllPinned('specimen') );
        if($ids == '')
          $ids = '0';
        $this->is_pinned_only_search = true;
        // Set the list of ids as criteria
        $criterias['specimen_search_filters']['spec_ids'] = $ids;

      }
      // If instead it's a call to a stored specimen search
      elseif($request->hasParameter('spec_search'))
      {
        // Get the saved search concerned
        $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('spec_search'), $this->getUser()->getId());
        // Forward 404 if we don't get the search requested
        $this->forward404Unless($saved_search);

        $criterias['specimen_search_filters']['spec_ids'] = $saved_search->getSearchedIdString();
        if($criterias['specimen_search_filters']['spec_ids'] == '')
          $criterias['specimen_search_filters']['spec_ids'] = '0';
        $this->is_specimen_search = $saved_search->getId();
      }
      $this->form->bind($criterias['specimen_search_filters']) ;
    }
    // If search_id parameter is given it means we try to open an already saved search with its criterias
    elseif($request->getParameter('search_id','') != '')
    {
      // Get the saved search asked
      $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('search_id'), $this->getUser()->getId()) ;
      // If not available, not found -> forward on 404 page
      $this->forward404Unless($saved_search);

      if($saved_search->getIsOnlyId())
        $this->is_specimen_search = $saved_search->getId();
      // Get all search criterias from DB
      $criterias = $saved_search->getUnserialRequest();
      // Transform all visible fields stored as a string with | as separator and store it into col_fields field
      $criterias['specimen_search_filters']['col_fields'] = implode('|',$saved_search->getVisibleFieldsInResult()) ;
      // If data were set, in other terms specimen_search_filters array is available...
      if(isset($criterias['specimen_search_filters']))
      {
        // Bring all the required/necessary widgets on page
        Doctrine::getTable('Specimens')->getRequiredWidget($criterias['specimen_search_filters'], $this->getUser()->getId(), 'specimensearch_widget');
        if($saved_search->getisOnlyId() && $criterias['specimen_search_filters']['spec_ids']=='')
          $criterias['specimen_search_filters']['spec_ids'] = '0';
        $this->form->bind($criterias['specimen_search_filters']) ;
      }
    }

    if($this->form->isBound())
    {
      if ($this->form->isValid())
      {
        $this->getUser()->storeRecPerPage( $this->form->getValue('rec_per_page'));
        // When criteria parameter is given, it means we go back to criterias
        if($request->hasParameter('criteria'))
        {
          $this->setTemplate('index');
          // Bring all the required/necessary widgets on page
          Doctrine::getTable('Specimens')->getRequiredWidget($criterias['specimen_search_filters'], $this->getUser()->getId(), 'specimensearch_widget');
          $this->loadWidgets();
          return;
        }
        else
        {
          $this->spec_lists = Doctrine::getTable('MySavedSearches')
            ->getListFor($this->getUser()->getId(), 'specimen');
          $query = $this->form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir. ', id');
          //If export is defined export it!
          $this->field_to_show = $this->getVisibleColumns($this->getUser(), $this->form);

          if($request->getParameter('export','') != '')
          {
            $this->specimensearch = $query->limit(1000)->execute();
            $this->setLayout(false);
            $this->loadRelated();
            $this->getResponse()->setHttpHeader('Pragma: private', true);
            $this->getResponse()->setHttpHeader('Content-Disposition',
                            'attachment; filename="export.csv"');
            $this->getResponse()->setContentType("application/force-download text/csv");
            $this->setTemplate('exportCsv');
            return ;
          }
          // Define in one line a pager Layout based on a pagerLayoutWithArrows object
          // This pager layout is based on a Doctrine_Pager, itself based on a customed Doctrine_Query object (call to the getExpLike method of ExpeditionTable class)
          $pager = new DarwinPager($query,
            $this->currentPage,
            $this->form->getValue('rec_per_page')
          );
          // Replace the count query triggered by the Pager to get the number of records retrieved
          $count_q = clone $query;
          // Remove from query the group by and order by clauses
          $count_q = $count_q->select('count(s.id)')->removeDqlQueryPart('orderby')->limit(0);
          if($this->form->with_group) {
             $count_q->select('count(distinct s.id)')->removeDqlQueryPart('groupby');
          }

          // Initialize an empty count query
          $counted = new DoctrineCounted();
          // Define the correct select count() of the count query
          $counted->count_query = $count_q;
          // And replace the one of the pager with this new one
          $pager->setCountQuery($counted);
          $this->pagerLayout = new PagerLayoutWithArrows($pager,
                                                        new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
                                                        $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
                                                        );
          // Sets the Pager Layout templates
          $this->setDefaultPaggingLayout($this->pagerLayout);
          // If pager not yet executed, this means the query has to be executed for data loading
          if (! $this->pagerLayout->getPager()->getExecuted())
            $this->specimensearch = $this->pagerLayout->execute();

          //Load Codes and Loans and related for each item
          $this->loadRelated();
          $this->field_to_show = $this->getVisibleColumns($this->getUser(), $this->form);
          $this->defineFields($this->source);
          return $request->isXmlHttpRequest()? $this->renderPartial('searchSuccess'): null;
        }
      }
    }

    $this->setTemplate('index');
    if(isset($criterias['specimen_search_filters']))
      Doctrine::getTable('Specimens')->getRequiredWidget($criterias['specimen_search_filters'], $this->getUser()->getId(), 'specimensearch_widget');
    $this->loadWidgets();
  }

  /**
  * Load related things for the specimens (code, loans related)
  */
  protected function loadRelated()
  {
    // Fill in the specimens list that will be given for codes and loans retrieving
    $spec_list = array();
    foreach($this->specimensearch as $key=>$specimen){
      $spec_list[] = $specimen->getId() ;
    }

    // codes retrieve and fill of a $this->codes variable (available in the specimen search result template)
    $codes_collection = Doctrine::getTable('Codes')->getCodesRelatedMultiple('specimens',$spec_list) ;
    $this->codes = array();
    foreach($codes_collection as $code) {
      if(! isset($this->codes[$code->getRecordId()]))
        $this->codes[$code->getRecordId()] = array();
      $this->codes[$code->getRecordId()][] = $code;
    }

    // loans retrieve and fill of a $this->loans variable (available in the the specimen search result template)
    $loans_collection = Doctrine::getTable('Loans')->getLoansRelatedArray($this->getUser(), $spec_list);
    $this->loans = array();
    foreach($loans_collection as $loan) {
      if ($loan['loans_count'] > 1) {
        $loan_refs = preg_split('/\n/', $loan['loans_ref']);
        $loan_names = preg_split('/\n/', $loan['loans_name']);
        $loan_status = preg_split('/\n/', $loan['loans_status']);
        $loan_status_tooltip = preg_split('/\n/', $loan['loans_status_tooltip']);
        $loan_status_class = preg_split('/\n/', $loan['loans_status_class']);
        $loan['specimen_infos'] = array();
        foreach ($loan_refs as $key => $value) {
          $loan['specimen_infos'][$key]['id'] = $value;
          $loan['specimen_infos'][$key]['name'] = $loan_names[$key];
          $loan['specimen_infos'][$key]['status'] = $loan_status[$key];
          $loan['specimen_infos'][$key]['status_tooltip'] = $loan_status_tooltip[$key];
          $loan['specimen_infos'][$key]['status_class'] = $loan_status_class[$key];
        }
      }
      if(! isset($this->loans[$loan['specimen_id']])) {
        $this->loans[ $loan['specimen_id'] ] = array ();
      }
      $this->loans[$loan['specimen_id']][] = $loan;
    }
  }

  /**
  * Compute different sources to get the columns that must be showed
  * 1) from form request 2) from session 3) from default value
  * @param sfBasicSecurityUser $user the user
  * @param sfForm $form The filter form with the 'col_fields' field defined
  * @param bool $as_string specify if you want the return to be a string (concat of visible cols)
  * @return array of fields with check or uncheck or a list of visible fields separated by |
  */
  private function getVisibleColumns(sfBasicSecurityUser $user, sfForm $form, $as_string = false)
  {
    $flds = array('category','collection','taxon','type','gtu','codes','chrono','ig','acquisition_category',
              'litho','lithologic','mineral','expedition','type', 'individual_type','sex','state','stage','social_status','rock_form','individual_count',
              'part', 'object_name', 'part_status', 'building', 'floor', 'room', 'row', 'col' ,'shelf', 'container', 'container_type',  'container_storage', 'sub_container',
              'sub_container_type' , 'sub_container_storage', 'specimen_count','part_codes', 'loans');


    $flds = array_fill_keys($flds, 'uncheck');

    if($form->isBound() && $form->getValue('col_fields') != "")
    {
      $req_fields = $form->getValue('col_fields');
      $req_fields_array = explode('|',$req_fields);

    }
    else
    {
      $req_fields_array = $user->fetchVisibleCols();
    }

    if(empty($req_fields_array))
      $req_fields_array = explode('|', $form->getDefault('col_fields'));
    if($as_string)
    {
      return  implode('|',$req_fields_array);
    }

    foreach($req_fields_array as $k => $val)
    {
      $flds[$val] = 'check';
    }
    return $flds;
  }

  public function executeAndSearch(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));

    $form = new SpecimensFormFilter(null,array('user' => $this->getUser()));
    $form->addGtuTagValue($number);
    return $this->renderPartial('andSearch',array('form' => $form['Tags'][$number], 'row_line'=>$number));
  }

  public function executeAddCode(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));

    $form = new SpecimensFormFilter(null,array('user' => $this->getUser()));
    $form->addCodeValue($number);
    return $this->renderPartial('specimensearchwidget/codeline',array('code' => $form['Codes'][$number], 'row_line'=>$number));
  }

  protected function defineFields($source)
  {
    $this->columns = array(
      'category' => array(
        'category',
        $this->getI18N()->__('Category'),),
      'collection' => array(
        'collection_name',
        $this->getI18N()->__('Collection'),),
      'taxon' => array(
        'taxon_name_indexed',
        $this->getI18N()->__('Taxon'),),
      'gtu' => array(
        false,
        $this->getI18N()->__('Sampling locations'),),
      'codes' => array(
        false,
        $this->getI18N()->__('Codes'),),
      'chrono' => array(
        'chrono_name_indexed',
        $this->getI18N()->__('Chronostratigraphic unit'),),
      'ig' => array(
        'ig_num_indexed',
        $this->getI18N()->__('I.G. unit'),),
      'litho' => array(
        'litho_name_indexed',
        $this->getI18N()->__('Lithostratigraphic unit'),),
      'lithologic' => array(
        'lithology_name_indexed',
        $this->getI18N()->__('Lithologic unit'),),
      'mineral' => array(
        'mineral_name_indexed',
        $this->getI18N()->__('Mineralogic unit'),),
      'expedition' => array(
        'expedition_name_indexed',
        $this->getI18N()->__('Expedition'),),
      'acquisition_category' => array(
        'acquisition_category',
        $this->getI18N()->__('Acquisition category'),),

      'individual_type' => array(
        'type_group',
        $this->getI18N()->__('Type'),),
      'sex' => array(
        'sex',
        $this->getI18N()->__('Sex'),),
      'state' => array(
        'state',
        $this->getI18N()->__('State'),),
      'stage' => array(
        'stage',
        $this->getI18N()->__('Stage'),),
      'social_status' => array(
        'social_status',
        $this->getI18N()->__('Social Status'),),
      'rock_form' => array(
        'rock_form',
        $this->getI18N()->__('Rock Form'),),
        );
    if($this->getUser()->IsA(Users::REGISTERED_USER)){
      $this->columns = array_merge($this->columns, array(
        'part' => array(
          'specimen_part',
          $this->getI18N()->__('Part'),),
        'object_name' => array(
          'object_name',
          $this->getI18N()->__('Object name'),),
        'part_status' => array(
          'specimen_status',
          $this->getI18N()->__('Part Status'),),
        'specimen_count' => array(
          'specimen_count_max',
          $this->getI18N()->__('Specimen count'),),
        ));
    } else {
      $this->columns = array_merge($this->columns, array(
        'part' => array(
          'specimen_part',
          $this->getI18N()->__('Part'),),
        'object_name' => array(
          'object_name',
          $this->getI18N()->__('Object name'),),
        'part_status' => array(
          'specimen_status',
          $this->getI18N()->__('Part Status'),),
        'building' => array(
          'building',
          $this->getI18N()->__('Building'),),
        'floor' => array(
          'floor',
          $this->getI18N()->__('Floor'),),
        'room' => array(
          'room',
          $this->getI18N()->__('Room'),),
        'row' => array(
          'row',
          $this->getI18N()->__('Row'),),
        'col' => array(
          'col',
          $this->getI18N()->__('column'),),
        'shelf' => array(
          'shelf',
          $this->getI18N()->__('Shelf'),),

        'container' => array(
          'container',
          $this->getI18N()->__('Container'),),
        'container_type' => array(
          'container_type',
          $this->getI18N()->__('Container Type'),),
        'container_storage' => array(
          'container_storage',
          $this->getI18N()->__('Container Storage'),),
        'sub_container' => array(
          'sub_container',
          $this->getI18N()->__('Sub Container'),),
        'sub_container_type' => array(
          'sub_container_type',
          $this->getI18N()->__('Sub Container Type'),),
        'sub_container_storage' => array(
          'sub_container_storage',
          $this->getI18N()->__('Sub Container Storage'),),
        'specimen_count' => array(
          'specimen_count_max',
          $this->getI18N()->__('Specimen Count'),),
        'loans' => array(
          false,
          $this->getI18N()->__('Loans'),),
        ));
      }
  }
}
