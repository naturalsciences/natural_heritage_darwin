\set log_error_verbosity terse

/*** All length conversion functions ***/

DROP FUNCTION IF EXISTS fct_cpy_unified_values() CASCADE;
DROP FUNCTION IF EXISTS convert_to_unified (IN property varchar, IN property_unit varchar, IN property_type varchar) CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_length_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_temperature_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_time_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_speed_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) CASCADE;

/*** Tracking and logging functions ***/

DROP FUNCTION IF EXISTS fct_trk_log_table() CASCADE;

/*** All hierarchical catalogues functions ***/

DROP FUNCTION IF EXISTS fct_cpy_gtuTags() CASCADE;
DROP FUNCTION IF EXISTS lineToTagRows(line text) CASCADE;
DROP FUNCTION IF EXISTS lineToTagArray(line text) CASCADE;

/*** Others ***/

DROP FUNCTION IF EXISTS fct_cpy_path() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_path_catalogs() CASCADE;

DROP FUNCTION IF EXISTS fct_cpy_FormattedName() CASCADE;
DROP FUNCTION IF EXISTS fct_chk_AreRole() CASCADE;
DROP FUNCTION IF EXISTS fct_cas_userType() CASCADE;
DROP FUNCTION IF EXISTS fct_array_find(IN in_array varchar, IN elem anyelement,OUT item_order integer) CASCADE;
DROP FUNCTION IF EXISTS fct_array_find(IN in_array anyarray, IN elem anyelement,OUT item_order integer) CASCADE;
DROP FUNCTION IF EXISTS fct_explode_array(in_array anyarray) CASCADE;
DROP FUNCTION IF EXISTS fct_remove_array_elem(IN in_array anyarray, IN elem anyelement,OUT out_array anyarray) CASCADE;
DROP FUNCTION IF EXISTS fct_clear_referencedRecord() CASCADE;
DROP FUNCTION IF EXISTS fct_clr_specialstatus() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_fullToIndex() CASCADE;
DROP FUNCTION IF EXISTS fullToIndex(to_indexed varchar) CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_specimensMainCode() CASCADE;
DROP FUNCTION IF EXISTS fct_trg_word() CASCADE;
DROP FUNCTION IF EXISTS fct_nbr_in_synonym() CASCADE;
DROP FUNCTION IF EXISTS fct_nbr_in_relation() CASCADE;
DROP FUNCTION IF EXISTS fct_chk_onceinpath(path varchar) CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_word(tbl_name character varying, fld_name character varying, word_ts tsvector) CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_updateHosts() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_updateSpecHostImpact() CASCADE;
DROP FUNCTION IF EXISTS concat(VARIADIC text[]) CASCADE;
DROP FUNCTION IF EXISTS ts_stat(tsvector, OUT word text, OUT ndoc integer, OUT nentry integer) CASCADE;
DROP FUNCTION IF EXISTS sha1(bytea) CASCADE;
DROP FUNCTION IF EXISTS get_setting(text, OUT value text) CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_updateCollectionAdmin() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_updateCollectionRights() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_updateUserRights() CASCADE;
DROP FUNCTION IF EXISTS getGtusForTags(in_array anyarray) CASCADE;
DROP FUNCTION IF EXISTS getTagsIndexedAsArray(IN tagList varchar) CASCADE;
DROP FUNCTION IF EXISTS fct_update_darwin_flat() CASCADE;
DROP FUNCTION IF EXISTS fct_darwin_flat_individuals_after_del() CASCADE;
DROP FUNCTION IF EXISTS fct_searchCodes(VARIADIC varchar[])  CASCADE;
DROP FUNCTION IF EXISTS fct_clr_savedspecimens() CASCADE;
DROP FUNCTION IF EXISTS fct_search_methods(str_ids varchar) CASCADE;
DROP FUNCTION IF EXISTS fct_search_tools(str_ids varchar) CASCADE;
DROP FUNCTION IF EXISTS fct_delete_darwin_flat_ind_part() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_location() CASCADE;
DROP FUNCTION IF EXISTS fct_chk_specimenCollectionAllowed() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_updateMyWidgetsColl() CASCADE;
DROP FUNCTION IF EXISTS fct_chk_parentCollInstitution() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_updateCollInstitutionCascade() CASCADE;
DROP FUNCTION IF EXISTS fct_unpromotion_impact_prefs() CASCADE;
DROP FUNCTION IF EXISTS fct_chk_canUpdateCollectionsRights() CASCADE;

DROP AGGREGATE IF EXISTS array_accum (anyelement);
DROP AGGREGATE IF EXISTS dummy_first(anyelement);
DROP FUNCTION IF EXISTS dummy( in anyelement, inout anyelement ) CASCADE;
DROP FUNCTION IF EXISTS convert_to_integer(v_input varchar) CASCADE;

DROP FUNCTION IF EXISTS fct_search_authorized_encoding_collections (user_id integer) CASCADE;
DROP FUNCTION IF EXISTS fct_search_authorized_view_collections (user_id integer) CASCADE;
DROP FUNCTION IF EXISTS fct_filter_encodable_row(ids varchar, col_name varchar, user_id integer) CASCADE;
DROP FUNCTION IF EXISTS fct_remove_last_flag();

DROP FUNCTION IF EXISTS fct_chk_PeopleIsMoral() CASCADE;
DROP FUNCTION IF EXISTS fct_trg_chk_possible_upper_level () CASCADE;
DROP FUNCTION IF EXISTS fct_chk_possible_upper_level () CASCADE;
DROP FUNCTION IF EXISTS fct_chk_upper_level_for_childrens() CASCADE;
DROP FUNCTION IF EXISTS fct_chk_ReferencedRecord() CASCADE;
DROP FUNCTION IF EXISTS fct_chk_ReferencedRecordRelationShip() CASCADE;
DROP FUNCTION IF EXISTS trg_fct_chk_possible_upper_level() CASCADE;

DROP FUNCTION IF EXISTS fct_set_user(user_id integer) ;
DROP FUNCTION IF EXISTS fct_upd_staging_fields() CASCADE;
DROP FUNCTION IF EXISTS fct_importer_abcd(integer) CASCADE;
DROP FUNCTION IF EXISTS fct_importer_catalogue(req_import_ref integer,referenced_relation text);
DROP FUNCTION IF EXISTS fct_importer_catalogue(req_import_ref integer,referenced_relation text,exclude_invalid_entries boolean);
DROP FUNCTION IF EXISTS fct_upd_people_staging_fields();
DROP FUNCTION IF EXISTS trg_ins_update_dict() CASCADE;
DROP FUNCTION IF EXISTS trg_del_dict() CASCADE;

DROP FUNCTION IF EXISTS fct_upd_people_in_flat() CASCADE;
DROP FUNCTION IF EXISTS fct_update_import();
DROP FUNCTION IF EXISTS fct_imp_checker_catalogue(staging, text, text) CASCADE;
DROP FUNCTION IF EXISTS fct_imp_checker_manager(staging) CASCADE;
DROP FUNCTION IF EXISTS fct_imp_checker_catalogues_parents(staging, integer, text, text) CASCADE;
DROP FUNCTION IF EXISTS fct_imp_checker_igs(staging, boolean) CASCADE;
DROP FUNCTION IF EXISTS fct_imp_checker_expeditions(staging, boolean) CASCADE;
DROP FUNCTION IF EXISTS fct_imp_checker_gtu(staging, boolean) CASCADE;
DROP FUNCTION IF EXISTS fct_look_for_people(text) CASCADE;
DROP FUNCTION IF EXISTS fct_imp_checker_people(staging) CASCADE;
DROP FUNCTION IF EXISTS fct_importer_dna(integer) CASCADE;
DROP FUNCTION IF EXISTS fct_upd_people_staging_fields() CASCADE;
DROP FUNCTION IF EXISTS fct_mask_date(timestamp , integer) CASCADE;
DROP FUNCTION IF EXISTS upsert (varchar, hstore) CASCADE;
DROP FUNCTION IF EXISTS fct_auto_insert_status_history() CASCADE;
DROP FUNCTION IF EXISTS fct_remove_last_flag_loan() CASCADE;
DROP FUNCTION IF EXISTS fct_remove_last_flag() CASCADE;
DROP FUNCTION IF EXISTS convert_to_real(varchar) CASCADE;

DROP FUNCTION IF EXISTS convert_to_real(character varying);
DROP FUNCTION IF EXISTS fct_add_in_dict(text, text, text, text);
DROP FUNCTION IF EXISTS fct_add_in_dict_dept(text, text, text, text, text, text);
DROP FUNCTION IF EXISTS fct_del_in_dict(text, text, text, text);
DROP FUNCTION IF EXISTS fct_del_in_dict_dept(text, text, text, text, text, text, text);
DROP FUNCTION IF EXISTS fct_find_tax_level(text, integer);
DROP FUNCTION IF EXISTS fct_chk_possible_upper_level(character varying, integer, integer, integer);
DROP FUNCTION IF EXISTS fct_cpy_loan_history(integer);
DROP FUNCTION IF EXISTS fct_cpy_volume_conversion(real, character varying);
DROP FUNCTION IF EXISTS fct_cpy_weight_conversion(real, character varying);
DROP FUNCTION IF EXISTS fct_look_for_institution(text);
DROP FUNCTION IF EXISTS fct_remove_array_elem(anyarray, anyarray);
DROP FUNCTION IF EXISTS get_import_row();
DROP FUNCTION IF EXISTS getspecificparentforlevel(character varying, character varying, character varying);
DROP FUNCTION IF EXISTS linetotagrowsformatconserved(text);
DROP FUNCTION IF EXISTS touniquestr(character varying);

DROP FUNCTION IF EXISTS chk_part_not_loaned() CASCADE;
DROP FUNCTION IF EXISTS fct_clear_identifiers_in_flat() CASCADE;
DROP FUNCTION IF EXISTS fct_count_units() CASCADE;
DROP FUNCTION IF EXISTS fct_cpy_deleted_file();
DROP FUNCTION IF EXISTS fct_cpy_ig_to_loan_items() CASCADE;
DROP FUNCTION IF EXISTS fct_informative_reset_last_flag();
DROP FUNCTION IF EXISTS fct_update_specimen_flat();
DROP FUNCTION IF EXISTS fct_update_specimens_flat_related();
DROP FUNCTION IF EXISTS fct_upd_institution_staging_relationship() ;
DROP FUNCTION IF EXISTS fct_imp_checker_staging_info() ;
DROP FUNCTION IF EXISTS fct_imp_checker_staging_info(staging);
DROP FUNCTION IF EXISTS fct_imp_checker_staging_relationship()  ;
DROP FUNCTION IF EXISTS check_auto_increment_code_in_spec();
DROP FUNCTION IF EXISTS fct_after_save_add_code(collections.id%TYPE,specimens.id%TYPE);
DROP FUNCTION IF EXISTS update_collections_code_last_val();
DROP FUNCTION IF EXISTS update_collections_code_last_val_after_spec_del();

/* Listing functions for reporting */
DROP FUNCTION IF EXISTS fct_listing_taxonomy (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) ;
DROP FUNCTION IF EXISTS fct_listing_zoology (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) ;
DROP FUNCTION IF EXISTS fct_listing_botany (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) ;
DROP FUNCTION IF EXISTS fct_listing_chronostratigraphy (IN nbr_records INTEGER, VARIADIC chrono_unit_ids INTEGER[]) ;
DROP FUNCTION IF EXISTS fct_listing_lithostratigraphy (IN nbr_records INTEGER, VARIADIC litho_unit_ids INTEGER[]) ;
DROP FUNCTION IF EXISTS fct_listing_mineralogy (IN nbr_records INTEGER, VARIADIC mineralo_unit_ids INTEGER[]) ;
DROP FUNCTION IF EXISTS fct_listing_lithology (IN nbr_records INTEGER, VARIADIC litholo_unit_ids INTEGER[]) ;

/* Reporting functions */
drop function if exists fct_report_loans_transporters (loan_id loans.id%TYPE, transporter_side TEXT, lang TEXT);
drop function if exists fct_report_loans_return_to (loan_id loans.id%TYPE, lang TEXT);
drop function if exists fct_report_loans_maintenances (loan_id loans.id%TYPE, maintenance_type TEXT);
drop function if exists fct_report_loans_addresses (loan_id loans.id%TYPE, target_copy TEXT);
drop function if exists fct_report_loans_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text, targeted_catalogues text, with_addr boolean, lang text);

DROP FUNCTION IF EXISTS fct_duplicate_loans (loan_id loans.id%TYPE);
DROP FUNCTION IF EXISTS fct_clean_staging_catalogue ( importRef staging_catalogue.import_ref%TYPE );

-- Statistics functions
DROP FUNCTION IF EXISTS stats_collections_encoding (collections.id%TYPE, timestamp, timestamp);
DROP FUNCTION IF EXISTS stats_collections_encoding (collections.id%TYPE, text, text);
DROP FUNCTION IF EXISTS stats_encoders_encoding (collections.id%TYPE, users.id%TYPE, timestamp, timestamp);
DROP FUNCTION IF EXISTS stats_encoders_encoding (collections.id%TYPE, TEXT, TEXT, TEXT);
DROP FUNCTION IF EXISTS stats_encoders_encoding (collections.id%TYPE, TEXT, timestamp, timestamp);

-- Imports functions
DROP FUNCTION IF EXISTS fct_catalogue_import_keywords_update();