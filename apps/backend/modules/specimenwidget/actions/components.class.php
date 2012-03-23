<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage speicmen_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class specimenwidgetComponents extends sfComponents
{

  protected function defineForm()
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) die("<div class='warn_message'>".__("you can't do that !!")."</div>") ;   
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid != null)
      {
        $spec = Doctrine::getTable('Specimens')->find($this->eid);
        $this->form = new SpecimensForm($spec);
        $this->spec_id = $this->eid;
        if(!$this->getUser()->isA(Users::ADMIN))
        {
          if(in_array($spec->getCollectionRef(),Doctrine::getTable('Specimens')->testNoRightsCollections('spec_ref',$this->eid, $this->getUser()->getId())))
            die("<div class='warn_message'>".__("you can't do that !!")."</div>") ;
        }            
      }
      else
      {
        $this->form = new SpecimensForm();
        $this->spec_id = 0;
      }
      if(!isset($this->individual_id)) $this->individual_id = 0;    
    }
    elseif(! isset($this->individual_id) )
    {
      $this->individual_id = 0;
      $this->spec_id = $this->form->getObject()->getId();
    }
    if(! isset($this->module) )
    {
      $this->module = 'specimen';
    }
  }

  public function executeRefCollection()
  {
    $this->defineForm();
  }

  public function executeRefExpedition()
  {
    $this->defineForm();
  }

  public function executeRefIgs()
  {
    $this->defineForm();
  }

  public function executeAcquisitionCategory()
  {
    $this->defineForm();
  }

  public function executeTool()
  {
    $this->defineForm();
    $this->form->loadEmbedTools();
  }

  public function executeMethod()
  {
    $this->defineForm();
    $this->form->loadEmbedMethods();
  }

  public function executeRefTaxon()
  {
    $this->defineForm();
  }

  public function executeRefChrono()
  {
    $this->defineForm();
  }

  public function executeRefLitho()
  {
    $this->defineForm();
  }

  public function executeRefLithology()
  {
    $this->defineForm();
  }

  public function executeRefMineral()
  {
    $this->defineForm();
  }

  public function executeRefGtu()
  {
    $this->defineForm();
  }

  public function executeRefHosts()
  {
    $this->defineForm();
  }

  public function executeRefCodes()
  {
    $this->defineForm();
    if(!isset($this->form['newCodes']))
    $this->form->loadEmbedCode();
  }

  public function executeRefCollectors()
  {
    $this->defineForm();
    if(!isset($this->form['newCollectors']))
    $this->form->loadEmbedCollectors();
  }

  public function executeRefDonators()
  {
    $this->defineForm();
    if(!isset($this->form['newDonators']))
    $this->form->loadEmbedDonators();
  }

  public function executeRefProperties()
  {    
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
  }

  public function executeRefComment()
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
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
    if(!isset($this->form['newRelatedFiles']))
      $this->form->loadEmbedRelatedFiles();
  }
  
  public function executeRefIdentifications()
  {
    $this->defineForm();
    if(!isset($this->form['newIdentification']))
    $this->form->loadEmbedIndentifications();

  }

  public function executeSpecimensAccompanying()
  {
    $this->defineForm();
    if(!isset($this->form['newSpecimensAccompanying']))
    $this->form->loadEmbedAccompanying();
  }

  public function executeInformativeWorkflow()
  {
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
  }

  public function executeBiblio()
  {
    $this->defineForm();
    if(!isset($this->form['newBiblio']))
      $this->form->loadEmbedBiblio();
  }
}
