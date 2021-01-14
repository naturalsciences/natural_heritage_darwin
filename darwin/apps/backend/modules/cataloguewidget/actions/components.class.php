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
    $this->relations = Doctrine_Core::getTable('CatalogueRelationships')->getRelationsForTable($this->table, $this->eid, 'recombined from');
  }

  public function executeComment()
  {
    $this->comments =  Doctrine_Core::getTable('Comments')->findForTable($this->table, $this->eid);
  }
  
  public function executeExtLinks()
  {
    if($this->table == 'taxonomy')
    {
      $this->taxon = Doctrine_Core::getTable('Taxonomy')->find($this->eid);  
	}
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

  public function executeRelatedFiles()
  {
    $this->files = Doctrine_Core::getTable('Multimedia')->findForTable($this->table, $this->eid) ;
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
    $this->kingdom = '' ;
    if($this->table == 'taxonomy')
    {
      $taxon = Doctrine_Core::getTable('Taxonomy')->find($this->eid);  
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
    $this->informativeWorkflow = Doctrine_Core::getTable('InformativeWorkflow')->findForTable($this->table, $this->eid);
    if(!isset($this->view)) $this->view = false ;
  }

  public function executeBiblio()
  {
    $this->Biblios = Doctrine_Core::getTable('CatalogueBibliography')->findForTable($this->table, $this->eid);
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
}
