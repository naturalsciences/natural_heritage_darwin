CREATE TRIGGER trg_cpy_specimensMainCode_specimenPartCode AFTER INSERT
	ON specimen_parts FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_specimensMainCode();

CREATE TRIGGER trg_cpy_idToCode_gtu BEFORE INSERT OR UPDATE
	ON gtu FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_idToCode();

-- BEGIN FULLTOINDEX
CREATE TRIGGER trg_cpy_fullToIndex_catalogueproperties BEFORE INSERT OR UPDATE
	ON catalogue_properties FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_chronostratigraphy BEFORE INSERT OR UPDATE
	ON chronostratigraphy FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_expeditions BEFORE INSERT OR UPDATE
	ON expeditions FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();


CREATE TRIGGER trg_cpy_fullToIndex_identifications BEFORE INSERT OR UPDATE
	ON identifications FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_lithology BEFORE INSERT OR UPDATE
	ON lithology FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_lithostratigraphy BEFORE INSERT OR UPDATE
	ON lithostratigraphy FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_mineralogy BEFORE INSERT OR UPDATE
	ON mineralogy FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_multimedia BEFORE INSERT OR UPDATE
	ON multimedia FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_codes BEFORE INSERT OR UPDATE
	ON codes FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_taggroups BEFORE INSERT OR UPDATE
	ON tag_groups FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_taxa BEFORE INSERT OR UPDATE
	ON taxonomy FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_collection BEFORE INSERT OR UPDATE
	ON collections FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();	

CREATE TRIGGER trg_cpy_fullToIndex_classification_keywords BEFORE INSERT OR UPDATE
        ON classification_keywords FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_classvernacularnames BEFORE INSERT OR UPDATE
	ON class_vernacular_names FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_vernacularnames BEFORE INSERT OR UPDATE
	ON vernacular_names FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_igs BEFORE INSERT OR UPDATE
	ON igs FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_collecting_methods BEFORE INSERT OR UPDATE
  ON collecting_methods FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_collecting_tools BEFORE INSERT OR UPDATE
  ON collecting_tools FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_cpy_fullToIndex_loans BEFORE INSERT OR UPDATE
  ON loans FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_clr_specialstatus_specimenindividuals BEFORE INSERT OR UPDATE
	ON specimen_individuals FOR EACH ROW
	EXECUTE PROCEDURE fct_clr_specialstatus();

CREATE TRIGGER trg_cpy_gtuTags_TagGroups AFTER INSERT OR UPDATE OR DELETE
	ON tag_groups FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_gtuTags();

/******* BEGIN TRIGGER REFS *************
**
**
*****************************************/
CREATE TRIGGER trg_clr_referenceRecord_staging AFTER DELETE
	ON staging FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_gtu AFTER DELETE
	ON gtu FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_identifiers_in_flat BEFORE DELETE
	ON identifications FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_identifiers_in_flat();

CREATE TRIGGER trg_clr_referenceRecord_identifications AFTER DELETE
	ON identifications FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_insurances AFTER DELETE
 	ON insurances FOR EACH ROW
 	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_vernacularnames AFTER DELETE
	ON vernacular_names FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_expeditions AFTER DELETE
	ON expeditions FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_people AFTER DELETE
	ON people FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_users AFTER DELETE
	ON users FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_multimedia AFTER DELETE
	ON multimedia FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();


CREATE TRIGGER trg_clr_referenceRecord_collections AFTER DELETE
	ON collections FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

/*CREATE TRIGGER trg_clr_referenceRecord_userscollrightsasked AFTER DELETE
	ON users_coll_rights_asked FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();
*/
CREATE TRIGGER trg_clr_referenceRecord_mysavedsearches AFTER DELETE
	ON collection_maintenance FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_taxa AFTER DELETE
	ON taxonomy FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_chronostratigraphy AFTER DELETE
	ON chronostratigraphy FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_lithostratigraphy AFTER DELETE
	ON lithostratigraphy FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_mineralogy AFTER DELETE
	ON mineralogy FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_lithology AFTER DELETE
	ON lithology FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_specimens AFTER DELETE
	ON specimens FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_specimenindividuals AFTER DELETE
	ON specimen_individuals FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_specimenparts AFTER DELETE
	ON specimen_parts FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_specimensaccompanying AFTER DELETE
	ON specimens_accompanying FOR EACH ROW
	EXECUTE PROCEDURE fct_clear_referencedRecord();

/**** BEGIN _TS *****/

CREATE TRIGGER trg_cpy_toFullText_comments BEFORE INSERT OR UPDATE
	ON comments FOR EACH ROW
	EXECUTE PROCEDURE tsvector_update_trigger(comment_ts, 'pg_catalog.simple', comment);

CREATE TRIGGER trg_cpy_toFullText_ext_links BEFORE INSERT OR UPDATE
	ON ext_links FOR EACH ROW
	EXECUTE PROCEDURE tsvector_update_trigger(comment_ts, 'pg_catalog.simple', comment);

CREATE TRIGGER trg_cpy_toFullText_identifications BEFORE INSERT OR UPDATE
	ON identifications FOR EACH ROW
	EXECUTE PROCEDURE tsvector_update_trigger(value_defined_ts, 'pg_catalog.simple', value_defined);

CREATE TRIGGER trg_cpy_toFullText_peopleaddresses BEFORE INSERT OR UPDATE
	ON people_addresses FOR EACH ROW
	EXECUTE PROCEDURE tsvector_update_trigger(address_parts_ts, 'pg_catalog.simple', entry, po_box, extended_address, locality, region, zip_code, country);

CREATE TRIGGER trg_cpy_toFullText_usersaddresses BEFORE INSERT OR UPDATE
	ON users_addresses FOR EACH ROW
	EXECUTE PROCEDURE tsvector_update_trigger(address_parts_ts, 'pg_catalog.simple', entry, po_box, extended_address, locality, region, zip_code, country);

CREATE TRIGGER trg_cpy_toFullText_multimedia BEFORE INSERT OR UPDATE
	ON multimedia FOR EACH ROW
	EXECUTE PROCEDURE tsvector_update_trigger(search_ts, 'pg_catalog.simple', title, subject );

CREATE TRIGGER trg_cpy_toFullText_expeditions BEFORE INSERT OR UPDATE
  ON expeditions FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(name_ts, 'pg_catalog.simple', name);

CREATE TRIGGER trg_cpy_toFullText_vernacularnames BEFORE INSERT OR UPDATE
  ON vernacular_names FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(name_ts, 'pg_catalog.simple', name);

CREATE TRIGGER trg_cpy_toFullText_collectionmaintenance BEFORE INSERT OR UPDATE
	ON collection_maintenance FOR EACH ROW
	EXECUTE PROCEDURE tsvector_update_trigger(description_ts, 'pg_catalog.simple', description);

CREATE TRIGGER trg_cpy_updateCollectionRights AFTER INSERT OR UPDATE
	ON collections FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_updateCollectionRights();

CREATE TRIGGER trg_chk_canUpdateCollectionsRights BEFORE UPDATE
  ON collections_rights FOR EACH ROW
  EXECUTE PROCEDURE fct_chk_canUpdateCollectionsRights();

CREATE TRIGGER trg_cpy_updateUserRights AFTER INSERT OR UPDATE OR DELETE
  ON collections_rights FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_updateUserRights();

CREATE TRIGGER trg_cpy_updateUserRightsCollections AFTER INSERT OR UPDATE
  ON collections FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_updateUserRights();

CREATE TRIGGER trg_cpy_updateMyWidgetsCollRights AFTER UPDATE OR DELETE
  ON collections_rights FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_updateMyWidgetsColl();

CREATE TRIGGER trg_chk_specimenCollectionAllowed BEFORE INSERT OR UPDATE OR DELETE
  ON specimens FOR EACH ROW
  EXECUTE PROCEDURE fct_chk_specimenCollectionAllowed();

CREATE TRIGGER trg_chk_specimenIndCollectionAllowed BEFORE INSERT OR UPDATE OR DELETE
  ON specimen_individuals FOR EACH ROW
  EXECUTE PROCEDURE fct_chk_specimenCollectionAllowed();

CREATE TRIGGER trg_chk_specimenPartCollectionAllowed BEFORE INSERT OR UPDATE OR DELETE
  ON specimen_parts FOR EACH ROW
  EXECUTE PROCEDURE fct_chk_specimenCollectionAllowed();

CREATE TRIGGER trg_chk_parentCollInstitution BEFORE INSERT OR UPDATE
  ON collections FOR EACH ROW
  EXECUTE PROCEDURE fct_chk_parentCollInstitution();

CREATE TRIGGER trg_cpy_updateCollInstitutionCascade AFTER UPDATE
  ON collections FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_updateCollInstitutionCascade();

/* trigger set BEFORE update, in order to avoid bad db_user_type to be set when this user is a collection manager */

CREATE TRIGGER trg_cpy_FormattedName BEFORE INSERT OR UPDATE
	ON people FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_FormattedName();

CREATE TRIGGER trg_cpy_FormattedName BEFORE INSERT OR UPDATE
	ON users FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_FormattedName();

CREATE TRIGGER trg_cpy_path_multimedia BEFORE INSERT OR UPDATE
	ON multimedia FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_path();

CREATE TRIGGER trg_cpy_path_collections BEFORE INSERT OR UPDATE
	ON collections FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_path();

CREATE TRIGGER trg_cpy_path_peopleRelationships BEFORE INSERT OR UPDATE
	ON people_relationships FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_path();

CREATE TRIGGER trg_cpy_path_gtu BEFORE INSERT OR UPDATE
	ON gtu FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_path();

CREATE TRIGGER trg_cpy_path_specimen_parts BEFORE INSERT OR UPDATE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_path();

CREATE TRIGGER trg_cpy_path_staging BEFORE INSERT OR UPDATE
        ON staging FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_path();
        
CREATE TRIGGER trg_upd_fields_staging BEFORE UPDATE
        ON staging FOR EACH ROW
        EXECUTE PROCEDURE fct_upd_staging_fields();   
        
CREATE TRIGGER trg_upd_people_ref_staging_people AFTER UPDATE
        ON staging_people FOR EACH ROW
        EXECUTE PROCEDURE fct_upd_people_staging_fields();               

CREATE TRIGGER trg_cpy_path_chronostratigraphy BEFORE INSERT OR UPDATE
        ON chronostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_path_catalogs();

CREATE TRIGGER trg_chk_upper_level_for_childrens_chronostratigraphy AFTER UPDATE
        ON chronostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();

CREATE TRIGGER trg_cpy_path_lithology BEFORE INSERT OR UPDATE
        ON lithology FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_path_catalogs();

CREATE TRIGGER trg_chk_upper_level_for_childrens_lithology AFTER UPDATE
        ON lithology FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();

CREATE TRIGGER trg_cpy_path_lithostratigraphy BEFORE INSERT OR UPDATE
        ON lithostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_path_catalogs();

CREATE TRIGGER trg_chk_upper_level_for_childrens_lithostratigraphy AFTER UPDATE
        ON lithostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();

CREATE TRIGGER trg_cpy_path_mineralogy BEFORE INSERT OR UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_path_catalogs();

CREATE TRIGGER trg_chk_upper_level_for_childrens_mineralogy AFTER UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();

CREATE TRIGGER trg_cpy_path_taxonomy BEFORE INSERT OR UPDATE
        ON taxonomy FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_path_catalogs();

CREATE TRIGGER trg_chk_upper_level_for_childrens_taxonomy AFTER UPDATE
        ON taxonomy FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();

/*** TRACKING ****/

CREATE TRIGGER trg_trk_log_table_catalogue_relationships AFTER INSERT OR UPDATE OR DELETE
        ON catalogue_relationships FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_classification_keywords AFTER INSERT OR UPDATE OR DELETE
        ON classification_keywords FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_classification_synonymies AFTER INSERT OR UPDATE OR DELETE
        ON classification_synonymies FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_catalogue_properties AFTER INSERT OR UPDATE OR DELETE
        ON catalogue_properties FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_properties_values AFTER INSERT OR UPDATE OR DELETE
        ON properties_values FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_identifications AFTER INSERT OR UPDATE OR DELETE
        ON identifications FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_class_vernacular_names AFTER INSERT OR UPDATE OR DELETE
        ON class_vernacular_names FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_vernacular_names AFTER INSERT OR UPDATE OR DELETE
        ON vernacular_names FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_people_relationships AFTER INSERT OR UPDATE OR DELETE
        ON people_relationships FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_people_comm AFTER INSERT OR UPDATE OR DELETE
        ON people_comm FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_people_addresses AFTER INSERT OR UPDATE OR DELETE
        ON people_addresses FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_people_multimedia AFTER INSERT OR UPDATE OR DELETE
        ON people_multimedia FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_collections_rights AFTER INSERT OR UPDATE OR DELETE
        ON collections_rights FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_specimens_accompanying AFTER INSERT OR UPDATE OR DELETE
        ON specimens_accompanying FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_collecting_tools AFTER INSERT OR UPDATE OR DELETE
        ON collecting_tools FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_specimen_collecting_tools AFTER INSERT OR UPDATE OR DELETE
        ON specimen_collecting_tools FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_collecting_methods AFTER INSERT OR UPDATE OR DELETE
        ON collecting_methods FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_specimen_collecting_methods AFTER INSERT OR UPDATE OR DELETE
        ON specimen_collecting_methods FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_comments AFTER INSERT OR UPDATE OR DELETE
        ON comments FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_ext_links AFTER INSERT OR UPDATE OR DELETE
        ON ext_links FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_gtu AFTER INSERT OR UPDATE OR DELETE
        ON gtu FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_tag_groups AFTER INSERT OR UPDATE OR DELETE
        ON tag_groups FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_expeditions AFTER INSERT OR UPDATE OR DELETE
        ON expeditions FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_multimedia AFTER INSERT OR UPDATE OR DELETE
        ON multimedia FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_collections AFTER INSERT OR UPDATE OR DELETE
        ON collections FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_collection_maintenance AFTER INSERT OR UPDATE OR DELETE
        ON collection_maintenance FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_igs AFTER INSERT OR UPDATE OR DELETE
        ON igs FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_codes AFTER INSERT OR UPDATE OR DELETE
        ON codes FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_insurances AFTER INSERT OR UPDATE OR DELETE
        ON insurances FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_specimens AFTER INSERT OR UPDATE OR DELETE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_specimen_individuals AFTER INSERT OR UPDATE OR DELETE
        ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_specimen_parts AFTER INSERT OR UPDATE OR DELETE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_taxonomy AFTER INSERT OR UPDATE OR DELETE
        ON taxonomy FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_chronostratigraphy AFTER INSERT OR UPDATE OR DELETE
        ON chronostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_lithostratigraphy AFTER INSERT OR UPDATE OR DELETE
        ON lithostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_mineralogy AFTER INSERT OR UPDATE OR DELETE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_lithology AFTER INSERT OR UPDATE OR DELETE
        ON lithology FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_trk_log_table_people AFTER INSERT OR UPDATE OR DELETE
        ON people FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

/*
** Trigger aimed at calculating unified values
*/

CREATE TRIGGER trg_cpy_unified_values BEFORE INSERT OR UPDATE
	ON properties_values FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_unified_values();

CREATE TRIGGER trg_cpy_unified_values BEFORE INSERT OR UPDATE
	ON catalogue_properties FOR EACH ROW
	EXECUTE PROCEDURE fct_cpy_unified_values();

/** GTU GIS ***/
CREATE TRIGGER trg_cpy_location BEFORE INSERT OR UPDATE
        ON gtu FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_location() ;

/**** ADDing TS words Trigger ***/
/*
CREATE TRIGGER trg_words_ts_cpy_collection_maintenance BEFORE INSERT OR UPDATE
	ON collection_maintenance FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_comments BEFORE INSERT OR UPDATE
	ON comments FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();
	
CREATE TRIGGER trg_words_ts_cpy_ext_links BEFORE INSERT OR UPDATE
	ON ext_links FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();*/

CREATE TRIGGER trg_words_ts_cpy_vernacular_names BEFORE INSERT OR UPDATE
	ON vernacular_names FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();
/*
CREATE TRIGGER trg_words_ts_cpy_identification BEFORE INSERT OR UPDATE
	ON identifications FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_multimedia BEFORE INSERT OR UPDATE
	ON multimedia FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();*/

CREATE TRIGGER trg_words_ts_cpy_people BEFORE INSERT OR UPDATE
	ON people FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_users BEFORE INSERT OR UPDATE
	ON users FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_expeditions BEFORE INSERT OR UPDATE
	ON expeditions FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_mineralogy BEFORE INSERT OR UPDATE
	ON mineralogy FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_chronostratigraphy BEFORE INSERT OR UPDATE
	ON chronostratigraphy FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_lithostratigraphy BEFORE INSERT OR UPDATE
	ON lithostratigraphy FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_lithology BEFORE INSERT OR UPDATE
	ON lithology FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_taxonomy BEFORE INSERT OR UPDATE
	ON taxonomy FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();

CREATE TRIGGER trg_words_ts_cpy_loans BEFORE INSERT OR UPDATE
        ON loans FOR EACH ROW
        EXECUTE PROCEDURE fct_trg_word();
/*
CREATE TRIGGER trg_words_ts_cpy_codes BEFORE INSERT OR UPDATE
	ON codes FOR EACH ROW
	EXECUTE PROCEDURE fct_trg_word();*/

CREATE TRIGGER trg_nbr_in_relation  BEFORE INSERT OR UPDATE
   	ON catalogue_relationships FOR EACH ROW
	EXECUTE PROCEDURE fct_nbr_in_relation();

CREATE TRIGGER trg_nbr_in_synonym  AFTER INSERT OR UPDATE
   	ON classification_synonymies FOR EACH ROW
	EXECUTE PROCEDURE fct_nbr_in_synonym();


/**** Specimen Hosting triggers ****/

CREATE TRIGGER trg_cpy_updateHosts AFTER UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_updateHosts();

CREATE TRIGGER trg_cpy_updateSpecHostImpact BEFORE UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_updateSpecHostImpact();

/**** Darwin Flat Synchronisation triggers ****/

CREATE TRIGGER trg_update_expeditions_darwin_flat AFTER UPDATE
        ON expeditions FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_collections_darwin_flat AFTER UPDATE
        ON collections FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_gtu_darwin_flat AFTER UPDATE
        ON gtu FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_tag_groups_darwin_flat AFTER INSERT OR UPDATE OR DELETE
        ON tag_groups FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_igs_darwin_flat AFTER UPDATE
        ON igs FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_taxonomy_darwin_flat AFTER UPDATE
        ON taxonomy FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_chronostratigraphy_darwin_flat AFTER UPDATE
        ON chronostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_lithostratigraphy_darwin_flat AFTER UPDATE
        ON lithostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_lithology_darwin_flat AFTER UPDATE
        ON lithology FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();

CREATE TRIGGER trg_update_mineralogy_darwin_flat AFTER UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE fct_update_darwin_flat();


CREATE TRIGGER trg_update_specimens_darwin_flat BEFORE INSERT OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE fct_update_specimen_flat();

CREATE TRIGGER trg_fct_count_units_individuals AFTER INSERT OR UPDATE OR DELETE
        ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE fct_count_units();

CREATE TRIGGER trg_fct_count_units_parts AFTER INSERT OR DELETE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE fct_count_units();


CREATE TRIGGER trg_unpromotion_remove_cols AFTER UPDATE
        ON users FOR EACH ROW
        EXECUTE PROCEDURE fct_unpromotion_impact_prefs();


/*************** CHECK of Referenced Records **************/

CREATE TRIGGER trg_chk_ref_record_catalogue_people AFTER INSERT OR UPDATE
        ON catalogue_people FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_comments AFTER INSERT OR UPDATE
        ON comments FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_ext_links AFTER INSERT OR UPDATE
        ON ext_links FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_catalogue_properties AFTER INSERT OR UPDATE
        ON catalogue_properties FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_identifications AFTER INSERT OR UPDATE
        ON identifications FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_class_vernacular_names AFTER INSERT OR UPDATE
        ON class_vernacular_names FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_informative_workflow AFTER INSERT OR UPDATE
        ON informative_workflow FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE trigger trg_chk_is_last_informative_workflow BEFORE INSERT
	ON informative_workflow FOR EACH ROW
	EXECUTE PROCEDURE fct_remove_last_flag();

CREATE TRIGGER trg_chk_ref_record_collection_maintenance AFTER INSERT OR UPDATE
        ON collection_maintenance FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_template_table_record_ref AFTER INSERT OR UPDATE
        ON template_table_record_ref FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_classification_synonymies AFTER INSERT OR UPDATE
        ON classification_synonymies FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_catalogue_codes AFTER INSERT OR UPDATE
        ON codes FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_associated_multimedia AFTER INSERT OR UPDATE
        ON associated_multimedia FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER trg_chk_ref_record_insurances AFTER INSERT OR UPDATE
        ON insurances FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();


CREATE TRIGGER trg_chk_ref_record_relationship_catalogue_relationships AFTER INSERT OR UPDATE
        ON catalogue_relationships FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecordRelationShip();


/************* Possible upper level Check ***********/

CREATE TRIGGER trg_chk_possible_upper_level_chronostratigraphy AFTER INSERT OR UPDATE
        ON chronostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();

CREATE TRIGGER trg_chk_possible_upper_level_lithostratigraphy AFTER INSERT OR UPDATE
        ON lithostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();

CREATE TRIGGER trg_chk_possible_upper_level_mineralogy AFTER INSERT OR UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();

CREATE TRIGGER trg_chk_possible_upper_level_lithology AFTER INSERT OR UPDATE
        ON lithology FOR EACH ROW
        EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();

CREATE TRIGGER trg_chk_possible_upper_level_taxonomy AFTER INSERT OR UPDATE
        ON taxonomy FOR EACH ROW
        EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();

/************ CHk Only One Lang ***************/

CREATE TRIGGER fct_chk_upper_level_for_childrens_people AFTER INSERT OR UPDATE
        ON people_languages FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_one_pref_language();

CREATE TRIGGER fct_chk_upper_level_for_childrens_users AFTER INSERT OR UPDATE
        ON users_languages FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_one_pref_language();


/************* Check If Institution is a Moral Person ***********/

CREATE TRIGGER fct_chk_PeopleIsMoral_collections AFTER INSERT OR UPDATE
        ON collections FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_PeopleIsMoral();

/************* ADD to dictionnary *******************/

CREATE TRIGGER fct_cpy_trg_ins_update_dict_codes AFTER INSERT OR UPDATE
        ON codes FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_collection_maintenance AFTER INSERT OR UPDATE
        ON collection_maintenance FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_identifications AFTER INSERT OR UPDATE
        ON identifications FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_people AFTER INSERT OR UPDATE
        ON people FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_people_addresses AFTER INSERT OR UPDATE
        ON people_addresses FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_insurances AFTER INSERT OR UPDATE
        ON insurances FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_mineralogy AFTER INSERT OR UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_specimen_individuals AFTER INSERT OR UPDATE
        ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_specimens AFTER INSERT OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_specimens_accompanying AFTER INSERT OR UPDATE
        ON specimens_accompanying FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_users AFTER INSERT OR UPDATE
        ON users FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_users_addresses AFTER INSERT OR UPDATE
        ON users_addresses FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_specimen_parts AFTER INSERT OR UPDATE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_loan_status AFTER INSERT OR UPDATE
        ON loan_status FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

/******************* DELETE FROM DICT ******************/

CREATE TRIGGER fct_cpy_trg_del_dict_codes AFTER DELETE  OR UPDATE
        ON codes FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_collection_maintenance AFTER DELETE  OR UPDATE
        ON collection_maintenance FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_identifications AFTER DELETE  OR UPDATE
        ON identifications FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_people AFTER DELETE  OR UPDATE
        ON people FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_people_addresses AFTER DELETE  OR UPDATE
        ON people_addresses FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_insurances AFTER DELETE  OR UPDATE
        ON insurances FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_mineralogy AFTER DELETE  OR UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_specimen_individuals AFTER DELETE  OR UPDATE
        ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_specimens AFTER DELETE  OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_specimens_accompanying AFTER DELETE  OR UPDATE
        ON specimens_accompanying FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_users AFTER DELETE  OR UPDATE
        ON users FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_users_addresses AFTER DELETE  OR UPDATE
        ON users_addresses FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_specimen_parts AFTER DELETE  OR UPDATE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_loan_status AFTER DELETE OR UPDATE
        ON loan_status FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();
  
/********************* *****/
      
CREATE TRIGGER trg_upd_people_in_flat AFTER INSERT OR UPDATE OR DELETE
  ON catalogue_people FOR EACH ROW
  EXECUTE PROCEDURE fct_upd_people_in_flat();

CREATE trigger trg_chk_is_last_loan_status BEFORE INSERT
        ON loan_status FOR EACH ROW
        EXECUTE PROCEDURE fct_remove_last_flag_loan();

CREATE trigger trg_add_status_history after INSERT
        ON loans FOR EACH ROW
        EXECUTE PROCEDURE fct_auto_insert_status_history();
