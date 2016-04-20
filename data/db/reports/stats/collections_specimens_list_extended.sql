with family_ids as 
(
  select subset.id, taxon_id::bigint, family.name as family_name
  from 
  (
    select id,unnest(string_to_array(taxon_path,'/')) as taxon_id
    from specimens
    where  (collection_ref = 1
            or collection_path LIKE '/2/%'
            or collection_path LIKE '/2/%'
           )
  ) as subset
  inner join taxonomy as family on subset.taxon_id::bigint = family.id and family.level_ref = 34
  where subset.taxon_id is not null 
    and subset.taxon_id != ''
),
order_ids as 
(
  select subset.id, taxon_id::bigint, orders.name as order_name
  from 
  (
    select id,unnest(string_to_array(taxon_path,'/')) as taxon_id
    from specimens
    where  (collection_ref = 1
            or collection_path LIKE '/2/%'
            or collection_path LIKE '/2/%'
           )
  ) as subset
  inner join taxonomy as orders on subset.taxon_id::bigint = orders.id and orders.level_ref = 28
  where subset.taxon_id is not null 
    and subset.taxon_id != ''
),
class_ids as 
(
  select subset.id, taxon_id::bigint, classes.name as class_name
  from 
  (
    select id,unnest(string_to_array(taxon_path,'/')) as taxon_id
    from specimens
    where  (collection_ref = 1
            or collection_path LIKE '/2/%'
            or collection_path LIKE '/2/%'
           )
  ) as subset
  inner join taxonomy as classes on subset.taxon_id::bigint = classes.id and classes.level_ref = 12
  where subset.taxon_id is not null 
    and subset.taxon_id != ''
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
       taxon_name as "Taxon",
       taxon_level_name as "Taxon level",
       taxon_status as "Taxon status",
       class_ids.class_name as "Class name",
       order_ids.order_name as "Order name",
       family_ids.family_name as "Family name",
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
left join family_ids on specimens.id = family_ids.id
left join order_ids on specimens.id = order_ids.id
left join class_ids on specimens.id = class_ids.id
where 
  (
   collection_ref = 2
   or 
   collection_path LIKE '/2/%'
  )
  and
  collection_ref != 324
  and
  (
   taxon_ref = 357
   or
   taxon_path like '%/357/%'
  ) 
  --and gtu_country_tag_value IN ('Burundi', 'Democratic Republic of the Congo', 'Rwanda')
order by collection_ref, "Countries", "Code(s)", "Taxon";
