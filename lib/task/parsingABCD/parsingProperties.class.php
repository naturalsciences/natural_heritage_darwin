<?php

class ParsingProperties extends importABCDXml
{
  public $people_order_by=null, $date_from, $accuracy=null ;

  public function __construct($tag)
  {
    $this->property = new Properties() ;
    $this->property->setPropertyType("length") ;
    $this->property->setAppliesTo('') ;
  }

  public function handlePeople($people,$staging)
  {
    $people->setPeopleType('donator');
    if($this->people_order_by)
    {
      $people->setOrderBy($this->people_order_by) ;
      $this->people_order_by = null ;
    }
    $staging->addRelated($people) ;
  }

  public function getDateFrom($date, $tag,$staging)
  {
    if($tag=="Altitude") $staging["gtu_from_date"]=$date ;
    else
    {
      $this->date_from = $date ;
      $this->property->setDateFrom($date) ;
    }
  }

  public function setDateTo($duration)
  {
    if(strtotime($duration)) $this->property->setDateTo($this->date_from+$duration) ;
  }
/*
  public function getLowerValue($data, $tag, $staging)
  {
    if($tag=="Altitude") $staging['gtu_elevation']=$data;
    else $this->addPropertyvalue($data) ;
  }

  public function getUpperValue($data, $tag, $staging)
  {
    if($tag!="Altitude") $value = new PropertiesValues() ;
  }

  private function addPropertyvalue($data)
  {
    $value = new PropertiesValues() ;
    $value->setPropertyAccuracy($this->accuracy) ;
    $value->setPropertyValue($data) ;
    $this->property->PropertiesValues[] = $value ;
  }*/
}
