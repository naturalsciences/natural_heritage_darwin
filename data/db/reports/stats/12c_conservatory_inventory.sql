select
  collection_name as "Collection name",
  room as "Conservation room",
  "row" as "Row",
  shelf as "Column",
  case
    when coalesce(container, '') = '' then
      upper(coalesce(sub_container, ''))
    else
      upper(container)
  end as "Drawer",
  (select name
   from taxonomy
   where id IN (select unnest(string_to_array(trim(taxon_path,'/'),'/'))::bigint)
     and level_ref = 4
  ) as "Phylum",
  (select name
   from taxonomy
   where id IN (select unnest(string_to_array(trim(taxon_path,'/'),'/'))::bigint)
     and level_ref = 12
  ) as "Class",
  (select name
   from taxonomy
   where id IN (select unnest(string_to_array(trim(taxon_path,'/'),'/'))::bigint)
     and level_ref = 28
  ) as "Order",
  (select name
   from taxonomy
   where id IN (select unnest(string_to_array(trim(taxon_path,'/'),'/'))::bigint)
     and level_ref = 33
  ) as "Super family",
  (select name
   from taxonomy
   where id IN (select unnest(string_to_array(trim(taxon_path,'/'),'/'))::bigint)
     and level_ref = 34
  ) as "Family",
  taxon_name as "Taxon name",
  sum(specimen_count_min) as "Minimum number of objects",
  sum(specimen_count_max) as "Maximum number of objects"
from specimens
where (strpos(collection_path, (select path || id from collections where id = 2)) != 0
   or collection_ref = 2)
  and upper(room) = '12C' 
group by collection_name, room, "row", shelf, case
    when coalesce(container, '') = '' then
      upper(coalesce(sub_container, ''))
    else
      upper(container)
  end, taxon_name, taxon_path
order by collection_name, room, "row", shelf, case
    when coalesce(container, '') = '' then
      upper(coalesce(sub_container, ''))
    else
      upper(container)
  end;
