 GRANT USAGE ON SCHEMA :dbname TO d2viewer;
 SET search_path TO :dbname, public;

 GRANT SELECT ON template_table_record_ref TO d2viewer;
 GRANT SELECT ON catalogue_levels TO d2viewer;
 GRANT SELECT ON catalogue_properties TO d2viewer;
 GRANT SELECT ON template_classifications TO d2viewer;
 GRANT SELECT ON collection_maintenance TO d2viewer;
 GRANT SELECT ON catalogue_relationships TO d2viewer;
 GRANT SELECT ON classification_keywords TO d2viewer;
 GRANT SELECT ON collections TO d2viewer;
 GRANT SELECT ON collections_rights TO d2viewer;
 GRANT SELECT ON comments TO d2viewer;
 GRANT SELECT ON collecting_tools TO d2viewer;
 GRANT SELECT ON collecting_methods TO d2viewer;
 GRANT SELECT ON codes TO d2viewer;
 GRANT SELECT ON classification_synonymies TO d2viewer;
 GRANT SELECT ON igs TO d2viewer;
 GRANT SELECT ON ext_links TO d2viewer;
 GRANT SELECT ON expeditions TO d2viewer;
 GRANT SELECT ON identifications TO d2viewer;
 GRANT SELECT ON flat_dict TO d2viewer;
 GRANT SELECT ON insurances TO d2viewer;
 GRANT SELECT ON lithology TO d2viewer;
 GRANT SELECT ON imports TO d2viewer;
 GRANT SELECT ON people_addresses TO d2viewer;
 GRANT SELECT ON template_people TO d2viewer;
 GRANT SELECT ON people_comm TO d2viewer;
 GRANT SELECT ON my_saved_searches TO d2viewer;
 GRANT SELECT ON template_people_users_addr_common TO d2viewer;
 GRANT SELECT ON my_widgets TO d2viewer;
 GRANT SELECT ON template_people_users_comm_common TO d2viewer;
 GRANT SELECT ON specimen_collecting_methods TO d2viewer;
 GRANT SELECT ON people_languages TO d2viewer;
 GRANT SELECT ON specimens_accompanying TO d2viewer;
 GRANT SELECT ON possible_upper_levels TO d2viewer;
 GRANT SELECT ON people_relationships TO d2viewer;
 GRANT SELECT ON specimen_individuals TO d2viewer;
 GRANT SELECT ON properties_values TO d2viewer;
 GRANT SELECT ON preferences TO d2viewer;
 GRANT SELECT ON staging_tag_groups TO d2viewer;
 GRANT SELECT ON users_comm TO d2viewer;
 GRANT SELECT ON tags TO d2viewer;
 GRANT SELECT ON users_login_infos TO d2viewer;
 GRANT SELECT ON users_addresses TO d2viewer;
 GRANT SELECT ON staging_people TO d2viewer;
 GRANT SELECT ON users_tracking TO d2viewer;
 GRANT SELECT ON taxonomy TO d2viewer;
 GRANT SELECT ON lithostratigraphy TO d2viewer;
 GRANT SELECT ON staging TO d2viewer;
 GRANT SELECT ON mineralogy TO d2viewer;
 GRANT SELECT ON tag_groups TO d2viewer;
 GRANT SELECT ON chronostratigraphy TO d2viewer;
 GRANT SELECT ON catalogue_people TO d2viewer;
 GRANT SELECT ON gtu TO d2viewer;
 GRANT SELECT ON vernacular_names TO d2viewer;
 GRANT SELECT ON specimen_collecting_tools TO d2viewer;
 GRANT SELECT ON specimens TO d2viewer;
 GRANT SELECT ON people TO d2viewer;
 GRANT SELECT ON multimedia TO d2viewer;
 GRANT SELECT ON specimen_parts TO d2viewer;
 GRANT SELECT ON loan_items TO d2viewer;
 GRANT SELECT ON loan_rights TO d2viewer;
 GRANT SELECT ON loan_status TO d2viewer;
 GRANT SELECT ON informative_workflow TO d2viewer;
 GRANT SELECT ON users TO d2viewer;
 GRANT SELECT ON loans TO d2viewer;
 GRANT SELECT ON loan_history TO d2viewer;
 GRANT SELECT ON catalogue_bibliography TO d2viewer;
 GRANT SELECT ON bibliography TO d2viewer;
 GRANT SELECT ON specimens_flat TO d2viewer;

 GRANT SELECT, INSERT ON users_comm TO d2viewer;
 GRANT USAGE ON users_comm_id_seq to d2viewer;
 GRANT SELECT, INSERT ON users_login_infos TO d2viewer;
 GRANT USAGE ON users_login_infos_id_seq TO d2viewer;
 GRANT SELECT, INSERT ON users_addresses TO d2viewer;
 GRANT USAGE ON users_addresses_id_seq TO d2viewer;
 GRANT SELECT, INSERT ON my_widgets TO d2viewer;
 GRANT USAGE ON my_widgets_id_seq TO d2viewer;
 GRANT SELECT, INSERT ON users TO d2viewer;
 GRANT USAGE ON users_id_seq TO d2viewer;
 GRANT SELECT, INSERT ON flat_dict TO d2viewer;
 GRANT USAGE ON flat_dict_id_seq TO d2viewer;
 GRANT SELECT, INSERT ON preferences TO d2viewer;
 GRANT USAGE ON preferences_id_seq TO d2viewer;
 GRANT SELECT, INSERT ON informative_workflow TO d2viewer;
 GRANT USAGE ON informative_workflow_id_seq TO d2viewer;
 ALTER USER d2viewer SET search_path TO darwin2, public;
