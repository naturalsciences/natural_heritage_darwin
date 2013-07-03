<?php

class ParsingTag
{
  public $GTUDate = array('from'=>null,'to'=>null,'time'=>null) ;
//  public $peoples = array();
//  public $comments = array() ;
  public $tags = array() ;
  public $tag_group_name, $tag_value, $people_order_by=null, $accession, $accession_num, $accession_date ;
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
    return $info ;
  }
  public function InitAccessionVar($date)
  {
    $this->accession_date = $date!= ''?$date:null ;
    $this->accession_num = null ;
  }
  public function addAccession($catalogue)
  {
    switch(strtolower($catalogue))
    {
      case "code" : $this->accession = "code" ; break ;;
      case "ig" :
      case "ig number" : $this->accession = "igs" ; break ;;
      default : $this->accession = null ; break ;;
    }
  }
  public function HandleAccession($staging)
  {
    if(!$this->accession_num) return null ;
    switch($this->accession)
    {
      case 'code' :
        $object = new Codes() ;
        $object->fromArray(array('code_date' => $this->accession_date?strtotime($this->accession_date):null, 'code' => $this->accession_num)) ;
        $staging->addRelated($object) ;
        break ;;
      case "igs" : 
        $object = new Igs() ;
        $object->fromArray(array('ig_date' => $this->accession_date, 'ig_num' => $this->accession_num)) ;
        $staging->addRelated($object) ;
        break ;;
      default :
        return null ;
    }
  }
  public function addMethod($data,$staging_id)
  {
    $method = Doctrine::getTable('CollectingMethods')->findOneByMethod($data);
    if($method) $ref = $method->getId() ;
    else
    {
      $object = new CollectingMethods() ;
      $object->setMethod($data) ;
      $object->save() ;
      $ref = $object->getId() ;
    }
    $object = new SpecimensMethods() ;
    $object->fromArray(array("specimen_ref" => $staging_id, "collecting_method_ref" => $ref)) ;
    return $object ;
  }
  
  public function handlePeople($people,$staging)
  {
    $people->setPeopleType($this->people_type);
    if($this->people_order_by)
    {
      $people->setOrderBy($this->people_order_by) ;
      $this->people_order_by = null ;
    }
    $staging->addRelated($people) ;
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