<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage speicmen_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class partwidgetComponents extends sfComponents
{

  protected function defineForm()
  {
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid != null)
      {
        $spec = Doctrine::getTable('SpecimenParts')->find($this->eid);
        $this->form = new SpecimenPartsForm($spec);
        if(!$this->getUser()->isAtLeast(Users::ENCODER)) die("<div class='warn_message'>".__("you can't do that !!")."</div>") ;  
        $spec = Doctrine::getTable('SpecimenSearch')->findOneByPartRef($this->eid);
        if(!$this->getUser()->isA(Users::ADMIN))
        {
          if(in_array($spec->getCollectionRef(),Doctrine::getTable('Specimens')->testNoRightsCollections('part_ref',$this->eid, $this->getUser()->getId())))
            die("<div class='warn_message'>".__("you can't do that !!")."</div>") ;
        }         
      }
      else
      {
        $this->form = new SpecimenPartsForm();
      }
    }   
  }

  public function executeParent()
  {
    $this->defineForm();
  }

  public function executePartCount()
  {
    $this->defineForm();
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
    $this->form->forceContainerChoices();
  }

  public function executeRefCodes()
  {
    $this->defineForm();
    if(!isset($this->form['newCode']))  
     $this->form->loadEmbedCode();

    $this->code_copy = false;
    if($this->form->getObject()->isNew())
    {
      if(! isset($this->col_ref))
      {
        $this->col_ref =  $this->getRequest()->getParameter('col_ref');
      }
      $col = Doctrine::getTable('Collections')->find($this->col_ref);
      if($col)
        $this->code_copy = $col->getCodePartCodeAutoCopy();
    }

    $this->defineForm();
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

  public function executeComments()
  {
    $this->defineForm();
    if(!isset($this->form['newComments']))
    $this->form->loadEmbedComment();
  }

  public function executeExtLinks()
  {
    $this->defineForm();
    if(!isset($this->form['newExtLinks']))
    $this->form->loadEmbedLink();

  }  
  public function executeRefRelatedFiles()
  {
    $this->defineForm();
    if(!isset($this->form['newRelatedFiles']))
      $this->form->loadEmbedRelatedFiles();
  }
  public function executeMaintenance()
  {
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId();

    if($this->eid)
    {
      $this->maintenances = Doctrine::getTable('CollectionMaintenance')->getRelatedArray('specimen_parts', array($this->eid));
    }
  }
  public function executeInformativeWorkflow()
  {    
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
  }    
}
