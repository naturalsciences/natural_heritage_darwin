begin;
set search_path=darwin2,public;

CREATE OR REPLACE FUNCTION fct_cpy_fullToIndex() RETURNS trigger
AS $$
DECLARE
  codeNum varchar;
BEGIN
        IF TG_TABLE_NAME = 'properties' THEN
                NEW.applies_to_indexed := COALESCE(fullToIndex(NEW.applies_to),'');
                NEW.method_indexed := COALESCE(fullToIndex(NEW.method),'');
        ELSIF TG_TABLE_NAME = 'chronostratigraphy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'collections' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'expeditions' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'bibliography' THEN
                NEW.title_indexed := fullToIndex(NEW.title);
        ELSIF TG_TABLE_NAME = 'identifications' THEN
                NEW.value_defined_indexed := COALESCE(fullToIndex(NEW.value_defined),'');
        ELSIF TG_TABLE_NAME = 'lithology' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'mineralogy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
                NEW.formule_indexed := fullToIndex(NEW.formule);
        ELSIF TG_TABLE_NAME = 'people' THEN
                NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.name_formated_indexed := fulltoindex(coalesce(NEW.given_name,'') || coalesce(NEW.family_name,''));
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
        ELSIF TG_TABLE_NAME = 'codes' THEN
                codeNum := coalesce(trim(regexp_replace(NEW.code, '[^0-9]','','g')), '');
                IF codeNum = '' THEN
                  NEW.code_num := 0;
                ELSE
                  NEW.code_num := codeNum::int;
                END IF;
                NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
        ELSIF TG_TABLE_NAME = 'tag_groups' THEN
                NEW.group_name_indexed := fullToIndex(NEW.group_name);
                NEW.sub_group_name_indexed := fullToIndex(NEW.sub_group_name);
        ELSIF TG_TABLE_NAME = 'taxonomy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'classification_keywords' THEN
                NEW.keyword_indexed := fullToIndex(NEW.keyword);
        ELSIF TG_TABLE_NAME = 'users' THEN
                NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
        ELSIF TG_TABLE_NAME = 'vernacular_names' THEN
                NEW.community_indexed := fullToIndex(NEW.community);
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'igs' THEN
                NEW.ig_num_indexed := fullToIndex(NEW.ig_num);
        ELSIF TG_TABLE_NAME = 'collecting_methods' THEN
                NEW.method_indexed := fullToIndex(NEW.method);
        ELSIF TG_TABLE_NAME = 'collecting_tools' THEN
                NEW.tool_indexed := fullToIndex(NEW.tool);
        ELSIF TG_TABLE_NAME = 'loans' THEN
                NEW.search_indexed := fullToIndex(COALESCE(NEW.name,'') || COALESCE(NEW.description,''));
        ELSIF TG_TABLE_NAME = 'multimedia' THEN
                NEW.search_indexed := fullToIndex ( COALESCE(NEW.title,'') ||  COALESCE(NEW.description,'') || COALESCE(NEW.extracted_info,'') ) ;
        ELSIF TG_TABLE_NAME = 'comments' THEN
                NEW.comment_indexed := fullToIndex(NEW.comment);
        ELSIF TG_TABLE_NAME = 'ext_links' THEN
                NEW.comment_indexed := fullToIndex(NEW.comment);
        ELSIF TG_TABLE_NAME = 'specimens' THEN
                NEW.object_name_indexed := fullToIndex(COALESCE(NEW.object_name,'') );
        END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_clear_referencedRecord() RETURNS TRIGGER
AS $$
BEGIN
  IF TG_OP ='UPDATE' THEN
    IF NEW.id != OLD.id THEN
      UPDATE template_table_record_ref SET record_id = NEW.id WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
    END IF;
  ELSEIF TG_OP = 'DELETE' THEN
    DELETE FROM template_table_record_ref where referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
  END IF;
  RETURN NULL;
 END;
$$ LANGUAGE plpgsql;

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

CREATE TRIGGER trg_clr_referenceRecord_specimens_relationships AFTER DELETE OR UPDATE
    ON specimens_relationships FOR EACH ROW
    EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_loans AFTER DELETE OR UPDATE
        ON loans FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_loan_items AFTER DELETE OR UPDATE
        ON loan_items FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

commit;
