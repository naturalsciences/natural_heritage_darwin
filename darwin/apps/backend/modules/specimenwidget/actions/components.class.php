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
        $spec = Doctrine_Core::getTable('Specimens')->find($this->eid);
        $this->form = new SpecimensForm($spec);
        $this->spec_id = $this->eid;
        if(!$this->getUser()->isA(Users::ADMIN))
        {
          if(! Doctrine_Core::getTable('Specimens')->hasRights('spec_ref', $this->eid, $this->getUser()->getId()))
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

    if(!isset($this->eid))
      $this->eid = $this->form->getObject()->getId();
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
    $this->defineForm();
    if(!isset($this->form['newProperties']))
      $this->form->loadEmbed('Properties');
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
		 
		$this->spec_related_inverse = Doctrine_Core::getTable("SpecimensRelationships")->getAllInverseRelationships($this->spec_id);
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
    if(!isset($this->form['newCollectionMaintenance']))
      $this->form->loadEmbed('CollectionMaintenance');
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
	  $loan_names=Array();
      $loan_properties_tmp=Array();
      $this->loan_properties=Array();
      
      foreach($this->loans as $loan) {
        $loan_list[] = $loan->getId() ;
		$loan_names[$loan->getId()]=$loan->getName();
        $props=Doctrine_Core::getTable('Properties')->findForTable("loans",$loan->getId());
        //$list_props=$props->fetchAll(PDO::FETCH_ASSOC);
        $loan_properties_tmp[$loan->getId()]=$props;
      }
      $status = Doctrine_Core::getTable('LoanStatus')->getStatusRelatedArray($loan_list) ;
      $this->status = array();
      foreach($status as $sta) {
        $this->status[$sta->getLoanRef()] = $sta;
      }
      $this->rights = Doctrine_Core::getTable('loanRights')->getEncodingRightsForUser($this->getUser()->getId());
	  $loanItems=Doctrine_Core::getTable('LoanItems')->findBySpecimenRef($this->eid);
	  $this->items=Array();
      $this->loans_props=Array();
	  foreach($loanItems as $item)
	  {
			if($item->getToDate()!==null)
			{
				$this->items[$loan_names[$item->getLoanRef()]]=$item;
			}
	  }
      //print_r($this->loan_properties);
      foreach($loan_properties_tmp as $id_loan=> $prop)
      {
          $this->loan_properties[$id_loan]=Array();
        foreach($props as $prop)
        {
            $this->loan_properties[$id_loan][]=$prop;
        }
      
      }
      

    }
  }
  
  //ftheeten 2016 06 29
  public function executeEcology()
  {
       $this->defineForm();
      if(!isset($this->form['newEcology']))
         $this->form->loadEmbed('Ecology');
  }
  
  //ftheeten 2016 07 07
  public function executeGtuDate()
  {
    $this->defineForm();
  }
  
    //ftheeten 2016 08 11
  public function executeStorageParts()
  {
       $this->defineForm();
      if(!isset($this->form['newStorageParts']))
      {
         $this->form->loadEmbed('StorageParts');
      }
      $this->form->forceContainerChoices();
  }
}
