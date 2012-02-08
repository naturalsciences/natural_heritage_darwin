<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage loan_widget
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class loanwidgetviewComponents extends sfComponents
{

  protected function defineObject()
  {
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

  }

  public function executeMainInfo()
  { 
    $this->defineObject();
  }
  
  public function executeActors()
  {
    $this->defineObject();     
  }  
    
  public function executeRefRelatedFiles()
  { 
    $this->defineObject();
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
}
