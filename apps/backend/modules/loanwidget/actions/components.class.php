<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage loan_widget
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class loanwidgetComponents extends sfComponents
{
  protected function defineForm()
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) die("<div class='warn_message'>".__("you can't do that !!")."</div>") ;   
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid != null)
      {
        $loan = Doctrine::getTable('Loans')->find($this->eid);
        $this->form = new LoansForm($loan);
      }
      else
        $this->form = new LoansForm();
    }
  }

  public function executeRefInsurances()
  {
    $this->defineForm();
    if(!isset($this->form['newInsurance']))
      $this->form->loadEmbedInsurance();  
  }

  public function executeRefProperties()
  {
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;  
  }

  public function executeRefUsers()
  {
    $this->defineForm();
    if(!isset($this->form['newUsers']))
      $this->form->loadEmbedUsers();  
  }

  public function executeMainInfo()
  { 
    $this->defineForm();
  }
  
  public function executeActors()
  {
    $this->defineForm();
    if(!isset($this->form['newActorsSender']))
      $this->form->loadEmbedActorsSender();    
    if(!isset($this->form['newActorsReceiver']))
      $this->form->loadEmbedActorsReceiver();          
  }  
    
  public function executeRefRelatedFiles()
  { 
    $this->defineForm();
    if(!isset($this->form['newRelatedFiles']))
      $this->form->loadEmbedRelatedFiles();  
  }  

  public function executeRefComments()
  { 
    $this->defineForm();
    if(!isset($this->form['newComments']))
      $this->form->loadEmbedComments();   
  }
  
  public function executeLoanStatus()
  { 
    if(isset($this->form))
      $this->eid = $this->form->getObject()->getId() ;  
    if(isset($this->eid))
       $this->loanstatus = Doctrine::getTable('LoanStatus')->getLoanStatus($this->eid);
    else $this->eid = false;
    $this->form = new informativeWorkflowForm(null, array('available_status' => LoanStatus::getAvailableStatus())) ;     
  }   
}
