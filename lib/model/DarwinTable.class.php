<?php
class DarwinTable extends Doctrine_Table
{

  public static function getFilterForTable($table)
  {
    return self::getModelForTable($table). 'FormFilter';
  }
  
  /**
    * Get the Form formating of a table name
    * @param $table string a table name
    * @return string The Form name formatted
  */
  public static function getFormForTable($table)
  {
    return self::getModelForTable($table). 'Form';
  }

  /**
    * Get the Model formating of a table name
    * @param $table string a table name
    * @return string The model name formatted
  */
  public static function getModelForTable($table)
  {
    return sfInflector::camelize($table);
  }

  /**
    * Finds a record by its identifier if the id is greater than 0
    *
    * @param integer $rowId          Database Row ID
    * @return Doctrine_Record or false if no result
    */
  public function findExcept($rowId)
  {
      if ($rowId != 0)
      { 
        return parent::find($rowId);
      }
      else
      {
        return false;
      }
  }

  public function hasChildrens($table, $id)
  {
    $q =  Doctrine_Query::create()
      ->select('count(id)')
      ->from($table.' t')
      ->where('t.parent_ref = ?',$id);
    return $q->execute(null, Doctrine::HYDRATE_SINGLE_SCALAR);
  }

  protected function getI18N()
  {
     return sfContext::getInstance()->getI18N();
  }

  /**
   * addCatalogueReferences
   * Filter by Referenced table and record Id
   * @param Doctrine_Query $query the query
   * @param string $table_name the table name to filter results
   * @param int $record_id record id of the referenced record
   * @param string $alias alias used for table in the query (myTable t ==> alias is 't')
   * @param boolean $with_zero permit to include the referenced record 0
   * @return Doctrine_Query the modified query
  */
  public function addCatalogueReferences($query, $table_name, $record_id, $alias, $with_zero = false)
  {
    $query->andWhere($alias.'.referenced_relation = ?',$table_name)
         ->andWhere($alias.'.record_id = ?',$record_id);
    if(! $with_zero)
	 $query->andWhere($alias.'.record_id != 0');
    return $query;
  }
  /**
   * createDistinct 
   * Initiate a distinct query on a given model and column
   * @param string $model The model name
   * @param string $column The db column name that will be distinct
   * @param string $new_col The name of the new column with distincts
   * @param string $table_alias the alias of the used table
   * @return Doctrine_Query with the distinct ordered by the $new_col ASC.
  */
  public function createDistinct($model, $column, $new_col='item', $table_alias = 't')
  {
    $q = Doctrine_Query::create()
      ->useResultCache(new Doctrine_Cache_Apc())
      ->setResultCacheLifeSpan(5) //5 sec
      ->select("DISTINCT($table_alias.$column) as $new_col")
      ->from("$model $table_alias")
      ->orderBy("$new_col ASC");
    return $q;
  }
  
  public function createFlatDistinct($table, $column, $new_col='item')
  {
    $q = Doctrine_Query::create()
      ->useResultCache(new Doctrine_Cache_Apc())
      ->setResultCacheLifeSpan(5) //5 sec
      ->From('FlatDict')
      ->select('dict_value as '.$new_col)
      ->where('dict_field = ?', $column)
      ->andwhere('referenced_relation = ?', $table)
      ->orderBy("$new_col ASC");
    return $q;
  }

  /**
   * findWithParents
   * Find records with his parents order by the path ( root first)
   * @param $id int Id of the record to search
   * @return Doctrine_Collection A collection of records
  */
  public function findWithParents($id)
  {
    $self_unit = Doctrine::getTable($this->getTableName())->find($id);
    $ids = explode('/', $self_unit->getPath().$self_unit->getId());

    array_shift($ids); //Removing the first blank element 

    $q = Doctrine_Query::create()
	 ->from($this->getTableName())
	 ->whereIn('id', $ids)
	 ->orderBy('path ASC');
    return $q->execute();
  }
  
  public function findRights($user, $table)
  {
 		$q = Doctrine_Query::create()
		   ->select('collection_ref')
		   ->from($table)
		   ->andWhere('user_ref = ?', $user) ;
		return $q->execute() ; 
  }

  /** Search in flat specimens with a given value ($unit_id) for a field (field_name)
   * if a there is at least on collections matching criterias where you don't have rights to encod, 
   * return collections ids
   * @param string $field_name field of the darwin_flat
   * @param int $unit_id An field value
   * @param int $user_id the id of the user to test
   * @return an array of collections where you do not have rights
  **/
  public function testNoRightsCollections($field_name, $unit_id, $user_id)
  {
    $q = Doctrine_Query::create()
      ->select('distinct(collection_ref) as collection_ref')
      ->from('SpecimenSearch s')
			->where("s.$field_name = ". ( (int)$unit_id) );
    $collections = $q->execute();
    $ids = array();
    foreach($collections as $col)
    {
      $ids[] = $col->getCollectionRef();
    }
    if(empty($ids)) return array(); // If not referenced... you have rights !

    $q = Doctrine_Query::create()
      ->from('CollectionsRights r')
      ->whereIn('r.collection_ref', $ids)
      ->andWhere('r.user_ref = ?',$user_id)
      ->andWhere('r.db_user_type >= 2');
    $has_right_col = $q->execute();
    $rights_cols = array();
    foreach($has_right_col as $col)
    {
      $rights_cols[] = $col->getCollectionRef();
    }
    $noRights = array_diff($ids,$rights_cols);
    return $noRights;
  }

  public function completeaAsArray($name, $limit = 30)
  {
    $conn_MGR = Doctrine_Manager::connection();
    $q = Doctrine_Query::create()
         ->from($this->getTableName())
         ->andWhere("name_order_by like concat(fulltoindex(".$conn_MGR->quote($name, 'string')."),'%') ")
         ->orderBy('path ASC')
         ->limit($limit);
    $q_results = $q->execute();
    $result = array();
    foreach($q_results as $item) {
      $result[] = array('name' => $item->getName(), 'name_order_by'=> $item->getNameOrderBy(), 'id'=> $item->getId() );
    }
    return $result;
  }
}
