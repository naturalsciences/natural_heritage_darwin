\i createfunctions.sql
ALTER TABLE people_languages DROP CONSTRAINT chk_chk_people_languages_preferred_one;
ALTER TABLE users_languages DROP CONSTRAINT chk_chk_users_languages_preferred_one;
ALTER TABLE collections DROP CONSTRAINT chk_chk_InstitutionIsMoral;
ALTER TABLE chronostratigraphy DROP CONSTRAINT chk_chk_possible_upper_level_chronostratigraphy;
ALTER TABLE lithostratigraphy DROP CONSTRAINT chk_chk_possible_upper_level_lithostratigraphy;
ALTER TABLE mineralogy DROP CONSTRAINT chk_chk_possible_upper_level_mineralogy;
ALTER TABLE lithology DROP CONSTRAINT chk_chk_possible_upper_level_lithology;
ALTER TABLE taxonomy DROP CONSTRAINT chk_chk_possible_upper_level_taxa;
ALTER TABLE template_table_record_ref DROP CONSTRAINT fct_chk_ReferencedRecord_template_table_record_ref;
ALTER TABLE catalogue_relationships DROP CONSTRAINT fct_chk_ReferencedRecord_catalogue_relationships_rec1;
ALTER TABLE catalogue_relationships DROP CONSTRAINT fct_chk_ReferencedRecord_catalogue_relationships_rec2;

DROP FUNCTION fct_chk_one_pref_language(id people_languages.id%TYPE, person people_languages.people_ref%TYPE, preferred people_languages.preferred_language%TYPE, table_prefix varchar);
DROP FUNCTION fct_chk_one_pref_language(id people_languages.id%TYPE, person people_languages.people_ref%TYPE, preferred people_languages.preferred_language%TYPE);
DROP FUNCTION fct_chk_PeopleIsMoral(people_ref people.id%TYPE);
DROP FUNCTION fct_chk_ReferencedRecord(referenced_relation varchar,record_id integer);


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

CREATE TRIGGER trg_chk_ref_record_users_workflow AFTER INSERT OR UPDATE
        ON users_workflow FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

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
