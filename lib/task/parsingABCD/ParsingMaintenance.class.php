<?php

class ParsingMaintenance
{
  public $maintenance, $desc='' , $people_type='operator';
  public function __construct($action)
  {
    $this->maintenance= new CollectionMaintenance() ;
    $this->maintenance->setActionObservation($action) ;
  }
  
  public function addMaintenance(Staging $staging, $with_desc=false)
  {
    if($with_desc) $this->maintenance->setDescription($this->desc) ;
    $staging->addRelated($this->maintenance) ;
  }
  
  public function handleRelation($people,$staging)
  {
    $this->maintenance->addRelated($people) ;
  }
}
