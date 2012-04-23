SET search_path = darwin2, public;

DROP FUNCTION if exists lineToTagRowsFormatConserved(text) CASCADE;
drop function if exists labeling_country_for_indexation(gtu.id%TYPE) CASCADE;
drop function if exists labeling_country_for_indexation_array(gtu.id%TYPE) CASCADE;
drop function if exists labeling_province_for_indexation(gtu.id%TYPE) CASCADE;
drop function if exists labeling_province_for_indexation_array(gtu.id%TYPE) CASCADE;
drop function if exists labeling_other_gtu_for_indexation(gtu.id%TYPE) CASCADE;
drop function if exists labeling_other_gtu_for_indexation_array(gtu.id%TYPE) CASCADE;
DROP FUNCTION IF EXISTS labeling_code_num_for_indexation(specimen_parts.id%TYPE) CASCADE;
drop function if exists labeling_individual_sex_for_indexation(specimen_individuals.sex%TYPE) CASCADE;
drop function if exists labeling_individual_stage_for_indexation(specimen_individuals.stage%TYPE) CASCADE;

DROP INDEX IF EXISTS idx_labeling_country;
DROP INDEX IF EXISTS idx_labeling_province;
DROP INDEX IF EXISTS idx_labeling_other_gtu;
DROP INDEX IF EXISTS idx_labeling_code;
DROP INDEX IF EXISTS idx_labeling_code_varchar;
DROP INDEX IF EXISTS idx_labeling_code_numeric;
DROP INDEX IF EXISTS idx_labeling_individual_type;
DROP INDEX IF EXISTS idx_labeling_individual_sex;
DROP INDEX IF EXISTS idx_darwin_flat_individual_sex;
DROP INDEX IF EXISTS idx_labeling_individual_stage;
DROP INDEX IF EXISTS idx_darwin_flat_individual_stage;
DROP INDEX IF EXISTS idx_labeling_part;
DROP INDEX IF EXISTS idx_labeling_ig_num_numeric;
DROP INDEX IF EXISTS idx_labeling_ig_num_coalesced;

CREATE INDEX idx_labeling_province ON darwin_flat USING gin (gtu_province_tag_indexed) WHERE part_ref IS NOT NULL;

CREATE INDEX idx_labeling_other_gtu ON darwin_flat USING gin (gtu_others_tag_indexed) WHERE part_ref IS NOT NULL;

create or replace function labeling_code_for_indexation(in part_ref specimen_parts.id%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
select array_agg(coding)
from (select trim(coalesce(code_prefix, '') || coalesce(code_prefix_separator, '') || coalesce(code, '') || coalesce(code_suffix_separator, '') || coalesce(code_suffix, ''))::varchar as coding
      from codes
      where referenced_relation = 'specimen_parts'
        and record_id = $1
        and code_category = 'main'
        and coalesce(upper(code_prefix),'') != 'RBINS'
     ) as x;
$$;

GRANT EXECUTE ON FUNCTION labeling_code_for_indexation(specimen_parts.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_code_for_indexation(specimen_parts.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_code_for_indexation(specimen_parts.id%TYPE) OWNER TO darwin2;

CREATE INDEX idx_labeling_code ON darwin_flat USING gin (labeling_code_for_indexation(part_ref)) WHERE part_ref IS NOT NULL;

create or replace function labeling_individual_type_for_indexation(in individual_type specimen_individuals.type%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
SELECT array[coalesce(fullToIndex($1),'-')];
$$;

create or replace function labeling_part_for_indexation(in part specimen_parts.specimen_part%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
SELECT array[coalesce(fullToIndex($1),'-')];
$$;

GRANT EXECUTE ON FUNCTION labeling_individual_type_for_indexation(specimen_individuals.type%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_individual_type_for_indexation(specimen_individuals.type%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_individual_type_for_indexation(specimen_individuals.type%TYPE) OWNER TO darwin2;
GRANT EXECUTE ON FUNCTION labeling_part_for_indexation(specimen_parts.specimen_part%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_part_for_indexation(specimen_parts.specimen_part%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_part_for_indexation(specimen_parts.specimen_part%TYPE) OWNER TO darwin2;

CREATE INDEX idx_labeling_individual_type ON specimen_individuals using gin (labeling_individual_type_for_indexation("type"));
CREATE INDEX idx_labeling_part ON specimen_parts using gin (labeling_part_for_indexation(specimen_part));

CREATE INDEX idx_labeling_ig_num_numeric ON specimens_flat(convert_to_integer(coalesce(ig_num, '-')));

drop view "public"."labeling";

create or replace view "public"."labeling" as
select df.part_ref as unique_id,
       df.collection_ref as collection,
       df.collection_name as collection_name,
       df.collection_path as collection_path, 
       trim(both ',' from
        trim(case when coalesce(df.part,'') in ('specimen', 'animal', 'undefined', 'unknown', '') then '' else df.part end 
              || 
              case when df.individual_sex in ('undefined', 'unknown', 'not stated', 'non applicable') then '' else ', ' || df.individual_sex || case when df.individual_state = 'not applicable' then '' else df.individual_state end end 
              || 
              case when df.individual_type = 'specimen' then '' else ', ' || df.individual_type end 
              || 
              case when df.individual_stage in ('undefined', 'unknown', 'not stated') then '' else ', ' || df.individual_stage end 
              || 
              case when coalesce(df.container_storage, '') in ('unknown', '/', '') then '' || case when coalesce(df.sub_container_storage, '') in ('unknown', '/', '')  then '' else ', ' || df.sub_container_storage end else ', ' || df.container_storage || case when coalesce(df.sub_container_storage, '') in ('unknown', '/', '') or df.sub_container_storage = df.container_storage then '' else ' - ' || df.sub_container_storage end end
            )) as item,
       array[fullToIndex(df.part)] as part,
       array[fullToIndex(df.individual_type)] as type,
       df.individual_sex as sex,
       df.individual_stage as stage,
       CAST(array_to_string(labeling_code_for_indexation(df.part_ref), ';') AS varchar) as code,
       (select code_num from codes where referenced_relation = 'specimen_parts' and record_id = df.part_ref and code_category = 'main' and coalesce(upper(code_prefix),'') != 'RBINS' and code_num is not null limit 1) as code_num,
       labeling_code_for_indexation(df.part_ref) as code_array,
       df.taxon_ref as taxon_ref,
       df.taxon_name as taxon_name,
       df.taxon_name_indexed as taxon_name_indexed,
       df.taxon_path as taxon_path,
       (select phyl.name
        from (select x.id::integer as id from
                 (select regexp_split_to_table(path, '/') as id
                  from taxonomy as taxphyls
                  where taxphyls.id = df.taxon_ref
                 ) as x
              where x.id != ''
             ) as y
             inner join taxonomy as phyl on y.id = phyl.id and phyl.level_ref = 4
       )::varchar as phyl,
       (select clas.name
        from (select x.id::integer as id from
                 (select regexp_split_to_table(path, '/') as id
                  from taxonomy as taxclass
                  where taxclass.id = df.taxon_ref
                 ) as x
              where x.id != ''
             ) as y
             inner join taxonomy as clas on y.id = clas.id and clas.level_ref = 12
       )::varchar as clas,
       (select ordo.name
        from (select x.id::integer as id from
                 (select regexp_split_to_table(path, '/') as id
                  from taxonomy as taxord
                  where taxord.id = df.taxon_ref
                 ) as x
              where x.id != ''
             ) as y
             inner join taxonomy as ordo on y.id = ordo.id and ordo.level_ref = 28
       )::varchar as ordo,
       (select fam.name 
        from (select x.id::integer as id from
                 (select regexp_split_to_table(path, '/') as id 
                  from taxonomy as taxfam
                  where taxfam.id = df.taxon_ref
                 ) as x 
              where x.id != ''
             ) as y
             inner join taxonomy as fam on y.id = fam.id and fam.level_ref = 34
       )::varchar as family,
       (select ct.name 
        from taxonomy as ct inner join classification_synonymies as cs on cs.referenced_relation = 'taxonomy' and cs.record_id = ct.id and is_basionym = true
        where group_id = (select group_id 
                          from classification_synonymies 
                          where referenced_relation = 'taxonomy' and record_id = df.taxon_ref and group_name = 'rename'
                         )
       )::varchar as current_name,
       case when df.acquisition_category is not null and trim(df.acquisition_category) !='' then 'Acq.: ' || df.acquisition_category else '' end as acquisition_category,
       df.gtu_ref as gtu_ref,
       df.gtu_country_tag_value::varchar as countries,
       df.gtu_country_tag_indexed as countries_array,
       df.gtu_province_tag_value::varchar as provinces,
       df.gtu_province_tag_indexed as provinces_array,
       df.gtu_others_tag_value::varchar as other_gtus,
       df.gtu_others_tag_indexed as other_gtus_array,
       case when trim(df.gtu_code) in ('', '/', '0', '0/') then '' else 'Code: ' || trim(df.gtu_code) end as location_code,
       case when df.gtu_from_date_mask >= 32 then 'Sampling dates: ' || to_char(df.gtu_from_date, 'DD/MM/YYYY') else '' end || case when df.gtu_to_date_mask >= 32 then ' - ' || to_char(df.gtu_to_date, 'DD/MM/YYYY') else '' end as gtu_date,
       case when df.gtu_location is not null then 'Lat.Long.: ' || trunc((ST_Y(ST_Centroid(geometry(location))))::numeric, 6) || '/' || trunc((ST_X(ST_Centroid(geometry(location))))::numeric, 6)
       case when df.gtu_elevation is not null then 'Elevation: ' || trunc(df.gtu_elevation::numeric,2) || 'm' || case when df.gtu_elevation_accuracy is not null then ' +- ' || trunc(df.gtu_elevation_accuracy::numeric,2) || 'm' else '' end else '' end as elevation,
       (select 'Coll.: ' || array_to_string(array_agg(people_list), ' - ') 
        from (select trim(formated_name) as people_list 
              from catalogue_people as cp inner join people as peo on cp.people_ref = peo.id 
              where cp.people_type = 'collector' and cp.referenced_relation = 'specimens' and cp.record_id = df.spec_ref order by cp.order_by
             ) as x
       )::varchar as collectors,
       (select 'Dét.: ' || array_to_string(array_agg(people_list), ' - ') 
        from (select trim(formated_name) as people_list 
              from (catalogue_people as cp inner join people as peo on cp.people_ref = peo.id) inner join identifications as ident on cp.record_id = ident.id and cp.referenced_relation = 'identifications' and cp.people_type = 'identifier' 
              where ident.referenced_relation = 'specimens' and ident.record_id = df.spec_ref order by cp.order_by
             ) as x
       )::varchar as identifiers,
       (select 'Don.: ' || array_to_string(array_agg(people_list), ' - ')
        from (select trim(formated_name) as people_list
              from catalogue_people as cp inner join people as peo on cp.people_ref = peo.id
              where cp.people_type = 'donator' and cp.referenced_relation = 'specimens' and cp.record_id = df.spec_ref order by cp.order_by
             ) as x
       )::varchar as donators,
       coalesce(df.ig_num, '-') as ig_num,
       df.ig_num_indexed as ig_num_indexed,
       convert_to_integer(coalesce(ig_num, '-')) as ig_numeric,
       case when df.part_count_min <> df.part_count_max and df.part_count_min is not null and df.part_count_max is not null then 'Count: ' || df.part_count_min || ' - ' || df.part_count_max else case when df.part_count_min is not null then 'Count: ' || df.part_count_min else '' end end as specimen_number,
       case when exists(select 1 from comments where (referenced_relation = 'specimens' and record_id = df.spec_ref) or (referenced_relation = 'specimen_parts' and record_id = df.part_ref)) then 'Comm.?: Y' else 'Comm.?: N' end as comments
from darwin_flat as df
where part_ref is not null;

ALTER VIEW "public"."labeling" OWNER TO darwin2;
GRANT SELECT ON "public"."labeling" TO d2viewer;

DROP INDEX IF EXISTS idx_specimen_individuals_sex;
DROP INDEX IF EXISTS idx_specimen_individuals_stage;
CREATE INDEX CONCURRENTLY idx_specimen_individuals_sex on specimen_individuals(sex) where sex not in ('undefined', 'unknown');
CREATE INDEX CONCURRENTLY idx_specimen_individuals_stage on specimen_individuals(stage) WHERE stage not in ('undefined', 'unknown');

