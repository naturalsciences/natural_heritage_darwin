<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage speicmen_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class specimenwidgetviewComponents extends sfComponents
{

  protected function defineObject()
  {
    if(! isset($this->spec) )
      $this->spec = Doctrine::getTable('Specimens')->find($this->eid);
  }
  public function executeType()
  {
    $this->defineObject();
  }

  public function executeSex()
  {
    $this->defineObject();
  }

  public function executeStage()
  {
    $this->defineObject();
  }

  public function executeSocialStatus()
  {
    $this->defineObject();
  }

  public function executeRockForm()
  {
    $this->defineObject();
  }

  public function executeRefCollection()
  {
    $this->defineObject();
  }

  public function executeRefDonators()
  {
    $this->Donators = Doctrine::getTable('CataloguePeople')->getPeopleRelated('specimens','donator',$this->eid) ;
  }

  public function executeRefExpedition()
  {
    $this->defineObject();
  }

  public function executeRefIgs()
  {
    $this->defineObject();
  }

  public function executeAcquisitionCategory()
  {
    $this->defineObject();
  }

  public function executeTool()
  {
    $this->form = Doctrine::getTable('SpecimensTools')->getToolName($this->eid) ;
  }

  public function executeMethod()
  {
    $this->form = Doctrine::getTable('SpecimensMethods')->getMethodName($this->eid) ;
  }

  public function executeRefTaxon()
  {
    $this->defineObject();
  }

  public function executeRefChrono()
  {
    $this->defineObject();
  }

  public function executeRefLitho()
  {
    $this->defineObject();
  }

  public function executeRefLithology()
  {
    $this->defineObject();
  }

  public function executeRefMineral()
  {
    $this->defineObject();
  }

  public function executeRefGtu()
  {
    $this->defineObject();
    if($this->spec->getGtuRef())
    {
      $this->gtu = Doctrine::getTable('Gtu')->find($this->spec->getGtuRef());
      //ftheeten 2015 07 01 to display the exact site on the main page
      $this->commentsGtu = Doctrine::getTable('Comments')->findForTable('gtu',$this->spec->getGtuRef()) ;
    }
  }

  public function executeRefCodes()
  {
    $this->Codes = Doctrine::getTable('Codes')->getCodesRelatedArray('specimens',$this->eid) ;
  }

  public function executeRefMainCodes()
  {
    $this->Codes = Doctrine::getTable('Codes')->getMainCodesRelatedArray('specimens',$this->eid);
  }

  public function executeRefCollectors()
  {
    $this->Collectors = Doctrine::getTable('CataloguePeople')->getPeopleRelated('specimens','collector',$this->eid) ;
  }

  public function executeRefProperties()
  {
  }

  public function executeRefComment()
  {
    $this->Comments = Doctrine::getTable('Comments')->findForTable('specimens',$this->eid) ;
  }

  public function executeRefIdentifications()
  {
    $this->identifications = Doctrine::getTable('Identifications')->getIdentificationsRelated('specimens',$this->eid) ;
    $this->people = array() ;
    foreach ($this->identifications as $key=>$val)
    {
      $Identifier = Doctrine::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $val->getId()) ;
      $this->people[$val->getId()] = array();
      foreach ($Identifier as $key2=>$val2)
      {
        $this->people[$val->getId()][] = $val2->People->getFormatedName() ;
      }
    }
  }

  public function executeExtLinks()
  {}

  public function executeSpecimensRelationships()
  {
    $this->spec_related = Doctrine::getTable("SpecimensRelationships")->findBySpecimenRef($this->eid);
    $this->spec_related_inverse = Doctrine::getTable("SpecimensRelationships")->findByRelatedSpecimenRef($this->eid);
  }

  public function executeRefRelatedFiles()
  {
    $this->atLeastOneFileVisible = $this->getUser()->isAtLeast(Users::ENCODER);
    $this->files = Doctrine::getTable('Multimedia')->findForTable('specimens', $this->eid, !($this->atLeastOneFileVisible));
    if(!($this->atLeastOneFileVisible)) {
      $this->atLeastOneFileVisible = ($this->files->count()>0);
    }
  }

  public function executeInformativeWorkflow()
  {
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
  }

  public function executeBiblio()
  {
    $this->Biblios = Doctrine::getTable('CatalogueBibliography')->findForTable('specimens', $this->eid);
  }


  public function executeSpecimenCount()
  {
    $this->defineObject();
    if ($this->spec->getSpecimenCountMin() === $this->spec->getSpecimenCountMax())
      $this->accuracy = "Exact" ;
    else
      $this->accuracy = "Imprecise" ;
  }

  public function executeSpecPart()
  {
    $this->defineObject();
  }

  public function executeComplete()
  {
    $this->defineObject();
  }

  public function executeLocalisation()
  {
    $this->defineObject();
  }

  public function executeContainer()
  {
    $this->defineObject();
  }

  public function executeRefInsurances()
  {
    $this->Insurances = Doctrine::getTable('Insurances')->findForTable('specimens',$this->eid) ;
  }

  public function executeMaintenance()
  {
    $this->maintenances = Doctrine::getTable('CollectionMaintenance')->getRelatedArray('specimens', array($this->eid));
  }
  public function executeHistoric()
  {
    $this->defineObject();
  }
  public function executeLoan()
  {
    $this->defineObject();
  }
}
