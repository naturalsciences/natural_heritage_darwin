DROP TYPE IF EXISTS taxo_collections, taxo_collections_no_fam, taxo_collections_total CASCADE;

create type taxo_collections as (family varchar, family_cnt bigint, genus varchar, genus_cnt bigint, species varchar, species_cnt bigint);
create type taxo_collections_no_fam as (count bigint);
create type taxo_collections_total as (count bigint);

alter type taxo_collections owner to darwin2;
alter type taxo_collections_no_fam owner to darwin2;
alter type taxo_collections_total owner to darwin2;

create or replace function taxo_collections_count (collections.id%TYPE) returns setof taxo_collections language sql immutable as $$

(SELECT DISTINCT r.family_name as family, cnt_fam.number_family as family_cnt, 
r.genus_name as genus, cnt_gen.number_genus as genus_cnt, 
r.species_name as species, cnt_spec.number_species as species_cnt 
FROM 
  (SELECT f.family_name, g.genus_name, s.species_name 
  FROM 
    (SELECT taxon_name, (taxon_path || taxon_ref || '/') as taxon_full_path
    FROM specimens s
    WHERE collection_ref = ANY((SELECT (array(SELECT c.id FROM collections c 
                WHERE c.id = $1
                OR c.path LIKE ('%/' || $1 || '/%'))) as collections)::int[])
    /*LIMIT 100*/) AS spec
    LEFT JOIN
      (SELECT id as id_family, name as family_name FROM taxonomy WHERE level_ref=34) AS f
      ON spec.taxon_full_path LIKE '%/' || f.id_family || '/%'
    LEFT JOIN
      (SELECT id as id_genus, name as genus_name FROM taxonomy WHERE level_ref=41) AS g
      ON spec.taxon_full_path LIKE '%/' || g.id_genus || '/%'
    LEFT JOIN
      (SELECT id as id_species, name as species_name FROM taxonomy WHERE level_ref=48) AS s
      ON spec.taxon_full_path LIKE '%/' || s.id_species || '/%') AS r
LEFT JOIN
        (SELECT count(family_name) as number_family, family_name
        FROM 
          (SELECT f.family_name, g.genus_name, s.species_name 
          FROM 
            (SELECT taxon_name, (taxon_path || taxon_ref || '/') as taxon_full_path
            FROM specimens s
            WHERE collection_ref = ANY((SELECT (array(SELECT c.id FROM collections c 
                WHERE c.id = $1
                OR c.path LIKE ('%/' || $1 || '/%'))) as collections)::int[])
            /*LIMIT 100*/) AS spec
          LEFT JOIN
            (SELECT id as id_family, name as family_name FROM taxonomy WHERE level_ref=34) AS f
          ON spec.taxon_full_path LIKE '%/' || f.id_family || '/%'
          LEFT JOIN
            (SELECT id as id_genus, name as genus_name FROM taxonomy WHERE level_ref=41) AS g
          ON spec.taxon_full_path LIKE '%/' || g.id_genus || '/%'
          LEFT JOIN
            (SELECT id as id_species, name as species_name FROM taxonomy WHERE level_ref=48) AS s
          ON spec.taxon_full_path LIKE '%/' || s.id_species || '/%') as r
        GROUP BY family_name) AS cnt_fam
ON cnt_fam.family_name = r.family_name
LEFT JOIN
        (SELECT count(genus_name) as number_genus, genus_name
        FROM 
          (SELECT f.family_name, g.genus_name, s.species_name 
          FROM 
            (SELECT taxon_name, (taxon_path || taxon_ref || '/') as taxon_full_path
            FROM specimens s
            WHERE collection_ref = ANY((SELECT (array(SELECT c.id FROM collections c 
                WHERE c.id = $1
                OR c.path LIKE ('%/' || $1 || '/%'))) as collections)::int[])
            /*LIMIT 100*/) AS spec
          LEFT JOIN
            (SELECT id as id_family, name as family_name FROM taxonomy WHERE level_ref=34) AS f
          ON spec.taxon_full_path LIKE '%/' || f.id_family || '/%'
          LEFT JOIN
            (SELECT id as id_genus, name as genus_name FROM taxonomy WHERE level_ref=41) AS g
          ON spec.taxon_full_path LIKE '%/' || g.id_genus || '/%'
          LEFT JOIN
            (SELECT id as id_species, name as species_name FROM taxonomy WHERE level_ref=48) AS s
          ON spec.taxon_full_path LIKE '%/' || s.id_species || '/%') as r
        GROUP BY genus_name) AS cnt_gen
ON cnt_gen.genus_name = r.genus_name
LEFT JOIN
        (SELECT count(species_name) as number_species, species_name
        FROM 
          (SELECT f.family_name, g.genus_name, s.species_name 
          FROM 
            (SELECT taxon_name, (taxon_path || taxon_ref || '/') as taxon_full_path
            FROM specimens s
            WHERE collection_ref = ANY((SELECT (array(SELECT c.id FROM collections c 
                WHERE c.id = $1
                OR c.path LIKE ('%/' || $1 || '/%'))) as collections)::int[])
            /*LIMIT 100*/) AS spec
          LEFT JOIN
            (SELECT id as id_family, name as family_name FROM taxonomy WHERE level_ref=34) AS f
          ON spec.taxon_full_path LIKE '%/' || f.id_family || '/%'
          LEFT JOIN
            (SELECT id as id_genus, name as genus_name FROM taxonomy WHERE level_ref=41) AS g
          ON spec.taxon_full_path LIKE '%/' || g.id_genus || '/%'
          LEFT JOIN
            (SELECT id as id_species, name as species_name FROM taxonomy WHERE level_ref=48) AS s
          ON spec.taxon_full_path LIKE '%/' || s.id_species || '/%') as r
        GROUP BY species_name) AS cnt_spec
ON cnt_spec.species_name = r.species_name
ORDER BY r.family_name, r.genus_name, r.species_name)
$$;

alter function taxo_collections_count (collections.id%TYPE) owner to darwin2;
grant execute on function taxo_collections_count (collections.id%TYPE) to d2viewer;

create or replace function taxo_collections_no_fam_count (collections.id%TYPE) returns setof taxo_collections_no_fam language sql immutable as $$
SELECT count(*)
FROM specimens s
WHERE taxon_level_ref < 34 and (collection_ref=$1 or collection_path like '%/' || $1 || '/%')
$$;

alter function taxo_collections_no_fam_count (collections.id%TYPE) owner to darwin2;
grant execute on function taxo_collections_no_fam_count (collections.id%TYPE) to d2viewer;

create or replace function taxo_collections_total_count (collections.id%TYPE) returns setof taxo_collections_total language sql immutable as $$
SELECT count(*)
FROM specimens s
WHERE collection_ref=$1 or collection_path like '%/' || $1 || '/%'
$$;

alter function taxo_collections_total_count (collections.id%TYPE) owner to darwin2;
grant execute on function taxo_collections_total_count (collections.id%TYPE) to d2viewer;

--SELECT * FROM taxo_collections_count(7);
--SELECT * FROM taxo_collections_no_fam_count(7);
--SELECT * FROM taxo_collections_total_count(7);
