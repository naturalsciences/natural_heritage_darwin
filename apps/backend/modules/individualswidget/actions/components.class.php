<?php

/**
 * specimen individuals components actions.
 *
 * @package    darwin
 * @subpackage individuals_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class individualswidgetComponents extends sfComponents
{

  protected function defineForm()
  {
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid != null)
      {
        $spec_individual = Doctrine::getTable('SpecimenIndividuals')->find($this->eid);
        $this->form = new SpecimenIndividualsForm($spec_individual);
        $this->individual_id = $this->form->getObject()->getId();
        $this->spec_id = $this->form->getObject()->getSpecimenRef();
        if(!$this->getUser()->isAtLeast(Users::ENCODER)) die("<div class='warn_message'>".__("you can't do that !!")."</div>") ;  
        $spec = Doctrine::getTable('SpecimenSearch')->findOneByIndividualRef($this->eid);
        if(!$this->getUser()->isA(Users::ADMIN))
        {
          if(in_array($spec->getCollectionRef(),Doctrine::getTable('Specimens')->testNoRightsCollections('individual_ref',$this->eid, $this->getUser()->getId())))
            die("<div class='warn_message'>".__("you can't do that !!")."</div>") ;
        }          
      }
      else
      {
        $this->form = new SpecimenIndividualsForm();
        $this->individual_id = 0;
        $this->spec_id = 0;
      }
    }
    else
    {
      $this->individual_id = $this->form->getObject()->getId();
      $this->spec_id = $this->form->getObject()->getSpecimenRef();
    }
    if(! isset($this->module) )
    {
      $this->module = 'individuals';
    }  
  }

  public function executeType()
  {
    $this->defineForm();
  }

  public function executeSex()
  {
    $this->defineForm();
  }

  public function executeStage()
  {
    $this->defineForm();
  }

  public function executeSocialStatus()
  {
    $this->defineForm();
  }

  public function executeRockForm()
  {
    $this->defineForm();
  }

  public function executeSpecimenIndividualCount()
  {
    $this->defineForm();
  }

  public function executeSpecimenIndividualComments()
  {
    $this->defineForm();
    if(!isset($this->form['newComments']))
    $this->form->loadEmbedComment();
  }
  
  public function executeRefIdentifications()
  {
    $this->defineForm();
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
    $this->form->loadEmbedLink();
  }  
  public function executeRefRelatedFiles()
  {
    $this->defineForm();
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
    if(!isset($this->form['newRelatedFiles']))
      $this->form->loadEmbedRelatedFiles();
  }
  public function executeInformativeWorkflow()
  {    
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
  }    
}
