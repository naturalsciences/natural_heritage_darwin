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

                        string_agg(id::varchar,'; ' order by a.id desc ) as id,
                        collection_code,

                        code,
                        additional_codes,
                        ig_num,
                        string_agg(taxon_name,'; ' order by a.id desc) as taxon_name,
                        string_agg(author,'; ' order by a.id desc) as author,
                        string_agg(family,'; ' order by a.id desc) as family,
                        string_agg(type,'; ' order by a.id desc) as type,
                        specimen_count_min,
                        specimen_count_max,
                        string_agg(identifiers,'; ' order by a.id desc) as identifiers,
                        string_agg(identification_year::varchar,'; ' order by a.id desc) as identification_year, 
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
                        string_agg(properties_locality, '; ' order by a.id) as  properties_locality,
                        collectors,
                        expedition_name,
                        donators,



                        string_agg(comment, '; ' order by a.id) as comment,
                        string_agg(properties_all, '; ' order by a.id) as properties_all,
                        specimen_creation_date
                         
                        FROM

                            (SELECT 
                            s.id,  
                            collection_code,
                            COALESCE(c.code_prefix,'')||COALESCE(c.code_prefix_separator,'')||COALESCE(c.code,'')||COALESCE(c.code_suffix,'')||
                            COALESCE(c.code_suffix_separator,'') as code,
                            string_agg(DISTINCT c2.code,', ') as additional_codes,
                            ig_num,
                            case 
                                when 	taxon_level_ref=49 
                                    or 
                                    (taxon_level_ref>=48 and array_length(regexp_split_to_array(taxon_name, ' '),1)>=3 and 
                                    (regexp_split_to_array(taxon_name, ' '))[3]=lower((regexp_split_to_array(taxon_name, ' '))[3]) )
                                then
                                    array_to_string((regexp_split_to_array(taxon_name, ' '))[1:3], ' ')
                                when taxon_level_ref=48 then
                                    array_to_string((regexp_split_to_array(taxon_name, ' '))[1:2], ' ')
                                else
                                    taxon_name 
                            end as taxon_name,
                            case
                                when 	taxon_level_ref=49 
                                    or 
                                    (taxon_level_ref>=48 and array_length(regexp_split_to_array(taxon_name, ' '),1)>=3 and 
                                    (regexp_split_to_array(taxon_name, ' '))[3]=lower((regexp_split_to_array(taxon_name, ' '))[3]) )
                                then
                                    array_to_string((regexp_split_to_array(taxon_name, ' '))[4:100], ' ')
                                when taxon_level_ref=48 then
                                    array_to_string((regexp_split_to_array(taxon_name, ' '))[3:100], ' ')
                            end as author,
                            (fct_rmca_sort_taxon_get_parent_level_text(taxon_ref,34)) as family,
                            type,
                            specimen_count_min,
                            specimen_count_max,
                            array_to_string(array_agg(DISTINCT ident.formated_name),'; ') as identifiers,
                            date_part('year', i.notion_date) as identification_year, 
                            gtu_country_tag_value,
                            array_to_string(array_agg(DISTINCT municipality.tag), '; ') as municipality,
                            array_to_string(array_agg(DISTINCT region_district.tag), '; ') as region_district,
                            array_to_string(array_agg(DISTINCT exact_site.tag), '; ') as exact_site,
                            array_to_string(array_agg(DISTINCT ecology.tag), '; ') as ecology,
                            gtu_others_tag_value,
                            gtu_code,
                            --coordinates_source,
                            gtu_location[1] as latitude_deci,
                            gtu_location[2] as longitude_deci,

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
                            expedition_name,
                            array_to_string(array_agg(DISTINCT donator.formated_name),'; ') as donators,
                            
                            
                            
                            array_to_string(array_agg(DISTINCT comm.notion_concerned||': '||comm.comment), '; ') as comment,
                             array_to_string(array_agg(
                                DISTINCT p.property_type||': '::varchar||p.lower_value||COALESCE('-'::varchar||p.upper_value,'')||COALESCE(' '::varchar||p.property_unit,'')),'; ')
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
                                ON locp.referenced_relation='gtu' AND s.id=locp.record_id
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
                            
                            WHERE s.id in
                                (SELECT fct_rmca_dynamic_saved_search(
                                    :ID_Q, :ID_USER
                                )) 

                            GROUP BY s.id, c.code_prefix, c.code_prefix_separator, c.code, c.code_suffix, c.code_suffix_separator, i.id 
                            
                             
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
                        collectors,
                        expedition_name,
                        donators,
                        specimen_creation_date


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
