<?php

/**
 * account actions.
 *
 * @package    darwin
 * @subpackage board_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class cataloguewidgetViewComponents extends sfComponents
{

  public function executeRelationRecombination()
  {
    $this->relations = Doctrine_Core::getTable('CatalogueRelationships')->getRelationsForTable($this->table, $this->eid, 'recombined from');
  }

  public function executeComment()
  { 
	
    $this->comments =  Doctrine_Core::getTable('Comments')->findForTable($this->table, $this->eid);
  }

  public function executeExtLinks()
  {
    $this->links =  Doctrine_Core::getTable('ExtLinks')->findForTable($this->table, $this->eid);
  }
  
  public function executeInsurances()
  {
    $this->insurances =  Doctrine_Core::getTable('Insurances')->findForTable($this->table, $this->eid);
  }

  public function executeProperties()
  {
    $this->properties = Doctrine_Core::getTable('Properties')->findForTable($this->table, $this->eid);
  }

  public function executeVernacularNames()
  {
    $this->vernacular_names =  Doctrine_Core::getTable('VernacularNames')->findForTable($this->table, $this->eid);
  }

  public function executeSynonym()
  {
    $this->synonyms = Doctrine_Core::getTable('ClassificationSynonymies')->findAllForRecord($this->table, $this->eid);
	$this->other_synonyms= Doctrine_Core::getTable('ClassificationSynonymies')->findOtherSynonymsForRecord($this->table, $this->eid);
  }
  
  public function executeCataloguePeople()
  {
    $this->types = Doctrine_Core::getTable('CataloguePeople')->findForTableByType($this->table, $this->eid);
  }

  public function executeCollectionsCodes()
  {
    $this->collCodes = Doctrine_Core::getTable('Collections')->find($this->eid);
  }

  public function executeKeywords()
  {
    $this->keywords = Doctrine_Core::getTable('ClassificationKeywords')->findForTable($this->table, $this->eid);
  }
  public function executeInformativeWorkflow()
  {
    $this->informativeWorkflow = Doctrine_Core::getTable('InformativeWorkflow')->findForTable($this->table, $this->eid);
  }
  public function executeBiblio()
  {
    $this->Biblios = Doctrine_Core::getTable('CatalogueBibliography')->findForTable($this->table, $this->eid);
  }
  public function executeRelatedFiles()
  {
    $this->atLeastOneFileVisible = $this->getUser()->isAtLeast(Users::ENCODER);
    $this->files = Doctrine_Core::getTable('Multimedia')->findForTable($this->table, $this->eid, !($this->atLeastOneFileVisible));
    if(!($this->atLeastOneFileVisible)) {
      $this->atLeastOneFileVisible = ($this->files->count()>0);
    }
  }
  
    public function executeGtuTemporalInformation()
  {  
	if(isset($this->eid) && $this->eid !== null)
      {
        $gtu = Doctrine_Core::getTable('Gtu')->find($this->eid);
        $this->form = new GtuForm($gtu);
        $this->gtu_id = $this->eid;
	  }  
	
  }
  
   public function executeIdentifiers()
  {
    $this->identifiers = Doctrine_Core::getTable('Identifiers')->findForTable($this->table, $this->eid);
  }
  
   public function executePeopleSubTypes()
  {
	  $this->sub_types = Doctrine_Core::getTable('PeopleSubTypes')->findByPeopleRef($this->eid); 
  }
  
  public function executeCollections()
  {
	
	if(strtolower($this->table)=="igs")
	{
		$this->collections =  Doctrine_Core::getTable('Igs')->countCollectionsInIg($this->eid); 
  
	}
	elseif(strtolower($this->table)=="expeditions")
	{
		$this->collections =   Doctrine_Core::getTable('Expeditions')->countCollectionsInExpedition($this->eid); 
	}
	else
	{
		$this->collections = [];
	}
  }
  
   public function executeExpeditions()
  {
	if(strtolower($this->table)=="igs")
	{
		$this->expeditions =  Doctrine_Core::getTable('Igs')->countExpeditionsInIg($this->eid); 
	}
	else
	{
		$this->expeditions =[];
	}
  }
  
     public function executeIgs()
  {
	if(strtolower($this->table)=="expeditions")
	{
			$this->igs =  Doctrine_Core::getTable('Expeditions')->countIgsInExpedition($this->eid);  
	}
	else
	{
		$this->igs =  []; 
	}
  }
  
  

}
