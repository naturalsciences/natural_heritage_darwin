<?php

class ParsingTag extends ImportABCDXml
{
  public $GTUdate = array('from'=>null,'to'=>null,'time'=>null),  $staging_info=null, $tag_group_name, $tag_value,
      $people_order_by=null, $accession, $accession_num, $accession_date ;
  private $array_object = array() ;

  public function __construct($tagtype=null)
  {
    switch($tagtype)
    {
      case "gtu" : $this->people_type = "collector" ; $this->relation = "gtu";  break ;;
      case "unit" : $this->people_type = "donator" ; break ;;
      case "lithology" : $this->relation = "lithology" ; break ;;
    }
  }

  public function getDateText($date)
  {
    if(strpos($date, '-'))
    {
      $dates= explode('-',$date);
      $this->GTUdate['from'] = FuzzyDateTime::getValidDate(trim($dates[0])) ;
      $this->GTUdate['to'] = FuzzyDateTime::getValidDate(trim($dates[1])) ;
    }
    else
      $this->GTUdate['time'] = FuzzyDateTime::getValidDate($date) ;
  }

  //return ISODateTimeBegin tag value, if not return DateTime tag value, null otherwise
  public function getFromDate()
  {
    $time = null;
    if($this->GTUdate['time'])
      $time =  $this->GTUdate['time'] ;
    return ($this->GTUdate['from'] ? FuzzyDateTime::getValidDate($this->GTUdate['from']) : $time) ;
  }

  //return ISODateTimeEnd tag value, if not return DateTime tag value, null otherwise
  public function getToDate()
  {
    $time = null;
    if($this->GTUdate['time'])
      $time =  $this->GTUdate['time'] ;
    return ($this->GTUdate['to'] ? FuzzyDateTime::getValidDate($this->GTUdate['to']) : $time) ;
  }

  public function addTagGroups()
  {
    $tag_group = new stagingTagGroups() ;

    
    // @TODO find a better way to manage all known tags
    if(in_array(strtolower($this->tag_group_name),array("continent", "country", "state or territory", "province", "region", "district", "department", "county", "city", "municipality", "state or province", "region or district")))
    {
      $tag_group->setGroupName("administrative area") ;
      $tag_group->setSubGroupName($this->tag_group_name) ;
    }
    else if (in_array(strtolower($this->tag_group_name),array("ocean", "sea", "archipelago", "island")))
    {
      $tag_group->setGroupName("hydrographic") ;
      $tag_group->setSubGroupName($this->tag_group_name) ;
    }
    else
    {
      if($this->tag_group_name == "ecology")
        $tag_group->setGroupName("habitat");
      else
        $tag_group->setGroupName("other") ;
      $tag_group->setSubGroupName($this->tag_group_name) ;
    }
    $tag_group->setTagValue($this->tag_value) ;
    return $tag_group ;
  }
  public function addStagingInfo($object, $id)
  {
    if(!$this->staging_info)
    {
      $this->staging_info = new StagingInfo() ;
      $this->staging_info->setStagingRef($id) ;
      $this->staging_info->setReferencedRelation($this->relation) ;
    }
    $this->staging_info->addRelated($object) ;
  }
  public function InitAccessionVar($date)
  {
    $this->accession_date = date('Y-m-d',strtotime($date)) ;
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
  public function HandleAccession($staging,$tosave)
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
        $staging->setIgDate($this->accession_date);
        $staging->setIgNum($this->accession_num) ;
        break ;;
      default :
        return null ;
    }
  }

  public function addMethod($data,$staging_id)
  {
    $method = Doctrine::getTable('CollectingMethods')->checkIfMethod($data);
    if($method) $ref = $method->getId() ;
    else
    {
      $object = new CollectingMethods() ;
      $object->setMethod($data) ;
      $object->save() ;
      $ref = $object->getId() ;
    }
    $object = new StagingMethods() ;
    $object->fromArray(array("staging_ref" => $staging_id, "collecting_method_ref" => $ref)) ;
    return $object ;
  }

  public function handleRelation($object,$staging)
  {
    $staging->addRelated($object) ;
  }
}
