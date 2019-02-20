/*create temporary table if not exists zzz_taxa_families as
(
  select id, name
  from taxonomy
  where level_ref = 34
);
*/
with taxa_ids as 
(
  select subset.id, taxon_id::bigint, taxonomy.name as family_name
  from 
  (
    select id,unnest(string_to_array(taxon_path,'/')) as taxon_id
    from specimens
    where  (collection_ref = 1
            or collection_path LIKE '/1/%'
            or collection_path LIKE '/1/%'
           )
  ) as subset
  inner join taxonomy on subset.taxon_id::bigint = taxonomy.id and taxonomy.level_ref = 34
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
       taxa_ids.family_name as "Family name",
       specimen_part as "Part state",
       type as "Type",
       sex as "Sex",
       state as "Sexual state",
       stage as "Stage",
       social_status as "Social status",
       complete as "Complete specimen",
       acquisition_category as "Acquisition type",
       fct_mask_date(acquisition_date, acquisition_date_mask) as "Acquisition date",
       ig_num as "I.G.",
       fct_mask_date(ig_date, ig_date_mask) as "I.G. date",
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
left join taxa_ids on specimens.id = taxa_ids.id
where 
  (collection_ref = 1
   or collection_path LIKE '/1/%'
   or collection_path LIKE '/1/%'
  )
order by collection_ref, "Code(s)";
