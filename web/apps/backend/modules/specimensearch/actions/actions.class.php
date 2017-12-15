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
    else
	{
		$this->form->addGtuTagValue(0);
		$this->form->addPeopleValue(0);
	}
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
   
    $this->search_request=$request;
    
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
          /*
          echo $query->getSqlQuery(); 
          echo "<br/>";
          foreach ($query->getFlattenedParams() as $index => $param)
  echo "$index => $param"."<br/>";
          return;
*/
        
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
          $count_q = clone $query;//$pager->getCountQuery();
          // Remove from query the group by and order by clauses
  
          /*ftheeten 2016 12 06 as it uses  veiw with storage apart*/
         /* $count_q = $count_q->select('count(s.id)')->removeDqlQueryPart('orderby')->limit(0);
          if($this->form->with_group) {
             $count_q->select('count(distinct s.id)')->removeDqlQueryPart('groupby');
          }*/
            $count_q = $count_q->select('count(distinct s.id)')->removeDqlQueryPart('orderby')->limit(0);
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


          //if (! $this->pagerLayout->getPager()->getExecuted())
            $this->specimensearch = $this->pagerLayout->execute();

          //Load Codes and related for each item
          $this->loadRelated();

          $this->field_to_show = $this->getVisibleColumns($this->getUser(), $this->form);
          $this->defineFields($this->source);
 
            //ftheeten 2016 06 08 save query for report (via save searc)
            //$this->queryToSave = $this->form->getQuery();
            $this->setParamsToSaveQuery();
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
  * Load related things for the specimens (code )
  */
  protected function loadRelated()
  {
    $spec_list = array();
    $part_list = array() ;
    //ftheeten 2016 09 22
    //$this->storageParts=array();
    foreach($this->specimensearch as $key=>$specimen){
      $spec_list[] = $specimen->getId() ;
      //ftheeten 2016 09 22
       // $this->storageParts[$specimen->getId() ]=$specimen->getStorageParts();
    }

    $codes_collection = Doctrine::getTable('Codes')->getCodesRelatedMultiple('specimens',$spec_list) ;
    $this->codes = array();
    foreach($codes_collection as $code) {
      if(! isset($this->codes[$code->getRecordId()]))
        $this->codes[$code->getRecordId()] = array();
      $this->codes[$code->getRecordId()][] = $code;
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
     $flds = array('category','collection','taxon', 'collecting_dates', 'type','gtu', 'ecology','codes','chrono','ig','acquisition_category',
              'litho','lithologic','mineral','expedition','type', 'individual_type','sex','state','stage','social_status','rock_form','individual_count',
              'part', 'object_name', 'part_status', 'amount_males', 'amount_females', 'building', 'floor', 'room', 'row', 'col' ,'shelf', 'container', 'container_type',  'container_storage', 'sub_container',
              'sub_container_type' , 'sub_container_storage', 'specimen_count','part_codes', 'col_peoples', 'ident_peoples','don_peoples', 'valid_label', 'loans');



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
  
    public function executeAddPeople(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));

    $form = new SpecimensFormFilter(null,array('user' => $this->getUser()));
    $form->addPeopleValue($number);
    return $this->renderPartial('addPeople',array('form' => $form['Peoples'][$number], 'row_line'=>$number));
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
      //ftheeten 2016 09 13
      'collecting_dates' => array(
	  //ftheeten 2017 12 14
        'gtu_from_date',
        $this->getI18N()->__('Collecting date'),
      ),
      //ftheeten 2016 09 13
      'ecology' => array(
        false,
        $this->getI18N()->__('Ecology'),
      ),
      'codes' => array(
        false,
        $this->getI18N()->__('Codes'),),
        );
        
        //ftheeten 2017 04 28
        if(!$this->getUser()->IsA(Users::REGISTERED_USER)){
         $this->columns = array_merge($this->columns, array(
            'loans' => array(
              'loans',
              $this->getI18N()->__('Loans'),),
        ));
        }
        $this->columns = array_merge($this->columns, array(
            //ftheeten 2017 02 10
           'valid_label' => array(
            'valid_label',
            $this->getI18N()->__('Valid label'),),
            
            //ftheeten 2015 01 11
            'col_peoples' => array(
            'col_peoples',
            $this->getI18N()->__('Collectors'),),
            'ident_peoples' => array(
            'ident_peoples',
            $this->getI18N()->__('Identifiers'),),
            'don_peoples' => array(
            'don_peoples',
            $this->getI18N()->__('Donators'),),
            //
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
            //two fields below ftheeten 2015 03 31
          'amount_males' => array(
            'amount_males',
            $this->getI18N()->__('Amount Males'),),
          'amount_females' => array(
           'amount_females',
            $this->getI18N()->__('Amount Females'),),
            
            // end addition
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
            ));
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
          //ftheeten 
        'storageParts' => array(
          'storageParts',
          $this->getI18N()->__('Storage Parts'),)
          //ftheeten 
        ));
      }
      $this->arrayDisplay=$this->columns;
      ksort($this->arrayDisplay);
      //ksort($this->columns);
  }



 public function executePrint(sfWebRequest $request)
  {
  
		$this->printAndReport($request);   
  }

public function executePrinttemplate(sfWebRequest $request)
  {
		$this->template_type=$request->getParameter("id", "-1");
		return;
  }

  
public function executeSpecimensearchws(sfWebRequest $request)
{

	$this->pagesize=$this->getUser()->fetchRecPerPage();
	$this->page=$request->getParameter("current_page");
	$this->offset=($this->page - 1)* $this->pagesize;
	
	$this->debugstate="0";
	
    $this->is_specimen_search = false;
    // Initialize the order by and paging values: order by collection_name here
    $this->setCommonValues('specimensearch', 'collection_name', $request);
    // Modify the s_url to call the searchResult action when on result page and playing with pager
    $this->form = new SpecimensFormFilter(null,array('user' => $this->getUser()));
	
	$ids_pinned=$this->getUser()->getAllPinned('specimen');
	
    // If the search has been triggered by clicking on the search button or with pinned specimens
    if(($request->isMethod('post') && $request->getParameter('specimen_search_filters','') !== '' ) )
    {
	$this->debugstate="1";
      // Store all post parameters
      $criterias = $request->getPostParameters();
      $criterias['specimen_search_filters']=unserialize($_POST['specimen_search_filters']);
      // If pinned specimens called
      $this->form->bind($criterias['specimen_search_filters']) ;
	 
    }
	 elseif($request->getParameter('search_id','') != '')
    {
		$this->debugstate="2";
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
			if($saved_search->getisOnlyId() && $criterias['specimen_search_filters']['spec_ids']=='')
			  $criterias['specimen_search_filters']['spec_ids'] = '0';
			$this->form->bind($criterias['specimen_search_filters']) ;
		  }
	}
	elseif(isset($ids_pinned))
	{
		$this->debugstate="3";
		$criterias = $request->getPostParameters();
		$ids = implode(',',$this->getUser()->getAllPinned('specimen') );
		$criterias['specimen_search_filters']['spec_ids'] = $ids;
		$this->form->bind($criterias['specimen_search_filters']);
		
	}
	
    if($this->form->isBound())
    {
      if ($this->form->isValid())
      {
        
		
			//ftheeten : placed there to overwrite "order by" settings by setting from the form (otherwise ordrer by is always collection)
			$this->orderBy=$request->getParameter("order_by");
			$this->orderDir=$request->getParameter("order_dir");
          $query = $this->form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir. ', id');
          //If export is defined export it!
          $this->specimensearch = $query->limit($this->pagesize)->offset($this->offset)->execute();
		  // $this->getUser()->setAttribute('specimen_search_session', $this->specimensearch);
		  $this->loadRelated();
		  $this->debugstate="5";
		  
          return ;
      }
	 }
	 
	 
	
}


//ftheeten 2016 01 02
 public function executeReport(sfWebRequest $request)
  {
		$this->printAndReport($request);
		$this->random=sha1(time());
		
		$this->getUser()->setAttribute($this->random, $this->specimensearch);
  }

public function printAndReport(sfWebRequest $request)
  {
  
	  $this->pagesize=$this->getUser()->fetchRecPerPage();
		$this->page=$request->getParameter("current_page");
		$this->offset=($this->page - 1)* $this->pagesize;
		
		$this->debugstate="0";
		
		$this->is_specimen_search = false;
		// Initialize the order by and paging values: order by collection_name here
		$this->setCommonValues('specimensearch', 'collection_name', $request);
		// Modify the s_url to call the searchResult action when on result page and playing with pager
		$this->form = new SpecimensFormFilter(null,array('user' => $this->getUser()));
		//ftheeten 20141215 to print or output pinned specimens in XML format
		$ids_pinned=$this->getUser()->getAllPinned('specimen');
		// If the search has been triggered by clicking on the search button or with pinned specimens
		if(($request->isMethod('post') && $request->getParameter('specimen_search_filters','') !== '' )|| $request->hasParameter('pinned') )
		{
		$this->debugstate="1";
		  // Store all post parameters
		  $criterias = $request->getPostParameters();
		  $criterias['specimen_search_filters']=unserialize($_POST['specimen_search_filters']);
		  // If pinned specimens called
		  //added ftheeten 20141212
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
		 elseif($request->getParameter('search_id','') != '')
		{
			$this->debugstate="2";
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
				if($saved_search->getisOnlyId() && $criterias['specimen_search_filters']['spec_ids']=='')
				  $criterias['specimen_search_filters']['spec_ids'] = '0';
				$this->form->bind($criterias['specimen_search_filters']) ;
			  }
		}		
		elseif(isset($ids_pinned))
		{
			$this->debugstate="3";
			$criterias = $request->getPostParameters();
			$ids = implode(',',$this->getUser()->getAllPinned('specimen') );
			$criterias['specimen_search_filters']['spec_ids'] = $ids;
			$this->form->bind($criterias['specimen_search_filters']);
		
		}
		
		    if($this->form->isBound())
    {
      if ($this->form->isValid())
      {
        //$this->getUser()->storeRecPerPage( $this->form->getValue('rec_per_page'));
        // When criteria parameter is given, it means we go back to criterias
		
			//ftheeten : placed there to overwrite "order by" settings by setting from the form (otherwise ordrer by is always collection)
			$this->orderBy=$request->getParameter("order_by");
			$this->orderDir=$request->getParameter("order_dir");
          $query = $this->form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir. ', id');
          //If export is defined export it!
          $this->specimensearch = $query->limit($this->pagesize)->offset($this->offset)->execute();
			$this->queryDebug=$query;		 
		 // $this->getUser()->setAttribute('specimen_search_session', $this->specimensearch);
		  $this->loadRelated();
		  $this->debugstate="5";
		  
          return ;
      }
	 }

   

 
}

public function executeDownloadws(sfWebRequest $request)
{
	$random=$request->getParameter("sess_id");
	$this->specimenList=$this->getUser()->getAttribute($random);
	//print_r($this->specimenList);
	$this->xsltfile=$request->getParameter("lbltem");
	$this->specimensearchxml=$request->getParameter("tmpData");
	$arrayFile=explode("|",$this->xsltfile);
	if(count($arrayFile)==3)
	{
		$fileXSLT=$arrayFile[0];
		$fileCSV=$arrayFile[1];
		$MIMEextension=$arrayFile[2];
		$this->getResponse()->setHttpHeader("Expires", "Mon, 26 Jul 1997 05:00:00 GMT");
		$this->getResponse()->setHttpHeader("Cache-Control", "no-cache");
		$this->getResponse()->setHttpHeader('Pragma: private', true);
		$this->getResponse()->setHttpHeader('Content-Disposition','attachment; filename="'.$fileCSV.'"');
        $this->getResponse()->setContentType("application/force-download ".$MIMEextension);
        //$this->setTemplate('exportCsv');
        $this->setLayout(false);
		
		
           
    }       
            
	return ;
}


 //ftheeten 2016 06 08
  function setParamsToSaveQuery()
  {
   
    
    if($this->form->getQuery())
    {
        $stringTmp=$this->form->getQuery()->getSqlQuery();
        $stringTmp=substr($stringTmp,  strpos($stringTmp,' FROM ' ));
        $stringTmp=substr($stringTmp, 0, strpos($stringTmp,' LIMIT ' ));
       $this->getUser()->setAttribute('queryToSaveWhere', $stringTmp);
    }
   
    if($this->form->getQuery()->getParams())
    {
        $stringTmp="";
        $arrayTmp=$this->form->getQuery()->getParams();
        if(isset($arrayTmp['where']))
        {
            foreach($arrayTmp['where'] as $key=>$value)
            {
                $stringTmp.=";|".$value."|";
            }
           $this->getUser()->setAttribute('queryToSaveParams', $stringTmp);
        }
    }
    return;
  }
  
  //ftheeten 2016 11 24 for loans
  public function executeSearchByNumAndIG(sfWebRequest $request)
  {}

}
