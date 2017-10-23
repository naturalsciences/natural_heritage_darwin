begin transaction;

alter table people disable trigger fct_cpy_trg_del_dict_people;
alter table people disable trigger fct_cpy_trg_ins_update_dict_people;
alter table people disable trigger trg_chk_peopletype;
alter table people disable trigger trg_cpy_formattedname;
alter table people disable trigger trg_trk_log_table_people;
alter table people disable trigger trg_words_ts_cpy_people;
alter table users disable trigger fct_cpy_trg_del_dict_users;
alter table users disable trigger fct_cpy_trg_ins_update_dict_users;
alter table users disable trigger trg_cpy_formattedname;
alter table users disable trigger trg_unpromotion_remove_cols;
alter table users disable trigger trg_words_ts_cpy_users;
alter table gtu disable trigger trg_cpy_idtocode_gtu;
alter table gtu disable trigger trg_cpy_location;
alter table gtu disable trigger trg_cpy_path_gtu;
alter table gtu disable trigger trg_trk_log_table_gtu;
alter table gtu disable trigger trg_update_gtu_darwin_flat;
alter table catalogue_properties disable trigger trg_chk_ref_record_catalogue_properties;
alter table catalogue_properties disable trigger trg_cpy_fulltoindex_catalogueproperties;
alter table catalogue_properties disable trigger trg_cpy_unified_values;
alter table catalogue_properties disable trigger trg_trk_log_table_catalogue_properties;
alter table expeditions disable trigger trg_cpy_fulltoindex_expeditions;
alter table expeditions disable trigger trg_cpy_tofulltext_expeditions;
alter table expeditions disable trigger trg_trk_log_table_expeditions;
alter table expeditions disable trigger trg_update_expeditions_darwin_flat;
alter table expeditions disable trigger trg_words_ts_cpy_expeditions;

update users
set people_id = 45567
where people_id in (48160, 431, 41240);

update users
set people_id = 47275
where people_id = 763;

update users
set people_id = 5932
where people_id in (42964, 48137, 48138);

update users
set people_id = 43000
where people_id = 48191;

update collection_maintenance
set people_ref = 45567
where people_ref in (48160, 431, 41240);

update collection_maintenance
set people_ref = 47275
where people_ref = 763;

update collection_maintenance
set people_ref = 5932
where people_ref in (42964, 48137, 48138);

update collection_maintenance
set people_ref = 43000
where people_ref = 48191;

update catalogue_people
set people_ref = 45567
where people_ref in (48160, 431, 41240);

update catalogue_people
set people_ref = 47275
where people_ref = 763;

update catalogue_people
set people_ref = 5932
where people_ref in (42964, 48137, 48138);

update catalogue_people
set people_ref = 43000
where people_ref = 48191;

delete from people
where id in (48160, 431, 41240, 763, 42964, 48137, 48138, 48191);

update people
set activity_date_to = '2038-12-31'
where activity_date_to_mask = 0
  and activity_date_to != '2038-12-31';

update people
set activity_date_from = '0001-01-01'
where activity_date_from_mask = 0
  and activity_date_from != '0001-01-01';

update people
set end_date = '2038-12-31'
where end_date_mask = 0
  and end_date != '2038-12-31';

update people
set birth_date = '0001-01-01'
where birth_date_mask = 0
  and birth_date != '0001-01-01';

alter table people alter column birth_date set default '0001-01-01'::date;
alter table people alter column end_date set default '2038-12-31'::date;
alter table people alter column activity_date_to set default '2038-12-31'::date;

update users
set birth_date = '0001-01-01'
where birth_date_mask = 0
  and birth_date != '0001-01-01';

alter table users alter column birth_date set default '0001-01-01'::date;

update multimedia
set publication_date_from = '0001-01-01'
where publication_date_from_mask = 0
  and publication_date_from != '0001-01-01';

update multimedia
set publication_date_to = '2038-12-31'
where publication_date_to_mask = 0
  and publication_date_to != '2038-12-31';

alter table multimedia alter column publication_date_from set default '0001-01-01'::date;
alter table multimedia alter column publication_date_to set default '2038-12-31'::date;

update people_relationships
set activity_date_from = '0001-01-01'
where activity_date_from_mask = 0
  and activity_date_from != '0001-01-01';

update people_relationships
set activity_date_to = '2038-12-31'
where activity_date_to_mask = 0
  and activity_date_to != '2038-12-31';

alter table people_relationships alter column activity_date_from set default '0001-01-01'::date;
alter table people_relationships alter column activity_date_to set default '2038-12-31'::date;

update gtu
set gtu_from_date = '0001-01-01 00:00:00'
where gtu_from_date_mask = 0
  and gtu_from_date != '0001-01-01 00:00:00';

update gtu
set gtu_to_date = '2038-12-31 00:00:00'
where gtu_to_date_mask = 0
  and gtu_to_date != '2038-12-31 00:00:00';

alter table gtu alter column gtu_from_date set default '0001-01-01 00:00:00'::timestamp;
alter table gtu alter column gtu_to_date set default '2038-12-31 00:00:00'::timestamp;

update catalogue_properties
set date_from = '0001-01-01 00:00:00'
where date_from_mask = 0
  and date_from != '0001-01-01 00:00:00';

update catalogue_properties
set date_to = '2038-12-31 00:00:00'
where date_to_mask = 0
  and date_to != '2038-12-31 00:00:00';

alter table catalogue_properties alter column date_from set default '0001-01-01 00:00:00'::timestamp;
alter table catalogue_properties alter column date_to set default '2038-12-31 00:00:00'::timestamp;

update expeditions
set expedition_to_date = '2038-12-31'
where expedition_to_date_mask = 0
  and expedition_to_date != '2038-12-31';

update expeditions
set expedition_from_date = '0001-01-01'
where expedition_from_date_mask = 0
  and expedition_from_date != '0001-01-01';

alter table expeditions alter column expedition_from_date set default '0001-01-01'::date;
alter table expeditions alter column expedition_to_date set default '2038-12-31'::date;

alter table people enable trigger fct_cpy_trg_del_dict_people;
alter table people enable trigger fct_cpy_trg_ins_update_dict_people;
alter table people enable trigger trg_chk_peopletype;
alter table people enable trigger trg_cpy_formattedname;
alter table people enable trigger trg_trk_log_table_people;
alter table people enable trigger trg_words_ts_cpy_people;
alter table users enable trigger fct_cpy_trg_del_dict_users;
alter table users enable trigger fct_cpy_trg_ins_update_dict_users;
alter table users enable trigger trg_cpy_formattedname;
alter table users enable trigger trg_unpromotion_remove_cols;
alter table users enable trigger trg_words_ts_cpy_users;
alter table gtu enable trigger trg_cpy_idtocode_gtu;
alter table gtu enable trigger trg_cpy_location;
alter table gtu enable trigger trg_cpy_path_gtu;
alter table gtu enable trigger trg_trk_log_table_gtu;
alter table gtu enable trigger trg_update_gtu_darwin_flat;
alter table catalogue_properties enable trigger trg_chk_ref_record_catalogue_properties;
alter table catalogue_properties enable trigger trg_cpy_fulltoindex_catalogueproperties;
alter table catalogue_properties enable trigger trg_cpy_unified_values;
alter table catalogue_properties enable trigger trg_trk_log_table_catalogue_properties;
alter table expeditions enable trigger trg_cpy_fulltoindex_expeditions;
alter table expeditions enable trigger trg_cpy_tofulltext_expeditions;
alter table expeditions enable trigger trg_trk_log_table_expeditions;
alter table expeditions enable trigger trg_update_expeditions_darwin_flat;
alter table expeditions enable trigger trg_words_ts_cpy_expeditions;

commit;
