/* Modification of an existing function to make it immutable */

CREATE OR REPLACE FUNCTION convert_to_integer(v_input varchar) RETURNS INTEGER IMMUTABLE
AS $$
DECLARE v_int_value INTEGER DEFAULT 0;
BEGIN
    BEGIN
        v_int_value := v_input::INTEGER;
    EXCEPTION WHEN OTHERS THEN
/*        RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', v_input;*/
        RETURN 0;
    END;
RETURN v_int_value;
END;
$$ LANGUAGE plpgsql;

create or replace function labeling_country_for_indexation_array(in gtu_ref gtu.id%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
select array_agg(tags_list)
from (select lineToTagRows(tag_value) as tags_list 
      from tag_groups as tg 
      where tg.gtu_ref = $1 and tg.sub_group_name = 'country'
     ) as x;
$$;

GRANT EXECUTE ON FUNCTION labeling_country_for_indexation_array(gtu.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_country_for_indexation_array(gtu.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_country_for_indexation_array(gtu.id%TYPE) OWNER TO darwin2;

DROP INDEX IF EXISTS idx_labeling_country;
CREATE INDEX idx_labeling_country ON darwin_flat USING gin (labeling_country_for_indexation_array(gtu_ref)) WHERE part_ref IS NOT NULL;

create or replace function labeling_province_for_indexation_array(in gtu_ref gtu.id%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
select array_agg(tags_list)
from (select lineToTagRows(tag_value) as tags_list 
      from tag_groups as tg 
      where tg.gtu_ref = $1 and tg.sub_group_name = 'province'
     ) as x;
$$;

create or replace function labeling_province_for_indexation(in gtu_ref gtu.id%TYPE) returns varchar language SQL IMMUTABLE as
$$
select array_to_string(x.tags_list,';')
from (select array_agg(y.tags_list) as tags_list
      from (select lineToTagRowsFormatConserved(tag_value) as tags_list
            from tag_groups as tg
            where tg.gtu_ref = $1 and tg.sub_group_name = 'province'
           ) as y
     ) as x;
$$;

GRANT EXECUTE ON FUNCTION labeling_province_for_indexation_array(gtu.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_province_for_indexation_array(gtu.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_province_for_indexation_array(gtu.id%TYPE) OWNER TO darwin2;
GRANT EXECUTE ON FUNCTION labeling_province_for_indexation(gtu.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_province_for_indexation(gtu.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_province_for_indexation(gtu.id%TYPE) OWNER TO darwin2;

DROP INDEX IF EXISTS idx_labeling_province;
CREATE INDEX idx_labeling_province ON darwin_flat USING gin (labeling_province_for_indexation_array(gtu_ref)) WHERE part_ref IS NOT NULL;

create or replace function labeling_other_gtu_for_indexation_array(in gtu_ref gtu.id%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
select array_agg(tags_list)
from (select lineToTagRows(tag_value) as tags_list 
      from tag_groups as tg 
      where tg.gtu_ref = $1 and tg.sub_group_name not in ('country','province')
     ) as x;
$$;

create or replace function labeling_other_gtu_for_indexation(in gtu_ref gtu.id%TYPE) returns varchar language SQL IMMUTABLE as
$$
select array_to_string(x.tags_list,';')
from (select array_agg(y.tags_list) as tags_list
      from (select lineToTagRowsFormatConserved(tag_value) as tags_list
            from tag_groups as tg
            where tg.gtu_ref = $1 and tg.sub_group_name not in ('province', 'country')
           ) as y
     ) as x;
$$;

GRANT EXECUTE ON FUNCTION labeling_other_gtu_for_indexation_array(gtu.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_other_gtu_for_indexation_array(gtu.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_other_gtu_for_indexation_array(gtu.id%TYPE) OWNER TO darwin2;
GRANT EXECUTE ON FUNCTION labeling_other_gtu_for_indexation(gtu.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_other_gtu_for_indexation(gtu.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_other_gtu_for_indexation(gtu.id%TYPE) OWNER TO darwin2;

DROP INDEX IF EXISTS idx_labeling_other_gtu;
CREATE INDEX idx_labeling_other_gtu ON darwin_flat USING gin (labeling_other_gtu_for_indexation_array(gtu_ref)) WHERE part_ref IS NOT NULL;

create or replace function labeling_code_for_indexation(in part_ref specimen_parts.id%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
select array_agg(coding)
from (select trim(coalesce(code_prefix, '') || coalesce(code_prefix_separator, '') || coalesce(code, '') || coalesce(code_suffix_separator, '') || coalesce(code_suffix, ''))::varchar as coding
      from codes
      where referenced_relation = 'specimen_parts'
        and record_id = $1
        and code_category = 'main'
        and code_prefix != 'RBINS'
     ) as x;
$$;

create or replace function labeling_code_num_for_indexation(in part_ref specimen_parts.id%TYPE) returns codes.code_num%TYPE language SQL IMMUTABLE as
$$
select code_num
from codes
where referenced_relation = 'specimen_parts'
  and record_id = $1
  and code_category = 'main'
  and code_prefix != 'RBINS'
limit 1;
$$;

GRANT EXECUTE ON FUNCTION labeling_code_for_indexation(specimen_parts.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_code_for_indexation(specimen_parts.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_code_for_indexation(specimen_parts.id%TYPE) OWNER TO darwin2;
GRANT EXECUTE ON FUNCTION labeling_code_num_for_indexation(specimen_parts.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_code_num_for_indexation(specimen_parts.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_code_num_for_indexation(specimen_parts.id%TYPE) OWNER TO darwin2;

DROP INDEX IF EXISTS idx_labeling_code;
DROP INDEX IF EXISTS idx_labeling_code_varchar;
DROP INDEX IF EXISTS idx_labeling_code_numeric;
CREATE INDEX idx_labeling_code ON darwin_flat USING gin (labeling_code_for_indexation(part_ref)) WHERE part_ref IS NOT NULL;
CREATE INDEX idx_labeling_code_varchar ON darwin_flat (CAST(array_to_string(labeling_code_for_indexation(part_ref), ';') AS varchar)) WHERE part_ref IS NOT NULL;
CREATE INDEX idx_labeling_code_numeric ON darwin_flat (labeling_code_num_for_indexation(part_ref)) WHERE part_ref IS NOT NULL;

create or replace function labeling_individual_type_for_indexation(in individual_type specimen_individuals.type%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
SELECT case when fullToIndex($1) = 'specimen' then array['-'] else array[coalesce(fullToIndex($1),'/')] end;
$$;

create or replace function labeling_individual_sex_for_indexation(in individual_sex specimen_individuals.sex%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
SELECT case when fullToIndex($1) in ('undefined', 'unknown', 'notstated', 'nonapplicable') then array['-'] else array[coalesce(fullToIndex($1),'/')] end;
$$;

create or replace function labeling_individual_stage_for_indexation(in individual_stage specimen_individuals.stage%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
SELECT case when fullToIndex($1) in ('undefined', 'unknown', 'notstated', 'nonapplicable') then array['-'] else array[coalesce(fullToIndex($1),'/')] end;
$$;

create or replace function labeling_part_for_indexation(in part specimen_parts.specimen_part%TYPE) returns varchar[] language SQL IMMUTABLE as
$$
SELECT case when fullToIndex($1) in ('undefined', 'unknown', 'animal', 'specimen', '') then array['-'] else array[coalesce(fullToIndex($1),'/')] end;
$$;

GRANT EXECUTE ON FUNCTION labeling_individual_type_for_indexation(specimen_individuals.type%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_individual_type_for_indexation(specimen_individuals.type%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_individual_type_for_indexation(specimen_individuals.type%TYPE) OWNER TO darwin2;
GRANT EXECUTE ON FUNCTION labeling_individual_sex_for_indexation(specimen_individuals.sex%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_individual_sex_for_indexation(specimen_individuals.sex%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_individual_sex_for_indexation(specimen_individuals.sex%TYPE) OWNER TO darwin2;
GRANT EXECUTE ON FUNCTION labeling_individual_stage_for_indexation(specimen_individuals.stage%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_individual_stage_for_indexation(specimen_individuals.stage%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_individual_stage_for_indexation(specimen_individuals.stage%TYPE) OWNER TO darwin2;
GRANT EXECUTE ON FUNCTION labeling_part_for_indexation(specimen_parts.specimen_part%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_part_for_indexation(specimen_parts.specimen_part%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_part_for_indexation(specimen_parts.specimen_part%TYPE) OWNER TO darwin2;

DROP INDEX IF EXISTS idx_labeling_individual_type;
CREATE INDEX idx_labeling_individual_type ON darwin_flat using gin (labeling_individual_type_for_indexation(individual_type));
DROP INDEX IF EXISTS idx_labeling_individual_sex;
CREATE INDEX idx_labeling_individual_sex ON darwin_flat using gin (labeling_individual_sex_for_indexation(individual_sex));
DROP INDEX IF EXISTS idx_labeling_individual_stage;
CREATE INDEX idx_labeling_individual_stage ON darwin_flat using gin (labeling_individual_stage_for_indexation(individual_stage));
DROP INDEX IF EXISTS idx_labeling_part;
CREATE INDEX idx_labeling_part ON darwin_flat using gin (labeling_part_for_indexation(part));

DROP INDEX IF EXISTS idx_labeling_ig_num_numeric;
DROP INDEX IF EXISTS idx_labeling_ig_num_coalesced;
CREATE INDEX idx_labeling_ig_num_coalesced ON darwin_flat(coalesce(ig_num, '-')) where part_ref is not null;
CREATE INDEX idx_labeling_ig_num_numeric ON darwin_flat(convert_to_integer(coalesce(ig_num, '-'))) where part_ref is not null;

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
       labeling_part_for_indexation(df.part) as part,
       labeling_individual_type_for_indexation(df.individual_type) as type,
       labeling_individual_sex_for_indexation(df.individual_sex) as sex,
       labeling_individual_stage_for_indexation(df.individual_stage) as stage,
       CAST(array_to_string(labeling_code_for_indexation(part_ref), ';') AS varchar) as code,
       labeling_code_num_for_indexation(part_ref) as code_num,
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
       case when df.acquisition_category is not null then 'Acq.: ' || df.acquisition_category else '' end as acquisition_category,
       df.gtu_ref as gtu_ref,
       replace(df.gtu_country_tag_value, ' ', '')::varchar as countries,
       labeling_country_for_indexation_array(df.gtu_ref) as countries_array,
       labeling_province_for_indexation(df.gtu_ref) as provinces,
       labeling_province_for_indexation_array(df.gtu_ref) as provinces_array,
       labeling_other_gtu_for_indexation(df.gtu_ref) as location,
       labeling_other_gtu_for_indexation_array(df.gtu_ref) as location_array,
       case when trim(gtu.code) in ('', '/', '0', '0/') then '' else 'Code: ' || trim(gtu.code) end as location_code,
       case when gtu.gtu_from_date_mask >= 32 then 'Sampling dates: ' || to_char(gtu.gtu_from_date, 'DD/MM/YYYY') else '' end || case when gtu.gtu_to_date_mask >= 32 then ' - ' || to_char(gtu.gtu_to_date, 'DD/MM/YYYY') else '' end as gtu_date,
       case when gtu.latitude is not null and gtu.longitude is not null then 'Lat./Long.: ' || trunc(gtu.latitude::numeric,6) || '/' || trunc(gtu.longitude::numeric,6) || case when gtu.lat_long_accuracy is not null then ' +- ' || trunc(gtu.lat_long_accuracy::numeric,2) || 'm' else '' end else '' end as lat_long,
       case when gtu.elevation is not null then 'Elevation: ' || trunc(gtu.elevation::numeric,2) || 'm' || case when gtu.elevation_accuracy is not null then ' +- ' || trunc(gtu.elevation_accuracy::numeric,2) || 'm' else '' end else '' end as elevation,
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
       convert_to_integer(coalesce(ig_num, '-')) as ig_numeric,
       case when df.part_count_min <> df.part_count_max and df.part_count_min is not null and df.part_count_max is not null then 'Count: ' || df.part_count_min || ' - ' || df.part_count_max else case when df.part_count_min is not null then 'Count: ' || df.part_count_min else '' end end as specimen_number,
       case when exists(select 1 from comments where (referenced_relation = 'specimens' and record_id = df.spec_ref) or (referenced_relation = 'specimen_parts' and record_id = df.part_ref)) then 'Comm.?: Y' else 'Comm.?: N' end as comments
from darwin_flat as df inner join gtu on df.gtu_ref = gtu.id
where part_ref is not null;

ALTER VIEW "public"."labeling" OWNER TO darwin2;
GRANT SELECT ON "public"."labeling" TO d2viewer;
