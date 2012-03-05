<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage speicmen_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class partwidgetViewComponents extends sfComponents
{

  protected function defineForm()
  {
    $this->part = Doctrine::getTable('SpecimenSearch')->findOneByPartRef($this->eid);
  }

  public function executeParent()
  {
    $this->part = Doctrine::getTable('SpecimenParts')->findExcept($this->eid);
    $this->Codes = Doctrine::getTable('Codes')->getCodesRelatedArray('specimen_parts',$this->part->getParentRef()) ;     
  }

  public function executePartCount()
  {
    $this->defineForm();
    if ($this->part->getPartCountMin() === $this->part->getPartCountMax()) $this->accuracy = "Exact" ;
    else $this->accuracy = "Imprecise" ;    
  }

  public function executeSpecPart()
  {
    $this->defineForm();
  }

  public function executeComplete()
  {
    $this->defineForm();
  }

  public function executeLocalisation()
  {
    $this->defineForm();
  }

  public function executeContainer()
  {
    $this->defineForm();
  }

  public function executeRefCodes()
  {
    $this->Codes = Doctrine::getTable('Codes')->getCodesRelatedArray('specimen_parts',$this->eid) ;   
  }

  public function executeRefInsurances()
  {
    $this->Insurances = Doctrine::getTable('Insurances')->findForTable('specimen_parts',$this->eid) ;   
  }

  public function executeRefProperties()
  {
  }

  public function executeComments()
  {
    $this->Comments = Doctrine::getTable('Comments')->findForTable('specimen_parts',$this->eid) ;
  }
  public function executeMaintenance()
  {
	  $this->maintenances = Doctrine::getTable('CollectionMaintenance')->getRelatedArray('specimen_parts', array($this->eid));
  }
  public function executeExtLinks()
  {}  
}
