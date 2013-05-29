<?php

class ParsingGTU
{
  public $TagGroupData = array() ;
  public $GTUDate = array('from'=>null,'to'=>null,'time'=>null) ;
  public $peoples = array();
  public $tags = array() ;
  public $tag_group_name, $tag_value ;

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
  public function addComment($note)
  {
    $comment = new Comments();
    $comment->fromArray('referenced_relation' => 'staging'
  
  }
  public function handleTagGroups()
  {
    $tag_group = new stagingTagGroups() ;
    //@TODO find a better way to manage all known tags
    if(in_array(str_to_lower($this->tag_group_name),array("continent", "country", "province", "region", "municipality"))
    {
      $tag_group->setGroupName("administrative area") ;
      $tag_group->setSubGroupName($this->tag_group_name) ;
    }
    else
    {
      $tag_group->setGroupName("other") ;
      $tag_group->setSubGroupName($this->tag_group_name) ;
    }
    $tag_group->setTagValue($this->tag_value) ;
    $tags[] = $tag_group
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

  private function insertTags($record_id)
  {
    foreach($this->tags as $tag)
    {
      $tag->setStagingRef($record_id) ;
      $tag->save() ;
    }
  }
}