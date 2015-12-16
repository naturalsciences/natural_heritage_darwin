
DROP TRIGGER IF EXISTS trg_cpy_idToCode_gtu ON gtu ;
DROP TRIGGER IF EXISTS trg_update_import ON imports
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_lithology ON lithology ;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_catalogueproperties ON catalogue_properties ;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_chronostratigraphy ON chronostratigraphy ;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_expeditions ON expeditions ;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_identifications ON identifications;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_lithostratigraphy ON lithostratigraphy;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_mineralogy ON mineralogy;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_multimedia ON multimedia;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_codes ON codes ;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_taggroups ON tag_groups;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_taxa ON taxonomy;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_collection ON collections;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_vernacularnames ON vernacular_names;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_collecting_tools ON collecting_tools;
DROP TRIGGER IF EXISTS trg_cpy_fullToIndex_collecting_methods ON collecting_methods;

/*** REPERCUTION record_id ****/
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_bibliography ON bibliography;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_cataloguepeople ON catalogue_people;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_cataloguerelationships ON catalogue_relationships;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_chronostratigraphy ON chronostratigraphy;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_collections ON collections;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_collectionmaintenance ON collection_maintenance;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_expeditions ON expeditions;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_gtu ON gtu;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_identifications ON identifications;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_insurances ON insurances;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_loans ON loans;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_loan_items ON loan_items;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_lithology ON lithology;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_lithostratigraphy ON lithostratigraphy;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_mineralogy ON mineralogy;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_multimedia ON multimedia;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_mysavedsearches ON collection_maintenance;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_people ON people;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_specimens ON specimens;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_specimens_relationships ON specimens_relationships;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_staging ON staging;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_taxa ON taxonomy;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_users ON users;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_vernacularnames ON vernacular_names;


DROP TRIGGER IF EXISTS trg_cpy_toFullText_comments ON comments;
DROP TRIGGER IF EXISTS trg_cpy_toFullText_identifications ON identifications;
DROP TRIGGER IF EXISTS trg_cpy_toFullText_peopleaddresses ON people_addresses;
DROP TRIGGER IF EXISTS trg_cpy_toFullText_usersaddresses ON users_addresses;
DROP TRIGGER IF EXISTS trg_cpy_toFullText_multimedia ON multimedia;
DROP TRIGGER IF EXISTS trg_cpy_toFullText_collectionmaintenance ON collection_maintenance;
DROP TRIGGER IF EXISTS trg_cpy_toFullText_expeditions ON expeditions;
DROP TRIGGER IF EXISTS trg_cpy_toFullText_vernacularnames ON vernacular_names;

/*DROP TRIGGER IF EXISTS trg_cas_userType_users ON users;*/
DROP TRIGGER IF EXISTS trg_chk_AreRole ON catalogue_people;

DROP TRIGGER IF EXISTS trg_cpy_FormattedName ON people;
DROP TRIGGER IF EXISTS trg_cpy_FormattedName ON users;

DROP TRIGGER IF EXISTS trg_cpy_gtuTags_TagGroups ON tag_groups;

/*** Specimen hosting ***/
DROP TRIGGER IF EXISTS trg_cpy_updateHosts ON specimens;
DROP TRIGGER IF EXISTS trg_cpy_updateSpecHostImpact ON specimens;

/*** Words triggers ***/
DROP TRIGGER IF EXISTS trg_words_ts_cpy_collection_maintenance
	ON collection_maintenance;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_comments
	ON comments;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_vernacular_names
	ON vernacular_names;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_identification
	ON identifications;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_multimedia
	ON multimedia;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_people
	ON people;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_people_addresses
	ON people_addresses;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_users
	ON users;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_expeditions
	ON expeditions;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_mineralogy
	ON mineralogy;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_chronostratigraphy
	ON chronostratigraphy;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_lithostratigraphy
	ON lithostratigraphy;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_lithology
	ON lithology;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_taxonomy
	ON taxonomy;

DROP TRIGGER IF EXISTS trg_words_ts_cpy_codes
	ON codes;
    
DROP TRIGGER IF EXISTS trg_insert_auto_code 
    ON codes;    

DROP TRIGGER IF EXISTS trg_update_collections_code_last_val
    ON specimens;

DROP TRIGGER IF EXISTS trg_update_collections_code_last_val_after_spec_del
    ON specimens;

/*** Tracking ***/
DROP TRIGGER IF EXISTS trg_trk_log_table_catalogue_relationships 
        ON catalogue_relationships;

DROP TRIGGER IF EXISTS trg_trk_log_table_classification_keywords 
        ON classification_keywords;

DROP TRIGGER IF EXISTS trg_trk_log_table_classification_synonymies 
        ON classification_synonymies;

DROP TRIGGER IF EXISTS trg_trk_log_table_catalogue_properties 
        ON catalogue_properties;

DROP TRIGGER IF EXISTS trg_trk_log_table_properties_values 
        ON properties_values;

DROP TRIGGER IF EXISTS trg_trk_log_table_identifications 
        ON identifications;

DROP TRIGGER IF EXISTS trg_trk_log_table_vernacular_names 
        ON vernacular_names;

DROP TRIGGER IF EXISTS trg_trk_log_table_people_relationships 
        ON people_relationships;

DROP TRIGGER IF EXISTS trg_trk_log_table_people_comm 
        ON people_comm;

DROP TRIGGER IF EXISTS trg_trk_log_table_people_addresses 
        ON people_addresses;

DROP TRIGGER IF EXISTS trg_trk_log_table_collections_rights 
        ON collections_rights;

DROP TRIGGER IF EXISTS trg_trk_log_table_collecting_tools 
        ON collecting_tools;

DROP TRIGGER IF EXISTS trg_trk_log_table_specimen_collecting_tools 
        ON specimen_collecting_tools;

DROP TRIGGER IF EXISTS trg_trk_log_table_collecting_methods 
        ON collecting_methods;

DROP TRIGGER IF EXISTS trg_trk_log_table_specimen_collecting_methods 
        ON specimen_collecting_methods;

DROP TRIGGER IF EXISTS trg_trk_log_table_comments 
        ON comments;

DROP TRIGGER IF EXISTS trg_trk_log_table_ext_links 
        ON ext_links;

DROP TRIGGER IF EXISTS trg_trk_log_table_gtu 
        ON gtu;

DROP TRIGGER IF EXISTS trg_trk_log_table_tag_groups 
        ON tag_groups;

DROP TRIGGER IF EXISTS trg_trk_log_table_expeditions 
        ON expeditions;

DROP TRIGGER IF EXISTS trg_trk_log_table_multimedia 
        ON multimedia;

DROP TRIGGER IF EXISTS trg_trk_log_table_collections 
        ON collections;

DROP TRIGGER IF EXISTS trg_trk_log_table_collection_maintenance 
        ON collection_maintenance;

DROP TRIGGER IF EXISTS trg_trk_log_table_igs 
        ON igs;

DROP TRIGGER IF EXISTS trg_trk_log_table_codes 
        ON codes;

DROP TRIGGER IF EXISTS trg_trk_log_table_insurances 
        ON insurances;

DROP TRIGGER IF EXISTS trg_trk_log_table_specimens 
        ON specimens;

DROP TRIGGER IF EXISTS trg_trk_log_table_taxonomy 
        ON taxonomy;

DROP TRIGGER IF EXISTS trg_trk_log_table_chronostratigraphy 
        ON chronostratigraphy;

DROP TRIGGER IF EXISTS trg_trk_log_table_lithostratigraphy 
        ON lithostratigraphy;

DROP TRIGGER IF EXISTS trg_trk_log_table_mineralogy 
        ON mineralogy;

DROP TRIGGER IF EXISTS trg_trk_log_table_lithology 
        ON lithology;

DROP TRIGGER IF EXISTS trg_trk_log_table_people 
        ON people;

DROP TRIGGER IF EXISTS trg_trk_log_table_loans ON loans;
DROP TRIGGER IF EXISTS trg_trk_log_table_loan_items ON loan_items;
DROP TRIGGER IF EXISTS trg_trk_log_table_loan_status ON loan_status;
DROP TRIGGER IF EXISTS trg_trk_log_table_loan_rights ON loan_rights;

/*** darwin flat synchro triggers ***/

DROP TRIGGER IF EXISTS trg_update_expeditions_darwin_flat
  ON expeditions;

DROP TRIGGER IF EXISTS trg_update_collections_darwin_flat
        ON collections;

DROP TRIGGER IF EXISTS trg_update_gtu_darwin_flat
        ON gtu;

DROP TRIGGER IF EXISTS trg_update_tag_groups_darwin_flat
        ON tag_groups;

DROP TRIGGER IF EXISTS trg_update_people_darwin_flat
        ON people;

DROP TRIGGER IF EXISTS trg_update_users_darwin_flat
        ON users;

DROP TRIGGER IF EXISTS trg_update_igs_darwin_flat
        ON igs;

DROP TRIGGER IF EXISTS trg_update_taxonomy_darwin_flat
        ON taxonomy;

DROP TRIGGER IF EXISTS trg_update_chronostratigraphy_darwin_flat
        ON chronostratigraphy;

DROP TRIGGER IF EXISTS trg_update_lithostratigraphy_darwin_flat
        ON lithostratigraphy;

DROP TRIGGER IF EXISTS trg_update_lithology_darwin_flat
        ON lithology;

DROP TRIGGER IF EXISTS trg_update_mineralogy_darwin_flat
        ON mineralogy;

DROP TRIGGER IF EXISTS trg_update_specimens_darwin_flat
        ON specimens;

DROP TRIGGER IF EXISTS trg_cpy_fulltoindex_classification_keywords
        ON classification_keywords;

DROP TRIGGER IF EXISTS trg_cpy_fulltoindex_igs
        ON igs;

DROP TRIGGER IF EXISTS trg_clr_referencerecord_insurances
        ON insurances;

-- DROP TRIGGER IF EXISTS trg_cpy_updatecollectionadmin_collections
--         ON collections;

DROP TRIGGER IF EXISTS trg_chk_specimenCollectionAllowed
        ON specimens;

DROP TRIGGER IF EXISTS trg_chk_canUpdateCollectionsRights
        ON collections_rights;

DROP TRIGGER IF EXISTS trg_cpy_updateCollectionRights
        ON collections;

DROP TRIGGER IF EXISTS trg_cpy_updateUserRights
        ON collections_rights;

DROP TRIGGER IF EXISTS trg_cpy_updateUserRightsCollections
        ON collections;

DROP TRIGGER IF EXISTS trg_chk_parentCollInstitution
        ON collections;

DROP TRIGGER IF EXISTS trg_cpy_updateCollInstitutionCascade
        ON collections;

DROP TRIGGER IF EXISTS trg_cpy_updateMyWidgetsCollRights
        ON collections_rights;

DROP TRIGGER IF EXISTS trg_cpy_path_multimedia
        ON multimedia;

DROP TRIGGER IF EXISTS trg_cpy_path_collections
        ON collections;

DROP TRIGGER IF EXISTS trg_cpy_path_peoplerelationships
        ON people_relationships;

DROP TRIGGER IF EXISTS trg_cpy_path_gtu
        ON gtu;

DROP TRIGGER IF EXISTS trg_upd_fields_staging
        ON staging;

DROP TRIGGER IF EXISTS trg_cpy_unified_values
        ON properties_values;

DROP TRIGGER IF EXISTS trg_cpy_unified_values
        ON catalogue_properties;

DROP TRIGGER IF EXISTS trg_nbr_in_relation
        ON catalogue_relationships;

DROP TRIGGER IF EXISTS trg_nbr_in_synonym
        ON classification_synonymies;

DROP TRIGGER IF EXISTS trg_unpromotion_remove_cols
        ON users;

DROP TRIGGER IF EXISTS trg_upd_people_ref_staging_people ON staging_people;



/*************** CHECK of Referenced Records **************/

DROP TRIGGER IF EXISTS trg_chk_ref_record_catalogue_people ON catalogue_people;

DROP TRIGGER IF EXISTS trg_chk_ref_record_comments ON comments;

DROP TRIGGER IF EXISTS trg_chk_ref_record_ext_links ON ext_links;

DROP TRIGGER IF EXISTS trg_chk_ref_record_catalogue_properties ON catalogue_properties;

DROP TRIGGER IF EXISTS trg_chk_ref_record_identifications ON identifications;

DROP TRIGGER IF EXISTS trg_chk_ref_record_vernacular_names ON vernacular_names;

DROP TRIGGER IF EXISTS trg_chk_ref_record_informative_workflow ON informative_workflow;
DROP TRIGGER IF EXISTS trg_chk_is_last_informative_workflow ON informative_workflow;
DROP TRIGGER IF EXISTS trg_reset_last_flag_informative_workflow ON informative_workflow;

DROP TRIGGER IF EXISTS trg_chk_ref_record_collection_maintenance ON collection_maintenance;

DROP TRIGGER IF EXISTS trg_chk_ref_record_template_table_record_ref ON template_table_record_ref;

DROP TRIGGER IF EXISTS trg_chk_ref_record_classification_synonymies ON classification_synonymies;

DROP TRIGGER IF EXISTS trg_chk_ref_record_catalogue_codes ON codes;

DROP TRIGGER IF EXISTS trg_chk_ref_record_insurances ON insurances;

DROP TRIGGER IF EXISTS trg_chk_ref_record_relationship_catalogue_relationships ON catalogue_relationships;


/************* Possible upper level Check ***********/

DROP TRIGGER IF EXISTS trg_chk_possible_upper_level_chronostratigraphy ON chronostratigraphy;

DROP TRIGGER IF EXISTS trg_chk_possible_upper_level_lithostratigraphy ON lithostratigraphy;

DROP TRIGGER IF EXISTS trg_chk_possible_upper_level_mineralogy ON mineralogy;

DROP TRIGGER IF EXISTS trg_chk_possible_upper_level_lithology ON lithology;

DROP TRIGGER IF EXISTS trg_chk_possible_upper_level_taxonomy ON taxonomy;

/************ CHk Only One Lang ***************/

DROP TRIGGER IF EXISTS fct_chk_upper_level_for_childrens_people ON people_languages;


/************* Check If Institution is a Moral Person ***********/

DROP TRIGGER IF EXISTS fct_chk_PeopleIsMoral_collections ON collections;


/********************* DICT Add / remove  **************/

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_codes ON codes;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_catalogue_people ON catalogue_people;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_collection_maintenance ON collection_maintenance;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_identifications ON identifications;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_people ON people;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_people_addresses ON people_addresses;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_insurances ON insurances;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_mineralogy ON mineralogy;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_specimens ON specimens;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_users ON users;

DROP TRIGGER IF EXISTS fct_cpy_ins_update_dict_users_addresses ON users_addresses;

/******************* DELETE FROM DICT ******************/

DROP TRIGGER IF EXISTS fct_cpy_del_dict_codes ON codes ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_catalogue_people ON catalogue_people ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_collection_maintenance ON collection_maintenance ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_identifications ON identifications ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_people ON people ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_people_addresses ON people_addresses ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_insurances ON insurances ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_mineralogy ON mineralogy ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_specimens ON specimens ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_users ON users ;

DROP TRIGGER IF EXISTS fct_cpy_del_dict_users_addresses ON users_addresses ;


/**** LOANS ***/

DROP TRIGGER IF EXISTS trg_chk_is_last_loan_status ON loan_status;
DROP TRIGGER IF EXISTS trg_add_status_history ON loans;


DROP TRIGGER IF EXISTS trg_cpy_deleted_file ON multimedia;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_staging_info ON staging_info ;
DROP TRIGGER IF EXISTS trg_upd_institution_staging_relationship ON staging_relationship ;

/**** Imports ****/

DROP TRIGGER IF EXISTS trg_catalogue_import_keywords_update ON staging_catalogue;
DROP TRIGGER IF EXISTS trg_catalogue_import_keywords_update ON staging;