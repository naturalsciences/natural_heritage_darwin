<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage loan_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class loanwidgetviewComponents extends sfComponents
{

  protected function defineObject()
  {
    $this->table ="loans";
    if(! isset($this->loan) )
      $this->loan = Doctrine::getTable('Loans')->find($this->eid);
  }

  public function executeRefInsurances()
  {
    $this->defineObject();
    $this->Insurances = Doctrine::getTable('Insurances')->findForTable('loans', $this->loan->getId()) ;

  }

  public function executeRefProperties()
  {
    $this->defineObject();
  }

  public function executeRefUsers()
  {
    $this->defineObject();
    $this->users_rights = Doctrine::getTable('LoanRights')->findByLoanRef($this->loan->getId());
    $this->users_ids = array();
    foreach($this->users_rights as $peo) $this->users_ids[$peo->getUserRef()] = '';
    $tmp_users = Doctrine::getTable('Users')->getIdsFromArrayQuery('Users', array_keys($this->users_ids));
    foreach($tmp_users as $usr) $this->users_ids[$usr->getId()] = $usr;
  }

  public function executeMainInfo()
  { 
    $this->defineObject();
  }
  
  public function executeActors()
  {
    $this->defineObject();
    $this->senders = Doctrine::getTable('CataloguePeople')->findActors($this->loan->getId(),'sender','loans');
    $this->receivers = Doctrine::getTable('CataloguePeople')->findActors($this->loan->getId(),'receiver','loans');
    $this->people_ids = array();
    foreach($this->senders as $peo) $this->people_ids[$peo->getPeopleRef()] = '';
    foreach($this->receivers as $peo) $this->people_ids[$peo->getPeopleRef()] = '';
    $people = Doctrine::getTable('People')->getIdsFromArrayQuery('People', array_keys($this->people_ids));
    foreach($people as $peo) $this->people_ids[$peo->getId()] = $peo;
  }  
    
  public function executeRefRelatedFiles()
  { 
    $this->defineObject();
    $this->files = Doctrine::getTable('Multimedia')->findForTable('loans', $this->loan->getId()) ;
    $this->atLeastOneFileVisible = true;
  }  

  public function executeRefComments()
  { 
    $this->defineObject();
    $this->Comments = Doctrine::getTable('Comments')->findForTable('loans', $this->loan->getId()) ;
  }
  
  public function executeLoanStatus()
  { 
    $this->defineObject();
    $this->loanstatus = Doctrine::getTable('LoanStatus')->getLoanStatus($this->loan->getId());
  }

  public function executeMaintenances()
  { 
    $this->defineObject();
    $this->maintenances = Doctrine::getTable('CollectionMaintenance')->getMergedMaintenances('loans', $this->loan->getId());
  }
}
