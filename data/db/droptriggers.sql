DROP TRIGGER trg_cpy_specimensMainCode_specimenPartCode ON specimen_parts;
DROP TRIGGER trg_cpy_idToCode_gtu ON gtu ;

DROP TRIGGER trg_cpy_fullToIndex_lithology ON lithology ;
DROP TRIGGER trg_cpy_fullToIndex_catalogueproperties ON catalogue_properties ;
DROP TRIGGER trg_cpy_fullToIndex_chronostratigraphy ON chronostratigraphy ;
DROP TRIGGER trg_cpy_fullToIndex_expeditions ON expeditions ;
DROP TRIGGER trg_cpy_fullToIndex_identifications ON identifications;
DROP TRIGGER trg_cpy_fullToIndex_lithostratigraphy ON lithostratigraphy;
DROP TRIGGER trg_cpy_fullToIndex_mineralogy ON mineralogy;
DROP TRIGGER trg_cpy_fullToIndex_multimedia ON multimedia;
DROP TRIGGER trg_cpy_fullToIndex_codes ON codes ;
DROP TRIGGER trg_cpy_fullToIndex_multimediakeywords ON multimedia_keywords;
DROP TRIGGER trg_cpy_fullToIndex_taggroups ON tag_groups;
DROP TRIGGER trg_cpy_fullToIndex_taxa ON taxonomy;
DROP TRIGGER trg_cpy_fullToIndex_collection ON collections;
DROP TRIGGER trg_cpy_fullToIndex_classvernacularnames ON class_vernacular_names;
DROP TRIGGER trg_cpy_fullToIndex_vernacularnames ON vernacular_names;
DROP TRIGGER trg_cpy_fullToIndex_collecting_tools ON collecting_tools;
DROP TRIGGER trg_cpy_fullToIndex_collecting_methods ON collecting_methods;
DROP TRIGGER trg_clr_specialstatus_specimenindividuals ON specimen_individuals;

/*** REPERCUTION record_id ****/
DROP TRIGGER trg_clr_referenceRecord_cataloguerelationships ON catalogue_relationships;
DROP TRIGGER trg_clr_referenceRecord_cataloguepeople ON catalogue_people;
DROP TRIGGER trg_clr_referenceRecord_gtu ON gtu;
DROP TRIGGER trg_clr_referenceRecord_staging ON staging;
DROP TRIGGER trg_clr_referenceRecord_catalogueproperties ON catalogue_properties;
DROP TRIGGER trg_clr_referenceRecord_identifications ON identifications;
--DROP TRIGGER trg_clr_referenceRecord_expertises ON expertises;
DROP TRIGGER trg_clr_referenceRecord_vernacularnames ON vernacular_names;
DROP TRIGGER trg_clr_referenceRecord_expeditions ON expeditions;
DROP TRIGGER trg_clr_referenceRecord_people ON people;
DROP TRIGGER trg_clr_referenceRecord_users ON users;
DROP TRIGGER trg_clr_referenceRecord_multimedia ON multimedia;
DROP TRIGGER trg_clr_referenceRecord_collections ON collections;
/*DROP TRIGGER trg_clr_referenceRecord_userscollrightsasked ON users_coll_rights_asked;*/
DROP TRIGGER trg_clr_referenceRecord_mysavedsearches ON collection_maintenance;
DROP TRIGGER trg_clr_referenceRecord_taxa ON taxonomy;
DROP TRIGGER trg_clr_referenceRecord_chronostratigraphy ON chronostratigraphy;
DROP TRIGGER trg_clr_referenceRecord_lithostratigraphy ON lithostratigraphy;
DROP TRIGGER trg_clr_referenceRecord_mineralogy ON mineralogy;
DROP TRIGGER trg_clr_referenceRecord_lithology ON lithology;
DROP TRIGGER trg_clr_referenceRecord_habitats ON habitats;
DROP TRIGGER trg_clr_referenceRecord_soortenregister ON soortenregister;
DROP TRIGGER trg_clr_referenceRecord_specimens ON specimens;
DROP TRIGGER trg_clr_referenceRecord_specimenindividuals ON specimen_individuals;
DROP TRIGGER trg_clr_referenceRecord_specimenparts ON specimen_parts;
DROP TRIGGER trg_clr_referenceRecord_specimensaccompanying ON specimens_accompanying;

--DROP TRIGGER trg_clear_referencedPeople ON people;

DROP TRIGGER trg_cpy_toFullText_comments ON comments;
DROP TRIGGER trg_cpy_toFullText_identifications ON identifications;
DROP TRIGGER trg_cpy_toFullText_peopleaddresses ON people_addresses;
DROP TRIGGER trg_cpy_toFullText_usersaddresses ON users_addresses;
DROP TRIGGER trg_cpy_toFullText_multimedia ON multimedia;
DROP TRIGGER trg_cpy_toFullText_collectionmaintenance ON collection_maintenance;
DROP TRIGGER trg_cpy_toFullText_expeditions ON expeditions;
DROP TRIGGER trg_cpy_toFullText_habitats ON habitats;
DROP TRIGGER trg_cpy_toFullText_vernacularnames ON vernacular_names;

/*DROP TRIGGER trg_cas_userType_users ON users;*/
DROP TRIGGER trg_chk_peopleType ON people;
DROP TRIGGER trg_chk_AreRole ON catalogue_people;

DROP TRIGGER trg_cpy_FormattedName ON people;
DROP TRIGGER trg_cpy_FormattedName ON users;

DROP TRIGGER trg_cpy_gtuTags_TagGroups ON tag_groups;

/*** Specimen hosting ***/
DROP TRIGGER trg_cpy_updateHosts ON specimens;
DROP TRIGGER trg_cpy_updateSpecHostImpact ON specimens;

/*** Words triggers ***/
DROP TRIGGER trg_words_ts_cpy_collection_maintenance
	ON collection_maintenance;

DROP TRIGGER trg_words_ts_cpy_comments
	ON comments;

DROP TRIGGER trg_words_ts_cpy_vernacular_names
	ON vernacular_names;

DROP TRIGGER trg_words_ts_cpy_identification
	ON identifications;

DROP TRIGGER trg_words_ts_cpy_multimedia
	ON multimedia;

DROP TRIGGER trg_words_ts_cpy_people
	ON people;

DROP TRIGGER trg_words_ts_cpy_people_addresses
	ON people_addresses;

DROP TRIGGER trg_words_ts_cpy_users
	ON users;

DROP TRIGGER trg_words_ts_cpy_expeditions
	ON expeditions;

DROP TRIGGER trg_words_ts_cpy_habitats
	ON habitats;

DROP TRIGGER trg_words_ts_cpy_mineralogy
	ON mineralogy;

DROP TRIGGER trg_words_ts_cpy_chronostratigraphy
	ON chronostratigraphy;

DROP TRIGGER trg_words_ts_cpy_lithostratigraphy
	ON lithostratigraphy;

DROP TRIGGER trg_words_ts_cpy_lithology
	ON lithology;

DROP TRIGGER trg_words_ts_cpy_taxonomy
	ON taxonomy;

DROP TRIGGER trg_words_ts_cpy_codes
	ON codes;

/*** Tracking ***/
DROP TRIGGER trg_trk_log_table_catalogue_relationships 
        ON catalogue_relationships;

DROP TRIGGER trg_trk_log_table_classification_keywords 
        ON classification_keywords;

DROP TRIGGER trg_trk_log_table_classification_synonymies 
        ON classification_synonymies;

DROP TRIGGER trg_trk_log_table_catalogue_properties 
        ON catalogue_properties;

DROP TRIGGER trg_trk_log_table_properties_values 
        ON properties_values;

DROP TRIGGER trg_trk_log_table_identifications 
        ON identifications;

DROP TRIGGER trg_trk_log_table_class_vernacular_names 
        ON class_vernacular_names;

DROP TRIGGER trg_trk_log_table_vernacular_names 
        ON vernacular_names;

DROP TRIGGER trg_trk_log_table_people_relationships 
        ON people_relationships;

DROP TRIGGER trg_trk_log_table_people_comm 
        ON people_comm;

DROP TRIGGER trg_trk_log_table_people_addresses 
        ON people_addresses;

DROP TRIGGER trg_trk_log_table_people_multimedia 
        ON people_multimedia;

DROP TRIGGER trg_trk_log_table_collections_rights 
        ON collections_rights;

DROP TRIGGER trg_trk_log_table_specimens_accompanying 
        ON specimens_accompanying;

DROP TRIGGER trg_trk_log_table_collecting_tools 
        ON collecting_tools;

DROP TRIGGER trg_trk_log_table_specimen_collecting_tools 
        ON specimen_collecting_tools;

DROP TRIGGER trg_trk_log_table_collecting_methods 
        ON collecting_methods;

DROP TRIGGER trg_trk_log_table_specimen_collecting_methods 
        ON specimen_collecting_methods;

DROP TRIGGER trg_trk_log_table_comments 
        ON comments;

DROP TRIGGER trg_trk_log_table_ext_links 
        ON ext_links;

DROP TRIGGER trg_trk_log_table_gtu 
        ON gtu;

DROP TRIGGER trg_trk_log_table_tag_groups 
        ON tag_groups;

DROP TRIGGER trg_trk_log_table_expeditions 
        ON expeditions;

DROP TRIGGER trg_trk_log_table_multimedia 
        ON multimedia;

DROP TRIGGER trg_trk_log_table_collections 
        ON collections;

DROP TRIGGER trg_trk_log_table_collection_maintenance 
        ON collection_maintenance;

DROP TRIGGER trg_trk_log_table_soortenregister 
        ON soortenregister;

DROP TRIGGER trg_trk_log_table_igs 
        ON igs;

DROP TRIGGER trg_trk_log_table_codes 
        ON codes;

DROP TRIGGER trg_trk_log_table_insurances 
        ON insurances;

DROP TRIGGER trg_trk_log_table_specimens 
        ON specimens;

DROP TRIGGER trg_trk_log_table_specimen_individuals 
        ON specimen_individuals;

DROP TRIGGER trg_trk_log_table_specimen_parts 
        ON specimen_parts;

DROP TRIGGER trg_trk_log_table_taxonomy 
        ON taxonomy;

DROP TRIGGER trg_trk_log_table_chronostratigraphy 
        ON chronostratigraphy;

DROP TRIGGER trg_trk_log_table_lithostratigraphy 
        ON lithostratigraphy;

DROP TRIGGER trg_trk_log_table_mineralogy 
        ON mineralogy;

DROP TRIGGER trg_trk_log_table_lithology 
        ON lithology;

DROP TRIGGER trg_trk_log_table_habitats 
        ON habitats;

DROP TRIGGER trg_trk_log_table_people 
        ON people;


/*** darwin flat synchro triggers ***/

DROP TRIGGER trg_update_expeditions_darwin_flat
  ON expeditions;

DROP TRIGGER trg_update_collections_darwin_flat
        ON collections;

DROP TRIGGER trg_update_gtu_darwin_flat
        ON gtu;

DROP TRIGGER trg_update_tag_groups_darwin_flat
        ON tag_groups;

DROP TRIGGER trg_update_people_darwin_flat
        ON people;

DROP TRIGGER trg_update_users_darwin_flat
        ON users;

DROP TRIGGER trg_update_igs_darwin_flat
        ON igs;

DROP TRIGGER trg_update_taxonomy_darwin_flat
        ON taxonomy;

DROP TRIGGER trg_update_chronostratigraphy_darwin_flat
        ON chronostratigraphy;

DROP TRIGGER trg_update_lithostratigraphy_darwin_flat
        ON lithostratigraphy;

DROP TRIGGER trg_update_lithology_darwin_flat
        ON lithology;

DROP TRIGGER trg_update_mineralogy_darwin_flat
        ON mineralogy;

DROP TRIGGER trg_update_specimens_darwin_flat
        ON specimens;

DROP TRIGGER trg_delete_specimen_individuals_darwin_flat
        ON specimen_individuals;

DROP TRIGGER trg_delete_specimen_parts_darwin_flat
        ON specimen_parts;

DROP TRIGGER trg_update_specimen_individuals_darwin_flat
        ON specimen_individuals;

DROP TRIGGER trg_update_specimen_parts_darwin_flat
        ON specimen_parts;

DROP TRIGGER trg_cpy_fulltoindex_expeditions
        ON habitats;

DROP TRIGGER trg_cpy_fulltoindex_classification_keywords
        ON classification_keywords;

DROP TRIGGER trg_cpy_fulltoindex_igs
        ON igs;

DROP TRIGGER trg_clr_referencerecord_insurances
        ON insurances;

-- DROP TRIGGER trg_cpy_updatecollectionadmin_collections
--         ON collections;

DROP TRIGGER trg_chk_specimenCollectionAllowed
        ON specimens;

DROP TRIGGER trg_chk_specimenIndCollectionAllowed
        ON specimen_individuals;

DROP TRIGGER trg_chk_specimenPartCollectionAllowed
        ON specimen_parts;

DROP TRIGGER trg_chk_canUpdateCollectionsRights
        ON collections_rights;

DROP TRIGGER trg_cpy_updateCollectionRights
        ON collections;

DROP TRIGGER trg_cpy_updateUserRights
        ON collections_rights;

DROP TRIGGER trg_cpy_updateUserRightsCollections
        ON collections;

DROP TRIGGER trg_chk_parentCollInstitution
        ON collections;

DROP TRIGGER trg_cpy_updateCollInstitutionCascade
        ON collections;

DROP TRIGGER trg_cpy_updateMyWidgetsCollRights
        ON collections_rights;

DROP TRIGGER trg_cpy_path_multimedia
        ON multimedia;

DROP TRIGGER trg_cpy_path_collections
        ON collections;

DROP TRIGGER trg_cpy_path_peoplerelationships
        ON people_relationships;

DROP TRIGGER trg_cpy_path_gtu
        ON gtu;

DROP TRIGGER trg_cpy_path_specimen_parts
        ON specimen_parts;

DROP TRIGGER trg_cpy_path_habitats
        ON habitats;

DROP TRIGGER trg_cpy_path_staging
        ON staging;
        
DROP TRIGGER trg_upd_fields_staging
        ON staging;

DROP TRIGGER trg_cpy_unified_values
        ON properties_values;

DROP TRIGGER trg_cpy_unified_values
        ON catalogue_properties;

DROP TRIGGER trg_nbr_in_relation
        ON catalogue_relationships;

DROP TRIGGER trg_nbr_in_synonym
        ON classification_synonymies;

DROP TRIGGER trg_unpromotion_remove_cols
        ON users;

DROP TRIGGER trg_upd_people_ref_staging_people ON staging_people;



/*************** CHECK of Referenced Records **************/

DROP TRIGGER trg_chk_ref_record_catalogue_people ON catalogue_people;

DROP TRIGGER trg_chk_ref_record_comments ON comments;

DROP TRIGGER trg_chk_ref_record_ext_links ON ext_links;

DROP TRIGGER trg_chk_ref_record_catalogue_properties ON catalogue_properties;

DROP TRIGGER trg_chk_ref_record_identifications ON identifications;

DROP TRIGGER trg_chk_ref_record_class_vernacular_names ON class_vernacular_names;

DROP TRIGGER trg_chk_ref_record_users_workflow ON users_workflow;

DROP TRIGGER trg_chk_ref_record_collection_maintenance ON collection_maintenance;

DROP TRIGGER trg_chk_ref_record_template_table_record_ref ON template_table_record_ref;

DROP TRIGGER trg_chk_ref_record_classification_synonymies ON classification_synonymies;

DROP TRIGGER trg_chk_ref_record_catalogue_codes ON codes;

DROP TRIGGER trg_chk_ref_record_associated_multimedia ON associated_multimedia;

DROP TRIGGER trg_chk_ref_record_insurances ON insurances;

DROP TRIGGER trg_chk_ref_record_relationship_catalogue_relationships ON catalogue_relationships;


/************* Possible upper level Check ***********/

DROP TRIGGER trg_chk_possible_upper_level_chronostratigraphy ON chronostratigraphy;

DROP TRIGGER trg_chk_possible_upper_level_lithostratigraphy ON lithostratigraphy;

DROP TRIGGER trg_chk_possible_upper_level_mineralogy ON mineralogy;

DROP TRIGGER trg_chk_possible_upper_level_lithology ON lithology;

DROP TRIGGER trg_chk_possible_upper_level_taxonomy ON taxonomy;

/************ CHk Only One Lang ***************/

DROP TRIGGER fct_chk_upper_level_for_childrens_people ON people_languages;

DROP TRIGGER fct_chk_upper_level_for_childrens_users ON users_languages;


/************* Check If Institution is a Moral Person ***********/

DROP TRIGGER fct_chk_PeopleIsMoral_collections ON collections;


/********************* DICT Add / remove  **************/

CREATE TRIGGER fct_cpy_ins_update_dict_codes AFTER INSERT OR UPDATE
        ON codes FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_catalogue_people AFTER INSERT OR UPDATE
        ON catalogue_people FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_collection_maintenance AFTER INSERT OR UPDATE
        ON collection_maintenance FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_identifications AFTER INSERT OR UPDATE
        ON identifications FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_people AFTER INSERT OR UPDATE
        ON people FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_people_addresses AFTER INSERT OR UPDATE
        ON people_addresses FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_insurances AFTER INSERT OR UPDATE
        ON insurances FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_mineralogy AFTER INSERT OR UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_specimen_individuals AFTER INSERT OR UPDATE
        ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_specimens AFTER INSERT OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_specimens_accompanying AFTER INSERT OR UPDATE
        ON specimens_accompanying FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_users AFTER INSERT OR UPDATE
        ON users FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_users_addresses AFTER INSERT OR UPDATE
        ON users_addresses FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

CREATE TRIGGER fct_cpy_ins_update_dict_specimen_parts AFTER INSERT OR UPDATE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE ins_update_dict();

/******************* DELETE FROM DICT ******************/

CREATE TRIGGER fct_cpy_del_dict_codes AFTER INSERT OR UPDATE
        ON codes FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_catalogue_people AFTER INSERT OR UPDATE
        ON catalogue_people FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_collection_maintenance AFTER INSERT OR UPDATE
        ON collection_maintenance FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_identifications AFTER INSERT OR UPDATE
        ON identifications FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_people AFTER INSERT OR UPDATE
        ON people FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_people_addresses AFTER INSERT OR UPDATE
        ON people_addresses FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_insurances AFTER INSERT OR UPDATE
        ON insurances FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_mineralogy AFTER INSERT OR UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_specimen_individuals AFTER INSERT OR UPDATE
        ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_specimens AFTER INSERT OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_specimens_accompanying AFTER INSERT OR UPDATE
        ON specimens_accompanying FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_users AFTER INSERT OR UPDATE
        ON users FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_users_addresses AFTER INSERT OR UPDATE
        ON users_addresses FOR EACH ROW
        EXECUTE PROCEDURE del_dict();

CREATE TRIGGER fct_cpy_del_dict_specimen_parts AFTER INSERT OR UPDATE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE del_dict();
