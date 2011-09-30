--create table catalogue_levels

DROP TABLE IF EXISTS users_login_infos;
DROP TABLE IF EXISTS people_multimedia;

DROP TABLE IF EXISTS users_multimedia;
DROP TABLE IF EXISTS collections_rights; -- SURE???
DROP TABLE IF EXISTS my_saved_searches;
DROP TABLE IF EXISTS my_widgets;
DROP TABLE IF EXISTS preferences;
-- create table darwin_flat
-- create table flat_dict
-- create table imports
-- create table staging
-- create table  staging_tag_groups

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

DROP sequence IF EXISTS flat_abcd_id_seq;
DROP sequence IF EXISTS identifications_abdc_id_seq;
DROP sequence IF EXISTS taxon_identified_id_seq;
DROP sequence IF EXISTS mineral_identified_id_seq;

alter table catalogue_levels DROP constraint unq_catalogue_levels;


UPDATE catalogue_levels
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

DELETE FROM darwin_flat where collection_is_public = false;

create sequence flat_abcd_id_seq;

CREATE TABLE public.flat_abcd as 
(
  nextval('flat_abcd_id_seq') as id,
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

  (select lineToTagRows(tag_value) FROM tag_groups taggr WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country' limit 1) AS gtu_country,
  --(select  * FROM tag_groups taggr WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country' limit 1) AS gtu_country,
  (select method from specimen_collecting_methods sm 
        INNER JOIN collecting_methods cm on sm.collecting_method_ref = cm.id  
        WHERE sm.specimen_ref = f.spec_ref limit 1) AS gtu_method,


  cp1.date_to - cp1.date_from as depth_duration,
  ( select min(property_value) from properties_values where property_ref = cp1.id) as depth_lowervalue,
  ( select max(property_value) from properties_values where property_ref = cp1.id) as depth_uppervalue, /*** only if 1? **/
  cp1.property_method as depth_method,
  cp1.property_unit as depth_unit,
  cp1.date_from as depth_date_time,

  cp2.date_to - cp2.date_from as height_duration,
  ( select min(property_value) from properties_values where property_ref = cp2.id) as height_lowervalue,
  ( select max(property_value) from properties_values where property_ref = cp2.id) as height_uppervalue, /*** only if 1? **/
  cp2.property_method as height_method,
  cp2.property_unit as height_unit,
  cp2.date_from as height_date_time,


  (select array_to_string(array(select comment from comments c_flat where ( c_flat.referenced_relation = 'gtu' AND c_flat.record_id=f.gtu_ref)
or ( c_flat.referenced_relation = 'specimens' AND c_flat.record_id = f.spec_ref) ),' ' )) as flat_comments,

  ( select min(property_value) from properties_values where property_ref = cp3.id) as utm_text


  FROM darwin_flat f
  
  INNER JOIN collections coll ON f.collection_ref = coll.id
  INNER JOIN people i_col ON i_col.id = coll.institution_ref
  INNER JOIN people p_col on p_col.id = coll.main_manager_ref
  INNER JOIN gtu ON f.gtu_ref = gtu.id

  LEFT JOIN ( gtu as g_depth LEFT JOIN catalogue_properties cp1 ON cp1.referenced_relation = 'gtu' AND cp1.record_id = g_depth.id AND cp1.property_type = 'physical measurement' and cp1.property_qualifier = 'depth' )

        ON f.gtu_ref = g_depth.id

     LEFT JOIN ( gtu as g_height LEFT JOIN catalogue_properties cp2 ON cp2.referenced_relation = 'gtu' AND cp2.record_id = g_height.id and cp2.property_type = 'physical measurement' AND cp2.property_qualifier = 'height' )
        ON f.gtu_ref = g_height.id

     LEFT JOIN ( gtu as g_utm LEFT JOIN catalogue_properties cp3 ON cp3.referenced_relation = 'gtu' AND cp3.record_id = g_utm.id and cp3.property_type = 'geo position' AND cp3.property_qualifier = 'utm' )
        ON f.gtu_ref = g_utm.id
);


CREATE TABLE public.gtu_properties as
(
  select
    flat.id as flat_id,
    date_to - date_from as duration,
    min(v.property_value) as lowervalue,
    max(v.property_value) as uppervalue,
    property_method as method ,
    property_unit as unit,
    date_from as date_from,
    date_to as date_to,
    property_type  || ' / ' || property_sub_type as parameter,
    gtu.id,
    property_qualifier as qualifier,
    min(v.property_accuracy) as accuracy
  FROM 
    darwin_flat as flat
    INNER JOIN gtu ON flat.gtu_ref = gtu.id
    INNER JOIN catalogue_properties c ON referenced_relation = 'gtu' AND record_id = gtu.id
    INNER JOIN properties_values v ON property_ref = c.id 
  WHERE 
    property_type != 'geo position' and property_sub_type not in ('height', 'altitude', 'depth')

  GROUP BY 
    flat.id,
    gtu.id ,
    c.id,
    date_to - date_from, 
    property_method, 
    property_unit,
    date_from,
    date_to,
    property_type,
    property_sub_type
);

CREATE TABLE public.gtu_place as
(
  select f.id as flat_id,
  tag as place
  
  FROM  darwin_flat  f
  
  inner join tags ON f.gtu_ref = tags.gtu_ref

  WHERE sub_group_type != 'country'
);




CREATE TABLE public.collectors as
(
  select f.id as flat_id,
  c.people_ref,
  c.order_by
  FROM  darwin_flat  f
  inner join catalogue_people as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'
  WHERE 
    c.people_type = 'collector'
    AND exists (select 1 from people p where p.id = c.people_ref and is_physical = true)
);



CREATE TABLE public.collectors_institution as
(
  select f.id as flat_id,
  c.people_ref,
  c.order_by,
  p.formated_name

  FROM  darwin_flat  f
  inner join catalogue_people as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'
  INNER JOIN  people p ON p.id = c.people_ref and is_physical = true
  WHERE 
    c.people_type = 'collector'
  
);


CREATE TABLE public.donators as
(
  select f.id as flat_id,
  c.people_ref,
  c.order_by
  FROM  darwin_flat  f
  inner join catalogue_people as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'
  WHERE 
    c.people_type = 'donator'
    AND exists (select 1 from people p where p.id = c.people_ref and is_physical = true)
);



CREATE TABLE public.donators_institution as
(
  select f.id as flat_id,
  c.people_ref,
  c.order_by,
  p.formated_name

  FROM  darwin_flat  f
  inner join catalogue_people as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'
  INNER JOIN  people p ON p.id = c.people_ref and is_physical = true
  WHERE 
    c.people_type = 'donator'
  
);



create sequence identifications_abdc_id_seq;

CREATE TABLE public.identifications_abdc as
(
  select
  nextval('identifications_abdc_id_seq') as id,
  f.id as flat_id,
  notion_date,
  determination_status,
  false as is_current,
  notion_concerned,
  c.id as old_identification_id

  FROM  darwin_flat  f
  INNER JOIN identifications as c ON f.spec_ref = c.record_id  AND c.referenced_relation = 'specimens'

);



create sequence taxon_identified_id_seq;

CREATE TABLE public.taxon_identified as
(
  SELECT 
    nextval('taxon_identified_id_seq') as id,
    i.id as identification_ref,
    c.value_defined as taxon_name,
    null::integer as taxon_ref,
    null::integer as taxon_parent_ref
  FROM 
    identifications_abdc i
    INNER JOIN identifications as c ON i.old_identification_id = c.id
    WHERE 
    notion_concerned = 'taxonomy'
);



insert into identifications_abdc 
(
    id,
    flat_id,
    notion_date,
    determination_status,
    is_current
)
(
  select
   nextval('identifications_abdc_id_seq') as id,
    f.id as flat_id,
    now() as notion_date,
    '' as determination_status,
    true as is_current
    FROM  darwin_flat  f
);


insert into taxon_identified
(
    id,
    identification_ref,
    taxon_name,
    taxon_ref,
    taxon_parent_ref
)
(
  select
    nextval('taxon_identified_id_seq') as id,
    i.id as identification_ref,
    f.taxon_name as taxon_name,
    f.taxon_ref as taxon_ref,
    f.taxon_parent_ref as taxon_parent_ref
    FROM  identifications_abdc i
    INNER JOIN darwin_flat  f on i.flat_id = f.id

    WHERE i.is_current = true
      AND taxon_ref !=0 
);


ALTER COLUMN taxonomy.parent_ref DROP NOT NULL;

UPDATE taxonomy SET parent_ref = NULL WHERE parent_ref = 0;


CREATE TABLE public.bota_taxa_keywords AS
(
  SELECT
  
  ti.id as taxon_identified_ref,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='AuthorTeam' LIMIT 1) as AuthorTeam,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='AuthorTeamParenthesis' LIMIT 1) as AuthorTeamParenthesis,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='CultivarGroupName' LIMIT 1) as CultivarGroupName,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='CultivarName' LIMIT 1) as CultivarName,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='FirstEpithet' LIMIT 1) as FirstEpithet,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='GenusOrMonomial' LIMIT 1) as GenusOrMonomial,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='InfraspecificEpithet' LIMIT 1) as InfraspecificEpithet

  FROM taxon_identified ti
  INNER JOIN taxonomy t on t.id = ti.taxon_ref
  
  WHERE
    t.path like '/-1/141538/%' --PLANTEA
);


CREATE TABLE public.zoo_taxa_keywords AS
(
  SELECT
  
  ti.id as taxon_identified_ref,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='AuthorTeamOriginalAndYear' LIMIT 1) as AuthorTeamOriginalAndYear,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='AuthorTeamParenthesisAndYear' LIMIT 1) as AuthorTeamParenthesisAndYear,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='Breed' LIMIT 1) as Breed,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='CombinationAuthorTeamAndYear' LIMIT 1) as CombinationAuthorTeamAndYear,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='GenusOrMonomial' LIMIT 1) as GenusOrMonomial,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='NamedIndividual' LIMIT 1) as NamedIndividual,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='SpeciesEpithet' LIMIT 1) as SpeciesEpithet,
  (SELECT keyword FROM classification_keywords where 
        referenced_relation = 'taxonomy' and record_id = t.id AND keyword_type='Subgenus' LIMIT 1) as Subgenus

  FROM taxon_identified ti
  INNER JOIN taxonomy t on t.id = ti.taxon_ref
  
  WHERE
    t.path like '/-1/1/%' --ANIMAL
);



CREATE TABLE public.taxa_vernacular_name as
(
  SELECT
  ti.id as taxon_identified_ref,
  community,
  v.name

  FROM taxon_identified ti
  INNER JOIN taxonomy t on t.id = ti.taxon_ref
  INNER JOIN class_vernacular_names c ON t.id = c.record_id AND c.referenced_relation = 'taxonomy' 
  INNER JOIN vernacular_names v ON c.id = v.vernacular_class_ref
);






create sequence mineral_identified_id_seq;

CREATE TABLE public.mineral_identified as
(
  SELECT 
    nextval('mineral_identified_id_seq') as id,
    i.id as identification_ref,
    c.value_defined as mineral_name,
    null::integer as mineral_ref, 
    null::varchar as classification
  FROM 
    identifications_abdc i
    INNER JOIN identifications as c ON i.old_identification_id = c.id
    WHERE 
    c.notion_concerned = 'mineralogy'
    AND i.is_current = FALSE
);


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
    nextval('mineral_identified_id_seq') as id,
    i.id as identification_ref,
    m.name as mineral_name,
    f.mineral_ref as mineral_ref,
    m.classification as classification
    FROM  identifications_abdc i
    INNER JOIN darwin_flat  f on i.flat_id = f.id
    INNER JOIN mineralogy m on f.mineral_ref = m.id
    WHERE i.is_current = true
      AND f.mineral_ref !=0 
      
);


CREATE TABLE public.mineral_vernacular_name as
(
  SELECT
  ti.id as mineral_identified_ref,
  community,
  v.name

  FROM mineral_identified ti
  INNER JOIN mineralogy t on t.id = ti.mineral_ref
  INNER JOIN class_vernacular_names c ON t.id = c.record_id AND c.referenced_relation = 'mineralogy' 
  INNER JOIN vernacular_names v ON c.id = v.vernacular_class_ref
);


CREATE TABLE public.identifier as
(
  select 
    i.id as identification_ref,
    c.people_ref
  
  FROM
    identifications_abdc i
    INNER JOIN catalogue_people c on i.old_identification_id = c.record_id AND c.referenced_relation='identifications'
    INNER JOIN people p on p.id = c.people_ref
  WHERE
    p.is_physical = true
    
);

CREATE TABLE public.identifier_instituion as
(
  select 
    i.id as identification_ref,
    c.people_ref
  
  FROM
    identifications_abdc i
    INNER JOIN catalogue_people c on i.old_identification_id = c.record_id AND c.referenced_relation='identifications'
    INNER JOIN people p on p.id = c.people_ref
  WHERE
    p.is_physical = false
    
);



CREATE TABLE public.flat_properties as
(
  select
    flat.id as flat_id,
    date_to - date_from as duration,
    min(property_value) as lowervalue,
    max(property_value) as uppervalue,
    property_method as method ,
    property_unit as unit,
    date_from as date_from,
    date_to as date_to,
    property_type  || ' / ' || property_sub_type as parameter

  FROM 
    darwin_flat as flat
    INNER JOIN catalogue_properties c ON referenced_relation = 'specimens' AND record_id = flat.spec_ref
    INNER JOIN properties_values p ON property_ref = c.id 
  /*WHERE 
    property_type != 'geo position' and property_sub_type not in ('height', 'altitude', 'depth')    */
  GROUP BY 
    flat.id,
    c.id,
    date_to - date_from, 
    property_method, 
    property_unit,
    date_from, 
    date_to,
    property_type  || ' / ' || property_sub_type

UNION


  select
    flat.id as flat_id,
    date_to - date_from as duration,
    min(property_value) as lowervalue,
    max(property_value) as uppervalue,
    property_method as method ,
    property_unit as unit,
    date_from as date_from,
    date_to as date_to,
    property_type  || ' / ' || property_sub_type as parameter

  FROM 
    darwin_flat as flat
    INNER JOIN catalogue_properties c ON referenced_relation = 'specimen_individuals' AND record_id = flat.individual_ref
    INNER JOIN properties_values p ON property_ref = c.id 
  GROUP BY 
    flat.id,
    c.id,
    date_to - date_from, 
    property_method, 
    property_unit,
    date_from, 
    date_to,
    property_type  || ' / ' || property_sub_type

UNION


  select
    flat.id as flat_id,
    date_to - date_from as duration,
    min(property_value) as lowervalue,
    max(property_value) as uppervalue,
    property_method as method ,
    property_unit as unit,
    date_from as date_from,
    date_to as date_to,
    property_type  || ' / ' || property_sub_type as parameter

  FROM 
    darwin_flat as flat
    INNER JOIN catalogue_properties c ON referenced_relation = 'part_ref' AND record_id = flat.individual_ref
    INNER JOIN properties_values p ON property_ref = c.id 
  GROUP BY 
    flat.id,
    c.id,
    date_to - date_from, 
    property_method, 
    property_unit,
    date_from, 
    date_to,
    property_type  || ' / ' || property_sub_type
);


CREATE TABLE public.users_abc as 
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM users_addresses where  person_user_ref = u.id ORDER BY tag like '%pref%'  LIMIT 1) as address,
  ( SELECT entry FROM users_comm where  person_user_ref = u.id and comm_type='e-mail' ORDER BY tag like '%pref%' LIMIT 1) as email

  from users u
  
);


CREATE TABLE public.people_abc as 
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM people_addresses where  person_user_ref = p.id ORDER BY tag like '%pref%'LIMIT 1) as address,
  ( SELECT entry FROM people_comm where person_user_ref = p.id    and comm_type='e-mail' ORDER BY tag like '%pref%' LIMIT 1) as email

  from people p
  
  WHERE is_physical = true
  
);

CREATE TABLE public.institutions_abc as
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM people_addresses where  person_user_ref = p.id ORDER BY tag like '%pref%' LIMIT 1) as address,
  ( SELECT entry FROM people_comm where person_user_ref = p.id   and comm_type='e-mail' ORDER BY tag like '%pref%' LIMIT 1) as email

  from people p
  
  WHERE is_physical = false
  
);



CREATE TABLE public.lithostratigraphy_abc as 
(
  SELECT * ,
  ( SELECT name from lithostratigraphy l2  WHERE level_ref = 64  AND l1.path like '%/' || l2.id || '/%' ) AS group,
  ( SELECT name from lithostratigraphy l2  WHERE level_ref = 65  AND l1.path like '%/' || l2.id || '/%' ) as formation,
  ( SELECT name from lithostratigraphy l2  WHERE level_ref = 66  AND l1.path like '%/' || l2.id || '/%' ) as member
 
  from Lithostratigraphy l1
    
);



CREATE TABLE public.accomp_mineral AS
(


  SELECT
    a.id,
    f.id as flat_id,
    m.classification as classification,
    m.name as mineral_name
   FROM 
    darwin_flat f
    INNER JOIN specimens_accompanying a ON f.spec_ref = a.specimen_ref
    INNER JOIN mineralogy m ON a.mineral_ref = m.id
    WHERE accompanying_type ='mineral'

);


CREATE TABLE public.darwin_metadata AS
(
  SELECT
    'Rue Vautier straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgïe'::text as content_contact_address,
    'collections@naturalsciences.be'::text as content_contact_email,
    'RBINS contact'::text as content_contact_name,
    'EN'::text as metadata_representation_language,
    'RBINS collections'::text as metadata_representation_title,
    current_timestamp as metadata_revision_date,
    'Rue Vautier straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgïe'::text as content_technical_contact_address,
    'collections@naturalsciences.be'::text as content_technical_contact_email,
    'RBINS contact'::text as content_technical_contact_name
);


ALTER TABLE taxonomy SET SCHEMA public; 
ALTER TABLE catalogue_levels SET SCHEMA public; 
ALTER TABLE darwin_flat SET SCHEMA public; 
ALTER TABLE mineralogy SET SCHEMA public; 

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

UPDATE public.darwin_flat
SET gtu_from_date = null::timestamp
WHERE gtu_from_date_mask = 0;

UPDATE public.darwin_flat
SET gtu_to_date = null::timestamp
WHERE gtu_to_date_mask = 0;

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
ANALYZE public.taxa_vernacular_name;
ANALYZE public.mineral_identified;
ANALYZE public.mineral_vernacular_name;
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


DROP SCHEMA IF EXISTS darwin1;
DROP ROLE IF EXISTS darwin1;

--\i ../createindexes_darwinflat.sql
