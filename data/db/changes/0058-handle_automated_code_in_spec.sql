begin;
set search_path=darwin2,public;

CREATE OR REPLACE FUNCTION check_auto_increment_code_in_spec() RETURNS trigger 
AS $$
DECLARE 
  col collections;
  code RECORD;
  number integer ;
BEGIN
code = NEW ;
  IF code.referenced_relation = 'specimens' THEN
    SELECT c.* INTO col FROM collections c JOIN specimens s ON s.collection_ref=c.id WHERE s.id=code.record_id;  
    IF col.code_auto_increment = TRUE AND isnumeric(code.code) THEN 
      number := code.code::integer ;
      IF number > col.code_last_value THEN
        UPDATE collections set code_last_value = number WHERE id=col.id ;
      END IF;
    END IF;
  END IF ;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION isnumeric(text) RETURNS BOOLEAN AS $$
DECLARE x NUMERIC;
BEGIN
    x = $1::NUMERIC;
    RETURN TRUE;
EXCEPTION WHEN others THEN
    RETURN FALSE;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE TRIGGER trg_insert_auto_code BEFORE INSERT ON codes FOR EACH ROW
EXECUTE PROCEDURE check_auto_increment_code_in_spec() ;

COMMIT;
