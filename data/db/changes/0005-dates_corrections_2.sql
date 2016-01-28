begin transaction;

alter table gtu disable trigger trg_cpy_idtocode_gtu;
alter table gtu disable trigger trg_cpy_location;
alter table gtu disable trigger trg_cpy_path_gtu;
alter table gtu disable trigger trg_trk_log_table_gtu;
alter table gtu disable trigger trg_update_gtu_darwin_flat;
alter table catalogue_properties disable trigger trg_chk_ref_record_catalogue_properties;
alter table catalogue_properties disable trigger trg_cpy_fulltoindex_catalogueproperties;
alter table catalogue_properties disable trigger trg_cpy_unified_values;
alter table catalogue_properties disable trigger trg_trk_log_table_catalogue_properties;
alter table identifications disable trigger fct_cpy_trg_del_dict_identifications;
alter table identifications disable trigger fct_cpy_trg_ins_update_dict_identifications;
alter table identifications disable trigger trg_chk_ref_record_identifications;
alter table identifications disable trigger trg_cpy_fulltoindex_identifications;
alter table identifications disable trigger trg_cpy_tofulltext_identifications;
alter table identifications disable trigger trg_trk_log_table_identifications;
alter table identifications disable trigger trg_words_ts_cpy_identification;

update gtu 
set gtu_from_date_mask = 0,
    gtu_from_date = '0001-01-01 00:00:00'::timestamp
where gtu_from_date_mask != 0
  and (gtu_from_date_mask & 32) = 0;

update gtu
set gtu_to_date_mask = gtu_from_date_mask,
    gtu_to_date = (extract(year from gtu_from_date) || '-' || extract(month from gtu_from_date) || '-' || extract(day from gtu_from_date) || ' ' || extract(hour from gtu_to_date) || ':' || extract(minute from gtu_to_date) || ':' || extract(second from gtu_to_date))::timestamp
where gtu_to_date_mask != 0
  and (gtu_to_date_mask & 32) = 0
  and gtu_from_date_mask != 0
  and (gtu_from_date_mask & 32) != 0;

update gtu 
set gtu_to_date_mask = 0,
    gtu_to_date = '2038-12-31 00:00:00'::timestamp
where gtu_to_date_mask != 0
  and (gtu_to_date_mask & 32) = 0;

update darwin_flat
set gtu_from_date_mask = 0,
    gtu_from_date = '0001-01-01 00:00:00'::timestamp
where gtu_ref IN (select id from gtu where gtu_from_date_mask = 0);

update darwin_flat
set gtu_to_date_mask = 0,
    gtu_to_date = '2038-12-31 00:00:00'::timestamp
where gtu_ref IN (select id from gtu where gtu_to_date_mask = 0);

update catalogue_properties 
set date_from_mask = 0,
    date_from = '0001-01-01 00:00:00'::timestamp
where date_from_mask != 0
  and (date_from_mask & 32) = 0;

update catalogue_properties
set date_to_mask = date_from_mask,
    date_to = (extract(year from date_from) || '-' || extract(month from date_from) || '-' || extract(day from date_from) || ' ' || extract(hour from date_to) || ':' || extract(minute from date_to) || ':' || extract(second from date_to))::timestamp
where date_to_mask != 0
  and (date_to_mask & 32) = 0
  and date_from_mask != 0
  and (date_from_mask & 32) != 0;

update catalogue_properties 
set date_to_mask = 0,
    date_to = '2038-12-31 00:00:00'::timestamp
where date_to_mask != 0
  and (date_to_mask & 32) = 0;


update identifications
set notion_date_mask = 0,
    notion_date = '0001-01-01 00:00:00'::timestamp
where notion_date_mask != 0
  and (notion_date_mask & 32) = 0;

alter table gtu enable trigger trg_cpy_idtocode_gtu;
alter table gtu enable trigger trg_cpy_location;
alter table gtu enable trigger trg_cpy_path_gtu;
alter table gtu enable trigger trg_trk_log_table_gtu;
alter table gtu enable trigger trg_update_gtu_darwin_flat;
alter table catalogue_properties enable trigger trg_chk_ref_record_catalogue_properties;
alter table catalogue_properties enable trigger trg_cpy_fulltoindex_catalogueproperties;
alter table catalogue_properties enable trigger trg_cpy_unified_values;
alter table catalogue_properties enable trigger trg_trk_log_table_catalogue_properties;
alter table identifications enable trigger fct_cpy_trg_del_dict_identifications;
alter table identifications enable trigger fct_cpy_trg_ins_update_dict_identifications;
alter table identifications enable trigger trg_chk_ref_record_identifications;
alter table identifications enable trigger trg_cpy_fulltoindex_identifications;
alter table identifications enable trigger trg_cpy_tofulltext_identifications;
alter table identifications enable trigger trg_trk_log_table_identifications;
alter table identifications enable trigger trg_words_ts_cpy_identification;

commit;
