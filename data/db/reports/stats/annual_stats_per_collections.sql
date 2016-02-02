DROP TYPE IF EXISTS stats_collections CASCADE;

create type stats_collections as (collection varchar, new_items bigint, updated_items bigint, new_types bigint, updated_types bigint, new_species bigint);

alter type stats_collections owner to darwin2;

create or replace function stats_collections_encoding (collections.id%TYPE, timestamp, timestamp) returns setof stats_collections language sql immutable as $$
WITH RECURSIVE collpath(name, collection_path, parent_path, id, parent_ref) AS (
  SELECT name, '/' as collection_path, NULL as parent_path, id, parent_ref
  FROM collections
  WHERE id = $1
  UNION
  SELECT
    collections.name,
    parentpath.collection_path ||
      CASE parentpath.collection_path
        WHEN '/' THEN ''
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
  SELECT collection_path as collection,
       (select count(s.id)
        from users_tracking as ut inner join
        specimens as s
        on ut.referenced_relation = 'specimens'
        and ut.action = 'insert'
        and ut.record_id = s.id
        and modification_date_time between $2 and $3
        where s.collection_ref = collpath.id
        ) as new_items,

        (select count(distinct s.id)
        from users_tracking as ut inner join
        specimens as s
        on ut.referenced_relation = 'specimens'
        and ut.action = 'update'
        and ut.record_id = s.id
        and modification_date_time between $2 and $3
        where s.collection_ref = collpath.id
        ) as updated_items,

        (select count(s.id)
        from users_tracking as ut inner join
            specimens as s
        on ut.referenced_relation = 'specimens'
        and ut.action = 'insert'
        and ut.record_id = s.id
        and modification_date_time between $2 and $3
        where s.collection_ref = collpath.id
        AND s.type != 'specimen'
        ) as new_types,

        (select count(s.id)
        from users_tracking as ut inner join
            specimens as s
        on ut.referenced_relation = 'specimens'
        and ut.action = 'insert'
        and ut.record_id = s.id
        and modification_date_time between $2 and $3
        where s.collection_ref = collpath.id
        AND s.type != 'specimen'
        ) as updated_types,

        (select count(distinct tax.id)
        from
        (users_tracking as ut inner join taxonomy as tax
          on ut.referenced_relation = 'taxonomy'
          and ut.action = 'insert'
          and ut.record_id = tax.id
          and tax.level_ref > 47
          and ut.modification_date_time between $2 and $3
        ) inner join
        (specimens as s  inner join users_tracking as ust
          on ust.referenced_relation = 'specimens'
          and ust.action = 'insert'
          and ust.record_id = s.id
          and ust.modification_date_time between $2 and $3
        ) on s.taxon_ref = tax.id
        where s.collection_ref = collpath.id
        ) as new_species

  FROM collpath
  ORDER BY collection_path
) as subqry
WHERE subqry.new_species != 0
  OR  subqry.new_items != 0
  OR  subqry.updated_items != 0
  OR  subqry.new_types != 0
  OR  subqry.updated_types != 0
$$;


alter function stats_collections_encoding (collections.id%TYPE, timestamp, timestamp) owner to darwin2;

grant execute on function stats_collections_encoding (collections.id%TYPE, timestamp, timestamp) to d2viewer;
