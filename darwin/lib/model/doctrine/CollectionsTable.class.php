<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CollectionsTable extends DarwinTable
{
  public function completeAsArray($user, $needle, $exact, $limit = 30, $level)
  {
    $conn_MGR = Doctrine_Manager::connection();
    $q = Doctrine_Query::create()
      ->from('Collections col')
      ->orderBy('name ASC')
      ;
    if($exact)
      $q->andWhere("name = ?",$needle);
    else
      $q->andWhere("name_indexed like concat('%',fulltoindex(".$conn_MGR->quote($needle, 'string')."),'%') ");

    if($user && ! $user->isA(Users::ADMIN) ) {
      $q->leftJoin('col.CollectionsRights r ON col.id=r.collection_ref AND r.user_ref = '.$user->getId())
        ->andWhere('r.id is not null OR col.is_public = TRUE');

      $q->andWhere('r.db_user_type >= ?',USERS::ENCODER);
    }
    $q_results = $q->execute();
    $result = array();
    foreach($q_results as $item) {
      $result[] = array('label' => $item->getName(), 'name_indexed'=> $item->getNameIndexed(), 'value'=> $item->getId() );
    }
    return $result;
  }

  public function fetchByInstitutionList($user, $institutionId = null, $public_only = false, $only_encodable = false)
  {
    $q = Doctrine_Query::create()
      ->select('p.*, col.*,r.id,r.db_user_type, CONCAT(col.path,col.id,E\'/\') as col_path_id,  regexp_split_to_array(CONCAT(col.path,col.id,E\'/\'), E\'/\') as col_path_id2')
      ->from('People p')
      ->innerJoin('p.Collections col')
      ->andWhere('p.is_physical = false')
      ->orderBy('p.id ASC, col_path_id2 ASC, col.name ASC');


    if($user && ! $user->isA(Users::ADMIN) ) {
      $q->leftJoin('col.CollectionsRights r ON col.id=r.collection_ref AND r.user_ref = '.$user->getId())
        ->andWhere('r.id is not null OR col.is_public = TRUE');

      if($only_encodable) {
        $q->andWhere('r.db_user_type >= ?',USERS::ENCODER);
      }

    } elseif(!$user || $user->isA(Users::ADMIN)  ) {
      $q->leftJoin('col.CollectionsRights r ON col.id=r.collection_ref AND r.user_ref = -1');
    }

    if($public_only) {
      $q->andWhere('col.is_public = TRUE');
    }
    if($institutionId !== null) {
      $q->andWhere('p.id = ?', $institutionId);
    }

    return $q->execute();
  }

  public function getDistinctCollectionByInstitution($inst = null)
  {
    $q = Doctrine_Query::create()
      ->select('c.*, CONCAT(c.path,c.id,E\'/\') as col_path_id')
      ->from('Collections c')
      ->orderBy('col_path_id ASC,c.name ASC');
    if($inst !== null) {
      $q->andWhere('c.institution_ref = ?',$inst);
    }

    $res = $q->execute();
    $results = array('' =>'');
    foreach($res as $row)
    {
      $results[$row->getId()] = $row->__toString();
    }
    return $results;
  }

  public function getCollectionByName($name)
  {
    $q = Doctrine_Query::create()
      ->from('collections c')
      ->where('c.name = ?', $name)
      ->orderBy('c.code ASC');

    return $q->fetchOne();
  }

  public function fetchByCollectionParent($curent_user, $user_id, $collection_id)
  {
    $expr = "%/$collection_id/%" ;
    $q = Doctrine_Query::create()
      ->select('c.*, r.*, CONCAT(c.path,c.id,E\'/\') as coll_path_id')
      ->from('Collections c')
      ->leftJoin('c.CollectionsRights r ON c.id=r.collection_ref AND r.user_ref = '.$user_id);
    if(! $curent_user->isAtLeast(Users::ADMIN))
      $q->innerJoin('c.CollectionsRights r2 ON c.id=r2.collection_ref AND r2.db_user_type >=4 AND r2.user_ref = '.$curent_user->getId());

    $q->andWhere('c.path like ?', $expr)
      ->orderBy('coll_path_id ASC');
    return $q->execute();
  }

  public function getAllCollections($public_only = false)
  {
    $q = Doctrine_Query::create()
      ->from('Collections c')
      ->orderBy('path,name ASC');
    if($public_only)
      $q->andWhere('c.is_public = TRUE');
    return $q->execute();
  }

  public function getAndUpdateLastCode($collectionId)
  {
    if (!isset($collectionId))
      return 0;
    $conn = Doctrine_Manager::connection();
    $collId = $conn->quote($collectionId, 'integer');
    $sql = "UPDATE collections SET code_last_value = code_last_value+1 WHERE id = $collId RETURNING code_last_value";
    $returnedVal = $conn->fetchOne($sql);
    return $returnedVal;
  }

  public function getInstitutionNameByCollection($collection_ref)
  {
    $q = Doctrine_Query::create()
      ->select('p.*')
      ->from('People p')
      ->innerJoin('p.Collections col')
      ->where('col.id = ?', $collection_ref);
    return $q->fetchOne();
  }

  public function getAllAvailableCollectionsFor($user)
  {
    $user_id = $user->getId();
    $q = Doctrine_Query::create()
      ->select('c.*')
      ->from('Collections c')  ;   
    if(!($user->isA(Users::ADMIN)))
      $q->leftJoin('c.CollectionsRights r')
        ->addwhere('r.user_ref = ?',$user_id)
        ->addwhere('db_user_type > 1');
    $q->orderBy('name ASC');
    $res = $q->execute();
    $results = array(0 =>'All');
    foreach($res as $row)
    {
      $results[$row->getId()] = $row->getName();
    }
    return $results;
  }

  public function afterSaveAddCode($collectionId,$specimenId) 
  {
    if (
      $collectionId !== null &&
      $collectionId !== '' &&
      $specimenId !== null &&
      $specimenId !== ''
    ) {
      $conn = Doctrine_Manager::connection();
      $conn->quote($collectionId, 'integer');
      $conn->quote($specimenId, 'integer');
      $conn->getDbh()->exec('BEGIN TRANSACTION;');
      $conn->getDbh()->exec("SELECT fct_after_save_add_code($collectionId, $specimenId)");
      $conn->getDbh()->exec('COMMIT;');
    }
    return 0;
  }
  
    //ftheeten 2018 04 27
  
    //ftheeten 2018 04 27
  
  public function countSpecimens($collectionID ="/", $year="", $creation_date_min="", $creation_date_max="", $ig_num="", $includeSubcollection=false, $detailSubCollections=false )
  {
  
    $fields =Array();
    $groups =Array();
    $where =Array();
    $orders=Array();

   
    if(strlen($year)>0)
    {
        $fields[2]="year";
        $where[]= "year = :year";
        $groups[]="year";
    }
    
    if(strlen($creation_date_min)>0)
    {
        $fields[2]="year";
        $where[]= "specimen_creation_date >= :creation_date_min::timestamp";
        $groups[]="year";
    }
    
    if(strlen($creation_date_max)>0)
    {
        $orders[]="year";
        $fields[2]="year";
        $where[]= "specimen_creation_date <= :creation_date_max::timestamp";
        $groups[]="year";
    }
    
     if(strlen($ig_num)>0)
    {
         $orders[]="ig_num";
        $fields[3]="ig_num";
         $groups[]="ig_num";
        $where[]= "ig_num = :ig_num";
    }
    
    
    $fields[4]="SUM(nb_records) as nb_database_records";
    $fields[5]="SUM(specimen_count_min) as nb_physical_specimens_low";
    $fields[6]="SUM(specimen_count_max) as nb_physical_specimens_high";
    
    if($detailSubCollections)
    {
        $orders[]="collection_name";
        $fields[1]="collection_name";
        $groups[]="collection_name";
        $includeSubcollection=true;
    }
    
    if($includeSubcollection||$collectionID=="/")
    {
        
        if($collectionID=="/")
        {
            $where[]= "collection_path LIKE  :id||'%'";            
        }
        else
        {
            //$where[]= "collections.id::varchar  = :ida";
            $where[]= "collection_path||'/'||collection_ref||'/' LIKE '%/'||:idb||'/%'";
        }       
    }
    else
    {
         $where[]= "collection_ref::varchar  = :id";
    }
    
    ksort($fields);
    
    $all_fields=implode(", ", $fields);
   
    $sql ="SELECT ".$all_fields." FROM tv_reporting_count_all_specimens_by_collection_year_ig WHERE ".implode(" AND ", $where);
    
    if(count($groups)>0)
    {
        $sql = $sql." GROUP BY ".implode(", ", $groups);
    }
    
     if(count($orders)>0)
    {
        $sql = $sql." ORDER BY ".implode(", ", $orders);
    }
    
    $conn = Doctrine_Manager::connection();
    $q = $conn->prepare($sql);
    
     if(strlen($year)>0)
    {
        $q->bindParam(":year", $year);
    }
    
     if(strlen($ig_num)>0)
    {
       $q->bindParam(":ig_num", $ig_num, PDO::PARAM_STR);
    }
    
    if($includeSubcollection||$collectionID=="/")    
    {
        
        if($collectionID=="/")
        {
            $q->bindParam(":id", $collectionID, PDO::PARAM_STR);
            
        }
        else
        {
            //$q->bindParam(":ida", $collectionID, PDO::PARAM_STR);
            $q->bindParam(":idb", $collectionID, PDO::PARAM_STR);
        }       
    }
    else
    {
         $q->bindParam(":id", $collectionID, PDO::PARAM_STR);
    }
    
    if(strlen($creation_date_min)>0)
    {
         $q->bindParam(":creation_date_min", $creation_date_min, PDO::PARAM_STR);
    }
    
    if(strlen($creation_date_max)>0)
    {
        $q->bindParam(":creation_date_max", $creation_date_max, PDO::PARAM_STR);
    }
       
   
   
    $q->execute();
    
    $items=$q->fetchAll(PDO::FETCH_ASSOC);

    return $items;
  }
  
    public function countTypeSpecimens($collectionID ="/", $year="", $creation_date_min="", $creation_date_max="", $ig_num="", $includeSubcollection=false, $detailSubCollections=false )
  {
  
    $fields =Array();
    $groups =Array();
    $where =Array();
    $orders=Array();
    if($detailSubCollection>0)
    {
        $fields[0]="collection_name";
        $fields[1]="type";
        $groups[]="collection_name";
        $groups[]="type";
        
    }
   
    if(strlen($year)>0)
    {
        $fields[2]="year";
        $where[]= "year = :year";
        $groups[]="year";
    }
    
    if(strlen($creation_date_min)>0)
    {
        $fields[2]="year";
        $where[]= "specimen_creation_date >= :creation_date_min::timestamp";
        $groups[]="year";
    }
    
    if(strlen($creation_date_max)>0)
    {
        $fields[2]="year";
        $where[]= "specimen_creation_date <= :creation_date_max::timestamp";
        $groups[]="year";
    }
    
     if(strlen($ig_num)>0)
    {
        $fields[3]="ig_num";
         $groups[]="ig_num";
        $where[]= "ig_num = :ig_num";
    }
    
    
    $fields[4]="SUM(nb_records) as nb_database_records";
    $fields[5]="SUM(specimen_count_min) as nb_physical_specimens_low";
    $fields[6]="SUM(specimen_count_max) as nb_physical_specimens_high";
    
    $orders[]="type";
    $fields[1]="type";
    $groups[]="type";
    
    if($detailSubCollections)
    {
        
        $orders[]="collection_name";
        $fields[0]="collection_name";
        
        $groups[]="collection_name";
        $includeSubcollection=true;
    }
    
    if($includeSubcollection||$collectionID=="/")
    {
        
        if($collectionID=="/")
        {
            $where[]= "collection_path LIKE  :id||'%'";            
        }
        else
        {
            //$where[]= "collections.id::varchar  = :ida";
            $where[]= "collection_path||'/'||collection_ref||'/' LIKE '%/'||:idb||'/%'";
        }       
    }
    else
    {
         $where[]= "collection_ref::varchar  = :id";
    }
    
    $where[]="type <> 'specimen'";
    $where[]="type <> 'specimens'";
    $where[]="type IS NOT NULL";
    $where[]="type <> ''";
    
    ksort($fields);
    
    $all_fields=implode(", ", $fields);
   
    $sql ="SELECT ".$all_fields." FROM tv_reporting_count_all_specimens_type_by_collection_ref_year_ig  WHERE ".implode(" AND ", $where);
    
    if(count($groups)>0)
    {
        $sql = $sql." GROUP BY ".implode(", ", $groups);
    }
    
     if(count($orders)>0)
    {
        $sql = $sql." ORDER BY ".implode(", ", $orders);
    }
    
    $conn = Doctrine_Manager::connection();
    $q = $conn->prepare($sql);
    
     if(strlen($year)>0)
    {
        $q->bindParam(":year", $year);
    }
    
     if(strlen($ig_num)>0)
    {
       $q->bindParam(":ig_num", $ig_num, PDO::PARAM_STR);
    }
    
    if($includeSubcollection||$collectionID=="/")    
    {
        
        if($collectionID=="/")
        {
            $q->bindParam(":id", $collectionID, PDO::PARAM_STR);
            
        }
        else
        {
            //$q->bindParam(":ida", $collectionID, PDO::PARAM_STR);
            $q->bindParam(":idb", $collectionID, PDO::PARAM_STR);
        }       
    }
    else
    {
         $q->bindParam(":id", $collectionID, PDO::PARAM_STR);
    }
    
    if(strlen($creation_date_min)>0)
    {
         $q->bindParam(":creation_date_min", $creation_date_min, PDO::PARAM_STR);
    }
    
    if(strlen($creation_date_max)>0)
    {
        $q->bindParam(":creation_date_max", $creation_date_max, PDO::PARAM_STR);
    }
       
   
   
    $q->execute();
    
    $items=$q->fetchAll(PDO::FETCH_ASSOC);

    return $items;
  }
  
  
  public function countTaxaInSpecimen($collectionID ="/", $year="", $creation_date_min="", $creation_date_max="", $ig_num="", $includeSubcollection=false, $detailSubCollections=false )
  {
  
    $fields =Array();
    $groups =Array();
    $where =Array();
    $orders=Array();
    if($detailSubCollection>0)
    {
        $fields[0]="collection_name";
        $fields[1]="level_name";
        $groups[]="collection_name";
        $groups[]="level_name";
        
    }
   
    if(strlen($year)>0)
    {
        $fields[2]="year";
        $where[]= "year = :year";
        $groups[]="year";
    }
    
    if(strlen($creation_date_min)>0)
    {
        $fields[2]="year";
        $where[]= "creation_date >= :creation_date_min::timestamp";
        $groups[]="year";
    }
    
    if(strlen($creation_date_max)>0)
    {
        $fields[2]="year";
        $where[]= "creation_date <= :creation_date_max::timestamp";
        $groups[]="year";
    }
    
     if(strlen($ig_num)>0)
    {
        $fields[3]="ig_num";
         $groups[]="ig_num";
        $where[]= "ig_num = :ig_num";
    }
    
    
    $fields[4]="COUNT(DISTINCT taxonomy_id) as nb_database_records";
   // $fields[5]="SUM(specimen_count_min) as nb_physical_specimens_low";
   // $fields[6]="SUM(specimen_count_max) as nb_physical_specimens_high";
    
    $orders[]="level_ref DESC";
    $groups[]="level_ref";
    $fields[1]="level_name";
    $groups[]="level_name";
    
    if($detailSubCollections)
    {
        
        $orders[]="collection_name";
        $fields[0]="collection_name";
        
        $groups[]="collection_name";
        $includeSubcollection=true;
    }
    
    if($includeSubcollection||$collectionID=="/")
    {
        
        if($collectionID=="/")
        {
            $where[]= "collection_path LIKE  :id||'%'";            
        }
        else
        {
            //$where[]= "collections.id::varchar  = :ida";
            $where[]= "collection_path||'/'||collection_ref||'/' LIKE '%/'||:idb||'/%'";
        }       
    }
    else
    {
         $where[]= "collection_ref::varchar  = :id";
    }
    
    
    
    ksort($fields);
    
    $all_fields=implode(", ", $fields);
   
    $sql ="SELECT ".$all_fields." FROM tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig  WHERE ".implode(" AND ", $where);
    
    if(count($groups)>0)
    {
        $sql = $sql." GROUP BY ".implode(", ", $groups);
    }
    
     if(count($orders)>0)
    {
        $sql = $sql." ORDER BY ".implode(", ", $orders);
    }
    
  
    $conn = Doctrine_Manager::connection();
    $q = $conn->prepare($sql);
    
     if(strlen($year)>0)
    {
        $q->bindParam(":year", $year);
    }
    
     if(strlen($ig_num)>0)
    {
       $q->bindParam(":ig_num", $ig_num, PDO::PARAM_STR);
    }
    
    if($includeSubcollection||$collectionID=="/")    
    {
        
        if($collectionID=="/")
        {
            $q->bindParam(":id", $collectionID, PDO::PARAM_STR);
            
        }
        else
        {
            //$q->bindParam(":ida", $collectionID, PDO::PARAM_STR);
            $q->bindParam(":idb", $collectionID, PDO::PARAM_STR);
        }       
    }
    else
    {
         $q->bindParam(":id", $collectionID, PDO::PARAM_STR);
    }
    
    if(strlen($creation_date_min)>0)
    {
         $q->bindParam(":creation_date_min", $creation_date_min, PDO::PARAM_STR);
    }
    
    if(strlen($creation_date_max)>0)
    {
        $q->bindParam(":creation_date_max", $creation_date_max, PDO::PARAM_STR);
    }
       
   
   
    $q->execute();
    
    $items=$q->fetchAll(PDO::FETCH_ASSOC);

    return $items;
  }
  
  //ftheeten 2018 07 02
   public function getSpatialCoverage($collectionID, $includeSubcollection=false)
   {
	   
	   $sql = "SELECT 
		string_agg(DISTINCT gtu_country_tag_value, ',' ORDER BY gtu_country_tag_value) as countries, 
        min(gtu_location[0])::varchar||','||min(gtu_location[1])||','||max(gtu_location[0])::varchar||','||max(gtu_location[1]) as bbox
       FROM specimens";

	   
	   if($includeSubcollection||$collectionID=="/")
		{
        
			if($collectionID=="/")
			{
				$where[]= "collection_path LIKE  :id||'%'";            
			}
			else
			{
				$where[]= "collection_path||'/'||collection_ref||'/' LIKE '%/'||:idb||'/%'";
			}       
		}
		else
		{
			 $where[]= "collection_ref::varchar  = :id";
		}
		$sql= $sql." WHERE ".implode(" AND ", $where);
		$conn = Doctrine_Manager::connection();
		$q = $conn->prepare($sql);
		if($includeSubcollection||$collectionID=="/")    
		{
			
			if($collectionID=="/")
			{
				$q->bindParam(":id", $collectionID, PDO::PARAM_STR);
				
			}
			else
			{
				$q->bindParam(":idb", $collectionID, PDO::PARAM_STR);
			}       
		}
		else
		{
			 $q->bindParam(":id", $collectionID, PDO::PARAM_STR);
		}
	    $q->execute();
    
		$items=$q->fetchAll(PDO::FETCH_ASSOC);

		return $items;
   }
   
   
   //ftheeten 2018 07 02
   public function getTemporalCoverage($collectionID, $includeSubcollection=false)
   {
	   
	   $sql = "SELECT MIN(fct_mask_date(gtu_from_date, gtu_from_date_mask)) AS date_min, 
		MAX(
		CASE WHEN gtu_to_date_mask=0
		THEN
			fct_mask_date(
			gtu_from_date
			, 
			gtu_from_date_mask
			)
		ELSE
			fct_mask_date(
			gtu_to_date
			, 
			gtu_to_date_mask
			)
		END
		) 
		AS date_to
		 FROM specimens ";

	   
	   if($includeSubcollection||$collectionID=="/")
		{
        
			if($collectionID=="/")
			{
				$where[]= "collection_path LIKE  :id||'%'";            
			}
			else
			{
				$where[]= "collection_path||'/'||collection_ref||'/' LIKE '%/'||:idb||'/%'";
			}       
		}
		else
		{
			 $where[]= "collection_ref::varchar  = :id";
		}
		
		$where[]= "gtu_from_date_mask !=0";
		$sql= $sql." WHERE ".implode(" AND ", $where);
		$conn = Doctrine_Manager::connection();
		$q = $conn->prepare($sql);
		if($includeSubcollection||$collectionID=="/")    
		{
			
			if($collectionID=="/")
			{
				$q->bindParam(":id", $collectionID, PDO::PARAM_STR);
				
			}
			else
			{
				$q->bindParam(":idb", $collectionID, PDO::PARAM_STR);
			}       
		}
		else
		{
			 $q->bindParam(":id", $collectionID, PDO::PARAM_STR);
		}
	    $q->execute();
    
		$items=$q->fetchAll(PDO::FETCH_ASSOC);

		return $items;
   }
   
    //ftheeten 2017 07 05
    public static function getAllAvailableCollectionsHierarchical()
  {

    $q = Doctrine_Query::create()
      ->select('c.*')
      ->from('Collections c')  ;   
    $q->orderBy('name ASC');
    $res = $q->execute();
    $results = array(0 =>'All');
    $resultsTmp = array();
    $indexedResults = array();
    $mapping=array();
    $alphaPaths=array();
    foreach($res as $row)
    {
      $resultsTmp[$row->getId()] = $row->getName();
      $indexedResults[$row->getId()] = $row->getNameIndexed();
      $mapping[$row->getId()] = $row->getPath().'/'.$row->getId().'/';

    }
    foreach($mapping as $key=>$path)
    {
      $alphaPath=CollectionsTable::getAlphabeticalPath($path, $indexedResults);
      $alphaPaths[$alphaPath]=$key;
    }
    ksort($alphaPaths);
    foreach($alphaPaths as $alphaPath=>$colId)
    {
        $levels= substr_count( $alphaPath, "/");
        $results[$colId]= str_repeat("-", $levels-1).$resultsTmp[$colId];
    }
    return $results;
  }
  
  //ftheeten 2017 >08 01 ?
  public static function getAlphabeticalPath($path, $colNames)
  {

    $arrayPath=explode("/", $path);
    $returned="/";
    foreach($arrayPath as $item)
    {
        if(is_numeric($item))
        {            
            $collname=$colNames[$item];
             $returned.=$collname."/";
        }
    }
    return $returned;
  }
}