BEGIN;
set search_path=darwin2,public;

CREATE OR REPLACE FUNCTION check_auto_increment_code_in_spec() RETURNS trigger 
AS $$
DECLARE 
  col collections%ROWTYPE;
  number integer ;
BEGIN
  IF TG_OP != 'DELETE' THEN
    IF NEW.referenced_relation = 'specimens' THEN
      SELECT c.* INTO col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=NEW.record_id;
      IF FOUND THEN
        IF NEW.code_category = 'main' THEN
          IF isnumeric(NEW.code) THEN 
            number := NEW.code::integer ;
            IF number > col.code_last_value THEN
              UPDATE collections set code_last_value = number WHERE id=col.id ;
            END IF;
          ELSE
            UPDATE collections 
            SET code_last_value = (SELECT max(code_num)
                                   FROM codes inner join specimens
                                     ON codes.referenced_relation = 'specimens'
                                     AND codes.record_id = specimens.id
                                   WHERE codes.code_category = 'main'
                                     AND specimens.collection_ref = col.id
                                     AND codes.code_num IS NOT NULL
                                  )
            WHERE id = col.id
              AND EXISTS (SELECT 1
                          FROM codes inner join specimens
                            ON codes.referenced_relation = 'specimens'
                            AND codes.record_id = specimens.id
                          WHERE codes.code_category = 'main'
                            AND specimens.collection_ref = col.id
                            AND codes.code_num IS NOT NULL
                          LIMIT 1
                         );
            IF NOT FOUND THEN
              UPDATE collections
              SET code_last_value = DEFAULT
              WHERE id=col.id;
            END IF;
          END IF;
        ELSEIF TG_OP = 'UPDATE' THEN
          IF OLD.code_category = 'main' THEN
            IF isnumeric(OLD.code) THEN 
              number := OLD.code::integer ;
              IF number = col.code_last_value THEN
                UPDATE collections 
                SET code_last_value = (SELECT max(code_num)
                                       FROM codes inner join specimens
                                         ON codes.referenced_relation = 'specimens'
                                         AND codes.record_id = specimens.id
                                       WHERE codes.code_category = 'main'
                                         AND specimens.collection_ref = col.id
                                         AND codes.code_num IS NOT NULL
                                      )
                WHERE id = col.id
                  AND EXISTS (SELECT 1
                              FROM codes inner join specimens
                                ON codes.referenced_relation = 'specimens'
                                AND codes.record_id = specimens.id
                              WHERE codes.code_category = 'main'
                                AND specimens.collection_ref = col.id
                                AND codes.code_num IS NOT NULL
                              LIMIT 1
                             );
                IF NOT FOUND THEN
                  UPDATE collections
                  SET code_last_value = DEFAULT
                  WHERE id=col.id;
                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
      END IF;
    END IF ;
    RETURN NEW;
  ELSE
    IF OLD.referenced_relation = 'specimens' AND OLD.code_category = 'main' THEN
      SELECT c.* INTO col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=OLD.record_id; 
      IF FOUND AND isnumeric(OLD.code) THEN 
        UPDATE collections 
        SET code_last_value = (SELECT max(code_num)
                               FROM codes INNER JOIN specimens 
                                 ON  codes.referenced_relation = 'specimens'
                                 AND codes.record_id = specimens.id 
                               WHERE codes.code_category = 'main'
                                 AND specimens.collection_ref = col.id
                                 AND codes.code_num IS NOT NULL
                              )
        WHERE id=col.id
          AND EXISTS (SELECT 1
                      FROM codes inner join specimens
                        ON codes.referenced_relation = 'specimens'
                        AND codes.record_id = specimens.id
                      WHERE codes.code_category = 'main'
                        AND specimens.collection_ref = col.id
                        AND codes.code_num IS NOT NULL
                      LIMIT 1
                     );
        IF NOT FOUND THEN
          UPDATE collections
          SET code_last_value = DEFAULT
          WHERE id=col.id;
        END IF;
      END IF;
    END IF ;
    RETURN OLD;
  END IF;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_insert_auto_code 
    ON codes;    

CREATE TRIGGER trg_insert_auto_code AFTER INSERT OR UPDATE OR DELETE ON codes FOR EACH ROW
EXECUTE PROCEDURE check_auto_increment_code_in_spec() ;

CREATE OR REPLACE FUNCTION update_collections_code_last_val() RETURNS trigger 
AS $$
BEGIN
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = NEW.collection_ref
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = NEW.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = NEW.collection_ref
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = OLD.collection_ref
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = OLD.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = OLD.collection_ref
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  IF NOT FOUND THEN
    UPDATE collections
    SET code_last_value = DEFAULT
    WHERE id = OLD.collection_ref;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_update_collections_code_last_val
    ON specimens;

CREATE TRIGGER trg_update_collections_code_last_val AFTER UPDATE OF collection_ref ON specimens FOR EACH ROW
EXECUTE PROCEDURE update_collections_code_last_val();


ALTER TABLE collections ADD COLUMN code_auto_increment_for_insert_only BOOLEAN NOT NULL DEFAULT TRUE;
COMMENT ON COLUMN collections.code_auto_increment_for_insert_only IS 'Flag telling if the autoincremented code insertion has to be done only after insertion of specimens or also after updates of specimens';

UPDATE collections SET code_auto_increment_for_insert_only = DEFAULT;

CREATE OR REPLACE FUNCTION fct_after_save_add_code(IN collectionId collections.id%TYPE, IN specimenId specimens.id%TYPE) RETURNS integer
AS $$
DECLARE
  col collections%ROWTYPE;
BEGIN
  SELECT c.* INTO col FROM collections c WHERE c.id = collectionId;
  IF FOUND THEN
    IF col.code_auto_increment = TRUE THEN
      INSERT INTO codes (referenced_relation, record_id, code_prefix, code_prefix_separator, code, code_suffix_separator, code_suffix)
      SELECT 'specimens', specimenId, col.code_prefix, col.code_prefix_separator, (col.code_last_value+1)::varchar, col.code_suffix_separator, col.code_suffix
      WHERE NOT EXISTS (SELECT 1 
                        FROM codes 
                        WHERE referenced_relation = 'specimens'
                          AND record_id = specimenId
                          AND code_category = 'main'
                          AND code_num IS NOT NULL
                        LIMIT 1
                       );
    END IF;
  END IF;
  RETURN 0;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION update_collections_code_last_val_after_spec_del() RETURNS trigger 
AS $$
BEGIN
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = OLD.collection_ref
                           AND specimens.id != OLD.id
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = OLD.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = OLD.collection_ref
                  AND specimens.id != OLD.id
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  IF NOT FOUND THEN
    UPDATE collections
    SET code_last_value = DEFAULT
    WHERE id = OLD.collection_ref;
  END IF;
  RETURN NULL;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_update_collections_code_last_val_after_spec_del
    ON specimens;

CREATE TRIGGER trg_update_collections_code_last_val_after_spec_del AFTER DELETE ON specimens FOR EACH ROW
EXECUTE PROCEDURE update_collections_code_last_val_after_spec_del();

COMMIT ;