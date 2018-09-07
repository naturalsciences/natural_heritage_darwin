set search_path=darwin1,darwin2,public;

begin;

/* Insertion of insurances */
insert into insurances (referenced_relation, record_id, insurance_value)
(
  select 'specimen_parts' as referenced_relation, spp.id as record_id, sum(bat_value) as insurance_value
  from (darwin1.tbl_batches inner join darwin1.id_refs on bat_id_ctn = old_id and darwin1.id_refs.system = 'batches')
       inner join specimens on specimens.id = new_id
       inner join specimen_individuals as sind on sind.specimen_ref = specimens.id
       inner join specimen_parts as spp on spp.specimen_individual_ref = sind.id
  where bat_value is not null and bat_value > 0
    and not exists (select 1 from insurances where referenced_relation = 'specimen_parts' and record_id = spp.id)
  group by spp.id
);

commit;
