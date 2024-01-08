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
    $this->tags = Doctrine_Core::getTable('TagGroups')->getPropositions($request->getParameter('value'), 'administrative area', 'country');
  }

  public function executeTree(sfWebRequest $request)
  {
    $this->items = Doctrine_Core::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )
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
   
    if (($this->form->isBound() && $this->form->isValid() && ! $request->hasParameter('criteria'))||$request->getParameter('link_url','') !== '')
    {
       $mode_force=false;
      // Get the generated query from the filter and add order criterion to the query
	  if($request->getParameter('link_url','') !== '')
	  {
		  $query =  Doctrine_Core::getTable('Specimens')->getSpecimensByLink($request->getParameter('link_url')) ;
		  $mode_force=true;
	  }
	  else
	  {
		$query = $this->form->getWithOrderCriteria();

      }
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
      {
        $this->search = $this->pagerLayout->execute();
      }
      $this->field_to_show = $this->getVisibleColumns($this->form, $mode_force);
      $this->defineFields();
      $ids = $this->FecthIdForCommonNames() ;
      //ftheeten 2018 10 29
      $this->fetchCodes();
      $this->common_names = Doctrine_Core::getTable('VernacularNames')->findAllCommonNames($ids) ;
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
    $specimen_set=false;
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
    //ftheeten 2020 01 10
    elseif($request->hasParameter('original_id')) 
    {
        $this->forward404Unless(ctype_digit($request->getParameter('original_id')));
        $stable = Doctrine_Core::getTable('SpecimensStableIds')->findOneBySpecimenRef((int) $request->getParameter('original_id'));
        $this->specimen = Doctrine_Core::getTable('Specimens')->findOneById($stable->getSpecimenRef());       
        $id=$this->specimen->getId();
        $this->comments = Doctrine_Core::getTable('Comments')->getRelatedComment('specimens', $id);
        $specimen_set=true;
    }
    elseif($request->hasParameter('uuid')) 
    {   
        $stable = Doctrine_Core::getTable('SpecimensStableIds')->findOneByUuid($request->getParameter('uuid'));    
        $this->specimen = Doctrine_Core::getTable('Specimens')->findOneById($stable->getSpecimenRef()); 
        $id=$this->specimen->getId();
        $this->comments = Doctrine_Core::getTable('Comments')->getRelatedComment('specimens', $id);
        $specimen_set=true;
    }
    else 
    {
        $id = $request->getParameter('id') ;
    }
    if(!$specimen_set)
    {
        $this->forward404Unless(ctype_digit($request->getParameter('id')));
        $this->specimen = Doctrine_Core::getTable('Specimens')->find((int) $request->getParameter('id'));
        $this->comments = Doctrine_Core::getTable('Comments')->getRelatedComment('specimens', (int) $request->getParameter('id'));
   }
	
    
    $this->forward404Unless($this->specimen);
	$this->forward404Unless(!$this->specimen->getRestrictedAccess());
    if(!$this->specimen->getCollectionIsPublic()) $this->forwardToSecureAction();

    $collection = Doctrine_Core::getTable('Collections')->findOneById($this->specimen->getCollectionRef());
    $this->institute = Doctrine_Core::getTable('People')->findOneById($collection->getInstitutionRef()) ;
    $this->files = Doctrine_Core::getTable('Multimedia')->findForPublic($this->specimen);
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
    $this->col_manager = Doctrine_Core::getTable('Users')->find($collection->getMainManagerRef());
    $this->col_staff = Doctrine_Core::getTable('Users')->find($collection->getStaffRef());
    $this->manager = Doctrine_Core::getTable('UsersComm')->fetchByUser($collection->getMainManagerRef());
    $this->codes = Doctrine_Core::getTable('Codes')->getCodesRelated('specimens', $this->specimen->getId());
    $this->properties = Doctrine_Core::getTable('Properties')->findForTable('specimens', $this->specimen->getId());

    $ids = $this->FecthIdForCommonNames() ;
    $this->common_names = Doctrine_Core::getTable('VernacularNames')->findAllCommonNames($ids) ;

    if ($tag = $this->specimen->getGtuCountryTagValue()) $this->tags = explode(';',$tag) ;
    else $this->tags = false ;
    $this->form = new SuggestionForm(null,array('ref_id' => $id, 'ajax' => $ajax)) ;
    if($request->isXmlHttpRequest())
    {
      //$this->form->bind($suggestion, array('captcha' => $captcha)) ;
      //ftheeten 2018 05 09
      $this->form->bind($suggestion) ;
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

  //madam 2019 04 09
  /*
  public function executeGetTaxon (sfWebRequest $request)
  {
	  $taxa = Doctrine_Core::getTable('Taxonomy')->getOneTaxon($request->getParameter('taxon-name'));
	$taxaCount = count($taxa);
	
	
	$taxaCount = count($taxa);
    if ($taxaCount==1) {
	  return $this->renderText($taxa[0]['name']." (".$taxa[0]['Level']['level_name'].")");
	}
	elseif($taxaCount>1) {
	   return $this->renderText('multiple match');
	}
	return $this->renderText('taxon not found');
  }
  */
  public function executeGetTaxon (sfWebRequest $request)
  {
	$this->getResponse()->setHttpHeader('Content-type','application/json');
    $this->setLayout('json');
	
	$taxa = Doctrine_Core::getTable('Taxonomy')->getOneTaxon($request->getParameter('taxon-name'),$request->getParameter('taxon-level'));
	$taxaCount = count($taxa);
	
    if ($taxaCount==1) {
		$response = array("response" => "unique match","count" => $taxaCount, "results" => $taxa);
		return $this->renderText(json_encode($response));
	}
	elseif($taxaCount>1) {
		$response = array("response" => "multiple match","count" => $taxaCount, "results" => $taxa);
		return $this->renderText(json_encode($response));
	}
	return $this->renderText(json_encode((array("response" => "not found","count" => $taxaCount))));
	
  }

  
  
  /**
   * @param \sfWebRequest $request
   */
  public function executeFamilycontent(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id', 0)!==0);
    $familyContent = Doctrine_Core::getTable('Specimens')->getFamilyContent($request->getParameter('id'));
    $family = Doctrine_Core::getTable('Taxonomy')->find($request->getParameter('id'));
    return $this->renderPartial('familycontent',array('familycontent'=>$familyContent, 'family'=>$family));
  }

  
  /**
  * Compute different sources to get the columns that must be showed
  * 1) from form request 2) from session 3) from default value
  * @param sfForm $form The form with the 'fields' field defined
  * @return array of fields with check or uncheck or a list of visible fields separated by |
  */
  private function getVisibleColumns(sfForm $form, $mode_force=false)
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
	elseif($mode_force)
	{
		return array_fill_keys(array('category','collection','taxon','type','gtu', 'expedition', 'individual_type',
             'state','stage','specimen_count','object_name'), 'check');;
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
        //ftheeten 2018 10 29
     'codes' => array(
        'codes',
        $this->getI18N()->__('Codes'),),
         //ftheeten 2018 10 29
     'ig_num' => array(
        'ig_num',
        $this->getI18N()->__('I.G. num'),),
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

   //ftheeten 2018 10 29
   protected function fetchCodes()
  {
    // Fill in the specimens list that will be given for codes and loans retrieving
    $spec_list = array();
    foreach($this->search as $key=>$specimen){
      $spec_list[] = $specimen->getId() ;
    }

    // codes retrieve and fill of a $this->codes variable (available in the specimen search result template)
    $codes_collection = Doctrine_Core::getTable('Codes')->getCodesRelatedMultiple('specimens',$spec_list) ;
    $this->codes = array();
    foreach($codes_collection as $code) {
      if(! isset($this->codes[$code->getRecordId()]))
        $this->codes[$code->getRecordId()] = array();
      $this->codes[$code->getRecordId()][] = $code;
    }
    
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
  
  //ftheeten 2018 05 30
  public function executeDisableMenu(sfWebRequest $request)
  {
    $flagMenu="on";
    if($request->hasParameter('menu')) 
    {
       if(strtolower($request->getParameter('menu'))=="off")
       {
           $flagMenu="off";
           $_SESSION['menu']= $flagMenu; 
       }
    }
   
    return sfView::NONE;    

  }
  
  
   public function executeCheckReferer(sfWebRequest $request)
  {
    $returned="";
    if(array_key_exists("DW_REFERER", $_SESSION)) 
    {
       $returned=$_SESSION["DW_REFERER"];
    }
    
   $this->getResponse()->setHttpHeader('Content-type','application/json');
   $this->setLayout('json');
   return $this->renderText(json_encode(array("DW_REFERER"=>$returned)));

  }
  
    //ftheeten 2018 06 28
  public function executeCollection(sfWebRequest $request)
  {
      $id=-1;
      $this->all_checked="checked";
        if($request->hasParameter('id'))
        {
            $id= $request->getParameter('id');
            $coll_obj= Doctrine_Core::getTable("Collections")->find($id);
            $this->name="";
            $this->all_checked="";
            if(is_object($coll_obj))
            {                 
                 $this->name=  $coll_obj->getName();
            }
        }
        elseif($request->hasParameter('code'))
        {
            $coll_obj= Doctrine_Core::getTable("Collections")->findOneByCode($request->getParameter('code'));
            $this->all_checked="";
            if(is_object($coll_obj))
            {
                 $id= $coll_obj->getId();
                 $this->name=  $coll_obj->getName();
            }
            
        }
        
        if($request->hasParameter('all'))
        {
            $all= $request->getParameter('all');
            if(strtolower($all)=="on")
            {
                $this->all_checked="checked";
            }
        }
        
        $this->include_sub_checked="checked";
         
        $this->display_sub_checked="";
        if($request->hasParameter('display_sub'))
        {
            $display_sub= $request->getParameter('display_sub');
            if(strtolower($display_sub)=="on")
            {
                $this->display_sub_checked="checked";
            }
        }
        
        $this->display_data_checked="";
        if($request->hasParameter('display_data'))
        {
            $display_data= $request->getParameter('display_data');
            if(strtolower($display_data)=="on")
            {
                $this->display_data_checked="checked";
            }
        }
        
        $this->objects="";
        $this->selection=false;
        if($request->hasParameter('objects'))
        {
            $obj= $request->getParameter('objects');
            if(strlen($obj)>0)
            {
                 $this->selection=true;
                $this->objects=$obj;
            }
        }
        $this->form = new CollectionsStatisticsFormFilter(array("id"=>$id));
        
        return sfView::SUCCESS;
  }
  
   public function executeDisplay_statistics_specimens(sfWebRequest $request)
  {
    $this->getResponse()->setHttpHeader('Content-type','application/json');
    $this->setLayout('json');
    return $this->renderText(json_encode($this->executeDisplay_statistics_specimens_main($request)));
  }
  

  
  public function executeDisplay_statistics_types(sfWebRequest $request)
  {
        $this->getResponse()->setHttpHeader('Content-type','application/json');
        $this->setLayout('json');
        return   $this->renderText(json_encode($this->execute_statistics_generic($request, "types")));
  }
  
  public function executeDisplay_statistics_taxa(sfWebRequest $request)
  {
        $this->getResponse()->setHttpHeader('Content-type','application/json');
        $this->setLayout('json');
        return   $this->renderText(json_encode($this->execute_statistics_generic($request, "taxa")));
  }
  
  public function executeDisplay_all_statistics_csv(sfWebRequest $request)
  {
    $returned=Array();
    
    
    $returned[]="Specimen count";
    $tmp=$this->executeDisplay_statistics_specimens_main($request);
    foreach($tmp as $row)
    {
        $returned[]=implode("\t", $row);
    }
    $returned[]="";
    $returned[]="Type specimen count";
    $tmp=$this->execute_statistics_generic($request, "types");
    foreach($tmp as $row)
    {
        $returned[]=implode("\t", $row);
    }
    $returned[]="";
    $returned[]="Taxa in specimen count";
    $tmp=$this->execute_statistics_generic($request, "taxa");
    foreach($tmp as $row)
    {
        $returned[]=implode("\t", $row);
    }
    $returned[]="";
    
    $this->getResponse()->setHttpHeader('Content-type','text/tab-separated-values');
    $this->getResponse()->setHttpHeader('Content-disposition','attachment; filename="darwin_statistics.txt"');
    $this->getResponse()->setHttpHeader('Pragma', 'no-cache');
    $this->getResponse()->setHttpHeader('Expires', '0');
    
    $this->getResponse()->sendHttpHeaders(); //edited to add the missed sendHttpHeaders
    //$this->getResponse()->setContent($returned);
    $this->getResponse()->sendContent();           
    print(implode("\r\n",$returned));
    return sfView::NONE;           
  }
  
    //ftheeten 2018 07 17
     public function executeCheckTaxonHierarchy(sfWebRequest $request)
	  {
	  
		$isCanonical=false;
		if($request->getParameter('canonical'))
		{
			if(strtolower($request->getParameter('canonical'))=="true"||strtolower($request->getParameter('canonical'))=="yes"||strtolower($request->getParameter('canonical'))=="on")
			{
				$isCanonical=true;
			}
		}
		$results = Doctrine_Core::getTable('Taxonomy')->checkTaxonExisting($request->getParameter('taxon-name'), $isCanonical);

		$this->getResponse()->setContentType('application/json');
		 return  $this->renderText(json_encode($results,JSON_UNESCAPED_SLASHES));
	  
	  }
      
      #2020 01 14
   public function executeDoiview(sfWebRequest $request)
   {
        $spec_ids= $this->getIDFromCollectionNumber($request);
        if(count($spec_ids>0))
        {
            $spec_id=$spec_ids[0];
            $count=count($spec_ids);
        }
        else
        {
            $spec_id=-1;
            $count=0;   
        }
        $this->specimen = Doctrine_Core::getTable('Specimens')->find((int) $request->getParameter('id'));
        $this->count= 1;
        
        $this->getResponse()->setHttpHeader('Content-type','application/xml');
        return $this->renderText($this->specimen->getXMLDataCite());
   }
  
}
