DROP TYPE IF EXISTS stats_collections CASCADE;

create type stats_collections as (collection_path varchar, specimen_type varchar, specimen_type_count bigint);

alter type stats_collections owner to darwin2;

create or replace function stats_collections_specimens (collections.id%TYPE) returns setof stats_collections language sql immutable as $$
WITH RECURSIVE collpath(name, collection_path, parent_path, id, parent_ref) AS (
  SELECT name, (name ||'/ ') as collection_path, NULL as parent_path, id, parent_ref
  FROM collections
  WHERE id = $1
  UNION
  SELECT
    collections.name,
    parentpath.collection_path ||
      CASE parentpath.collection_path
        WHEN (SELECT (name ||'/ ') FROM collections WHERE id = $1) THEN ''
        ELSE ' / '
      END || collections.name,
    parentpath.collection_path,
    collections.id,
    collections.parent_ref
  FROM collections, collpath as parentpath
  WHERE collections.parent_ref = parentpath.id
)
SELECT *
FROM
(
  SELECT * FROM 
    (SELECT collpath.collection_path as collection_path, type_group as specimen_type, COUNT(type_group) as specimen_type_count 
    FROM specimens s
    LEFT JOIN collpath AS collpath
    ON s.collection_ref = collpath.id
    WHERE (s.collection_ref = $1 OR s.collection_path LIKE '%/' || $1 || '/%')    
    GROUP BY collpath.collection_path, collpath.name, specimen_type
    ORDER BY collpath.collection_path, specimen_type) as subcollections 
  UNION ALL
  SELECT * FROM 
    (SELECT 'All'::varchar as collection_path, type_group, count(type_group) 
    FROM specimens 
    WHERE (collection_ref = $1 OR collection_path LIKE '%/' || $1 || '/%') 
    GROUP BY type_group 
    ORDER BY type_group) as allcollections

ORDER BY collection_path, specimen_type  
) as subqry
$$;

alter function stats_collections_specimens (collections.id%TYPE) owner to darwin2;

--select * from stats_collections_specimens (17);
