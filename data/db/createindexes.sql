\set log_error_verbosity terse

/*** BTree Indexes for foreign keys ***/

CREATE INDEX CONCURRENTLY idx_possible_upper_levels_level_upper_ref on possible_upper_levels(level_upper_ref);
CREATE INDEX CONCURRENTLY idx_gtu_code on gtu(code);
CREATE INDEX CONCURRENTLY idx_gtu_location ON gtu USING GIST ( location );

CREATE INDEX CONCURRENTLY idx_people_languages_people_ref on people_languages(people_ref);
CREATE INDEX CONCURRENTLY idx_people_relationships_person_1_ref on people_relationships(person_1_ref);
CREATE INDEX CONCURRENTLY idx_people_relationships_person_2_ref on people_relationships(person_2_ref);
CREATE INDEX CONCURRENTLY idx_people_comm_person_user_ref on people_comm(person_user_ref);
CREATE INDEX CONCURRENTLY idx_people_addresses_person_user_ref on people_addresses(person_user_ref);
CREATE INDEX CONCURRENTLY idx_users_comm_person_user_ref on users_comm(person_user_ref);
CREATE INDEX CONCURRENTLY idx_users_addresses_person_user_ref on users_addresses(person_user_ref);
-- CREATE INDEX CONCURRENTLY idx_users_login_infos_user_ref on users_login_infos(user_ref);
CREATE INDEX CONCURRENTLY idx_collections_main_manager_ref on collections(main_manager_ref);
CREATE INDEX CONCURRENTLY idx_collections_parent_ref on collections(parent_ref);
CREATE INDEX CONCURRENTLY idx_collections_rights_user_ref on collections_rights(user_ref);
CREATE INDEX CONCURRENTLY idx_collections_rights_db_user_type on collections_rights(db_user_type);
--CREATE INDEX CONCURRENTLY idx_collections_fields_visibilities_collection_ref on collections_fields_visibilities(collection_ref);
--CREATE INDEX CONCURRENTLY idx_collections_fields_visibilities_user_ref on collections_fields_visibilities(user_ref);
--CREATE INDEX CONCURRENTLY idx_users_coll_rights_asked_collection_ref on users_coll_rights_asked(collection_ref);
--CREATE INDEX CONCURRENTLY idx_users_coll_rights_asked_user_ref on users_coll_rights_asked(user_ref);
--CREATE INDEX CONCURRENTLY idx_record_visibilities on record_visibilities(user_ref);
CREATE INDEX CONCURRENTLY idx_collection_maintenance_user_ref on collection_maintenance(people_ref);
CREATE INDEX CONCURRENTLY idx_taxonomy_level_ref on taxonomy(level_ref);
CREATE INDEX CONCURRENTLY idx_taxonomy_parent_ref on taxonomy(parent_ref);
CREATE INDEX CONCURRENTLY idx_chronostratigraphy_level_ref on chronostratigraphy(level_ref);
CREATE INDEX CONCURRENTLY idx_chronostratigraphy_parent_ref on chronostratigraphy(parent_ref);
CREATE INDEX CONCURRENTLY idx_lithostratigraphy_level_ref on lithostratigraphy(level_ref);
CREATE INDEX CONCURRENTLY idx_lithostratigraphy_parent_ref on lithostratigraphy(parent_ref);
CREATE INDEX CONCURRENTLY idx_mineralogy_level_ref on mineralogy(level_ref);
CREATE INDEX CONCURRENTLY idx_mineralogy_parent_ref on mineralogy(parent_ref);
CREATE INDEX CONCURRENTLY idx_lithology_level_ref on lithology(level_ref);
CREATE INDEX CONCURRENTLY idx_lithology_parent_ref on lithology(parent_ref);

CREATE INDEX CONCURRENTLY idx_specimens_expedition_ref on specimens(expedition_ref) WHERE expedition_ref <> 0;
CREATE INDEX CONCURRENTLY idx_specimens_gtu_ref on specimens(gtu_ref) WHERE gtu_ref <> 0;
CREATE INDEX CONCURRENTLY idx_specimens_taxon_ref on specimens(taxon_ref) WHERE taxon_ref <> 0;
CREATE INDEX CONCURRENTLY idx_specimens_litho_ref on specimens(litho_ref) WHERE litho_ref <> 0;
CREATE INDEX CONCURRENTLY idx_specimens_chrono_ref on specimens(chrono_ref) WHERE chrono_ref <> 0;
CREATE INDEX CONCURRENTLY idx_specimens_lithology_ref on specimens(lithology_ref) WHERE lithology_ref <> 0;
CREATE INDEX CONCURRENTLY idx_specimens_mineral_ref on specimens(mineral_ref) WHERE mineral_ref <> 0;
CREATE INDEX CONCURRENTLY idx_specimens_institution_ref on specimens(institution_ref);

CREATE INDEX CONCURRENTLY idx_specimens_relationships_taxon_ref on specimens_relationships(taxon_ref);
CREATE INDEX CONCURRENTLY idx_specimens_relationships_mineral_ref on specimens_relationships(mineral_ref);
CREATE INDEX CONCURRENTLY idx_specimens_relationships_specimen_ref on specimens_relationships(specimen_ref);
CREATE INDEX CONCURRENTLY idx_specimens_relationships_specimen_related_ref on specimens_relationships(specimen_related_ref);

CREATE INDEX CONCURRENTLY idx_specimen_collecting_methods_method_ref on specimen_collecting_methods(collecting_method_ref);
CREATE INDEX CONCURRENTLY idx_specimen_collecting_tools_tool_ref on specimen_collecting_tools(collecting_tool_ref);
CREATE INDEX CONCURRENTLY idx_insurances_insurer_ref on insurances(insurer_ref);
CREATE INDEX CONCURRENTLY idx_insurances_contact_ref on insurances(contact_ref);
CREATE INDEX CONCURRENTLY idx_specimens_ig_ref on specimens(ig_ref);
CREATE INDEX CONCURRENTLY idx_tags_gtu_ref on tags(gtu_ref);
CREATE INDEX CONCURRENTLY idx_tags_group_ref on tags(group_ref);
CREATE INDEX CONCURRENTLY idx_users_tracking_user_ref on users_tracking(user_ref);
CREATE INDEX idx_users_tracking_action ON users_tracking USING btree (action);
CREATE INDEX CONCURRENTLY idx_imports_collection_ref ON imports(collection_ref);
CREATE INDEX CONCURRENTLY idx_staging_import_ref ON staging(import_ref);
CREATE INDEX CONCURRENTLY idx_staging_people_record ON staging_people(record_id,referenced_relation) ;


/*** BTree Indexes for fields used for searches purposes ***/

CREATE INDEX CONCURRENTLY idx_catalogue_people_people_type on catalogue_people(people_type);
CREATE INDEX CONCURRENTLY idx_catalogue_people_people_sub_type on catalogue_people(people_sub_type);
CREATE INDEX CONCURRENTLY idx_catalogue_people_people_order_by on catalogue_people(order_by);
CREATE INDEX CONCURRENTLY idx_catalogue_people_people_ref on catalogue_people(people_ref);
CREATE INDEX CONCURRENTLY idx_catalogue_people_referenced_record on catalogue_people(referenced_relation, record_id);
CREATE INDEX CONCURRENTLY idx_properties_property_type on properties(property_type);
CREATE INDEX CONCURRENTLY idx_properties_property_unit on properties(property_unit);
CREATE INDEX CONCURRENTLY idx_properties_property_lower_value on properties(lower_value);
CREATE INDEX CONCURRENTLY idx_properties_property_upper_value on properties(upper_value);
CREATE INDEX CONCURRENTLY idx_properties_referenced_record on properties(referenced_relation, record_id);


CREATE INDEX CONCURRENTLY idx_properties_property_lower_value_unified on properties(lower_value_unified );
CREATE INDEX CONCURRENTLY idx_properties_property_upper_value_unified  on properties(upper_value_unified );

CREATE INDEX CONCURRENTLY idx_catalogue_relationships_relations on catalogue_relationships (referenced_relation, record_id_1, relationship_type);

CREATE INDEX CONCURRENTLY idx_chronostratigraphy_lower_bound on chronostratigraphy(coalesce(lower_bound, -4600));
CREATE INDEX CONCURRENTLY idx_chronostratigraphy_upper_bound on chronostratigraphy(coalesce(upper_bound, 1));

CREATE INDEX CONCURRENTLY idx_classification_keywords_referenced_record on classification_keywords(referenced_relation, record_id);
CREATE INDEX CONCURRENTLY idx_classification_synonymies_grouping on classification_synonymies(group_id, is_basionym);
CREATE INDEX CONCURRENTLY idx_classification_synonymies_order_by on classification_synonymies(group_name, order_by);
CREATE INDEX CONCURRENTLY idx_classification_synonymies_referenced_record on classification_synonymies(referenced_relation, record_id, group_id);
CREATE INDEX CONCURRENTLY idx_vernacular_names_community_indexed on vernacular_names (community_indexed);
CREATE INDEX CONCURRENTLY idx_vernacular_names_name_indexed on vernacular_names (name_indexed);
CREATE INDEX CONCURRENTLY idx_vernacular_names_referenced_record on vernacular_names(referenced_relation, record_id);

CREATE INDEX CONCURRENTLY idx_specimens_object_name_indexed on specimens (object_name_indexed);


CREATE INDEX CONCURRENTLY idx_codes_code_num on codes(code_num) WHERE NOT code_num IS NULL;
CREATE INDEX idx_codes_full_code_indexed_btree ON codes USING btree (full_code_indexed);
CREATE INDEX CONCURRENTLY idx_codes_referenced_record on codes(referenced_relation, record_id);

CREATE INDEX CONCURRENTLY idx_collecting_methods_method_indexed on collecting_methods(method_indexed);
CREATE INDEX CONCURRENTLY idx_collecting_tools_tool_indexed on collecting_tools(tool_indexed);
CREATE INDEX CONCURRENTLY idx_collection_maintenance_action on collection_maintenance(action_observation);
CREATE INDEX CONCURRENTLY idx_collection_maintenance_referenced_record on collection_maintenance(referenced_relation, record_id);
--CREATE INDEX CONCURRENTLY idx_collections_fields_visibilities_searchable on collections_fields_visibilities(searchable) WHERE searchable is true;
--CREATE INDEX CONCURRENTLY idx_collections_fields_visibilities_visible on collections_fields_visibilities(visible) WHERE visible is true;
CREATE INDEX CONCURRENTLY idx_comments_notion_concerned on comments(notion_concerned);
CREATE INDEX CONCURRENTLY idx_comments_referenced_record on comments(referenced_relation, record_id);
CREATE INDEX CONCURRENTLY idx_identifications_notion_concerned on identifications(notion_concerned);
CREATE INDEX CONCURRENTLY idx_identifications_order_by on identifications(order_by);
CREATE INDEX CONCURRENTLY idx_identifications_determination_status on identifications(determination_status) WHERE determination_status <> '';
CREATE INDEX CONCURRENTLY idx_igs_ig_num_indexed ON igs(ig_num_indexed text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_insurances_insurance_currency on insurances(insurance_currency);

CREATE INDEX CONCURRENTLY idx_mineralogy_code on mineralogy(upper(code));

CREATE INDEX CONCURRENTLY idx_mineralogy_cristal_system on mineralogy(cristal_system) WHERE cristal_system <> '';

CREATE INDEX CONCURRENTLY idx_my_widgets_user_category on my_widgets(user_ref, category, group_name);
CREATE INDEX CONCURRENTLY idx_people_sub_type on people(sub_type) WHERE NOT sub_type IS NULL;
CREATE INDEX CONCURRENTLY idx_people_family_name on people(family_name);
CREATE INDEX CONCURRENTLY idx_people_addresses_country on people_addresses(country);
CREATE INDEX CONCURRENTLY idx_people_comm_comm_type on people_comm(comm_type);
CREATE INDEX CONCURRENTLY idx_people_languages_language_country on people_languages(language_country);

CREATE INDEX CONCURRENTLY idx_specimens_type_search on specimens(type_search) WHERE type_search <> 'specimen';
CREATE INDEX CONCURRENTLY idx_specimens_sex on specimens(sex) where sex not in ('undefined', 'unknown');
CREATE INDEX CONCURRENTLY idx_specimens_stage on specimens(stage) WHERE stage not in ('undefined', 'unknown');
CREATE INDEX CONCURRENTLY idx_specimens_state on specimens(state) WHERE state <> 'not applicable';
CREATE INDEX CONCURRENTLY idx_specimens_social_status on specimens(social_status)  WHERE social_status <> 'not applicable';
CREATE INDEX CONCURRENTLY idx_specimens_rock_form on specimens(rock_form);
CREATE INDEX CONCURRENTLY idx_specimens_specimen_part on specimens(specimen_part);
CREATE INDEX CONCURRENTLY idx_specimens_room on specimens(room) WHERE NOT room IS NULL;
CREATE INDEX CONCURRENTLY idx_specimens_row on specimens(row) WHERE NOT row IS NULL;
CREATE INDEX CONCURRENTLY idx_specimens_shelf on specimens(shelf) WHERE NOT shelf IS NULL;
CREATE INDEX CONCURRENTLY idx_specimens_container on specimens(container) WHERE NOT container IS NULL;
CREATE INDEX CONCURRENTLY idx_specimens_sub_container on specimens(sub_container) WHERE NOT sub_container IS NULL;
CREATE INDEX CONCURRENTLY idx_specimens_container_type on specimens(container_type);
CREATE INDEX CONCURRENTLY idx_specimens_sub_container_type on specimens(sub_container_type);
CREATE INDEX CONCURRENTLY idx_specimens_container_storage on specimens(container_storage);
CREATE INDEX CONCURRENTLY idx_specimens_sub_container_storage on specimens(sub_container_storage);

CREATE INDEX CONCURRENTLY idx_taxonomy_path on taxonomy(path text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_tag_groups_group_name_indexed on tag_groups(group_name_indexed);
CREATE INDEX CONCURRENTLY idx_tag_groups_group_name_indexed_txt_op on tag_groups(group_name_indexed text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_tag_groups_sub_group_name on tag_groups(sub_group_name);
CREATE INDEX CONCURRENTLY idx_tags_tag_indexed on tags(tag_indexed);
CREATE INDEX CONCURRENTLY idx_tags_group_type on tags(group_type);
CREATE INDEX CONCURRENTLY idx_tags_sub_group_type on tags(sub_group_type);
CREATE INDEX CONCURRENTLY idx_users_addresses_country on users_addresses(country);
CREATE INDEX CONCURRENTLY idx_users_comm_comm_type on users_comm(comm_type);

CREATE INDEX CONCURRENTLY idx_informative_workflow_user_status on informative_workflow(user_ref, status);

/***Indx for text fields ***/

/** NEW TS IDX **/
CREATE INDEX CONCURRENTLY idx_gin_trgm_comments_comment on comments  using gin ("comment" gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_expeditions_name on expeditions using gin(name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_people_formated_name on people using gin(formated_name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_users_formated_name on users using gin(formated_name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_taxonomy_naming on taxonomy using gin(name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_chronostratigraphy_naming on chronostratigraphy using gin(name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_lithostratigraphy_naming on lithostratigraphy using gin(name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_mineralogy_naming on mineralogy using gin(name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_lithology_naming on lithology using gin(name_indexed gin_trgm_ops);
/*** END NEW IDX*/

CREATE INDEX CONCURRENTLY idx_gin_trgm_bibliography_title on bibliography using gist(title_indexed gist_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_multimedia_search_indexed on multimedia using gin(search_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_gtu_tags_values on gtu using gin(tag_values_indexed);

/*** @TODO:Additional BTree Indexes created to fasten application ***/

/*** FullText ***/
CREATE INDEX CONCURRENTLY idx_tags_trgm ON tags USING gin(tag gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_tool_trgm ON collecting_tools USING gin(tool gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_method_trgm ON collecting_methods USING gin(method gin_trgm_ops);

/*** Dates ***/

CREATE INDEX CONCURRENTLY idx_igs_ig_date on igs(ig_date, ig_date_mask);
CREATE INDEX CONCURRENTLY idx_expeditions_expedition_from_date on expeditions(expedition_from_date, expedition_from_date_mask);
CREATE INDEX CONCURRENTLY idx_expeditions_expedition_to_date on expeditions(expedition_to_date, expedition_to_date_mask);
CREATE INDEX CONCURRENTLY idx_users_tracking_modification_date_time on users_tracking(modification_date_time DESC);


/** LOANS **/

CREATE INDEX CONCURRENTLY idx_loan_items_loan_ref on loan_items(loan_ref);
CREATE INDEX CONCURRENTLY idx_loan_items_ig_ref on loan_items(ig_ref);
CREATE INDEX CONCURRENTLY idx_loan_items_part_ref on loan_items(specimen_ref);


CREATE INDEX CONCURRENTLY idx_loan_rights_ig_ref on loan_rights(loan_ref);
CREATE INDEX CONCURRENTLY idx_loan_rights_part_ref on loan_rights(user_ref);

CREATE INDEX CONCURRENTLY idx_loan_status_user_ref on loan_status(user_ref);
CREATE INDEX CONCURRENTLY idx_loan_status_loan_ref on loan_status(loan_ref);
CREATE INDEX CONCURRENTLY idx_loan_status_loan_ref_is_last on loan_status(loan_ref,is_last);


CREATE INDEX CONCURRENTLY idx_ext_links_referenced_record on ext_links(referenced_relation, record_id);
CREATE INDEX CONCURRENTLY idx_informative_workflow_referenced_record on informative_workflow(referenced_relation, record_id);
CREATE INDEX CONCURRENTLY idx_multimedia_referenced_record on multimedia(referenced_relation, record_id);


/** Biblio **/

CREATE INDEX CONCURRENTLY idx_catalogue_bibliography_referenced_record on catalogue_bibliography(referenced_relation, record_id);
CREATE INDEX CONCURRENTLY idx_bibliography_type on bibliography(type);


CREATE INDEX idx_gin_trgm_comments_comment_indexed ON comments USING gin (comment_indexed public.gin_trgm_ops);
CREATE INDEX idx_gin_trgm_lithology_name_indexed ON lithology USING btree (name_indexed);
CREATE INDEX idx_gin_trgm_taxonomy_name_indexed ON taxonomy USING btree (name_indexed text_pattern_ops);
CREATE INDEX idx_lithology_name_order_by_txt_op ON lithology USING btree (name_indexed text_pattern_ops);
CREATE INDEX idx_lithostratigraphy_name_order_by_txt_op ON lithostratigraphy USING btree (name_indexed text_pattern_ops);
CREATE INDEX idx_mineralogy_name_order_by_txt_op ON mineralogy USING btree (name_indexed text_pattern_ops);

/** Staging **/
CREATE INDEX idx_staging_gtu_code ON staging (gtu_code) WHERE gtu_code IS NOT NULL;
CREATE INDEX idx_staging_gtu_code_fullToIndex ON staging (fullToIndex(gtu_code)) WHERE gtu_code IS NOT NULL;
CREATE INDEX idx_gtu_code_search_for_import ON gtu (position('import/' in code), COALESCE(latitude,0), COALESCE(longitude,0), COALESCE(fullToIndex(code), ''));
CREATE INDEX idx_staging_catalogue ON staging_catalogue (level_ref, fullToIndex(name));
CREATE INDEX idx_staging_catalogue_filter ON staging_catalogue (import_ref, name, level_ref);
CREATE INDEX idx_staging_catalogue_parent_ref ON staging_catalogue (parent_ref) WHERE parent_ref IS NOT NULL;
CREATE INDEX idx_staging_catalogue_catalogue_ref ON staging_catalogue (import_ref, parent_ref) WHERE catalogue_ref IS NOT NULL;
CREATE INDEX idx_staging_catalogue_parent_updated ON staging_catalogue (parent_updated);
