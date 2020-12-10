<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage loan_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class loanitemwidgetComponents extends sfComponents
{
  protected function defineForm()
  {
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid !== null)
      {
        $loanitem = Doctrine::getTable('LoanItems')->find($this->eid);
        $this->form = new LoanItemWidgetForm($loanitem);
      }
      else
        $this->form = new LoanItemWidgetForm();
    }
    if(! isset($this->addCodeUrl)) {
      $this->addCodeUrl = $this->module.'/addCode';
    }
  }

  public function executeRefCodes()
  {
    $this->defineForm();
    if(!isset($this->form['newCodes']))
      $this->form->loadEmbed('Codes');
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
      $this->form->loadEmbed('RelatedFiles');
  }  

  public function executeRefComments()
  { 
    $this->defineForm();
    if(!isset($this->form['newComments']))
      $this->form->loadEmbed('Comments');   
  }

  public function executeMaintenances()
  { 
    if(isset($this->form))
      $this->eid = $this->form->getObject()->getId();
    if(isset($this->eid))
       $this->maintenances = Doctrine::getTable('CollectionMaintenance')->getMergedMaintenances('loan_items', $this->eid);
  }
}
