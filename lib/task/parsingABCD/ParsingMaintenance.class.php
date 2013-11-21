<?php

class ParsingMaintenance
{
  public $maintenance, $desc='' , $people_order_by=null ;
  public function __construct($action)
  {
    $this->maintenance= new CollectionMaintenance() ;
    $this->maintenance->setActionObservation($action) ;
  }
  
  public function addMaintenance($staging, $with_desc=false)
  {
    if($with_desc) $this->maintenance->setDescription($this->desc) ;
    $staging->addRelated($this->maintenance) ;
  }
  
  // $staging is useless here, but I should put it to respect cohérence for HandlePeople function in parsingTag
  public function handlePeople($peopleName)
  {
    $people = new StagingPeople() ;
    $people->setPeopleType("operator");
    $people->setFormatedName($peopleName) ;
    $this->maintenance->addRelated($people) ;
  }
  
}