<?php
class DarwinModel extends sfDoctrineRecord
{
  private $array_object = array() ;
  private $array_errors = array() ;

  public function addRelated($object)
  {
    $this->array_object[] = $object ;
  }

  public function save( Doctrine_Connection $conn = null)
  {
    parent::save($conn);
    foreach($this->array_object as $object)
    {
      $object->setReferencedRelation($this->getTable()->getTableName()) ;
      $object->setRecordId($this->id) ;
      try {
        $object->save() ;
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $this->array_errors[] = "Unit ".$this->getTable()->getTableName()." object were not saved: ".$e->getMessage().";";
      }
    }
    return $this->array_errors ;
  }
}
