<?php

function returnAuthorizedColumns()
{
    $returned=array();
    $returned[]="id";
    $returned[]="code_display";
    $returned[]="taxon_path";
    $returned[]="taxon_ref";
    $returned[]="taxon_name";
    $returned[]="gtu_country_tag_value";
    $returned[]="gtu_others_tag_value";
    $returned[]="gtu_from_date";
    $returned[]="gtu_from_date_mask";
    $returned[]="gtu_to_date";
    $returned[]="gtu_to_date_mask";
    $returned[]="date_from_display";
    $returned[]="date_to_display";
    $returned[]="coll_type";
    $returned[]="longitude";
    $returned[]="latitude";
    $returned[]="full_count";
    $returned[]="collector";
    $returned[]="url";
    $returned[]="image_category"; 
    $returned[]="contributor";
    $returned[]="disclaimer";
    $returned[]="license";     
    
        
    return $returned;
}

function testIsAuthorizedColumn($column)
{
    if(in_array($column,returnAuthorizedColumns())===FALSE)
    {
        throw new Exception("error");
    }
    else
    {
        return true;
    }
}


function create_regex_taxon_list($pattern)
{
    return str_replace(" ","",str_replace(",","|", $pattern));
}

function generate_filter_localities_by_country($country_array, &$arraySubstitutions)
{
    $returned="";
    if(count($country_array)>0)
    {

        $i=0;
        foreach($country_array as $countryTmp)
        {
            $varName=":tmpLocCountry".(string)$i;
            $arraySubstitutions[$varName]=$countryTmp;
            if($i>0)
            {
                $returned=$returned."||'|'||";
            }
            $returned=$returned."(SELECT * FROM fulltoindex($varName))";
            $i++;
        }
         $returned="(SELECT * FROM fulltoindex(gtu_country_tag_indexed::text)) ~ (SELECT '('||". $returned."||')')" ;
    }
    return $returned;
}
//end helpers

function connect_to_darwin()
{
    $conn=NULL;
    $servername="";
    $port="5432";
    $dbname="";
    $username="";
    $password="";
    $cstring="pgsql:host=$servername;port=$port;dbname=$dbname";
    try
    {
        $conn=new PDO($cstring, $username, $password, array(PDO::ATTR_PERSISTENT => false));
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch(Exception $e)
    {
		//print("No data available");
		print($e->getMessage());
    }
    return $conn;
}


function json_darwin_get_collections()
{
    $conn=connect_to_darwin();
    $rows=array();
    $query="SELECT DISTINCT id, name_full_path as name FROM darwin2.v_collections_full_path_recursive_spec_count WHERE count_record>0 AND is_public ORDER BY name_full_path";
    $stmt=$conn->prepare($query);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
    header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
	
}

 function json_darwin_get_sub_collections($id_coll)
{
	 $conn=connect_to_darwin();
	 $rows=array();
    $query="SELECT DISTINCT id, name_full_path as name FROM darwin2.v_collections_full_path_recursive_spec_count WHERE path||'/'||id::varchar||'/' LIKE '%/'||:id_coll||'/%'  AND is_public ORDER BY name_full_path";
    $stmt=$conn->prepare($query);
	 $stmt->bindValue(":id_coll", $id_coll);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
	header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
	
}

function json_darwin_get_ig_num($pattern, $collection=-1)
{
	
    $conn=connect_to_darwin();
    $rows=array();
    if((string)$collection !="-1")
    {
        $query="SELECT DISTINCT ig_num as value, LENGTH(ig_num) as len FROM specimens WHERE LOWER(ig_num) LIKE CONCAT('%', (SELECT * FROM fulltoindex(:pattern)), '%') AND collection_path||'/'||collection_ref::varchar||'/' LIKE '%/'||:collection||'/%' 
			ORDER by LENGTH(ig_num), ig_num LIMIT 30;
            ";
			$stmt=$conn->prepare($query);
		$stmt->bindValue(":pattern", $pattern);
		$stmt->bindValue(":collection", $collection);
    }
    else
    {
        $query="SELECT DISTINCT ig_num as value, LENGTH(ig_num) as len FROM specimens WHERE LOWER(ig_num) LIKE CONCAT('%', (SELECT * FROM fulltoindex(:pattern)), '%') 
			ORDER by LENGTH(ig_num), ig_num LIMIT 30;
            ";
			$stmt=$conn->prepare($query);
		$stmt->bindValue(":pattern", $pattern);
    }

    
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
     header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
}

function json_darwin_get_code($pattern, $collection=-1)
{
	
    $conn=connect_to_darwin();
    $rows=array();
    if((string)$collection !="-1")
    {
        $query="SELECT DISTINCT COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'') as value FROM codes WHERE code_category='main' AND referenced_relation='specimens' AND
			full_code_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:pattern)), '%') AND 
			record_id IN (SELECT id FROM specimens WHERE collection_path||'/'||collection_ref::varchar||'/' LIKE '%/'||:collection||'/%')  
			ORDER by value LIMIT 30;
            ";
			$stmt=$conn->prepare($query);
		$stmt->bindValue(":pattern", $pattern);
		$stmt->bindValue(":collection", $collection);
    }
    else
    {
        $query="SELECT DISTINCT COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'') as value FROM codes WHERE code_category='main' AND referenced_relation='specimens' AND
			full_code_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:pattern)), '%') 
			ORDER by value LIMIT 30;
            ";
			$stmt=$conn->prepare($query);
		$stmt->bindValue(":pattern", $pattern);
    }

    
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
     header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
}

function json_darwin_get_code_by_taxon($pattern, $collection, $taxon_id )
{
    $conn=connect_to_darwin();
    $rows=array();
    if($collection !="-1")
    {
        $query="SELECT DISTINCT COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'') as value FROM codes WHERE code_category='main' AND referenced_relation='specimens' AND
			full_code_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:pattern)), '%') AND 
			record_id IN (SELECT id FROM specimens WHERE collection_ref IN (:collection) and taxon_path ~ '/:taxon_id/') and full_code_indexed like '%rmca%'
			ORDER by value LIMIT 30;
            ";
     }
     else
     {
        $query="SELECT DISTINCT COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,'') as value FROM codes WHERE code_category='main' AND referenced_relation='specimens' AND
			full_code_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:pattern)), '%') AND 
			:collection =-1
             and full_code_indexed like '%rmca%'
			ORDER by value LIMIT 30;
            ";
     }     
       
    $stmt=$conn->prepare($query);
     $stmt->bindValue(":pattern", $pattern);
    $stmt->bindValue(":collection", $collection);
    $taxon_id=create_regex_taxon_list($taxon_id);
    $stmt->bindValue(":taxon_id", $taxon_id);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
     header('Content-Type: application/json; charset=utf-8');
    header(json_encode($rs));
    $conn=null;
}


function json_darwin_get_countries_by_specimen($pattern, $collection_id, $taxon_id)
{
    $conn=connect_to_darwin();
    $rows=array();
    
    $flag_collection_id=FALSE;
    $flag_taxon_id=FALSE;   

    $query="SELECt DISTINCT '$pattern' as value, 0 as sortval UNION SELECt DISTINCT gtu_country_tag_value as value,  strpos(replace(gtu_country_tag_indexed[1], ' ',''), (SELECT * FROM fulltoindex(:pattern))) as sortval FROM specimens WHERE gtu_country_tag_indexed::varchar LIKE  CONCAT('%', (SELECT * FROM fulltoindex(:pattern)), '%') ";
    
    if($collection_id>-1)
    {
        $query.=" AND collection_path||collection_ref::varchar||'/' LIKE '%'||:collection_id||'%' ";
        $flag_collection_id=TRUE;
    }
    
    if($taxon_id >-1)
    {
        $query.= " AND  taxon_path||'/'||taxon_ref ~ :taxon_id";
        $flag_taxon_id=TRUE;   
    }
    $query.= " ORDER BY sortval, value ;";

    $stmt=$conn->prepare($query);
    $stmt->bindValue(":pattern", $pattern);
    if($flag_collection_id===TRUE)
    {
        $stmt->bindValue(":collection_id", $collection_id);
    }
    if($flag_taxon_id===TRUE)
    {
        $taxon_id=create_regex_taxon_list($taxon_id);
        $taxon_id="/".$taxon_id."/";
        $stmt->bindValue(":taxon_id", $taxon_id);
    }
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
     header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
    
}

function json_darwin_get_types()
{
    $conn=connect_to_darwin();
    $rows=array();
    $query="SELECT DISTINCT TRIM(regexp_split_to_table(dict_value, '/')) as name  FROM flat_dict WHERE              
        dict_field='type' AND LOWER(dict_value) NOT LIKE '%specimen%'
        AND LOWER(dict_value) NOT LIKE '%voucher%'  ORDER BY name;";
    $stmt=$conn->prepare($query);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
    header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
}


function json_darwin_get_localities_by_country($pattern, $collection_id, $taxa_ids, $country_names)
{
    $conn=connect_to_darwin();
    $rows=array();
    if(strlen($pattern)>=2)
    {
        $flag_collection_id=FALSE;
        $flag_taxa_ids=FALSE;
        $flag_country_names=FALSE;
        $arrayCountryNames=array();
        
        $query="SELECT DISTINCT LOWER(value) as value, strpos(replace(LOWER(value), ' ',''), (SELECT * FROM fulltoindex(:pattern,TRUE))) sortval FROM (SELECt DISTINCT :pattern as value UNION SELECT gtu_others_tag_value AS value  FROM specimens WHERE gtu_others_tag_indexed::text LIKE  CONCAT('%', (SELECT * FROM fulltoindex(:pattern,TRUE)), '%')";
        if($collection_id>-1)
        {
            $query.=" AND collection_path||collection_ref::varchar||'/' LIKE '%/'||:collection_id||'/%' ";
            $flag_collection_id=TRUE;
        }
        
        if($taxa_ids >-1)
        {
            $query.= " AND  taxon_path||'/'||taxon_ref||'/' ~ :taxa_ids";
            $flag_taxa_ids=TRUE;
        }
        
        if($country_names!="-1")
        {
			$country_names=trim($country_names,",");
			$country_names=trim($country_names,";");
			
            $query.= " AND ".generate_filter_localities_by_country(preg_split("/(,|;|\|)/", $country_names),$arrayCountryNames);
            $flag_country_names=TRUE;
        }

        $query.=") AS a ORDER BY  sortval, value LIMIT 50;";

        $stmt=$conn->prepare($query);
        $stmt->bindValue(":pattern", $pattern);
        if($flag_collection_id===TRUE)
        {
            $stmt->bindValue(":collection_id", $collection_id);
        }
        if($flag_taxa_ids===TRUE)
        {
            $taxa_ids=create_regex_taxon_list($taxa_ids);
            $taxa_ids="/".$taxa_ids."/";
            $stmt->bindValue(":taxa_ids", $taxa_ids);
        }
        if($flag_country_names===TRUE)
        {
            //$stmt->bindValue(":country_names", $country_names);
            foreach($arrayCountryNames as $fieldName=>$value)
            {
                $stmt->bindValue($fieldName, $value);
            }
        }
        $stmt->execute();
        $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);

          header('Content-Type: application/json; charset=utf-8');

        print(json_encode($rs));
    }
     else
    {
           header('Content-Type: application/json; charset=utf-8');
         print('[{"value":"'.$pattern.'"}]');
    }
    $conn=null;

}

function json_darwin_get_types_by_collection($collection_id)
{
    $conn=connect_to_darwin();
    $rows=array();
    $query="SELECT name FROM 
        (SELECT  DISTINCT TRIM(regexp_split_to_table(type, '/')) as name from specimens where collection_path||collection_ref::varchar||'/' LIKE '%/'||:collection_id||'/%'
        ) 
        a WHERE LOWER(name) NOT LIKE '%specimen%'
        AND LOWER(name) NOT LIKE '%voucher%'  ORDER BY name;";
    $stmt=$conn->prepare($query);
    $stmt->bindValue(":collection_id", $collection_id);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
     header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
}

//pvignaux 2016/11/22
function json_darwin_get_collectors_collection($prefix,$collection_id)
{
    $conn=connect_to_darwin();
    $rows=array();
    $query="SELECT DISTINCT TRIM(formated_name) as name from people where id in (select people_ref from  catalogue_people inner join specimens on catalogue_people.record_id = specimens.id and  catalogue_people.referenced_relation = 'specimens' and (people_type = 'collector' OR people_type = 'donator') and collection_path||collection_ref::varchar||'/' LIKE '%/'||:collection_id||'/%') and formated_name_indexed LIKE CONCAT((SELECT * FROM fulltoindex(:prefix)), '%') ORDER BY TRIM(formated_name); ";
    $stmt=$conn->prepare($query);
    
    $stmt=$conn->prepare($query);
     $stmt->bindValue(":collection_id", $collection_id);
    $stmt->bindValue(":prefix", $prefix);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   $rs=array_merge(array(array("name"=>$pattern)), $rs);
   header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
}

function json_darwin_get_collectors_collection_taxa_country_locality($pattern, $collection_id, $taxa_ids, $country_names, $localities)
{
	
    $conn=connect_to_darwin();
    $rows=array();
    if(strlen($pattern)>=1)
    {
        $flag_collection_id=FALSE;
        $flag_taxa_ids=FALSE;
        $flag_country_names=FALSE;
        $flag_localities=FALSE;
        $arrayCountryNamesSubstitution=array();
        $arrayLocalitiesSubstitution=array();
        
        $query="SELECT DISTINCT TRIM(formated_name) as name FROM people INNER JOIN (SELECT people_ref, gtu_others_tag_indexed AS localities_indexed, taxon_path, taxon_ref, gtu_country_tag_indexed FROM  catalogue_people inner join  specimens  ON catalogue_people.record_id = specimens.id AND  catalogue_people.referenced_relation = 'specimens' AND (people_type = 'collector' OR people_type = 'donator') ";
          
        
        if($collection_id>-1)
        {
            $query.=" WHERE collection_path||collection_ref::varchar||'/' LIKE '%/'||:collection_id||'/%' ";
            $flag_collection_id=TRUE;
        }
         $query.= ") a ON people.id=a.people_ref ";
        if($taxa_ids >-1)
        {
            $query.= " AND  taxon_path||'/'||taxon_ref||'/' ~ :taxa_ids ";
            $taxa_ids=TRUE;
        }
        
        if($country_names!="-1")
        {
            $query.= " AND ".generate_filter_localities_by_country(explode(",", $country_names), $arrayCountryNamesSubstitution);
            $flag_country_names=TRUE;
        }
        
         if((string)$localities!="-1")
        {
                $array_locality=explode(';', $localities);
                
                $i=0;
                $tmpWhere="";
                foreach($array_locality as $tmp)
                {
                    if(strlen(trim($tmp))>0)
                    {
                        $nameVar=":tmploc".(string)$i;
                        if($i>0)
                        {
                            $tmpWhere.= " AND "; 
                        }
                        $tmpWhere.= "  REPLACE(localities_indexed::varchar,' ','') LIKE CONCAT('%',(SELECT * FROM fulltoindex( $nameVar,FALSE)),'%') ";
                        $arrayLocalitiesSubstitution[ $nameVar]=$tmp;
                        $i++;
                   }
                }
                $flag_localities=TRUE;
                $query.= " AND (  $tmpWhere ) ";
        }

       
        
        $query.="WHERE formated_name_indexed LIKE CONCAT('%',(SELECT * FROM fulltoindex(:pattern)),'%') ";

        $query.=" ORDER BY TRIM(formated_name) LIMIT 50;";

        $stmt=$conn->prepare($query);
         $stmt->bindValue(":pattern", $pattern);
        if($flag_collection_id===TRUE)
        {
            $stmt->bindValue(":collection_id", $collection_id);
        }
        if($flag_taxa_ids===TRUE)
        {
            $taxa_ids=create_regex_taxon_list($taxa_ids);
            $taxa_ids="/".$taxa_ids."/";
            $stmt->bindValue(":taxa_ids", $taxa_ids);
        }
         if($flag_country_names===TRUE)
        {
            foreach($arrayCountryNamesSubstitution as $fieldName=>$value)
            {
                 $stmt->bindValue($fieldName, $value);
            }
        }
        if($flag_localities===TRUE)
        {
            foreach($arrayLocalitiesSubstitution as $fieldName=>$value)
            {
                 $stmt->bindValue($fieldName, $value);
            }
        }
        $stmt->execute();
		
        $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
		   $rs=array_merge(array(array("name"=>$pattern)), $rs);
        header('Content-Type: application/json; charset=utf-8');
		
        print(json_encode($rs));
    }
     else
    {
          header('Content-Type: application/json; charset=utf-8');
         print('[{"value":"'.$pattern.'"}]');
		 //print("debug");
    }
    $conn=null;
}


//function json_darwin_search_specimens($collections=-1, $taxas=-1, $number=-1, $countries=-1, $localities=-1, $collectors=-1, $gathering_date_begin=-1, $gathering_date_end=-1, $types=-1, $bool_images=-1, $bool_3d=-1, $north, $south, $west, $east, $page_size, $page, $sort)
function json_darwin_search_specimens($p_debug=false)
{
    
	try
	{
		$collections = -1; 
		$taxas = -1; 
		$number = -1;
		$ig_num=-1;
		$countries = -1; 
		$localities = -1; 
		$collectors = -1; 
		$gathering_date_begin = -1; 
		$gathering_date_end = -1; 
		$types = -1;
		$bool_images = "false"; 
		$bool_3d = "false";
		$bool_georefonly = "false";
		$bool_citizen_sciences = "false";
		$north = 90;
		$south = -90; 
		$west = -180;
		$east = 180;
		$page_size = 25; 
		$page = 1;
		$sort = -1;
		$wkt="";
		$sort_order="";
		$sort_direction="ASC";
		
		
		if(isset($_REQUEST["collections"]))
		{
			$collections = $_REQUEST["collections"];
		}
		if(isset($_REQUEST["taxas"]))
		{
			$taxas = $_REQUEST["taxas"];
		}
		if(isset($_REQUEST["number"]))
		{
			$number = $_REQUEST["number"];
		}
		if(isset($_REQUEST["ig_num"]))
		{
			$ig_num = $_REQUEST["ig_num"];
		}
		if(isset($_REQUEST["countries"]))
		{
			$countries = $_REQUEST["countries"];
			$countries=str_replace("&"," ", $countries);
		}
		if(isset($_REQUEST["localities"])) 
		{
			$localities = $_REQUEST["localities"];
			$localities=str_replace("&"," ", $localities);
		}
		if(isset($_REQUEST["collectors"]))
		{
			$collectors = $_REQUEST["collectors"];
			$collectors=str_replace("&"," ", $collectors);
		}
		if(isset($_REQUEST["gathering_begin"]))          
			$gathering_date_begin = $_REQUEST["gathering_begin"];
		if(isset($_REQUEST["gathering_end"]))          
			$gathering_date_end = $_REQUEST["gathering_end"];
		if(isset($_REQUEST["types"]))          
			$types = $_REQUEST["types"];
		if(isset($_REQUEST["has_images"]))  
			$bool_images =$_REQUEST["has_images"];
		if(isset($_REQUEST["has_3d"]))          
			$bool_3d = $_REQUEST["has_3d"];
		if(isset($_REQUEST["north"]))  
			$north = $_REQUEST["north"];
		if(isset($_REQUEST["south"]))  
			$south = $_REQUEST["south"];
		if(isset($_REQUEST["west"]))          
			$west = $_REQUEST["west"];
		if(isset($_REQUEST["east"]))  
			$east = $_REQUEST["east"];
		if(isset($_REQUEST["size"]))  
			$page_size = $_REQUEST["size"];
		if(isset($_REQUEST["page"]))          
			$page = $_REQUEST["page"];
		if(isset($_REQUEST["sort"]))  
			$sort = $_REQUEST["sort"];
		if(isset($_REQUEST["wkt"]))  
			$wkt = $_REQUEST["wkt"];
	    if(isset($_REQUEST["georef_only"]))          
			$bool_georefonly = $_REQUEST["georef_only"];
		 if(isset($_REQUEST["citizen_sciences"]))          
			$bool_citizen_sciences = $_REQUEST["citizen_sciences"];
		if(isset($_REQUEST["sort_order"]))  
			$sort_order = $_REQUEST["sort_order"];
		if(isset($_REQUEST["sort_direction"]))
		{
			if(strtoupper($_REQUEST["sort_direction"])=="DESCENDING")
			{
				$sort_direction ="DESC";
			}
		}
		$arraySpNum=array();
		$arrayCountries=array();
		$arrayLocalities=array();
		$arrayCollectors=array();
		$countries=pg_escape_string($countries);
		$localities=pg_escape_string($localities);
		$collectors=pg_escape_string($collectors);
		
		$flag_collections=FALSE; 
		$flag_taxas=FALSE; 
		$flag_number=FALSE; 
		$flag_countries=FALSE; 
		$flag_localities=FALSE; 
		$flag_collectors=FALSE; 
		$flag_gathering_date_begin=FALSE; 
		$flag_gathering_date_end=FALSE; 
		$flag_types=FALSE; 
		$flag_north=FALSE; 
		$flag_south=FALSE; 
		$flag_west=FALSE; 
		$flag_east=FALSE; 
		//$flag_page_size=FALSE; 
		//$flag_sort=FALSE;
		//$flag_offset=FALSE;
		
		if(is_numeric($page_size)&&is_numeric($page))
		{
				$conn=connect_to_darwin();
				$rows=array();
				
				$query="			 
				SELECT 
				
				 id, uuid, code_prefix, code, code_num, COALESCE(NULLIF(code_display,''), uuid::varchar) code_display, full_code_indexed, taxon_path, taxon_ref, collection_ref, gtu_country_tag_indexed, gtu_country_tag_value, NULL::varchar[] localities_indexed, NULL::varchar gtu_others_tag_value, family, taxon_name, sex, collector_ids, donator_ids, gtu_from_date, gtu_from_date_mask, gtu_to_date, gtu_to_date_mask, coll_type, collection_path, specimen_count_min, latitude, longitude, formated_date, code_concat,
	
				count(*) OVER() AS full_count
				
				FROM 
				
				mv_search_public_specimen         
				WHERE is_public_collection=TRUE AND (COALESCE(sensitive_info_withheld_taxonomy,FALSE)=FALSE)
			";
			
				if((string)$collections!='-1'&&(string)$collections!='')
				{
					$query.=" AND collection_path||'/'||collection_ref::varchar||'/' LIKE '%/'||:collections||'/%' ";
					  $flag_collections=TRUE; 
				}
				
				if((string)$taxas !='-1'&&(string)$taxas !='')
				{

					$query.= " AND  (((taxon_path||'/'||taxon_ref::varchar||'/') ~ :taxas) OR (taxon_ref in (select fct_rmca_taxo_get_syno_children_public_batch(:taxas)))) ";
					$flag_taxas=TRUE; 
				}
				
				/* if((string)$number!="-1")
				{
					$query.=" AND full_code_indexed=(SELECT * FROM fulltoindex(:number))";
					$flag_number=TRUE;
				}*/
				
				
				$varSpNumIdx=0;
				if((string)$number!="-1"&&(string)$number !='')
				{
				
				
					$tmpWhere="";
					$array_group_sp_num=explode('|', $number);
					
						
						$i=0;
						foreach($array_group_sp_num as $tmp)
						{
							
										if($i>0)
										{
											$tmpWhere.= " OR "; 
										}
										$tmpWhere.= " ( "; 
										$nameVar=":tmpspnum".(string)$varSpNumIdx;
										$tmpWhere.=" full_code_indexed=(SELECT * FROM fulltoindex($nameVar))";
										$arraySpNum[$nameVar]=$tmp;       
										 $tmpWhere.= " ) "; 
										$i++;
										$varSpNumIdx++;
							   
						}
						$flag_number=TRUE;
					   $query.= " AND ($tmpWhere) ";
							

				}
				
				
				$varIgNumIdx=0;
				if((string)$ig_num!="-1"&&(string)$ig_num !='')
				{
				
				
					$tmpWhere="";
					$array_group_ig_num=explode('|', $ig_num);
					
						
						$i=0;
						foreach($array_group_ig_num as $tmp)
						{
							
										if($i>0)
										{
											$tmpWhere.= " OR "; 
										}
										$tmpWhere.= " ( "; 
										$nameVar=":tmpignum".(string)$varIgNumIdx;
										$tmpWhere.=" ig_num_indexed=(SELECT * FROM fulltoindex($nameVar))";
										$arraySpNum[$nameVar]=$tmp;       
										 $tmpWhere.= " ) "; 
										$i++;
										$varIgNumIdx++;
							   
						}
						$flag_number=TRUE;
					   $query.= " AND ($tmpWhere) ";
							

				}
				
				$varCountryIdx=0;
				if((string)$countries!="-1"&&(string)$countries!="")
				{
				
				
					 $tmpWhere="";
					$array_group_country=explode('|', $countries);
					{
						$j=0;
					   
						foreach($array_group_country as $group_tmp)
						{
							$array_country=explode(';', $group_tmp);
							if(strlen(trim($group_tmp))>0)
							{
								$i=0;
								 if($j>0)
								{
									$tmpWhere.= " OR "; 
							   }
								foreach($array_country as $tmp)
								{
								  
									if(strlen(trim($tmp))>0)
									{
										if($i>0)
										{
											$tmpWhere.= " AND "; 
										}
										$tmpWhere.= " EXISTS ( "; 
										$nameVar=":tmpcountry".(string)$varCountryIdx;
										
										$tmpWhere.= "SELECT * from unnest(gtu_country_tag_indexed) as x where x  LIKE  (SELECT * FROM concat(fulltoindex($nameVar)))";
										
										$arrayCountries[$nameVar]=$tmp;       
										 $tmpWhere.= " ) "; 
										$i++;
										 $varCountryIdx++;
								   }
								}
								
								$j++;
							}
						}
						$flag_countries=TRUE;
					   $query.= " AND (  $tmpWhere ) ";
					}         

				}
				
				
				$varLocIdx=0;
				if((string)$localities!="-1"&&(string)$localities!="")
				{
					 $tmpWhere="";
					$array_group_locality=explode('|', $localities);
					{
						$j=0;
					   
						foreach($array_group_locality as $group_tmp)
						{
							$array_locality=explode(';', $group_tmp);
							if(strlen(trim($group_tmp))>0)
							{
								$i=0;
								 if($j>0)
								{
									$tmpWhere.= " OR "; 
							   }
								foreach($array_locality as $tmp)
								{
								  
									if(strlen(trim($tmp))>0)
									{
										if($i>0)
										{
											$tmpWhere.= " AND "; 
										}
										$tmpWhere.= " ( "; 
										$nameVar=":tmploc".(string)$varLocIdx;
										$tmpWhere.= "  REPLACE(gtu_others_tag_indexed::varchar,' ','') LIKE CONCAT('%',(SELECT * FROM fulltoindex($nameVar,FALSE)),'%') ";
										$arrayLocalities[$nameVar]=$tmp;
										 $tmpWhere.= " ) "; 
										$i++;
										 $varLocIdx++;
								   }
								}
								
								$j++;
							}
						}
						$flag_localities=TRUE;
						$query.= " AND (  $tmpWhere ) ";
					}
				}
				
			   
				if((string)$collectors!="-1"&&(string)$collectors!="")
				{
				
						$array_collectors=preg_split( "/(;|\|)/", $collectors );
					
						$i=0;
						$tmpWhere="";
						foreach($array_collectors as $tmp)
						{
						
							if(strlen(trim($tmp))>0)
							{
								if($i>0)
								{
									$tmpWhere.= " OR "; 
								}
								$nameVar=":tmpcollectors".(string)$i;
								$tmpWhere.= "  (SELECT * FROM fulltoindex(formated_name_indexed)) LIKE CONCAT((SELECT * FROM fulltoindex($nameVar,FALSE)), '%') "; 
								$arrayCollectors[$nameVar]=$tmp;
								$i++;
							}
						}
						$flag_collectors=TRUE;
						$query.= " AND collector_ids||donator_ids && (SELECT array_agg(id) FROM  people WHERE  $tmpWhere ) ";
					
					
					
				}
				
				if((string)$gathering_date_begin!="-1"&&(string)$gathering_date_begin!="")
				{
					$query.= " AND (   gtu_to_date>=:gathering_date_begin and gtu_to_date_mask >0 ) ";
					$flag_gathering_date_begin=TRUE;
				}
				
				if((string)$gathering_date_end!="-1"&&(string)$gathering_date_end!="")
				{
					$query.= " AND ((gtu_from_date <= :gathering_date_end AND gtu_from_date_mask > 0) OR (gtu_to_date <= :gathering_date_end AND gtu_to_date_mask > 0)) ";
					$flag_gathering_date_end=TRUE;
				}

				if((string)$types!="-1"&&(string)$types!="")
				{
					/*$query.= " AND (string_to_array(regexp_replace(coll_type ,' ', ''), '/') && string_to_array(regexp_replace(:types,' ',''), ','))";*/
					//$flag_types=TRUE;
					$tmp_types=explode(",",$types);
					$arr_type=Array();
					if(!in_array("types",$tmp_types)||!in_array("non-type",$tmp_types))
					{
						foreach( $tmp_types as $p_t)
						{
							if($p_t=="types")
							{
								$arr_type[]="( LOWER(TRIM(type)) != 'specimen' AND TRIM(COALESCE(type,''))!='' )";
							}
							elseif($p_t=="non-type")
							{
								$arr_type[]="( LOWER(TRIM(type)) = 'specimen' OR TRIM(COALESCE(type,''))='' )";
							}
						}
						$query.=" AND (".implode(" OR ", $arr_type).") ";
					}
					
				}
				
			   if(strtoupper($bool_images)=="TRUE"||strtoupper($bool_3d)=="TRUE")
			   {
					$query.= " AND (";
					if(strtoupper($bool_images)=="TRUE")
					{
						$query.= " EXISTS (SELECT ext_links.id FROM ext_links WHERE ext_links.record_id=mv_search_public_specimen.id AND ext_links.referenced_relation='specimens' and (type='iiif' )) ";
					}
					
					if(strtoupper($bool_3d)=="TRUE")
					{
						if(strtoupper($bool_images)=="TRUE")
						{
							$query.= " OR ";
						}
						$query.= " EXISTS (SELECT ext_links.id FROM ext_links WHERE ext_links.record_id=mv_search_public_specimen.id AND ext_links.referenced_relation='specimens' and (type='html_3d_snippet' OR type='html_3d_link' ))";
					}
					$query.= ")";
			   }
			   
			   if(strtoupper($bool_georefonly)=="TRUE")
			   {
					$query.= " AND geom IS NOT NULL ";
			   }
			   if(strtoupper($bool_citizen_sciences)=="TRUE")
			   {
					$query.= " AND EXISTS (SELECT p.id FROM properties p WHERE  p.record_id=mv_search_public_specimen.id AND p.referenced_relation='specimens' AND LOWER( p.property_type)='contributor' AND LOWER(p.lower_value)='citizen science - doedat community project cresco') ";
			   }
			   
			if((int)$north!=90 && (int)$south!=-90 && (int)$west!=-180 && (string)$east!=180)
			{
				 $query.= " AND ((latitude BETWEEN :south AND :north) AND ( longitude BETWEEN :west AND :east) ) ";
				 $flag_north=TRUE;
				 $flag_south=TRUE;
				 $flag_west=TRUE;
				 $flag_east=TRUE;
			}
			
			if(trim($wkt)!=="")
			{
				$query.= " AND ST_INTERSECTS(geom, ST_GEOMFROMTEXT('".$wkt."',4326))";
			}
			
			if((int)$page_size==-1)
			{
				$page_size=25;
			}
			if((int)$page==-1)
			{
				$offset=0;
			}
			else
			{
				$offset=(((int)$page)-1)*(int)$page_size;
			}
			
			/*if((string)$sort=="-1")
			{
				$sort="id";
			}*/
			$sort="id";
			if(strlen(trim($sort_order))>0)
			{
				if(trim($sort_order)=="specimen_number")
				{
					$sort="(COALESCE(code_prefix,''), COALESCE(code,''), code_num)";
				}
				elseif(trim($sort_order)=="country")
				{
					$sort="NULLIF(gtu_country_tag_value,'')";
				}
				elseif(trim($sort_order)=="taxon_name")
				{
					$sort="taxon_name";
				}
				elseif(trim($sort_order)=="family")
				{
					$sort="family ";
				}
				elseif(trim($sort_order)=="collecting_date")
				{
					$sort="NULLIF(NULLIF(darwin2.fct_mask_date(gtu_from_date, gtu_from_date_mask), 'xxxx-xx-xx'::text),'')";
				}
				elseif(trim($sort_order)=="latitude")
				{
					$sort="latitude";
				}
				elseif(trim($sort_order)=="longitude")
				{
					$sort="longitude ";
				}
			}
			
			//if(testIsAuthorizedColumn($sort)&&is_numeric($page_size)&&is_numeric($offset))
			$null_str="NULLS LAST";
			if($sort_direction=="DESC")
			{
				$null_str="NULLS FIRST";
			}
			if(is_numeric($page_size)&&is_numeric($offset))
			{
				
				$query=$query." ORDER BY $sort $sort_direction $null_str";
				$query="with a as (".$query."), b as (select count(*)  as georef_count from a where latitude is not null and  longitude is not null) select *, georef_count  from a, b";
				$query=$query." LIMIT $page_size OFFSET $offset;";
				 
			   
			}
			else
			{
				drupal_add_http_header('Content-Type', 'application/javascript; utf-8');
				return;
			}
			try 
			{
				if($p_debug)
				{
					print($query);
				}
				$stmt=$conn->prepare($query);
			
				
				if($flag_collections===TRUE)
				{
					$stmt->bindValue(":collections", $collections);
				}
				if($flag_taxas===TRUE)
				{
					$taxas=create_regex_taxon_list($taxas);

					$taxas="/".$taxas."/";
					
					$stmt->bindValue(":taxas", $taxas);
				}        
				if($flag_number===TRUE)
				{
					/*$stmt->bindValue(":number", $number);*/
					 foreach($arraySpNum as $placeHolder=>$value)
					{
					
						$stmt->bindValue($placeHolder, $value);
					}
				}        
				if( $flag_countries===TRUE)
				{
					
					foreach($arrayCountries as $placeHolder=>$value)
					{
						$stmt->bindValue($placeHolder, $value);
					}
				}        
				if($flag_localities===TRUE)
				{
					foreach($arrayLocalities as $placeHolder=>$value)
					{
						$stmt->bindValue($placeHolder, $value);
					}
				}
				if($flag_collectors===TRUE)
				{
					foreach($arrayCollectors as $placeHolder=>$value)
					{
						$stmt->bindValue($placeHolder, $value);
					}
				}        
				if($flag_gathering_date_begin===TRUE)
				{
					$stmt->bindValue(":gathering_date_begin", $gathering_date_begin);
				}        
				if($flag_gathering_date_end===TRUE)
				{
					$stmt->bindValue(":gathering_date_end", $gathering_date_end);
				}        
				/*if($flag_types===TRUE)
				{
					$stmt->bindValue(":types", $types);
				}*/        
				if($flag_north===TRUE)
				{
					$stmt->bindValue(":north", $north);
				}        
				if($flag_south===TRUE)
				{
					$stmt->bindValue(":south", $south);
				}        
				if($flag_west===TRUE)
				{
					$stmt->bindValue(":west", $west);
				}        
				if($flag_east===TRUE)
				{
					$stmt->bindValue(":east", $east);
				}   

				$stmt->execute();
				$rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
			   header('Content-Type: application/json; charset=utf-8');

				print(json_encode($rs));
				$conn=null;
				
			} 
			catch (PDOException $e) 
			{
				print('ERROR: ' . $e->getMessage());
			}
			return;
		}
		else
		{
			header('Content-Type: application/json; charset=utf-8');
	  
			print(json_encode(Array()));
			return;
		}
			header('Content-Type: application/json; charset=utf-8');
	  
			print(json_encode(Array()));
			return;
	} 
	
	catch (Exception $e) 
	{
	    
		//print(get_class($e));
		print('Exception  : '.  $e->getTraceAsString(). "\n");
	}
}

//count geo_Ref only

function json_darwin_count_geo_ref()
{
    
	try
	{
		$collections = -1; 
		$taxas = -1; 
		$number = -1;
		$countries = -1; 
		$localities = -1; 
		$collectors = -1; 
		$gathering_date_begin = -1; 
		$gathering_date_end = -1; 
		$types = -1;
		$bool_images = "false"; 
		$bool_3d = "false";
		$north = 90;
		$south = -90; 
		$west = -180;
		$east = 180;
		$page_size = 25; 
		$page = 1;
		$sort = -1;
		$wkt="";
		$sort_order="";
		
		if(isset($_REQUEST["collections"]))    
			$collections = $_REQUEST["collections"];
		if(isset($_REQUEST["taxas"]))          
			$taxas = $_REQUEST["taxas"];
		if(isset($_REQUEST["number"]))          
			$number = $_REQUEST["number"];
		if(isset($_REQUEST["countries"]))  
			$countries = $_REQUEST["countries"];
		if(isset($_REQUEST["localities"]))          
			$localities = $_REQUEST["localities"];
		if(isset($_REQUEST["collectors"]))          
			$collectors = $_REQUEST["collectors"];
		if(isset($_REQUEST["gathering_begin"]))          
			$gathering_date_begin = $_REQUEST["gathering_begin"];
		if(isset($_REQUEST["gathering_end"]))          
			$gathering_date_end = $_REQUEST["gathering_end"];
		if(isset($_REQUEST["types"]))          
			$types = $_REQUEST["types"];
		if(isset($_REQUEST["has_images"]))  
			$bool_images =$_REQUEST["has_images"];
		if(isset($_REQUEST["has_3d"]))          
			$bool_3d = $_REQUEST["has_3d"];
		if(isset($_REQUEST["north"]))  
			$north = $_REQUEST["north"];
		if(isset($_REQUEST["south"]))  
			$south = $_REQUEST["south"];
		if(isset($_REQUEST["west"]))          
			$west = $_REQUEST["west"];
		if(isset($_REQUEST["east"]))  
			$east = $_REQUEST["east"];
		if(isset($_REQUEST["size"]))  
			$page_size = $_REQUEST["size"];
		if(isset($_REQUEST["page"]))          
			$page = $_REQUEST["page"];
		if(isset($_REQUEST["sort"]))  
			$sort = $_REQUEST["sort"];
		if(isset($_REQUEST["wkt"]))  
			$wkt = $_REQUEST["wkt"];
		if(isset($_REQUEST["sort_order"]))  
			$sort_order = $_REQUEST["sort_order"];
		
		$arraySpNum=array();
		$arrayCountries=array();
		$arrayLocalities=array();
		$arrayCollectors=array();
		$countries=pg_escape_string($countries);
		$localities=pg_escape_string($localities);
		$collectors=pg_escape_string($collectors);
		
		$flag_collections=FALSE; 
		$flag_taxas=FALSE; 
		$flag_number=FALSE; 
		$flag_countries=FALSE; 
		$flag_localities=FALSE; 
		$flag_collectors=FALSE; 
		$flag_gathering_date_begin=FALSE; 
		$flag_gathering_date_end=FALSE; 
		$flag_types=FALSE; 
		$flag_north=FALSE; 
		$flag_south=FALSE; 
		$flag_west=FALSE; 
		$flag_east=FALSE; 
		//$flag_page_size=FALSE; 
		//$flag_sort=FALSE;
		//$flag_offset=FALSE;
		
		if(is_numeric($page_size)&&is_numeric($page))
		{
				$conn=connect_to_darwin();
				$rows=array();
				
				$query="			 
				 SELECT  COUNT(distinct specimens.id) as count_geo
				FROM specimens
				
				LEFT JOIN 
				codes
				ON codes.referenced_relation='specimens' and code_category='main' and specimens.id=codes.record_id
				INNER JOIN collections ON specimens.collection_ref=collections.id AND is_public=true
				INNER JOIN taxonomy ON specimens.taxon_ref=taxonomy.id AND COALESCE(sensitive_info_withheld, FALSE)=FALSE           
				WHERE gtu_location is null IS NOT NULL
			";
			
				if((string)$collections!='-1'&&(string)$collections!='')
				{
					$query.=" AND collection_path||'/'||specimens.collection_ref::varchar||'/' LIKE '%/'||:collections||'/%' ";
					  $flag_collections=TRUE; 
				}
				
				if((string)$taxas !='-1'&&(string)$taxas !='')
				{

					$query.= " AND  (taxon_path||'/'||taxon_ref::varchar||'/') ~ :taxas ";
					$flag_taxas=TRUE; 
				}
				
				/* if((string)$number!="-1")
				{
					$query.=" AND full_code_indexed=(SELECT * FROM fulltoindex(:number))";
					$flag_number=TRUE;
				}*/
				
				
				$varSpNumIdx=0;
				if((string)$number!="-1"&&(string)$number !='')
				{
				
				
					$tmpWhere="";
					$array_group_sp_num=explode('|', $number);
					
						
						$i=0;
						foreach($array_group_sp_num as $tmp)
						{
							
										if($i>0)
										{
											$tmpWhere.= " OR "; 
										}
										$tmpWhere.= " ( "; 
										$nameVar=":tmpspnum".(string)$varSpNumIdx;
										$tmpWhere.=" full_code_indexed=(SELECT * FROM fulltoindex($nameVar))";
										$arraySpNum[$nameVar]=$tmp;       
										 $tmpWhere.= " ) "; 
										$i++;
										$varSpNumIdx++;
							   
						}
						$flag_number=TRUE;
					   $query.= " AND ($tmpWhere) ";
							

				}
				
				
				$varCountryIdx=0;
				if((string)$countries!="-1"&&(string)$countries!="")
				{
				
				
					 $tmpWhere="";
					$array_group_country=explode('|', $countries);
					{
						$j=0;
					   
						foreach($array_group_country as $group_tmp)
						{
							$array_country=explode(';', $group_tmp);
							if(strlen(trim($group_tmp))>0)
							{
								$i=0;
								 if($j>0)
								{
									$tmpWhere.= " OR "; 
							   }
								foreach($array_country as $tmp)
								{
								  
									if(strlen(trim($tmp))>0)
									{
										if($i>0)
										{
											$tmpWhere.= " AND "; 
										}
										$tmpWhere.= " EXISTS ( "; 
										$nameVar=":tmpcountry".(string)$varCountryIdx;
										
										$tmpWhere.= "SELECT * from unnest(gtu_country_tag_indexed) as x where x  LIKE  (SELECT * FROM concat(fulltoindex($nameVar)))";
										
										$arrayCountries[$nameVar]=$tmp;       
										 $tmpWhere.= " ) "; 
										$i++;
										 $varCountryIdx++;
								   }
								}
								
								$j++;
							}
						}
						$flag_countries=TRUE;
					   $query.= " AND (  $tmpWhere ) ";
					}         

				}
				
				
				$varLocIdx=0;
				if((string)$localities!="-1"&&(string)$localities!="")
				{
					 $tmpWhere="";
					$array_group_locality=explode('|', $localities);
					{
						$j=0;
					   
						foreach($array_group_locality as $group_tmp)
						{
							$array_locality=explode(';', $group_tmp);
							if(strlen(trim($group_tmp))>0)
							{
								$i=0;
								 if($j>0)
								{
									$tmpWhere.= " OR "; 
							   }
								foreach($array_locality as $tmp)
								{
								  
									if(strlen(trim($tmp))>0)
									{
										if($i>0)
										{
											$tmpWhere.= " AND "; 
										}
										$tmpWhere.= " ( "; 
										$nameVar=":tmploc".(string)$varLocIdx;
										$tmpWhere.= "  REPLACE(gtu_others_tag_indexed::varchar,' ','') LIKE CONCAT('%',(SELECT * FROM fulltoindex($nameVar,FALSE)),'%') ";
										$arrayLocalities[$nameVar]=$tmp;
										 $tmpWhere.= " ) "; 
										$i++;
										 $varLocIdx++;
								   }
								}
								
								$j++;
							}
						}
						$flag_localities=TRUE;
						$query.= " AND (  $tmpWhere ) ";
					}
				}
				
			   
				if((string)$collectors!="-1"&&(string)$collectors!="")
				{
				
						$array_collectors=preg_split( "/(;|\|)/", $collectors );
					
						$i=0;
						$tmpWhere="";
						foreach($array_collectors as $tmp)
						{
						
							if(strlen(trim($tmp))>0)
							{
								if($i>0)
								{
									$tmpWhere.= " OR "; 
								}
								$nameVar=":tmpcollectors".(string)$i;
								$tmpWhere.= "  (SELECT * FROM fulltoindex(formated_name_indexed)) LIKE CONCAT((SELECT * FROM fulltoindex($nameVar,FALSE)), '%') "; 
								$arrayCollectors[$nameVar]=$tmp;
								$i++;
							}
						}
						$flag_collectors=TRUE;
						$query.= " AND spec_coll_ids||spec_don_sel_ids && (SELECT array_agg(id) FROM  people WHERE  $tmpWhere ) ";
					
					
					
				}
				
				if((string)$gathering_date_begin!="-1"&&(string)$gathering_date_begin!="")
				{
					$query.= " AND (   gtu_to_date>=:gathering_date_begin and gtu_to_date_mask >0 ) ";
					$flag_gathering_date_begin=TRUE;
				}
				
				if((string)$gathering_date_end!="-1"&&(string)$gathering_date_end!="")
				{
					$query.= " AND ((gtu_from_date <= :gathering_date_end AND gtu_from_date_mask > 0) OR (gtu_to_date <= :gathering_date_end AND gtu_to_date_mask > 0)) ";
					$flag_gathering_date_end=TRUE;
				}

				if((string)$types!="-1"&&(string)$types!="")
				{
					/*$query.= " AND (string_to_array(regexp_replace(coll_type ,' ', ''), '/') && string_to_array(regexp_replace(:types,' ',''), ','))";*/
					//$flag_types=TRUE;
					$tmp_types=explode(",",$types);
					$arr_type=Array();
					if(!in_array("types",$tmp_types)||!in_array("non-type",$tmp_types))
					{
						foreach( $tmp_types as $p_t)
						{
							if($p_t=="types")
							{
								$arr_type[]="( LOWER(TRIM(specimens.type)) != 'specimen' AND TRIM(COALESCE(specimens.type,''))!='' )";
							}
							elseif($p_t=="non-type")
							{
								$arr_type[]="( LOWER(TRIM(specimens.type)) = 'specimen' OR TRIM(COALESCE(specimens.type,''))='' )";
							}
						}
						$query.=" AND (".implode(" OR ", $arr_type).") ";
					}
					
				}
				
			   if(strtoupper($bool_images)=="TRUE"||strtoupper($bool_3d)=="TRUE")
			   {
					$query.= " AND (";
					if(strtoupper($bool_images)=="TRUE")
					{
						$query.= " EXISTS (SELECT ext_links.id FROM ext_links WHERE ext_links.record_id=specimens.id AND ext_links.referenced_relation='specimens' and category='image_link' ) ";
					}
					
					if(strtoupper($bool_3d)=="TRUE")
					{
						if(strtoupper($bool_images)=="TRUE")
						{
							$query.= " OR ";
						}
						$query.= " EXISTS (SELECT ext_links.id FROM ext_links WHERE ext_links.record_id=specimens.id AND ext_links.referenced_relation='specimens' and category='html_3d_snippet' )";
					}
					$query.= ")";
			   }
			   
			if((int)$north!=90 && (int)$south!=-90 && (int)$west!=-180 && (string)$east!=180)
			{
				 $query.= " AND ((gtu_location[1] BETWEEN :south AND :north) AND ( gtu_location[0] BETWEEN :west AND :east) ) ";
				 $flag_north=TRUE;
				 $flag_south=TRUE;
				 $flag_west=TRUE;
				 $flag_east=TRUE;
			}
			
			if(trim($wkt)!=="")
			{
				$query.= " AND ST_INTERSECTS(geom, ST_GEOMFROMTEXT('".$wkt."',4326))";
			}
			
			if((int)$page_size==-1)
			{
				$page_size=25;
			}
			if((int)$page==-1)
			{
				$offset=0;
			}
			else
			{
				$offset=(((int)$page)-1)*(int)$page_size;
			}
			
		
			
			try 
			{

				$stmt=$conn->prepare($query);
				
				
				if($flag_collections===TRUE)
				{
					$stmt->bindValue(":collections", $collections);
				}
				if($flag_taxas===TRUE)
				{
					$taxas=create_regex_taxon_list($taxas);

					$taxas="/".$taxas."/";
					
					$stmt->bindValue(":taxas", $taxas);
				}        
				if($flag_number===TRUE)
				{
					/*$stmt->bindValue(":number", $number);*/
					 foreach($arraySpNum as $placeHolder=>$value)
					{
						$stmt->bindValue($placeHolder, $value);
					}
				}        
				if( $flag_countries===TRUE)
				{
					
					foreach($arrayCountries as $placeHolder=>$value)
					{
						$stmt->bindValue($placeHolder, $value);
					}
				}        
				if($flag_localities===TRUE)
				{
					foreach($arrayLocalities as $placeHolder=>$value)
					{
						$stmt->bindValue($placeHolder, $value);
					}
				}
				if($flag_collectors===TRUE)
				{
					foreach($arrayCollectors as $placeHolder=>$value)
					{
						$stmt->bindValue($placeHolder, $value);
					}
				}        
				if($flag_gathering_date_begin===TRUE)
				{
					$stmt->bindValue(":gathering_date_begin", $gathering_date_begin);
				}        
				if($flag_gathering_date_end===TRUE)
				{
					$stmt->bindValue(":gathering_date_end", $gathering_date_end);
				}        
				/*if($flag_types===TRUE)
				{
					$stmt->bindValue(":types", $types);
				}*/        
				if($flag_north===TRUE)
				{
					$stmt->bindValue(":north", $north);
				}        
				if($flag_south===TRUE)
				{
					$stmt->bindValue(":south", $south);
				}        
				if($flag_west===TRUE)
				{
					$stmt->bindValue(":west", $west);
				}        
				if($flag_east===TRUE)
				{
					$stmt->bindValue(":east", $east);
				}   

				$stmt->execute();
				$rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
			   header('Content-Type: application/json; charset=utf-8');

				print(json_encode($rs));
				$conn=null;
				
			} 
			catch (PDOException $e) 
			{
				print('ERROR: ' . $e->getMessage());
			}
			return;
		}
		else
		{
			header('Content-Type: application/json; charset=utf-8');
	  
			print(json_encode(Array()));
			return;
		}
			header('Content-Type: application/json; charset=utf-8');
	  
			print(json_encode(Array()));
			return;
	} 
	
	catch (Exception $e) 
	{
	    
		//print(get_class($e));
		print('Exception  : '.  $e->getTraceAsString(). "\n");
	}
}

function stuff_for_properties($query)
{
	$query="with a as
( ".$query.")
,
b as (
select unnest(ids) as unnest_id from a)
,
c as ( select lower_value, upper_value,  property_unit, property_type, applies_to from properties 
				 inner join b on record_id=unnest_id and referenced_relation='specimens'
				UNION
				  select comment, NULL, NULL,  notion_concerned, NULL from comments 
				 inner join b on record_id=unnest_id and referenced_relation='specimens'
				),
d as
(
select 

trim(lower_value||(coalesce('-'||nullif(upper_value,''),''))||coalesce(' '||nullif(property_unit,''),'')) as prop_value
,* from c),
e as
(select  json_object_agg (TRIM(property_type||COALESCE(' - '||NULLIF(applies_to,''),'')), TRIM(prop_value)) properties from d)
select * from a, e;";
return $query;
}

function json_darwin_get_specimen( $uuid)
{

      if((string)$uuid!="-1")
      {
            $conn=connect_to_darwin();
            $rows=array();
            
            $query="
	SELECT v_specimen_public_display.* , count(*) OVER() AS full_count FROM darwin2.v_specimen_public_display WHERE uuid=:uuid
        ";          
            
 
         
         $query=$query."  LIMIT 20";

		$query=stuff_for_properties($query);
		
        $stmt=$conn->prepare($query);
        $stmt->bindValue(":uuid", $uuid);
        $stmt->execute();
        $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);

       header('Content-Type: application/json; charset=utf-8');
        if($rs[0]["full_count"]>0)
        {
     
            print(json_encode($rs));
           
        }
        $conn=null;
    }
    else
    {
                header('Content-Type: application/json; charset=utf-8');
    }
 
}


function json_darwin_get_specimen_id( $id)
{

      if((string)$id!="-1")
      {
            $conn=connect_to_darwin();
            $rows=array();
            
            $query="SELECT uuid
, ids, ig_num, string_agg(code_display,',') as code_display, taxon_paths, taxon_ref, taxon_name, sex, history_identification
gtu_country_tag_value, gtu_others_tag_value, gtu_from_date, gtu_from_date_mask, gtu_to_date, gtu_to_date_mask,
fct_mask_date, date_from_display, date_to_display, coll_type, urls_thumbnails, image_category_thumbnails, 
contributor_thumbnails, disclaimer_thumbnails, license_thumbnails, display_order_thumbnails, urls_image_links,
image_category_image_links, contributor_image_links, disclaimer_image_links, license_image_links, display_order_image_links, 
urls_3d_snippets, image_category_3d_snippets, contributor_3d_snippets, disclaimer_3d_snippets, license_3d_snippets, 
display_order_3d_snippets, longitude, latitude, collector_ids, collectors, donator_ids, donators, localities, family, t_order,
class, specimen_count_min, specimen_count_males_min, specimen_count_females_min, collection_code_full_path, collection_name_full_path , count(*) OVER() AS full_count FROM darwin2.v_specimen_public_display WHERE :id=any(ids) GROUP BY uuid, ids, ig_num, taxon_paths, taxon_ref, taxon_name, sex, history_identification,
gtu_country_tag_value, gtu_others_tag_value, gtu_from_date, gtu_from_date_mask, gtu_to_date, gtu_to_date_mask,
fct_mask_date, date_from_display, date_to_display, coll_type, urls_thumbnails, image_category_thumbnails, 
contributor_thumbnails, disclaimer_thumbnails, license_thumbnails, display_order_thumbnails, urls_image_links,
image_category_image_links, contributor_image_links, disclaimer_image_links, license_image_links, display_order_image_links, 
urls_3d_snippets, image_category_3d_snippets, contributor_3d_snippets, disclaimer_3d_snippets, license_3d_snippets, 
display_order_3d_snippets, longitude, latitude, collector_ids, collectors, donator_ids, donators, localities, family, t_order,
class, specimen_count_min, specimen_count_males_min, specimen_count_females_min, collection_code_full_path, collection_name_full_path
	LIMIT 1";          
            
 
         
    
		$query=stuff_for_properties($query);
        $stmt=$conn->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);

       header('Content-Type: application/json; charset=utf-8');
        if($rs[0]["full_count"]>0)
        {
     
            print(json_encode($rs));
           
        }
        $conn=null;
    }
    else
    {
                header('Content-Type: application/json; charset=utf-8');
    }
 
}

function  json_darwin_get_collection_id($param)
{
	
    $conn=connect_to_darwin();
     $query="SELECT id FROM collections where fulltoindex(code)=(SELECT * FROM fulltoindex(:param)) LIMIT 1;";
 
     $stmt=$conn->prepare($query);
     $stmt->bindValue(":param", $param);
     $stmt->execute();
   
     $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);

      header('Content-Type: application/json; charset=utf-8');
     if(count($rs)>0)
     {
         print(json_encode($rs));
           
     }
	 else
	 {
		 
		 print(json_encode(array()));
	 }
     
}

function json_darwin_get_taxon_generic($prefix, $rank_id, $includeLower=FALSE)
{
    $conn=connect_to_darwin();
    $rows=array();
    if($includeLower===FALSE)
    {
        $query="SELECT DISTINCT id, name FROM taxonomy 
            WHERE level_ref=:rank_id 
            AND name_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:prefix)),'%') 
            ORDER BY name LIMIT 50;
            ";
    }
    else
    {
        $query="SELECT DISTINCT id, name FROM taxonomy 
            WHERE level_ref>=:rank_id 
            AND name_indexed LIKE CONCAT('%', (SELECT * FROM fulltoindex(:prefix)),'%') 
            ORDER BY name LIMIT 50;
            ";
    }
    $stmt=$conn->prepare($query);
    $stmt->bindValue(":prefix", $prefix);
    $stmt->bindValue(":rank_id", $rank_id);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
     header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
}


function json_darwin_get_taxon_by_parent_generic($prefix, $rank_id, $parent_id, $includeLower=FALSE)
{
    $conn=connect_to_darwin();
    $rows=array();
    if($includeLower===FALSE)
    {
        $parent_id="(".str_replace(",","|",$parent_id).")";
        $query="SELECT DISTINCT id, name FROM taxonomy 
            WHERE level_ref=:rank_id
            AND path ~ :parent_id
            AND name_indexed LIKE CONCAT('%',(SELECT * FROM fulltoindex(:prefix)),'%') 
            ORDER BY name LIMIT 50;
            ";
    }
    else
    {
         $parent_id="(".str_replace(",","|",$parent_id).")";
        $query="SELECT DISTINCT id, name FROM taxonomy 
            WHERE level_ref>=:rank_id 
            AND path ~ :parent_id
            AND name_indexed LIKE CONCAT('%',(SELECT * FROM fulltoindex(:prefix)),'%')  
            ORDER BY name LIMIT 50;
            ";
    }
    $stmt=$conn->prepare($query);
    $stmt->bindValue(":prefix", $prefix);
    $stmt->bindValue(":rank_id", $rank_id);
    $parent_id="/".$parent_id."/";
    $stmt->bindValue(":parent_id", $parent_id);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
   
    header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
}

function json_darwin_get_taxon_by_collection($prefix, $rank_id, $collection_id, $includeLower=FALSE)
{

    $conn=connect_to_darwin();
    $rows=array();
        if($includeLower===FALSE)
    {
        $query="SELECT DISTINCT mv_taxonomy_by_collection.id AS id , mv_taxonomy_by_collection.name AS name FROM mv_taxonomy_by_collection          
            WHERE  level_ref=:rank_id
            AND mv_taxonomy_by_collection.name_indexed LIKE CONCAT('%',(SELECT * FROM fulltoindex(:prefix)),'%') 
			AND
			full_collection_path LIKE '%/'||:collection_id||'/%'
            ORDER BY name LIMIT 50;";
    }
    else
    {
        $query="SELECT DISTINCT mv_taxonomy_by_collection.id AS id , mv_taxonomy_by_collection.name AS name FROM mv_taxonomy_by_collection 
             
            WHERE  level_ref>=:rank_id
            AND mv_taxonomy_by_collection.name_indexed LIKE CONCAT('%',(SELECT * FROM fulltoindex(:prefix)),'%') 
			AND
			full_collection_path LIKE '%/'||:collection_id||'/%'
            ORDER BY name LIMIT 50;";
    }

   
    $stmt=$conn->prepare($query);
    $stmt->bindValue(":prefix", $prefix);
    $stmt->bindValue(":rank_id", $rank_id);
    $stmt->bindValue(":collection_id", $collection_id);
    $stmt->execute();
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
    
     header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
}

function json_darwin_get_taxon_by_collection_and_parent($prefix, $rank_id, $collection_id, $parent_id, $includeLower=FALSE)
{

    $conn=connect_to_darwin();
    $rows=array();
    if($includeLower===FALSE)
    {
        $parent_id="(".str_replace(",","|",$parent_id).")";
        $query="SELECT DISTINCT taxonomy.id AS id , taxonomy.name AS name FROM taxonomy 
            INNER JOIN
            (
                   SELECT distinct unnest(string_to_array(taxon_path||'/'||taxon_ref::varchar, '/'))  as key_taxon from specimens where collection_path||collection_ref::varchar||'/' LIKE '%/'||:collection_id||'/%'
                    AND taxon_path is not null 
            ) AS specimens
                    ON
                    taxonomy.id::text = specimens.key_taxon
                     WHERE
                     level_ref=:rank_id
                     AND 
                     taxonomy.path  ~  :parent_id
                    AND taxonomy.name_indexed LIKE  CONCAT('%',(SELECT * FROM fulltoindex(:prefix)),'%') 
                    ORDER BY name
                    LIMIT 50;
            ";
    }
    else
    {
        $parent_id="(".str_replace(",","|",$parent_id).")";
        $query="SELECT DISTINCT taxonomy.id AS id , taxonomy.name AS name FROM taxonomy 
            INNER JOIN
            (
                   SELECT unnest(string_to_array(taxon_path||'/'||taxon_ref::varchar, '/'))  as key_taxon from specimens where collection_path||collection_ref::varchar||'/' LIKE '%/'||:collection_id||'/%'
                    AND taxon_path is not null 
            ) AS specimens
                    ON
                    taxonomy.id::text = specimens.key_taxon
                     WHERE
                     level_ref>=:rank_id
                     AND 
                     taxonomy.path  ~  :parent_id
                    AND taxonomy.name_indexed LIKE  CONCAT('%',(SELECT * FROM fulltoindex(:prefix)),'%') 
                    ORDER BY name
                    LIMIT 50;
            ";
    }
    
    $stmt=$conn->prepare($query);
    $stmt->bindValue(":prefix", $prefix);
    $stmt->bindValue(":rank_id", $rank_id);
    $stmt->bindValue(":collection_id", $collection_id);
    $parent_id="/".$parent_id."/";
    $stmt->bindValue(":parent_id", $parent_id);
    $stmt->execute();
   // print($conn->errorInfo());
    $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);

     header('Content-Type: application/json; charset=utf-8');
    print(json_encode($rs));
    $conn=null;
}



?>
