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
    if(!$this->getUser()->isAtLeast(Users::ENCODER))  {
      print("<div class='warn_message'>".__("You don't have rights to edit these informations !")."</div>");
    }
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid !== null)
      {
        $spec = Doctrine_Core::getTable('Specimens')->find($this->eid);
        $this->form = new SpecimensForm($spec);
        $this->spec_id = $this->eid;
        if(!$this->getUser()->isA(Users::ADMIN))
        {
          if(! Doctrine_Core::getTable('Specimens')->hasRights('spec_ref', $this->eid, $this->getUser()->getId())) {
            print("<div class='warn_message'>".__("You don't have rights to edit these informations !")."</div>");
          }
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

    if(!isset($this->eid))
      $this->eid = $this->form->getObject()->getId();
    if(! isset($this->module) )
    {
      $this->module = 'specimen';
    }
    if(! isset($this->addCodeUrl)) {
      $this->addCodeUrl = $this->module.'/addCode';
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
      $this->form->loadEmbed('Codes');
  }

  public function executeRefCollectors()
  {
    $this->defineForm();
    if(!isset($this->form['newCollectors']))
      $this->form->loadEmbed('Collectors');
  }

  public function executeRefDonators()
  {
    $this->defineForm();
    if(!isset($this->form['newDonators']))
      $this->form->loadEmbed('Donators');
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
      $this->form->loadEmbed('Comments');
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

  public function executeRefIdentifications()
  {
    $this->defineForm();
    if(!isset($this->form['newIdentification']))
    $this->form->loadEmbedIndentifications();

  }

  public function executeSpecimensRelationships()
  {
    $this->defineForm();
    if(!isset($this->form['newSpecimensRelationships']))
      $this->form->loadEmbed('SpecimensRelationships');
    if($this->spec_id!=0)
	{
		 
		$this->spec_related_inverse = Doctrine_Core::getTable("SpecimensRelationships")->findByRelatedSpecimenRef($this->spec_id);
	}
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
      $this->form->loadEmbed('Biblio');
  }

  public function executeType()
  {
    $this->defineForm();
  }

  public function executeSex()
  {
    $this->defineForm();
  }

    public function executeNagoya()
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

  public function executeSpecimenCount()
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

  public function executeRefInsurances()
  {
    $this->defineForm();
    if(!isset($this->form['newInsurances']))
      $this->form->loadEmbed('Insurances');
  }

  public function executeMaintenance()
  {
    $this->defineForm();
    if($this->eid){
      $this->maintenances = Doctrine_Core::getTable('CollectionMaintenance')->getRelatedArray('specimens', array($this->eid));
    }
  }

  public function executeHistoric()
  {
    if(isset($this->form) )
      $this->eid = $this->form->getObject()->getId() ;
    if($this->eid){
      $this->items = Doctrine_Core::getTable('UsersTracking')->getRelated('specimens', $this->eid);
    }

  }
  public function executeLoan()
  {
    $this->defineForm();
    if($this->eid){
      $this->loans = Doctrine_Core::getTable('Loans')->getRelatedToSpecimen($this->eid);
      $loan_list = array();
      foreach($this->loans as $loan) {
        $loan_list[] = $loan->getId() ;
      }
      $status = Doctrine_Core::getTable('LoanStatus')->getStatusRelatedArray($loan_list) ;
      $this->status = array();
      foreach($status as $sta) {
        $this->status[$sta->getLoanRef()] = $sta;
      }
      $this->rights = Doctrine_Core::getTable('loanRights')->getEncodingRightsForUser($this->getUser()->getId());
    }
  }
  
    
  //ftheeten 2018 11 30
  public function executeGtuDate()
  {
    $this->defineForm();
  }
}
