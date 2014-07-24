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




COMMIT ;