begin;

ALTER TABLE specimen_parts DISABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;

CREATE TYPE recPartsDetail AS (
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
                                part_comment varchar
                              );

create or replace function decrementCount(IN partId specimen_parts.id%TYPE, IN decrementVal bigint) RETURNS boolean language plpgsql AS
$$
declare
  result boolean := true;
begin
  UPDATE specimen_parts
  SET specimen_part_count_min = case when specimen_part_count_min = 1 then specimen_part_count_min else specimen_part_count_min - decrementVal end,
      specimen_part_count_max = case when specimen_part_count_max = 1 then specimen_part_count_max else specimen_part_count_max - decrementVal end
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
  ELSIF substr(codeToSplit, 1, 4) IN ('AST.', 'CRI.', 'HOL.', 'INV.', 'NIV.', 'OPH.', 'POP.') THEN
    codes_prefix := substr(codeToSplit, 1, 4);
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 4 THEN
      code_to_insert:= substr(codeToSplit, 5);
    END IF;
  ELSIF substr(codeToSplit, 1, 3) ='IN.' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 3 THEN
      code_to_insert:= substr(codeToSplit, 4);
    END IF;
  ELSIF substr(codeToSplit, 1, 5) = 'HIST.' THEN
    codes_prefix := 'HIST';
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 5 THEN
      code_to_insert:= substr(codeToSplit, 6);
    END IF;
  ELSIF substr(codeToSplit, 1, 6) = 'KREPS.' THEN
    codes_prefix := 'KREPS';
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 6 THEN
      code_to_insert:= substr(codeToSplit, 7);
    END IF;
  ELSIF codeToSplit ~ E'^[PRT]\\s{1,2}\\d+\\s{1}\\w+\\W+\\w+$' THEN
    codes_prefix := substr(codeToSplit, 1, 1);
    codes_prefix_separator := ' ';
    code_to_insert:= trim(substring(trim(substr(codeToSplit, 2)) from E'^\\d+\\s{1}'));
    codes_suffix_separator := ' ';
    codes_suffix := substring(codeToSplit from E'\\w+\\W+\\w+$');
  ELSIF codeToSplit Like 'ALEX %' THEN
    code_to_insert:= codeToSplit;
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

create or replace function createNewPart(IN part_id specimen_parts.id%TYPE, IN recPartsDetails recPartsDetail) RETURNS specimen_parts.id%TYPE language plpgsql AS
$$
DECLARE  
  new_part_id specimen_parts.id%TYPE;
  recNewProperties RECORD;
  recOldProperties RECORD;
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
  IF code_count > 1 THEN
    UPDATE codes
    SET record_id = new_part_id
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND code_category = 'main'
      AND full_code_order_by IN (recPartsDetails.main_code, recPartsDetails.rbins_code, recPartsDetails.inventory_code, recPartsDetails.batch_main_code);
    GET DIAGNOSTICS code_count = ROW_COUNT;
    IF code_count = 0 THEN
      IF createCodes (new_part_id, recPartsDetails.main_code) < 0 THEN
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
       AND full_code_order_by IN (recPartsDetails.main_code, recPartsDetails.rbins_code, recPartsDetails.inventory_code, recPartsDetails.batch_main_code)
    );
    GET DIAGNOSTICS code_count = ROW_COUNT;
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
    RAISE WARNING 'Error: %', SQLERRM;
    return -1;
END;
$$;

drop function if exists resplit_parts();
create or replace function resplit_parts () returns boolean language plpgsql as
$$
declare
  recPartsDetails recPartsDetail;
  recCounting RECORD;
  part_id specimen_parts.id%TYPE := 0;
  new_part_id specimen_parts.id%TYPE;
  rowsUpdated integer;
  rowsCounted integer;
  code_count integer;
  coding varchar;
  recCodes varchar[];
begin
  FOR recPartsDetails IN select specimen_individual_ref,  specimen_parts.id as parts_id, fullToIndex(sgr_code) as main_code, 
                                fullToIndex(bat_unique_rbins_code) as rbins_code, fullToIndex(bat_code) as batch_main_code, fullToIndex(bat_inventory_code) as inventory_code, sgr_code as old_main_code,
                                specimen_part, building, coalesce(building, '') as coalesced_building, 
                                "floor", coalesce("floor", '') as coalesced_floor, 
                                room, coalesce("room", '') as coalesced_room, 
                                specimen_parts.row as row, coalesce("row", '') as coalesced_row,  
                                specimen_parts.shelf as shelf, coalesce("shelf", '') as coalesced_shelf, 
                                specimen_parts.container as container, coalesce("container", '') as coalesced_container, 
                                specimen_parts.sub_container as sub_container, coalesce("sub_container", '') as coalesced_sub_container, 
                                lower(cty_type_short_descr) as container_and_sub_container_type, specimen_parts.container_type as container_type, specimen_parts.sub_container_type as sub_container_type,
                                lower(sto_storage) as container_and_sub_container_storage, specimen_parts.container_storage as container_storage, specimen_parts.sub_container_storage as sub_container_storage,
                                specimen_status, 
                                case when sgr_number_in_group < 1 or sgr_number_in_group is null then 1 else sgr_number_in_group end as part_count_min,
                                specimen_part_count_min,
                                specimen_part_count_max,
                                case 
                                  when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                                  else false 
                                end as complete,
                                sgr_comment as part_comment
                          from darwin1.tbl_specimen_groups 
                          inner join darwin1.id_refs 
                          on sgr_id_ctn = old_id and system = 'individuals'
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
                          inner join 
                          specimen_parts
                          on     specimen_individual_ref = new_id
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
                          /*where bat_collection_id_nr between 1 and 8*/
                                where bat_collection_id_nr = 133
                                /*exists (select 1 from comments where comment is not null and referenced_relation = 'specimen_parts' and record_id = specimen_parts.id limit 1)*/
                          order by new_id desc, specimen_part, main_code 
                          limit 50
  LOOP
    IF part_id != recPartsDetails.parts_id THEN
      RAISE NOTICE 'Infos: %', recPartsDetails;
      part_id := recPartsDetails.parts_id;
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
      SELECT count(full_code_order_by) INTO code_count
      FROM codes
      where referenced_relation = 'specimen_parts'
        and record_id = part_id
        and code_category = 'main';
      IF code_count != 0 then
        select count(full_code)
        into code_count
        from (
              select full_code_order_by as full_code
              from codes
              where referenced_relation = 'specimen_parts'
                and code_category = 'main'
                and record_id = part_id
              except
              (
                select fullToIndex(sgr_code) as full_code
                from darwin1.tbl_specimen_groups 
                inner join darwin1.id_refs on sgr_id_ctn = old_id and system = 'individuals'
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
                where new_id = recPartsDetails.specimen_individual_ref
                  and lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation')) = recPartsDetails.specimen_part
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bui_name 
                      end::varchar = recPartsDetails.coalesced_building
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bfl_floor 
                      end::varchar = recPartsDetails.coalesced_floor
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else rom_code 
                      end::varchar = recPartsDetails.coalesced_room
                  and coalesce(sgr_row, '') = recPartsDetails.coalesced_row
                  and coalesce(sgr_shelf, '') = recPartsDetails.coalesced_shelf
                  and coalesce(sgr_container, '') = recPartsDetails.coalesced_container
                  and coalesce(sgr_subcontainer, '') = recPartsDetails.coalesced_sub_container
                  and case 
                        when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                        else false 
                      end = recPartsDetails.complete
                union
                select fullToIndex(bat_code) as full_code
                from darwin1.tbl_specimen_groups 
                inner join darwin1.id_refs on sgr_id_ctn = old_id and system = 'individuals'
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
                inner join darwin1.tbl_batches on sgr_batch_nr = bat_id_ctn
                where new_id = recPartsDetails.specimen_individual_ref
                  and lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation')) = recPartsDetails.specimen_part
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bui_name 
                      end::varchar = recPartsDetails.coalesced_building
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bfl_floor 
                      end::varchar = recPartsDetails.coalesced_floor
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else rom_code 
                      end::varchar = recPartsDetails.coalesced_room
                  and coalesce(sgr_row, '') = recPartsDetails.coalesced_row
                  and coalesce(sgr_shelf, '') = recPartsDetails.coalesced_shelf
                  and coalesce(sgr_container, '') = recPartsDetails.coalesced_container
                  and coalesce(sgr_subcontainer, '') = recPartsDetails.coalesced_sub_container
                  and case 
                        when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                        else false 
                      end = recPartsDetails.complete
                union
                select fullToIndex(bat_unique_rbins_code) as full_code
                from darwin1.tbl_specimen_groups 
                inner join darwin1.id_refs on sgr_id_ctn = old_id and system = 'individuals'
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
                inner join darwin1.tbl_batches on sgr_batch_nr = bat_id_ctn
                where new_id = recPartsDetails.specimen_individual_ref
                  and lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation')) = recPartsDetails.specimen_part
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bui_name 
                      end::varchar = recPartsDetails.coalesced_building
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bfl_floor 
                      end::varchar = recPartsDetails.coalesced_floor
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else rom_code 
                      end::varchar = recPartsDetails.coalesced_room
                  and coalesce(sgr_row, '') = recPartsDetails.coalesced_row
                  and coalesce(sgr_shelf, '') = recPartsDetails.coalesced_shelf
                  and coalesce(sgr_container, '') = recPartsDetails.coalesced_container
                  and coalesce(sgr_subcontainer, '') = recPartsDetails.coalesced_sub_container
                  and case 
                        when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                        else false 
                      end = recPartsDetails.complete
                union
                select fullToIndex(bat_inventory_code) as full_code
                from darwin1.tbl_specimen_groups 
                inner join darwin1.id_refs on sgr_id_ctn = old_id and system = 'individuals'
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
                inner join darwin1.tbl_batches on sgr_batch_nr = bat_id_ctn
                where new_id = recPartsDetails.specimen_individual_ref
                  and lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation')) = recPartsDetails.specimen_part
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bui_name 
                      end::varchar = recPartsDetails.coalesced_building
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bfl_floor 
                      end::varchar = recPartsDetails.coalesced_floor
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else rom_code 
                      end::varchar = recPartsDetails.coalesced_room
                  and coalesce(sgr_row, '') = recPartsDetails.coalesced_row
                  and coalesce(sgr_shelf, '') = recPartsDetails.coalesced_shelf
                  and coalesce(sgr_container, '') = recPartsDetails.coalesced_container
                  and coalesce(sgr_subcontainer, '') = recPartsDetails.coalesced_sub_container
                  and case 
                        when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                        else false 
                      end = recPartsDetails.complete
              ) 
            ) as x;
        IF code_count > 0 THEN
          SELECT createNewPart(part_id, recPartsDetails) INTO new_part_id;
          IF new_part_id < 0 THEN
            return false;
          END IF;
        END IF;
      ELSE
        IF createCodes (part_id, recPartsDetails.old_main_code) < 0 THEN
          return false;
        END IF;
      END IF;
    ELSE
      RAISE NOTICE '-Infos: %', recPartsDetails;
      SELECT createNewPart(part_id, recPartsDetails) INTO new_part_id;
      IF new_part_id < 0 THEN
        return false;
      END IF;
    END IF;
  END LOOP;
  return true;
exception
  when others then
    RAISE WARNING 'Error in main function: %', SQLERRM;
end;
$$;

create or replace function split_parts() returns boolean language plpgsql
AS
$$
declare
  response boolean;
begin
  select resplit_parts() INTO response;
  IF NOT response THEN
    ROLLBACK;
  END IF;
  return response;
end;
$$;

SELECT split_parts();

DROP TYPE IF EXISTS recPartsDetail CASCADE;

ALTER TABLE specimen_parts ENABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;

rollback;