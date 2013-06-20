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
    // find a way to add people_ref here, the problem is this people ref is probably actualy in staging_people, so his future people id don't exist
    $this->maintenance->setPeopleRef(14) ;
    $this->maintenance->addRelated($people) ;
    $staging->addRelated($this->maintenance) ;
  }

}