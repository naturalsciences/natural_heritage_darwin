select collection_name as "Collection",
       ( select array_agg(codes) from
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
       ) as "Code",
       ig_num as "I.G.",
       taxon_name as "Taxon",
       specimen_part as "Part state",
       building as "Building",
       floor as "Floor",
       room as "Room",
       "row" as "Row",
       shelf as "Shelf",
       container as "Container",
       sub_container as "Sub-Container",
       specimen_count_min as "Count min",
       specimen_count_max as "Count max"
from specimens
where 
  (collection_ref IN (6,7)
   or collection_path LIKE '/1/6/%'
   or collection_path LIKE '/1/7/%'
  )
and lower(specimen_part) like '%mount%'
order by collection_ref, "Code";
