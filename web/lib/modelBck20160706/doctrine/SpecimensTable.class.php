<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SpecimensTable extends DarwinTable
{
  static public $acquisition_category = array(
      'undefined' => 'Undefined',
      'donation' => 'Donation',
      'exchange' => 'Exchange',
      'internal work' => 'Internal work',
      'loan' => 'Loan',
      'mission' => 'Mission',
      'purchase' => 'Purchase',
      'seizure' => 'Judicial seizure',
      'trip' => 'Trip',
      'excavation' => 'Excavation',
      'exploration' => 'Exploration',
      'collect' => 'Collect',
      );

  protected static $widget_array = array(
    'collection_ref' => 'refCollection' ,
    'category' => 'refCollection' ,
    'gtu_ref' => 'refGtu' ,
    'station_visible' => 'refGtu' ,
    'taxon_ref' => 'refTaxon' ,
    'host_taxon_ref' => 'refHosts' ,
    'host_specimen_ref' => 'refHosts' ,
    'host_relationship' => 'refHost' ,
    'litho_ref' => 'refLitho' ,
    'chrono_ref' => 'refChrono' ,
    'lithology_ref' => 'refLithology' ,
    'mineral_ref' => 'refMineral' ,
    'ig_ref' => 'refIgs' ,
    'expedition_ref' => 'refExpedition' ,
    'acquisition_category' => 'acquisitionCategory' ,
    'acquisition_date_mask' => 'acquisitionCategory' ,
    'acquisition_date' => 'acquisitionCategory' ,
    'collecting_method' => 'tool' ,
    'collecting_tool' => 'tool' ,
  );


  public function findDuplicate($object)
  {
    $q = Doctrine_Query::create()
      ->from('Specimens s')
      ->where('s.collection_ref = ?', $object->getCollectionRef())
      ->andwhere('s.expedition_ref is not distinct from ?', $object->getExpeditionRef())
      ->andwhere('s.gtu_ref is not distinct from ?', $object->getGtuRef())
      ->andwhere('s.taxon_ref is not distinct from ?', $object->getTaxonRef())
      ->andwhere('s.litho_ref is not distinct from ?', $object->getLithoRef())
      ->andwhere('s.chrono_ref is not distinct from ?', $object->getChronoRef())
      ->andwhere('s.lithology_ref is not distinct from ?', $object->getLithologyRef())
      ->andwhere('s.mineral_ref is not distinct from ?', $object->getMineralRef())
      ->andwhere('s.host_taxon_ref is not distinct from ?', $object->getHostTaxonRef())
      ->andwhere('s.ig_ref is not distinct from ?', $object->getIgRef())
      ->andwhere('s.acquisition_category = ?', $object->getAcquisitionCategory())
      ->andwhere('s.acquisition_date = ?', $object->getRawAcquisitionDate());
    return $q->fetchOne();
  }


  public function getRandomPublicSpec($number)
  {
    $q = Doctrine_Query::create()
      ->from('Specimens s')
      ->where('s.collection_is_public = true')
      ->orderBy('random()')
      ->limit($number)
      ->useResultCache(true)
      ->setResultCacheLifeSpan( 60 * 30 ) // 30 min
      ;
    return $q->execute();
  }

  /**
  * Get differents acquisition categories
  * @return array of key/value of acquisition categories
  */
  public static function getDistinctCategories()
  {
      try{
          $i18n_object = sfContext::getInstance()->getI18n();
      }
      catch( Exception $e )
      {
          return self::$acquisition_category;
      }
      return array_map(array($i18n_object, '__'), self::$acquisition_category);
  }


  /**
  * Get Distincts Buildings of Part
  * @return array an Array of types in keys
  */
  public function getDistinctBuildings()
  {
    $items = $this->createUniqFlatDistinct('specimens', 'building', 'building', true);
    return $items;
  }

  /**
  * Get Distincts Floor of Part
  * @return array an Array of types in keys
  */
  public function getDistinctFloors($building = null)
  {
    $items = $this->createUniqFlatDistinct('specimens', 'floor', 'floor', true);
    return $items;
  }

  /**
  * Get Distincts Room of Part
  * @return array an Array of types in keys
  */
  public function getDistinctRooms($building = null, $floor = null)
  {
    $items = $this->createUniqFlatDistinct('specimens', 'room', 'room', true);
    return $items;
  }

  /**
  * Get Distincts Row of Part
  * @return array an Array of types in keys
  */
  public function getDistinctRows($building = null, $floor = null, $room = null)
  {
    $items = $this->createUniqFlatDistinct('specimens', 'row', 'row', true);
    return $items;
  }

    /**
  * Get Distincts Column of Part
  * @return array an Array of types in keys
  */
  public function getDistinctCols($building = null, $floor = null, $room = null)
  {
    $items = $this->createUniqFlatDistinct('specimens', 'col', 'col', true);
    return $items;
  }

  /**
  * Get Distincts Shelve of Part
  * @return array an Array of types in keys
  */
  public function getDistinctShelfs($building = null, $floor = null, $room = null, $rows = null)
  {
    $items = $this->createUniqFlatDistinct('specimens', 'shelf', 'shelf', true);
    return $items;
  }

  /**
  * Get Distincts Container of Part
  * @return array an Array of types in keys
  */
  public function getDistinctContainerTypes()
  {
    $items = $this->createUniqFlatDistinct('specimens', 'container_type', 'container_type', true);
    return $items;
  }

  /**
  * Get Distincts Sub Container of Part
  * @return array an Array of types in keys
  */
  public function getDistinctSubContainerTypes()
  {
    $items = $this->createUniqFlatDistinct('specimens', 'sub_container_type', 'sub_container_type', true);
    return $items;
  }

  /**
  * Get Distincts Sub Container of Part
  * @return array an Array of types in keys
  */
  public function getDistinctParts()
  {
    $items = $this->createUniqFlatDistinct('specimens', 'specimen_part', 'specimen_part', true);
    return $items;
  }

  /**
  * Get Distincts status of Part
  * @return array an Array of types in keys
  */
  public function getDistinctStatus()
  {
    $items = $this->createUniqFlatDistinct('specimens', 'specimen_status', 'specimen_status', true);
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
    $q = $this->createFlatDistinctDepend('specimens', 'container_storage', $type, 'storage');
    $a =  DarwinTable::CollectionToArray($q->execute(), 'storage');
    return array_merge(array('dry'=>'dry'),$a);
  }

    /**
    * Get distinct Types
    * @return Doctrine_collection with distinct "types" as column
    */
    public function getDistinctTypes()
    {
      $items = $this->createUniqFlatDistinct('specimens', 'type', 'type', true);
      return $items;
    }

    /**
    * Get distinct Type groups
    * @return Doctrine_collection with distinct "type groups" as column
    */
    public function getDistinctTypeGroups()
    {
      $items = $this->createUniqFlatDistinct('specimens', 'type_group', 'type_group', true);
      return $items;
    }

    /**
    * Get distinct Type searches
    * @return Doctrine_collection with distinct "type searches" as column
    */
    public function getDistinctTypeSearches()
    {
      $items = $this->createUniqFlatDistinct('specimens', 'type_search', 'type_search', true);
      return $items;
    }

    /**
    * Get distinct Sexes
    * @return Doctrine_collection with distinct "sexes" as column
    */
    public function getDistinctSexes()
    {
      $sexes = $this->createUniqFlatDistinct('specimens', 'sex', 'sex', true);
      return $sexes;
    }

    /**
    * Get distinct States
    * @return Doctrine_collection with distinct "states" as column
    */
    public function getDistinctStates()
    {
      $states = $this->createUniqFlatDistinct('specimens', 'state', 'state',true);
      return $states;
    }

    /**
    * Get distinct Stages
    * @return Doctrine_collection with distinct "stages" as column
    */
    public function getDistinctStages()
    {
      $stages = $this->createUniqFlatDistinct('specimens', 'stage', 'stage', true);
      return $stages;
    }

    /**
    * Get distinct Social statuses
    * @return Doctrine_collection with distinct "social statuses" as column
    */
    public function getDistinctSocialStatuses()
    {
      $items = $this->createUniqFlatDistinct('specimens', 'social_status', 'social_status', true);
      return $items;
    }

    /**
    * Get distinct Rock forms
    * @return Doctrine_collection with distinct "rock forms" as column
    */
    public function getDistinctRockForms()
    {
      $items = $this->createUniqFlatDistinct('specimens', 'rock_form', 'rock_form', true);
      return $items;
    }

  public function getSpecimenByRef($collection_id,$taxon_id)
  {
          $q = Doctrine_Query::create()
              ->from('specimens s')
              ->where('s.collection_ref = ?', $collection_id)
              ->andWhere('s.taxon_ref = ?', $taxon_id);

          return $q->fetchOne();
  }

  /**
  * Set required widget visible and opened
  */
  public function getRequiredWidget($criterias, $user, $category, $all = 0)
  {
    if (!$all && $criterias)
    {
      $req_widget = array() ;
      $default_values = array(0,"Undefined","undefined","not applicable","0001/01/01");
      foreach($criterias as $key => $fields)
      {
        if ($key == "rec_per_page") continue ;
        if (!$fields) continue ;

        if(isset(self::$widget_array[$key]) && !in_array($fields,$default_values))
          $req_widget[self::$widget_array[$key]] = 1 ;
      }
      Doctrine::getTable('MyWidgets')->forceWidgetOpened($user, $category ,array_keys($req_widget));
    }
    else
      Doctrine::getTable('MyWidgets')->forceWidgetOpened($user, $category ,1);
  }

  public function fetchOneWithRights($id, $user)
  {
    $q = Doctrine_Query::create()
      ->select('s.*, collection_ref in (select fct_search_authorized_encoding_collections('.$user->getId().')) as has_encoding_rights')
      ->from('specimens s')
      ->where('id = ?',$id);
    if (!$user->isA(Users::ADMIN)){
      $q->andWhere('collection_ref in (select fct_search_authorized_view_collections('.$user->getId().'))');
    }
    return $q->fetchOne();
  }


  /**
  * Fetch all specimens by an array of ids
  * @param array $ids Ids of specimen to search
  * @return Doctrine_collection
  */
  public function getByMultipleIds(array $ids, $user_id = -1, $is_admin = false)
  {
    if( empty($ids))
      return $ids;

    $q = DQ::create()
      ->from('Specimens s')
      ->wherein('s.id', $ids)
      ->orderBy('s.id');

    if(!$is_admin)
      $q->andWhere('s.collection_ref in (select fct_search_authorized_encoding_collections(?))',$user_id);
    return $q->execute();
  }

  public function createUniqFlatDistinct($table, $column,  $new_col='item', $empty = false)
  {
    if(! isset($this->flat_results)){
      $q = Doctrine_Query::create()
        ->useResultCache(true)
        ->setResultCacheLifeSpan(5) //5 sec
        ->From('FlatDict')
        ->select('dict_field, dict_value')
        ->andwhere('referenced_relation = ?', $table)
        ->orderBy("dict_value ASC");
      $res = $q->execute();
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

  public function findConservatories($user , $item, $cirterias) {
    $sql = "SELECT COALESCE(".$item."::text,'') as item,  count(*) as ctn FROM Specimens s
      WHERE  collection_ref in (select fct_search_authorized_view_collections(".$user->getId().'))';
    $sql_params = array();
    foreach($cirterias as $k => $v) {
      $sql .= " AND COALESCE(".$k.", '') = ?";
      $sql_params[] = $v;
    }
    $sql .= " GROUP BY item order by item asc";

    $conn_MGR = Doctrine_Manager::connection();
    $conn = $conn_MGR->getDbh();
    $statement = $conn->prepare($sql);
    $statement->execute($sql_params);
    $res = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $res;
  }
}