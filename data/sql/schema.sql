CREATE TABLE specimen_parts (id BIGSERIAL, specimen_individual_ref BIGINT NOT NULL, specimen_part TEXT DEFAULT 'specimen' NOT NULL, complete BOOLEAN DEFAULT 'true' NOT NULL, building TEXT, floor TEXT, room TEXT, row TEXT, shelf TEXT, container TEXT, sub_container TEXT, container_type TEXT DEFAULT 'container' NOT NULL, sub_container_type TEXT DEFAULT 'container' NOT NULL, storage TEXT DEFAULT 'dry' NOT NULL, surnumerary BOOLEAN DEFAULT 'false' NOT NULL, specimen_status TEXT DEFAULT 'good state' NOT NULL, specimen_part_count_min BIGINT DEFAULT 1 NOT NULL, specimen_part_count_max BIGINT DEFAULT 1 NOT NULL, category TEXT DEFAULT 'physical', PRIMARY KEY(id));
CREATE TABLE users (id BIGSERIAL, is_physical BOOLEAN NOT NULL, sub_type TEXT, public_class VARCHAR(255), formated_name TEXT, formated_name_indexed TEXT, formated_name_ts TEXT, title TEXT NOT NULL, family_name TEXT NOT NULL, given_name TEXT, additional_names TEXT, birth_date_mask BIGINT DEFAULT 0 NOT NULL, birth_date DATE DEFAULT '0001-01-01' NOT NULL, gender VARCHAR(255), db_user_type BIGINT DEFAULT 1 NOT NULL, PRIMARY KEY(id));
CREATE TABLE people_relationships (id BIGSERIAL, relationship_type TEXT DEFAULT 'belongs to' NOT NULL, person_1_ref BIGINT NOT NULL, person_2_ref BIGINT NOT NULL, person_title TEXT, path TEXT, organization_unit TEXT, person_user_role TEXT, activity_period TEXT, PRIMARY KEY(id));
CREATE TABLE people_addresses (id BIGSERIAL, person_user_ref BIGINT NOT NULL, tag TEXT NOT NULL, organization_unit TEXT, person_user_role TEXT, activity_period TEXT, po_box TEXT, extended_address TEXT, locality TEXT NOT NULL, region TEXT, zip_code TEXT, country TEXT NOT NULL, address_parts_ts TEXT, PRIMARY KEY(id));
CREATE TABLE class_vernacular_names (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, community TEXT NOT NULL, PRIMARY KEY(id));
CREATE TABLE users_tables_fields_tracked (id BIGSERIAL, table_name TEXT NOT NULL, field_name TEXT NOT NULL, user_ref BIGINT NOT NULL, PRIMARY KEY(id));
CREATE TABLE classification_keywords (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, keyword_type TEXT DEFAULT 'name' NOT NULL, keyword TEXT NOT NULL, PRIMARY KEY(id));
CREATE TABLE chronostratigraphy (id BIGSERIAL, name TEXT NOT NULL, name_indexed TEXT, level_ref BIGINT, status TEXT DEFAULT 'valid' NOT NULL, path TEXT DEFAULT '/' NOT NULL, parent_ref BIGINT DEFAULT 0 NOT NULL, eon_ref BIGINT DEFAULT 0 NOT NULL, eon_indexed TEXT DEFAULT '' NOT NULL, era_ref BIGINT DEFAULT 0 NOT NULL, era_indexed TEXT DEFAULT '' NOT NULL, sub_era_ref BIGINT DEFAULT 0 NOT NULL, sub_era_indexed TEXT DEFAULT '' NOT NULL, system_ref BIGINT DEFAULT 0 NOT NULL, system_indexed TEXT DEFAULT '' NOT NULL, serie_ref BIGINT DEFAULT 0 NOT NULL, serie_indexed TEXT DEFAULT '' NOT NULL, stage_ref BIGINT DEFAULT 0 NOT NULL, stage_indexed TEXT DEFAULT '' NOT NULL, sub_stage_ref BIGINT DEFAULT 0 NOT NULL, sub_stage_indexed TEXT DEFAULT '' NOT NULL, sub_level_1_ref BIGINT DEFAULT 0 NOT NULL, sub_level_1_indexed TEXT DEFAULT '' NOT NULL, sub_level_2_ref BIGINT DEFAULT 0 NOT NULL, sub_level_2_indexed TEXT DEFAULT '' NOT NULL, lower_bound BIGINT, upper_bound BIGINT, PRIMARY KEY(id));
CREATE TABLE collections_fields_visibilities (id BIGSERIAL, collection_ref BIGINT DEFAULT 0 NOT NULL, user_ref BIGINT DEFAULT 0 NOT NULL, field_group_name TEXT NOT NULL, db_user_type BIGINT DEFAULT 1 NOT NULL, searchable BOOLEAN DEFAULT 'true' NOT NULL, visible BOOLEAN DEFAULT 'true' NOT NULL, PRIMARY KEY(id));
CREATE TABLE specimens_accompanying (id BIGSERIAL, type TEXT DEFAULT 'secondary' NOT NULL, specimen_ref BIGINT NOT NULL, taxon_ref BIGINT DEFAULT 0 NOT NULL, mineral_ref BIGINT DEFAULT 0 NOT NULL, form TEXT, quantity FLOAT, unit TEXT DEFAULT '%' NOT NULL, PRIMARY KEY(id));
CREATE TABLE associated_multimedia (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, multimedia_ref BIGINT NOT NULL, PRIMARY KEY(id));
CREATE TABLE lithology (id BIGSERIAL, name TEXT NOT NULL, name_indexed TEXT, level_ref BIGINT, status TEXT DEFAULT 'valid' NOT NULL, path TEXT DEFAULT '/' NOT NULL, parent_ref BIGINT DEFAULT 0 NOT NULL, unit_main_group_ref BIGINT DEFAULT 0 NOT NULL, unit_main_group_indexed TEXT DEFAULT '' NOT NULL, unit_group_ref BIGINT DEFAULT 0 NOT NULL, unit_group_indexed TEXT DEFAULT '' NOT NULL, unit_sub_group_ref BIGINT DEFAULT 0 NOT NULL, unit_sub_group_indexed TEXT DEFAULT '' NOT NULL, unit_rock_ref BIGINT DEFAULT 0 NOT NULL, unit_rock_indexed TEXT DEFAULT '' NOT NULL, PRIMARY KEY(id));
CREATE TABLE possible_upper_levels (id BIGSERIAL, level_ref BIGINT NOT NULL, level_upper_ref BIGINT NOT NULL, PRIMARY KEY(id));
CREATE TABLE vernacular_names (vernacular_class_ref BIGINT, name TEXT NOT NULL, name_ts TEXT, country_language_full_text TEXT, PRIMARY KEY(vernacular_class_ref));
CREATE TABLE lithostratigraphy (id BIGSERIAL, name TEXT NOT NULL, name_indexed TEXT, level_ref BIGINT, status TEXT DEFAULT 'valid' NOT NULL, path TEXT DEFAULT '/' NOT NULL, parent_ref BIGINT DEFAULT 0 NOT NULL, group_ref BIGINT DEFAULT 0 NOT NULL, group_indexed TEXT DEFAULT '' NOT NULL, formation_ref BIGINT DEFAULT 0 NOT NULL, formation_indexed TEXT DEFAULT '' NOT NULL, member_ref BIGINT DEFAULT 0 NOT NULL, member_indexed TEXT DEFAULT '' NOT NULL, layer_ref BIGINT DEFAULT 0 NOT NULL, layer_indexed TEXT DEFAULT '' NOT NULL, sub_level_1_ref BIGINT DEFAULT 0 NOT NULL, sub_level_1_indexed TEXT DEFAULT '' NOT NULL, sub_level_2_ref BIGINT DEFAULT 0 NOT NULL, sub_level_2_indexed TEXT DEFAULT '' NOT NULL, PRIMARY KEY(id));
CREATE TABLE people_languages (id BIGSERIAL, people_ref BIGINT NOT NULL, language_country TEXT DEFAULT 'en_gb' NOT NULL, mother BOOLEAN DEFAULT 'true' NOT NULL, prefered_language BOOLEAN DEFAULT 'false' NOT NULL, PRIMARY KEY(id));
CREATE TABLE users_addresses (id BIGSERIAL, person_user_ref BIGINT NOT NULL, tag TEXT NOT NULL, organization_unit TEXT, person_user_role TEXT, activity_period TEXT, po_box TEXT, extended_address TEXT, locality TEXT NOT NULL, region TEXT, zip_code TEXT, country TEXT NOT NULL, address_parts_ts TEXT, PRIMARY KEY(id));
CREATE TABLE users_tracking_records (id BIGSERIAL, tracking_ref BIGINT NOT NULL, field_name TEXT NOT NULL, old_value TEXT, new_value TEXT, PRIMARY KEY(id));
CREATE TABLE my_preferences (user_ref BIGINT, category TEXT, group_name TEXT, order_by BIGINT DEFAULT 1 NOT NULL, col_num BIGINT DEFAULT 1 NOT NULL, mandatory BOOLEAN DEFAULT 'false' NOT NULL, visible BOOLEAN DEFAULT 'true' NOT NULL, opened BOOLEAN DEFAULT 'true' NOT NULL, color TEXT DEFAULT '#5BAABD', icon_ref BIGINT, title_perso TEXT, PRIMARY KEY(user_ref, category, group_name));
CREATE TABLE habitats (id BIGSERIAL, name TEXT NOT NULL, path TEXT DEFAULT '/' NOT NULL, code TEXT NOT NULL, code_indexed TEXT DEFAULT '/', description TEXT NOT NULL, description_ts TEXT, description_language_full_text TEXT, habitat_system TEXT DEFAULT 'eunis' NOT NULL, PRIMARY KEY(id));
CREATE TABLE multimedia (id BIGSERIAL, is_digital BOOLEAN NOT NULL, type TEXT DEFAULT 'image' NOT NULL, sub_type TEXT, title TEXT NOT NULL, title_indexed TEXT, subject TEXT DEFAULT '/' NOT NULL, coverage TEXT DEFAULT 'temporal' NOT NULL, apercu_path TEXT, copyright TEXT, license TEXT, uri TEXT, descriptive_ts TEXT, descriptive_language_full_text TEXT, creation_date DATE DEFAULT '0001-01-01' NOT NULL, creation_date_mask BIGINT DEFAULT 0 NOT NULL, publication_date_from DATE DEFAULT '0001-01-01' NOT NULL, publication_date_from_mask BIGINT DEFAULT 0 NOT NULL, publication_date_to DATE DEFAULT '0001-01-01' NOT NULL, publication_date_to_mask BIGINT DEFAULT 0 NOT NULL, parent_ref BIGINT, path TEXT DEFAULT '/' NOT NULL, mime_type TEXT, PRIMARY KEY(id));
CREATE TABLE specimen_parts_insurances (id BIGSERIAL, specimen_part_ref BIGINT NOT NULL, insurance_year BIGINT, insurance_value BIGINT NOT NULL, insurer_ref BIGINT, PRIMARY KEY(id));
CREATE TABLE codes (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, code_category TEXT DEFAULT 'main' NOT NULL, code_prefix TEXT, code BIGINT, code_suffix TEXT, full_code_indexed TEXT, code_date TIMESTAMP, PRIMARY KEY(id));
CREATE TABLE catalogue_levels (id BIGSERIAL, level_type TEXT NOT NULL, level_name TEXT NOT NULL, level_sys_name TEXT NOT NULL, optional_level BOOLEAN DEFAULT 'false' NOT NULL, PRIMARY KEY(id));
CREATE TABLE catalogue_properties (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, property_type TEXT NOT NULL, property_sub_type TEXT, property_sub_type_indexed TEXT, property_qualifier TEXT, property_qualifier_indexed TEXT, date_from_mask BIGINT DEFAULT 0 NOT NULL, date_from timestamp TIMESTAMP DEFAULT '0001-01-01' NOT NULL, date_to_mask BIGINT DEFAULT 0 NOT NULL, date_to timestamp TIMESTAMP DEFAULT '0001-01-01' NOT NULL, property_unit TEXT NOT NULL, property_accuracy_unit TEXT, property_method TEXT, property_method_indexed TEXT, property_tool TEXT NOT NULL, property_tool_indexed TEXT, PRIMARY KEY(id));
CREATE TABLE users_comm (id BIGSERIAL, person_user_ref BIGINT NOT NULL, comm_type TEXT DEFAULT 'phone/fax' NOT NULL, tag TEXT NOT NULL, organization_unit TEXT, person_user_role TEXT, activity_period TEXT, PRIMARY KEY(id));
CREATE TABLE expeditions (id BIGSERIAL, name TEXT NOT NULL, name_ts TEXT, name_indexed TEXT, name_language_full_text TEXT, expedition_from_date_mask BIGINT DEFAULT 0 NOT NULL, expedition_from_date DATE DEFAULT '0001-01-01', expedition_to_date_mask BIGINT DEFAULT 0 NOT NULL, expedition_to_date DATE DEFAULT '0001-01-01', PRIMARY KEY(id));
CREATE TABLE classification_synonymies (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, group_id BIGINT NOT NULL, group_name TEXT NOT NULL, basionym_record_id BIGINT, order_by BIGINT DEFAULT 0 NOT NULL, PRIMARY KEY(id));
CREATE TABLE users_multimedia (id BIGSERIAL, person_user_ref BIGINT NOT NULL, object_ref BIGINT NOT NULL, category TEXT DEFAULT 'avatar' NOT NULL, PRIMARY KEY(id));
CREATE TABLE gtu_tags (id BIGSERIAL, tag_group_ref BIGINT NOT NULL, gtu_ref BIGINT NOT NULL, PRIMARY KEY(id));
CREATE TABLE specimen_individuals (id BIGSERIAL, specimen_ref BIGINT NOT NULL, type TEXT DEFAULT 'specimen' NOT NULL, type_group TEXT, type_search TEXT, sex TEXT DEFAULT 'undefined' NOT NULL, stage TEXT DEFAULT 'undefined' NOT NULL, stat TEXT DEFAULT 'not applicable' NOT NULL, social_status TEXT DEFAULT 'not applicable' NOT NULL, rock_form TEXT DEFAULT 'not applicable' NOT NULL, specimen_individuals_count_min BIGINT DEFAULT 1 NOT NULL, specimen_individuals_count_max BIGINT DEFAULT 1 NOT NULL, PRIMARY KEY(id));
CREATE TABLE collection_maintenance (id BIGSERIAL, table_name TEXT NOT NULL, people_ref BIGINT NOT NULL, category TEXT DEFAULT 'action' NOT NULL, action_observation TEXT NOT NULL, description TEXT, description_ts TEXT, language_full_text TEXT, modification_date_time TIMESTAMP NOT NULL, modification_date_mask BIGINT DEFAULT 0 NOT NULL, PRIMARY KEY(id));
CREATE TABLE record_visibilities (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, db_user_type BIGINT DEFAULT 0 NOT NULL, user_ref BIGINT DEFAULT 0 NOT NULL, visible BOOLEAN DEFAULT 'true' NOT NULL, PRIMARY KEY(id));
CREATE TABLE specimens (id BIGSERIAL, collection_ref BIGINT NOT NULL, expedition_ref BIGINT DEFAULT 0, gtu_ref BIGINT DEFAULT 0, taxon_ref BIGINT DEFAULT 0, litho_ref BIGINT DEFAULT 0, chrono_ref BIGINT DEFAULT 0, lithology_ref BIGINT DEFAULT 0, mineral_ref BIGINT DEFAULT 0, host_taxon_ref BIGINT DEFAULT 0, host_specimen_ref BIGINT, host_relationship TEXT, acquisition_category TEXT DEFAULT 'expedition', acquisition_date_mask BIGINT DEFAULT 0, acquisition_date DATE DEFAULT '0001-01-01', collecting_method TEXT, collecting_tool TEXT, specimen_count_min BIGINT DEFAULT 1, specimen_count_max BIGINT DEFAULT 1, station_visible BOOLEAN DEFAULT 'true', multimedia_visible BOOLEAN DEFAULT 'true', PRIMARY KEY(id));
CREATE TABLE users_coll_rights_asked (id BIGSERIAL, collection_ref BIGINT DEFAULT 0 NOT NULL, user_ref BIGINT DEFAULT 0 NOT NULL, field_group_name TEXT NOT NULL, db_user_type BIGINT NOT NULL, searchable BOOLEAN DEFAULT 'true' NOT NULL, visible BOOLEAN DEFAULT 'true' NOT NULL, motivation TEXT NOT NULL, asking_date_time TIMESTAMP NOT NULL, with_sub_collections BOOLEAN DEFAULT 'true' NOT NULL, PRIMARY KEY(id));
CREATE TABLE people (id BIGSERIAL, is_physical BOOLEAN NOT NULL, sub_type TEXT, public_class VARCHAR(255), formated_name TEXT, formated_name_indexed TEXT, formated_name_ts TEXT, title TEXT, family_name TEXT NOT NULL, given_name TEXT, additional_names TEXT, birth_date_mask BIGINT DEFAULT 0 NOT NULL, birth_date DATE DEFAULT '0001-01-01' NOT NULL, gender VARCHAR(255), db_people_type BIGINT DEFAULT 1 NOT NULL, end_date_mask BIGINT DEFAULT 0 NOT NULL, end_date DATE DEFAULT '0001-01-01' NOT NULL, PRIMARY KEY(id));
CREATE TABLE people_multimedia (id BIGSERIAL, person_user_ref BIGINT NOT NULL, object_ref BIGINT NOT NULL, category TEXT DEFAULT 'avatar' NOT NULL, PRIMARY KEY(id));
CREATE TABLE mineralogy (id BIGSERIAL, name TEXT NOT NULL, name_indexed TEXT, level_ref BIGINT, status TEXT DEFAULT 'valid' NOT NULL, path TEXT DEFAULT '/' NOT NULL, parent_ref BIGINT DEFAULT 0 NOT NULL, code TEXT NOT NULL, classification TEXT DEFAULT 'strunz' NOT NULL, formule TEXT, formule_indexed TEXT, cristal_system TEXT, unit_class_ref BIGINT DEFAULT 0 NOT NULL, unit_class_indexed TEXT DEFAULT '' NOT NULL, unit_division_ref BIGINT DEFAULT 0 NOT NULL, unit_division_indexed TEXT DEFAULT '' NOT NULL, unit_family_ref BIGINT DEFAULT 0 NOT NULL, unit_family_indexed TEXT DEFAULT '' NOT NULL, unit_group_ref BIGINT DEFAULT 0 NOT NULL, unit_group_indexed TEXT DEFAULT '' NOT NULL, unit_variety_ref BIGINT DEFAULT 0 NOT NULL, unit_variety_indexed TEXT DEFAULT '' NOT NULL, PRIMARY KEY(id));
CREATE TABLE my_saved_specimens (user_ref BIGINT, name TEXT, specimen_ids TEXT NOT NULL, favorite BOOLEAN DEFAULT 'false' NOT NULL, modification_date_time TIMESTAMP NOT NULL, PRIMARY KEY(user_ref, name));
CREATE TABLE comments (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, notion_concerned TEXT NOT NULL, comment TEXT NOT NULL, comment_ts TEXT, comment_language_full_text TEXT, PRIMARY KEY(id));
CREATE TABLE catalogue_people (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, people_type TEXT DEFAULT 'authors' NOT NULL, people_sub_type TEXT NOT NULL, order_by BIGINT DEFAULT 1 NOT NULL, people_ref BIGINT NOT NULL, PRIMARY KEY(id));
CREATE TABLE gtu (id BIGSERIAL, code TEXT NOT NULL, parent_ref BIGINT NOT NULL, gtu_from_date_mask BIGINT DEFAULT 0 NOT NULL, gtu_from_date TIMESTAMP DEFAULT '0001-01-01' NOT NULL, gtu_to_date_mask BIGINT DEFAULT 0 NOT NULL, gtu_to_date TIMESTAMP DEFAULT '0001-01-01' NOT NULL, PRIMARY KEY(id));
CREATE TABLE tags (id BIGSERIAL, label TEXT NOT NULL, label_indexed TEXT NOT NULL, PRIMARY KEY(id));
CREATE TABLE collections_rights (id BIGSERIAL, collection_ref BIGINT DEFAULT 0 NOT NULL, user_ref BIGINT DEFAULT 0 NOT NULL, rights BIGINT DEFAULT 1 NOT NULL, PRIMARY KEY(id));
CREATE TABLE properties_values (id BIGSERIAL, property_ref BIGINT, property_min TEXT NOT NULL, property_min_unified TEXT, property_max TEXT, property_max_unified TEXT, property_accuracy FLOAT, property_accuracy_unified FLOAT, PRIMARY KEY(id));
CREATE TABLE collections (id BIGSERIAL, collection_type VARCHAR(255) DEFAULT 'mix' NOT NULL, code TEXT NOT NULL, name TEXT NOT NULL, institution_ref BIGINT NOT NULL, main_manager_ref BIGINT NOT NULL, parent_ref BIGINT, path TEXT DEFAULT '/' NOT NULL, code_auto_increment BOOLEAN DEFAULT 'false' NOT NULL, code_part_code_auto_copy BOOLEAN DEFAULT 'false' NOT NULL, PRIMARY KEY(id));
CREATE TABLE identifications (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, notion_concerned TEXT NOT NULL, notion_date TIMESTAMP, value_defined TEXT, value_defined_indexed TEXT, value_defined_ts TEXT, determination_status TEXT, order_by BIGINT DEFAULT 1 NOT NULL, PRIMARY KEY(id));
CREATE TABLE my_saved_searches (user_ref BIGINT, name TEXT, search_criterias TEXT NOT NULL, favorite BOOLEAN DEFAULT 'false' NOT NULL, modification_date_time TIMESTAMP NOT NULL, visible_fields_in_result TEXT NOT NULL, PRIMARY KEY(user_ref, name));
CREATE TABLE soortenregister (id BIGSERIAL, taxa_ref BIGINT DEFAULT 0 NOT NULL, gtu_ref BIGINT DEFAULT 0 NOT NULL, habitat_ref BIGINT DEFAULT 0 NOT NULL, date_from DATE, date_to DATE, PRIMARY KEY(id));
CREATE TABLE people_comm (id BIGSERIAL, person_user_ref BIGINT NOT NULL, comm_type TEXT DEFAULT 'phone/fax' NOT NULL, tag TEXT NOT NULL, organization_unit TEXT, person_user_role TEXT, activity_period TEXT, PRIMARY KEY(id));
CREATE TABLE multimedia_keywords (id BIGSERIAL, object_ref BIGINT NOT NULL, keyword TEXT NOT NULL, keyword_indexed TEXT, PRIMARY KEY(id));
CREATE TABLE people_aliases (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, person_ref BIGINT NOT NULL, collection_ref BIGINT DEFAULT 0 NOT NULL, person_name TEXT NOT NULL, PRIMARY KEY(id));
CREATE TABLE collections_admin (id BIGSERIAL, collection_ref BIGINT DEFAULT 0 NOT NULL, user_ref BIGINT DEFAULT 0 NOT NULL, PRIMARY KEY(id));
CREATE TABLE users_login_infos (user_ref BIGINT, login_type TEXT DEFAULT 'local' NOT NULL, user_name TEXT, password TEXT, system_id TEXT, last_seen TIMESTAMP, PRIMARY KEY(user_ref, system_id));
CREATE TABLE tag_groups (id BIGSERIAL, tag_ref BIGINT NOT NULL, group_name TEXT NOT NULL, group_name_indexed TEXT, sub_group_name TEXT NOT NULL, sub_group_name_indexed TEXT, color TEXT DEFAULT '#FFFFFF', PRIMARY KEY(id));
CREATE TABLE catalogue_relationships (id BIGSERIAL, table_name TEXT NOT NULL, record_id_1 BIGINT NOT NULL, record_id_2 BIGINT NOT NULL, relationship_type BIGINT DEFAULT recombined from NOT NULL, PRIMARY KEY(id));
CREATE TABLE users_languages (users_ref BIGINT, language_country TEXT, mother BOOLEAN DEFAULT 'true' NOT NULL, prefered_language BOOLEAN DEFAULT 'false' NOT NULL, PRIMARY KEY(users_ref, language_country));
CREATE TABLE users_workflow (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, user_ref BIGINT NOT NULL, status TEXT DEFAULT 'to check' NOT NULL, modification_date_time TIMESTAMP NOT NULL, comment TEXT, PRIMARY KEY(id));
CREATE TABLE users_tracking (id BIGSERIAL, table_name TEXT NOT NULL, record_id BIGINT NOT NULL, user_ref BIGINT NOT NULL, action TEXT DEFAULT 'insert' NOT NULL, modification_date_time TIMESTAMP NOT NULL, PRIMARY KEY(id));
CREATE TABLE taxonomy (id BIGSERIAL, name TEXT NOT NULL, name_indexed TEXT, level_ref BIGINT, status TEXT DEFAULT 'valid' NOT NULL, path TEXT DEFAULT '/' NOT NULL, parent_ref BIGINT DEFAULT 0 NOT NULL, domain_ref BIGINT DEFAULT 0 NOT NULL, domain_indexed TEXT DEFAULT '' NOT NULL, kingdom_ref BIGINT DEFAULT 0 NOT NULL, kingdom_indexed TEXT DEFAULT '' NOT NULL, super_phylum_ref BIGINT DEFAULT 0 NOT NULL, super_phylum_indexed TEXT DEFAULT '' NOT NULL, phylum_ref BIGINT DEFAULT 0 NOT NULL, phylum_indexed TEXT DEFAULT '' NOT NULL, sub_phylum_ref BIGINT DEFAULT 0 NOT NULL, sub_phylum_indexed TEXT DEFAULT '' NOT NULL, infra_phylum_ref BIGINT DEFAULT 0 NOT NULL, infra_phylum_indexed TEXT DEFAULT '' NOT NULL, super_cohort_botany_ref BIGINT DEFAULT 0 NOT NULL, super_cohort_botany_indexed TEXT DEFAULT '' NOT NULL, cohort_botany_ref BIGINT DEFAULT 0 NOT NULL, cohort_botany_indexed TEXT DEFAULT '' NOT NULL, sub_cohort_botany_ref BIGINT DEFAULT 0 NOT NULL, sub_cohort_botany_indexed TEXT DEFAULT '' NOT NULL, infra_cohort_botany_ref BIGINT DEFAULT 0 NOT NULL, infra_cohort_botany_indexed TEXT DEFAULT '' NOT NULL, super_class_ref BIGINT DEFAULT 0 NOT NULL, super_class_indexed TEXT DEFAULT '' NOT NULL, class_ref BIGINT DEFAULT 0 NOT NULL, class_indexed TEXT DEFAULT '' NOT NULL, sub_class_ref BIGINT DEFAULT 0 NOT NULL, sub_class_indexed TEXT DEFAULT '' NOT NULL, infra_class_ref BIGINT DEFAULT 0 NOT NULL, infra_class_indexed TEXT DEFAULT '' NOT NULL, super_division_ref BIGINT DEFAULT 0 NOT NULL, super_division_indexed TEXT DEFAULT '' NOT NULL, division_ref BIGINT DEFAULT 0 NOT NULL, division_indexed TEXT DEFAULT '' NOT NULL, sub_division_ref BIGINT DEFAULT 0 NOT NULL, sub_division_indexed TEXT DEFAULT '' NOT NULL, infra_division_ref BIGINT DEFAULT 0 NOT NULL, infra_division_indexed TEXT DEFAULT '' NOT NULL, super_legion_ref BIGINT DEFAULT 0 NOT NULL, super_legion_indexed TEXT DEFAULT '' NOT NULL, legion_ref BIGINT DEFAULT 0 NOT NULL, legion_indexed TEXT DEFAULT '' NOT NULL, sub_legion_ref BIGINT DEFAULT 0 NOT NULL, sub_legion_indexed TEXT DEFAULT '' NOT NULL, infra_legion_ref BIGINT DEFAULT 0 NOT NULL, infra_legion_indexed TEXT DEFAULT '' NOT NULL, super_cohort_zoology_ref BIGINT DEFAULT 0 NOT NULL, super_cohort_zoology_indexed TEXT DEFAULT '' NOT NULL, cohort_zoology_ref BIGINT DEFAULT 0 NOT NULL, cohort_zoology_indexed TEXT DEFAULT '' NOT NULL, sub_cohort_zoology_ref BIGINT DEFAULT 0 NOT NULL, sub_cohort_zoology_indexed TEXT DEFAULT '' NOT NULL, infra_cohort_zoology_ref BIGINT DEFAULT 0 NOT NULL, infra_cohort_zoology_indexed TEXT DEFAULT '' NOT NULL, super_order_ref BIGINT DEFAULT 0 NOT NULL, super_order_indexed TEXT DEFAULT '' NOT NULL, order_ref BIGINT DEFAULT 0 NOT NULL, order_indexed TEXT DEFAULT '' NOT NULL, sub_order_ref BIGINT DEFAULT 0 NOT NULL, sub_order_indexed TEXT DEFAULT '' NOT NULL, infra_order_ref BIGINT DEFAULT 0 NOT NULL, infra_order_indexed TEXT DEFAULT '' NOT NULL, section_zoology_ref BIGINT DEFAULT 0 NOT NULL, section_zoology_indexed TEXT DEFAULT '' NOT NULL, sub_section_zoology_ref BIGINT DEFAULT 0 NOT NULL, sub_section_zoology_indexed TEXT DEFAULT '' NOT NULL, super_family_ref BIGINT DEFAULT 0 NOT NULL, super_family_indexed TEXT DEFAULT '' NOT NULL, family_ref BIGINT DEFAULT 0 NOT NULL, family_indexed TEXT DEFAULT '' NOT NULL, sub_family_ref BIGINT DEFAULT 0 NOT NULL, sub_family_indexed TEXT DEFAULT '' NOT NULL, infra_family_ref BIGINT DEFAULT 0 NOT NULL, infra_family_indexed TEXT DEFAULT '' NOT NULL, super_tribe_ref BIGINT DEFAULT 0 NOT NULL, super_tribe_indexed TEXT DEFAULT '' NOT NULL, tribe_ref BIGINT DEFAULT 0 NOT NULL, tribe_indexed TEXT DEFAULT '' NOT NULL, sub_tribe_ref BIGINT DEFAULT 0 NOT NULL, sub_tribe_indexed TEXT DEFAULT '' NOT NULL, infra_tribe_ref BIGINT DEFAULT 0 NOT NULL, infra_tribe_indexed TEXT DEFAULT '' NOT NULL, genus_ref BIGINT DEFAULT 0 NOT NULL, genus_indexed TEXT DEFAULT '' NOT NULL, sub_genus_ref BIGINT DEFAULT 0 NOT NULL, sub_genus_indexed TEXT DEFAULT '' NOT NULL, section_botany_ref BIGINT DEFAULT 0 NOT NULL, section_botany_indexed TEXT DEFAULT '' NOT NULL, sub_section_botany_ref BIGINT DEFAULT 0 NOT NULL, sub_section_botany_indexed TEXT DEFAULT '' NOT NULL, serie_ref BIGINT DEFAULT 0 NOT NULL, serie_indexed TEXT DEFAULT '' NOT NULL, sub_serie_ref BIGINT DEFAULT 0 NOT NULL, sub_serie_indexed TEXT DEFAULT '' NOT NULL, super_species_ref BIGINT DEFAULT 0 NOT NULL, super_species_indexed TEXT DEFAULT '' NOT NULL, species_ref BIGINT DEFAULT 0 NOT NULL, species_indexed TEXT DEFAULT '' NOT NULL, sub_species_ref BIGINT DEFAULT 0 NOT NULL, sub_species_indexed TEXT DEFAULT '' NOT NULL, variety_ref BIGINT DEFAULT 0 NOT NULL, variety_indexed TEXT DEFAULT '' NOT NULL, sub_variety_ref BIGINT DEFAULT 0 NOT NULL, sub_variety_indexed TEXT DEFAULT '' NOT NULL, form_ref BIGINT DEFAULT 0 NOT NULL, form_indexed TEXT DEFAULT '' NOT NULL, sub_form_ref BIGINT DEFAULT 0 NOT NULL, sub_form_indexed TEXT DEFAULT '' NOT NULL, abberans_ref BIGINT DEFAULT 0 NOT NULL, abberans_indexed TEXT DEFAULT '' NOT NULL, extinct BOOLEAN DEFAULT 'false' NOT NULL, PRIMARY KEY(id));
ALTER TABLE specimen_parts ADD CONSTRAINT specimen_parts_specimen_individual_ref_specimen_individuals_id FOREIGN KEY (specimen_individual_ref) REFERENCES specimen_individuals(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_relationships ADD CONSTRAINT people_relationships_person_2_ref_people_id FOREIGN KEY (person_2_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_relationships ADD CONSTRAINT people_relationships_person_1_ref_people_id FOREIGN KEY (person_1_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_addresses ADD CONSTRAINT people_addresses_person_user_ref_people_id FOREIGN KEY (person_user_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_tables_fields_tracked ADD CONSTRAINT users_tables_fields_tracked_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE chronostratigraphy ADD CONSTRAINT chronostratigraphy_parent_ref_chronostratigraphy_id FOREIGN KEY (parent_ref) REFERENCES chronostratigraphy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections_fields_visibilities ADD CONSTRAINT collections_fields_visibilities_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections_fields_visibilities ADD CONSTRAINT collections_fields_visibilities_collection_ref_collections_id FOREIGN KEY (collection_ref) REFERENCES collections(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens_accompanying ADD CONSTRAINT specimens_accompanying_taxon_ref_taxonomy_id FOREIGN KEY (taxon_ref) REFERENCES taxonomy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens_accompanying ADD CONSTRAINT specimens_accompanying_specimen_ref_specimens_id FOREIGN KEY (specimen_ref) REFERENCES specimens(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens_accompanying ADD CONSTRAINT specimens_accompanying_mineral_ref_mineralogy_id FOREIGN KEY (mineral_ref) REFERENCES mineralogy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE associated_multimedia ADD CONSTRAINT associated_multimedia_multimedia_ref_multimedia_id FOREIGN KEY (multimedia_ref) REFERENCES multimedia(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE lithology ADD CONSTRAINT lithology_parent_ref_lithology_id FOREIGN KEY (parent_ref) REFERENCES lithology(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE possible_upper_levels ADD CONSTRAINT possible_upper_levels_level_upper_ref_catalogue_levels_id FOREIGN KEY (level_upper_ref) REFERENCES catalogue_levels(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE possible_upper_levels ADD CONSTRAINT possible_upper_levels_level_ref_catalogue_levels_id FOREIGN KEY (level_ref) REFERENCES catalogue_levels(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE lithostratigraphy ADD CONSTRAINT lithostratigraphy_parent_ref_lithostratigraphy_id FOREIGN KEY (parent_ref) REFERENCES lithostratigraphy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_languages ADD CONSTRAINT people_languages_people_ref_people_id FOREIGN KEY (people_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_addresses ADD CONSTRAINT users_addresses_person_user_ref_users_id FOREIGN KEY (person_user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE my_preferences ADD CONSTRAINT my_preferences_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE my_preferences ADD CONSTRAINT my_preferences_icon_ref_multimedia_id FOREIGN KEY (icon_ref) REFERENCES multimedia(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE multimedia ADD CONSTRAINT multimedia_parent_ref_multimedia_id FOREIGN KEY (parent_ref) REFERENCES multimedia(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimen_parts_insurances ADD CONSTRAINT specimen_parts_insurances_specimen_part_ref_specimen_parts_id FOREIGN KEY (specimen_part_ref) REFERENCES specimen_parts(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_comm ADD CONSTRAINT users_comm_person_user_ref_users_id FOREIGN KEY (person_user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_multimedia ADD CONSTRAINT users_multimedia_person_user_ref_users_id FOREIGN KEY (person_user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_multimedia ADD CONSTRAINT users_multimedia_object_ref_multimedia_id FOREIGN KEY (object_ref) REFERENCES multimedia(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE gtu_tags ADD CONSTRAINT gtu_tags_tag_group_ref_tag_groups_id FOREIGN KEY (tag_group_ref) REFERENCES tag_groups(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE gtu_tags ADD CONSTRAINT gtu_tags_gtu_ref_gtu_id FOREIGN KEY (gtu_ref) REFERENCES gtu(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimen_individuals ADD CONSTRAINT specimen_individuals_specimen_ref_specimens_id FOREIGN KEY (specimen_ref) REFERENCES specimens(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collection_maintenance ADD CONSTRAINT collection_maintenance_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE record_visibilities ADD CONSTRAINT record_visibilities_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_taxon_ref_taxonomy_id FOREIGN KEY (taxon_ref) REFERENCES taxonomy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_mineral_ref_mineralogy_id FOREIGN KEY (mineral_ref) REFERENCES mineralogy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_lithology_ref_lithology_id FOREIGN KEY (lithology_ref) REFERENCES lithology(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_litho_ref_lithostratigraphy_id FOREIGN KEY (litho_ref) REFERENCES lithostratigraphy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_identification_taxon_ref_taxonomy_id FOREIGN KEY (identification_taxon_ref) REFERENCES taxonomy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_host_taxon_ref_taxonomy_id FOREIGN KEY (host_taxon_ref) REFERENCES taxonomy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_host_specimen_ref_specimens_id FOREIGN KEY (host_specimen_ref) REFERENCES specimens(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_expedition_ref_expeditions_id FOREIGN KEY (expedition_ref) REFERENCES expeditions(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_collection_ref_collections_id FOREIGN KEY (collection_ref) REFERENCES collections(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE specimens ADD CONSTRAINT specimens_chrono_ref_chronostratigraphy_id FOREIGN KEY (chrono_ref) REFERENCES chronostratigraphy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_coll_rights_asked ADD CONSTRAINT users_coll_rights_asked_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_coll_rights_asked ADD CONSTRAINT users_coll_rights_asked_collection_ref_collections_id FOREIGN KEY (collection_ref) REFERENCES collections(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_multimedia ADD CONSTRAINT people_multimedia_person_user_ref_people_id FOREIGN KEY (person_user_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_multimedia ADD CONSTRAINT people_multimedia_object_ref_multimedia_id FOREIGN KEY (object_ref) REFERENCES multimedia(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE mineralogy ADD CONSTRAINT mineralogy_parent_ref_mineralogy_id FOREIGN KEY (parent_ref) REFERENCES mineralogy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE my_saved_specimens ADD CONSTRAINT my_saved_specimens_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE catalogue_people ADD CONSTRAINT catalogue_people_people_ref_people_id FOREIGN KEY (people_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE gtu ADD CONSTRAINT gtu_parent_ref_gtu_id FOREIGN KEY (parent_ref) REFERENCES gtu(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections_rights ADD CONSTRAINT collections_rights_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections_rights ADD CONSTRAINT collections_rights_collection_ref_collections_id FOREIGN KEY (collection_ref) REFERENCES collections(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE properties_values ADD CONSTRAINT properties_values_property_ref_catalogue_properties_id FOREIGN KEY (property_ref) REFERENCES catalogue_properties(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections ADD CONSTRAINT collections_parent_ref_collections_id FOREIGN KEY (parent_ref) REFERENCES collections(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections ADD CONSTRAINT collections_main_manager_ref_users_id FOREIGN KEY (main_manager_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections ADD CONSTRAINT collections_institution_ref_people_id FOREIGN KEY (institution_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE my_saved_searches ADD CONSTRAINT my_saved_searches_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE soortenregister ADD CONSTRAINT soortenregister_taxa_ref_taxonomy_id FOREIGN KEY (taxa_ref) REFERENCES taxonomy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE soortenregister ADD CONSTRAINT soortenregister_habitat_ref_habitats_id FOREIGN KEY (habitat_ref) REFERENCES habitats(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE soortenregister ADD CONSTRAINT soortenregister_gtu_ref_gtu_id FOREIGN KEY (gtu_ref) REFERENCES gtu(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_comm ADD CONSTRAINT people_comm_person_user_ref_people_id FOREIGN KEY (person_user_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE multimedia_keywords ADD CONSTRAINT multimedia_keywords_object_ref_multimedia_id FOREIGN KEY (object_ref) REFERENCES multimedia(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_aliases ADD CONSTRAINT people_aliases_person_ref_people_id FOREIGN KEY (person_ref) REFERENCES people(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE people_aliases ADD CONSTRAINT people_aliases_collection_ref_collections_id FOREIGN KEY (collection_ref) REFERENCES collections(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections_admin ADD CONSTRAINT collections_admin_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE collections_admin ADD CONSTRAINT collections_admin_collection_ref_collections_id FOREIGN KEY (collection_ref) REFERENCES collections(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_login_infos ADD CONSTRAINT users_login_infos_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE tag_groups ADD CONSTRAINT tag_groups_tag_ref_tags_id FOREIGN KEY (tag_ref) REFERENCES tags(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_languages ADD CONSTRAINT users_languages_users_ref_users_id FOREIGN KEY (users_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_workflow ADD CONSTRAINT users_workflow_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_tracking ADD CONSTRAINT users_tracking_user_ref_users_id FOREIGN KEY (user_ref) REFERENCES users(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE taxonomy ADD CONSTRAINT taxonomy_parent_ref_taxonomy_id FOREIGN KEY (parent_ref) REFERENCES taxonomy(id) NOT DEFERRABLE INITIALLY IMMEDIATE;
