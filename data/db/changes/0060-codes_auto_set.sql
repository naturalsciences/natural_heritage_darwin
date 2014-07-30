BEGIN;
set search_path=darwin2,public;

DROP TRIGGER IF EXISTS trg_insert_auto_code 
    ON codes;    

CREATE OR REPLACE FUNCTION check_auto_increment_code_in_spec() RETURNS trigger 
AS $$
DECLARE 
  col collections;
  code RECORD;
  number integer ;
BEGIN
  code = NEW ;
  IF code.referenced_relation = 'specimens' THEN
    SELECT c.* INTO STRICT col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=code.record_id;  
    IF isnumeric(code.code) THEN 
      number := code.code::integer ;
      IF number > col.code_last_value THEN
        UPDATE collections set code_last_value = number WHERE id=col.id ;
      END IF;
    END IF;
  END IF ;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_insert_auto_code AFTER INSERT ON codes FOR EACH ROW
EXECUTE PROCEDURE check_auto_increment_code_in_spec() ;


ALTER TABLE collections ADD COLUMN code_auto_increment_for_insert_only BOOLEAN NOT NULL DEFAULT TRUE;
COMMENT ON COLUMN collections.code_auto_increment_for_insert_only IS 'Flag telling if the autoincremented code insertion has to be done only after insertion of specimens or also after updates of specimens';
ALTER TABLE collections ADD COLUMN code_auto_increment_even_if_existing_numeric BOOLEAN NOT NULL DEFAULT FALSE;
COMMENT ON COLUMN collections.code_auto_increment_even_if_existing_numeric IS 'Flag telling if the autoincremented code insertion has to be done even if numeric codes have already been inserted in the list of specimen codes - default to false';

UPDATE collections SET code_auto_increment_for_insert_only = DEFAULT, code_auto_increment_even_if_existing_numeric = DEFAULT;

CREATE OR REPLACE fct_after_save_add_code(IN collectionId collections.id%TYPE, IN specimenId specimens.id%TYPE) RETURNS integer
AS $$
DECLARE
  col collections%ROWTYPE;
BEGIN
  SELECT c.* INTO STRICT col FROM collections c WHERE c.id = collectionId;
  IF FOUND THEN
    IF col.code_auto_increment = TRUE THEN
      IF col.code_auto_increment_even_if_existing_numeric = FALSE THEN
        INSERT INTO codes (referenced_relation, record_id, code_prefix, code_prefix_separator, code, code_suffix_separator, code_suffix)
        SELECT 'specimens', specimenId, col.code_prefix, col.code_prefix_separator, col.code_last_value::varchar, col.code_suffix_separator, col.code_suffix
        WHERE NOT EXISTS (SELECT 1 
                          FROM codes 
                          WHERE referenced_relation = 'specimens'
                            AND record_id = specimenId
                            AND code_category = 'main'
                            AND code_num != 0
                         );
      ELSE
        INSERT INTO codes (referenced_relation, record_id, code_prefix, code_prefix_separator, code, code_suffix_separator, code_suffix)
        SELECT 'specimens', specimenId, col.code_prefix, col.code_prefix_separator, col.code_last_value::varchar, col.code_suffix_separator, col.code_suffix
        WHERE NOT EXISTS (SELECT 1 
                          FROM codes 
                          WHERE referenced_relation = 'specimens'
                            AND record_id = specimenId
                            AND code_category = 'main'
                            AND code_prefix = col.code_prefix
                            AND code = col.code_last_value::varchar
                            AND code_suffix = col.code_suffix
                         );
      END IF;
    END IF;
  END IF;
  RETURN 0;
END;
$$ LANGUAGE plpgsql;

COMMIT ;