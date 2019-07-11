\o /var/lib/postgresql/output.log

SET search_path = darwin2, darwin1, public;

BEGIN;

create or replace function correct_part_count() returns boolean language plpgsql as
$$
declare
  partsToCorrect RECORD;
begin
  FOR partsToCorrect IN
      select ind.id as individual_id, ind.type as individual_type, pa.id as part_id, pa.specimen_part as part, pa.specimen_part_count_min as part_count, case when sgr.sgr_number_in_group = 0 then 1 else sgr.sgr_number_in_group end as old_part_count
      from (specimen_individuals as ind inner join specimen_parts as pa on ind.id = pa.specimen_individual_ref)
            inner join
            (darwin1.id_refs as re inner join darwin1.tbl_specimen_groups as sgr on sgr.sgr_id_ctn = re.old_id and re.system = 'individuals'
              inner join (darwin1.tbl_rooms as ro
                          inner join (darwin1.tbl_buildings as bui inner join darwin1.tbl_building_floors as bf on bui.bui_id_ctn = bf.bfl_building_nr) on bf.bfl_id_ctn = ro.rom_building_floor_nr
                        ) on ro.rom_id_ctn = sgr.sgr_room_nr
            )
            on re.new_id = ind.id
      and sgr_number_in_group != pa.specimen_part_count_min
      and bui.bui_name = coalesce(pa.building,'')
      and bf.bfl_floor = coalesce(pa.floor, '')
      and ro.rom_code = coalesce(pa.room, '')
      and coalesce(sgr.sgr_row,'') = coalesce(pa.row, '')
      and coalesce(sgr.sgr_shelf, '') = coalesce(pa.shelf,'')
      and coalesce(sgr.sgr_container, '') = coalesce(pa.container,'')
      and fullToIndex(sgr.sgr_code) = (select full_code_order_by from codes where referenced_relation = 'specimen_parts' and record_id = pa.id and code_category = 'main')
      and not exists (select 1 from users_tracking where referenced_relation = 'specimen_parts' and record_id = pa.id and action = 'update' and (old_value -> 'specimen_part_count_min') != (new_value -> 'specimen_part_count_min'))
      order by ind.id, pa.id
  LOOP
    update specimen_parts
    set specimen_part_count_min = partsToCorrect.old_part_count,
        specimen_part_count_max = partsToCorrect.old_part_count
    where id = partsToCorrect.part_id;
    update zzz_to_correct
    set new_part_min = partsToCorrect.old_part_count,
        new_part_max = partsToCorrect.old_part_count
    where part_id = partsToCorrect.part_id;
  END LOOP;
  return true;
exception
  when others then
    return false;
    RAISE WARNING 'Error: %', SQLERRM;
    rollback;
end;
$$;

select sum(specimen_part_count_min) as part_count_before_update ,s.type as type_before_update
from specimen_parts p join specimen_individuals s
on p.specimen_individual_ref = s.id
group by s.type
order by s.type;

select count(*) as counting_before_update from zzz_to_correct where new_part_min = part_min;

select correct_part_count();

select count(*) as counting_after_update from zzz_to_correct where new_part_min = part_min;

select sum(specimen_part_count_min) as part_count_before_update ,s.type as type_before_update
from specimen_parts p join specimen_individuals s
on p.specimen_individual_ref = s.id
group by s.type
order by s.type;

drop function correct_part_count();

commit;

analyze specimen_parts;
