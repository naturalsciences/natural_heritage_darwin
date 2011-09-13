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

DROP TABLE IF EXISTS public.flat_abcd 
DROP TABLE IF EXISTS public.gtu_properties
DROP TABLE IF EXISTS public.gtu_place
DROP TABLE IF EXISTS public.collectors
DROP TABLE IF EXISTS public.collectors_institution
DROP TABLE IF EXISTS public.donators
DROP TABLE IF EXISTS public.donators_institution
DROP TABLE IF EXISTS public.identifications_abdc
DROP TABLE IF EXISTS public.taxon_identified
DROP TABLE IF EXISTS public.bota_taxa_keywords
DROP TABLE IF EXISTS public.zoo_taxa_keywords
DROP TABLE IF EXISTS public.taxa_vernacular_name
DROP TABLE IF EXISTS public.mineral_identified
DROP TABLE IF EXISTS public.mineral_vernacular_name
DROP TABLE IF EXISTS public.identifier
DROP TABLE IF EXISTS public.identifier_instituion
DROP TABLE IF EXISTS public.flat_properties
DROP TABLE IF EXISTS public.users_abc
DROP TABLE IF EXISTS public.people_abc
DROP TABLE IF EXISTS public.institutions_abc
DROP TABLE IF EXISTS public.lithostratigraphy_abc
DROP TABLE IF EXISTS public.accomp_mineral

DROP sequence IF EXISTS flat_abcd_id_seq;
DROP sequence IF EXISTS identifications_abdc_id_seq;
DROP sequence IF EXISTS taxon_identified_id_seq;
DROP sequence IF EXISTS mineral_identified_id_se

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


create sequence flat_abcd_id_seq;
CREATE TABLE public.flat_abcd as 
(
  select
  nextval('flat_abcd_id_seq'),
  spec.id AS           spec_ref,
  spec.category AS             category,
  spec.collection_ref AS       collection_ref,
  coll.collection_type AS      collection_type,
  coll.code AS         collection_code,
  coll.name AS         collection_name,
  coll.is_public AS            collection_is_public,
  coll.parent_ref AS           collection_parent_ref,
  coll.path AS         collection_path,
  spec.expedition_ref AS       expedition_ref,
  exp.name AS          expedition_name,
  exp.name_ts AS       expedition_name_ts,
  exp.name_indexed AS          expedition_name_indexed,
  spec.station_visible AS      station_visible,
  spec.gtu_ref AS      gtu_ref,
  gtu.code AS          gtu_code,
  gtu.parent_ref AS            gtu_parent_ref,
  gtu.path AS          gtu_path,
  gtu.gtu_from_date_mask AS            gtu_from_date_mask,
  gtu.gtu_from_date AS         gtu_from_date,
  gtu.gtu_to_date_mask AS      gtu_to_date_mask,
  gtu.gtu_to_date AS           gtu_to_date,
  gtu.tag_values_indexed AS            gtu_tag_values_indexed,
  taggr.tag_value AS           gtu_country_tag_value,
  (select lineToTagArray(tag_value) FROM tag_groups taggr WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country') AS            gtu_country_tag_indexed,
  gtu.location AS      gtu_location,
  ( select array_accum(DISTINCT people_ref) from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications' where i.referenced_relation='specimens' and p.people_type='identifier' and i.record_id = spec.id ) AS             spec_ident_ids,
  ( select array_accum(DISTINCT people_ref) from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications' where i.referenced_relation='specimen_individuals' and p.people_type='identifier' and i.record_id = sInd.id ) AS          ind_ident_ids,
  ( select array_accum(DISTINCT people_ref) from catalogue_people where referenced_relation='specimens' and people_type='collector' and record_id = spec.id) AS        spec_coll_ids,
  ( select array_accum(DISTINCT people_ref) from catalogue_people where referenced_relation='specimens' and people_type='donator' and record_id = spec.id) AS          spec_don_sel_ids,
  spec.taxon_ref AS            taxon_ref,
  taxon.name AS        taxon_name,
  taxon.name_indexed AS        taxon_name_indexed,
  taxon.name_order_by AS       taxon_name_order_by,
  taxon.level_ref AS           taxon_level_ref,
  taxon_level.level_name AS            taxon_level_name,
  taxon.status AS      taxon_status,
  taxon.path AS        taxon_path,
  taxon.parent_ref AS          taxon_parent_ref,
  taxon.extinct AS             taxon_extinct,
  spec.litho_ref AS            litho_ref,
  litho.name AS        litho_name,
  litho.name_indexed AS        litho_name_indexed,
  litho.name_order_by AS       litho_name_order_by,
  litho.level_ref AS           litho_level_ref,
  litho_level.level_name AS            litho_level_name,
  litho.status AS      litho_status,
  litho.local_naming AS        litho_local,
  litho.color AS       litho_color,
  litho.path AS        litho_path,
  litho.parent_ref AS          litho_parent_ref,
  spec.chrono_ref AS           chrono_ref,
  chrono.name AS       chrono_name,
  chrono.name_indexed AS       chrono_name_indexed,
  chrono.name_order_by AS      chrono_name_order_by,
  chrono.level_ref AS          chrono_level_ref,
  chrono_level.level_name AS           chrono_level_name,
  chrono.status AS             chrono_status,
  chrono.local_naming AS       chrono_local,
  chrono.color AS      chrono_color,
  chrono.path AS       chrono_path,
  chrono.parent_ref AS         chrono_parent_ref,
  spec.lithology_ref AS        lithology_ref,
  lithology.name AS            lithology_name,
  lithology.name_indexed AS            lithology_name_indexed,
  lithology.name_order_by AS           lithology_name_order_by,
  lithology.level_ref AS       lithology_level_ref,
  lithology_level.level_name AS        lithology_level_name,
  lithology.status AS          lithology_status,
  lithology.local_naming AS            lithology_local,
  lithology.color AS           lithology_color,
  lithology.path AS            lithology_path,
  lithology.parent_ref AS      lithology_parent_ref,
  spec.mineral_ref AS          mineral_ref,
  mineral.name AS      mineral_name,
  mineral.name_indexed AS      mineral_name_indexed,
  mineral.name_order_by AS             mineral_name_order_by,
  mineral.level_ref AS         mineral_level_ref,
  mineral_level.level_name AS          mineral_level_name,
  mineral.status AS            mineral_status,
  mineral.local_naming AS      mineral_local,
  mineral.color AS             mineral_color,
  mineral.path AS      mineral_path,
  mineral.parent_ref AS        mineral_parent_ref,
  spec.host_taxon_ref AS       host_taxon_ref,
  spec.host_relationship AS            host_relationship,
  host_taxon.name AS           host_taxon_name,
  host_taxon.name_indexed AS           host_taxon_name_indexed,
  host_taxon.name_order_by AS          host_taxon_name_order_by,
  host_taxon.level_ref AS      host_taxon_level_ref,
  host_taxon_level.level_name AS       host_taxon_level_name,
  host_taxon.status AS         host_taxon_status,
  host_taxon.path AS           host_taxon_path,
  host_taxon.parent_ref AS             host_taxon_parent_ref,
  host_taxon.extinct AS        host_taxon_extinct,
  spec.host_specimen_ref AS            host_specimen_ref,
  spec.ig_ref AS       ig_ref,
  igs.ig_num AS        ig_num,
  igs.ig_num_indexed AS        ig_num_indexed,
  igs.ig_date_mask AS          ig_date_mask,
  igs.ig_date AS       ig_date,
  CASE WHEN spec.category='observation' THEN null ELSE spec.acquisition_category END  as acquisition_category,
  CASE WHEN spec.category='observation' THEN null ELSE spec.acquisition_date_mask END  as acquisition_date_mask,
  CASE WHEN spec.category='observation' THEN null ELSE spec.acquisition_date END as acquisition_date ,

  sInd.id AS           individual_ref,
  coalesce(sInd.type, 'specimen') AS           individual_type,
  coalesce(sInd.type_group, 'specimen') AS             individual_type_group,
  coalesce(sInd.type_search, 'specimen') AS            individual_type_search,
  coalesce(sInd.sex, 'undefined') AS           individual_sex,
  coalesce(sInd.state, 'not applicable') AS            individual_state,
  coalesce(sInd.stage, 'undefined') AS         individual_stage,
  coalesce(sInd.social_status, 'not applicable') AS            individual_social_status,
  coalesce(sInd.rock_form, 'not applicable') AS        individual_rock_form,
  coalesce(sInd.specimen_individuals_count_min, 1) AS          individual_count_min,
  coalesce(sInd.specimen_individuals_count_max, 1) AS          individual_count_max,
  sPart.id AS          part_ref,
  sPart.specimen_part AS       part,
  sPart.specimen_status AS             part_status,
  sPart.building AS            building,
  sPart.floor AS       "floor",
  sPart.room AS        "room",
  sPart.row AS         "row",
  sPart.shelf AS       shelf,
  sPart.container AS           "container",
  sPart.sub_container AS       sub_container,
  sPart.container_type AS      container_type,
  sPart.sub_container_type AS          sub_container_type,
  sPart.container_storage AS           container_storage,
  sPart.sub_container_storage AS       sub_container_storage,
  sPart.specimen_part_count_min AS             part_count_min,
  sPart.specimen_part_count_max AS             part_count_max,
  sPart.specimen_status AS             specimen_status,
  sPart.complete AS            "complete",
  sPart.surnumerary AS          surnumerary,
  'http://darwin.naturalsciences.be/search/view/id/' || sInd.id as unit_url,
  CASE WHEN sInd.type = 'specimen' THEN null ELSE sInd.type END as individual_simple_type,

  coll.institution_ref as collection_institution_ref,
  i_col.formated_name as collection_institution_name,
  coll.main_manager_ref as collection_main_manager_ref,
  p_col.formated_name as collection_main_manager_name,

-- GTU
  
  gtu.elevation as gtu_altitude,
  gtu.elevation_accuracy as gtu_altitude_accuracy,
  gtu.latitude as gtu_latitude,
  gtu.latitude as gtu_longitude,
  gtu.lat_long_accuracy as gtu_lat_long_accuracy,

  (select lineToTagRows(tag_value) FROM tag_groups taggr WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country' limit 1) AS gtu_country,
  --(select  * FROM tag_groups taggr WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country' limit 1) AS gtu_country,
  (select method from specimen_collecting_methods sm 
        INNER JOIN collecting_methods cm on sm.collecting_method_ref = cm.id  
        WHERE sm.specimen_ref = spec.id limit 1) AS gtu_method,


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


  (select array_to_string(array(select comment from comments c_flat where ( c_flat.referenced_relation = 'gtu' AND c_flat.record_id=spec.gtu_ref)
or ( c_flat.referenced_relation = 'specimens' AND c_flat.record_id = spec.id) ),' ' )) as flat_comments,

  ( select min(property_value) from properties_values where property_ref = cp3.id) as utm_text

FROM specimens spec
     LEFT JOIN igs ON spec.ig_ref = igs.id
     INNER JOIN collections coll ON spec.collection_ref = coll.id
     INNER JOIN people i_col ON i_col.id = coll.institution_ref
     INNER JOIN people p_col on p_col.id = coll.main_manager_ref
     INNER JOIN expeditions exp ON spec.expedition_ref = exp.id
     INNER JOIN (gtu LEFT JOIN tag_groups taggr ON gtu.id = taggr.gtu_ref 
                                                AND taggr.group_name_indexed = 'administrativearea' 
                                                AND sub_group_name_indexed = 'country'
                ) ON spec.gtu_ref = gtu.id
     LEFT JOIN ( gtu as g_depth LEFT JOIN catalogue_properties cp1 ON cp1.referenced_relation = 'gtu' AND cp1.record_id = g_depth.id AND cp1.property_type = 'physical measurement' and cp1.property_qualifier = 'depth' )

        ON spec.gtu_ref = g_depth.id

     LEFT JOIN ( gtu as g_height LEFT JOIN catalogue_properties cp2 ON cp2.referenced_relation = 'gtu' AND cp2.record_id = g_height.id and cp2.property_type = 'physical measurement' AND cp2.property_qualifier = 'height' )
        ON spec.gtu_ref = g_height.id

     LEFT JOIN ( gtu as g_utm LEFT JOIN catalogue_properties cp3 ON cp3.referenced_relation = 'gtu' AND cp3.record_id = g_utm.id and cp3.property_type = 'geo position' AND cp3.property_qualifier = 'utm' )
        ON spec.gtu_ref = g_height.id

     INNER JOIN (taxonomy taxon INNER JOIN catalogue_levels taxon_level ON taxon.level_ref = taxon_level.id
                ) ON spec.taxon_ref = taxon.id
     INNER JOIN (chronostratigraphy chrono INNER JOIN catalogue_levels chrono_level ON chrono.level_ref = chrono_level.id
                ) ON spec.chrono_ref = chrono.id
     INNER JOIN (lithostratigraphy litho INNER JOIN catalogue_levels litho_level ON litho.level_ref = litho_level.id
                ) ON spec.litho_ref = litho.id
     INNER JOIN (lithology INNER JOIN catalogue_levels lithology_level ON lithology.level_ref = lithology_level.id
                ) ON spec.lithology_ref = lithology.id
     INNER JOIN (mineralogy mineral INNER JOIN catalogue_levels mineral_level ON mineral.level_ref = mineral_level.id
                ) ON spec.mineral_ref = mineral.id
     INNER JOIN (taxonomy host_taxon INNER JOIN catalogue_levels host_taxon_level ON host_taxon.level_ref = host_taxon_level.id
                ) ON spec.host_taxon_ref = host_taxon.id
     LEFT JOIN (specimen_individuals sInd LEFT JOIN specimen_parts sPart ON sInd.id = sPart.specimen_individual_ref
               ) ON spec.id = sInd.specimen_ref
    
    WHERE coll.is_public = true
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
    property_type  || ' / ' || property_sub_type as parameter,
    gtu.id    

  FROM 
    flat_abcd as flat
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
    property_type,
    property_sub_type
);

CREATE TABLE public.gtu_place as
(
  select f.id as flat_id,
  tag as place
  
  FROM  flat_abcd  f
  
  inner join tags ON f.gtu_ref = tags.gtu_ref
);




CREATE TABLE public.collectors as
(
  select f.id as flat_id,
  c.people_ref,
  c.order_by
  FROM  flat_abcd  f
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

  FROM  flat_abcd  f
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
  FROM  flat_abcd  f
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

  FROM  flat_abcd  f
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

  FROM  flat_abcd  f
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
    FROM  flat_abcd  f
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
    INNER JOIN flat_abcd  f on i.flat_id = f.id

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
    INNER JOIN flat_abcd  f on i.flat_id = f.id
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
    property_type  || ' / ' || property_sub_type as parameter

  FROM 
    flat_abcd as flat
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
    property_type  || ' / ' || property_sub_type as parameter

  FROM 
    flat_abcd as flat
    INNER JOIN catalogue_properties c ON referenced_relation = 'specimen_individuals' AND record_id = flat.individual_ref
    INNER JOIN properties_values p ON property_ref = c.id 
  GROUP BY 
    flat.id,
    c.id,
    date_to - date_from, 
    property_method, 
    property_unit,
    date_from, 
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
    property_type  || ' / ' || property_sub_type as parameter

  FROM 
    flat_abcd as flat
    INNER JOIN catalogue_properties c ON referenced_relation = 'part_ref' AND record_id = flat.individual_ref
    INNER JOIN properties_values p ON property_ref = c.id 
  GROUP BY 
    flat.id,
    c.id,
    date_to - date_from, 
    property_method, 
    property_unit,
    date_from, 
    property_type  || ' / ' || property_sub_type
);


CREATE TABLE public.users_abc as 
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM users_addresses where tag like '%pref%'  LIMIT 1) as address,
  ( SELECT entry FROM users_comm where tag like '%pref%'  LIMIT 1) as email

  from users
  
);


CREATE TABLE public.people_abc as 
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM people_addresses where tag like '%pref%'  LIMIT 1) as address,
  ( SELECT entry FROM people_comm where tag like '%pref%'  LIMIT 1) as email

  from people
  
  WHERE is_physical = true
  
);

CREATE TABLE public.institutions_abc as
(
  SELECT * ,

  ( SELECT entry || ' ' || zip_code  || ' ' || locality  || ' ' || country FROM people_addresses where tag like '%pref%'  LIMIT 1) as address,
  ( SELECT entry FROM people_comm where tag like '%pref%'  LIMIT 1) as email

  from people
  
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
    flat_abcd f
    INNER JOIN specimens_accompanying a ON f.spec_ref = a.specimen_ref
    INNER JOIN mineralogy m ON a.mineral_ref = m.id
    WHERE accompanying_type ='mineral'

);



--\i ../createindexes_darwinflat.sql
