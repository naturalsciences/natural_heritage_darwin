<?php
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
      $tableRecord = Doctrine::getTable(self::$correspondingTable[$request->getParameter('table')])->find($request->getParameter('id',0));

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
    $this->widgets = Doctrine::getTable('MyWidgets')
      ->setUserRef($id)
      ->setDbUserType($this->getUser()->getDbUserType())
      ->getWidgets($this->widgetCategory, $collection);
    $this->widget_list = Doctrine::getTable('MyWidgets')->sortWidgets($this->widgets, $this->getI18N());
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
}
