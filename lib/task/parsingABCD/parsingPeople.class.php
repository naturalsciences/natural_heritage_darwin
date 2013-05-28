<?php 

public function parsingPeople()
{
  public $full_name, $given_name, $family_name, $title, $order_by
  
  public function savePeople($record_id, $type)
  {
    if ($this->full_name) $name = $this->full_name ;
    else $name = ($this->title?$this->title." ":"").$this->family_name." ".$this->given_name ;
    $people = new StagingPeople() ;
    $people->fromArray(array('people_type' => $type, 'record_id' => $record_id, 
                'referenced_relation' => ($type == 'identifier' ? 'identifications':'staging'),
                'formated_name' => $name, 'order_by' => $this->order_by)) ;
    $people->save() ;
  }
}