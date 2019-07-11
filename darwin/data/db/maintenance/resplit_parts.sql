SET search_path = darwin2, darwin1, public;

create index idx_id_refs_old_id on darwin1.id_refs ("system", old_id) where "system" = 'individuals';
create index idx_id_refs_new_id on darwin1.id_refs ("system", new_id) where "system" = 'individuals';
create index idx_buildings on darwin1.tbl_buildings (bui_id_ctn);
create index idx_building_floors on darwin1.tbl_building_floors (bfl_id_ctn);
create index idx_building_floors_bui on darwin1.tbl_building_floors (bfl_building_nr);
create index idx_rooms on darwin1.tbl_rooms (rom_id_ctn);
create index idx_spec_status on darwin1.tbl_specimen_status (sst_id_ctn);
create index idx_spec_storages on darwin1.tbl_storage (sto_id_ctn);
create index idx_spec_cont_types on darwin1.tbl_container_types (cty_id_ctn);
create index idx_spec_freshness on darwin1.tbl_spec_freshness_levels (sfl_level);
create index idx_spec_units on darwin1.tbl_units (uni_id_ctn);
create index idx_batches on darwin1.tbl_batches (bat_id_ctn);
create index idx_spec_groups on darwin1.tbl_specimen_groups (sgr_id_ctn);
create index idx_spec_spec_sst on darwin1.tbl_specimen_groups (sgr_status_nr);
create index idx_spec_spec_prep on darwin1.tbl_specimen_groups (sgr_preparator_nr);
create index idx_spec_spec_batch on darwin1.tbl_specimen_groups (sgr_batch_nr);
create index idx_spec_spec_freshness on darwin1.tbl_specimen_groups (sgr_freshness_level_nr);
create index idx_spec_spec_room on darwin1.tbl_specimen_groups (sgr_room_nr);
create index idx_spec_spec_item on darwin1.tbl_specimen_groups (sgr_item_concerned_nr);
create index idx_spec_spec_storage on darwin1.tbl_specimen_groups (sgr_storage_nr);
create index idx_spec_spec_cont_type on darwin1.tbl_specimen_groups (sgr_container_type_nr);
create index idx_spec_spec_weight_unit on darwin1.tbl_specimen_groups (sgr_weight_min_uni_nr);
create index idx_spec_spec_height_unit on darwin1.tbl_specimen_groups (sgr_height_min_uni_nr);
create index idx_spec_spec_depth_unit on darwin1.tbl_specimen_groups (sgr_depth_min_uni_nr);

/*drop table if exists darwin2.migrated_parts CASCADE;*/

create table darwin2.migrated_parts as
(
 select sgr_id_ctn::integer as old_spec_id,
        specimen_individual_ref::integer as specimen_individual_ref,  specimen_parts.id::bigint as parts_id, (fullToIndex(sgr_code))::varchar as main_code,
        (fullToIndex(bat_unique_rbins_code))::varchar as rbins_code, (fullToIndex(bat_code))::varchar as batch_main_code, (fullToIndex(bat_inventory_code))::varchar as inventory_code, sgr_code::varchar as old_main_code,
        specimen_part::varchar as specimen_part, building::varchar, (coalesce(building, ''))::varchar as coalesced_building,
        ("floor")::varchar, (coalesce("floor", ''))::varchar as coalesced_floor,
        room::varchar, (coalesce("room", ''))::varchar as coalesced_room,
        (specimen_parts.row)::varchar as row, (coalesce("row", ''))::varchar as coalesced_row,
        (specimen_parts.shelf)::varchar as shelf, (coalesce("shelf", ''))::varchar as coalesced_shelf,
        (specimen_parts.container)::varchar as container, (coalesce("container", ''))::varchar as coalesced_container,
        (specimen_parts.sub_container)::varchar as sub_container, (coalesce("sub_container", ''))::varchar as coalesced_sub_container,
        (lower(cty_type_short_descr))::varchar as container_and_sub_container_type, (specimen_parts.container_type)::varchar as container_type, (specimen_parts.sub_container_type)::varchar as sub_container_type,
        (lower(sto_storage))::varchar as container_and_sub_container_storage, (specimen_parts.container_storage)::varchar as container_storage, (specimen_parts.sub_container_storage)::varchar as sub_container_storage,
        specimen_status::varchar as specimen_status,
        case when sgr_number_in_group < 1 or sgr_number_in_group is null then 1 else sgr_number_in_group end::bigint as part_count_min,
        specimen_part_count_min::integer as specimen_part_count_min,
        specimen_part_count_max::integer as specimen_part_count_max,
        case
          when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true
          else false
        end::boolean as complete,
        case when sfl_description = 'Undefined' then '' else coalesce(sfl_description,'') end::varchar as freshness_level,
        sgr_length_min::varchar as old_length_min,
        (convert_to_unified(sgr_length_min::varchar, case when length_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(length_unit.uni_unit,'') END, 'length'))::varchar as length_min_unified,
        sgr_length_max::varchar as old_length_max,
        (convert_to_unified(sgr_length_max::varchar, case when length_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(length_unit.uni_unit,'') END, 'length'))::varchar as length_max_unified,
        case when length_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(length_unit.uni_unit,'') END::varchar as old_length_unit,
        sgr_height_min::varchar as old_height_min,
        (convert_to_unified(sgr_height_min::varchar, case when height_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(height_unit.uni_unit,'') END, 'length'))::varchar as height_min_unified,
        sgr_height_max::varchar as old_height_max,
        (convert_to_unified(sgr_height_max::varchar, case when height_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(height_unit.uni_unit,'') END, 'length'))::varchar as height_max_unified,
        case when height_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(height_unit.uni_unit,'') END::varchar as old_height_unit,
        sgr_depth_min::varchar as old_depth_min,
        (convert_to_unified(sgr_depth_min::varchar, case when depth_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(depth_unit.uni_unit,'') END, 'length'))::varchar as depth_min_unified,
        sgr_depth_max::varchar as old_depth_max,
        (convert_to_unified(sgr_depth_max::varchar, case when depth_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(depth_unit.uni_unit,'') END, 'length'))::varchar as depth_max_unified,
        case when depth_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(depth_unit.uni_unit,'') END::varchar as old_depth_unit,
        sgr_weight_min::varchar as old_weight_min,
        (convert_to_unified(sgr_weight_min::varchar, case when weight_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(weight_unit.uni_unit,'') END, 'weight'))::varchar as weight_min_unified,
        sgr_weight_max::varchar as old_weight_max,
        (convert_to_unified(sgr_weight_max::varchar, case when weight_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(weight_unit.uni_unit,'') END, 'weight'))::varchar as weight_max_unified,
        case when weight_unit.uni_unit = 'Undef.' THEN '' ELSE coalesce(weight_unit.uni_unit,'') END::varchar as old_weight_unit,
        bat_value::numeric as old_insurance_value,
        bat_value_year::smallint as old_insurance_year,
        case when exists(select 1 from people where id = sgr_preparator_nr) then case when sgr_preparator_nr = 0 then null else sgr_preparator_nr end else null::integer end::integer as maintenance_people_ref,
        'action'::varchar as maintenance_category,
        'preparation'::varchar as maintenance_action_observation,
        (
          ('01/' ||
            CASE WHEN sgr_preparation_month IS NULL THEN '01' ELSE sgr_preparation_month::varchar END
            || '/' ||
            CASE WHEN sgr_preparation_year IS NULL THEN '0001' ELSE sgr_preparation_year::varchar END
          )::timestamp
          + (CASE WHEN sgr_preparation_day IS NULL OR sgr_preparation_day = 0 THEN '0' ELSE (sgr_preparation_day::integer - 1)::varchar END || ' days')::interval
        )::timestamp as maintenance_modification_date_time,
        CASE WHEN sgr_preparation_day IS NOT NULL THEN 8 ELSE 0 END
        +
        CASE WHEN sgr_preparation_month IS NOT NULL THEN 16 ELSE 0 END
        +
        CASE WHEN sgr_preparation_year IS NOT NULL THEN 32 ELSE 0 END::integer as maintenance_modification_date_mask,
        coalesce(sgr_comment, '')::varchar as part_comment
  from darwin1.tbl_specimen_groups
  inner join darwin1.id_refs
  on system = 'individuals' and sgr_id_ctn = old_id
  inner join (
              darwin1.tbl_rooms
              inner join (
                          darwin1.tbl_building_floors
                          inner join darwin1.tbl_buildings
                          on bui_id_ctn = bfl_building_nr
                        )
              on bfl_id_ctn = rom_building_floor_nr
            )
  on sgr_room_nr = rom_id_ctn
  inner join darwin1.tbl_specimen_status
  on sst_id_ctn = sgr_status_nr
  inner join darwin1.tbl_storage on sto_id_ctn = sgr_storage_nr
  inner join darwin1.tbl_container_types on cty_id_ctn = sgr_container_type_nr
  inner join darwin1.tbl_batches on sgr_batch_nr = bat_id_ctn
  inner join darwin1.tbl_spec_freshness_levels on sfl_level = sgr_freshness_level_nr
  inner join darwin1.tbl_units as length_unit on sgr_length_min_uni_nr = length_unit.uni_id_ctn
  inner join darwin1.tbl_units as height_unit on sgr_height_min_uni_nr = height_unit.uni_id_ctn
  inner join darwin1.tbl_units as depth_unit on sgr_depth_min_uni_nr = depth_unit.uni_id_ctn
  inner join darwin1.tbl_units as weight_unit on sgr_weight_min_uni_nr = weight_unit.uni_id_ctn
  inner join darwin1.tbl_units as vol_unit on sgr_vol_min_uni_nr = vol_unit.uni_id_ctn
  inner join
  specimen_parts
  on "system" = 'individuals' and specimen_individual_ref = new_id
    and specimen_part = lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation'))
    and coalesce(specimen_parts.building,'') =
        case
          when sgr_room_nr = 0 then ''
          else bui_name
        end::varchar
    and coalesce(specimen_parts.floor,'') =
        case
          when sgr_room_nr = 0 then ''
          else bfl_floor
        end::varchar
    and coalesce(specimen_parts.room, '') =
        case
          when sgr_room_nr = 0 then ''
          else rom_code
        end::varchar
    and coalesce(specimen_parts.row, '') = coalesce(sgr_row, '')
    and coalesce(specimen_parts.shelf, '') = coalesce(sgr_shelf, '')
    and coalesce(specimen_parts.container, '') = coalesce(sgr_container, '')
    and coalesce(specimen_parts.sub_container, '') = coalesce(sgr_subcontainer, '')
    and complete =
        case
          when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true
          else false
        end
  where
      not exists (select 1
                  from users_tracking
                  where referenced_relation = 'codes'
                    and action = 'delete'
                    and old_value -> 'referenced_relation' = 'specimen_parts'
                    and old_value -> 'record_id' = specimen_parts.id::varchar
                    and old_value -> 'code_category' = 'main'
                    and old_value -> 'full_code_order_by' = fullToIndex(sgr_code)
                  )

/*>Remove when ready*/
--     and sgr_length_min is not null and sgr_length_min != 0
--   limit 100
/*<Remove when ready*/
);

CREATE INDEX idx_migrated_parts ON migrated_parts (specimen_individual_ref desc, parts_id, specimen_part, main_code);

ALTER TABLE migrated_parts OWNER TO darwin2;

BEGIN;

ALTER TABLE specimen_parts DISABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;
ALTER TABLE specimen_parts DISABLE TRIGGER fct_cpy_trg_ins_update_dict_specimen_parts;
ALTER TABLE specimen_parts DISABLE TRIGGER trg_chk_specimenpartcollectionallowed;
ALTER TABLE specimen_parts DISABLE TRIGGER trg_cpy_path_specimen_parts;
ALTER TABLE specimen_parts DISABLE TRIGGER trg_trk_log_table_specimen_parts;
ALTER TABLE specimen_parts DISABLE TRIGGER trg_update_specimen_parts_darwin_flat;
ALTER TABLE codes DISABLE TRIGGER trg_chk_ref_record_catalogue_codes;
ALTER TABLE codes DISABLE TRIGGER trg_trk_log_table_codes;
ALTER TABLE codes DISABLE TRIGGER fct_cpy_trg_del_dict_codes;
ALTER TABLE codes DISABLE TRIGGER fct_cpy_trg_ins_update_dict_codes;
ALTER TABLE catalogue_properties DISABLE TRIGGER trg_chk_ref_record_catalogue_properties;
ALTER TABLE catalogue_properties DISABLE TRIGGER trg_trk_log_table_catalogue_properties;
ALTER TABLE properties_values DISABLE TRIGGER trg_trk_log_table_properties_values;
ALTER TABLE collection_maintenance DISABLE TRIGGER trg_chk_ref_record_collection_maintenance;
ALTER TABLE collection_maintenance DISABLE TRIGGER trg_trk_log_table_collection_maintenance;
ALTER TABLE collection_maintenance DISABLE TRIGGER fct_cpy_trg_del_dict_collection_maintenance;
ALTER TABLE collection_maintenance DISABLE TRIGGER fct_cpy_trg_ins_update_dict_collection_maintenance;
ALTER TABLE insurances DISABLE TRIGGER trg_chk_ref_record_insurances;
ALTER TABLE insurances DISABLE TRIGGER trg_trk_log_table_insurances;
ALTER TABLE insurances DISABLE TRIGGER fct_cpy_trg_del_dict_insurances;
ALTER TABLE insurances DISABLE TRIGGER fct_cpy_trg_ins_update_dict_insurances;
ALTER TABLE comments DISABLE TRIGGER trg_chk_ref_record_comments;
ALTER TABLE comments DISABLE TRIGGER trg_trk_log_table_comments;

CREATE OR REPLACE FUNCTION convert_to_real(v_input varchar) RETURNS REAL IMMUTABLE
AS $$
DECLARE v_int_value REAL DEFAULT 0;
BEGIN
    BEGIN
        v_int_value := v_input::REAL;
    EXCEPTION WHEN OTHERS THEN
/*        RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', v_input;*/
        RETURN 0;
    END;
RETURN v_int_value;
END;
$$ LANGUAGE plpgsql;

DROP TYPE IF EXISTS recPartsDetail CASCADE;
CREATE TYPE recPartsDetail AS (
                                old_spec_id integer,
                                specimen_individual_ref integer,
                                parts_id bigint,
                                main_code varchar,
                                rbins_code varchar,
                                batch_main_code varchar,
                                inventory_code varchar,
                                old_main_code varchar,
                                specimen_part varchar,
                                building varchar,
                                coalesced_building varchar,
                                "floor" varchar,
                                coalesced_floor varchar,
                                room varchar,
                                coalesced_room varchar,
                                "row" varchar,
                                coalesced_row varchar,
                                shelf varchar,
                                coalesced_shelf varchar,
                                container varchar,
                                coalesced_container varchar,
                                sub_container varchar,
                                coalesced_sub_container varchar,
                                container_and_sub_container_type varchar,
                                container_type varchar,
                                sub_container_type varchar,
                                container_and_sub_container_storage varchar,
                                container_storage varchar,
                                sub_container_storage varchar,
                                specimen_status varchar,
                                part_count_min bigint,
                                specimen_part_count_min integer,
                                specimen_part_count_max integer,
                                complete boolean,
                                freshness_level varchar,
                                old_length_min varchar,
                                old_length_min_unified varchar,
                                old_length_max varchar,
                                old_length_max_unified varchar,
                                old_length_unit varchar,
                                old_height_min varchar,
                                old_height_min_unified varchar,
                                old_height_max varchar,
                                old_height_max_unified varchar,
                                old_height_unit varchar,
                                old_depth_min varchar,
                                old_depth_min_unified varchar,
                                old_depth_max varchar,
                                old_depth_max_unified varchar,
                                old_depth_unit varchar,
                                old_weight_min varchar,
                                old_weight_min_unified varchar,
                                old_weight_max varchar,
                                old_weight_max_unified varchar,
                                old_weight_unit varchar,
                                old_insurance_value numeric,
                                old_insurance_year smallint,
                                maintenance_people_ref integer,
                                maintenance_category varchar,
                                maintenance_action_observation varchar,
                                maintenance_modification_date_time timestamp,
                                maintenance_modification_date_mask integer,
                                part_comment varchar
                              );

create or replace function decrementCount(IN partId specimen_parts.id%TYPE, IN decrementVal bigint) RETURNS boolean language plpgsql AS
$$
declare
  result boolean := true;
begin
  UPDATE specimen_parts
  SET specimen_part_count_min = case when specimen_part_count_min = 1 then specimen_part_count_min else case when specimen_part_count_min - decrementVal < 1 then 1 else specimen_part_count_min - decrementVal end end,
      specimen_part_count_max = case when specimen_part_count_max = 1 then specimen_part_count_max else case when specimen_part_count_max - decrementVal < 1 then 1 else specimen_part_count_max - decrementVal end end
  WHERE id = partId;
  return result;
exception when others then
  RAISE WARNING 'Error in decrementCount: %', SQLERRM;
  return false;
end;
$$;

create or replace function createCodes(IN new_part_id specimen_parts.id%TYPE, IN codeToSplit varchar) returns integer language plpgsql AS
$$
declare
  codes_prefix varchar;
  codes_prefix_separator varchar;
  code_to_insert varchar;
  codes_suffix_separator varchar;
  codes_suffix varchar;
  code_temp varchar;
  response integer := 0;
begin
  IF codeToSplit = 'A.193' THEN
    codes_prefix := 'A';
    codes_prefix_separator := '.';
    code_to_insert:= '193';
  ELSIF codeToSplit = 'A/G/26' THEN
    codes_prefix := 'A/G';
    codes_prefix_separator := '/';
    code_to_insert:= '26';
  ELSIF codeToSplit = 'A.G306' THEN
    codes_prefix := 'A';
    codes_prefix_separator := '.';
    code_to_insert:= 'G306';
  ELSIF codeToSplit = 'HIOL.1054' THEN
    codes_prefix := 'HOL';
    codes_prefix_separator := '.';
    code_to_insert:= '1054';
  ELSIF codeToSplit = 'IINV.62442' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    code_to_insert:= '62442';
  ELSIF codeToSplit = 'IN16124' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    code_to_insert:= '16124';
  ELSIF codeToSplit = 'IV.17792' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    code_to_insert:= '17792';
  ELSIF codeToSplit = 'IV.84836' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    code_to_insert:= '84836';
  ELSIF codeToSplit = 'T1987' THEN
    codes_prefix := 'T';
    codes_prefix_separator := '.';
    code_to_insert:= '1987';
  ELSIF codeToSplit = 'V.78427' THEN
    codes_prefix := 'V';
    codes_prefix_separator := '.';
    code_to_insert:= '78427';
  ELSIF substr(codeToSplit, 1, 3) ='IN.' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 3 THEN
      code_to_insert:= substr(codeToSplit, 4);
    END IF;
  ELSIF codeToSplit ~ E'^[PRT]\\s{1,2}\\d+\\s{1}\\w+\\W+\\w+$' THEN
    codes_prefix := substr(codeToSplit, 1, 1);
    codes_prefix_separator := ' ';
    code_to_insert:= trim(substring(trim(substr(codeToSplit, 2)) from E'^\\d+\\s{1}'));
    codes_suffix_separator := ' ';
    codes_suffix := substring(codeToSplit from E'\\w+\\W+\\w+$');
  ELSIF codeToSplit Like 'ALEX %' THEN
    code_to_insert:= codeToSplit;
  ELSIF codeToSplit ~ E'^[a-zA-Z]+[\\s.?#&\\B/\;]*' THEN
    codes_prefix := substring(codeToSplit from E'^[a-zA-Z]+');
    codes_prefix_separator := substring(substr(codeToSplit, length(codes_prefix)+1) from E'^\\s+');
    IF length(codes_prefix_separator) > 0 then
      codes_prefix_separator := ' ';
    ELSE
      codes_prefix_separator := substring(substr(codeToSplit, length(codes_prefix)+1) from E'^[\\B]+');
      IF length(codes_prefix_separator) > 0 then
        codes_prefix_separator := E'\\B';
      ELSE
        codes_prefix_separator := substring(substr(codeToSplit, length(codes_prefix)+1) from E'^/+');
        IF length(codes_prefix_separator) > 0 then
          codes_prefix_separator := '/';
        ELSE
          codes_prefix_separator := '.';
        END IF;
      END IF;
    END IF;
    code_temp := substr(codeToSplit, length(codes_prefix)+ length(substring(substr(codeToSplit, length(codes_prefix)+1) from E'^[\\s.?#&\\B/\;]*'))+1);
    IF code_temp != '' THEN
      IF code_temp ~ E'^\\d+' THEN
        IF code_temp ~ E'^\\d+[a-zA-Z\\s.\\B/&?#\;]+' THEN
          code_to_insert := substring(code_temp from E'^\\d+');
          codes_suffix_separator := trim(substring(substr(code_temp, length(code_to_insert)+1) from E'^[\\s.?#&\\B/\;]*'));
          IF coalesce(codes_suffix_separator,'') != '' THEN
            codes_suffix := trim(substr(code_temp,length(code_to_insert)+ length(substring(substr(code_temp, length(code_to_insert)+1) from E'^[\\s.?#&\\B/\;]*'))+1));
          ELSE
            codes_suffix := trim(substr(code_temp, length(code_to_insert)+1));
          END IF;
        ELSE
          code_to_insert := trim(code_temp);
        END IF;
      ELSE
        code_to_insert := trim(code_temp);
      END IF;
    END IF;
  ELSIF codeToSplit ~ E'^\\d+[\\s.?#&\\B/\;]*[a-zA-Z\\d]+' THEN
    code_to_insert := substring(codeToSplit from E'^\\d+');
    codes_suffix_separator := substring(substr(codeToSplit, length(code_to_insert)+1) from E'^\\s+');
    IF length(codes_suffix_separator) > 0 then
      codes_suffix_separator := ' ';
    ELSE
      codes_suffix_separator := substring(substr(codeToSplit, length(code_to_insert)+1) from E'^[\\B]+');
      IF length(codes_suffix_separator) > 0 then
        codes_suffix_separator := E'\\B';
      ELSE
        codes_suffix_separator := substring(substr(codeToSplit, length(code_to_insert)+1) from E'^/+');
        IF length(codes_suffix_separator) > 0 then
          codes_suffix_separator := '/';
        ELSE
          codes_suffix_separator := '.';
        END IF;
      END IF;
    END IF;
    IF coalesce(codes_suffix_separator,'') != '' THEN
      codes_suffix := trim(substr(codeToSplit,length(code_to_insert)+ length(substring(substr(codeToSplit, length(code_to_insert)+1) from E'^[\\s.?#&\\B/\;]+'))+1));
    ELSE
      codes_suffix := trim(substr(codeToSplit, length(code_to_insert)+1));
    END IF;
  ELSE
    return response;
  END IF;
  INSERT INTO codes
  (referenced_relation, record_id, code_category, "code_prefix", "code_prefix_separator", "code", "code_suffix", "code_suffix_separator")
  VALUES
  ('specimen_parts', new_part_id, 'main', codes_prefix, codes_prefix_separator, code_to_insert, codes_suffix, codes_suffix_separator);
  GET DIAGNOSTICS response := ROW_COUNT;
  return response;
exception
  when others then
    RAISE WARNING 'Error in createCodes: %', SQLERRM;
    return -1;
end;
$$;

create or replace function createNewPart(IN part_id specimen_parts.id%TYPE, IN recPartsDetails recPartsDetail, IN new_code_id codes.id%TYPE DEFAULT NULL) RETURNS specimen_parts.id%TYPE language plpgsql AS
$$
DECLARE
  new_part_id specimen_parts.id%TYPE;
  code_count integer;
BEGIN
    INSERT INTO specimen_parts
    (parent_ref, path, specimen_individual_ref, specimen_part, complete,
      building, "floor", room, "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage,
      surnumerary, specimen_status, specimen_part_count_min, specimen_part_count_max, institution_ref
    )
    (
      SELECT parent_ref, path, specimen_individual_ref, specimen_part, complete,
            building, "floor", room, "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage,
            surnumerary, specimen_status, recPartsDetails.part_count_min, recPartsDetails.part_count_min, institution_ref
      FROM specimen_parts
      WHERE id = part_id
    )
    RETURNING id INTO new_part_id;
    if decrementCount(part_id, recPartsDetails.part_count_min) then
    end if;
    SELECT count(full_code_order_by) INTO code_count
    FROM codes
    WHERE referenced_relation = 'specimen_parts'
      AND code_category = 'main'
      AND record_id = part_id;
--     RAISE NOTICE '°Code count in new part creation: %', code_count;
    IF code_count > 1 THEN
      UPDATE codes
      SET record_id = new_part_id
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND code_category = 'main'
        AND (full_code_order_by IN (recPartsDetails.main_code, recPartsDetails.rbins_code, recPartsDetails.inventory_code, recPartsDetails.batch_main_code)
             OR
             CASE WHEN coalesce(new_code_id,0) = 0 THEN false ELSE id = new_code_id END
            );
      GET DIAGNOSTICS code_count = ROW_COUNT;
--       RAISE NOTICE '°°Number of records updated: %', code_count;
      IF code_count = 0 THEN
        IF createCodes (new_part_id, recPartsDetails.old_main_code) < 0 THEN
          return -1;
        END IF;
      END IF;
    ELSIF code_count = 1 THEN
      INSERT INTO codes
      (referenced_relation, record_id, code_category, code_prefix, code_prefix_separator, code, code_suffix, code_suffix_separator, code_date, code_date_mask)
      (SELECT 'specimen_parts', new_part_id, code_category, code_prefix, code_prefix_separator, code, code_suffix, code_suffix_separator, code_date, code_date_mask
       FROM codes
       WHERE referenced_relation = 'specimen_parts'
         AND record_id = part_id
         AND code_category = 'main'
         AND (full_code_order_by IN (recPartsDetails.main_code, recPartsDetails.rbins_code, recPartsDetails.inventory_code, recPartsDetails.batch_main_code)
              OR
              CASE WHEN coalesce(new_code_id,0) = 0 THEN false ELSE id = new_code_id END
             )
      );
      GET DIAGNOSTICS code_count = ROW_COUNT;
--       RAISE NOTICE '°°Number of records inserted: %', code_count;
      IF code_count = 0 THEN
        IF createCodes (new_part_id, recPartsDetails.old_main_code) < 0 THEN
          return -1;
        END IF;
      END IF;
    ELSE
      IF createCodes (new_part_id, recPartsDetails.old_main_code) < 0 THEN
        return -1;
      END IF;
    END IF;
    /* Comments are not treated: only two are in identifiable corresponding old parts, but do not need to be splitted*/
    return new_part_id;
EXCEPTION
  WHEN OTHERS THEN
    RAISE WARNING 'Error in createNewPart: %', SQLERRM;
    return -1;
END;
$$;

CREATE OR REPLACE FUNCTION column_exists(colname text, tablename text) RETURNS boolean LANGUAGE plpgsql AS
$$
DECLARE
  q text;
  onerow record;
BEGIN
  q = 'SELECT attname FROM pg_attribute WHERE attrelid = ( SELECT oid FROM pg_class WHERE relname = ' || quote_literal(tablename) || ') AND attname = ' || quote_literal(colname);
  FOR onerow IN EXECUTE q
  LOOP
    RETURN true;
  END LOOP;
  RETURN false;
END;
$$;

create or replace function moveOrCreateProp (IN part_id specimen_parts.id%TYPE, IN new_part_id specimen_parts.id%TYPE, IN recPartsDetails recPartsDetail, IN recFirstPartsDetails recPartsDetail) returns integer language plpgsql AS
$$
declare
  prop_count integer;
  update_count integer;
--   insert_count integer;
  cat_prop_id integer;
  booUpdate boolean := false;
  booContinue boolean := false;
begin
  /* Comments */
  IF recPartsDetails.part_comment != '' THEN
    INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
    (
      SELECT 'specimen_parts', new_part_id, 'general', recPartsDetails.part_comment
      WHERE NOT EXISTS (
                        SELECT 1
                        FROM comments
                        WHERE referenced_relation = 'specimen_parts'
                          AND record_id = new_part_id
                          AND notion_concerned = 'general'
                       )
    );
--     GET DIAGNOSTICS insert_count = ROW_COUNT;
--     IF coalesce(insert_count,0) != 0 THEN
--       RAISE NOTICE 'Comment inserted';
--     END IF;
  END IF;
  /*Freshness Level*/
  IF coalesce(recPartsDetails.freshness_level, '') != '' THEN
    SELECT COUNT(*)
    INTO prop_count
    FROM catalogue_properties INNER JOIN properties_values ON catalogue_properties.id = properties_values.property_ref
    WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'part state' AND property_sub_type = 'freshness level' AND property_value = recPartsDetails.freshness_level;
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type)
    (
      SELECT 'specimen_parts', new_part_id, 'part state', 'freshness level'
      WHERE NOT EXISTS (SELECT 1
                        FROM catalogue_properties
                        WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'part state' AND property_sub_type = 'freshness level'
                       )
    );
    IF prop_count >= 1 THEN
      IF prop_count > 1 OR (prop_count = 1 AND recPartsDetails.freshness_level != recFirstPartsDetails.freshness_level) THEN
        booUpdate := true;
      ELSIF prop_count = 1 AND recPartsDetails.freshness_level = recFirstPartsDetails.freshness_level THEN
        booContinue := true;
      END IF;
      IF booUpdate THEN
        UPDATE properties_values
        SET property_ref = (SELECT id
                            FROM catalogue_properties
                            WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'part state' AND property_sub_type = 'freshness level'
                          )
        WHERE property_ref = (SELECT DISTINCT catalogue_properties.id
                              FROM catalogue_properties INNER JOIN properties_values ON catalogue_properties.id = properties_values.property_ref
                              WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'part state' AND property_sub_type = 'freshness level' AND property_value = recPartsDetails.freshness_level
                              LIMIT 1
                            )
          AND property_value_unified = recPartsDetails.freshness_level
          AND id IN (SELECT properties_values.id
                     FROM catalogue_properties INNER JOIN properties_values ON catalogue_properties.id = properties_values.property_ref
                     WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'part state' AND property_sub_type = 'freshness level' AND property_value = recPartsDetails.freshness_level
                     LIMIT 1
                    );
        GET DIAGNOSTICS update_count = ROW_COUNT;
        IF update_count = 0 THEN
          booContinue := true;
        END IF;
      END IF;
    ELSE
      booContinue := true;
    END IF;
    IF booContinue THEN
      INSERT INTO properties_values (property_ref, property_value)
      (SELECT id, recPartsDetails.freshness_level
        FROM catalogue_properties
        WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'part state' AND property_sub_type = 'freshness level'
      );
    END IF;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Weight level*/
  SELECT
  NOT EXISTS (SELECT 1
              FROM users_tracking as ut
              WHERE (ut.referenced_relation = 'catalogue_properties'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.old_value -> 'record_id' = part_id::varchar
                           AND ut.old_value -> 'property_type' = 'physical measurement'
                           AND ut.old_value -> 'property_sub_type' = 'weight'
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.new_value -> 'record_id' = part_id::varchar
                          )
                         )
                    )
                 OR (ut.referenced_relation = 'properties_values'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'weight' LIMIT 1)
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'weight' LIMIT 1)
                          )
                         )
                    )
             )
  INTO booContinue;
--   RAISE NOTICE '< No User tracking for this part ?: %', booContinue;
  IF booContinue AND recPartsDetails.old_spec_id != recFirstPartsDetails.old_spec_id THEN
    IF coalesce(recPartsDetails.old_weight_min, '') != '' OR coalesce(recPartsDetails.old_weight_max, '') != '' THEN
      INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_unit, property_accuracy_unit)
      (
        SELECT 'specimen_parts', new_part_id, 'physical measurement', 'weight', recPartsDetails.old_weight_unit, recPartsDetails.old_weight_unit
        WHERE NOT EXISTS (SELECT 1
                          FROM catalogue_properties
                          WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'physical measurement' AND property_sub_type = 'weight'
                        )
      );
--       GET DIAGNOSTICS prop_count = ROW_COUNT;
--       RAISE NOTICE '<< Catalogue prop inserted: %', prop_count;
      IF coalesce(recPartsDetails.old_weight_min, '') = coalesce(recPartsDetails.old_weight_max, '') THEN
--         RAISE NOTICE '<<< Min and Max the same';
        INSERT INTO properties_values (property_ref, property_value)
        (SELECT id, recPartsDetails.old_weight_min
          FROM catalogue_properties
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = new_part_id
            AND property_type = 'physical measurement'
            AND property_sub_type = 'weight'
        );
--         GET DIAGNOSTICS prop_count = ROW_COUNT;
--         RAISE NOTICE '<< Catalogue prop value inserted: %', prop_count;
      ELSE
--         RAISE NOTICE '<<< Min and Max Not the same';
        IF coalesce(recPartsDetails.old_weight_min, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_weight_min
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'weight'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value min inserted: %', prop_count;
        END IF;
        IF coalesce(recPartsDetails.old_weight_max, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_weight_max
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'weight'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value max inserted: %', prop_count;
        END IF;
      END IF;
    END IF;
  ELSE
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_method, property_tool, property_unit, property_accuracy_unit)
    (SELECT referenced_relation, new_part_id, property_type, property_sub_type, property_method, property_tool, property_unit, property_accuracy_unit
      FROM catalogue_properties
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'weight'
    )
    RETURNING id INTO cat_prop_id;
    INSERT INTO properties_values (property_ref, property_value, property_accuracy)
    (SELECT DISTINCT cat_prop_id, property_value, property_accuracy
      FROM catalogue_properties as cp INNER JOIN properties_values as pv ON cp.id = pv.property_ref
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'weight'
    );
--     GET DIAGNOSTICS prop_count = ROW_COUNT;
--     RAISE NOTICE '< All in one insertion: %', prop_count;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Length level*/
  SELECT
  NOT EXISTS (SELECT 1
              FROM users_tracking as ut
              WHERE (ut.referenced_relation = 'catalogue_properties'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.old_value -> 'record_id' = part_id::varchar
                           AND ut.old_value -> 'property_type' = 'physical measurement'
                           AND ut.old_value -> 'property_sub_type' = 'length'
                           AND coalesce(ut.old_value -> 'property_qualifier' , '') NOT IN ('height', 'depth')
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.new_value -> 'record_id' = part_id::varchar
                          )
                         )
                    )
                 OR (ut.referenced_relation = 'properties_values'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') NOT IN ('height','depth') LIMIT 1)
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') NOT IN ('height','depth') LIMIT 1)
                          )
                         )
                    )
             )
  INTO booContinue;
--   RAISE NOTICE '< No User tracking for this part ?: %', booContinue;
  IF booContinue AND recPartsDetails.old_spec_id != recFirstPartsDetails.old_spec_id  THEN
    IF coalesce(recPartsDetails.old_length_min, '') != '' OR coalesce(recPartsDetails.old_length_max, '') != '' THEN
      INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_unit, property_accuracy_unit)
      (
        SELECT 'specimen_parts', new_part_id, 'physical measurement', 'length', recPartsDetails.old_length_unit, recPartsDetails.old_length_unit
        WHERE NOT EXISTS (SELECT 1
                          FROM catalogue_properties
                          WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier, '') NOT IN ('height','depth')
                        )
      );
--       GET DIAGNOSTICS prop_count = ROW_COUNT;
--       RAISE NOTICE '<< Catalogue prop inserted: %', prop_count;
      IF coalesce(recPartsDetails.old_length_min, '') = coalesce(recPartsDetails.old_length_max, '') THEN
--         RAISE NOTICE '<<< Min and Max the same';
        INSERT INTO properties_values (property_ref, property_value)
        (SELECT id, recPartsDetails.old_length_min
          FROM catalogue_properties
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = new_part_id
            AND property_type = 'physical measurement'
            AND property_sub_type = 'length'
            AND coalesce(property_qualifier, '') NOT IN ('height','depth')
        );
--         GET DIAGNOSTICS prop_count = ROW_COUNT;
--         RAISE NOTICE '<< Catalogue prop value inserted: %', prop_count;
      ELSE
--         RAISE NOTICE '<<< Min and Max Not the same';
        IF coalesce(recPartsDetails.old_length_min, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_length_min
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') NOT IN ('height','depth')
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value min inserted: %', prop_count;
        END IF;
        IF coalesce(recPartsDetails.old_length_max, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_length_max
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') NOT IN ('height','depth')
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value max inserted: %', prop_count;
        END IF;
      END IF;
    END IF;
  ELSE
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_method, property_tool, property_unit, property_accuracy_unit)
    (SELECT referenced_relation, new_part_id, property_type, property_sub_type, property_method, property_tool, property_unit, property_accuracy_unit
      FROM catalogue_properties
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') NOT IN ('height','depth')
    )
    RETURNING id INTO cat_prop_id;
    INSERT INTO properties_values (property_ref, property_value, property_accuracy)
    (SELECT DISTINCT cat_prop_id, property_value, property_accuracy
      FROM catalogue_properties as cp INNER JOIN properties_values as pv ON cp.id = pv.property_ref
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') NOT IN ('height','depth')
    );
--     GET DIAGNOSTICS prop_count = ROW_COUNT;
--     RAISE NOTICE '< All in one insertion: %', prop_count;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Height level*/
  SELECT
  NOT EXISTS (SELECT 1
              FROM users_tracking as ut
              WHERE (ut.referenced_relation = 'catalogue_properties'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.old_value -> 'record_id' = part_id::varchar
                           AND ut.old_value -> 'property_type' = 'physical measurement'
                           AND ut.old_value -> 'property_sub_type' = 'length'
                           AND coalesce(ut.old_value -> 'property_qualifier' , '') = 'height'
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.new_value -> 'record_id' = part_id::varchar
                          )
                         )
                    )
                 OR (ut.referenced_relation = 'properties_values'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') = 'height' LIMIT 1)
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') = 'height' LIMIT 1)
                          )
                         )
                    )
             )
  INTO booContinue;
--   RAISE NOTICE '< No User tracking for this part ?: %', booContinue;
  IF booContinue AND recPartsDetails.old_spec_id != recFirstPartsDetails.old_spec_id  THEN
    IF coalesce(recPartsDetails.old_height_min, '') != '' OR coalesce(recPartsDetails.old_height_max, '') != '' THEN
      INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
      (
        SELECT 'specimen_parts', new_part_id, 'physical measurement', 'length', 'height', recPartsDetails.old_height_unit, recPartsDetails.old_height_unit
        WHERE NOT EXISTS (SELECT 1
                          FROM catalogue_properties
                          WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier, '') = 'height'
                        )
      );
--       GET DIAGNOSTICS prop_count = ROW_COUNT;
--       RAISE NOTICE '<< Catalogue prop inserted: %', prop_count;
      IF coalesce(recPartsDetails.old_height_min, '') = coalesce(recPartsDetails.old_height_max, '') THEN
--         RAISE NOTICE '<<< Min and Max the same';
        INSERT INTO properties_values (property_ref, property_value)
        (SELECT id, recPartsDetails.old_height_min
          FROM catalogue_properties
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = new_part_id
            AND property_type = 'physical measurement'
            AND property_sub_type = 'length'
            AND coalesce(property_qualifier, '') = 'height'
        );
--         GET DIAGNOSTICS prop_count = ROW_COUNT;
--         RAISE NOTICE '<< Catalogue prop value inserted: %', prop_count;
      ELSE
--         RAISE NOTICE '<<< Min and Max Not the same';
        IF coalesce(recPartsDetails.old_height_min, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_height_min
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') = 'height'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value min inserted: %', prop_count;
        END IF;
        IF coalesce(recPartsDetails.old_height_max, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_height_max
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') = 'height'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value max inserted: %', prop_count;
        END IF;
      END IF;
    END IF;
  ELSE
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_method, property_tool, property_unit, property_accuracy_unit)
    (SELECT referenced_relation, new_part_id, property_type, property_sub_type, property_qualifier, property_method, property_tool, property_unit, property_accuracy_unit
      FROM catalogue_properties
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') = 'height'
    )
    RETURNING id INTO cat_prop_id;
    INSERT INTO properties_values (property_ref, property_value, property_accuracy)
    (SELECT DISTINCT cat_prop_id, property_value, property_accuracy
      FROM catalogue_properties as cp INNER JOIN properties_values as pv ON cp.id = pv.property_ref
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') = 'height'
    );
--     GET DIAGNOSTICS prop_count = ROW_COUNT;
--     RAISE NOTICE '< All in one insertion: %', prop_count;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Depth level*/
  SELECT
  NOT EXISTS (SELECT 1
              FROM users_tracking as ut
              WHERE (ut.referenced_relation = 'catalogue_properties'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.old_value -> 'record_id' = part_id::varchar
                           AND ut.old_value -> 'property_type' = 'physical measurement'
                           AND ut.old_value -> 'property_sub_type' = 'length'
                           AND coalesce(ut.old_value -> 'property_qualifier' , '') = 'depth'
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.new_value -> 'record_id' = part_id::varchar
                          )
                         )
                    )
                 OR (ut.referenced_relation = 'properties_values'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') = 'depth' LIMIT 1)
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') = 'depth' LIMIT 1)
                          )
                         )
                    )
             )
  INTO booContinue;
--   RAISE NOTICE '< No User tracking for this part ?: %', booContinue;
  IF booContinue AND recPartsDetails.old_spec_id != recFirstPartsDetails.old_spec_id  THEN
    IF coalesce(recPartsDetails.old_depth_min, '') != '' OR coalesce(recPartsDetails.old_depth_max, '') != '' THEN
      INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
      (
        SELECT 'specimen_parts', new_part_id, 'physical measurement', 'length', 'depth', recPartsDetails.old_depth_unit, recPartsDetails.old_depth_unit
        WHERE NOT EXISTS (SELECT 1
                          FROM catalogue_properties
                          WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier, '') = 'depth'
                        )
      );
--       GET DIAGNOSTICS prop_count = ROW_COUNT;
--       RAISE NOTICE '<< Catalogue prop inserted: %', prop_count;
      IF coalesce(recPartsDetails.old_depth_min, '') = coalesce(recPartsDetails.old_depth_max, '') THEN
--         RAISE NOTICE '<<< Min and Max the same';
        INSERT INTO properties_values (property_ref, property_value)
        (SELECT id, recPartsDetails.old_depth_min
          FROM catalogue_properties
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = new_part_id
            AND property_type = 'physical measurement'
            AND property_sub_type = 'length'
            AND coalesce(property_qualifier, '') = 'depth'
        );
--         GET DIAGNOSTICS prop_count = ROW_COUNT;
--         RAISE NOTICE '<< Catalogue prop value inserted: %', prop_count;
      ELSE
--         RAISE NOTICE '<<< Min and Max Not the same';
        IF coalesce(recPartsDetails.old_depth_min, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_depth_min
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') = 'depth'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value min inserted: %', prop_count;
        END IF;
        IF coalesce(recPartsDetails.old_depth_max, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_depth_max
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') = 'depth'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value max inserted: %', prop_count;
        END IF;
      END IF;
    END IF;
  ELSE
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_method, property_tool, property_unit, property_accuracy_unit)
    (SELECT referenced_relation, new_part_id, property_type, property_sub_type, property_qualifier, property_method, property_tool, property_unit, property_accuracy_unit
      FROM catalogue_properties
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') = 'depth'
    )
    RETURNING id INTO cat_prop_id;
    INSERT INTO properties_values (property_ref, property_value, property_accuracy)
    (SELECT DISTINCT cat_prop_id, property_value, property_accuracy
      FROM catalogue_properties as cp INNER JOIN properties_values as pv ON cp.id = pv.property_ref
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') = 'depth'
    );
--     GET DIAGNOSTICS prop_count = ROW_COUNT;
--     RAISE NOTICE '< All in one insertion: %', prop_count;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Insurances*/
  IF recPartsDetails.old_insurance_value > 0 THEN
    IF coalesce(recPartsDetails.old_spec_id = recFirstPartsDetails.old_spec_id) AND coalesce(recPartsDetails.old_insurance_value,0) != 0 THEN
      booUpdate := true;
    ELSE
      SELECT COUNT(*) INTO prop_count
      FROM insurances
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0)
        AND 1 < (SELECT COUNT(*) FROM insurances WHERE referenced_relation = 'specimen_parts' AND record_id = part_id);
      IF prop_count = 1 THEN
        booUpdate := true;
      ELSE
        booContinue := true;
      END IF;
    END IF;
    IF booUpdate THEN
      UPDATE insurances
      SET record_id = new_part_id
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0);
      GET DIAGNOSTICS update_count = ROW_COUNT;
      IF update_count = 0 THEN
        booContinue := true;
      END IF;
    END IF;
    IF booContinue THEN
      IF column_exists('date_from', 'insurances') THEN
        INSERT INTO insurances (referenced_relation, record_id, insurance_value, insurance_year,
                                date_from, date_from_mask,
                                date_to, date_to_mask
                              )
        (SELECT 'specimen_parts', new_part_id, recPartsDetails.old_insurance_value, coalesce(recPartsDetails.old_insurance_year,0),
                case when coalesce(recPartsDetails.old_insurance_year,0) = 0 then DATE ('0001-01-01') else DATE (recPartsDetails.old_insurance_year || '-01-01') end,
                case when coalesce(recPartsDetails.old_insurance_year,0) = 0 then 0 else 32 end,
                case when coalesce(recPartsDetails.old_insurance_year,0) = 0 then DATE ('2038-12-31') else DATE (recPartsDetails.old_insurance_year || '-12-31') end,
                case when coalesce(recPartsDetails.old_insurance_year,0) = 0 then 0 else 32 end
         WHERE NOT EXISTS (SELECT 1
                           FROM insurances
                           WHERE referenced_relation = 'specimen_parts'
                             AND record_id = new_part_id
                             AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0)
                          )
        );
      ELSE
        INSERT INTO insurances (referenced_relation, record_id, insurance_value, insurance_year)
        (SELECT 'specimen_parts', new_part_id, recPartsDetails.old_insurance_value, coalesce(recPartsDetails.old_insurance_year,0)
         WHERE NOT EXISTS (SELECT 1
                           FROM insurances
                           WHERE referenced_relation = 'specimen_parts'
                             AND record_id = new_part_id
                             AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0)
                          )
        );
      END IF;
    END IF;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Maintenances*/
  IF recPartsDetails.maintenance_people_ref > 0 AND recPartsDetails.maintenance_people_ref IS NOT NULL THEN
    IF coalesce(recPartsDetails.old_spec_id = recFirstPartsDetails.old_spec_id) THEN
      booUpdate := true;
    ELSE
      SELECT COUNT(*) INTO prop_count
      FROM collection_maintenance
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND "category" = recPartsDetails.maintenance_category
        AND action_observation = recPartsDetails.maintenance_action_observation
        AND people_ref = recPartsDetails.maintenance_people_ref
        AND modification_date_time = recPartsDetails.maintenance_modification_date_time
        AND 1 < (SELECT COUNT(*)
                 FROM collection_maintenance
                 WHERE referenced_relation = 'specimen_parts'
                   AND record_id = part_id
                   AND "category" = recPartsDetails.maintenance_category
                   AND action_observation = recPartsDetails.maintenance_action_observation
                   AND people_ref = recPartsDetails.maintenance_people_ref
                   AND modification_date_time = recPartsDetails.maintenance_modification_date_time
                );
      IF prop_count = 1 THEN
        booUpdate := true;
      ELSE
        booContinue := true;
      END IF;
    END IF;
    IF booUpdate THEN
      UPDATE collection_maintenance
      SET record_id = new_part_id,
          modification_date_mask = recPartsDetails.maintenance_modification_date_mask
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND "category" = recPartsDetails.maintenance_category
        AND action_observation = recPartsDetails.maintenance_action_observation
        AND people_ref = recPartsDetails.maintenance_people_ref
        AND modification_date_time = recPartsDetails.maintenance_modification_date_time;
      GET DIAGNOSTICS update_count = ROW_COUNT;
      IF update_count = 0 THEN
        booContinue := true;
      END IF;
    END IF;
    IF booContinue THEN
      INSERT INTO collection_maintenance (referenced_relation, record_id, people_ref, "category", action_observation, modification_date_time, modification_date_mask)
      (SELECT 'specimen_parts', new_part_id, recPartsDetails.maintenance_people_ref, recPartsDetails.maintenance_category, recPartsDetails.maintenance_action_observation, recPartsDetails.maintenance_modification_date_time, recPartsDetails.maintenance_modification_date_mask
       WHERE NOT EXISTS
             (SELECT 1
              FROM collection_maintenance
              WHERE referenced_relation = 'specimen_parts'
                AND record_id = new_part_id
                AND "category" = recPartsDetails.maintenance_category
                AND action_observation = recPartsDetails.maintenance_action_observation
                AND people_ref = recPartsDetails.maintenance_people_ref
                AND modification_date_time = recPartsDetails.maintenance_modification_date_time
             )
      );
    END IF;
  END IF;
  booContinue := false;
  booUpdate := false;
  return 1;
exception
  when others then
    raise warning 'Error in moveOrCreateProp: %', SQLERRM;
    return -1;
end;
$$;

create or replace function createProperties (IN part_id specimen_parts.id%TYPE, recPartsDetails recPartsDetail) returns integer language plpgsql AS
$$
declare
  cat_prop_id integer := 0;
  prop_id integer := 0;
  prop_count integer;
--   insert_count integer;
  booContinue boolean := true;
begin
  /* Comments */
  IF recPartsDetails.part_comment != '' THEN
    INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
    (
      SELECT 'specimen_parts', part_id, 'general', recPartsDetails.part_comment
      WHERE NOT EXISTS (
                        SELECT 1
                        FROM comments
                        WHERE referenced_relation = 'specimen_parts'
                          AND record_id = part_id
                          AND notion_concerned = 'general'
                       )
    );
--     GET DIAGNOSTICS insert_count = ROW_COUNT;
--     IF insert_count != 0 THEN
--       RAISE NOTICE 'Comment inserted';
--     END IF;
  END IF;
  /*Freshness level*/
  IF coalesce(recPartsDetails.freshness_level,'') != '' THEN
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'part state'
      AND property_sub_type = 'freshness level'
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'part state'
                          AND old_value -> 'property_sub_type' = 'freshness level'
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type)
        (
          select 'specimen_parts', part_id, 'part state', 'freshness level'
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.freshness_level
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  /*Insurances*/
  IF coalesce(recPartsDetails.old_insurance_value,0) != 0 THEN
    SELECT NOT EXISTS ( select 1
                        from users_tracking
                        where referenced_relation = 'insurances'
                          and (action in ('delete', 'update')
                                and old_value -> 'referenced_relation' = 'specimen_parts'
                                and old_value -> 'record_id' = part_id::varchar
                                and old_value -> 'insurance_year' = coalesce(recPartsDetails.old_insurance_year,0)::varchar
                              )
                          or (action in ('insert')
                                and new_value -> 'referenced_relation' = 'specimen_parts'
                                and new_value -> 'record_id' = part_id::varchar
                                and new_value -> 'insurance_year' = coalesce(recPartsDetails.old_insurance_year,0)::varchar
                              )
                      )
    INTO booContinue;
    IF booContinue THEN
      update insurances
      set insurance_value = recPartsDetails.old_insurance_value
      where referenced_relation = 'specimen_parts'
        and record_id = part_id
        and insurance_year = coalesce(recPartsDetails.old_insurance_year,0);
      GET DIAGNOSTICS prop_count = ROW_COUNT;
      IF prop_count = 0 THEN
        IF column_exists('date_from', 'insurances') THEN
          INSERT INTO insurances (referenced_relation, record_id, insurance_value, insurance_year,
                                  date_from, date_from_mask,
                                  date_to, date_to_mask
                                )
          (SELECT 'specimen_parts', part_id, recPartsDetails.old_insurance_value, coalesce(recPartsDetails.old_insurance_year,0),
                  case when coalesce(recPartsDetails.old_insurance_year,0) = 0 then DATE ('0001-01-01') else DATE (recPartsDetails.old_insurance_year || '-01-01') end,
                  case when coalesce(recPartsDetails.old_insurance_year,0) = 0 then 0 else 32 end,
                  case when coalesce(recPartsDetails.old_insurance_year,0) = 0 then DATE ('2038-12-31') else DATE (recPartsDetails.old_insurance_year || '-12-31') end,
                  case when coalesce(recPartsDetails.old_insurance_year,0) = 0 then 0 else 32 end
           WHERE NOT EXISTS (SELECT 1
                             FROM insurances
                             WHERE referenced_relation = 'specimen_parts'
                               AND record_id = part_id
                               AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0)
                            )
          );
        ELSE
          INSERT INTO insurances (referenced_relation, record_id, insurance_value, insurance_year)
          (SELECT 'specimen_parts', part_id, recPartsDetails.old_insurance_value, coalesce(recPartsDetails.old_insurance_year,0)
           WHERE NOT EXISTS (SELECT 1
                             FROM insurances
                             WHERE referenced_relation = 'specimen_parts'
                               AND record_id = part_id
                               AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0)
                            )
          );
        END IF;
      END IF;
    END IF;
  END IF;
  /*Maintenances*/
  IF recPartsDetails.maintenance_people_ref IS NOT NULL THEN
    insert into collection_maintenance (referenced_relation, record_id, people_ref, "category", action_observation, modification_date_time, modification_date_mask)
    (
      select 'specimen_parts', part_id, recPartsDetails.maintenance_people_ref, recPartsDetails.maintenance_category, recPartsDetails.maintenance_action_observation, recPartsDetails.maintenance_modification_date_time, recPartsDetails.maintenance_modification_date_mask
      where not exists (
                          select 1
                          from collection_maintenance
                          where referenced_relation = 'specimen_parts'
                            and record_id = part_id
                            and people_ref = recPartsDetails.maintenance_people_ref
                            and "category" = recPartsDetails.maintenance_category
                            and action_observation = recPartsDetails.maintenance_action_observation
                            and modification_date_time = recPartsDetails.maintenance_modification_date_time
                            and modification_date_mask = recPartsDetails.maintenance_modification_date_mask
                       )
        and not exists (
                          select 1
                          from users_tracking
                          where referenced_relation = 'collection_maintenance'
                            and (action in ('delete', 'update')
                                  and old_value -> 'referenced_relation' = 'specimen_parts'
                                  and old_value -> 'record_id' = part_id::varchar
                                  and old_value -> 'people_ref' = recPartsDetails.maintenance_people_ref::varchar
                                  and old_value -> 'category' = recPartsDetails.maintenance_category::varchar
                                  and old_value -> 'action_observation' = recPartsDetails.maintenance_action_observation::varchar
                                  and old_value -> 'modification_date_time' = recPartsDetails.maintenance_modification_date_time::varchar
                                  and old_value -> 'modification_date_mask' = recPartsDetails.maintenance_modification_date_mask::varchar
                                )
                            or (action in ('delete', 'update')
                                  and new_value -> 'referenced_relation' = 'specimen_parts'
                                  and new_value -> 'record_id' = part_id::varchar
                                  and new_value -> 'people_ref' = recPartsDetails.maintenance_people_ref::varchar
                                  and new_value -> 'category' = recPartsDetails.maintenance_category::varchar
                                  and new_value -> 'action_observation' = recPartsDetails.maintenance_action_observation::varchar
                                  and new_value -> 'modification_date_time' = recPartsDetails.maintenance_modification_date_time::varchar
                                  and new_value -> 'modification_date_mask' = recPartsDetails.maintenance_modification_date_mask::varchar
                               )
                       )
        and exists (select 1 from people where id = recPartsDetails.maintenance_people_ref)
    );
  END IF;
  /*Length level*/
  IF coalesce(recPartsDetails.old_length_min, '') != '' OR coalesce(recPartsDetails.old_length_max, '') != '' THEN
--     RAISE NOTICE '**Length';
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'physical measurement'
      AND property_sub_type = 'length'
      AND property_qualifier NOT IN ('height','depth')
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'physical measurement'
                          AND old_value -> 'property_sub_type' = 'length'
                          AND old_value -> 'property_qualifier' NOT IN ('height','depth')
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
--         RAISE NOTICE 'Existing property:%', cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
        (
          select 'specimen_parts', part_id, 'physical measurement', 'length', '', recPartsDetails.old_length_unit, recPartsDetails.old_length_unit
        )
        returning id INTO cat_prop_id;
--         RAISE NOTICE 'New property:%', cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.old_length_min
            where coalesce(recPartsDetails.old_length_min, '') != ''
            union
            select cat_prop_id, recPartsDetails.old_length_max
            where coalesce(recPartsDetails.old_length_max, '') != ''
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  /*Height level*/
  IF coalesce(recPartsDetails.old_height_min, '') != '' OR coalesce(recPartsDetails.old_height_max, '') != '' THEN
--     RAISE NOTICE '**Height';
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'physical measurement'
      AND property_sub_type = 'length'
      AND property_qualifier = 'height'
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'physical measurement'
                          AND old_value -> 'property_sub_type' = 'length'
                          AND old_value -> 'property_qualifier' = 'height'
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
        (
          select 'specimen_parts', part_id, 'physical measurement', 'length', 'height', recPartsDetails.old_height_unit, recPartsDetails.old_height_unit
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.old_height_min
            where coalesce(recPartsDetails.old_height_min, '') != ''
            union
            select cat_prop_id, recPartsDetails.old_height_max
            where coalesce(recPartsDetails.old_height_max, '') != ''
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  /*Depth level*/
  IF coalesce(recPartsDetails.old_depth_min, '') != '' OR coalesce(recPartsDetails.old_depth_max, '') != '' THEN
--     RAISE NOTICE '**Depth';
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'physical measurement'
      AND property_sub_type = 'length'
      AND property_qualifier = 'depth'
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'physical measurement'
                          AND old_value -> 'property_sub_type' = 'length'
                          AND old_value -> 'property_qualifier' = 'depth'
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
        (
          select 'specimen_parts', part_id, 'physical measurement', 'length', 'depth', recPartsDetails.old_depth_unit, recPartsDetails.old_depth_unit
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.old_depth_min
            where coalesce(recPartsDetails.old_depth_min, '') != ''
            union
            select cat_prop_id, recPartsDetails.old_depth_max
            where coalesce(recPartsDetails.old_depth_max, '') != ''
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  /*Weight level*/
  IF coalesce(recPartsDetails.old_weight_min, '') != '' OR coalesce(recPartsDetails.old_weight_max, '') != '' THEN
--     RAISE NOTICE '**Weight';
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'physical measurement'
      AND property_sub_type = 'weight'
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'physical measurement'
                          AND old_value -> 'property_sub_type' = 'weight'
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
        (
          select 'specimen_parts', part_id, 'physical measurement', 'weight', '', recPartsDetails.old_weight_unit, recPartsDetails.old_weight_unit
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.old_weight_min
            where coalesce(recPartsDetails.old_weight_min, '') != ''
            union
            select cat_prop_id, recPartsDetails.old_weight_max
            where coalesce(recPartsDetails.old_weight_max, '') != ''
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  return 1;
exception
  when others then
    RAISE WARNING 'For part: % Error in createProperties: %', part_id, SQLERRM;
    return -1;
end;
$$;

drop function if exists resplit_parts();
create or replace function resplit_parts () returns boolean language plpgsql as
$$
declare
  recPartsDetails recPartsDetail;
  recFirstPart recPartsDetail;
  part_id specimen_parts.id%TYPE := 0;
  new_part_id specimen_parts.id%TYPE;
  new_first_part_id specimen_parts.id%TYPE;
  code_count integer;
  recActualCodes RECORD;
  recTransferedCodes RECORD;
  recProperties RECORD;
  recInsurances RECORD;
  spec_part_count_min integer;
  spec_part_count_max integer;
  comptage integer := 0;
  new_code_id codes.id%TYPE;
  new_code_insertion boolean := false;
  booUpdateProperties boolean := false;
begin
  FOR recPartsDetails IN select *
                         from darwin2.migrated_parts
--                          where old_weight_min is not null and old_weight_min != '0'
  LOOP
    comptage := comptage + 1;
    IF comptage IN (5000, 10000, 20000, 40000, 50000, 75000, 90000, 100000, 125000, 150000, 175000, 200000, 225000, 250000, 275000, 300000, 325000, 350000, 375000) THEN
      RAISE NOTICE 'Already % records parsed ;)', comptage;
    END IF;
    IF part_id != recPartsDetails.parts_id THEN

--       RAISE NOTICE 'Next part infos: %', recPartsDetails;

      new_first_part_id := NULL;
      booUpdateProperties := false;

--       SELECT COUNT(*) INTO code_count
--       FROM codes
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = part_id
--         AND code_category = 'main';
-- 
--       IF code_count > 1 THEN
--         RAISE WARNING 'There are more than one code left for part: %', part_id;
--       END IF;

      recFirstPart := recPartsDetails;
      part_id := recPartsDetails.parts_id;

      SELECT count(full_code_order_by) INTO code_count
      FROM codes
      where referenced_relation = 'specimen_parts'
        and record_id = part_id
        and code_category = 'main';
      IF code_count = 0 THEN
--         RAISE NOTICE '+New code creation for next part';
--         select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')), code_prefix, code, code_suffix into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = part_id group by code_prefix, code, code_suffix;
--         RAISE NOTICE '++ Codes before creation: %', recActualCodes;
        IF createCodes (part_id, recPartsDetails.old_main_code) < 0 THEN
          return false;
        END IF;
--         select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')), code_prefix, code, code_suffix into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = part_id group by code_prefix, code, code_suffix;
--         RAISE NOTICE '++ Codes after creation: %', recActualCodes;
        code_count := 1;
      END IF;
      IF code_count = 1 THEN
--         select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')), code_prefix, code, code_suffix into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = part_id group by code_prefix, code, code_suffix;
--         RAISE NOTICE '+++ Code is exactly one: %', recActualCodes;
        IF recPartsDetails.specimen_part_count_min = 0 AND recPartsDetails.part_count_min > 0 THEN
          IF recPartsDetails.specimen_part_count_max = recPartsDetails.specimen_part_count_min THEN
            UPDATE specimen_parts
            SET specimen_part_count_min = recPartsDetails.part_count_min,
                specimen_part_count_max = recPartsDetails.part_count_min
            WHERE id = part_id;
          ELSIF recPartsDetails.specimen_part_count_max >= recPartsDetails.part_count_min THEN
            UPDATE specimen_parts
            SET specimen_part_count_min = recPartsDetails.part_count_min
            WHERE id = part_id;
          END IF;
        END IF;
        booUpdateProperties := true;
      ELSE
--         RAISE NOTICE '*More than one code...';
        SELECT EXISTS(SELECT 1
                      FROM users_tracking
                      WHERE referenced_relation = 'codes'
                        AND action = 'insert'
                        AND new_value -> 'referenced_relation' = 'specimen_parts'
                        AND new_value -> 'record_id' = part_id::varchar
                        AND new_value -> 'code_category' = 'main'
                      ) INTO new_code_insertion;
        IF new_code_insertion THEN
--           RAISE NOTICE '+Insertions occured for this part...';
          INSERT INTO specimen_parts
          (parent_ref, path, specimen_individual_ref, specimen_part, complete,
            building, "floor", room, "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage,
            surnumerary, specimen_status, specimen_part_count_min, specimen_part_count_max, institution_ref
          )
          (
            SELECT parent_ref, path, specimen_individual_ref, specimen_part, complete,
                  building, "floor", room, "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage,
                  surnumerary, specimen_status, recPartsDetails.part_count_min, recPartsDetails.part_count_min, institution_ref
            FROM specimen_parts
            WHERE id = part_id
          )
          RETURNING id INTO new_first_part_id;
--           RAISE NOTICE '+New part created: %', new_first_part_id;
          IF recPartsDetails.specimen_part_count_min > 0 AND recPartsDetails.part_count_min > 0 AND recPartsDetails.part_count_min < recPartsDetails.specimen_part_count_min THEN
            UPDATE specimen_parts
            SET specimen_part_count_min = specimen_part_count_min - recPartsDetails.part_count_min,
                specimen_part_count_max = specimen_part_count_max - recPartsDetails.part_count_min
            WHERE id = part_id;
          END IF;
--           select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')), code_prefix, code, code_suffix  into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = part_id group by code_prefix, code, code_suffix;
          UPDATE codes
          SET record_id = new_first_part_id
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = part_id
            AND id NOT IN (SELECT id
                            FROM codes
                            WHERE referenced_relation = 'specimen_parts'
                              AND record_id = part_id
                              AND EXISTS (SELECT 1
                                          FROM users_tracking
                                          WHERE referenced_relation = 'codes'
                                            AND record_id = codes.id
                                            AND action = 'insert'
                                            AND new_value -> 'referenced_relation' = 'specimen_parts'
                                            AND new_value -> 'record_id' = part_id::varchar
                                         )
                          );
--           select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')), code_prefix, code, code_suffix into recTransferedCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = new_first_part_id  group by code_prefix, code, code_suffix;
--           RAISE NOTICE '++ Left part codes: %, New part codes: %', recActualCodes, recTransferedCodes;
--           select count(*) into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = part_id;
--           RAISE NOTICE '++ !! For left part, count of codes: % !!', recActualCodes;
--           select count(*) into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = new_first_part_id;
--           RAISE NOTICE '++ !! For new part, count of codes: % !!', recActualCodes;
--           SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = part_id;
--           RAISE NOTICE '++Left part count min and max: % and %', spec_part_count_min, spec_part_count_max;
--           SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = new_first_part_id;
--           RAISE NOTICE '++New part count min and max: % and %', spec_part_count_min, spec_part_count_max;
--           SELECT array_agg(property_value/*_unified*/)
--           INTO recProperties
--           FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Properties before transfert: %', recProperties;
--           SELECT array_agg(insurance_value), array_agg(insurance_year)
--           INTO recInsurances
--           FROM insurances
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Insurances before transfert: %', recInsurances;
--           SELECT array_agg(people_ref)
--           INTO recProperties
--           FROM collection_maintenance
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Maintenance before transfert: %', recProperties;
          IF createProperties(new_first_part_id, recPartsDetails) < 0 THEN
            return false;
          END IF;
--           SELECT array_agg(property_value/*_unified*/)
--           INTO recProperties
--           FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Properties after transfert for left part: %', recProperties;
--           SELECT array_agg(property_value/*_unified*/)
--           INTO recProperties
--           FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = new_first_part_id;
--           RAISE NOTICE '+++ Properties after transfert for new part: %', recProperties;
--           SELECT array_agg(insurance_value), array_agg(insurance_year)
--           INTO recInsurances
--           FROM insurances
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Insurances after transfert for left part: %', recInsurances;
--           SELECT array_agg(insurance_value), array_agg(insurance_year)
--           INTO recInsurances
--           FROM insurances
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = new_first_part_id;
--           RAISE NOTICE '+++ Insurances after transfert for new part: %', recInsurances;
--           SELECT array_agg(people_ref)
--           INTO recProperties
--           FROM collection_maintenance
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Maintenance after transfert for left part: %', recProperties;
--           SELECT array_agg(people_ref)
--           INTO recProperties
--           FROM collection_maintenance
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = new_first_part_id;
--           RAISE NOTICE '+++ Maintenance after transfert for new part: %', recProperties;
        ELSE
          booUpdateProperties := true;
        END IF;
      END IF;
      IF booUpdateProperties THEN
--         SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = part_id;
--         RAISE NOTICE '++Actual count min and max: % and %', spec_part_count_min, spec_part_count_max;
--         select array_agg(property_value_unified), array_agg(property_value)
--         into recProperties
--         from catalogue_properties inner join properties_values on catalogue_properties.id = properties_values.property_ref
--         where referenced_relation = 'specimen_parts' and record_id = part_id;
--         RAISE NOTICE '+++ Properties before update: %', recProperties;
--         SELECT array_agg(insurance_value), array_agg(insurance_year)
--         INTO recInsurances
--         FROM insurances
--         WHERE referenced_relation = 'specimen_parts'
--           AND record_id = part_id;
--         RAISE NOTICE '+++ Insurances before update: %', recInsurances;
--         SELECT array_agg(people_ref)
--         INTO recProperties
--         FROM collection_maintenance
--         WHERE referenced_relation = 'specimen_parts'
--           AND record_id = part_id;
--         RAISE NOTICE '+++ Maintenance before update: %', recProperties;
        IF createProperties (part_id, recPartsDetails) < 0 THEN
          return false;
        END IF;

--         select array_agg(property_value_unified), array_agg(property_value)
--         into recProperties
--         from catalogue_properties inner join properties_values on catalogue_properties.id = properties_values.property_ref
--         where referenced_relation = 'specimen_parts' and record_id = part_id;
--         RAISE NOTICE '+++ Actual properties: %', recProperties;
--         SELECT array_agg(insurance_value), array_agg(insurance_year)
--         INTO recInsurances
--         FROM insurances
--         WHERE referenced_relation = 'specimen_parts'
--           AND record_id = part_id;
--         RAISE NOTICE '+++ Actual Insurances: %', recInsurances;
--         SELECT array_agg(people_ref)
--         INTO recProperties
--         FROM collection_maintenance
--         WHERE referenced_relation = 'specimen_parts'
--           AND record_id = part_id;
--         RAISE NOTICE '+++ Actual Maintenance: %', recProperties;
      END IF;
      booUpdateProperties := false;
    ELSE
--       RAISE NOTICE '- Same part id infos: %', recPartsDetails;

      SELECT record_id INTO new_code_id
      FROM users_tracking
      WHERE referenced_relation = 'codes'
        AND action = 'update'
        AND old_value -> 'referenced_relation' = 'specimen_parts'
        AND old_value -> 'record_id' = part_id::varchar
        AND old_value -> 'code_category' = 'main'
        AND old_value -> 'full_code_order_by' = recPartsDetails.main_code::varchar;

      IF coalesce(new_code_id,0) = 0 THEN
--         RAISE NOTICE 'No update occured for code: %', recPartsDetails.old_main_code;
        IF coalesce(new_first_part_id,0) = 0 THEN
          SELECT createNewPart(part_id, recPartsDetails) INTO new_part_id;
          IF new_part_id < 0 THEN
            return false;
          END IF;
        ELSE
          SELECT createNewPart(new_first_part_id, recPartsDetails) INTO new_part_id;
          IF new_part_id < 0 THEN
            return false;
          END IF;
        END IF;
      ELSE
--         RAISE NOTICE 'At least one update occured for code: % ', recPartsDetails.old_main_code;
        IF coalesce(new_first_part_id,0) = 0 THEN
          SELECT createNewPart(part_id, recPartsDetails, new_code_id) INTO new_part_id;
          IF new_part_id < 0 THEN
            return false;
          END IF;
        ELSE
          SELECT createNewPart(new_first_part_id, recPartsDetails, new_code_id) INTO new_part_id;
          IF new_part_id < 0 THEN
            return false;
          END IF;
        END IF;
      END IF;
--       select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')), code_prefix, code, code_suffix into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end group by code_prefix, code, code_suffix;
--       select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')), code_prefix, code, code_suffix into recTransferedCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = new_part_id  group by code_prefix, code, code_suffix;
--       RAISE NOTICE '-- Actual codes: %, Transfered codes: %', recActualCodes, recTransferedCodes;
--       select count(*) into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end;
--       select count(*) into recTransferedCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = new_part_id;
--       RAISE NOTICE '-- Count codes for left: %, Count codes transfered: %', recActualCodes, recTransferedCodes;
--       SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end;
--       RAISE NOTICE '++Actual count min and max: % and %', spec_part_count_min, spec_part_count_max;
--       SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = new_part_id;
--       RAISE NOTICE '++Transfered count min and max: % and %', spec_part_count_min, spec_part_count_max;
--       SELECT array_agg(property_value_unified), array_agg(property_value/*_unified*/)
--       INTO recProperties
--       FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end;
--       RAISE NOTICE '+++ Properties before transfert: %', recProperties;
--       SELECT array_agg(insurance_value), array_agg(insurance_year)
--       INTO recInsurances
--       FROM insurances
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end;
--       RAISE NOTICE '+++ Insurances before transfert: %', recInsurances;
--       SELECT array_agg(people_ref)
--       INTO recProperties
--       FROM collection_maintenance
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end;
--       RAISE NOTICE '+++ Maintenance before transfert: %', recProperties;
      IF moveOrCreateProp(part_id, new_part_id, recPartsDetails, recFirstPart) < 0 THEN
        return false;
      END IF;

--       SELECT array_agg(property_value_unified), array_agg(property_value/*_unified*/)
--       INTO recProperties
--       FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end;
--       RAISE NOTICE '+++ Properties after transfert: %', recProperties;
--       SELECT array_agg(insurance_value), array_agg(insurance_year)
--       INTO recInsurances
--       FROM insurances
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end;
--       RAISE NOTICE '+++ Insurances after transfert: %', recInsurances;
--       SELECT array_agg(people_ref)
--       INTO recProperties
--       FROM collection_maintenance
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = case when coalesce(new_first_part_id,0)=0 then part_id else new_first_part_id end;
--       RAISE NOTICE '+++ Maintenance after transfert: %', recProperties;
--       SELECT array_agg(property_value_unified), array_agg(property_value/*_unified*/)
--       INTO recProperties
--       FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = new_part_id;
--       RAISE NOTICE '+++ Properties after transfert for new part: %', recProperties;
--       SELECT array_agg(insurance_value), array_agg(insurance_year)
--       INTO recInsurances
--       FROM insurances
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = new_part_id;
--       RAISE NOTICE '+++ Insurances after transfert for new part: %', recInsurances;
--       SELECT array_agg(people_ref)
--       INTO recProperties
--       FROM collection_maintenance
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = new_part_id;
--       RAISE NOTICE '+++ Maintenance after transfert for new part: %', recProperties;
    END IF;
    new_code_id := NULL;
  END LOOP;
  return true;
exception
  when others then
    RAISE WARNING 'Error in main function: %', SQLERRM;
end;
$$;

SELECT resplit_parts();

create or replace function split_parts() returns boolean language plpgsql
AS
$$
declare
  response boolean := true;
  recPartsAfter RECORD;
  recPartsAfterCodes RECORD;
  recPartsAfterDiverse RECORD;
  intCounter integer := 0;
  newPartId specimen_parts.id%TYPE;
  newDiverseId catalogue_properties.id%TYPE;
  countStillCodes integer;
begin
  FOR recPartsAfter IN
    SELECT DISTINCT id
    FROM specimen_parts
    WHERE EXISTS (select 1 from codes where referenced_relation = 'specimen_parts' and record_id = specimen_parts.id and code_category = 'main' GROUP BY referenced_relation, record_id, code_category HAVING COUNT(*) > 1)
  LOOP
--     RAISE NOTICE 'After migration correction, part id splitted is: %', recPartsAfter.id;
    FOR recPartsAfterCodes IN
      SELECT id
      FROM codes
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = recPartsAfter.id
        AND code_category = 'main'
    LOOP
      IF intCounter != 0 THEN
        INSERT INTO specimen_parts (parent_ref, path, specimen_individual_ref, specimen_part, "complete", building, "floor", "room", "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage, surnumerary, specimen_status, specimen_part_count_min, specimen_part_count_max, institution_ref)
        (SELECT parent_ref, path, specimen_individual_ref, specimen_part, "complete", building, "floor", "room", "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage, surnumerary, specimen_status, specimen_part_count_min, specimen_part_count_max, institution_ref
         FROM specimen_parts
         WHERE id = recPartsAfter.id
        )
        RETURNING id INTO newPartId;
--         RAISE NOTICE '--Updating codes--';
        UPDATE codes
        SET record_id = newPartId
        WHERE id = recPartsAfterCodes.id;
--         RAISE NOTICE '--Insertion of non main codes--';
        INSERT INTO codes (referenced_relation, record_id, code_category, code_prefix, code_prefix_separator, code, code_suffix, code_suffix_separator, code_date, code_date_mask)
        (SELECT 'specimen_parts', newPartId, code_category, code_prefix, code_prefix_separator, code, code_suffix, code_suffix_separator, code_date, code_date_mask
         FROM codes
         WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id AND code_category != 'main'
        );
--         RAISE NOTICE '--Copy comments--';
        INSERT INTO comments (referenced_relation, record_id, notion_concerned, "comment")
        (SELECT 'specimen_parts', newPartId, notion_concerned, "comment"
         FROM comments
         WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id
        );
--         RAISE NOTICE '--Copy insurances--';
        INSERT INTO insurances (referenced_relation, record_id, insurance_value, insurance_currency, insurance_year, insurer_ref)
        (SELECT 'specimen_parts', newPartId, insurance_value, insurance_currency, insurance_year, insurer_ref
         FROM insurances
         WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id
        );
--         RAISE NOTICE '--Copy collection maintenances--';
        INSERT INTO collection_maintenance (referenced_relation, record_id, people_ref, "category", action_observation, description, modification_date_time, modification_date_mask)
        (SELECT 'specimen_parts', newPartId, people_ref, "category", action_observation, description, modification_date_time, modification_date_mask
         FROM collection_maintenance
         WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id
        );
        FOR recPartsAfterDiverse IN
          SELECT id FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id
        LOOP
--           RAISE NOTICE '---Properties---';
          INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, date_from_mask, date_from, date_to_mask, date_to, property_unit, property_accuracy_unit, property_method, property_tool)
          (SELECT 'specimen_parts', newPartId, property_type, property_sub_type, property_qualifier, date_from_mask, date_from, date_to_mask, date_to, property_unit, property_accuracy_unit, property_method, property_tool
           FROM catalogue_properties
           WHERE id = recPartsAfterDiverse.id
          )
          RETURNING id INTO newDiverseId;
          INSERT INTO properties_values (property_ref, property_value, property_accuracy)
          (SELECT newDiverseId, property_value, property_accuracy
           FROM properties_values WHERE property_ref = recPartsAfterDiverse.id
          );
        END LOOP;
      END IF;
      intCounter := intCounter + 1;
    END LOOP;
    intCounter := 0;
  END LOOP;
  SELECT COUNT(*) INTO countStillCodes
  FROM specimen_parts
  WHERE EXISTS (SELECT 1 FROM codes WHERE referenced_relation = 'specimen_parts' AND record_id = specimen_parts.id AND code_category = 'main' GROUP BY referenced_relation, record_id, code_category HAVING COUNT(*) > 1);
  IF countStillCodes != 0 THEN
    RAISE NOTICE 'Still % parts that have multiple main codes ! - Not possible !!!', countStillCodes;
    rollback;
    return false;
  END IF;
  return response;
exception
  when others then
    RAISE WARNING 'Error in split_parts: %', SQLERRM;
    rollback;
    return false;
end;
$$;

select split_parts();

DROP FUNCTION IF EXISTS moveOrCreateProp (specimen_parts.id%TYPE, specimen_parts.id%TYPE, recPartsDetail, recPartsDetail) CASCADE;
DROP FUNCTION IF EXISTS createProperties (specimen_parts.id%TYPE, recPartsDetail) CASCADE;
DROP FUNCTION IF EXISTS createNewPart(specimen_parts.id%TYPE, recPartsDetail) CASCADE;
DROP TYPE IF EXISTS recPartsDetail CASCADE;
DROP FUNCTION IF EXISTS decrementCount(specimen_parts.id%TYPE, bigint) CASCADE;
DROP FUNCTION IF EXISTS createCodes(specimen_parts.id%TYPE, varchar) CASCADE;
DROP FUNCTION IF EXISTS resplit_parts () CASCADE;
DROP FUNCTION IF EXISTS split_parts () CASCADE;
DROP FUNCTION IF EXISTS column_exists(text,text) CASCADE;

ALTER TABLE specimen_parts ENABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;
ALTER TABLE specimen_parts ENABLE TRIGGER fct_cpy_trg_ins_update_dict_specimen_parts;
ALTER TABLE specimen_parts ENABLE TRIGGER trg_chk_specimenpartcollectionallowed;
ALTER TABLE specimen_parts ENABLE TRIGGER trg_cpy_path_specimen_parts;
ALTER TABLE specimen_parts ENABLE TRIGGER trg_trk_log_table_specimen_parts;
ALTER TABLE specimen_parts ENABLE TRIGGER trg_update_specimen_parts_darwin_flat;
ALTER TABLE codes ENABLE TRIGGER trg_chk_ref_record_catalogue_codes;
ALTER TABLE codes ENABLE TRIGGER trg_trk_log_table_codes;
ALTER TABLE codes ENABLE TRIGGER fct_cpy_trg_del_dict_codes;
ALTER TABLE codes ENABLE TRIGGER fct_cpy_trg_ins_update_dict_codes;
ALTER TABLE catalogue_properties ENABLE TRIGGER trg_chk_ref_record_catalogue_properties;
ALTER TABLE catalogue_properties ENABLE TRIGGER trg_trk_log_table_catalogue_properties;
ALTER TABLE properties_values ENABLE TRIGGER trg_trk_log_table_properties_values;
ALTER TABLE collection_maintenance ENABLE TRIGGER trg_chk_ref_record_collection_maintenance;
ALTER TABLE collection_maintenance ENABLE TRIGGER trg_trk_log_table_collection_maintenance;
ALTER TABLE collection_maintenance ENABLE TRIGGER fct_cpy_trg_del_dict_collection_maintenance;
ALTER TABLE collection_maintenance ENABLE TRIGGER fct_cpy_trg_ins_update_dict_collection_maintenance;
ALTER TABLE insurances ENABLE TRIGGER trg_chk_ref_record_insurances;
ALTER TABLE insurances ENABLE TRIGGER trg_trk_log_table_insurances;
ALTER TABLE insurances ENABLE TRIGGER fct_cpy_trg_del_dict_insurances;
ALTER TABLE insurances ENABLE TRIGGER fct_cpy_trg_ins_update_dict_insurances;
ALTER TABLE comments ENABLE TRIGGER trg_chk_ref_record_comments;
ALTER TABLE comments ENABLE TRIGGER trg_trk_log_table_comments;

commit;

\i ./recreate_flat.sql
