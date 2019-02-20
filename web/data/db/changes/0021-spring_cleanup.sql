SET search_path = darwin2, public;

DROP INDEX IF EXISTS idx_associated_multimedia_multimedia_ref;
DROP INDEX IF EXISTS idx_catalogue_levels_level_type;

DROP INDEX IF EXISTS idx_cat_peo;
DROP INDEX IF EXISTS idx_catalogue_people_referenced_record;

CREATE INDEX CONCURRENTLY idx_catalogue_people_referenced_record on catalogue_people(referenced_relation, record_id);

drop index IF EXISTS idx_users_tracking_modification_date_time;
drop index IF EXISTS idx_users_tracking_user_ref;
create index idx_users_tracking_user_ref_date_time on users_tracking (user_ref, modification_date_time desc);

DROP INDEX IF EXISTS idx_catalogue_properties_referenced_record;
DROP INDEX IF EXISTS idx_class_vernacular_names_referenced_record;
DROP INDEX IF EXISTS idx_collections_institution_ref;
DROP INDEX IF EXISTS idx_collections_rights_collection_ref;
DROP INDEX IF EXISTS idx_identifications_referenced_record;
DROP INDEX IF EXISTS idx_insurances_referenced_record;
DROP INDEX IF EXISTS idx_my_saved_searches_user_ref;
DROP INDEX IF EXISTS idx_my_widgets_user_ref;
DROP INDEX IF EXISTS idx_my_saved_searches_user_ref ;
DROP INDEX IF EXISTS idx_my_widgets_user_ref;
DROP INDEX IF EXISTS idx_people_is_physical;
DROP INDEX IF EXISTS idx_people_languages_people_ref;
DROP INDEX IF EXISTS idx_possible_upper_levels_level_ref;
DROP INDEX IF EXISTS idx_specimen_collecting_methods_specimen_ref;
DROP INDEX IF EXISTS idx_specimen_collecting_tools_specimen_ref;
DROP INDEX IF EXISTS idx_specimen_individuals_specimen_ref;
DROP INDEX IF EXISTS idx_specimens_accompanying_specimen_ref;
DROP INDEX IF EXISTS idx_tag_groups_gtu_ref;
DROP INDEX IF EXISTS idx_users_languages_users_ref;
DROP INDEX IF EXISTS idx_words_table_field;


DROP TABLE IF EXISTS habitats CASCADE;

DROP sequence IF EXISTS habitats_id_seq;
DROP table IF EXISTS multimedia_keywords;
DROP table IF EXISTS soortenregister;


DROP table IF EXISTS  associated_multimedia;
DROP sequence IF EXISTS  associated_multimedia_id_seq;

DROP table IF EXISTS people_multimedia;
DROP table IF EXISTS users_multimedia;
DROP table IF EXISTS template_people_users_multimedia;

UPDATE my_widgets SET group_name = 'comments' WHERE category = 'individuals_widget' and group_name = 'specimenIndividualComments';
