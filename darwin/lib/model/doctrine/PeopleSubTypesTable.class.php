<?php

/**
 * PeopleSubTypesTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PeopleSubTypesTable extends DarwinTable
{
    /**
     * Returns an instance of this class.
     *
     * @return PeopleSubTypesTable The table instance
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PeopleSubTypes');
    }
	

  public function getDistinctSubTypes()
  {
    
    $q = $this->createFlatDistinct('people_sub_types', 'sub_type', 'sub_type');
    $a = DarwinTable::CollectionToArray($q->execute(), 'sub_type');
    return array_merge(array(''=>''),$a);
  }
}