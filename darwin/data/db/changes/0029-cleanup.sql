SET search_path=darwin2, public;

ALTER TABLE gtu drop constraint fct_chk_onceInPath_gtu;
ALTER TABLE gtu drop constraint fk_gtu_gtu;
DROP INDEX idx_gtu_parent_ref;

alter table gtu drop column path;
alter table gtu drop column parent_ref;
ALTER TABLE gtu alter column code set default '';

drop view darwin_flat cascade;
alter table specimens_flat drop column gtu_parent_ref;
alter table specimens_flat drop column gtu_path;


create view darwin_flat as 
  select

 row_number() OVER (ORDER BY s.id) AS id, 

  s.category,
  s.collection_ref,
  s.expedition_ref,
  s.gtu_ref,
  s.taxon_ref,
  s.litho_ref,
  s.chrono_ref,
  s.lithology_ref,
  s.mineral_ref,
  s.host_taxon_ref,
  s.host_specimen_ref,
  s.host_relationship,
  s.acquisition_category,
  s.acquisition_date_mask,
  s.acquisition_date,
  s.station_visible,
  s.ig_ref,


  f.collection_type,
  f.collection_code,
  f.collection_name,
  f.collection_is_public,
  f.collection_parent_ref,
  f.collection_path,
  f.expedition_name,
  f.expedition_name_ts,
  f.expedition_name_indexed,

  f.gtu_code,
  f.gtu_from_date_mask,
  f.gtu_from_date,
  f.gtu_to_date_mask,
  f.gtu_to_date,
  f.gtu_elevation,
  f.gtu_elevation_accuracy,
  f.gtu_tag_values_indexed,
  f.gtu_country_tag_value,
  f.gtu_country_tag_indexed,
  f.gtu_province_tag_value,
  f.gtu_province_tag_indexed,
  f.gtu_others_tag_value,
  f.gtu_others_tag_indexed,
  f.gtu_location,

  f.taxon_name,
  f.taxon_name_indexed,
  f.taxon_name_order_by,
  f.taxon_level_ref,
  f.taxon_level_name,
  f.taxon_status,
  f.taxon_path,
  f.taxon_parent_ref,
  f.taxon_extinct,

  f.litho_name,
  f.litho_name_indexed,
  f.litho_name_order_by,
  f.litho_level_ref,
  f.litho_level_name,
  f.litho_status,
  f.litho_local,
  f.litho_color,
  f.litho_path,
  f.litho_parent_ref,

  f.chrono_name,
  f.chrono_name_indexed,
  f.chrono_name_order_by,
  f.chrono_level_ref,
  f.chrono_level_name,
  f.chrono_status,
  f.chrono_local,
  f.chrono_color,
  f.chrono_path,
  f.chrono_parent_ref,

  f.lithology_name,
  f.lithology_name_indexed,
  f.lithology_name_order_by,
  f.lithology_level_ref,
  f.lithology_level_name,
  f.lithology_status,
  f.lithology_local,
  f.lithology_color,
  f.lithology_path,
  f.lithology_parent_ref,

  f.mineral_name,
  f.mineral_name_indexed,
  f.mineral_name_order_by,
  f.mineral_level_ref,
  f.mineral_level_name,
  f.mineral_status,
  f.mineral_local,
  f.mineral_color,
  f.mineral_path,
  f.mineral_parent_ref,

  f.host_taxon_name,
  f.host_taxon_name_indexed,
  f.host_taxon_name_order_by,
  f.host_taxon_level_ref,
  f.host_taxon_level_name,
  f.host_taxon_status,
  f.host_taxon_path,
  f.host_taxon_parent_ref,
  f.host_taxon_extinct,

  f.ig_num,
  f.ig_num_indexed,
  f.ig_date_mask,
  f.ig_date,

  s.id as spec_ref,

  spec_ident_ids,
  spec_coll_ids,
  spec_don_sel_ids,
  i.ind_ident_ids as ind_ident_ids,

  f.with_types,
  f.with_individuals,
  COALESCE(i.with_parts,false) as with_parts,

  i.id as individual_ref,
  coalesce(i.type, 'specimen') as individual_type,
  coalesce(i.type_group, 'specimen') as individual_type_group,
  coalesce(i.type_search, 'specimen') as individual_type_search,
  coalesce(i.sex, 'undefined') as individual_sex,
  coalesce(i.state, 'not applicable') as individual_state,
  coalesce(i.stage, 'undefined') as individual_stage,
  coalesce(i.social_status, 'not applicable') as individual_social_status,
  coalesce(i.rock_form, 'not applicable') as individual_rock_form,
  coalesce(i.specimen_individuals_count_min, 1) as individual_count_min,
  coalesce(i.specimen_individuals_count_max, 1) as individual_count_max,
  p.id as part_ref,
  p.specimen_part as part,
  p.specimen_status as part_status,
  p.institution_ref,
  p.building,
  p.floor ,
  p.room ,
  p.row  ,
  p.shelf ,
  p.container ,
  p.sub_container ,
  p.container_type ,
  p.sub_container_type ,
  p.container_storage ,
  p.sub_container_storage ,
  p.specimen_part_count_min as part_count_min,
  p.specimen_part_count_max as part_count_max,
  p.specimen_status,
  p.complete,
  p.surnumerary


  from specimens s
  INNER JOIN specimens_flat f on f.specimen_ref = s.id
  LEFT JOIN specimen_individuals  i ON s.id = i.specimen_ref 
  LEFT JOIN specimen_parts p ON i.id = p.specimen_individual_ref
;

ALTER VIEW darwin_flat OWNER TO darwin2;

GRANT ALL ON darwin_flat TO darwin2;
GRANT SELECT ON darwin_flat TO d2viewer;


DROP TRIGGER trg_cpy_idToCode_gtu ON gtu;
DROP TRIGGER trg_cpy_path_gtu ON gtu;

DROP TRIGGER fct_chk_upper_level_for_childrens_people ON people_languages;
DROP TRIGGER fct_chk_upper_level_for_childrens_users ON users_languages;


DROP function fct_cpy_idToCode();
DROP function fct_chk_one_pref_language();
DROP function fct_compose_timestamp(day integer, month integer, year integer, hour integer, minute integer, second integer);
DROP FUNCTION fct_compose_date(day integer, month integer, year integer);

drop function concat(text, text);
drop function concat(text, text, text);
drop function search_words_to_query(tbl_name words.referenced_relation%TYPE, fld_name words.field_name%TYPE, value varchar, op varchar);

drop function datesOverlaps(start1 date, end1 date, start2 date, end2 date);

\i  ../createfunctions.sql

\i ../reports/ticketing/labeling.sql
