<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Taxonomy extends BaseTaxonomy
{
  public function getNameWithFormat()
  {
    $name = $this->getName();
    if($name == '-') return $name;
    $name = '<i>'.$name.'</i>';
    if($this->_get('extinct'))
    {
      return $name . ' †';
    }
    else
    {
      return $name;
    }
  }

  public function getName()
  {
    if(! $this->isNew() && $this->_get('id')==0)
      return '-';
    return $this->_get('name');
  }
  
  	//ftheeten 2017 08 07
    public function getTaxonomyMetadataName()
    {
        return Doctrine_Core::getTable('TaxonomyMetadata')->find($this->getMetadataRef())->getTaxonomyName();
    }
    
     public function getTaxonomyMetadataReferenceStatus()
    {
        return Doctrine_Core::getTable('TaxonomyMetadata')->find($this->getMetadataRef())->getIsReferenceTaxonomy();
    }
    
    public static function getStatusList()
    {
        return array(
            'valid'=>'valid', 
            'invalid'=>'invalid', 
            'deprecated'=>'deprecated', 
            "in litteris"=> "in litteris", 
            "nomen nudum"=> "nomen nudum", 
            "in press"=> "submitted / in press");
    }
    
	 
	 
  
}

