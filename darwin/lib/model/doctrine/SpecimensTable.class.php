<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SpecimensTable extends DarwinTable
{
  static public $acquisition_category = array(
      'undefined' => 'Undefined',
      'collect' => 'Collect',
      'donation' => 'Donation',
      'excavation' => 'Excavation',
      'exchange' => 'Exchange',
      'exploration' => 'Exploration',
      'gift' => 'Gift',
      'internal work' => 'Internal work',
      'seizure' => 'Judicial seizure',
      'loan' => 'Loan',
      'mission' => 'Mission',
      'purchase' => 'Purchase',
      'trip' => 'Trip',
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
      ->andwhere('s.acquisition_date = ?', $object->getRawAcquisitionDate())
       //ftheeten 2018 30 11
       ->andwhere('s.gtu_from_date = ?', $object->getRawGtuFromDate())
       ->andwhere('s.gtu_to_date = ?', $object->getRawGtuToDate())
      ;
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
  public function getDistinctSubContainerStorages($type="")
  {
    if(strlen($type)>0)
    {
        $q = $this->createFlatDistinctDepend('specimens', 'sub_container_storage', $type, 'storage');
        $a =  DarwinTable::CollectionToArray($q->execute(), 'storage');
        return array_merge(array('dry'=>'dry'),$a);
    }
    else
    {
         $items = $this->createUniqFlatDistinct('specimens', 'sub_container_storage', 'sub_container_storage', true);
        return $items;
    }
  }


  /**
  * Get Distincts Container Storages of Part
  * filter by type if one given
  * @param string $type a type
  * @return array an Array of types in keys
  */
  public function getDistinctContainerStorages($type="")
  {
    if(strlen($type)>0)
    {
        $q = $this->createFlatDistinctDepend('specimens', 'container_storage', $type, 'storage');
        $a =  DarwinTable::CollectionToArray($q->execute(), 'storage');
        return array_merge(array('dry'=>'dry'),$a);
    }
    else
    {
         $items = $this->createUniqFlatDistinct('specimens', 'container_storage', 'container_storage', true);
        return $items;
    }
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
    * Get distinct Specimen_status
    * @return Doctrine_collection with distinct "specimen_status" as column
    */
    public function getDistinctSpecimenStatus()
    {
      $states = $this->createUniqFlatDistinct('specimens', 'specimen_status', 'specimen_status',true);
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
      Doctrine_Core::getTable('MyWidgets')->forceWidgetOpened($user, $category ,array_keys($req_widget));
    }
    else
      Doctrine_Core::getTable('MyWidgets')->forceWidgetOpened($user, $category ,1);
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



  /**
   * For a given sort of "storage" ($item: building, floor, room, row, shelf, container, sub_container),
   * for a list of collections availabe for the user passed as parameter and
   * for a list of filtering applied (a selection of a given building for instance,...),
   * return the list of entries found (type of storage asked - $item)
   * @param object $user User object
   * @param string $item Type of storage to be retrieved
   * @param array $criterias An array of filtering criterias to be applied
   * @return array A recordset of the list of type of storage given available, with, for each of them,
   *               the count of specimens concerned
   */
  public function findConservatories($user , $item, $criterias) {

    $sql = "SELECT COALESCE( $item ::text,'') as item,
                   COUNT(*) as ctn
            FROM Specimens s
            WHERE  collection_ref IN (
                                        SELECT fct_search_authorized_view_collections( ? )
                                     )
           ";
    $sql_params = array($user->getId());
    foreach($criterias as $k => $v) {
      $sql .= " AND COALESCE( $k , '') = ? ";
      $sql_params[] = $v;
    }
    $sql .= "
              GROUP BY item
              ORDER BY item ASC
            ";
    $conn_MGR = Doctrine_Manager::connection();
    $conn = $conn_MGR->getDbh();
    $statement = $conn->prepare($sql);
    $statement->execute($sql_params);
    $res = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $res;
  }

  /**
   * @param integer $id Id of family to use as top family
   * @return array A recordset of the list of specimens that are of the family
   *               provided
   */
  public function getFamilyContent($id) {
    $sql="
            select collection_name, id, taxon_name
            from specimens
            where taxon_ref in
            (
              select id
              from taxonomy
              where id = ?
                 or path like '%/'||?::text||'/%'
            )
            and collection_ref not in (176,316)
            order by collection_name, taxon_name, id
         ";
    $sql_params = array($id,$id);
    $conn_MGR = Doctrine_Manager::connection();
    $conn = $conn_MGR->getDbh();
    $statement = $conn->prepare($sql);
    $statement->execute($sql_params);
    $res = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $res;
  }
  
      //ftheeten 2017 10 09
  public function getSpecimenIDCorrespondingToCollectionNumber($collection_number, $code_category)
  {
    $returned=NULL;
    if(strlen($collection_number)>0)
      {
            $q = Doctrine_Query::create()
              ->select("c.record_id")
              ->from('Codes c')
              ->where('c.referenced_relation = ?', 'specimens')
              ->andwhere('c.code_category = ?', $code_category)
              ->andwhere("LOWER(REGEXP_REPLACE(TRIM(COALESCE(code_prefix,'')|| 
       COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix)), '\s+', '', 'g')) = TRIM(REGEXP_REPLACE(LOWER(?), '\s+', '', 'g'))", $collection_number);
              
            $vals = $q->execute();
           
            $returned=Array();
            foreach($vals as $val)
            {
                 $returned[]= $val->getRecordId();
            }
    }
    return $returned;
  }
  
    public function getSpecimenIDCorrespondingToMainCollectionNumber($collection_number)
   {
		return $this->getSpecimenIDCorrespondingToCollectionNumber($collection_number, 'main');
   }
   
   public function getMainCode($spec_id)
   {
		$returned="";
		$tmp = Doctrine_Query::create()
              ->select("DISTINCT COALESCE(c.code_prefix,'')||COALESCE(c.code_prefix_separator,'')||COALESCE(c.code,'')||COALESCE(c.code_suffix_separator,'')||COALESCE(code_suffix,'') as value")
              ->from('Codes c')
              ->where('c.referenced_relation = ?', 'specimens')
              ->andwhere('c.code_category = ?', "main")
			  ->andWhere('c.record_id = ?', $spec_id)->fetchOne();
	    if(isset($tmp))
		{
			$returned =$tmp["value"];
		}
		return $returned;
   }
   
        //ftheeten 2017 14 11
    public function getSpecimensInCollectionsJSON($p_collection_code, $p_host, $p_size=50, $p_page=1, $p_prefix_service_specimen="/public.php/json/getjson?", $p_prefix_service_collection="public.php/json/getcollectionjson?")
    {

      
      if((string)$p_collection_code!="-1"&&is_numeric($p_size)&&is_numeric($p_page))
      {
      
            if($p_size>0 && $p_page>0)
            {

                $conn_MGR = Doctrine_Manager::connection();
                $conn = $conn_MGR->getDbh();
               
                $rows=array();
                
                $query="SELECT a.*, count(*) OVER() AS full_count FROM (SELECT distinct 'http://'||:host||:prefix||'specimennumber='||COALESCE(codes.code_prefix,'')||COALESCE(codes.code_prefix_separator,'')||COALESCE(codes.code,'')||COALESCE(codes.code_suffix_separator,'')||COALESCE(codes.code_suffix,'') as url_specimen,
                
               'http://'||:host||:prefix||'id='||codes.record_id::varchar as technical_url_specimen, COALESCE(codes.code_prefix,'')||COALESCE(codes.code_prefix_separator,'')||COALESCE(codes.code,'')||COALESCE(codes.code_suffix_separator,'')||COALESCE(codes.code_suffix,'') AS code_display
                FROM codes WHERE 
                referenced_relation='specimens'
                AND code_category='main' and codes.record_id
                IN (SELECT id FROM specimens WHERE collection_ref = (SELECT id FROM collections where LOWER(collections.code)=LOWER(:collection) ))
                GROUP BY codes.code_prefix, codes.code_prefix_separator, codes.code, codes.code_suffix_separator, codes.code_suffix , record_id
                ORDER BY code_display) a
                LIMIT :size OFFSET :offset;";
                $stmt=$conn->prepare($query);
                $stmt->bindValue(":host", $p_host);
                $stmt->bindValue(":prefix",$p_prefix_service_specimen);
                $stmt->bindValue(":collection", $p_collection_code);
                $stmt->bindValue(":size", (int)$p_size);
                $stmt->bindValue(":offset", ((int)$p_page -1)*((int)$p_size));
                $stmt->execute();
               
                $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
                if($rs[0]["full_count"]>0)
                {
                 
                   $returned=Array();
                   $returned["count"]=$rs[0]["full_count"];
                   $returned["size_page"]=$p_size;
                   $returned["current_page"]=(int)$p_page;
                   $last_page=ceil($rs[0]["full_count"]/$p_size);
                   $returned["last_page"]=$last_page;
                   $returned["this_url"]="http://".$p_host."/".$p_prefix_service_collection."collection=".$p_collection_code."&page=".$p_page."&size=".$p_size;
                   if((int)$p_page<$last_page)
                   {
                        $returned["next_url"]="http://".$p_host."/".$p_prefix_service_collection."collection=".$p_collection_code."&page=".($p_page+1)."&size=".$p_size;
                   }
                   else
                   {
                        $returned["next_url"]="http://".$p_host."/".$p_prefix_service_collection."collection=".$p_collection_code."&page=".$last_page."&size=".$p_size;
                   }
                   $returned["last_url"]="http://".$p_host."/".$p_prefix_service_collection."collection=".$p_collection_code."&page=".$last_page."&size=".$p_size;
                   $records=Array();
                   foreach($rs as $item)
                   {
                        $row["code_display"]=$item["code_display"];
                        $row["url_specimen"]=$item["url_specimen"];
                        $row["technical_url_specimen"]=$item["technical_url_specimen"];
                        $records[]=$row;
                   }
                   $returned["records"]=$records;
                   return $returned;
                       
                }
             }
        }
        return Array();
     }

    //ftheeten 2017 12 04
     public function getCollectionsAllAccessPointsJSON($p_host, $p_prefix_url="/public.php/search/getcollectionjson?")
     {
   
        $conn_MGR = Doctrine_Manager::connection();
        $conn = $conn_MGR->getDbh();
               
        $rows=array();
        
        $query="SELECT code as collection_code, name as collection_name,
         'http://'||:host||:prefix||'collection='||code as accespoint_collection
        FROM collections;";
        $stmt=$conn->prepare($query);
        $stmt->bindValue(":host", $p_host);
        $stmt->bindValue(":prefix", $p_prefix_url);
        $stmt->execute();
        $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
       
         if(count($rs)>0)
         {
              return $rs;
         }
         return Array();
     }

 public function getJSON($p_mode="NUMBER", $p_specimencode=NULL, $p_public_url = "https://darwin.naturalsciences.be/darwin/search/view/id/")
    {
  
       
      if((string)$p_specimencode!="-1")
      {
      
            
            $conn_MGR = Doctrine_Manager::connection();
            $conn = $conn_MGR->getDbh();
           
            $rows=array();
            
            $query="
            SELECT distinct  id, 
            :public_url||id::varchar as public_url,
            collection_name, collection_code, (SELECT modification_date_time FROM users_tracking where referenced_relation='specimens' and record_id= max(specimens.id)  GROUP BY modification_date_time ,users_tracking.id having users_tracking.id=max(users_tracking.id) limit 1) as last_modification, code_display, string_agg(DISTINCT taxon_path::varchar, ',') as taxon_paths, string_agg(DISTINCT taxon_ref::varchar, ',') as taxon_ref,
                    string_agg(DISTINCT taxon_name, ',') as taxon_name,
                    string_agg(DISTINCT  history, ';') as history_identification
                    ,
                     string_agg(DISTINCT gtu_country_tag_value, ';') as country,  string_agg(DISTINCT gtu_others_tag_value, ';') as geographical,          
                    
                    
                    fct_mask_date(gtu_from_date,
                    gtu_from_date_mask) as date_from_display,
                    fct_mask_date(gtu_to_date,
                    gtu_to_date_mask) as date_to_display,
                    coll_type,
                                
                               
                                  
                    longitude, latitude
                     ,count(*) OVER() AS full_count,collector_ids, 
                     (SELECT string_agg(formated_name, ',') from people where id = any(collector_ids)) as collectors
                      , donator_ids,
                      (SELECT array_agg(formated_name) from people where id = any(donator_ids)) as donators
                      ,
                      string_agg(distinct tag_locality, '; ') as localities	
                      from 
                    (SELECT specimens.id,
                    collections.code as collection_code, collections.name as collection_name, 
                    COALESCE(codes.code_prefix,'')||COALESCE(codes.code_prefix_separator,'')||COALESCE(codes.code,'')||COALESCE(codes.code_suffix_separator,'')||COALESCE(codes.code_suffix,'') as code_display, full_code_indexed, taxon_path, taxon_ref, collection_ref ,CASE WHEN station_visible THEN  gtu_country_tag_indexed ELSE NULL END AS gtu_country_tag_indexed, 
                    CASE WHEN station_visible THEN  specimens.gtu_country_tag_value ELSE NULL END AS gtu_country_tag_value,
                            CASE WHEN station_visible THEN specimens.gtu_others_tag_indexed ELSE NULL END as localities_indexed,
                     CASE WHEN station_visible THEN specimens.gtu_others_tag_value  ELSE NULL END as gtu_others_tag_value
                    , taxon_name,
                     spec_coll_ids as collector_ids , 
                     spec_don_sel_ids as donator_ids,
                    CASE WHEN station_visible THEN gtu_from_date ELSE NULL END AS gtu_from_date,
                   CASE WHEN station_visible THEN  gtu_from_date_mask  END AS gtu_from_date_mask,
                   CASE WHEN station_visible  THEN gtu_to_date ELSE NULL END AS gtu_to_date,
                    CASE WHEN station_visible THEN gtu_to_date_mask ELSE NULL END AS gtu_to_date_mask,
                    type as coll_type,
                    case
                    when gtu_country_tag_indexed is not null AND station_visible then
                    unnest(gtu_country_tag_indexed) 
                else null end
                    as country_unnest,                   

                               
                    CASE WHEN station_visible THEN gtu_location[1] ELSE NULL END as latitude,
                    CASE WHEN station_visible THEN gtu_location[0] ELSE NULL END as longitude,
                    notion_date as identification_date, 
                    notion_date_mask as identification_date_mask,
                    coalesce(fct_mask_date(notion_date, notion_date_mask)||': ','')||taxon_name as history,
                     specimens.gtu_ref,
                    group_type, sub_group_type,
                    tag

                    , CASE WHEN station_visible THEN group_type||'-'||sub_group_type||':'||tag ELSE NULL END as tag_locality 
                    FROM specimens
                    LEFT JOIN
                    collections ON
                    specimens.collection_ref=collections.id
                    LEFT JOIN 
                    codes
                    ON codes.referenced_relation='specimens' and code_category='main' and specimens.id=codes.record_id
                    
                   
                    
                    LEFT JOIN identifications
                    on identifications.referenced_relation='specimens'
                    and specimens.id= identifications.record_id
                    and notion_concerned='taxonomy'
                    LEFT JOIN tags
                    ON specimens.gtu_ref=tags.gtu_ref
                    order by group_ref
                    ) as specimens
                ";
                
                    
                    
                 if($p_mode=="NUMBER")
                {
                      $query.=" WHERE full_code_indexed=(SELECT * FROM fulltoindex(:number))";
                }
                elseif($p_mode=="ID")
                {
                      $query.=" WHERE specimens.id=:id";
                }
                       
                    
                    
                 $query.=" GROUP BY id, 
                    collection_name,
                    collection_code,
                    code_display         
                    ,
                    gtu_from_date,
                    gtu_from_date_mask,
                    gtu_to_date,
                    gtu_to_date_mask,
                    coll_type
                    , 
                    longitude, latitude
                     ,
                     collector_ids
                      , donator_ids  ";    
                 
                 $query=$query."  LIMIT 20;";
               
                $stmt=$conn->prepare($query);
                $stmt->bindValue(":public_url", $p_public_url);
                if($p_mode=="NUMBER")
                {
                     $stmt->bindValue(":number", $p_specimencode);
                }
                elseif($p_mode=="ID")
                {
                     $stmt->bindValue(":id", $p_specimencode);
                }
              
                $stmt->execute();
                $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
        
               $tmpTypesMultimedia=Array("thumbnails", "image_links", "3d_snippets");
               foreach($tmpTypesMultimedia as $field)
               {                
                    $array_urls_thumbnails=explode(";", $rs[0]["urls_".$field]);
                    $array_category_thumbnails=explode(";", $rs[0]["image_category_".$field]);
                    $array_contributor_thumbnails=explode(";", $rs[0]["contributor_".$field]);
                    $array_disclaimer_thumbnails=explode(";", $rs[0]["disclaimer_".$field]);
                    $array_license_thumbnails=explode(";", $rs[0]["license_".$field]);
                    $array_display_order_thumbnails=explode(";", $rs[0]["display_order_".$field]);
                  
                    $tmpArray=Array();
                    foreach($array_display_order_thumbnails as $key=>$value)
                    {                                              
                        $tmpArray["urls_".$field][$value]=$array_urls_thumbnails[$key];
                        $tmpArray["image_category_".$field][$value]=$array_category_thumbnails[$key];
                        $tmpArray["contributor_".$field][$value]=$array_contributor_thumbnails[$key];
                        $tmpArray["disclaimer_".$field][$value]=$array_disclaimer_thumbnails[$key];
                        $tmpArray["license_".$field][$value]=$array_license_thumbnails[$key];
                        $tmpArray["display_order_".$field][$value]=$array_display_order_thumbnails[$key];
                    }

                    foreach($tmpArray as $key=>$value)
                    {
                        $rs[0][$key]=implode(";", $tmpArray[$key]);
                    }
                }
                if($rs[0]["full_count"]>0)
                {
             
                    return $rs;
                   
                }
            }
            return Array();
    }

    public function getSpecimensInInstitutionJSON($p_institution_protocol, $p_institution_identifier, $p_host, $p_size=50, $p_page=1, $p_prefix_service_specimen="/public.php/json/getjson?", $p_prefix_service_collection="public.php/json/get_institution_identifier_json?")
    {

      
      if(strlen($p_institution_protocol)>0&&strlen($p_institution_identifier)>0&&is_numeric($p_size)&&is_numeric($p_page))
      {
      
            if($p_size>0 && $p_page>0)
            {

                $conn_MGR = Doctrine_Manager::connection();
                $conn = $conn_MGR->getDbh();
               
                $rows=array();
                
                $query="SELECT a.*, count(*) OVER() AS full_count FROM (SELECT distinct 'http://'||:host||:prefix||'specimennumber='||COALESCE(codes.code_prefix,'')||COALESCE(codes.code_prefix_separator,'')||COALESCE(codes.code,'')||COALESCE(codes.code_suffix_separator,'')||COALESCE(codes.code_suffix,'') as url_specimen,
                
               'http://'||:host||:prefix||'id='||codes.record_id::varchar as technical_url_specimen, COALESCE(codes.code_prefix,'')||COALESCE(codes.code_prefix_separator,'')||COALESCE(codes.code,'')||COALESCE(codes.code_suffix_separator,'')||COALESCE(codes.code_suffix,'') AS code_display
                FROM codes WHERE 
                referenced_relation='specimens'
                AND code_category='main' 
				AND EXISTS (SELECT 1 FROM specimens INNER JOIN identifiers ON specimens.institution_ref= identifiers.record_id WHERE identifiers.referenced_relation='people' AND LOWER(identifiers.protocol)=:institution_protocol AND identifiers.value =  :institution_identifier  AND codes.record_id=specimens.id)
                GROUP BY codes.code_prefix, codes.code_prefix_separator, codes.code, codes.code_suffix_separator, codes.code_suffix , record_id
                ORDER BY code_display) a
                LIMIT :size OFFSET :offset;";
                $stmt=$conn->prepare($query);
                $stmt->bindValue(":host", $p_host);
                $stmt->bindValue(":prefix",$p_prefix_service_specimen);
                $stmt->bindValue(":institution_protocol", strtolower($p_institution_protocol));
				$stmt->bindValue(":institution_identifier", $p_institution_identifier);
                $stmt->bindValue(":size", (int)$p_size);
                $stmt->bindValue(":offset", ((int)$p_page -1)*((int)$p_size));
                $stmt->execute();
              
                $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
                if($rs[0]["full_count"]>0)
                {
                 
                   $returned=Array();
                   $returned["count"]=$rs[0]["full_count"];
                   $returned["size_page"]=$p_size;
                   $returned["current_page"]=(int)$p_page;
                   $last_page=ceil($rs[0]["full_count"]/$p_size);
                   $returned["last_page"]=$last_page;
                   $returned["this_url"]="http://".$p_host."/".$p_prefix_service_collection."identifier_protocol=".$p_institution_protocol."&identifier_value=".$p_institution_identifier."&page=".$p_page."&size=".$p_size;
                   if((int)$p_page<$last_page)
                   {
                        $returned["next_url"]="http://".$p_host."/".$p_prefix_service_collection."identifier_protocol=".$p_institution_protocol."&identifier_value=".$p_institution_identifier."&page=".($p_page+1)."&size=".$p_size;
                   }
                   else
                   {
                        $returned["next_url"]="http://".$p_host."/".$p_prefix_service_collection."identifier_protocol=".$p_institution_protocol."&identifier_value=".$p_institution_identifier."&page=".$last_page."&size=".$p_size;
                   }
                   $returned["last_url"]="http://".$p_host."/".$p_prefix_service_collection."identifier_protocol=".$p_institution_protocol."&identifier_value=".$p_institution_identifier."&page=".$last_page."&size=".$p_size;
                   $records=Array();
                   foreach($rs as $item)
                   {
                        $row["code_display"]=$item["code_display"];
                        $row["url_specimen"]=$item["url_specimen"];
                        $row["technical_url_specimen"]=$item["technical_url_specimen"];
                        $records[]=$row;
                   }
                   $returned["records"]=$records;
                   return $returned;
                       
                }
             }
        }
        return Array();
     }
	 
	public function getSpecimensForIdentifiersPeopleJSON($p_identifier_protocol, $p_identifier_value, $identifier_role, $p_host, $p_size=50, $p_page=1, $p_prefix_service_specimen="/public.php/json/getjson?", $p_prefix_service_collection="public.php/json/get_institution_identifier_json?")
    {

      
      if(strlen($p_identifier_protocol)>0&&strlen($p_identifier_value)>0&&is_numeric($p_size)&&is_numeric($p_page))
      {
      
            if($p_size>0 && $p_page>0)
            {
				 $id=Doctrine_Core::getTable('Identifiers')->getLinkedId($p_identifier_protocol, $p_identifier_value, "people");
				  if($id!==null)
				  {
					 
					  if($role == 'determinator')
					  {
						$build_query = ":id =any(spec_ident_ids) " ;
						
						
					  }
					  elseif($role == 'collector')
					  {
						 
						  $build_query = "(:id =any(spec_coll_ids) OR EXISTS (SELECT cp.id FROM catalogue_people cp WHERE cp.referenced_relation= 'expeditions' AND cp.people_ref=:id AND specimens.expedition_ref=cp.record_id ))" ;
						  

					  }
					  elseif($role == 'donator')
					  {
						$build_query .= ":id =any(spec_don_sel_ids) " ;
					
					  }
					  else
					  {
						  $build_query = "(:id =any(spec_coll_ids||spec_ident_ids||spec_don_sel_ids) OR EXISTS (SELECT cp.id FROM catalogue_people cp WHERE cp.referenced_relation= 'expeditions' AND cp.people_ref=:id AND specimens.expedition_ref=cp.record_id ))" ;
						 

					  }
					 
				  
					$conn_MGR = Doctrine_Manager::connection();
					$conn = $conn_MGR->getDbh();
				   
					$rows=array();
					
					$query="SELECT a.*, count(*) OVER() AS full_count FROM (SELECT distinct 'http://'||:host||:prefix||'specimennumber='||COALESCE(codes.code_prefix,'')||COALESCE(codes.code_prefix_separator,'')||COALESCE(codes.code,'')||COALESCE(codes.code_suffix_separator,'')||COALESCE(codes.code_suffix,'') as url_specimen,
					
				   'http://'||:host||:prefix||'id='||codes.record_id::varchar as technical_url_specimen, COALESCE(codes.code_prefix,'')||COALESCE(codes.code_prefix_separator,'')||COALESCE(codes.code,'')||COALESCE(codes.code_suffix_separator,'')||COALESCE(codes.code_suffix,'') AS code_display
					FROM codes WHERE 
					referenced_relation='specimens'
					AND code_category='main' 
					AND EXISTS (SELECT 1 FROM specimens WHERE $build_query  AND codes.record_id=specimens.id )
					GROUP BY codes.code_prefix, codes.code_prefix_separator, codes.code, codes.code_suffix_separator, codes.code_suffix , record_id
					ORDER BY code_display) a
					LIMIT :size OFFSET :offset;";
					$stmt=$conn->prepare($query);
					$stmt->bindValue(":host", $p_host);
					$stmt->bindValue(":prefix",$p_prefix_service_specimen);
					$stmt->bindValue(":id", $id);
					
					$stmt->bindValue(":size", (int)$p_size);
					$stmt->bindValue(":offset", ((int)$p_page -1)*((int)$p_size));
					$stmt->execute();
				  
					$rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
					if($rs[0]["full_count"]>0)
					{
					 
					   $returned=Array();
					   $returned["count"]=$rs[0]["full_count"];
					   $returned["size_page"]=$p_size;
					   $returned["current_page"]=(int)$p_page;
					   $last_page=ceil($rs[0]["full_count"]/$p_size);
					   $returned["last_page"]=$last_page;
					   $returned["this_url"]="http://".$p_host."/".$p_prefix_service_collection."identifier_protocol=".$p_institution_protocol."&identifier_value=".$p_institution_identifier."&page=".$p_page."&size=".$p_size;
					   if((int)$p_page<$last_page)
					   {
							$returned["next_url"]="http://".$p_host."/".$p_prefix_service_collection."identifier_protocol=".$p_institution_protocol."&identifier_value=".$p_institution_identifier."&page=".($p_page+1)."&size=".$p_size;
					   }
					   else
					   {
							$returned["next_url"]="http://".$p_host."/".$p_prefix_service_collection."identifier_protocol=".$p_institution_protocol."&identifier_value=".$p_institution_identifier."&page=".$last_page."&size=".$p_size;
					   }
					   $returned["last_url"]="http://".$p_host."/".$p_prefix_service_collection."identifier_protocol=".$p_institution_protocol."&identifier_value=".$p_institution_identifier."&page=".$last_page."&size=".$p_size;
					   $records=Array();
					   foreach($rs as $item)
					   {
							$row["code_display"]=$item["code_display"];
							$row["url_specimen"]=$item["url_specimen"];
							$row["technical_url_specimen"]=$item["technical_url_specimen"];
							$records[]=$row;
					   }
					   $returned["records"]=$records;
					   return $returned;
                    }    
                }
             }
        }
        return Array();
     }
	 
	 public function getSpecimensByLink($url)
	 {
		 $query = Doctrine_Query::create()->from('Specimens s')->andWhere("EXISTS (select l.id FROM ExtLinks l WHERE referenced_relation='specimens' AND l.record_id = s.id AND fulltoindex(l.url)=fulltoindex(?))", $url);
		 return $query;		 
	 }
		

}
