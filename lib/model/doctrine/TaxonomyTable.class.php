<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class TaxonomyTable extends DarwinTable
{

  public function getTaxonByName($name,$level,$path)
  {
    $q = Doctrine_Query::create()
      ->from('Taxonomy t')
      ->where('t.name = ?', $name)
      ->andWhere('t.level_ref = ?', $level)
      ->andWhere('t.path = ?', $path);

    return $q->fetchOne();
  }

  public function getRealTaxon()
  {
    $q = Doctrine_Query::create()
      ->from('Taxonomy t')
      ->where('t.id > 0') ;
      return $q->execute() ;
  }
  
  public function getOneTaxon($taxonName) 
  {
    $response = Doctrine_Query::create()
               ->select('t.name, l.level_name')
               ->from('Taxonomy t')
               ->where('t.name_indexed = fullToIndex(?)', $taxonName)
               ->leftJoin('t.Level l')
               ->limit(2)
               ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    return $response;
  }
  
  
//ftheeten 2018 07 17
  public function checkTaxonExisting($canonicalTaxonName, $name_is_canonical=false) 
  {

    $conn = Doctrine_Manager::connection();
		if($name_is_canonical)
		{
		
	        $sql = "SELECT name, tmp[1] as canonical_name, tmp[2] as authorship , fct_rmca_sort_taxon_path_alphabetically_hstore(path) as hierarchy, fct_rmca_sort_taxon_path_alphabetically_hstore_key(path) as hierarchy_key FROM 
(SELECT *, fct_rmca_taxonomy_split_name_author(name, level_ref) as tmp, taxonomy.level_ref as taxonomy_level_ref
,
 MIN(taxonomy.level_ref) OVER () as min_taxonomy_level_ref FROM taxonomy WHERE name ~* '".$canonicalTaxonName."($|\s[A-Z]|\s\(?(von\s|van\s|de\s|da\s|le\s|la\s|dal\s|des\s)\)?)') AS taxonomy
WHERE taxonomy_level_ref= min_taxonomy_level_ref"; 
			$q = $conn->prepare($sql);
			$q->execute();
		}
		else
		{
	        $sql = "SELECT name, tmp[1] as canonical_name, tmp[2] as authorship , fct_rmca_sort_taxon_path_alphabetically_hstore(path) as hierarchy, fct_rmca_sort_taxon_path_alphabetically_hstore_key(path) as hierarchy_key FROM 
(SELECT *, fct_rmca_taxonomy_split_name_author(name, level_ref) as tmp from taxonomy WHERE fct_rmca_taxonomy_try_to_isolate_from_author(name) like fct_rmca_taxonomy_try_to_isolate_from_author(:name
)) AS taxonomy
;"; 
			$q = $conn->prepare($sql);
			$q->execute(array(':name'=> $canonicalTaxonName ));
		}
        
        $res = $q->fetchAll(PDO::FETCH_ASSOC);
		foreach($res as $key=>$row)
		{
			$res[$key]['found']=true;
			if($canonicalTaxonName==$row['canonical_name'])
			{
				$res[$key]['match']="SAME_CANONICAL_FORM";
			}
			elseif($canonicalTaxonName!=$row['name']&&$row['name']!=$row['canonical_name'])
			{
				$res[$key]['match']="OTHER_AUTHOR";
			}
			elseif($canonicalTaxonName==$row['name']&&$row['name']!=$row['canonical_name'])
			{
				$res[$key]['match']="SAME_AUTHOR";
			}
			
			
			$tmp_hierarchy_array=json_decode('{' . str_replace('"=>"', '":"', $row['hierarchy']) . '}', true);
			$name_to_rank=array_flip($tmp_hierarchy_array);
			$tmp_hierarchy_array_ref=json_decode('{' . str_replace('"=>"', '":"', $row['hierarchy_key']) . '}', true);
			
			$tmpArray=Array();
			foreach($tmp_hierarchy_array_ref as $rank=>$value)
			{
				
				$tmpWord= preg_split( '/\s+/', $value);
				
				if((int)$rank<48)
				{
					$tmpArray[$name_to_rank[$value]]=$tmpWord[0];
				}
				if((int)$rank==48)
				{
					$tmpArray[$name_to_rank[$value]]=$tmpWord[0]." ".$tmpWord[1];
				}
				elseif((int)$rank>48)
				{
					$tmpArray[$name_to_rank[$value]]=implode(" ", $tmpWord);
				}
				
			}
			
			$res[$key]['hierarchy']=$tmpArray;
			unset($res[$key]['hierarchy_key']);
		}
		
		$returned=Array();
		if(count($res)==0)
		{
			$returned['found']=false;
		}
		else
		{
			$returned['found']=true;
		}
		$returned['matches']=$res;
        return $returned;
  }

  
  //ftheeten 2017 06 26
  public function getTaxonByNameAndCollectionAndLevel($name, $level, $collections)
  {
        $conn = Doctrine_Manager::connection();
        $sql = "SELECT DISTINCT name as label, name_indexed,  id as value  FROM taxonomy 
                 INNER JOIN
                (
                       SELECT distinct unnest(string_to_array(taxon_path||'/'||taxon_ref::varchar, '/'))  as key_taxon from specimens where  collection_ref IN (".$collections.")
                        AND taxon_path is not null 
                ) AS specimens
                        ON
                        taxonomy.id::text = specimens.key_taxon
                WHERE  level_ref=:rank_id
                AND taxonomy.name_indexed LIKE CONCAT((SELECT * FROM fulltoindex(:prefix)),'%') 
                ORDER BY name LIMIT 30;";
        $q = $conn->prepare($sql);
		$q->execute(array(':rank_id'=> $level, ':prefix'=>$name ));
        $res = $q->fetchAll(PDO::FETCH_ASSOC);

        return $res;
  }
  
  //ftheeten 2017 06 26
  public function getTaxonByNameAndCollection($name, $collections)
  {
        $conn = Doctrine_Manager::connection();
        $sql = "SELECT DISTINCT name as label, name_indexed,  id as value FROM taxonomy 
                 INNER JOIN
                (
                       SELECT distinct unnest(string_to_array(taxon_path||'/'||taxon_ref::varchar, '/'))  as key_taxon from specimens where  collection_ref IN (".$collections.")
                        AND taxon_path is not null 
                ) AS specimens
                        ON
                        taxonomy.id::text = specimens.key_taxon
                WHERE taxonomy.name_indexed LIKE CONCAT((SELECT * FROM fulltoindex(:prefix)),'%') 
                ORDER BY name LIMIT 30;";
        $q = $conn->prepare($sql);
		$q->execute(array(':prefix'=>$name ));
        $res = $q->fetchAll(PDO::FETCH_ASSOC);

        return $res;
  }
  
   //ftheeten 2017 06 26
  public function getTaxonByNameAndLevel($name, $level)
  {
        $conn = Doctrine_Manager::connection();
        $sql = "SELECT DISTINCT name as label, name_indexed,  id as value  FROM taxonomy 
                 INNER JOIN
                (
                       SELECT distinct unnest(string_to_array(taxon_path||'/'||taxon_ref::varchar, '/'))  as key_taxon from specimens where  taxon_path is not null 
                ) AS specimens
                        ON
                        taxonomy.id::text = specimens.key_taxon
                WHERE  level_ref=:rank_id
                AND taxonomy.name_indexed LIKE CONCAT((SELECT * FROM fulltoindex(:prefix)),'%') 
                ORDER BY name LIMIT 30;";
        $q = $conn->prepare($sql);
		$q->execute(array(':rank_id'=> $level, ':prefix'=>$name ));
        $res = $q->fetchAll(PDO::FETCH_ASSOC);

        return $res;
  }
  
  //ftheeten 2018 06 06
   public static function getTaxaByLevel( $level)
  {
        $returned=Array();
		$q = Doctrine_Query::create()
		  ->from('Taxonomy t')
		  ->where("level_ref = ?", $level)
		  ->orderBy("t.name");
		
		$res= $q->execute();
		if($addAll===TRUE)
		{
			$returned[''] = "All";
		}
		foreach($res as $row)
		{
		  $returned[$row->getId()] = $row->getName();
		}
		return $returned;
  }
  
  //ftheeten 2018 10 12                  
  public function completeTaxonomyDisambiguateMetadata($user, $needle, $exact, $limit = 30)
  {
        $conn = Doctrine_Manager::connection();
        if($exact)
        {
            $sql = "SELECT  taxonomy.id as value, CASE WHEN count(taxonomy.id) OVER (partition BY (fct_rmca_taxonomy_split_name_author(name, level_ref))[1]) =1 THEN name
                ELSE
                name||' (taxonomy: '||fct_rmca_sort_taxon_path_alphabetically_not_indexed(path)||')'
                END as label
                  FROM taxonomy 
                   WHERE name=:term ORDER BY name LIMIT :limit;
                ";
        
        }
        else
        {
            $sql = "SELECT  taxonomy.id as value, CASE WHEN count(taxonomy.id) OVER (partition BY (fct_rmca_taxonomy_split_name_author(name, level_ref))[1]) =1 THEN name
                ELSE
                name||' (taxonomy: '||fct_rmca_sort_taxon_path_alphabetically_not_indexed(path)||')'
                END as label
                  FROM taxonomy 
                   WHERE name_indexed like concat(fulltoindex(:term),'%') ORDER BY name LIMIT :limit;
                ";
        }       
        $q = $conn->prepare($sql);
		$q->execute(array(':term' => $needle, ':limit'=> $limit));
        $results = $q->fetchAll(PDO::FETCH_ASSOC);        
		
		return  $results;
  }
  
    //ftheeten 2018 11 27                 
  public function completeTaxonomyMetadataWithRef($user, $needle, $exact, $taxon_ref, $limit = 30)
  {
        $conn = Doctrine_Manager::connection();
        if(is_numeric($taxon_ref))
        {
           if($exact)
            {
               

                $sql = "SELECT  string_agg(taxonomy.id::varchar, ';') as value, name as label
                      FROM taxonomy 
                       WHERE name=:term AND metadata_ref= :taxon_ref GROUP BY name ORDER BY name LIMIT :limit;
                    ";
            
            }
            else
            {
                $sql = "SELECT  string_agg(taxonomy.id::varchar, ';') as value, name as label
                      FROM taxonomy 
                       WHERE name_indexed like concat(fulltoindex(:term),'%') AND metadata_ref= :taxon_ref GROUP BY name ORDER BY name LIMIT :limit;
                    ";
            }       
            $q = $conn->prepare($sql);
            $q->execute(array(':term' => $needle, ':taxon_ref' => $taxon_ref, ':limit'=> $limit));
        }
        else
        {
             if($exact)
            {
               

                $sql = "SELECT  string_agg(taxonomy.id::varchar, ';') as value, name as label
                      FROM taxonomy 
                       WHERE name=:term  GROUP BY name ORDER BY name LIMIT :limit;
                    ";
            
            }
            else
            {
                $sql = "SELECT  string_agg(taxonomy.id::varchar, ';') as value, name as label
                      FROM taxonomy 
                       WHERE name_indexed like concat(fulltoindex(:term),'%')  GROUP BY name ORDER BY name LIMIT :limit;
                    ";
            }       
            $q = $conn->prepare($sql);
            $q->execute(array(':term' => $needle, ':limit'=> $limit));
        }
        $results = $q->fetchAll(PDO::FETCH_ASSOC);        
		
		return  $results;
  }
}
