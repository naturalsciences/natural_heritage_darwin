<?php
  error_reporting(E_ERROR | E_PARSE);
  
class DarwinActions extends sfActions
{

  protected static $correspondingTable = array (
    'specimens'=>'Specimens',
    'specimen_individuals'=>'SpecimenIndividuals',
    'specimen_parts'=>'SpecimenParts',
    'loans'=>'Loans',
    'loan_items'=>'LoanItems',
    'collection_maintenance' => 'CollectionMaintenance'
  );

  protected function getSpecificForm(sfWebRequest $request, $options=null)
  {
    $tableRecord = null;

    $this->forward404Unless($request->hasParameter('table') && array_key_exists ($request->getParameter('table',''),self::$correspondingTable));

    if($request->hasParameter('id'))
      $tableRecord = Doctrine_Core::getTable(self::$correspondingTable[$request->getParameter('table')])->find($request->getParameter('id',0));

    if($request->getParameter('table','')== 'loans')
    {
      $form = new LoansForm($tableRecord,$options);
    }
    elseif ($request->getParameter('table','')== 'loan_items')
    {
      $form = new LoanItemWidgetForm($tableRecord,$options);
    }
    elseif ($request->getParameter('table','')== 'collection_maintenance')
    {
      $form = new MaintenanceForm($tableRecord,$options);
    }
    elseif ($request->getParameter('table','')== 'specimens')
    {
      $form = new SpecimensForm($tableRecord,$options);
    }
    elseif ($request->getParameter('table','')== 'specimen_individuals')
    {
      $form = new SpecimenIndividualsForm($tableRecord,$options);
    }
    elseif ($request->getParameter('table','')== 'specimen_parts')
    {
      $form = new SpecimenPartsForm($tableRecord,$options);
    }
    return $form;
  }

  protected function setCommonValues($moduleName, $defaultOrderByField, sfWebRequest $request)
  {
    // Define all properties that will be either used by the data query or by the pager
    // They take their values from the request. If not present, a default value is defined
    $this->pagerSlidingSize = intval(sfConfig::get('dw_pagerSlidingSize'));
    $this->currentPage = ($request->getParameter('page', '') == '')? 1: $request->getParameter('page');
    $this->is_choose = ($request->getParameter('is_choose', '') == '')?0:intval($request->getParameter('is_choose'));
    $this->orderBy = ($request->getParameter('orderby', '') == '')?$defaultOrderByField:$request->getParameter('orderby');
    $this->orderDir = ($request->getParameter('orderdir', '') == '' || $request->getParameter('orderdir') == 'asc') ? 'asc' : 'desc';

    $this->s_url = $moduleName.'/search'.'?is_choose='.$this->is_choose;
    $this->o_url = '&orderby='.$this->orderBy.'&orderdir='.$this->orderDir;
  }

  protected function setDefaultPaggingLayout(PagerLayoutWithArrows $pagerLayout)
  {
    $pagerLayout->setTemplate('<li><a href="{%url}">{%page}</a></li>');
    $pagerLayout->setSelectedTemplate('<li>{%page}</li>');
    $pagerLayout->setSeparatorTemplate('<span class="pager_separator">::</span>');
  }
  
  protected function setLevelAndCaller(sfWebRequest $request)
  {
    $this->level = (!$request->hasParameter('level'))?'':$request->getParameter('level');
    $this->caller_id = (!$request->hasParameter('caller_id'))?'':$request->getParameter('caller_id');
  }

  protected function setPeopleRole(sfWebRequest $request)
  {
    $this->only_role = (!$request->hasParameter('only_role'))?'0':$request->getParameter('only_role');
  }

  protected function loadWidgets($id = null,$collection = null)
  {
    $this->__set('widgetCategory',$this->widgetCategory);
    if($id === null) {
      $id = $this->getUser()->getId();
    }
    $this->widgets = Doctrine_Core::getTable('MyWidgets')
      ->setUserRef($id)
      ->setDbUserType($this->getUser()->getDbUserType())
      ->getWidgets($this->widgetCategory, $collection);
    $this->widget_list = Doctrine_Core::getTable('MyWidgets')->sortWidgets($this->widgets, $this->getI18N());
    if(! $this->widgets) $this->widgets=array();   
  }

  protected function getI18N()
  {
     return sfContext::getInstance()->getI18N();
  }

  /**
   * Forwards the current request to the secure action.
   *
   * Copied from sfBasicSecurityFilter
   *
   * @see lib/vendor/symfony/lib/filter/sfBasicSecurityFilter.class.php
   * @throws sfStopException
   */
  public function forwardToSecureAction()
  {
    sfContext::getInstance()->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    $this->getResponse()->setStatusCode(403);
    throw new sfStopException();
  }

  protected function getRecordIfDuplicate($id , $obj, $is_spec = false)
  {
    if ($id)
    {
      $check = $obj->getTable()->find($id);
      if(!$check) return $obj ;
      if($is_spec)
      {
        $check->SpecimensMethods->count() ;
        $check->SpecimensTools->count() ;
      }
      $record = $check->toArray(true);
      unset($record['id']) ;
      $obj->fromArray($record,true) ;
      switch(get_class($obj))
      {
       case 'Expeditions' : 
        $obj->setExpeditionFromDate(new FuzzyDateTime($check->getExpeditionFromDate(),$check->getExpeditionFromDateMask()) );
        $obj->setExpeditionToDate(new FuzzyDateTime($check->getExpeditionToDate(),$check->getExpeditionToDateMask()) );
        break ; 
       case 'People' :            
        $obj->setBirthDate(new FuzzyDateTime($check->getBirthDate(),$check->getBirthDateMask()) );
        $obj->setEndDate(new FuzzyDateTime($check->getEndDate(),$check->getEndDateMask()) );
        $obj->setActivityDateFrom(new FuzzyDateTime($check->getActivityDateFrom(),$check->getActivityDateFromMask()) );
        $obj->setActivityDateTo(new FuzzyDateTime($check->getActivityDateTo(),$check->getActivityDateToMask()) );
        break ;
       case 'Gtu' :
        $obj->setGtuFromDate(new FuzzyDateTime($check->getGtuFromDate(),$check->getGtuFromDateMask()) );
        $obj->setGtuToDate(new FuzzyDateTime($check->getGtuToDate(),$check->getGtuToDateMask()) );
        break ;
       case 'Igs' :
        $obj->setIgDate(new FuzzyDateTime($check->getIgDate(),$check->getIgDateMask()) );
        break ;
       case 'Specimens' :
        $obj->setAcquisitionDate(new FuzzyDateTime($check->getAcquisitionDate(),$check->getAcquisitionDateMask()) );
        break ;
       default: break ;
      }
    }
    return $obj ;
  }
  
  protected function executeDisplay_statistics_specimens_main(sfWebRequest $request)
  {
    $idCollections="/";
    $year="";
    $creation_date_min="";
    $creation_date_max="";
    $ig_num="";
    $includeSubcollection=false;
    $detailSubCollections=false;
    if($request->hasParameter("collectionids"))
    {
        $idCollections=$request->getParameter("collectionids");
    }
    
    if($request->hasParameter("ig_num"))
    {
        $ig_num=$request->getParameter("ig_num");
    }
    
    if($request->hasParameter("year"))
    {
        $year=$request->getParameter("year");
    }
    
    if($request->hasParameter("creation_date_min"))
    {
          $creation_date_min=$request->getParameter("creation_date_min");
       
    }
    
    if($request->hasParameter("creation_date_max"))
    {
        $creation_date_max=$request->getParameter("creation_date_max");
    }
    
    if($request->hasParameter("withdetails"))
    {
        if(strtolower($request->getParameter("withdetails")=="on")||strtolower($request->getParameter("withdetails")=="true"))
        {
            $detailSubCollections=true;
        }
    }
    
    if($request->hasParameter("withsubcollections"))
    {
        if(strtolower($request->getParameter("withsubcollections"))=="on"||strtolower($request->getParameter("withsubcollections"))=="true")
        {
            $includeSubcollection=true;
        }
    }
    
    $items=Doctrine_Core::getTable('Collections')->countSpecimens($idCollections, $year,$creation_date_min, $creation_date_max, $ig_num, $includeSubcollection, $detailSubCollections) ;
     
    if(count($items)>1)
    {
        
        $sum=Array();
        
        $sum_records=0;
        $sum_batch_low=0;
        $sum_batch_high =0;
        $keyField="";
        $flagFindKeyfield=false;
        foreach($items as $item)
        {            
            if($flagFindKeyfield===false)
            {  
                foreach($item as $field=> $value)
                {
                    $keyField=$field;
                    $flagFindKeyfield=true;
                    break;
                }
            }
            
            $sum_records+=$item["nb_database_records"];
            $sum_batch_low+=$item["nb_physical_specimens_low"];
            $sum_batch_high+=$item["nb_physical_specimens_high"];
            $key++;
        }
        $sum[$keyField]="TOTAL";
        $sum["nb_database_records"]=$sum_records;
        $sum["nb_physical_specimens_low"]=$sum_batch_low;
        $sum["nb_physical_specimens_high"]=$sum_batch_high;
        $items[]=$sum;
        
    }
  
    return $items;
    
  }
  
  //ftheeten 2018 04 30
   protected function execute_statistics_generic(sfWebRequest $request, $table_name)
  {
    $idCollections="/";
    $year="";
    $creation_date_min="";
    $creation_date_max="";
    $ig_num="";
    $includeSubcollection=false;
    $detailSubCollections=false;
    if($request->hasParameter("collectionids"))
    {
        $idCollections=$request->getParameter("collectionids");
    }
    
    if($request->hasParameter("ig_num"))
    {
        $ig_num=$request->getParameter("ig_num");
    }
    
    if($request->hasParameter("year"))
    {
        $year=$request->getParameter("year");
    }
    
    if($request->hasParameter("creation_date_min"))
    {
          $creation_date_min=$request->getParameter("creation_date_min");
       
    }
    
    if($request->hasParameter("creation_date_max"))
    {
        $creation_date_max=$request->getParameter("creation_date_max");
    }
    
    if($request->hasParameter("withdetails"))
    {
        if(strtolower($request->getParameter("withdetails")=="on")||strtolower($request->getParameter("withdetails")=="true"))
        {
            $detailSubCollections=true;
        }
    }
    
    if($request->hasParameter("withsubcollections"))
    {
        if(strtolower($request->getParameter("withsubcollections"))=="on"||strtolower($request->getParameter("withsubcollections"))=="true")
        {
            $includeSubcollection=true;
        }
    }
    if($table_name=="types")
    {
        $items=Doctrine_Core::getTable('Collections')->countTypeSpecimens($idCollections, $year,$creation_date_min, $creation_date_max, $ig_num, $includeSubcollection, $detailSubCollections) ;
    }
    elseif($table_name=="taxa")
    {
        $items=Doctrine_Core::getTable('Collections')->countTaxaInSpecimen($idCollections, $year,$creation_date_min, $creation_date_max, $ig_num, $includeSubcollection, $detailSubCollections) ;
    }
    if(count($items)>1)
    {
        
        $sum=Array();
        
        $sum_records=0;
        $sum_batch_low=0;
        $sum_batch_high =0;
        $keyField="";
        $flagFindKeyfield=false;
        foreach($items as $item)
        {
            if($flagFindKeyfield===false)
            {  
                foreach($item as $field=> $value)
                {
                    $keyField=$field;
                    $flagFindKeyfield=true;
                    break;
                }
            }
            $sum_records+=$item["nb_database_records"];
            if($table_name=="types")
            {
                $sum_batch_low+=$item["nb_physical_specimens_low"];
                $sum_batch_high+=$item["nb_physical_specimens_high"];
            }
        }
        $sum[$keyField]="TOTAL";
        $sum["nb_database_records"]=$sum_records;
        if($table_name=="types")
        {
            $sum["nb_physical_specimens_low"]=$sum_batch_low;
            $sum["nb_physical_specimens_high"]=$sum_batch_high;
        }
        $items[]=$sum;
        
    }
    
    return $items;
    
  }
  
      //ftheeten 2017 10 09	
	protected function getIDFromCollectionNumber(sfWebRequest $request)
	{
		
		 //ftheeten 2017 10 09
		$tmp_id=NULL;
		$initialized=false;
        //2020 01 10
        if(null!==($request->getParameter('original_id')))
		{
			if(strlen($request->getParameter('original_id')))
			{			  
              $stable = Doctrine_Core::getTable('SpecimensStableIds')->findOneBySpecimenRef((int) $request->getParameter('original_id'));
              $this->specimen = Doctrine_Core::getTable('Specimens')->findOneById($stable->getSpecimenRef());
              $tmp_id=Array($this->specimen->getId());
			  $initialized=true;
			}
		}
        elseif(null!==($request->getParameter('uuid')))
		{
			if(strlen($request->getParameter('uuid')))
			{
			  $stable = Doctrine_Core::getTable('SpecimensStableIds')->findOneByUuid($request->getParameter('uuid'));
              $this->specimen = Doctrine_Core::getTable('Specimens')->findOneById($stable->getSpecimenRef());
              $tmp_id=Array($this->specimen->getId());
			  $initialized=true;
			}
		}
		elseif(null!==($request->getParameter('id')))
		{
			if(strlen($request->getParameter('id')))
			{
			  $tmp_id=Array($request->getParameter('id'));
			  $initialized=true;
			}
		}
		if($initialized==false)
		{
			if(null!==($request->getParameter('specimennumber')))
			{
				if(strlen($request->getParameter('specimennumber')))
				{	
					$tmp_id=Doctrine_Core::getTable('Specimens')->getSpecimenIDCorrespondingToMainCollectionNumber($request->getParameter('specimennumber'));
					$initialized=true;
				}
			}
			
		}
		return $tmp_id;
	}
    
     //ftheeten 2017 11 24
    protected function getSpecimenJSON(sfWebRequest $request)
    {
    
        if($request->hasParameter('specimennumber'))
		{
       
            $results=Doctrine_Core::getTable('Specimens')->getJSON("NUMBER",$request->getParameter('specimennumber'));
            
            return  $results;
            
        }
        elseif($request->hasParameter('id'))
		{
       
            $results=Doctrine_Core::getTable('Specimens')->getJSON("ID",$request->getParameter('id'));
            
            return  $results;
            
        }
        return Array();
    }
    
    //ftheeten 2017 11 24
    protected function getCollectionJSON(sfWebRequest $request)
    {
    
         
        
        $size=50; 
        $page=1;
       
        if($request->hasParameter('collection'))
		{
 
            $collection_code=$request->getParameter('collection');
            $host=$request->getHost();
            $prefix_service="/public.php/search/getjson?specimennumber=" ;            
           
            if($request->hasParameter('size'))
            {
           
                 $size=$request->getParameter('size');
            }
             if($request->hasParameter('page'))
            {

                 $page=$request->getParameter('page');
            }
   
            $results=Doctrine_Core::getTable('Specimens')->getSpecimensInCollectionsJSON($collection_code, $host, $size, $page);
      
            return  $results;
            
        }
  
        return Array();
    }
    
    //ftheeten 2017 12 04
    protected function getAllCollectionsAccessPointJSON(sfWebRequest $request)
    {
        $host=$request->getHost();
        $results=Doctrine_Core::getTable('Specimens')->getCollectionsAllAccessPointsJSON( $host);
        return  $results;
    }
    
   
}
