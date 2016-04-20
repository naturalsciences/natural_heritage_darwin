<?php

/**
 * search actions.
 *
 * @package    darwin
 * @subpackage search
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class searchActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new PublicSearchFormFilter();
    $this->form->setDefault('search_type','zoo') ;

  }

  public function executeSearchGeo(sfWebRequest $request) {
    $this->form = new PublicSearchFormFilter();
    $this->form->setDefault('search_type','geo') ;
  }
  public function executePurposeTag(sfWebRequest $request)
  {
    $this->tags = Doctrine::getTable('TagGroups')->getPropositions($request->getParameter('value'), 'administrative area', 'country');
  }

  public function executeTree(sfWebRequest $request)
  {
    $this->items = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )
      ->findWithParents($request->getParameter('id'));
  }

  public function executeSearch(sfWebRequest $request)
  {
    // Initialize the order by and paging values: order by collection_name here
    $this->setCommonValues('search', 'collection_name', $request);

    $this->form = new PublicSearchFormFilter();
    // If the search has been triggered by clicking on the search button or with pinned specimens
    if($request->getParameter('specimen_search_filters','') !== '')
    {
      $this->form->bind($request->getParameter('specimen_search_filters')) ;
    }

    if ($this->form->isBound() && $this->form->isValid() && ! $request->hasParameter('criteria'))
    {

      // Get the generated query from the filter and add order criterion to the query
      $query = $this->form->getWithOrderCriteria();

      // Define the pager
      $pager = new DarwinPager($query, $this->form->getValue('current_page'), $this->form->getValue('rec_per_page'));

      // Replace the count query triggered by the Pager to get the number of records retrieved
      $count_q = clone $query;
      // Remove from query the group by and order by clauses
      $count_q = $count_q->select('count(*)')->removeDqlQueryPart('orderby')->limit(0);
      // Initialize an empty count query
      $counted = new DoctrineCounted();
      // Define the correct select count() of the count query
      $counted->count_query = $count_q;
      // And replace the one of the pager with this new one
      $pager->setCountQuery($counted);
      // Define in one line a pager Layout based on a pagerLayoutWithArrows object
      // This pager layout is based on a Doctrine_Pager, itself based on a customed Doctrine_Query object (call to the getExpLike method of ExpeditionTable class)

      $params = $request->isMethod('post') ? $request->getPostParameters() : $request->getGetParameters();

      unset($params['specimen_search_filters']['current_page']);
      $this->pagerLayout = new PagerLayoutWithArrows($pager,
                                                    new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
                                                    'search/search?specimen_search_filters[current_page]={%page_number}&'.http_build_query($params)
                                                    );
      // Sets the Pager Layout templates
      $this->setDefaultPaggingLayout($this->pagerLayout);
      $this->pagerLayout->setTemplate('<li data-page="{%page_number}"><a href="{%url}">{%page}</a></li>');

      // If pager not yet executed, this means the query has to be executed for data loading
      if (! $this->pagerLayout->getPager()->getExecuted())
        $this->search = $this->pagerLayout->execute();
      $this->field_to_show = $this->getVisibleColumns($this->form);
      $this->defineFields();
      $ids = $this->FecthIdForCommonNames() ;
      $this->common_names = Doctrine::getTable('VernacularNames')->findAllCommonNames($ids) ;
      if(!count($this->common_names))
        $this->common_names = array('taxonomy'=> array(), 'chronostratigraphy' => array(), 'lithostratigraphy' => array(),
                                    'lithology' => array(),'mineralogy' => array()) ;
      return;
    }
    if($this->form->isBound() &&  $this->form->getValue('search_type','zoo') != 'zoo')
      $this->setTemplate('searchGeo');
    else
    $this->setTemplate('index');
  }

  public function executeSearchResult(sfWebRequest $request)
  {
    // Do the same as a executeSearch...
    $this->executeSearch($request) ;
    // ... and render partial searchSuccess
    return $this->renderPartial('searchSuccess');
  }

  public function executeView(sfWebRequest $request)
  {
    $this->full = false ;
    if($request->hasParameter('full')) 
    {
        $this->full = true ;
        $this->setLayout('refined');
    }
    $ajax = false ;
    if($request->isXmlHttpRequest())
    {
      $suggestion = $request->getParameter('suggestion') ;
      $captcha = array(
        'recaptcha_challenge_field' => $request->getParameter('recaptcha_challenge_field'),
        'recaptcha_response_field'  => $request->getParameter('recaptcha_response_field'),
      );
      $id = $suggestion['id'] ;
      $ajax = true ;
    }
    else $id = $request->getParameter('id') ;

    $this->forward404Unless(ctype_digit($request->getParameter('id')));
    $this->specimen = Doctrine::getTable('Specimens')->find((int) $request->getParameter('id'));
    $this->comments = Doctrine::getTable('Comments')->getRelatedComment('specimens', (int) $request->getParameter('id'));
    $this->forward404Unless($this->specimen);
    if(!$this->specimen->getCollectionIsPublic()) $this->forwardToSecureAction();

    $collection = Doctrine::getTable('Collections')->findOneById($this->specimen->getCollectionRef());
    $this->institute = Doctrine::getTable('People')->findOneById($collection->getInstitutionRef()) ;
    $this->files = Doctrine::getTable('Multimedia')->findForPublic($this->specimen);
    $this->specFilesCount = $this->taxFilesCount = $this->chronoFilesCount = $this->lithoFilesCount = $this->lithologyFilesCount = $this->mineraloFilesCount = 0;
    foreach($this->files as $file) {
      switch ($file->getReferencedRelation()){
        case 'taxonomy':
          $this->taxFilesCount+=1;
          break;
        case 'chronostratigraphy':
          $this->chronoFilesCount+=1;
          break;
        case 'lithostratigraphy':
          $this->lithoFilesCount+=1;
          break;
        case 'lithology':
          $this->lithologyFilesCount+=1;
          break;
        case 'mineralogy':
          $this->mineraloFilesCount+=1;
          break;
        default:
          $this->specFilesCount+=1;
          break;
      }
    }
    $this->col_manager = Doctrine::getTable('Users')->find($collection->getMainManagerRef());
    $this->col_staff = Doctrine::getTable('Users')->find($collection->getStaffRef());
    $this->manager = Doctrine::getTable('UsersComm')->fetchByUser($collection->getMainManagerRef());
    $this->codes = Doctrine::getTable('Codes')->getCodesRelated('specimens', $this->specimen->getId());
    $this->properties = Doctrine::getTable('Properties')->findForTable('specimens', $this->specimen->getId());

    $ids = $this->FecthIdForCommonNames() ;
    $this->common_names = Doctrine::getTable('VernacularNames')->findAllCommonNames($ids) ;

    if ($tag = $this->specimen->getGtuCountryTagValue()) $this->tags = explode(';',$tag) ;
    else $this->tags = false ;
    $this->form = new SuggestionForm(null,array('ref_id' => $id, 'ajax' => $ajax)) ;
    if($request->isXmlHttpRequest())
    {
      $this->form->bind($suggestion, array('captcha' => $captcha)) ;
      if ($this->form->isBound() && $this->form->isValid())
      {
        $comment = $suggestion['comment'];
        if($suggestion['email'] != '') $comment = $this->getI18N()->__("Suggestion send by")." : ".$suggestion['email']."\n".$suggestion['comment']; ;
        $data = array(
            'referenced_relation' => 'specimens',
            'record_id' => $suggestion['id'],
            'status' => 'suggestion',
            'comment' => $comment,
            'formated_name' => $suggestion['formated_name']!=''?$suggestion['formated_name']:'anonymous'
        );
        $workflow = new InformativeWorkflow() ;
        $workflow->fromArray($data) ;
        $workflow->save() ;
        return $this->renderPartial("info_msg") ;
      }
      return $this->renderPartial("suggestion", array('form' => $this->form,'id'=> $id)) ;
    }
  }

  public function executeGetTaxon (sfWebRequest $request)
  {
  
	$taxa = Doctrine::getTable('Taxonomy')->getOneTaxon($request->getParameter('taxon-name'));
	$taxaCount = count($taxa);
    if ($taxaCount==1) {
	  return $this->renderText($taxa[0]['name']." (".$taxa[0]['Level']['level_name'].")");
	}
	elseif($taxaCount>1) {
	   return $this->renderText('multiple match');
	}
	return $this->renderText('taxon not found');
  
  }

  /**
   * @param \sfWebRequest $request
   */
  public function executeFamilycontent(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id', 0)!==0);
    $familyContent = Doctrine::getTable('Specimens')->getFamilyContent($request->getParameter('id'));
    $family = Doctrine::getTable('Taxonomy')->find($request->getParameter('id'));
    return $this->renderPartial('familycontent',array('familycontent'=>$familyContent, 'family'=>$family));
  }

  
  /**
  * Compute different sources to get the columns that must be showed
  * 1) from form request 2) from session 3) from default value
  * @param sfForm $form The form with the 'fields' field defined
  * @return array of fields with check or uncheck or a list of visible fields separated by |
  */
  private function getVisibleColumns(sfForm $form)
  {
    $flds = array('category','collection','taxon','type','gtu','chrono','taxon_common_name', 'chrono_common_name',
              'litho_common_name','lithologic_common_name','mineral_common_name', 'expedition', 'individual_type',
              'litho','lithologic','mineral','sex','state','stage','social_status','rock_form','specimen_count','object_name');
    $flds = array_fill_keys($flds, 'uncheck');

    if($form->isBound())
    {
      $req_fields = $form->getValue('col_fields');
      if($form->getValue('taxon_common_name') != '' || $form->getValue('taxon_name') != '') $req_fields .= '|taxon|taxon_common_name';
      if($form->getValue('chrono_common_name') != '' || $form->getValue('chrono_name') != '') $req_fields .= '|chrono|chrono_common_name';
      if($form->getValue('litho_common_name') != '' || $form->getValue('litho_name') != '') $req_fields .= '|litho|litho_common_name';
      if($form->getValue('lithology_common_name') != '' || $form->getValue('lithology_name') != '') $req_fields .= '|lithologic|lithology_common_name';
      if($form->getValue('mineral_common_name') != '' || $form->getValue('mineral_name') != '') $req_fields .= '|mineral|mineral_common_name';

      if($form->getValue('search_type','zoo') == 'zoo') {
        if(!strpos($req_fields,'common_name')) {
          $req_fields .= '|taxon|taxon_common_name'; // add taxon by default if there is not other catalogue
        }
      }
      else {
        if(!strpos($req_fields,'common_name')) $req_fields .= '|chrono|litho|lithologic|mineral'; // add cols by default if there is not other catalogue
      }
      $req_fields_array = explode('|',$req_fields);

    }

    if(empty($req_fields_array))
      $req_fields_array = explode('|', $form->getDefault('col_fields'));
    foreach($req_fields_array as $k => $val)
    {
      $flds[$val] = 'check';
    }
    $form->setDefault('col_fields',$req_fields) ;
    return $flds;
  }

  protected function defineFields()
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
      'gtu' => array( ///
        false,
        $this->getI18N()->__('Country'),),
      'chrono' => array(
        'chrono_name_indexed',
        $this->getI18N()->__('Chronostratigraphic unit'),),
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

      'individual_type' => array(
        'type_search',
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
      'specimen_count' => array(
        'specimen_count_max',
        $this->getI18N()->__('Specimen Count'),),

      'object_name' => array(
        'object_name',
        $this->getI18N()->__('Object name'),),

      'taxon_common_name' => array(
        false,
        $this->getI18N()->__('Taxon common name'),),
      'chrono_common_name' => array(
        false,
        $this->getI18N()->__('Chrono common name'),),
      'litho_common_name' => array(
        false,
        $this->getI18N()->__('Litho common name'),),
      'lithologic_common_name' => array(
        false,
        $this->getI18N()->__('Lithologic common name'),),
      'mineral_common_name' => array(
        false,
        $this->getI18N()->__('Mineral common name'),),
    );
  }

  private function FecthIdForCommonNames()
  {
    $tab = array('taxonomy'=> array(), 'chronostratigraphy' => array(), 'lithostratigraphy' => array(), 'lithology' => array(),'mineralogy' => array()) ;
    if(isset($this->search))
    {
      foreach($this->search as $specimen)
      {
        if($specimen->getTaxonRef()) $tab['taxonomy'][] = $specimen->getTaxonRef() ;
        if($specimen->getChronoRef()) $tab['chronostratigraphy'][] = $specimen->getChronoRef() ;
        if($specimen->getLithoRef()) $tab['lithostratigraphy'][] = $specimen->getLithoRef() ;
        if($specimen->getLithologyRef()) $tab['lithology'][] = $specimen->getLithologyRef() ;
        if($specimen->getMineralRef()) $tab['mineralogy'][] = $specimen->getMineralRef() ;
      }
    }
    else
    {
      if($this->specimen->getTaxonRef()) $tab['taxonomy'][] = $this->specimen->getTaxonRef() ;
      if($this->specimen->getChronoRef()) $tab['chronostratigraphy'][] = $this->specimen->getChronoRef() ;
      if($this->specimen->getLithoRef()) $tab['lithostratigraphy'][] = $this->specimen->getLithoRef() ;
      if($this->specimen->getLithologyRef()) $tab['lithology'][] = $this->specimen->getLithologyRef() ;
      if($this->specimen->getMineralRef()) $tab['mineralogy'][] = $this->specimen->getMineralRef() ;
    }
    return $tab ;
  }
}
