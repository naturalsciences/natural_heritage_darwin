<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage loan_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class loanwidgetComponents extends sfComponents
{
  protected function defineForm()
  {
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid !== null)
      {
        $loan = Doctrine::getTable('Loans')->find($this->eid);
        $this->form = new LoansForm($loan);
      }
      else
        $this->form = new LoansForm();
    }
    if(!isset($this->eid))
      $this->eid = $this->form->getObject()->getId();

    if(! isset($this->module) )
    {
      $this->module = 'loans';
    }
  }

  public function executeRefInsurances()
  {
    $this->defineForm();
    if(!isset($this->form['newInsurances']))
      $this->form->loadEmbed('Insurances');
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
    if(! $this->form->getObject()->isNew())
    {
      $this->status =  Doctrine::getTable('LoanStatus')->getFromLoans(array($this->form->getObject()->getId()));
    }
    
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
      $this->form->loadEmbed('RelatedFiles');
  }  

  public function executeRefComments()
  { 
    $this->defineForm();
    if(!isset($this->form['newComments']))
      $this->form->loadEmbed('Comments');
  }

  public function executeLoanStatus()
  { 
    $this->defineForm();
    $this->loanstatus = Doctrine::getTable('LoanStatus')->getLoanStatus($this->eid);
    $this->form = new informativeWorkflowForm(null, array('available_status' => LoanStatus::getAvailableStatus())) ;     
  }

  public function executeMaintenances()
  { 
    $this->defineForm();
    $this->maintenances = Doctrine::getTable('CollectionMaintenance')->getMergedMaintenances('loans', $this->eid);
  }
}
