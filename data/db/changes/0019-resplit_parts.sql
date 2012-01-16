begin;

ALTER TABLE specimen_parts DISABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;

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

drop function if exists resplit_parts();
create or replace function resplit_parts () returns boolean language plpgsql as
$$
declare
  recPartsDetails RECORD;
  recCounting RECORD;
  part_id specimen_parts.id%TYPE := 0;
  new_part_id specimen_parts.id%TYPE;
  rowsUpdated integer;
  rowsCounted integer;
begin
  FOR recPartsDetails IN    select specimen_individual_ref,  specimen_parts.id as parts_id, fullToIndex(sgr_code) as main_code, 
                                   fullToIndex(bat_unique_rbins_code) as rbins_code, fullToIndex(bat_code) as batch_main_code, fullToIndex(bat_inventory_code) as inventory_code,
                                   specimen_part, complete, building, "floor", room, specimen_parts.row as row, specimen_parts.shelf as shelf, specimen_parts.container as container, specimen_parts.sub_container as sub_container,
                                   lower(cty_type_short_descr) as container_and_sub_container_type, specimen_parts.container_type as container_type, specimen_parts.sub_container_type as sub_container_type,
                                   lower(sto_storage) as container_and_sub_container_storage, specimen_parts.container_storage as container_storage, specimen_parts.sub_container_storage as sub_container_storage,
                                   specimen_status, 
                                   case when sgr_number_in_group < 1 or sgr_number_in_group is null then 1 else sgr_number_in_group end as part_count_min,
                                   specimen_part_count_min,
                                   specimen_part_count_max,
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
                               and coalesce(building,'') = 
                                   case 
                                     when sgr_room_nr = 0 then '' 
                                     else bui_name 
                                   end::varchar
                               and coalesce(floor,'') = 
                                   case 
                                     when sgr_room_nr = 0 then '' 
                                     else bfl_floor 
                                   end::varchar
                               and coalesce(room, '') = 
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
                            where bat_collection_id_nr between 1 and 8
                                  /*exists (select 1 from comments where comment is not null and referenced_relation = 'specimen_parts' and record_id = specimen_parts.id limit 1)*/
                            order by new_id, specimen_part, main_code 
                            limit 200
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
      SELECT specimen_part_count_min, specimen_part_count_max INTO recCounting
      FROM specimen_parts WHERE id = part_id;
      RAISE NOTICE '+Counting: %', recCounting;
      
    ELSE
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
      UPDATE codes
      SET record_id = new_part_id
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND full_code_order_by IN (fullToIndex(recPartsDetails.main_code),
                                   fullToIndex(recPartsDetails.rbins_code),
                                   fullToIndex(recPartsDetails.inventory_code),
                                   fullToIndex(recPartsDetails.batch_main_code)
                                  );
      SELECT specimen_part_count_min, specimen_part_count_max INTO recCounting
      FROM specimen_parts WHERE id = part_id;
      RAISE NOTICE '--New Counting: %', recCounting;
    END IF;
  END LOOP;
  RETURN TRUE;
exception
  when others then
    RAISE WARNING 'Error in main function: %', SQLERRM;
end;
$$;

SELECT resplit_parts();

ALTER TABLE specimen_parts ENABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;

rollback;