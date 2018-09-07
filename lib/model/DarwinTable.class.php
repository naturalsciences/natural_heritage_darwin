<?php
class DarwinTable extends Doctrine_Table
{

  public static function getFilterForTable($table)
  {
    return self::getModelForTable($table). 'FormFilter';
  }


  public static function getLinkforKnownTable($table, $id)
  {
    switch($table)
    {
      case 'collections':
        $link = 'collection/edit?id='.$id; break;
      case 'specimens':
        $link = 'specimen/edit?id='.$id; break;
      case 'expeditions':
        $link = 'expedition/edit?id='.$id; break;
      case 'loans':
        $link = 'loan/edit?id='.$id; break;
      case 'loan_items':
        $link = 'loanitem/edit?id='.$id; break;
      case 'taxonomy':
      case 'lithology':
      case 'lithostratigraphy':
      case 'chronostratigraphy':
      case 'mineralogy':
      case 'people':
      case 'insurances':
      case 'igs':
      case 'igs':
      case 'gtu':
      case 'bibliography':
        $link = $table.'/edit?id='.$id; break;
      case 'specimen_individuals':
      case 'specimen_parts': $link = false; break;
      default:
        $link = false; break;
    }
    return $link;
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
      ->useResultCache(true)
      ->setResultCacheLifeSpan(5) //5 sec
      ->select("DISTINCT($table_alias.$column) as $new_col")
      ->from("$model $table_alias")
      ->orderBy("$new_col ASC");
    return $q;
  }

  public function createFlatDistinct($table, $column, $new_col='item')
  {
    $q = Doctrine_Query::create()
      ->useResultCache(true)
      ->setResultCacheLifeSpan(5) //5 sec
      ->From('FlatDict')
      ->select('dict_value as '.$new_col)
      ->where('dict_field = ?', $column)
      ->andwhere('referenced_relation = ?', $table)
      ->orderBy("$new_col ASC");
    return $q;
  }
  public function createFlatDistinctDepend($table, $column, $depend, $new_col='item')
  {
    $q = Doctrine_Query::create()
      ->useResultCache(true)
      ->setResultCacheLifeSpan(5) //5 sec
      ->From('FlatDict')
      ->select('dict_value as '.$new_col)
      ->where('dict_field = ?', $column)
      ->andWhere('dict_depend = ?', $depend)
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
      ->from($this->getTableName().' t');
    if(strtolower($this->getTableName()) != 'collections')
      $q->innerJoin('t.Level l');

    $q->whereIn('id', $ids)
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

  public function hasRights($field_name, $unit_id, $user_id)
  {
    //TEST the rights in the given collection
    if($field_name =='spec_ref')
    {
      $q = Doctrine_Query::create()
        ->select('id')
        ->from('Specimens s')
        ->andWhere('s.id = ?', $unit_id)
        ->andWhere('s.collection_ref in (select fct_search_authorized_encoding_collections(?))',$user_id);
      $r = $q->execute();
      return $r->count();
    }
    elseif($field_name =='individual_ref')
    {
      $q = Doctrine_Query::create()
        ->select('id')
        ->from('SpecimenIndividuals i')
        ->InnerJoin('i.Specimens s')
        ->andWhere('i.id = ?', $unit_id)
        ->andWhere('s.collection_ref in (select fct_search_authorized_encoding_collections(?))',$user_id);
      $r = $q->execute();
      return $r->count();
    }
    if($field_name =='part_ref')
    {
      $q = Doctrine_Query::create()
        ->select('id')
        ->from('SpecimenParts p')
        ->InnerJoin('p.Individual i')
        ->InnerJoin('i.Specimens s')
        ->andWhere('p.id = ?', $unit_id)
        ->andWhere('s.collection_ref in (select fct_search_authorized_encoding_collections(?))',$user_id);
      $r = $q->execute();
      return $r->count();
    }
    return false;
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
    if(in_array($field_name,array('chrono_ref','litho_ref','lithology_ref','mineral_ref','taxon_ref','gtu_ref')))
    {
      $q = Doctrine_Query::create()
        ->select('distinct(collection_ref) as collection_ref')
        ->from('specimens s')
        ->where("s.$field_name = ". ( (int)$unit_id) );
      $collections = $q->execute();
      $ids = array();
      foreach($collections as $col)
      {
        $ids[] = $col->getCollectionRef();
      }
    }
    else
      $ids=array();

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

  /**
  * Get list of possible upper levels
  * @param $conn Doctrine Connection
  * @param $level the level used to get the possible upper ones
  * @return Array of results
  */
  private function getPossibleUpperLevels($level){
    $puls = array();
    if($level) {
      $pul = Doctrine_Query::create()
        ->select('level_upper_ref')
        ->from('PossibleUpperLevels pul');
        $pul->andWhere('pul.level_ref = ?', $level);
      $pul_results = $pul->execute();
      foreach ($pul_results as $element){
        $puls[] = $element->getLevelUpperRef();
      }
    }
    return $puls;
  }

  /**
  * Find item for autocompletion
  * @param $user The User object for right management
  * @param $needle the string entered by the user for search
  * @param $exact bool are we searching the exact term or more or less fuzzy
  * @param $limit int limit the number of results
  * @param $level the level used to get the possible upper ones
  * @return Array of results
  */
  public function completeAsArray($user, $needle, $exact, $limit = 30, $level)
  {

    $conn_MGR = Doctrine_Manager::connection();
    $q = Doctrine_Query::create()
      ->from($this->getTableName(). ' i')
      ->limit($limit);

    if($exact)
      $q->andWhere("name = ?",$needle);
    else
      $q->andWhere("name_indexed like concat(fulltoindex(".$conn_MGR->quote($needle, 'string')."),'%') ");
    if ($level && $level != '') {
      $q->innerJoin('i.Level l')
      ->orderBy('l.level_order DESC, name ASC');
      $puls = $this->getPossibleUpperLevels($level);
      if(count($puls))
        $q->andWhereIn('l.id', $puls);
    }
    else {
      $q->orderBy('name ASC');
    }
    $q_results = $q->execute();
    $result = array();
    foreach($q_results as $item) {
      $result[] = array('label' => $item->getName(), 'name_indexed'=> $item->getNameIndexed(), 'value'=> $item->getId() );
    }
    return $result;
  }



  /**
  * Find item for autocompletion
  * @param $user The User object for right management
  * @param $needle the string entered by the user for search
  * @param $exact bool are we searching the exact term or more or less fuzzy
  * @param $limit int limit the number of results
  * @param $level the level used to get the possible upper ones
  * @return Array of results
  */
  public function completeWithLevelAsArray($user, $needle, $exact, $limit = 30, $level)
  {
    $conn_MGR = Doctrine_Manager::connection();
      $q = Doctrine_Query::create()
      ->from($this->getTableName(). ' i')
      ->innerJoin('i.Level l')
      ->orderBy('l.level_order DESC, name ASC')
      ->limit($limit);
    if($exact)
      $q->andWhere("name = ?",$needle);
    else
      $q->andWhere("name_indexed like concat(fulltoindex(".$conn_MGR->quote($needle, 'string')."),'%') ");
    if ($level && $level != '') {
      $puls = $this->getPossibleUpperLevels($level);
        if(count($puls))
          $q->andWhereIn('l.id', $puls);
    }
    $q_results = $q->execute();
    $result = array();
    foreach($q_results as $item) {
      $result[] = array('label' => $item->getName(), 'name_indexed'=> $item->getNameIndexed(), 'value'=> $item->getId() );
    }
    return $result;
  }

  public static function CollectionToArray($collection, $key)
  {
    $result = array();
    foreach($collection as $row)
    {
      $val = $row[$key];
      $result[$val] = $val;
    }
    return $result;
  }

  public function getIdsFromArrayQuery($table, $Ids)
  {
    if(!is_array($Ids))
      $Ids = array($Ids);
        if(empty($Ids)) return array();
    $q = Doctrine_Query::create()->
         from($table)->
         andWhereIn('id', $Ids)->
         orderBy('id ASC');
    return $q->execute();
  }

  public static function newObjectFromArray($o_name, $a_array)
  {
    $object = new $o_name();
    $object->fromArray($a_array);
    return $object;
  }

  public function getLevelParents($table, $parents)
    {
      $catalogue_level =array();
      if(count($parents) == 0) return $catalogue_level ;
      $q= Doctrine_Query::create()
        ->from('CatalogueLevels cl')
        ->orderby('level_order')
        ->wherein('level_sys_name',array_keys($parents)) ;
      $result = $q->execute() ;
      foreach ($result as $catalogue) {
        $q = Doctrine_Query::create()
          ->from($table.' t')
          ->innerjoin('t.Level l')
          ->where('t.name_indexed ilike fulltoindex(?)', $parents[$catalogue->getLevelSysName()])
          ->andWhere('l.level_sys_name = ?', $catalogue->getLevelSysName());
        $elem = $q->fetchOne();
        $catalogue_level[$catalogue->getLevelName()] = array(
          'name' => $parents[$catalogue->getLevelSysName()],
          'level_sys_name' => $catalogue->getLevelSysName(),
          'level_ref' => $catalogue->getId(),
          'class' => $elem ? $elem->getId(): '') ;
      }
      return($catalogue_level) ;
  }

  public function getCatalogueUnits($ids) {
    $results = array();
    if(is_array($ids) && count($ids)) {
      $table = $this->getTableName();
      $q = Doctrine_Query::create()
        ->select('i.id, i.name, l.level_name')
        ->from($table. ' i')
        ->innerJoin('i.Level l')
        ->whereIn('i.id',$ids);
      $q_results = $q->execute();
      foreach($q_results as $result) {
        $results[] = array("id"=>$result->getId(), "name"=>$result->getName(), "level_name"=>$result->getLevel()->getLevelName());
      }
    }
    return $results;
  }
}
