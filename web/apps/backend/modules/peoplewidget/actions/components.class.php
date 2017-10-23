<?php

class peoplewidgetComponents extends sfComponents
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
    $this->addresses =  Doctrine::getTable('PeopleAddresses')->fetchByPeople($this->eid);
  }
  
  public function executeComm()
  {
    $this->comms =  Doctrine::getTable('PeopleComm')->fetchByPeople($this->eid);
  }
  
  public function executeLang()
  {
    $this->langs =  Doctrine::getTable('PeopleLanguages')->fetchByPeople($this->eid);
  }
  
  public function executeRelation()
  {
    $this->relations  = Doctrine::getTable('PeopleRelationships')->findAllRelated($this->eid);
  }
  public function executeInformativeWorkflow()
  {
    $this->informativeWorkflow = Doctrine::getTable('InformativeWorkflow')->findForTable($this->table, $this->eid);
  }  
}
