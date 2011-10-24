SET search_path TO "$user",darwin2,public;

DROP TABLE IF EXISTS public.darwin_flat;
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
DROP TABLE IF EXISTS public.flat_properties;
DROP TABLE IF EXISTS public.users_abc;
DROP TABLE IF EXISTS public.people_abc;
DROP TABLE IF EXISTS public.institutions_abc;
DROP TABLE IF EXISTS public.lithostratigraphy_abc;
DROP TABLE IF EXISTS public.accomp_mineral;
DROP TABLE IF EXISTS public.darwin_metadata;

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
DROP SEQUENCE IF EXISTS public.flat_properties_id_seq;
DROP SEQUENCE IF EXISTS public.taxonomy_id_seq;
DROP SEQUENCE IF EXISTS public.darwin_flat_id_seq;
DROP SEQUENCE IF EXISTS public.catalogue_levels_id_seq;
DROP SEQUENCE IF EXISTS public.mineralogy_id_seq;

DROP FUNCTION IF EXISTS public.gettagsindexedasarray(character varying);
DROP AGGREGATE IF EXISTS public.array_accum(anyelement);
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

DELETE FROM darwin2.darwin_flat where collection_is_public = false;

create sequence public.flat_abcd_id_seq;

CREATE TABLE public.flat_abcd as 
(
  SELECT 
  nextval('public.flat_abcd_id_seq') as id,
  f.id as flat_ref,
  
  CASE WHEN f.category='observation' THEN null::text ELSE f.acquisition_category END  as acquisition_category,
  CASE WHEN f.category='observation' THEN null::integer ELSE f.acquisition_date_mask END  as acquisition_date_mask,
  CASE WHEN f.category='observation' THEN null::date ELSE CASE WHEN f.acquisition_date_mask = 0 THEN null::date ELSE f.acquisition_date END END as acquisition_date ,

  'http://darwin.naturalsciences.be/search/view/id/' || f.individual_ref as unit_url,

  CASE WHEN individual_type = 'specimen' THEN null ELSE individual_type END as individual_simple_type,

  coll.institution_ref as collection_institution_ref,
  i_col.formated_name as collection_institution_name,
  coll.main_manager_ref as collection_main_manager_ref,
  p_col.formated_name as collection_main_manager_name,

-- GTU
  
  gtu.elevation as gtu_altitude,
  gtu.elevation_accuracy as gtu_altitude_accuracy,
  gtu.latitude as gtu_latitude,
  gtu.longitude as gtu_longitude,
  gtu.lat_long_accuracy as gtu_lat_long_accuracy,
  CASE WHEN gtu.gtu_from_date_mask = 0 THEN null::timestamp ELSE gtu.gtu_from_date END as gtu_from_date,
  CASE WHEN gtu.gtu_to_date_mask = 0 THEN null::timestamp ELSE gtu.gtu_to_date END as gtu_to_date,

  (select lineToTagRows(tag_value) FROM darwin2.tag_groups taggr WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country' limit 1) AS gtu_country,
  --(select  * FROM tag_groups taggr WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country' limit 1) AS gtu_country,
  (select method from darwin2.specimen_collecting_methods sm 
        INNER JOIN darwin2.collecting_methods cm on sm.collecting_method_ref = cm.id  
        WHERE sm.specimen_ref = f.spec_ref limit 1) AS gtu_method,


  ( CASE WHEN cp1.date_to_mask = 0 THEN null::timestamp ELSE cp1.date_to END - CASE WHEN cp1.date_from_mask = 0 THEN NULL::timestamp ELSE cp1.date_from END ) as depth_duration,
  ( select min(property_value) from darwin2.properties_values where property_ref = cp1.id) as depth_lowervalue,
  ( select case when max(property_value) = min(property_value) then null::text else max(property_value) end from darwin2.properties_values where property_ref = cp1.id) as depth_uppervalue, /*** only if 1? **/
  ( select avg(property_accuracy_unified) from darwin2.properties_values where property_ref = cp1.id) as depth_accuracy,
  cp1.property_method as depth_method,
  cp1.property_unit as depth_unit,
  CASE WHEN cp1.date_from_mask = 0 THEN null::timestamp ELSE cp1.date_from END as depth_date_time,

  ( CASE WHEN cp2.date_to_mask = 0 THEN null::timestamp ELSE cp2.date_to END - CASE WHEN cp2.date_from_mask = 0 THEN NULL::timestamp ELSE cp2.date_from END ) as height_duration,
  ( select min(property_value) from darwin2.properties_values where property_ref = cp2.id) as height_lowervalue,
  ( select case when max(property_value) = min(property_value) then null::text else max(property_value) end from darwin2.properties_values where property_ref = cp2.id) as height_uppervalue, /*** only if 1? **/
  ( select avg(property_accuracy_unified) from darwin2.properties_values where property_ref = cp2.id) as height_accuracy,
  cp2.property_method as height_method,
  cp2.property_unit as height_unit,
  CASE WHEN cp2.date_from_mask = 0 THEN null::timestamp ELSE cp2.date_from END as height_date_time,


  (select array_to_string(array(select comment 
                                from darwin2.comments c_flat 
                                where ( c_flat.referenced_relation = 'gtu' AND c_flat.record_id=f.gtu_ref)
                                   or ( c_flat.referenced_relation = 'specimens' AND c_flat.record_id = f.spec_ref)
                                   or ( c_flat.referenced_relation = 'specimen_individuals' AND c_flat.record_id = f.individual_ref)  
                                   or ( c_flat.referenced_relation = 'specimen_parts' AND c_flat.record_id = f.part_ref)  
                               ),E'\r\n' 
                         )) as flat_comments,

  ( select min(property_value) from darwin2.properties_values where property_ref = cp3.id) as utm_text


  FROM darwin2.darwin_flat f
  
  INNER JOIN darwin2.collections coll ON f.collection_ref = coll.id
  INNER JOIN darwin2.people i_col ON i_col.id = coll.institution_ref
  INNER JOIN darwin2.people p_col on p_col.id = coll.main_manager_ref
  INNER JOIN darwin2.gtu ON f.gtu_ref = gtu.id

  LEFT JOIN darwin2.catalogue_properties cp1 ON cp1.referenced_relation = 'gtu' AND cp1.record_id = gtu.id AND cp1.property_type = 'physical measurement' and cp1.property_qualifier = 'depth'

  LEFT JOIN darwin2.catalogue_properties cp2 ON cp2.referenced_relation = 'gtu' AND cp2.record_id = gtu.id and cp2.property_type = 'physical measurement' AND cp2.property_qualifier = 'height'

  LEFT JOIN darwin2.catalogue_properties cp3 ON cp3.referenced_relation = 'gtu' AND cp3.record_id = gtu.id and cp3.property_type = 'geo position' AND cp3.property_qualifier = 'utm'

);

ALTER TABLE public.flat_abcd ADD CONSTRAINT pk_flat_abcd PRIMARY KEY (id);

CREATE SEQUENCE public.gtu_properties_id_seq;

CREATE TABLE public.gtu_properties as
(
  select
    nextval('public.gtu_properties_id_seq') as id,
    flat.id as flat_id,
    (CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END) as duration,
    min(v.property_value) as lowervalue,
    case when max(property_value) = min(property_value) then null::text else max(property_value) end as uppervalue,
    property_method as method ,
    property_unit as unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END as date_from,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END as date_to,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / ') as parameter,
    gtu.id as gtu_ref,
    property_qualifier as qualifier,
    avg(v.property_accuracy_unified) as accuracy
  FROM 
    darwin2.darwin_flat as flat
    INNER JOIN darwin2.gtu ON flat.gtu_ref = gtu.id
    INNER JOIN darwin2.catalogue_properties c ON referenced_relation = 'gtu' AND record_id = gtu.id
    INNER JOIN darwin2.properties_values v ON property_ref = c.id 
  WHERE 
    property_type != 'geo position' and property_sub_type not in ('height', 'altitude', 'depth')

  GROUP BY 
    flat.id,
    gtu.id ,
    c.id,
    (CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END),
    property_method, 
    property_unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END ,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / '),
    gtu.id ,
    property_qualifier
);

ALTER TABLE public.gtu_properties ADD CONSTRAINT pk_gtu_properties PRIMARY KEY (id);

CREATE SEQUENCE public.gtu_place_id_seq;

CREATE TABLE public.gtu_place as
(
  select 
  nextval('public.gtu_place_id_seq') as id,
  f.id as flat_id,
  tag as place
  
  FROM  darwin2.darwin_flat f
  
  inner join darwin2.tags ON f.gtu_ref = tags.gtu_ref

  WHERE sub_group_type != 'country'
);

CREATE SEQUENCE public.collectors_abcd_id_seq;

CREATE TABLE public.collectors as
(
  select 
  nextval('public.collectors_abcd_id_seq') as id,
  f.id as flat_id,
  c.people_ref,
  c.order_by
  FROM  darwin2.darwin_flat f
  inner join darwin2.catalogue_people as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'
  WHERE 
    c.people_type = 'collector'
    AND exists (select 1 from darwin2.people p where p.id = c.people_ref and is_physical = true)
);

ALTER TABLE public.collectors ADD CONSTRAINT pk_collectors_abcd PRIMARY KEY (id);

CREATE SEQUENCE public.collectors_institution_abcd_id_seq;

CREATE TABLE public.collectors_institution as
(
  select 
  nextval('public.collectors_institution_abcd_id_seq') as id,
  f.id as flat_id,
  c.people_ref,
  c.order_by,
  p.formated_name
  FROM  darwin2.darwin_flat f
  inner join darwin2.catalogue_people as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'
  INNER JOIN darwin2.people p ON p.id = c.people_ref and is_physical = true
  WHERE 
    c.people_type = 'collector'
  
);

ALTER TABLE public.collectors_institution ADD CONSTRAINT pk_collectors_institutions_abcd PRIMARY KEY (id);

CREATE SEQUENCE public.donators_abcd_id_seq;

CREATE TABLE public.donators as
(
  select 
  nextval('public.donators_abcd_id_seq') as id,
  f.id as flat_id,
  c.people_ref,
  c.order_by
  FROM  darwin2.darwin_flat f
  inner join darwin2.catalogue_people as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'
  WHERE 
    c.people_type = 'donator'
    AND exists (select 1 from darwin2.people p where p.id = c.people_ref and is_physical = true)
);

ALTER TABLE public.donators ADD CONSTRAINT pk_donators_abcd PRIMARY KEY (id);

CREATE SEQUENCE public.donators_institution_abcd_id_seq;

CREATE TABLE public.donators_institution as
(
  select 
  nextval('public.donators_institution_abcd_id_seq') as id,
  f.id as flat_id,
  c.people_ref,
  c.order_by,
  p.formated_name
  FROM  darwin2.darwin_flat f
  inner join darwin2.catalogue_people as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'
  INNER JOIN darwin2.people p ON p.id = c.people_ref and is_physical = true
  WHERE 
    c.people_type = 'donator'
  
);

ALTER TABLE public.donators_institution ADD CONSTRAINT pk_donators_institutions_abcd PRIMARY KEY (id);

create sequence public.identifications_abdc_id_seq;

CREATE TABLE public.identifications_abdc as
(
  select
  nextval('public.identifications_abdc_id_seq') as id,
  f.id as flat_id,
  CASE WHEN notion_date_mask = 0 THEN current_timestamp ELSE notion_date END as notion_date,
  determination_status,
  false as is_current,
  notion_concerned,
  c.id as old_identification_id
  FROM  darwin2.darwin_flat f
  INNER JOIN darwin2.identifications as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'

);

ALTER TABLE public.identifications_abdc ADD CONSTRAINT pk_identifications_abcd PRIMARY KEY (id);

create sequence public.taxon_identified_id_seq;

CREATE TABLE public.taxon_identified as
(
  SELECT DISTINCT ON (i.id, c.value_defined)
    nextval('public.taxon_identified_id_seq') as id,
    i.id as identification_ref,
    c.value_defined as taxon_name,
    CASE WHEN c.value_defined = f.taxon_name THEN f.taxon_ref ELSE null::integer END as taxon_ref,
    CASE WHEN c.value_defined = f.taxon_name THEN f.taxon_parent_ref ELSE null::integer END as taxon_parent_ref
  FROM 
    public.identifications_abdc i
    INNER JOIN darwin2.identifications as c ON i.old_identification_id = c.id
    INNER JOIN darwin2.darwin_flat f ON  f.id = i.flat_id
    WHERE 
      c.notion_concerned = 'taxonomy'
);

ALTER TABLE public.taxon_identified ADD CONSTRAINT pk_taxon_identified PRIMARY KEY (id);

insert into public.identifications_abdc 
(
    id,
    flat_id,
    notion_date,
    determination_status,
    is_current
)
(
  select 
   nextval('public.identifications_abdc_id_seq') as id,
    f.id as flat_id,
    current_timestamp as notion_date,
    '' as determination_status,
    true as is_current
    FROM  darwin2.darwin_flat  f
    WHERE NOT EXISTS( SELECT 1 FROM public.identifications_abdc i WHERE taxon_ref is not null and i.flat_id = f.id)
);


insert into public.taxon_identified
(
    id,
    identification_ref,
    taxon_name,
    taxon_ref,
    taxon_parent_ref
)
(
  select DISTINCT ON (i.id, f.taxon_name)
    nextval('public.taxon_identified_id_seq') as id,
    i.id as identification_ref,
    f.taxon_name as taxon_name,
    f.taxon_ref as taxon_ref,
    f.taxon_parent_ref as taxon_parent_ref
    FROM  public.identifications_abdc i
    INNER JOIN darwin2.darwin_flat  f on i.flat_id = f.id

    WHERE i.is_current = true
      AND taxon_ref !=0 
);


ALTER TABLE darwin2.taxonomy ALTER COLUMN parent_ref DROP NOT NULL;
ALTER TABLE darwin2.darwin_flat ALTER COLUMN taxon_parent_ref DROP NOT NULL;
ALTER TABLE darwin2.darwin_flat ALTER COLUMN host_taxon_parent_ref DROP NOT NULL;

SET SESSION session_replication_role = replica;  

UPDATE darwin2.taxonomy SET parent_ref = NULL WHERE parent_ref = 0;
UPDATE darwin2.darwin_flat SET taxon_parent_ref = NULL WHERE taxon_parent_ref = 0;

SET SESSION session_replication_role = origin;  

CREATE SEQUENCE public.bota_taxa_keywords_id_seq;

CREATE TABLE public.bota_taxa_keywords AS
(
  SELECT
  nextval('public.bota_taxa_keywords_id_seq') as id,  
  ti.id as taxon_identified_ref,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='AuthorTeam' LIMIT 1) as AuthorTeam,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='AuthorTeamParenthesis' LIMIT 1) as AuthorTeamParenthesis,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='CultivarGroupName' LIMIT 1) as CultivarGroupName,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='CultivarName' LIMIT 1) as CultivarName,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='FirstEpithet' LIMIT 1) as FirstEpithet,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='GenusOrMonomial' LIMIT 1) as GenusOrMonomial,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='InfraspecificEpithet' LIMIT 1) as InfraspecificEpithet

  FROM public.taxon_identified ti
  INNER JOIN darwin2.taxonomy t on t.id = ti.taxon_ref
  
  WHERE
    t.path like '/-1/141538/%' --PLANTEA
);

ALTER TABLE public.bota_taxa_keywords ADD CONSTRAINT pk_bota_taxa_keywords PRIMARY KEY (id);

CREATE SEQUENCE public.zoo_taxa_keywords_id_seq;

CREATE TABLE public.zoo_taxa_keywords AS
(
  SELECT
  nextval('public.zoo_taxa_keywords_id_seq') as id,
  ti.id as taxon_identified_ref,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='AuthorTeamOriginalAndYear' LIMIT 1) as AuthorTeamOriginalAndYear,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='AuthorTeamParenthesisAndYear' LIMIT 1) as AuthorTeamParenthesisAndYear,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='Breed' LIMIT 1) as Breed,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='CombinationAuthorTeamAndYear' LIMIT 1) as CombinationAuthorTeamAndYear,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='GenusOrMonomial' LIMIT 1) as GenusOrMonomial,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='NamedIndividual' LIMIT 1) as NamedIndividual,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='SpeciesEpithet' LIMIT 1) as SpeciesEpithet,
  (SELECT keyword FROM darwin2.classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='Subgenus' LIMIT 1) as Subgenus

  FROM public.taxon_identified ti
  INNER JOIN darwin2.taxonomy t on t.id = ti.taxon_ref
  
  WHERE
    t.path like '/-1/1/%' --ANIMAL
);

ALTER TABLE public.zoo_taxa_keywords ADD CONSTRAINT pk_zoo_taxa_keywords PRIMARY KEY (id);

create sequence public.mineral_identified_id_seq;

CREATE TABLE public.mineral_identified as
(
  SELECT 
    nextval('public.mineral_identified_id_seq') as id,
    i.id as identification_ref,
    c.value_defined as mineral_name,
    null::integer as mineral_ref, 
    null::varchar as classification
  FROM 
    public.identifications_abdc i
    INNER JOIN darwin2.identifications as c ON i.old_identification_id = c.id
    WHERE 
    c.notion_concerned = 'mineralogy'
    AND i.is_current = FALSE
);

ALTER TABLE public.mineral_identified ADD CONSTRAINT pk_mineral_identified PRIMARY KEY (id);

insert into mineral_identified
(
    id,
    identification_ref,
    mineral_name,
    mineral_ref,
    classification
)
(
  select
    nextval('public.mineral_identified_id_seq') as id,
    i.id as identification_ref,
    m.name as mineral_name,
    f.mineral_ref as mineral_ref,
    m.classification as classification
    FROM  public.identifications_abdc i
    INNER JOIN darwin2.darwin_flat  f on i.flat_id = f.id
    INNER JOIN darwin2.mineralogy m on f.mineral_ref = m.id
    WHERE i.is_current = true
      AND f.mineral_ref !=0 
      
);

CREATE SEQUENCE public.identifier_abcd_id_seq;

CREATE TABLE public.identifier as
(
  select
    nextval('public.identifier_abcd_id_seq') as id, 
    i.id as identification_ref,
    c.people_ref
  FROM
    public.identifications_abdc i
    INNER JOIN darwin2.catalogue_people c on i.old_identification_id = c.record_id AND c.referenced_relation='identifications'
    INNER JOIN darwin2.people p on p.id = c.people_ref
  WHERE
    p.is_physical = true
    
);

ALTER TABLE public.identifier ADD CONSTRAINT pk_identifier PRIMARY KEY (id);

CREATE SEQUENCE public.identifier_institution_id_seq;

CREATE TABLE public.identifier_instituion as
(
  select 
    nextval('public.identifier_institution_id_seq') as id,
    i.id as identification_ref,
    c.people_ref
  FROM
    public.identifications_abdc i
    INNER JOIN darwin2.catalogue_people c on i.old_identification_id = c.record_id AND c.referenced_relation='identifications'
    INNER JOIN darwin2.people p on p.id = c.people_ref
  WHERE
    p.is_physical = false
    
);

ALTER TABLE public.identifier_instituion ADD CONSTRAINT pk_identifier_institution PRIMARY KEY (id);

CREATE SEQUENCE public.flat_properties_id_seq;

CREATE TABLE public.flat_properties as
(
  select
    nextval('public.flat_properties_id_seq') as id,
    flat.id as flat_id,
    ( CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END) as duration,
    min(property_value) as lowervalue,
    case when max(property_value) = min(property_value) then null::text else max(property_value) end as uppervalue,
    property_method as method ,
    property_unit as unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END as date_from,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END as date_to,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / ') as parameter

  FROM 
    darwin2.darwin_flat as flat
    INNER JOIN darwin2.catalogue_properties c ON referenced_relation = 'specimens' AND record_id = flat.spec_ref
    INNER JOIN darwin2.properties_values p ON property_ref = c.id 

  GROUP BY 
    flat.id,
    c.id,
    ( CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END), 
    property_method, 
    property_unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END ,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END ,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / ')

UNION


  select
    nextval('public.flat_properties_id_seq') as id,
    flat.id as flat_id,
    ( CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END) as duration,
    min(property_value) as lowervalue,
    case when max(property_value) = min(property_value) then null::text else max(property_value) end as uppervalue,
    property_method as method ,
    property_unit as unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END as date_from,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END as date_to,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / ') as parameter

  FROM 
    darwin2.darwin_flat as flat
    INNER JOIN darwin2.catalogue_properties c ON referenced_relation = 'specimen_individuals' AND record_id = flat.individual_ref
    INNER JOIN darwin2.properties_values p ON property_ref = c.id 
  GROUP BY 
    flat.id,
    c.id,
    ( CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END), 
    property_method, 
    property_unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END ,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / ')

UNION


  select
    nextval('public.flat_properties_id_seq') as id,
    flat.id as flat_id,
    ( CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END) as duration,
    min(property_value) as lowervalue,
    case when max(property_value) = min(property_value) then null::text else max(property_value) end as uppervalue,
    property_method as method ,
    property_unit as unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END as date_from,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END as date_to,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / ') as parameter

  FROM 
    darwin2.darwin_flat as flat
    INNER JOIN darwin2.catalogue_properties c ON referenced_relation = 'specimen_parts' AND record_id = flat.part_ref
    INNER JOIN darwin2.properties_values p ON property_ref = c.id 
  GROUP BY 
    flat.id,
    c.id,
    ( CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END - CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END), 
    property_method, 
    property_unit,
    CASE WHEN date_from_mask = 0 THEN NULL::timestamp ELSE date_from END ,
    CASE WHEN date_to_mask = 0 THEN NULL::timestamp ELSE date_to END ,
    trim(property_type  || ' / ' || lower(property_sub_type), ' / ')
);

ALTER TABLE public.flat_properties ADD CONSTRAINT pk_flat_properties PRIMARY KEY (id);

CREATE TABLE public.users_abc as 
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM darwin2.users_addresses where  person_user_ref = u.id ORDER BY tag like '%pref%'  LIMIT 1) as address,
  ( SELECT entry FROM darwin2.users_comm where  person_user_ref = u.id and comm_type='e-mail' ORDER BY tag like '%pref%' LIMIT 1) as email

  from users u
  
);


ALTER TABLE public.users_abc ADD CONSTRAINT pk_users_abc PRIMARY KEY (id);

CREATE TABLE public.people_abc as 
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM darwin2.people_addresses where  person_user_ref = p.id ORDER BY tag like '%pref%'LIMIT 1) as address,
  ( SELECT entry FROM darwin2.people_comm where person_user_ref = p.id    and comm_type='e-mail' ORDER BY tag like '%pref%' LIMIT 1) as email

  from people p
  
  WHERE is_physical = true
  
);

ALTER TABLE public.people_abc ADD CONSTRAINT pk_people_abc PRIMARY KEY (id);

CREATE TABLE public.institutions_abc as
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM darwin2.people_addresses where  person_user_ref = p.id ORDER BY tag like '%pref%' LIMIT 1) as address,
  ( SELECT entry FROM darwin2.people_comm where person_user_ref = p.id   and comm_type='e-mail' ORDER BY tag like '%pref%' LIMIT 1) as email

  from people p
  
  WHERE is_physical = false
  
);

ALTER TABLE public.institutions_abc ADD CONSTRAINT pk_institutions_abc PRIMARY KEY (id);

CREATE TABLE public.lithostratigraphy_abc as 
(
  SELECT * ,
  ( SELECT name from darwin2.lithostratigraphy l2  WHERE level_ref = 64  AND l1.path like '%/' || l2.id || '/%' ) AS group,
  ( SELECT name from darwin2.lithostratigraphy l2  WHERE level_ref = 65  AND l1.path like '%/' || l2.id || '/%' ) as formation,
  ( SELECT name from darwin2.lithostratigraphy l2  WHERE level_ref = 66  AND l1.path like '%/' || l2.id || '/%' ) as member
 
  from darwin2.lithostratigraphy l1
    
);

ALTER TABLE public.lithostratigraphy_abc ADD CONSTRAINT pk_litho_abc PRIMARY KEY (id);

CREATE TABLE public.accomp_mineral AS
(


  SELECT
    a.id,
    f.id as flat_id,
    m.classification as classification,
    m.name as mineral_name
   FROM 
    darwin2.darwin_flat f
    INNER JOIN darwin2.specimens_accompanying a ON f.spec_ref = a.specimen_ref
    INNER JOIN darwin2.mineralogy m ON a.mineral_ref = m.id
    WHERE accompanying_type ='mineral'

);

ALTER TABLE public.accomp_mineral ADD CONSTRAINT pk_accomp_mineral_abc PRIMARY KEY (id);

CREATE TABLE public.darwin_metadata AS
(
  SELECT
    1 as id,
    'RBINS'::text as metadata_owner_abbrev,
    'Royal Belgian Institute of Natural Sciences'::text as metadata_owner_name,
    'Rue Vautier straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgïe'::text as metadata_owner_address,
    'collections@naturalsciences.be'::text as metadata_owner_email,
    'http://www.naturalsciences.be'::text as metadata_owner_url,
    'http://www.naturalsciences.be/layout_images/logo'::text as metadata_owner_logo_uri,
    'Rue Vautier straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgïe'::text as content_contact_address,
    'collections@naturalsciences.be'::text as content_contact_email,
    'RBINS contact'::text as content_contact_name,
    'EN'::text as metadata_representation_language,
    'RBINS collections'::text as metadata_representation_title,
    E'The Royal Belgian Institute of Natural Sciences houses a precious collection of zoological, anthropological, paleontological, mineralogical and geological materials and data. The renowned Iguanodons from Bernissart, ambassadors of the Belgian science institute in Brussels, represent a natural history collection currently estimated to hold over 37 million specimens.\r\nThe roots of the present day collection reach far back in history. It evolved from the Natural History collection of Karel of Lotharingen, governor of The Netherlands (1712-1780) and was part of didactic materials owned by the Central School of the City of Brussels. After the independence of Belgium, the City of Brussels donated the collection to the Belgian Government and became part of the autonomous Royal Natural History Museum in 1846, known as the Royal Belgian Institute of Natural Sciences since 1948. Fieldwork by researchers and collaborators, in Belgium and abroad, donations and purchases have been expanding the assets ever since.\r\nData presented here are coming from the darwin database, the collection management tool of the RBINS. Today, the darwin database manages information on about 350.000 specimens stored in the institute\'s depositories. This number rises on a daily basis thanks to the continued efforts of curators and their adjuncts that are responsible for maintaining the stored specimens and information. Our online database provides information about the collections of the Vertebrates, Invertebrates, Entomology and Paleobotany. The application will soon be expanded with paleontozoological data.\r\nThe Department of Geology and the Department of Marine Ecosystems provide information on different systems. More information on these departments can be found on www.sciencesnaturelles.be/institute/structure/geology/gsb_website And www.mumm.ac.be\r\nThe corner stone of the darwin database is the specimen and the information about its origin and its status. Although the status of the specimens follow the current regulations of the International Code on Zoological Nomenclature other status specifications not treated by the ICZN regulations (eg. topotype) have been maintained as supplementary information about the specimen(s) in question.\r\nEnjoy your virtual visit through our collections!'::text as metadata_representation_details,
    current_timestamp as metadata_revision_date,
    'Rue Vautier straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgïe'::text as content_technical_contact_address,
    'collections@naturalsciences.be'::text as content_technical_contact_email,
    'RBINS contact'::text as content_technical_contact_name,
    E'All data given access here are the sole property of the Royal Belgian Institute for Natural Sciences (RBINS) and are protected by the laws of copyright.\r\n The reuse of data, for any purpose whatsoever, is subject to prior authorization given by the Royal Belgian Institute for Natural Sciences (RBINS).\r\n For more informations, comments or details on the above lines, please contact the Royal Belgian Institute for Natural Sciences (RBINS).'::text as IPRCopyright
);

ALTER TABLE darwin2.taxonomy SET SCHEMA public; 
ALTER TABLE darwin2.catalogue_levels SET SCHEMA public; 
ALTER TABLE darwin2.darwin_flat SET SCHEMA public; 
ALTER TABLE darwin2.mineralogy SET SCHEMA public; 
ALTER TABLE darwin2.template_classifications SET SCHEMA public;

CREATE SEQUENCE public.parent_taxonomy_id_seq;

CREATE TABLE public.parent_taxonomy AS
(
  select nextval('public.parent_taxonomy_id_seq') as id, pt.child_id as child_id, pt.parent_id as parent_id, spt.name as taxon_name, spt.level_name as level_name from 
  (select t.id as child_id, st.tax_parent::integer as parent_id
  from taxonomy as t 
  inner join 
    (select id, regexp_split_to_table(path, '/') as tax_parent from taxonomy) as st on st.id = t.id
  where st.tax_parent != '' 
  ) as pt
  inner join
    (select taxonomy.id, name, level_name from taxonomy inner join catalogue_levels as cl on cl.id = taxonomy.level_ref where cl.level_name != 'unranked') as spt on spt.id = pt.parent_id
);

ALTER TABLE public.parent_taxonomy ADD CONSTRAINT pk_parent_taxonomy PRIMARY KEY (id);
CREATE INDEX idx_parent_taxon_child_id ON public.parent_taxonomy (child_id);

ALTER TABLE public.darwin_flat 
  DROP COLUMN building,
  DROP COLUMN "floor",
  DROP COLUMN room,
  DROP COLUMN "row",
  DROP COLUMN shelf,
  DROP COLUMN "container",
  DROP COLUMN container_type,
  DROP COLUMN container_storage,
  DROP COLUMN sub_container,
  DROP COLUMN sub_container_type,
  DROP COLUMN sub_container_storage,
  DROP COLUMN gtu_location,
  DROP COLUMN gtu_tag_values_indexed,
  DROP COLUMN with_types,
  DROP COLUMN with_individuals,
  DROP COLUMN with_parts;

ALTER TABLE public.taxonomy DROP CONSTRAINT fct_chk_onceinpath_taxonomy;
ALTER TABLE public.mineralogy DROP CONSTRAINT fct_chk_onceinpath_mineralogy;

ALTER FUNCTION darwin2.gettagsindexedasarray(character varying) SET SCHEMA public;
ALTER FUNCTION darwin2.array_accum(anyelement) SET SCHEMA public;
ALTER FUNCTION darwin2.linetotagarray(text) SET SCHEMA public;
ALTER FUNCTION darwin2.linetotagrows(text) SET SCHEMA public;
ALTER FUNCTION darwin2.fct_remove_array_elem(anyarray,anyelement) SET SCHEMA public;
ALTER FUNCTION darwin2.fct_remove_array_elem(anyarray,anyarray) SET SCHEMA public;
ALTER FUNCTION darwin2.fulltoindex(character varying) SET SCHEMA public;
ALTER SEQUENCE darwin2.taxonomy_id_seq SET SCHEMA public;
ALTER SEQUENCE darwin2.darwin_flat_id_seq SET SCHEMA public;
ALTER SEQUENCE darwin2.catalogue_levels_id_seq SET SCHEMA public;
ALTER SEQUENCE darwin2.mineralogy_id_seq SET SCHEMA public;

\t
\o drop_old_d2_triggers.sql
select DISTINCT 'DROP TRIGGER IF EXISTS ' || trigger_name || ' ON ' || event_object_schema || '.' || event_object_table || ';' 
FROM information_schema.triggers 
WHERE event_object_table IN ('taxonomy', 'mineralogy');
\o
\i drop_old_d2_triggers.sql
\! rm drop_old_d2_triggers.sql
\t

GRANT SELECT ON  public.flat_abcd TO d2viewer;
GRANT SELECT ON  public.gtu_properties TO d2viewer;
GRANT SELECT ON  public.gtu_place TO d2viewer;
GRANT SELECT ON  public.collectors TO d2viewer;
GRANT SELECT ON  public.collectors_institution TO d2viewer;
GRANT SELECT ON  public.donators TO d2viewer;
GRANT SELECT ON  public.donators_institution TO d2viewer;
GRANT SELECT ON  public.identifications_abdc TO d2viewer;
GRANT SELECT ON  public.taxon_identified TO d2viewer;
GRANT SELECT ON  public.bota_taxa_keywords TO d2viewer;
GRANT SELECT ON  public.zoo_taxa_keywords TO d2viewer;
GRANT SELECT ON  public.mineral_identified TO d2viewer;
GRANT SELECT ON  public.identifier TO d2viewer;
GRANT SELECT ON  public.identifier_instituion TO d2viewer;
GRANT SELECT ON  public.flat_properties TO d2viewer;
GRANT SELECT ON  public.users_abc TO d2viewer;
GRANT SELECT ON  public.people_abc TO d2viewer;
GRANT SELECT ON  public.institutions_abc TO d2viewer;
GRANT SELECT ON  public.lithostratigraphy_abc TO d2viewer;
GRANT SELECT ON  public.accomp_mineral TO d2viewer;
GRANT SELECT ON  public.taxonomy TO d2viewer;
GRANT SELECT ON  public.catalogue_levels TO d2viewer;
GRANT SELECT ON  public.darwin_flat TO d2viewer;
GRANT SELECT ON  public.mineralogy TO d2viewer;
GRANT SELECT ON  public.darwin_metadata TO d2viewer;
GRANT SELECT ON  public.parent_taxonomy TO d2viewer;

ANALYZE public.flat_abcd;
ANALYZE public.gtu_properties;
ANALYZE public.gtu_place;
ANALYZE public.collectors;
ANALYZE public.collectors_institution;
ANALYZE public.donators;
ANALYZE public.donators_institution;
ANALYZE public.identifications_abdc;
ANALYZE public.taxon_identified;
ANALYZE public.bota_taxa_keywords;
ANALYZE public.zoo_taxa_keywords;
ANALYZE public.mineral_identified;
ANALYZE public.identifier;
ANALYZE public.identifier_instituion;
ANALYZE public.flat_properties;
ANALYZE public.users_abc;
ANALYZE public.people_abc;
ANALYZE public.institutions_abc;
ANALYZE public.lithostratigraphy_abc;
ANALYZE public.accomp_mineral;
ANALYZE public.taxonomy;
ANALYZE public.catalogue_levels;
ANALYZE public.darwin_flat;
ANALYZE public.mineralogy;
ANALYZE public.parent_taxonomy;

DROP SCHEMA IF EXISTS darwin1 CASCADE;

DROP SCHEMA IF EXISTS darwin2 CASCADE;

revoke execute on function public.fulltoindex(character varying) from darwin1;
revoke all on table public.geometry_columns from cebmpad;
revoke all on table public.spatial_ref_sys from cebmpad;

DROP ROLE IF EXISTS darwin1;

DROP ROLE IF EXISTS cebmpad;

--\i ../createindexes_darwinflat.sql
