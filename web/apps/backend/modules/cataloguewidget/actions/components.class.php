<?php

/**
 * account actions.
 *
 * @package    darwin
 * @subpackage board_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class cataloguewidgetComponents extends sfComponents
{
  public function executeRelationRecombination()
  {
    $this->relations = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable($this->table, $this->eid, 'recombined from');
  }

  public function executeComment()
  {
    $this->comments =  Doctrine::getTable('Comments')->findForTable($this->table, $this->eid);
  }
  
  public function executeExtLinks()
  {
    $this->links =  Doctrine::getTable('ExtLinks')->findForTable($this->table, $this->eid);
  }  

  public function executeInsurances()
  {
    $this->insurances =  Doctrine::getTable('Insurances')->findForTable($this->table, $this->eid);
  }

  public function executeProperties()
  {
    $this->properties = Doctrine::getTable('Properties')->findForTable($this->table, $this->eid);
  }

  public function executeVernacularNames()
  {
    $this->vernacular_names =  Doctrine::getTable('VernacularNames')->findForTable($this->table, $this->eid);
  }

  public function executeRelatedFiles()
  {
    $this->files = Doctrine::getTable('Multimedia')->findForTable($this->table, $this->eid) ;
  }

  public function executeSynonym()
  {
    $this->synonyms = Doctrine::getTable('ClassificationSynonymies')->findAllForRecord($this->table, $this->eid);
  }
  
  public function executeCataloguePeople()
  {
    $this->types = Doctrine::getTable('CataloguePeople')->findForTableByType($this->table, $this->eid);
  }

  public function executeCollectionsCodes()
  {
    $this->collCodes = Doctrine::getTable('Collections')->find($this->eid);
  }

  public function executeKeywords()
  {
    $this->keywords = Doctrine::getTable('ClassificationKeywords')->findForTable($this->table, $this->eid);
    $this->kingdom = '' ;
    if($this->table == 'taxonomy')
    {
      $taxon = Doctrine::getTable('Taxonomy')->find($this->eid);  
      if(substr($taxon->getPath(),0,4) == '/-4/' || $taxon->getId() == '-4') $this->kingdom = 'virus' ;
      if(substr($taxon->getPath(),0,4) == '/-3/' || $taxon->getId() == '-3') $this->kingdom = 'bacteriology' ;
      if(substr($taxon->getPath(),0,4) == '/-2/' || $taxon->getId() == '-2') $this->kingdom = 'bacteriology' ;
      if(substr($taxon->getPath(),0,6) == '/-1/1/' || $taxon->getId() == '1') $this->kingdom = 'zoology' ;
      if(substr($taxon->getPath(),0,6) == '/-1/14' || $taxon->getId() == '141538') $this->kingdom = 'botany' ;   
    }    
  }
  public function executeInformativeWorkflow()
  { 
    $this->form = new informativeWorkflowForm(null, array('available_status' => informativeWorkflow::getAvailableStatus($this->getUser()->getDbUserType()))) ;
    $this->informativeWorkflow = Doctrine::getTable('InformativeWorkflow')->findForTable($this->table, $this->eid);
    if(!isset($this->view)) $this->view = false ;
  }

  public function executeBiblio()
  {
    $this->Biblios = Doctrine::getTable('CatalogueBibliography')->findForTable($this->table, $this->eid);
  }
}
