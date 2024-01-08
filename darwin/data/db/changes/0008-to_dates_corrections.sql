begin transaction;

alter table expeditions disable trigger trg_cpy_fulltoindex_expeditions;
alter table expeditions disable trigger trg_cpy_tofulltext_expeditions;
alter table expeditions disable trigger trg_trk_log_table_expeditions;
alter table expeditions disable trigger trg_update_expeditions_darwin_flat;
alter table expeditions disable trigger trg_words_ts_cpy_expeditions;
alter table catalogue_properties disable trigger trg_chk_ref_record_catalogue_properties;
alter table catalogue_properties disable trigger trg_cpy_fulltoindex_catalogueproperties;
alter table catalogue_properties disable trigger trg_cpy_unified_values;
alter table catalogue_properties disable trigger trg_trk_log_table_catalogue_properties;
alter table gtu disable trigger trg_cpy_idtocode_gtu;
alter table gtu disable trigger trg_cpy_location;
alter table gtu disable trigger trg_cpy_path_gtu;
alter table gtu disable trigger trg_trk_log_table_gtu;
alter table people disable trigger fct_cpy_trg_del_dict_people;
alter table people disable trigger fct_cpy_trg_ins_update_dict_people;
alter table people disable trigger trg_chk_peopletype;
alter table people disable trigger trg_cpy_formattedname;
alter table people disable trigger trg_trk_log_table_people;
alter table people disable trigger trg_words_ts_cpy_people;

update expeditions
set expedition_to_date = 
  case
    when expedition_to_date_mask = 32 then
      (extract(year from expedition_to_date) || '-12-31')
    else
      case
        when extract(month from expedition_to_date) in (1, 3, 5, 7, 8, 10, 12) then (extract(year from expedition_to_date) || '-' || extract(month from expedition_to_date) || '-31')
        when extract(month from expedition_to_date) in (2) then (extract(year from expedition_to_date) || '-' || extract(month from expedition_to_date) || '-28')
        else (extract(year from expedition_to_date) || '-' || extract(month from expedition_to_date) || '-30')
      end
  end::date
where (expedition_to_date_mask = 32 and (extract(month from expedition_to_date) < 12 or extract(day from expedition_to_date) < 31))
   or (expedition_to_date_mask = 48 and extract(day from expedition_to_date) < 31);

update catalogue_properties
set date_to = 
  case
    when date_to_mask = 32 then
      (extract(year from date_to) || '-12-31 23:59:59')
    else
      case
        when extract(month from date_to) in (1, 3, 5, 7, 8, 10, 12) then (extract(year from date_to) || '-' || extract(month from date_to) || '-31 23:59:59')
        when extract(month from date_to) in (2) then (extract(year from date_to) || '-' || extract(month from date_to) || '-28 23:59:59')
        else (extract(year from date_to) || '-' || extract(month from date_to) || '-30 23:59:59')
      end
  end::timestamp
where (date_to_mask = 32 and (extract(month from date_to) < 12 or extract(day from date_to) < 31))
   or (date_to_mask = 48 and extract(day from date_to) < 31);

update gtu
set gtu_to_date = 
  case
    when gtu_to_date_mask = 32 then
      (extract(year from gtu_to_date) || '-12-31 23:59:59')
    else
      case
        when extract(month from gtu_to_date) in (1, 3, 5, 7, 8, 10, 12) then (extract(year from gtu_to_date) || '-' || extract(month from gtu_to_date) || '-31 23:59:59')
        when extract(month from gtu_to_date) in (2) then (extract(year from gtu_to_date) || '-' || extract(month from gtu_to_date) || '-28 23:59:59')
        else (extract(year from gtu_to_date) || '-' || extract(month from gtu_to_date) || '-30 23:59:59')
      end
  end::timestamp
where (gtu_to_date_mask = 32 and (extract(month from gtu_to_date) < 12 or extract(day from gtu_to_date) < 31))
   or (gtu_to_date_mask = 48 and extract(day from gtu_to_date) < 31);

update people
set activity_date_to = 
  case
    when activity_date_to_mask = 32 then
      (extract(year from activity_date_to) || '-12-31')
    else
      case
        when extract(month from activity_date_to) in (1, 3, 5, 7, 8, 10, 12) then (extract(year from activity_date_to) || '-' || extract(month from activity_date_to) || '-31')
        when extract(month from activity_date_to) in (2) then (extract(year from activity_date_to) || '-' || extract(month from activity_date_to) || '-28')
        else (extract(year from activity_date_to) || '-' || extract(month from activity_date_to) || '-30')
      end
  end::date
where (activity_date_to_mask = 32 and (extract(month from activity_date_to) < 12 or extract(day from activity_date_to) < 31))
   or (activity_date_to_mask = 48 and extract(day from activity_date_to) < 31);

alter table expeditions enable trigger trg_cpy_fulltoindex_expeditions;
alter table expeditions enable trigger trg_cpy_tofulltext_expeditions;
alter table expeditions enable trigger trg_trk_log_table_expeditions;
alter table expeditions enable trigger trg_update_expeditions_darwin_flat;
alter table expeditions enable trigger trg_words_ts_cpy_expeditions;
alter table catalogue_properties enable trigger trg_chk_ref_record_catalogue_properties;
alter table catalogue_properties enable trigger trg_cpy_fulltoindex_catalogueproperties;
alter table catalogue_properties enable trigger trg_cpy_unified_values;
alter table catalogue_properties enable trigger trg_trk_log_table_catalogue_properties;
alter table gtu enable trigger trg_cpy_idtocode_gtu;
alter table gtu enable trigger trg_cpy_location;
alter table gtu enable trigger trg_cpy_path_gtu;
alter table gtu enable trigger trg_trk_log_table_gtu;
alter table people enable trigger fct_cpy_trg_del_dict_people;
alter table people enable trigger fct_cpy_trg_ins_update_dict_people;
alter table people enable trigger trg_chk_peopletype;
alter table people enable trigger trg_cpy_formattedname;
alter table people enable trigger trg_trk_log_table_people;
alter table people enable trigger trg_words_ts_cpy_people;

commit;
