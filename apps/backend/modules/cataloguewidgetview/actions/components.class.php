<?php

/**
 * account actions.
 *
 * @package    darwin
 * @subpackage board_widget
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class cataloguewidgetViewComponents extends sfComponents
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
    $this->properties = Doctrine::getTable('CatalogueProperties')->findForTable($this->table, $this->eid);
  }

  public function executeVernacularNames()
  {
    $this->vernacular_names =  Doctrine::getTable('ClassVernacularNames')->findForTable($this->table, $this->eid);
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
    $this->collCodes = Doctrine::getTable('Collections')->findExcept($this->eid);
  }

  public function executeKeywords()
  {
    $this->keywords = Doctrine::getTable('ClassificationKeywords')->findForTable($this->table, $this->eid);
  }
}
