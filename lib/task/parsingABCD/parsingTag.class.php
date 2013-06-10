<?php

class ParsingTag
{
  public $GTUDate = array('from'=>null,'to'=>null,'time'=>null) ;
//  public $peoples = array();
//  public $comments = array() ;
  public $tags = array() ;
  public $tag_group_name, $tag_value ;
  private $array_object = array() ;

  public function __construct($tagtype=null)
  {
    switch($tagtype)
    {
      case "gtu" : $this->people_type = "collector" ; break ;;
      case "unit" : $this->people_type = "donator" ; break ;;
    }
  }
  public function addRelated($object)
  {
    $this->array_object[] = $object ;
  }
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

  public function addTagGroups()
  {
    $tag_group = new stagingTagGroups() ;
    //@TODO find a better way to manage all known tags
    if(in_array(strtolower($this->tag_group_name),array("continent", "country", "province", "region", "municipality")))
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
    $tags[] = $tag_group ;
  }
  public function addStagingInfo($object, $id)
  {
    $info = new StagingInfo() ;
    $info->setStagingRef($id) ;
    $info->setReferencedRelation('gtu') ;
    $info->addRelated($object) ;
    $info->save() ;
  }
  public function handlePeople($people,$staging,$is_maintenance=false)
  {
    if(!$is_maintenance)
    {
      $people->setPeopleType($this->people_type);
      $staging->addRelated($people) ;
    }
  }

  public function insertTags()
  {
    foreach($this->tags as $tag)
    {
      $tag->setStagingRef($this->record_id) ;
      $tag->save() ;
    }
  }
}