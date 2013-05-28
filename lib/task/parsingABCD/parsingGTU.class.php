<?php

class ParsingGTU
{
  public $TagGroupData = array() ;
  public $GTUDate = array('from'=>null,'to'=>null,'time'=>null) ;
  public $peoples = array();

  //return ISODateTimeBegin tag value, if not return DateTime tag value, null otherwise
  public function getFromDate()
  {
    return ($this->GTUDate['from'] ? $this->GTUDate['from'] : $this->GTUDate['time']) ;
  }

  //return ISODateTimeEnd tag value, if not return DateTime tag value, null otherwise
  public function getToDate()
  {
    return ($this->GTUDate['to'] ? $this->GTUDate['to'] : $this->GTUDate['time']) ;
  }

  public function HandleTagGroups()
  {

  }

  public function save($record_id)
  {
    $this->insertPeopleInStaging($record_id) ;
  }

  private function insertPeopleInStaging($record_id)
  {
    foreach($this->peoples as $order => $people)
    {
      if ($people->getFormatedName()) $name = $people->getFormatedName() ;
      else $name = $people->getFamilyName()." ".$people->getGivenName().($people->getTitle()?" (".$people->getTitle().")":"") ;
      $staging = new StagingPeople() ;
      $staging->fromArray(array('people_type' => 'collector', 'record_id' => $record_id, 
                'referenced_relation' => 'staging',
                'formated_name' => $name, 'order_by' => $order)) ;
      $staging->save() ;
    }
  }
}