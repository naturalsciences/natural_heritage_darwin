
DROP INDEX IF EXISTS idx_associated_multimedia_multimedia_ref;
DROP INDEX IF EXISTS idx_catalogue_levels_level_type;

DROP INDEX IF EXISTS idx_cat_peo;
DROP INDEX IF EXISTS idx_catalogue_people_referenced_record;

CREATE INDEX CONCURRENTLY idx_catalogue_people_referenced_record on catalogue_people(referenced_relation, record_id);

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
DROP INDEX IF EXISTS idx_specimen_individuals_specimen_ref
DROP INDEX IF EXISTS idx_specimens_accompanying_specimen_ref;
DROP INDEX IF EXISTS idx_tag_groups_gtu_ref;
DROP INDEX IF EXISTS idx_users_languages_users_ref;
DROP INDEX IF EXISTS idx_words_table_field;


DROP TABLE habitats;

DROP sequence habitats_id_seq;
DROP table multimedia_keywords;
DROP table soortenregister;


DROP table associated_multimedia;
DROP sequence associated_multimedia_id_seq;

DROP table people_multimedia;
DROP table users_multimedia;
DROP table template_people_users_multimedia;
