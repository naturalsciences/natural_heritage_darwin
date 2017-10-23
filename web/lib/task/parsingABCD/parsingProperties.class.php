<?php

class ParsingProperties extends importABCDXml
{
  public $people_type="donator",$date_from, $accuracy=null ;

  public function __construct($tag="length",$applies_to="")
  {
    $this->property = new Properties() ;
    $this->property->setPropertyType($tag) ;
    $this->property->setAppliesTo($applies_to) ;
  }

  public function handleRelation($people,$staging)
  {
    $staging->addRelated($people) ;
  }

  public function getDateFrom($date, $tag,$staging)
  {
    if($tag=="Altitude") {
      $staging["gtu_from_date"]=$date ;
    }
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
  
  public function getPropertyType()
  {
    return $this->property->getPropertyType();
  }
  
  public function getLowerValue()
  {
    return $this->property->getLowerValue();
  }
  
}
