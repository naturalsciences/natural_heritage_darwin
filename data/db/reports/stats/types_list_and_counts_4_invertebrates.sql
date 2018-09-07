select distinct collection_name as "Collection", 
                type as "Type", 
                type_group as "Type grouping", 
                type_search "Type grouping for public", 
                count(type) over (partition by collection_ref, type) as "Type record count",
                sum(specimen_count_min) over (partition by collection_ref, type) as "Type specimen count"
from specimens
where 
  ( collection_ref = 2
    or
    collection_path like '/2/%'
  )
  and
  collection_ref != 324
order by collection_name, type ;

