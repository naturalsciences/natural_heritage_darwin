<?php

class peoplewidgetViewComponents extends sfComponents
{
  public function executeComment()
  {}
  public function executeProperties()
  {}

  public function executeExtLinks()
  {}    

  public function executeRelatedFiles()
  {}

  public function executeAddress()
  {
    $this->addresses =  Doctrine_Core::getTable('PeopleAddresses')->fetchByPeople($this->eid);
  }
  
  public function executeComm()
  {
    $this->comms =  Doctrine_Core::getTable('PeopleComm')->fetchByPeople($this->eid);
  }
  
  public function executeLang()
  {
    $this->langs =  Doctrine_Core::getTable('PeopleLanguages')->fetchByPeople($this->eid);
  }
  
  public function executeRelation()
  {
    $this->relations  = Doctrine_Core::getTable('PeopleRelationships')->findAllRelated($this->eid);
  }
  public function executeInformativeWorkflow()
  {
    $this->informativeWorkflow = Doctrine_Core::getTable('InformativeWorkflow')->findForTable($this->table, $this->eid);
  }  
}
