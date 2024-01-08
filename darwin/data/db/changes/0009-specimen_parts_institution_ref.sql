begin;

alter table specimen_parts
  ADD institution_ref integer;

comment on column specimen_parts.institution_ref is 'Institution where object physicaly stored';

ALTER TABLE specimen_parts DISABLE TRIGGER fct_cpy_trg_ins_update_dict_specimen_parts;
ALTER TABLE specimen_parts DISABLE TRIGGER trg_chk_specimenpartcollectionallowed;
ALTER TABLE specimen_parts DISABLE TRIGGER trg_cpy_path_specimen_parts;
ALTER TABLE specimen_parts DISABLE TRIGGER trg_trk_log_table_specimen_parts;
ALTER TABLE specimen_parts DISABLE TRIGGER trg_update_specimen_parts_darwin_flat;

update specimen_parts 
set institution_ref  = 47859;

ALTER TABLE specimen_parts ENABLE TRIGGER fct_cpy_trg_ins_update_dict_specimen_parts;
ALTER TABLE specimen_parts ENABLE TRIGGER trg_chk_specimenpartcollectionallowed;
ALTER TABLE specimen_parts ENABLE TRIGGER trg_cpy_path_specimen_parts;
ALTER TABLE specimen_parts ENABLE TRIGGER trg_trk_log_table_specimen_parts;
ALTER TABLE specimen_parts ENABLE TRIGGER trg_update_specimen_parts_darwin_flat;

commit;
