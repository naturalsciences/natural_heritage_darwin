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
  
    
  //madam 2019 04 09 
  /*
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
  */
  
  public function getOneTaxon($taxonName, $taxonLevel)
  {	  
	$conn = Doctrine_Manager::connection();
	//$taxonName=$conn->quote($taxonName);
	if($taxonLevel=='')
	{
		$sql = "SELECT t.id as id, t.name as name, l.level_name as level, fct_rmca_sort_taxon_path_alphabetically_not_indexed(t.path) as hierarchy
			FROM taxonomy t
			LEFT JOIN catalogue_levels l
			ON t.level_ref=l.id
			WHERE t.name_indexed LIKE CONCAT(fulltoindex(:taxon_name),'%') AND l.level_type='taxonomy'";  
	}
	else
	{	
		$sql = "SELECT t.id as id, t.name as name, l.level_name as level, fct_rmca_sort_taxon_path_alphabetically_not_indexed(t.path) as hierarchy
			FROM taxonomy t
			LEFT JOIN catalogue_levels l
			ON t.level_ref=l.id
			WHERE t.name_indexed LIKE CONCAT(fulltoindex(:taxon_name),'%') AND l.level_sys_name=:taxon_level AND l.level_type='taxonomy'";  
	}
	
	$q = $conn->prepare($sql);
	if($taxonLevel=='')
	{
		$q->execute(array(':taxon_name'=> $taxonName));
	}
	else 
	{
		$q->execute(array(':taxon_name'=> $taxonName, ':taxon_level'=>$taxonLevel));
	}
	$response = $q->fetchAll(PDO::FETCH_ASSOC);
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
 MIN(taxonomy.level_ref) OVER () as min_taxonomy_level_ref FROM taxonomy WHERE name ~* '".str_replace(" ", " (\([^\(]+\) )?",str_replace("(","\(",str_replace(")","\)",trim($canonicalTaxonName))))."($|\s+[A-Z]|\s+\(|\s+\(??(von(\s+|'')|van(\s+|'')|de(\s+|'')|da(\s+|'')|le(\s+|'')|la(\s+|'')|dal(\s+|'')|des(\s+|'')|zu(\s+|'')|zur(\s+|'')|dos(\s+|''))\)?)') AS taxonomy
WHERE taxonomy_level_ref= min_taxonomy_level_ref ORDER BY level_ref"; 
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
   public static function getTaxaByLevel( $level, $with_taxon_ref=FALSE)
  {
        $returned=Array();
        if($with_taxon_ref)
        {
            $q = Doctrine_Query::create()
            ->select('t.*, m.taxonomy_name as taxonomy_name')
            ->from('Taxonomy t')
            ->leftJoin('t.TaxonomyMetadata m on t.metadata_ref=m.id')            
            ->where("level_ref = ?", $level)
            ->orderBy("t.name");
        }
        else
        {
            $q = Doctrine_Query::create()
            ->from('Taxonomy t')
            ->where("level_ref = ?", $level)
            ->orderBy("t.name");
		}
		$res= $q->execute();
		if($addAll===TRUE)
		{
			$returned[''] = "All";
		}
		foreach($res as $row)
		{
          if($with_taxon_ref)
          {
            $returned[$row->getId()] = $row->getName()." (".$row->getTaxonomyName().")";
          }
          else
          {
            $returned[$row->getId()] = $row->getName();
          }
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
               

                $sql = "WITH find_taxa AS
(SELECT  string_agg(taxonomy.id::varchar, ';') as value, CASE WHEN status <> 'valid' THEN name||' ('||status||')' ELSE name END as label
                      FROM taxonomy 
                       WHERE name=:term AND metadata_ref= :taxon_ref GROUP BY level_ref,name, status ORDER BY level_ref, name LIMIT :limit
                    )

,
 find_taxa_2 AS
(
SELECT group_id as group_id_tmp FROM darwin2.classification_synonymies INNER 
JOIN (SELECT unnest((string_to_array(find_taxa.value, ';')))::int as tmp_taxa from find_taxa) find_taxa
ON record_id=tmp_taxa and referenced_relation='taxonomy'
)

SELECT * FROM (
SELECT * FROM find_taxa
UNION
SELECT taxonomy.id::text, name||' ('||status||')' FROM classification_synonymies
INNER JOIN  find_taxa_2 ON group_id =group_id_tmp AND record_id NOT in (SELECT unnest((string_to_array(find_taxa.value, ';')))::int as tmp_taxa from find_taxa)
INNER JOIN taxonomy ON taxonomy.id=record_id) a ORDER BY LEVENSHTEIN(SUBSTR(label,1, ".strlen($needle)."), :term), label";
            
            }
            else
            {
                $sql = "WITH find_taxa AS
(SELECT  string_agg(taxonomy.id::varchar, ';') as value, CASE WHEN status <> 'valid' THEN name||' ('||status||')' ELSE name END as label
                      FROM taxonomy 
                       WHERE name_indexed like concat(fulltoindex(:term),'%') AND metadata_ref= :taxon_ref GROUP BY level_ref,name, status ORDER BY level_ref, name LIMIT :limit
                    )

,
 find_taxa_2 AS
(
SELECT group_id as group_id_tmp FROM darwin2.classification_synonymies INNER 
JOIN (SELECT unnest((string_to_array(find_taxa.value, ';')))::int as tmp_taxa from find_taxa) find_taxa
ON record_id=tmp_taxa and referenced_relation='taxonomy'
)

SELECT * FROM (
SELECT * FROM find_taxa
UNION
SELECT taxonomy.id::text, name||' ('||status||')' FROM classification_synonymies
INNER JOIN  find_taxa_2 ON group_id =group_id_tmp AND record_id NOT in (SELECT unnest((string_to_array(find_taxa.value, ';')))::int as tmp_taxa from find_taxa)
INNER JOIN taxonomy ON taxonomy.id=record_id) a ORDER BY LEVENSHTEIN(SUBSTR(label,1, ".strlen($needle)."), :term), label";
            }       
            $q = $conn->prepare($sql);
            $q->execute(array(':term' => $needle, ':taxon_ref' => $taxon_ref, ':limit'=> $limit));
        }
        else
        {
             if($exact)
            {
               

                $sql = "WITH find_taxa AS
(SELECT  string_agg(taxonomy.id::varchar, ';') as value, CASE WHEN status <> 'valid' THEN name||' ('||status||')' ELSE name END as label
                       ,count(id) as cpt FROM taxonomy 
                       WHERE name=:term  GROUP BY level_ref,name, status ORDER BY level_ref, name, status LIMIT :limit
                    )

,
 find_taxa_2 AS
(
SELECT group_id as group_id_tmp FROM darwin2.classification_synonymies INNER 
JOIN (SELECT unnest((string_to_array(find_taxa.value, ';')))::int as tmp_taxa from find_taxa) find_taxa
ON record_id=tmp_taxa and referenced_relation='taxonomy'
)

SELECT * FROM (
SELECT value,label  FROM find_taxa WHERE cpt=1
UNION
SELECT id::text, name||' (Family : '||fct_rmca_sort_taxon_get_parent_level_text(id,34)||' Order : '||fct_rmca_sort_taxon_get_parent_level_text(id,28)||')' FROM taxonomy INNER JOIN (SELECT unnest(string_to_array(value,';')) as id_unnest FROM find_taxa WHERE cpt>1) a
ON id=id_unnest::int
UNION
SELECT taxonomy.id::text, name||' ('||status||')' FROM classification_synonymies
INNER JOIN  find_taxa_2 ON group_id =group_id_tmp AND record_id NOT in (SELECT unnest((string_to_array(find_taxa.value, ';')))::int as tmp_taxa from find_taxa)
INNER JOIN taxonomy ON taxonomy.id=record_id) a ORDER BY LEVENSHTEIN(SUBSTR(label,1, ".strlen($needle)."), :term), label";
            
            }
            else
            {
                $sql = "WITH find_taxa AS
(SELECT  string_agg(taxonomy.id::varchar, ';') as value, CASE WHEN status <> 'valid' THEN name||' ('||status||')' ELSE name END as label
                    ,count(id) as cpt   FROM taxonomy 
                       WHERE name_indexed like concat(fulltoindex(:term),'%')  GROUP BY level_ref,name, status ORDER BY level_ref, name LIMIT :limit
                    )

,
 find_taxa_2 AS
(
SELECT group_id as group_id_tmp FROM darwin2.classification_synonymies INNER 
JOIN (SELECT unnest((string_to_array(find_taxa.value, ';')))::int as tmp_taxa from find_taxa) find_taxa
ON record_id=tmp_taxa and referenced_relation='taxonomy'
)

SELECT * FROM (
SELECT value,label  FROM find_taxa WHERE cpt=1
UNION
SELECT id::text, name||' (Family : '||fct_rmca_sort_taxon_get_parent_level_text(id,34)||' Order : '||fct_rmca_sort_taxon_get_parent_level_text(id,28)||')' FROM taxonomy INNER JOIN (SELECT unnest(string_to_array(value,';')) as id_unnest FROM find_taxa WHERE cpt>1) a
ON id=id_unnest::int
UNION
SELECT taxonomy.id::text, name||' ('||status||')' FROM classification_synonymies
INNER JOIN  find_taxa_2 ON group_id =group_id_tmp AND record_id NOT in (SELECT unnest((string_to_array(find_taxa.value, ';')))::int as tmp_taxa from find_taxa)
INNER JOIN taxonomy ON taxonomy.id=record_id) a ORDER BY LEVENSHTEIN(SUBSTR(label,1, ".strlen($needle)."), :term), label";
            }       
            $q = $conn->prepare($sql);
            $q->execute(array(':term' => $needle, ':limit'=> $limit));
        }
        $results = $q->fetchAll(PDO::FETCH_ASSOC);        
		
		return  $results;
  }
  
  public function getTaxonomyReport($id_taxa)
  {
	   $conn = Doctrine_Manager::connection();
	  //$page=$page-1;
	  //$offset=(int)$page*(int)$size;
	  $sql= "
			  with a as 
		(select taxonomy.id , fct_rmca_sort_taxon_path_alphabetically_hstore(path||parent_ref||'/'||taxonomy.id::varchar||'/' )
		|| hstore('nb_records', count(specimens.id)::varchar)
		|| hstore('physical_specimen_min', sum(specimens.specimen_count_min)::varchar)
		|| hstore('physical_specimen_max', sum(specimens.specimen_count_max)::varchar)
		|| hstore('nb_types',  COUNT(CASE WHEN type ='specimen' THEN NULL ELSE type END)::varchar)
		|| hstore('type_details', string_agg(distinct type, '; '  order by type))
		|| hstore('container_details', string_agg(distinct container_type, '; ' order by container_type))
		|| hstore('sub_container_details', string_agg(distinct sub_container_type, '; ' order by sub_container_type))
		|| hstore('storage_details', string_agg(distinct container_storage, '; ' order by container_storage))
		|| hstore('sub_storage_details', string_agg(distinct sub_container_storage, '; ' order by sub_container_storage))
		as main_array ,
		RANK () OVER ( 
				ORDER BY fct_rmca_sort_taxon_path_alphabetically(path||parent_ref||'/'||taxonomy.id::varchar||'/' ) DESC
			) rank 


		from taxonomy 
		LEFT JOIN specimens on taxonomy.id=taxon_ref

		where path||parent_ref::varchar||'/'||taxonomy.id::varchar||'/' like '%/'|| :taxon ||'/%'
		group by taxonomy.id
		
		)


		select (populate_record(null::rmca_taxon_report, a.main_array)).*  from a order by rank;
	  ";
	  
	   $q = $conn->prepare($sql);
       $q->execute(array(':taxon' => $id_taxa));
        
        $results = $q->fetchAll(PDO::FETCH_ASSOC);        
		
		return  $results;
  }
}
