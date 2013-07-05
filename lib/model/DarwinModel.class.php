<?php
class DarwinModel extends sfDoctrineRecord
{
  private $array_object = array() ;

  public function addRelated($object)
  {
    echo $object->getTable()->getTableName() ;
    $this->array_object[] = $object ;
  }

  public function save( Doctrine_Connection $conn = null)
  {
    parent::save($conn);
    foreach($this->array_object as $object)
    {
      $object->setReferencedRelation($this->getTable()->getTableName()) ;
      $object->setRecordId($this->id) ;
      $object->save() ;
    }
  }
}
?>