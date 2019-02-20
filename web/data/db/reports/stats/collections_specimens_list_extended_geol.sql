with system_ids as 
(
  select subset.id, chrono_id::bigint, chrono.name as chronol_name
  from 
  (
    select id,unnest(string_to_array(chrono_path,'/')) as chrono_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join chronostratigraphy as chrono on subset.chrono_id::bigint = chrono.id and chrono.level_ref = 58
  where subset.chrono_id is not null 
    and subset.chrono_id != ''
),
era_ids as 
(
  select subset.id, chrono_id::bigint, chrono.name as chronol_name
  from 
  (
    select id,unnest(string_to_array(chrono_path,'/')) as chrono_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join chronostratigraphy as chrono on subset.chrono_id::bigint = chrono.id and chrono.level_ref = 56
  where subset.chrono_id is not null 
    and subset.chrono_id != ''
),
eon_ids as 
(
  select subset.id, chrono_id::bigint, chrono.name as chronol_name
  from 
  (
    select id,unnest(string_to_array(chrono_path,'/')) as chrono_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join chronostratigraphy as chrono on subset.chrono_id::bigint = chrono.id and chrono.level_ref = 55
  where subset.chrono_id is not null 
    and subset.chrono_id != ''
),
mineral_sub_class_ids as 
(
  select subset.id, mineral_id::bigint, mineral.name as mineralo_name
  from 
  (
    select id,unnest(string_to_array(mineral_path,'/')) as mineral_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join mineralogy as mineral on subset.mineral_id::bigint = mineral.id and mineral.level_ref = 71
  where subset.mineral_id is not null 
    and subset.mineral_id != ''
),
mineral_class_ids as 
(
  select subset.id, mineral_id::bigint, mineral.name as mineralo_name
  from 
  (
    select id,unnest(string_to_array(mineral_path,'/')) as mineral_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join mineralogy as mineral on subset.mineral_id::bigint = mineral.id and mineral.level_ref = 70
  where subset.mineral_id is not null 
    and subset.mineral_id != ''
),
lithology_class_ids as 
(
  select subset.id, lithology_id::bigint, lithology.name as lithologic_name
  from 
  (
    select id,unnest(string_to_array(lithology_path,'/')) as lithology_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join lithology on subset.lithology_id::bigint = lithology.id and lithology.level_ref = 79
  where subset.lithology_id is not null 
    and subset.lithology_id != ''
),
lithology_main_class_ids as 
(
  select subset.id, lithology_id::bigint, lithology.name as lithologic_name
  from 
  (
    select id,unnest(string_to_array(lithology_path,'/')) as lithology_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join lithology on subset.lithology_id::bigint = lithology.id and lithology.level_ref = 84
  where subset.lithology_id is not null 
    and subset.lithology_id != ''
),
lithology_rock_ids as 
(
  select subset.id, lithology_id::bigint, lithology.name as lithologic_name
  from 
  (
    select id,unnest(string_to_array(lithology_path,'/')) as lithology_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join lithology on subset.lithology_id::bigint = lithology.id and lithology.level_ref = 78
  where subset.lithology_id is not null 
    and subset.lithology_id != ''
),
lithology_group_ids as 
(
  select subset.id, lithology_id::bigint, lithology.name as lithologic_name
  from 
  (
    select id,unnest(string_to_array(lithology_path,'/')) as lithology_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join lithology on subset.lithology_id::bigint = lithology.id and lithology.level_ref = 76
  where subset.lithology_id is not null 
    and subset.lithology_id != ''
),
lithology_main_group_ids as 
(
  select subset.id, lithology_id::bigint, lithology.name as lithologic_name
  from 
  (
    select id,unnest(string_to_array(lithology_path,'/')) as lithology_id
    from specimens
    where  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
           )
  ) as subset
  inner join lithology on subset.lithology_id::bigint = lithology.id and lithology.level_ref = 75
  where subset.lithology_id is not null 
    and subset.lithology_id != ''
)
select collection_name as "Collection",
       ( select array_to_string(array_agg(codes), ';') from
         ( select
             coalesce(code_prefix, '') ||
             coalesce(code_prefix_separator, '') ||
             code ||
             coalesce(code_suffix_separator, '') ||
             coalesce(code_suffix, '') as codes
           from codes
           where referenced_relation = 'specimens'
             and record_id = specimens.id
             and code_category = 'main'
         ) as cod    
       ) as "Code(s)",
       object_name as "Object name",
       chrono_name as "Chrono unit",
       chrono_level_name as "Chrono level",
       chrono_status as "Chrono status",
       eon_ids.chronol_name as "Eon name",
       era_ids.chronol_name as "Era name",
       system_ids.chronol_name as "System name",
       mineral_name as "Mineralogy unit",
       mineral_level_name as "Mineralogy level",
       mineral_status as "Mineralogy status",
       mineral_class_ids.mineralo_name as "Mineralogy class name",
       mineral_sub_class_ids.mineralo_name as "Mineralogy subclass name",
       lithology_name as "Lithology unit",
       lithology_level_name as "Lithology level",
       lithology_status as "Lithology status",
       lithology_main_class_ids.lithologic_name as "Lithology main class name",
       lithology_class_ids.lithologic_name as "Lithology class name",
       lithology_rock_ids.lithologic_name as "Lithology rock name",
       lithology_group_ids.lithologic_name as "Lithology group name",
       lithology_main_group_ids.lithologic_name as "Lithology main group name",
       specimen_part as "Part state",
       type as "Type",
       sex as "Sex",
       state as "Sexual state",
       stage as "Stage",
       social_status as "Social status",
       complete as "Complete specimen",
       (
         select array_to_string(array_agg("formated_name"), ';') from
         (
           select formated_name
           from people
           where id = ANY (spec_ident_ids)
         ) as identifiers_list
       ) as "Identifiers",
       acquisition_category as "Acquisition type",
       fct_mask_date(acquisition_date, acquisition_date_mask) as "Acquisition date",
       ig_num as "I.G.",
       fct_mask_date(ig_date, ig_date_mask) as "I.G. date",
       (
         select array_to_string(array_agg("formated_name"), ';') from
         (
           select formated_name
           from people
           where id = ANY (spec_coll_ids)
         ) as collectors_list
       ) as "Collectors",
       expedition_name as "Expedition",
       gtu_code as "Sampling location code",
       gtu_location[0] as "Sampling location latitude",
       gtu_location[1] as "Sampling location longitude",
       fct_mask_date(gtu_from_date, gtu_from_date_mask) as "Sampling start date",
       fct_mask_date(gtu_to_date, gtu_to_date_mask) as "Sampling end date",
       gtu_country_tag_value as "Countries",
       gtu_province_tag_value as "Provinces",
       gtu_others_tag_value as "Other location tags",
       building as "Building",
       floor as "Floor",
       room as "Room",
       "row" as "Row",
       shelf as "Shelf",
       container as "Container",
       sub_container as "Sub-Container",
       specimen_count_min as "Count min",
       specimen_count_max as "Count max",
       ( select array_to_string(array_agg("Comments"), '\n') from
         ( select
	     notion_concerned || ': ' || comment as "Comments"
           from comments
           where referenced_relation = 'specimens'
             and record_id = specimens.id
         ) as com    
       ) as "Comment(s)"
from specimens
left join system_ids on specimens.id = system_ids.id
left join era_ids on specimens.id = era_ids.id
left join eon_ids on specimens.id = eon_ids.id
left join mineral_sub_class_ids on specimens.id = mineral_sub_class_ids.id
left join mineral_class_ids on specimens.id = mineral_class_ids.id
left join lithology_class_ids on specimens.id = lithology_class_ids.id
left join lithology_main_class_ids on specimens.id = lithology_main_class_ids.id
left join lithology_rock_ids on specimens.id = lithology_rock_ids.id
left join lithology_group_ids on specimens.id = lithology_group_ids.id
left join lithology_main_group_ids on specimens.id = lithology_main_group_ids.id
where 
  (
/*   collection_ref = 9
   or 
   collection_path LIKE '%/9/%'
   or*/
   collection_ref = 231
   or 
   collection_path LIKE '%/231/%'
  )
  and
  collection_ref != 263
  and
  collection_path NOT LIKE '%/263/%'
  and
  (
   chrono_ref = 4
   or
   chrono_path like '%/4/%'
   or
   chrono_ref = 24
   or
   chrono_path like '%/24/%'
   or
   chrono_ref = 25
   or
   chrono_path like '%/25/%'
   or
   chrono_ref = 188
   or
   chrono_path like '%/188/%'
   or
   lithology_ref = 1
   or 
   lithology_path like '%/1/%'
   or
   lithology_ref = 2
   or 
   lithology_path like '%/2/%'
   or
   lithology_ref = 3
   or 
   lithology_path like '%/3/%'
   or
   lithology_ref = 4
   or 
   lithology_path like '%/4/%'
   or
   lithology_ref = 477
   or 
   lithology_path like '%/477/%'
   or
   lithology_ref = 516
   or 
   lithology_path like '%/516/%'
   or
   lithology_ref = 579
   or 
   lithology_path like '%/579/%'
   or
   lithology_ref = 599
   or 
   lithology_path like '%/599/%'
   or
   lithology_ref = 605
   or 
   lithology_path like '%/605/%'
   or
   lithology_ref = 612
   or 
   lithology_path like '%/612/%'
   or
   lithology_ref = 614
   or 
   lithology_path like '%/614/%'
   or
   lithology_ref = 617
   or 
   lithology_path like '%/617/%'
   or
   lithology_ref = 625
   or 
   lithology_path like '%/625/%'
   or
   mineral_ref = 2
   or 
   mineral_path like '%/2/%'
   or
   mineral_ref = 616
   or 
   mineral_path like '%/616/%'
   or
   mineral_ref = 617
   or 
   mineral_path like '%/617/%'
   or
   mineral_ref = 618
   or 
   mineral_path like '%/618/%'
   or
   mineral_ref = 619
   or 
   mineral_path like '%/619/%'
   or
   mineral_ref = 620
   or 
   mineral_path like '%/620/%'
   or
   mineral_ref = 621
   or 
   mineral_path like '%/621/%'
   or
   mineral_ref = 623
   or 
   mineral_path like '%/623/%'
   or
   mineral_ref = 624
   or 
   mineral_path like '%/624/%'
   or
   mineral_ref = 6436
   or 
   mineral_path like '%/6436/%'
   or
   mineral_ref = 6477
   or 
   mineral_path like '%/6477/%'
   or
   mineral_ref = 6479
   or 
   mineral_path like '%/6479/%'
   or
   mineral_ref = 6752
   or 
   mineral_path like '%/6752/%'
  ) 
  --and gtu_country_tag_value IN ('Burundi', 'Democratic Republic of the Congo', 'Rwanda')
order by collection_ref, "Countries", "Code(s)", "Chrono unit", "Mineralogy unit", "Lithology unit";
