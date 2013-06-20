<?php

class ParsingProperties
{
  public $people_order_by=null, $date_from, $accuracy=null ;

  public function __construct($tag)
  {
    $this->property = new CatalogueProperties() ;
    $this->property->setPropertyType("length") ;
    $this->property->setPropertySubType($tag) ;
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
    if($tag=="altitude") $staging["gtu_from_date"]=$date ;
    else
    {
      $this->date_from = $date ;
      $this->property->setDateFrom($date) ;
    }
  }

  public function getDateTo($duration)
  {
    if(strtotime($duration)) $this->property->setDateTo($this->date_from+$duration) ;
  }

  public function getLowerValue($data, $tag, $staging)
  {
    if($tag=="altitude") $staging['gtu_elevation']=$data;
    else $this->addPropertyvalue($data) ;
  }

  public function getUpperValue($data, $tag, $staging)
  {
    if($tag!="altitude") $value = new PropertiesValues() ;
  }

  private function addPropertyvalue($data)
  {
    $value = new PropertiesValues() ; 
    $value->setPropertyAccuracy($this->accuracy) ;
    $value->setPropertyValue($data) ;
    $this->property->PropertiesValues[] = $value ;
  }

  /*
"length" => type
Accuracy > property value => accuracy
Duration > MeasurementDateTime+duration = date_to
MeasurementDateTime > date_from
IsQuantitative > osef
LowerValue -> propertiesValues->propertyvalue
Parameter -> sub type
UnitOfMeasurement -> property_unit et property_accuracy_unit
UpperValue -> propertiesValues->propertyvalue
*/
}
