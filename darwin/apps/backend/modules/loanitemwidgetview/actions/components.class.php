<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage loan_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class loanitemwidgetviewComponents extends sfComponents
{

  protected function defineObject()
  {
    $this->table ="loan_items";
    if(! isset($this->loan) )
      $this->loan = Doctrine_Core::getTable('LoanItems')->find($this->eid);
  }

  public function executeRefInsurances()
  {
    $this->defineObject();
    $this->Insurances = Doctrine_Core::getTable('Insurances')->findForTable('LoanItems', $this->loan->getId()) ;

  }

  public function executeRefProperties()
  {
    $this->defineObject();
  }

  public function executeMainInfo()
  { 
    $this->defineObject();
  }
  
  public function executeActors()
  {
    $this->defineObject();
    $this->senders = Doctrine_Core::getTable('CataloguePeople')->findActors($this->loan->getId(),'sender','loan_items');
    $this->receivers = Doctrine_Core::getTable('CataloguePeople')->findActors($this->loan->getId(),'receiver','loan_items');
    $this->people_ids = array();
    foreach($this->senders as $peo) $this->people_ids[$peo->getPeopleRef()] = '';
    foreach($this->receivers as $peo) $this->people_ids[$peo->getPeopleRef()] = '';
    $people = Doctrine_Core::getTable('People')->getIdsFromArrayQuery('People', array_keys($this->people_ids));
    foreach($people as $peo) $this->people_ids[$peo->getId()] = $peo;
  }  
    
  public function executeRefRelatedFiles()
  { 
    $this->defineObject();
    $this->files = Doctrine_Core::getTable('Multimedia')->findForTable('loan_items', $this->loan->getId()) ;
    $this->atLeastOneFileVisible = true;
  }  

  public function executeRefComments()
  { 
    $this->defineObject();
    $this->Comments = Doctrine_Core::getTable('Comments')->findForTable('loan_items', $this->loan->getId()) ;
  }

  public function executeRefCodes()
  {
    $this->defineObject();
    $this->Codes = Doctrine_Core::getTable('Codes')->getCodesRelatedArray('loan_items',$this->loan->getId()) ;
  }

  public function executeMaintenances()
  { 
    $this->defineObject();
    $this->maintenances = Doctrine_Core::getTable('CollectionMaintenance')->getMergedMaintenances('loan_items', $this->loan->getId());
  }
}
