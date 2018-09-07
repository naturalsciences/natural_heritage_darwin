<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage loan_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class maintenanceswidgetComponents extends sfComponents
{
  protected function defineForm()
  {
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid !== null)
      {
        $loan = Doctrine::getTable('CollectionMaintenance')->find($this->eid);
        $this->form = new MaintenanceForm($loan);
      }
      else
        $this->form = new MaintenanceForm();
    }
    if(!isset($this->eid))
      $this->eid = $this->form->getObject()->getId();
    if(! isset($this->module) )
    {
      $this->module = 'maintenances';
    }
  }

  public function executeRefProperties()
  {
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;  
  }

  public function executeExtLinks()
  { 
    $this->defineForm();
    if(!isset($this->form['newExtLinks']))
      $this->form->loadEmbed('ExtLinks');
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
}
