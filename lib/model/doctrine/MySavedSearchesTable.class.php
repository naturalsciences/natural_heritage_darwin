<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class MySavedSearchesTable extends DarwinTable
{
  public function addUserOrder(Doctrine_Query $q = null,$user)
  {
    if (is_null($q))
    {
        $q = Doctrine_Query::create()
            ->from('MySavedSearches s');
    }
    $alias = $q->getRootAlias();
    $q->andWhere($alias . '.user_ref = ?', $user)
        ->orderBy($alias . '.favorite DESC');
    return $q;
  }

  public function addIsSearch(Doctrine_Query $q, $is_search = false)
  {
    $q->andWhere($q->getRootAlias() . '.is_only_id = ?', !$is_search);
    return $q;
  }
  
  public function getSavedSearchByKey($id, $user )
  {
    return $this->addUserOrder(null, $user)
      ->andWhere('id = ?', $id )
      ->fetchOne();
  }

  public function fetchSearch($user_ref, $num_per_page)
  {
    $q = $this->addUserOrder(null,$user_ref);
    $this->addIsSearch($q, true);
    $q->limit($num_per_page);

    return $q->execute();
  }

  public function fetchSpecimens($user_ref, $num_per_page)
  {
    $q = $this->addUserOrder(null,$user_ref);
    $this->addIsSearch($q, false);
    $q->limit($num_per_page);

    return $q->execute();
  }

  public function getListFor($user, $source)
  {
    $q = $this->addUserOrder(null,$user);
    $this->addIsSearch($q, false);
    return $q->andWhere('subject = ?',$source)->execute();
  }

  public function getAllFields($source, $is_reg_user = false)
  {
    $columns = array(
      'category'=>'Category',
      'collection'=>'Collection',
      'taxon'=>'Taxon',
      'type'=>'Type',
      'gtu'=>'Sampling Location',
      'codes'=>'Codes',
      'chrono'=>'Chronostratigraphy',
      'ig'=>'Inv. General',
      'litho'=>'Lithostratigraphy',
      'lithologic'=>'Lithology',
      'expedition'=>'Expedition',
      'mineral'=>'Mineralogy',
      'count'=>'Count',
      'acquisition_category' => 'Acquisition category',

      'individual_type' => 'Type',
      'sex' => 'Sex',
      'state' => 'State',
      'stage'=> 'Stage',
      'social_status' =>'Social Status',
      'rock_form'=>'Rock Form',

      'part'=>'Part',
      'part_status'=>'Part Status',
      'object_name' => 'Object name',
      'building'=>'Building',
      'floor'=>'Floor',
      'room'=>'Room',
      'row'=>'Row',
      'col'=>'Column',
      'shelf'=>'Shelf',
      'container'=>'Container',
      'container_type'=>'Container Type',
      'container_storage'=>'Container Storage',
      'sub_container'=>'Sub Container',
      'sub_container_type'=>'Sub Container Type',
      'sub_container_storage'=>'Sub Container Storage',
      'specimen_count'=>' Count',
      'loans' => 'Loans'
    );

    return $columns;
  }
  
  //ftheeten 2018 07 03
  public function countRecursiveSQLRecords($user_id, $query_id)
  {
	  $sql="SELECT COUNT(*) from  fct_rmca_dynamic_saved_search(
			:query_id,:user_id);";
	  $conn = Doctrine_Manager::connection();
	  $q = $conn->prepare($sql);
	  $q->bindParam(":query_id", $query_id, PDO::PARAM_INT);
	  $q->bindParam(":user_id", $user_id, PDO::PARAM_INT);
	  $q->execute();
	  return $q->fetch()[0];
  }
  
  public function getSavedSearchData($user_id, $query_id)
  {
                        $sql="SELECT

                        string_agg(DISTINCT id::varchar,'; ' order by a.id::varchar desc ) as id,
                        collection_code,

                        code,
                        additional_codes,
                        ig_num,
                        string_agg(DISTINCT taxon_name,'; ') as taxon_name,
                        string_agg(DISTINCT author,'; ') as author,
                        string_agg(DISTINCT full_scientific_name,'; ') as full_scientific_name,
                        string_agg(DISTINCT family,'; ') as family,
                        string_agg(DISTINCT type,'; ' ) as type,
                        specimen_count_min,
                        specimen_count_max,
                        string_agg(DISTINCT identifiers,'; ' ) as identifiers,
                        string_agg(DISTINCT abbreviated_identifiers,'; ' ) as abbreviated_identifiers,
                        string_agg(DISTINCT identification_year::varchar,'; ') as identification_year, 
                        --lat , long 2018 11 05
                        longitude_deci as longitude,
                        latitude_deci as latitude,
                        longitude_text,
                        latitude_text,
                        gtu_country_tag_value,
                        municipality,
                        region_district,
                        exact_site,
                        ecology,
                        gtu_others_tag_value,
                        gtu_code,
                         gtu_elevation,
                        collecting_year_from,
                        collecting_month_from,
                        collecting_day_from,
                        collecting_year_to,
                        collecting_month_to,
                        collecting_day_to,
                        string_agg(DISTINCT properties_locality, '; ' ) as  properties_locality,
                        string_agg(DISTINCT collectors, '; ' ) as collectors,
                        string_agg(DISTINCT abbreviated_collectors, '; ' ) as  abbreviated_collectors,
                        expedition_name,
                         string_agg(DISTINCT donators, '; ' ) as donators,
                        string_agg(DISTINCT abbreviated_donators, '; ' ) as  abbreviated_donators,
                        --2018 05 11
                        acquisition_category,
                        acquisition_date,
                        sex,
                        stage,
                        state,
                        social_status,
                        specimen_part,
                        complete,
                        object_name,
			specimen_status,
			container_storage,
			string_agg(method, '; ') as method,
			string_agg(tool, '; ') as tool,
			

                        string_agg(DISTINCT comment, '; ' ) as comment,
                        string_agg(DISTINCT properties_all, '; ' ) as properties_all,
                        specimen_creation_date
                         
                        FROM

                            (SELECT 
                            s.id,  
                            collection_code,
                            COALESCE(c.code_prefix,'')||COALESCE(c.code_prefix_separator,'')||COALESCE(c.code,'')||COALESCE(c.code_suffix,'')||
                            COALESCE(c.code_suffix_separator,'') as code,
                            string_agg(DISTINCT c2.code,', ') as additional_codes,
                            ig_num,
                            taxon_name as full_scientific_name,
                           

				(fct_rmca_taxonomy_split_name_author(taxon_name,taxon_level_ref ))[1]
                            taxon_name

                            ,
                            
			(fct_rmca_taxonomy_split_name_author(taxon_name,taxon_level_ref ))[2]
                            as author,
                            (fct_rmca_sort_taxon_get_parent_level_text(taxon_ref,34)) as family,
                            type,
                            specimen_count_min,
                            specimen_count_max,
                            array_to_string(array_agg(DISTINCT ident.formated_name),'; ') as identifiers,

			  /*2018 11 21 */
			  CASE WHEN ident.given_name IS NULL THEN
				ident.family_name
			    ELSE
				TRIM(ident.family_name)||' '||TRIM(fct_rmca_abbreviate_names(ident.given_name))
			END AS abbreviated_identifiers,
			
                            date_part('year', i.notion_date) as identification_year, 
                            gtu_country_tag_value,
                            array_to_string(array_agg(DISTINCT municipality.tag), '; ') as municipality,
                            array_to_string(array_agg(DISTINCT region_district.tag), '; ') as region_district,
                            array_to_string(array_agg(DISTINCT exact_site.tag), '; ') as exact_site,
                            array_to_string(array_agg(DISTINCT ecology.tag), '; ') as ecology,
                            gtu_others_tag_value,
                            gtu_code,
                            --coordinates_source,
                            gtu_location[0] as latitude_deci ,
                            gtu_location[1] as longitude_deci,
rmca_dms_to_text(coordinates_source, latitude_dms_degree,
latitude_dms_minutes,
latitude_dms_seconds,
latitude_dms_direction, latitude, 'lat'::varchar) as
latitude_text,
rmca_dms_to_text(coordinates_source, longitude_dms_degree,
longitude_dms_minutes,
longitude_dms_seconds,
longitude_dms_direction, longitude, 'lon'::varchar) as
longitude_text,
                            gtu_elevation,
                            CASE
                                WHEN 	s.gtu_from_date_mask>=32 
                                THEN	date_part('year', s.gtu_from_date)
                                ELSE 	NULL
                            END as collecting_year_from,
                            CASE
                                WHEN 	s.gtu_from_date_mask>=48 
                                THEN	date_part('month', s.gtu_from_date)
                                ELSE 	NULL
                            END as collecting_month_from,
                            CASE
                                WHEN 	s.gtu_from_date_mask>=56
                                THEN	date_part('day', s.gtu_from_date)
                                ELSE 	NULL
                            END as collecting_day_from,
                            CASE
                                WHEN 	s.gtu_to_date_mask>=32 
                                THEN	date_part('year', s.gtu_to_date)
                                ELSE 	NULL
                            END as collecting_year_to,
                            CASE
                                WHEN 	s.gtu_to_date_mask>=48 
                                THEN	date_part('month', s.gtu_to_date)
                                ELSE 	NULL
                            END as collecting_month_to,
                            CASE
                                WHEN 	s.gtu_to_date_mask>=56 
                                THEN	date_part('day', s.gtu_to_date)
                                ELSE 	NULL
                            END as collecting_day_to,
                            array_to_string(array_agg(DISTINCT locp.property_type||': '||locp.lower_value), '; ') as properties_locality,
                            array_to_string(array_agg(DISTINCT recol.formated_name),'; ') as collectors,
			/*2018 11 21 */
			  CASE WHEN recol.given_name IS NULL THEN
				recol.family_name
			    ELSE
				TRIM(recol.family_name)||' '||TRIM(fct_rmca_abbreviate_names(recol.given_name))
			END AS abbreviated_collectors,
                            
                            expedition_name,
                            array_to_string(array_agg(DISTINCT donator.formated_name),'; ') as donators,
                            		  /*2018 11 21 */
			  CASE WHEN donator.given_name IS NULL THEN
				donator.family_name
			    ELSE
				TRIM(donator.family_name)||' '||TRIM(fct_rmca_abbreviate_names(donator.given_name))
			END AS abbreviated_donators,
                            --2018 11 05
                            acquisition_category,
                            fct_mask_date(acquisition_date, acquisition_date_mask) as acquisition_date,
                            sex,
                            stage,
				state,
				social_status,
				specimen_part,
				complete,
				object_name,
				col_meth.method,
				col_tool.tool,

                            specimen_status,
							container_storage,
                            
                            array_to_string(array_agg(DISTINCT comm.notion_concerned||': '||comm.comment), '| ') as comment,
                             array_to_string(array_agg(
                                DISTINCT p.property_type||': '::varchar||p.lower_value||COALESCE('-'::varchar||p.upper_value,'')||COALESCE(' '::varchar||p.property_unit,'')),'| ')
                             as properties_all,
                             specimen_creation_date

                            FROM specimens s
                            LEFT JOIN codes c
                                ON s.id=c.record_id
                                AND c.referenced_relation='specimens'
                                AND c.code_category='main'
                            LEFT JOIN codes c2
                                ON  s.id=c2.record_id
                                AND c2.referenced_relation='specimens'
                                AND c2.code_category !='main'
                            LEFT JOIN identifications i
                                ON i.referenced_relation='specimens' AND s.id=i.record_id
                            LEFT JOIN catalogue_people cp1
                                ON cp1.referenced_relation='identifications' AND i.id=cp1.record_id 
                            LEFT JOIN people ident
                                ON cp1.people_ref = ident.id
                            LEFT JOIN tags AS municipality
                                ON s.gtu_ref= municipality.gtu_ref AND LOWER(municipality.sub_group_type) LIKE '%municipality%'
                            LEFT JOIN tags AS region_district
                                ON s.gtu_ref= region_district.gtu_ref AND LOWER(region_district.sub_group_type) LIKE '%region or district%'
                            LEFT JOIN tags AS exact_site
                                ON s.gtu_ref= exact_site.gtu_ref AND LOWER(exact_site.sub_group_type) LIKe '%exact_site%'
                            LEFT JOIN tags AS ecology
                                ON s.gtu_ref= (ecology.gtu_ref) AND LOWER(ecology.sub_group_type) LIKe '%ecology%'
                            LEFT JOIN gtu 
                                ON s.gtu_ref =gtu.id
                            LEFT JOIN properties AS locp 
                                ON locp.referenced_relation='gtu' AND gtu.id=locp.record_id
                            LEFT JOIN catalogue_people cp2
                                ON cp2.referenced_relation='specimens' AND cp2.people_type='collector' AND s.id=cp2.record_id 
                            LEFT JOIN people recol
                                ON cp2.people_ref = recol.id
                            LEFT JOIN catalogue_people cp3
                                ON cp3.referenced_relation='specimens' AND cp3.people_type='donator' AND s.id=cp3.record_id 
                            LEFT JOIN people donator
                                ON cp3.people_ref = donator.id
                            LEFT JOIN properties as males
                                ON males.referenced_relation='specimens' AND males.property_type='N males' AND s.id=males.record_id
                            LEFT JOIN properties as females
                                ON females.referenced_relation='specimens' and females.property_type='N females' AND s.id=females.record_id
                            LEFT JOIN properties as juveniles
                                ON juveniles.referenced_relation='specimens' and juveniles.property_type='N juveniles' AND s.id=juveniles.record_id
                            LEFT JOIN comments comm
                                ON comm.referenced_relation='specimens' AND s.id=comm.record_id
                            LEFT JOIN properties p
                                ON p.referenced_relation='specimens' AND s.id=p.record_id

                             -- 2018 11 05
                             LEFT JOIN specimen_collecting_methods as s_col_meth
				ON s.id= s_col_meth.specimen_ref
			     LEFT JOIN  collecting_methods as col_meth
				ON s_col_meth.collecting_method_ref=col_meth.id
				 -- 2018 11 05
                             LEFT JOIN specimen_collecting_tools as s_col_tool
				ON s.id= s_col_tool.specimen_ref
			     LEFT JOIN  collecting_tools as col_tool
				ON s_col_tool.collecting_tool_ref=col_tool.id
                            
                            WHERE s.id in
                                (SELECT fct_rmca_dynamic_saved_search(
                               
                                     :ID_Q, :ID_USER
                                   
                                )) 

                            GROUP BY s.id, c.code_prefix, c.code_prefix_separator, c.code, c.code_suffix, c.code_suffix_separator, i.id , col_tool.tool, col_meth.method,
                            --2018 11 21
                            ident.given_name, ident.family_name
                           ,recol.given_name, recol.family_name
                           ,donator.given_name, donator.family_name ,
                           coordinates_source, 
                           latitude_dms_degree, latitude_dms_minutes, latitude_dms_seconds, latitude_dms_direction, latitude,
                           longitude_dms_degree, longitude_dms_minutes, longitude_dms_seconds, longitude_dms_direction, longitude  
                            ) a


                        GROUP BY

                        collection_code,
                        code,
                        additional_codes,
                        ig_num,
                        specimen_count_min,
                        specimen_count_max, 
                        gtu_country_tag_value,
                        municipality,
                        region_district,
                        exact_site,
                        ecology,
                        gtu_others_tag_value,
                        gtu_code,



                        gtu_elevation,
                        collecting_year_from,
                        collecting_month_from,
                        collecting_day_from,
                        collecting_year_to,
                        collecting_month_to,
                        collecting_day_to,
                        --collectors,
                        expedition_name,
                        --donators,
                        specimen_creation_date,
                        --2018 11 05
                        acquisition_category,
                        acquisition_date,
                        sex,
                         stage,
                        state,
                        social_status,
                        specimen_part,
                        complete,
                        object_name,
                        specimen_status,
						container_storage,
                        longitude_deci ,
                        latitude_deci,
			latitude_text,
			longitude_text

                        ORDER BY code
                         LIMIT 50000;";
                        
                        $conn = Doctrine_Manager::connection();
                        $q = $conn->prepare($sql);
                        $q->bindParam(":ID_Q", $query_id, PDO::PARAM_INT);
                        $q->bindParam(":ID_USER", $user_id, PDO::PARAM_INT);
                        $q->execute();
    
                        $dataset=$q->fetchAll(PDO::FETCH_ASSOC);
                        return $dataset;
  
  }
  
   public function getSavedSearchDataTaxonomy($user_id, $query_id)
  {
                        $sql="SELECt * FROM fct_rmca_dynamic_saved_search_taxonomy(:ID_Q,:ID_USER);";
                        
                        $conn = Doctrine_Manager::connection();
                        $q = $conn->prepare($sql);
                        $q->bindParam(":ID_Q", $query_id, PDO::PARAM_INT);
                        $q->bindParam(":ID_USER", $user_id, PDO::PARAM_INT);
                        $q->execute();
    
                        $dataset=$q->fetchAll(PDO::FETCH_ASSOC);
                        return $dataset;
  
  }
  
  
  
}
