<?php

/**
 * StoragePartsTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class StoragePartsTable extends DarwinTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object StoragePartsTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('StorageParts');
    }
    
      /**
  * Get Distincts Sub Container of Part
  * @return array an Array of types in keys
  */
  public function getDistinctParts()
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'specimen_part', 'specimen_part', true);
    return $items;
  }
  
  
    /**
  * Get Distincts Buildings of Part
  * @return array an Array of types in keys
  */
  public function getDistinctBuildings()
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'building', 'building', true);
    return $items;
  }

  /**
  * Get Distincts Floor of Part
  * @return array an Array of types in keys
  */
  public function getDistinctFloors($building = null)
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'floor', 'floor', true);
    return $items;
  }

  /**
  * Get Distincts Room of Part
  * @return array an Array of types in keys
  */
  public function getDistinctRooms($building = null, $floor = null)
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'room', 'room', true);
    return $items;
  }

  /**
  * Get Distincts Row of Part
  * @return array an Array of types in keys
  */
  public function getDistinctRows($building = null, $floor = null, $room = null)
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'row', 'row', true);
    return $items;
  }

    /**
  * Get Distincts Column of Part
  * @return array an Array of types in keys
  */
  public function getDistinctCols($building = null, $floor = null, $room = null)
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'col', 'col', true);
    return $items;
  }

  /**
  * Get Distincts Shelve of Part
  * @return array an Array of types in keys
  */
  public function getDistinctShelfs($building = null, $floor = null, $room = null, $rows = null)
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'shelf', 'shelf', true);
    return $items;
  }
  
  
  /**
  * Get Distincts status of Part
  * @return array an Array of types in keys
  */
  public function getDistinctSpecimenStatuses()
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'specimen_status', 'specimen_status', true);
    return $items;
  }

  
    /**
  * Get Distincts Container of Part
  * @return array an Array of types in keys
  */
  public function getDistinctContainerTypes()
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'container_type', 'container_type', true);
    return $items;
  }

  /**
  * Get Distincts Sub Container of Part
  * @return array an Array of types in keys
  */
  public function getDistinctSubContainerTypes()
  {
    $items = $this->createUniqFlatDistinct('storage_parts', 'sub_container_type', 'sub_container_type', true);
    return $items;
  }
  
   /**
  * Get Distincts Sub Container Storages of Part
  * filter by type if one given
  * @param string $type a type
  * @return array an Array of types in keys
  */
  public function getDistinctSubContainerStorages($type)
  {
    $q = $this->createFlatDistinctDepend('specimens', 'sub_container_storage', $type, 'storage');
    $a =  DarwinTable::CollectionToArray($q->execute(), 'storage');
    return array_merge(array('dry'=>'dry'),$a);
  }


  /**
  * Get Distincts Container Storages of Part
  * filter by type if one given
  * @param string $type a type
  * @return array an Array of types in keys
  */
  public function getDistinctContainerStorages($type)
  {
    $q = $this->createFlatDistinctDepend('storage_parts', 'container_storage', $type, 'storage');
    $a =  DarwinTable::CollectionToArray($q->execute(), 'storage');
    return array_merge(array('dry'=>'dry'),$a);
  }
  
  public function createUniqFlatDistinct($table, $column,  $new_col='item', $empty = false)
  {
	 
    if(! isset($this->flat_results)){
      $q = Doctrine_Query::create()
        //->useResultCache(true)
        //->setResultCacheLifeSpan(5) //5 sec
        ->From('FlatDict')
        ->select('dict_field, dict_value')
		 ->andwhere('dict_field = ?', $new_col)
        ->andwhere('referenced_relation = ?', $table)
        ->orderBy("dict_value ASC");
      $res = $q->execute();
	  //print(" $new_col = ". $q->execute()->count());
      $this->flat_results = array();
      foreach($res as $result) {
		 
        if(! isset($this->flat_results[$result->getDictField()]))
          $this->flat_results[$result->getDictField()] = array();
        $this->flat_results[$result->getDictField()][$result->getDictValue()] = $result->getDictValue();
      }
    }
	
    if(isset($this->flat_results[$column]))
      return $this->flat_results[$column];

    return array();
  }
}