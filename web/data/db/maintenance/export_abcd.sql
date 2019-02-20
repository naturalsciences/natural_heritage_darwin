SET search_path TO "$user",darwin2,public;
begin;

DROP TABLE IF EXISTS public.mineralogy;
DROP TABLE IF EXISTS public.taxonomy;
DROP TABLE IF EXISTS public.catalogue_levels;
DROP TABLE IF EXISTS public.flat_abcd ;
DROP TABLE IF EXISTS public.gtu_properties;
DROP TABLE IF EXISTS public.gtu_place;
DROP TABLE IF EXISTS public.collectors;
DROP TABLE IF EXISTS public.collectors_institution;
DROP TABLE IF EXISTS public.donators;
DROP TABLE IF EXISTS public.donators_institution;
DROP TABLE IF EXISTS public.identifications_abdc;
DROP TABLE IF EXISTS public.taxon_identified;
DROP TABLE IF EXISTS public.bota_taxa_keywords;
DROP TABLE IF EXISTS public.zoo_taxa_keywords;
DROP TABLE IF EXISTS public.taxa_vernacular_name;
DROP TABLE IF EXISTS public.mineral_identified;
DROP TABLE IF EXISTS public.mineral_vernacular_name;
DROP TABLE IF EXISTS public.identifier;
DROP TABLE IF EXISTS public.identifier_instituion;
DROP TABLE IF EXISTS public.specimens_properties;
DROP TABLE IF EXISTS public.users_abc;
DROP TABLE IF EXISTS public.people_abc;
DROP TABLE IF EXISTS public.institutions_abc;
DROP TABLE IF EXISTS public.lithostratigraphy_abc;
DROP TABLE IF EXISTS public.chronostratigraphy_abc;
DROP TABLE IF EXISTS public.accomp_mineral;
DROP TABLE IF EXISTS public.darwin_metadata;
DROP TABLE IF EXISTS public.darwin_flat_bis;

DROP SEQUENCE IF EXISTS public.flat_abcd_id_seq;
DROP SEQUENCE IF EXISTS public.gtu_properties_id_seq;
DROP SEQUENCE IF EXISTS public.gtu_place_id_seq;
DROP SEQUENCE IF EXISTS public.collectors_abcd_id_seq;
DROP SEQUENCE IF EXISTS public.collectors_institution_abcd_id_seq;
DROP SEQUENCE IF EXISTS public.donators_abcd_id_seq;
DROP SEQUENCE IF EXISTS public.donators_institution_abcd_id_seq;
DROP SEQUENCE IF EXISTS public.identifications_abdc_id_seq;
DROP SEQUENCE IF EXISTS public.taxon_identified_id_seq;
DROP SEQUENCE IF EXISTS public.mineral_identified_id_seq;
DROP SEQUENCE IF EXISTS public.bota_taxa_keywords_id_seq;
DROP SEQUENCE IF EXISTS public.zoo_taxa_keywords_id_seq;
DROP SEQUENCE IF EXISTS public.taxa_vernacular_name_id_seq;
DROP SEQUENCE IF EXISTS public.mineral_vernacular_name_id_seq;
DROP SEQUENCE IF EXISTS public.identifier_abcd_id_seq;
DROP SEQUENCE IF EXISTS public.identifier_institution_id_seq;
DROP SEQUENCE IF EXISTS public.specimens_properties_id_seq;
DROP SEQUENCE IF EXISTS public.taxonomy_id_seq;
DROP SEQUENCE IF EXISTS public.darwin_flat_id_seq;
DROP SEQUENCE IF EXISTS public.catalogue_levels_id_seq;
DROP SEQUENCE IF EXISTS public.mineralogy_id_seq;

DROP FUNCTION IF EXISTS public.gettagsindexedasarray(character varying);
DROP FUNCTION IF EXISTS public.linetotagarray(text);
DROP FUNCTION IF EXISTS public.linetotagrows(text);
DROP FUNCTION IF EXISTS public.fct_remove_array_elem(anyarray,anyelement);
DROP FUNCTION IF EXISTS public.fct_remove_array_elem(anyarray,anyarray);
DROP FUNCTION IF EXISTS public.fulltoindex(character varying);


alter table darwin2.catalogue_levels DROP constraint unq_catalogue_levels;

UPDATE darwin2.catalogue_levels
SET level_name = (
CASE
WHEN level_name = 'kingdom'  THEN 'regnum'
WHEN level_name = 'super phylum'  THEN 'superphylum'
WHEN level_name = 'phylum'  THEN 'phylum'
WHEN level_name = 'sub phylum'  THEN 'subphylum'
WHEN level_name = 'super class'  THEN 'superclassis'
WHEN level_name = 'class'  THEN 'classis'
WHEN level_name = 'sub class'  THEN 'subclassis'
WHEN level_name = 'super order'  THEN 'superordo'
WHEN level_name = 'order'  THEN 'ordo'
WHEN level_name = 'sub order'  THEN 'subordo'
WHEN level_name = 'super family'  THEN 'superfamilia'
WHEN level_name = 'family'  THEN 'familia'
WHEN level_name = 'sub family'  THEN 'subfamilia'
WHEN level_name = 'tribe'  THEN 'tribus'
WHEN level_name = 'genus'  THEN 'genusgroup'
ELSE 'unranked'
END)
;

delete from specimen_collecting_methods where specimen_ref in (select id from specimens where collection_is_public = false);
delete from specimen_collecting_tools where specimen_ref in (select id from specimens where collection_is_public = false);


SET SESSION session_replication_role = replica;

delete from template_table_record_ref where referenced_relation='specimens' and record_id in (select id from specimens where collection_is_public = false);
DELETE FROM specimens where collection_is_public = false;


SET SESSION session_replication_role = origin;

create sequence public.flat_abcd_id_seq;
CREATE TABLE public.flat_abcd as
(
  SELECT
  nextval('public.flat_abcd_id_seq') as id,
  s.id as flat_ref,

  CASE WHEN s.category='observation' THEN null::text ELSE s.acquisition_category END  as acquisition_category,
  CASE WHEN s.category='observation' THEN null::integer ELSE s.acquisition_date_mask END  as acquisition_date_mask,
  CASE WHEN s.category='observation' THEN null::date ELSE CASE WHEN s.acquisition_date_mask = 0 THEN null::date ELSE s.acquisition_date END END as acquisition_date ,

  'http://darwin.naturalsciences.be/search/view/id/' || s.id as unit_url,

  CASE WHEN type = 'specimen' THEN null ELSE type END as simple_type,

  i_col.formated_name as collection_institution_name,
  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country
    FROM darwin2.people_addresses
    where person_user_ref = coll.institution_ref ORDER BY tag like '%pref%'LIMIT 1) as collection_institution_address,
  ( SELECT entry
    FROM darwin2.people_comm
    where person_user_ref = coll.institution_ref and comm_type='e-mail' ORDER BY tag like '%pref%' LIMIT 1) as collection_institution_email,
  p_col.formated_name as collection_main_manager_name,
  p_col.formated_name_indexed as collection_main_manager_sort_name,
  p_col.family_name as collection_main_manager_inherited_name,
  p_col.given_name as collection_main_manager_given_name,
  p_col.title as collection_main_manager_title,

-- GTU

  CASE WHEN station_visible = true THEN gtu.elevation ELSE null::double precision END as gtu_altitude,
  CASE WHEN station_visible = true THEN gtu.elevation_accuracy ELSE null::double precision END as gtu_altitude_accuracy,
  CASE WHEN station_visible = true THEN gtu.latitude ELSE null::double precision END as gtu_latitude,
  CASE WHEN station_visible = true THEN gtu.longitude ELSE null::double precision END as gtu_longitude,
  CASE WHEN station_visible = true THEN gtu.lat_long_accuracy ELSE null::double precision END as gtu_lat_long_accuracy,
  CASE WHEN gtu.gtu_from_date_mask = 0 THEN null::timestamp ELSE gtu.gtu_from_date END as gtu_from_date,
  CASE WHEN gtu.gtu_to_date_mask = 0 THEN null::timestamp ELSE gtu.gtu_to_date END as gtu_to_date,

  (select lineToTagRows(tag_value)
   FROM darwin2.tag_groups taggr
   WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country' limit 1) AS gtu_country,

  (select method from darwin2.specimen_collecting_methods sm
        INNER JOIN darwin2.collecting_methods cm on sm.collecting_method_ref = cm.id
        WHERE sm.specimen_ref = s.id limit 1) AS gtu_method,


  (select translate(array_to_string(array(select comment
                                          from darwin2.comments c
                                          where
                                            c.referenced_relation = 'specimens' AND c.record_id = s.id
                                         ),E'\r\n'
                                   ), chr(11) /* \n */, '')
   ) as flat_comments

  FROM darwin2.specimens s

  INNER JOIN (
               darwin2.collections coll
               INNER JOIN darwin2.people i_col ON i_col.id = coll.institution_ref
               INNER JOIN darwin2.users p_col on p_col.id = coll.main_manager_ref
             ) ON s.collection_ref = coll.id
  INNER JOIN darwin2.gtu ON s.gtu_ref = gtu.id
  -- Might be joined with Depth properties
);

ALTER TABLE public.flat_abcd ADD CONSTRAINT pk_flat_abcd PRIMARY KEY (id);

CREATE INDEX idx_flat_abcd_flat_ref ON public.flat_abcd (flat_ref);
CREATE INDEX idx_flat_abcd_gtu_country ON public.flat_abcd (gtu_country);

CREATE SEQUENCE public.gtu_properties_id_seq;

CREATE TABLE public.gtu_properties as
(
  select
    nextval('public.gtu_properties_id_seq') as id,
    s.id as specimen_id,
    (CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END) as duration,
    lower_value,
    upper_value,
    method as method ,
    property_unit as unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END as date_from,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END as date_to,
    gtu.id as gtu_ref,
    applies_to,
    property_type
  FROM
    darwin2.specimens as s
    INNER JOIN darwin2.gtu ON s.gtu_ref = gtu.id
    INNER JOIN darwin2.properties c ON referenced_relation = 'gtu' AND record_id = gtu.id
  WHERE
    /*property_type != 'geo position' and applies_to not in ('height', 'altitude', 'depth') and*/ station_visible = true

  GROUP BY
    s.id,
    gtu.id ,
    c.id
    /*,
    (CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END),
    property_method,
    property_unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END ,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / '),
    gtu.id ,
    property_qualifier*/
);

ALTER TABLE public.gtu_properties ADD CONSTRAINT pk_gtu_properties PRIMARY KEY (id);

CREATE INDEX idx_gtu_properties_specimen_id ON public.gtu_properties (specimen_id);
CREATE INDEX idx_gtu_properties_gtu_ref ON public.gtu_properties (gtu_ref);


CREATE SEQUENCE public.gtu_place_id_seq;

CREATE TABLE public.gtu_place as
(
  select
  nextval('public.gtu_place_id_seq') as id,
  s.id as specimen_id,
  tag as place

  FROM  darwin2.specimens s

  inner join darwin2.tags ON s.gtu_ref = tags.gtu_ref

  WHERE sub_group_type != 'country' and station_visible = true
);

ALTER TABLE public.gtu_place ADD CONSTRAINT pk_gtu_place_id PRIMARY KEY (id);
CREATE INDEX idx_gtu_place_specimen_id ON public.gtu_place (specimen_id);
CREATE INDEX idx_gtu_place_place ON public.gtu_place (place);


CREATE SEQUENCE public.collectors_abcd_id_seq;

CREATE TABLE public.collectors as
(
  select
    nextval('public.collectors_abcd_id_seq') as id,
    s.id as specimen_id,
    c.order_by,
    ins.formated_name as institution_formated_name,
    p.formated_name as people_formated_name,
    p.formated_name_indexed as people_sort_by,
    p.family_name as people_family_name,
    p.given_name as people_given_name,
    p.title as people_prefix
  FROM
    darwin2.specimens s
    inner join (
                 darwin2.catalogue_people as c
                 inner join
                   (
                     darwin2.people as p
                     left join
                     (
                       darwin2.people as ins
                       inner join
                       darwin2.people_relationships as pr
                       on ins.id = pr.person_2_ref and ins.is_physical = false
                     )
                     on p.id = pr.person_1_ref
                   )
                 on c.people_ref = p.id and p.is_physical = true
               )
    on s.id = c.record_id  AND c.referenced_relation = 'specimens' AND c.people_type = 'collector'
  union
  select
    nextval('public.collectors_abcd_id_seq') as id,
    s.id as specimen_id,
    c.order_by,
    p.formated_name as institution_formated_name,
    null::varchar as people_formated_name,
    null::varchar  as people_sort_by,
    null::varchar  as people_family_name,
    null::varchar  as people_given_name,
    null::varchar  as people_prefix
  FROM
    darwin2.specimens s
    inner join (
                 darwin2.catalogue_people as c
                 inner join
                 darwin2.people as p
                 on c.people_ref = p.id and p.is_physical = false
               )
    on s.id = c.record_id  AND c.referenced_relation = 'specimens' AND c.people_type = 'collector'
);

ALTER TABLE public.collectors ADD CONSTRAINT pk_collectors_abcd PRIMARY KEY (id);

CREATE INDEX idx_collectors_specimen_id ON public.collectors (specimen_id);
CREATE INDEX idx_collectors_order_by ON public.collectors (order_by);
CREATE INDEX idx_collectors_people_formated_name ON public.collectors (people_formated_name) where people_formated_name is not null;
CREATE INDEX idx_collectors_institution_formated_name ON public.collectors (institution_formated_name) where institution_formated_name is not null;

/*CREATE SEQUENCE public.donators_abcd_id_seq;

CREATE TABLE public.donators as
(
  select
    nextval('public.donators_abcd_id_seq') as id,
    s.id as specimen_id,
    d.order_by,
    ins.formated_name as institution_formated_name,
    p.formated_name as people_formated_name,
    p.formated_name_indexed as people_sort_by,
    p.family_name as people_family_name,
    p.given_name as people_given_name,
    p.title as people_prefix
  FROM
    darwin2.specimens s
    inner join (
                 darwin2.catalogue_people as d
                 inner join
                   (
                     darwin2.people as p
                     left join
                     (
                       darwin2.people as ins
                       inner join
                       darwin2.people_relationships as pr
                       on ins.id = pr.person_2_ref and ins.is_physical = false
                     )
                     on p.id = pr.person_1_ref
                   )
                 on d.people_ref = p.id and p.is_physical = true
               )
    on s.id = d.record_id  AND d.referenced_relation = 'specimens' AND d.people_type = 'donator'
  union
  select
    nextval('public.donators_abcd_id_seq') as id,
    s.id as specimen_id,
    d.order_by,
    p.formated_name as institution_formated_name,
    null::varchar as people_formated_name,
    null::varchar  as people_sort_by,
    null::varchar  as people_family_name,
    null::varchar  as people_given_name,
    null::varchar  as people_prefix
  FROM
    darwin2.specimens s
    inner join (
                 darwin2.catalogue_people as d
                 inner join
                 darwin2.people as p
                 on d.people_ref = p.id and p.is_physical = false
               )
    on s.id = d.record_id  AND d.referenced_relation = 'specimens' AND d.people_type = 'donator'
);

ALTER TABLE public.donators ADD CONSTRAINT pk_donators_abcd PRIMARY KEY (id);

CREATE INDEX idx_donators_specimen_id ON public.donators (specimen_id);
CREATE INDEX idx_donators_order_by ON public.donators (order_by);
CREATE INDEX idx_donators_people_formated_name ON public.donators (people_formated_name) where people_formated_name is not null;
CREATE INDEX idx_donators_institution_formated_name ON public.donators (institution_formated_name) where institution_formated_name is not null;
*/
create sequence public.identifications_abdc_id_seq;

CREATE TABLE public.identifications_abdc as
(
  select
  nextval('public.identifications_abdc_id_seq') as id,
  s.id as specimen_id,
  CASE WHEN notion_date_mask = 0 THEN current_timestamp ELSE notion_date END as notion_date,
  determination_status,
  false as is_current,
  notion_concerned,
  c.id as old_identification_id
  FROM  darwin2.specimens s
  INNER JOIN darwin2.identifications as c ON s.id = c.record_id  AND c.referenced_relation = 'specimens'

);

ALTER TABLE public.identifications_abdc ADD CONSTRAINT pk_identifications_abcd PRIMARY KEY (id);

create sequence public.taxon_identified_id_seq;

CREATE TABLE public.taxon_identified as
(
  SELECT DISTINCT ON (i.id, c.value_defined)
    nextval('public.taxon_identified_id_seq') as id,
    i.id as identification_ref,
    c.value_defined as taxon_name,
    CASE WHEN c.value_defined = s.taxon_name THEN s.taxon_ref ELSE null::integer END as taxon_ref,
    CASE WHEN c.value_defined = s.taxon_name THEN s.taxon_parent_ref ELSE null::integer END as taxon_parent_ref,
    CASE WHEN c.value_defined = s.taxon_name THEN s.taxon_level_name ELSE null::varchar END as taxon_level_name,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='AuthorTeam' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141538/%')
     LIMIT 1
    ) as AuthorTeam,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='AuthorTeamParenthesis' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141538/%')
     LIMIT 1
    ) as AuthorTeamParenthesis,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='AuthorTeamAndYear' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141541/%')
     LIMIT 1
    ) as AuthorTeamAndYear,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='AuthorTeamOriginalAndYear' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as AuthorTeamOriginalAndYear,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='AuthorTeamParenthesisAndYear' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as AuthorTeamParenthesisAndYear,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='CombinationAuthorTeamAndYear' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as CombinationAuthorTeamAndYear,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='CultivarGroupName' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141538/%')
     LIMIT 1
    ) as CultivarGroupName,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='CultivarName' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141538/%')
     LIMIT 1
    ) as CultivarName,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='GenusOrMonomial' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141538/%')
     LIMIT 1
    ) as BotanyGenusOrMonomial,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='GenusOrMonomial' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as ZooGenusOrMonomial,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='GenusOrMonomial' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141541/%')
     LIMIT 1
    ) as BacterialGenusOrMonomial,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='Subgenus' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as ZooSubgenus,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='Subgenus' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141541/%')
     LIMIT 1
    ) as BacterialSubgenus,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='FirstEpithet' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141538/%')
     LIMIT 1
    ) as FirstEpithet,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='SpeciesEpithet' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as ZooSpeciesEpithet,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='SpeciesEpithet' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141541/%')
     LIMIT 1
    ) as BacterialSpeciesEpithet,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='InfraspecificEpithet' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141538/%')
     LIMIT 1
    ) as InfraspecificEpithet,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='Breed' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as Breed,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='SubspeciesEpithet' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as ZooSubspeciesEpithet,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='SubspeciesEpithet' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141541/%')
     LIMIT 1
    ) as BacterialSubspeciesEpithet,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='SubgenusAuthorAndYear' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/141541/%')
     LIMIT 1
    ) as SubgenusAuthorAndYear,
    (SELECT keyword FROM darwin2.classification_keywords where
          referenced_relation = 'taxonomy' and record_id = s.taxon_ref AND keyword_type='NamedIndividual' AND CASE WHEN c.value_defined = s.taxon_name THEN true ELSE false END AND
          exists (select 1 from taxonomy where id = s.taxon_ref and path like '/-1/1/%')
     LIMIT 1
    ) as NamedIndividual
  FROM
    public.identifications_abdc i
    INNER JOIN darwin2.identifications as c ON i.old_identification_id = c.id
    INNER JOIN darwin2.specimens s ON  s.id = i.specimen_id
    WHERE
      c.notion_concerned = 'taxonomy'
);

ALTER TABLE public.taxon_identified ADD CONSTRAINT pk_taxon_identified PRIMARY KEY (id);

create sequence public.mineral_identified_id_seq;

CREATE TABLE public.mineral_identified as
(
  SELECT
    nextval('public.mineral_identified_id_seq') as id,
    i.id as identification_ref,
    c.value_defined as mineral_name,
    CASE WHEN c.value_defined = s.mineral_name THEN s.mineral_ref ELSE null::integer END as mineral_ref,
    CASE WHEN c.value_defined = s.mineral_name THEN s.mineral_parent_ref ELSE null::integer END as mineral_parent_ref,
    CASE WHEN c.value_defined = s.mineral_name THEN s.mineral_level_name ELSE null::varchar END as mineral_level_name,
    CASE WHEN c.value_defined = s.mineral_name THEN (SELECT classification FROM darwin2.mineralogy WHERE id = s.mineral_ref) ELSE null::varchar END as mineral_classification,
    CASE WHEN c.value_defined = s.mineral_name THEN (SELECT cristal_system FROM darwin2.mineralogy WHERE id = s.mineral_ref) ELSE null::varchar END as mineral_cristal_system,
    CASE WHEN c.value_defined = s.mineral_name THEN (SELECT color FROM darwin2.mineralogy WHERE id = s.mineral_ref) ELSE null::varchar END as mineral_colour
  FROM
    public.identifications_abdc i
    INNER JOIN darwin2.identifications as c ON i.old_identification_id = c.id
    INNER JOIN darwin2.specimens s ON  s.id = i.specimen_id
    WHERE
      c.notion_concerned = 'mineralogy'
);

ALTER TABLE public.mineral_identified ADD CONSTRAINT pk_mineral_identified PRIMARY KEY (id);

insert into public.identifications_abdc
(
    id,
    specimen_id,
    notion_date,
    notion_concerned,
    determination_status,
    is_current
)
(
  select
   nextval('public.identifications_abdc_id_seq') as id,
    s.id as specimen_id,
    current_timestamp as notion_date,
    'taxonomy' as notion_concerned,
    '' as determination_status,
    true as is_current
    FROM  darwin2.specimens  s
    WHERE NOT EXISTS( SELECT 1 FROM public.identifications_abdc i INNER JOIN taxon_identified ti ON i.id = ti.identification_ref WHERE i.specimen_id = s.id AND ti.taxon_ref = s.taxon_ref)
      AND s.taxon_ref is not null
      AND s.taxon_ref != 0
);

CREATE INDEX idx_taxon_identified_identification_ref ON public.taxon_identified (identification_ref);
CREATE INDEX idx_taxon_identified_taxon_ref ON public.taxon_identified (taxon_ref);
CREATE INDEX idx_taxon_identified_taxon_parent_ref ON public.taxon_identified (taxon_parent_ref);
CREATE INDEX idx_taxon_identified_taxon_name ON public.taxon_identified (taxon_name);

insert into public.identifications_abdc
(
    id,
    specimen_id,
    notion_date,
    notion_concerned,
    determination_status,
    is_current
)
(
  select
    nextval('public.identifications_abdc_id_seq') as id,
    s.id as specimen_id,
    current_timestamp as notion_date,
    'mineralogy' as notion_concerned,
    '' as determination_status,
    true as is_current
    FROM  darwin2.specimens s
    WHERE NOT EXISTS( SELECT 1 FROM public.identifications_abdc i INNER JOIN mineral_identified mi ON i.id = mi.identification_ref WHERE i.specimen_id = s.id AND mi.mineral_ref = s.mineral_ref)
      AND s.mineral_ref is not null
      AND s.mineral_ref != 0
);

insert into mineral_identified
(
    id,
    identification_ref,
    mineral_name,
    mineral_ref,
    mineral_parent_ref,
    mineral_level_name,
    mineral_classification,
    mineral_cristal_system,
    mineral_colour
)
(
  select distinct on (i.id, s.mineral_name)
    nextval('public.mineral_identified_id_seq') as id,
    i.id as identification_ref,
    s.mineral_name as mineral_name,
    s.mineral_ref as mineral_ref,
    s.mineral_parent_ref as mineral_parent_ref,
    s.mineral_level_name as mineral_level_name,
    (SELECT classification FROM darwin2.mineralogy WHERE id = s.mineral_ref) as mineral_classification,
    (SELECT cristal_system FROM darwin2.mineralogy WHERE id = s.mineral_ref) as mineral_cristal_system,
    (SELECT color FROM darwin2.mineralogy WHERE id = s.mineral_ref) as mineral_colour
    FROM  public.identifications_abdc i
    INNER JOIN darwin2.specimens  s on i.specimen_id = s.id
    WHERE i.is_current = true
      AND i.notion_concerned = 'mineralogy'
);

CREATE INDEX idx_mineral_identified_identification_ref ON public.mineral_identified (identification_ref);
CREATE INDEX idx_mineral_identified_mineral_ref ON public.mineral_identified (mineral_ref);
CREATE INDEX idx_mineral_identified_mineral_name ON public.mineral_identified (mineral_name);

CREATE INDEX idx_identifications_abdc_specimen_id ON public.identifications_abdc (specimen_id);

ALTER TABLE darwin2.taxonomy ALTER COLUMN parent_ref DROP NOT NULL;

CREATE SEQUENCE public.identifier_abcd_id_seq;

CREATE TABLE public.identifier as
(
  select
    nextval('public.identifier_abcd_id_seq') as id,
    i.id as identification_ref,
    c.people_ref as people_ref,
    p.title as title,
    p.given_name as given_name,
    p.family_name as inherited_name,
    p.formated_name as full_name,
    p.formated_name_indexed as sorting_name,
    ins.formated_name as institution_formated_name
  FROM
    public.identifications_abdc i
    INNER JOIN darwin2.catalogue_people c on i.old_identification_id = c.record_id AND c.referenced_relation='identifications'
    INNER JOIN
    (
      darwin2.people p
      left join
      (
        darwin2.people as ins
        inner join
        darwin2.people_relationships as pr
        on ins.id = pr.person_2_ref and ins.is_physical = false
      )
      on p.id = pr.person_1_ref
    ) on p.id = c.people_ref and p.is_physical = true
);

ALTER TABLE public.identifier ADD CONSTRAINT pk_identifier PRIMARY KEY (id);

CREATE INDEX idx_identifier_identification_ref ON public.identifier (identification_ref);
CREATE INDEX idx_identifier_people_ref ON public.identifier (people_ref);

CREATE SEQUENCE public.specimens_properties_id_seq;

CREATE TABLE public.specimens_properties as
(
  select
    nextval('public.specimens_properties_id_seq') as id,
    s.id as specimen_id,
    ( CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END) as duration,
    lower_value,
    upper_value,
    method as method ,
    property_unit as unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END as date_from,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END as date_to,
    property_type,
    applies_to

  FROM
    darwin2.specimens as s
    INNER JOIN darwin2.properties c ON referenced_relation = 'specimens' AND record_id = s.id

  GROUP BY
    s.id,
    c.id,
    ( CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END),
    method,
    property_unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END ,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END ,
    property_type,
    applies_to
);

ALTER TABLE public.specimens_properties ADD CONSTRAINT pk_specimens_properties PRIMARY KEY (id);

CREATE INDEX idx_specimens_properties_specimen_id ON public.specimens_properties (specimen_id);

CREATE TABLE public.lithostratigraphy_abc as
(
  SELECT * ,
  ( SELECT name from darwin2.lithostratigraphy l2  WHERE level_ref = 64  AND l1.path like '%/' || l2.id || '/%' ) AS group,
  ( SELECT name from darwin2.lithostratigraphy l2  WHERE level_ref = 65  AND l1.path like '%/' || l2.id || '/%' ) as formation,
  ( SELECT name from darwin2.lithostratigraphy l2  WHERE level_ref = 66  AND l1.path like '%/' || l2.id || '/%' ) as member

  from darwin2.lithostratigraphy l1

);

ALTER TABLE public.lithostratigraphy_abc ADD CONSTRAINT pk_litho_abc PRIMARY KEY (id);

CREATE TABLE public.chronostratigraphy_abc as
(
  SELECT * ,
  ( SELECT name from darwin2.chronostratigraphy c2  WHERE level_ref = 55  AND c1.path like '%/' || c2.id || '/%' ) AS eon,
  ( SELECT name from darwin2.chronostratigraphy c2  WHERE level_ref = 56  AND c1.path like '%/' || c2.id || '/%' ) as era,
  ( SELECT name from darwin2.chronostratigraphy c2  WHERE level_ref = 57  AND c1.path like '%/' || c2.id || '/%' ) as subera,
  ( SELECT name from darwin2.chronostratigraphy c2  WHERE level_ref = 58  AND c1.path like '%/' || c2.id || '/%' ) as system,
  ( SELECT name from darwin2.chronostratigraphy c2  WHERE level_ref = 59  AND c1.path like '%/' || c2.id || '/%' ) as serie,
  ( SELECT name from darwin2.chronostratigraphy c2  WHERE level_ref = 60  AND c1.path like '%/' || c2.id || '/%' ) as stage,
  ( SELECT name from darwin2.chronostratigraphy c2  WHERE level_ref = 61  AND c1.path like '%/' || c2.id || '/%' ) as substage
  from darwin2.chronostratigraphy c1

);

ALTER TABLE public.chronostratigraphy_abc ADD CONSTRAINT pk_chrono_abc PRIMARY KEY (id);

CREATE TABLE public.accomp_mineral AS
(
  SELECT
    r.id,
    s.id as specimen_id,
    m.classification as classification,
    m.name as mineral_name
   FROM
    darwin2.specimens s
    INNER JOIN darwin2.specimens_relationships r on s.id = r.specimen_ref
    INNER JOIN darwin2.mineralogy m ON r.mineral_ref = m.id
    WHERE unit_type ='mineralogy' and relationship_type ='combination'

);

ALTER TABLE public.accomp_mineral ADD CONSTRAINT pk_accomp_mineral_abc PRIMARY KEY (id);

CREATE TABLE public.darwin_metadata AS
(
  SELECT
    1 as id,
    'RBINS'::text as metadata_owner_abbrev,
    'Royal Belgian Institute of Natural Sciences'::text as metadata_owner_name,
    'Rue Vautier straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgïe'::text as metadata_owner_address,
    'darwin-ict@naturalsciences.be'::text as metadata_owner_email,
    'http://darwin.naturalsciences.be'::text as metadata_owner_url,
    'http://www.naturalsciences.be/layout_images/logo'::text as metadata_owner_logo_uri,
    'Rue Vautier straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgïe'::text as content_contact_address,
    'collections@naturalsciences.be'::text as content_contact_email,
    'RBINS contact'::text as content_contact_name,
    'EN'::text as metadata_representation_language,
    'RBINS collections'::text as metadata_representation_title,
    E'The Royal Belgian Institute of Natural Sciences houses a precious collection of zoological, anthropological, paleontological, mineralogical and geological materials and data.
      The renowned Iguanodons from Bernissart, ambassadors of the Belgian science institute in Brussels, represent a natural history collection currently estimated to hold over 37 million specimens.\r\n
      The roots of the present day collection reach far back in history.
      It evolved from the Natural History collection of Karel of Lotharingen, governor of The Netherlands (1712-1780) and was part of didactic materials owned by the Central School of the City of Brussels.
      After the independence of Belgium, the City of Brussels donated the collection to the Belgian Government and became part of the autonomous Royal Natural History Museum in 1846,
      known as the Royal Belgian Institute of Natural Sciences since 1948.
      Fieldwork by researchers and collaborators, in Belgium and abroad, donations and purchases have been expanding the assets ever since.\r\n
      Data presented here are coming from the darwin database, the collection management tool of the RBINS.
      Today, the darwin database manages information on about 350.000 specimens stored in the institute\'s depositories.
      This number rises on a daily basis thanks to the continued efforts of curators and their adjuncts that are responsible for maintaining the stored specimens and information.
      Our online database provides information about the collections of the Vertebrates, Invertebrates, Entomology and Paleobotany.
      The application will soon be expanded with paleontozoological data.\r\n
      The Department of Geology and the Department of Marine Ecosystems provide information on different systems.
      More information on these departments can be found on www.sciencesnaturelles.be/institute/structure/geology/gsb_website and www.mumm.ac.be\r\n
      The corner stone of the darwin database is the specimen and the information about its origin and its status.
      Although the status of the specimens sollow the current regulations of the International Code on Zoological Nomenclature other status specifications not treated by the ICZN regulations
      (eg. topotype) have been maintained as supplementary information about the specimen(s) in question.\r\n
      Enjoy your virtual visit through our collections!'::text as metadata_representation_details,
    current_timestamp as metadata_revision_date,
    'Rue Vautier straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgïe'::text as content_technical_contact_address,
    'darwin-ict@naturalsciences.be'::text as content_technical_contact_email,
    'RBINS contact'::text as content_technical_contact_name,
    E'All data given access here are the sole property of the Royal Belgian Institute for Natural Sciences (RBINS) and are protected by the laws of copyright.\r\n The reuse of data, for any purpose whatsoever, is subject to prior authorization given by the Royal Belgian Institute for Natural Sciences (RBINS).\r\n For more informations, comments or details on the above lines, please contact the Royal Belgian Institute for Natural Sciences (RBINS).'::text as IPRCopyright
);

ALTER TABLE darwin2.template_classifications SET SCHEMA public;
ALTER TABLE public.template_classifications OWNER TO postgres;

CREATE TABLE public.multimedia AS
(
select *, 'https://darwin.naturalsciences.be/multimedia/preview/id/' || id as url
 from darwin2.multimedia where referenced_relation != 'specimens' or publishable=false
 );
ALTER TABLE public.multimedia ADD CONSTRAINT pk_multimedia PRIMARY KEY (id);



CREATE SEQUENCE public.parent_taxonomy_id_seq;

CREATE TABLE public.parent_taxonomy AS
(
  select nextval('public.parent_taxonomy_id_seq') as id, s.id as specimen_id, pt.child_id as child_id, pt.parent_id as parent_id, spt.name as taxon_name, spt.level_name as level_name from
  (select t.id as child_id, st.tax_parent::integer as parent_id
   from darwin2.taxonomy as t
   inner join
   (select id, regexp_split_to_table(path, '/') as tax_parent from darwin2.taxonomy) as st
   on st.id = t.id
   where st.tax_parent != ''
  ) as pt
  inner join
  (select taxonomy.id, name, level_name from darwin2.taxonomy inner join darwin2.catalogue_levels as cl on cl.id = taxonomy.level_ref where cl.level_name != 'unranked') as spt
  on spt.id = pt.parent_id
  inner join
  specimens as s
  on s.taxon_ref = pt.child_id
);

ALTER TABLE specimens SET SCHEMA public;
ALTER TABLE public.specimens OWNER TO postgres;
ALTER TABLE specimens OWNER TO postgres;
REVOKE ALL ON specimens FROM darwin2;
REVOKE ALL ON specimens FROM cebmpad;
REVOKE ALL ON specimens_id_seq FROM cebmpad;

ALTER TABLE public.parent_taxonomy ADD CONSTRAINT pk_parent_taxonomy PRIMARY KEY (id);

CREATE INDEX idx_parent_taxon_specimen_id ON public.parent_taxonomy (specimen_id);
CREATE INDEX idx_parent_taxon_child_id ON public.parent_taxonomy (child_id);
CREATE INDEX idx_parent_taxon_parent_id ON public.parent_taxonomy (parent_id);

ALTER FUNCTION darwin2.gettagsindexedasarray(character varying) SET SCHEMA public;
ALTER FUNCTION darwin2.linetotagarray(text) SET SCHEMA public;
ALTER FUNCTION darwin2.linetotagrows(text) SET SCHEMA public;
ALTER FUNCTION darwin2.fct_remove_array_elem(anyarray,anyelement) SET SCHEMA public;
ALTER FUNCTION darwin2.fct_remove_array_elem(anyarray,anyarray) SET SCHEMA public;
ALTER FUNCTION darwin2.fulltoindex(character varying) SET SCHEMA public;

ALTER FUNCTION public.gettagsindexedasarray(character varying) OWNER TO postgres;
ALTER FUNCTION public.linetotagarray(text) OWNER TO postgres;
ALTER FUNCTION public.linetotagrows(text) OWNER TO postgres;
ALTER FUNCTION public.fct_remove_array_elem(anyarray,anyelement) OWNER TO postgres;
ALTER FUNCTION public.fct_remove_array_elem(anyarray,anyarray) OWNER TO postgres;
ALTER FUNCTION public.fulltoindex(character varying) OWNER TO postgres;

DROP SCHEMA IF EXISTS darwin1 CASCADE;

DROP SCHEMA IF EXISTS darwin2 CASCADE;

revoke execute on function public.fulltoindex(character varying) from darwin1;

DROP ROLE IF EXISTS darwin2;

DROP ROLE IF EXISTS darwin1;

DROP ROLE IF EXISTS cebmpad;


GRANT SELECT ON  public.flat_abcd TO d2viewer;
GRANT SELECT ON  public.gtu_properties TO d2viewer;
GRANT SELECT ON  public.gtu_place TO d2viewer;
GRANT SELECT ON  public.collectors TO d2viewer;
/*GRANT SELECT ON  public.donators TO d2viewer;*/
GRANT SELECT ON  public.identifications_abdc TO d2viewer;
GRANT SELECT ON  public.taxon_identified TO d2viewer;
GRANT SELECT ON  public.mineral_identified TO d2viewer;
GRANT SELECT ON  public.identifier TO d2viewer;
GRANT SELECT ON  public.specimens_properties TO d2viewer;
GRANT SELECT ON  public.lithostratigraphy_abc TO d2viewer;
GRANT SELECT ON  public.chronostratigraphy_abc TO d2viewer;
GRANT SELECT ON  public.accomp_mineral TO d2viewer;
GRANT SELECT ON  public.specimens TO d2viewer;
GRANT SELECT ON  public.darwin_metadata TO d2viewer;
GRANT SELECT ON  public.parent_taxonomy TO d2viewer;
GRANT SELECT ON  public.multimedia TO d2viewer;

ANALYZE public.flat_abcd;
ANALYZE public.gtu_properties;
ANALYZE public.gtu_place;
ANALYZE public.collectors;
/*ANALYZE public.donators;*/
ANALYZE public.identifications_abdc;
ANALYZE public.taxon_identified;
ANALYZE public.mineral_identified;
ANALYZE public.identifier;
ANALYZE public.specimens_properties;
ANALYZE public.lithostratigraphy_abc;
ANALYZE public.chronostratigraphy_abc;
ANALYZE public.accomp_mineral;
ANALYZE public.specimens;
ANALYZE public.parent_taxonomy;
ANALYZE public.multimedia;


commit;
-- ROLLBACK;
