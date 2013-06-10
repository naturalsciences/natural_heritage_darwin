<?php

class ParsingDNA
{
  public $maintenance ;
  public function __construct()
  {
    $this->maintenance= new CollectionMaintenance() ;
  }
  
  public function addMaintenance($staging, $people)
  {
    $this->maintenance->setActionObservation('action') ;
    $this->maintenance->setPeopleRef(1) ;
    $this->maintenance->addRelated($people) ;
    $staging->addRelated($this->maintenance) ;
  }

}