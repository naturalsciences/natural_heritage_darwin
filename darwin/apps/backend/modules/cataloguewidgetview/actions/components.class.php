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
}
