begin;
set search_path=darwin2,public;

SET SESSION session_replication_role = replica;

UPDATE template_table_record_ref r set referenced_relation = 'old_multimedia' where referenced_relation='multimedia' and not exists (select 1 from multimedia t where t.id = r.record_id);
SET SESSION session_replication_role = origin;


delete from template_table_record_ref r where referenced_relation = 'staging' AND not exists (select 1 from staging t where t.id = r.record_id);

 
CREATE OR REPLACE FUNCTION fct_clear_referencedRecord() RETURNS TRIGGER
AS $$
BEGIN
  IF TG_OP ='UPDATE' THEN
    IF NEW.id != OLD.id THEN
      UPDATE template_table_record_ref SET record_id = NEW.id WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
    END IF;
  ELSE
    DELETE FROM template_table_record_ref where referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
  END IF;
   RETURN OLD;
 END;
 $$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_clr_referenceRecord_staging on staging;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_gtu on gtu;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_identifications on identifications;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_insurances on insurances;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_vernacularnames on vernacular_names;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_expeditions on expeditions;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_people on people;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_users on users;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_multimedia on multimedia;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_collections on collections;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_bibliography on bibliography;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_mysavedsearches on collection_maintenance;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_taxa on taxonomy;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_chronostratigraphy on chronostratigraphy;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_lithostratigraphy on lithostratigraphy;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_mineralogy on mineralogy;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_lithology on lithology;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_specimens on specimens;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_specimenindividuals on specimen_individuals;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_specimenparts on specimen_parts;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_specimensaccompanying on specimens_accompanying;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_loans on loans;
DROP TRIGGER IF EXISTS trg_clr_referenceRecord_loan_items on loan_items;


CREATE TRIGGER trg_clr_referenceRecord_staging AFTER DELETE OR UPDATE
        ON staging FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_gtu AFTER DELETE OR UPDATE
        ON gtu FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_identifications AFTER DELETE OR UPDATE
        ON identifications FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_insurances AFTER DELETE OR UPDATE
        ON insurances FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_vernacularnames AFTER DELETE OR UPDATE
        ON vernacular_names FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_expeditions AFTER DELETE OR UPDATE
        ON expeditions FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_people AFTER DELETE OR UPDATE
        ON people FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_users AFTER DELETE OR UPDATE
        ON users FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_multimedia AFTER DELETE OR UPDATE
        ON multimedia FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_igs AFTER DELETE OR UPDATE
        ON igs FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_collections AFTER DELETE OR UPDATE
        ON collections FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_bibliography AFTER DELETE OR UPDATE
        ON bibliography FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

/*CREATE TRIGGER trg_clr_referenceRecord_userscollrightsasked AFTER DELETE OR UPDATE
        ON users_coll_rights_asked FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();
*/
CREATE TRIGGER trg_clr_referenceRecord_collectionmaintenance AFTER DELETE OR UPDATE
        ON collection_maintenance FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_taxa AFTER DELETE OR UPDATE
        ON taxonomy FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_chronostratigraphy AFTER DELETE OR UPDATE
        ON chronostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_lithostratigraphy AFTER DELETE OR UPDATE
        ON lithostratigraphy FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_mineralogy AFTER DELETE OR UPDATE
        ON mineralogy FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_lithology AFTER DELETE OR UPDATE
        ON lithology FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_specimens AFTER DELETE OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_specimenindividuals AFTER DELETE OR UPDATE
        ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_specimenparts AFTER DELETE OR UPDATE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_specimensaccompanying AFTER DELETE OR UPDATE
        ON specimens_accompanying FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_loans AFTER DELETE OR UPDATE
        ON loans FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_loan_items AFTER DELETE OR UPDATE
        ON loan_items FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

