SET search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION check_auto_increment_code_in_spec()
  RETURNS trigger AS
  $BODY$
DECLARE
  col collections%ROWTYPE;
  number BIGINT ;
BEGIN
  IF TG_OP != 'DELETE' THEN
    IF NEW.referenced_relation = 'specimens' THEN
      SELECT c.* INTO col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=NEW.record_id;
      IF FOUND THEN
        IF NEW.code_category = 'main' THEN
          IF isnumeric(NEW.code) AND strpos(NEW.code, 'E') = 0 THEN
            number := NEW.code::bigint;
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
            IF isnumeric(OLD.code) AND strpos(OLD.code, 'E') = 0 THEN
              number := OLD.code::bigint;
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
      IF FOUND AND isnumeric(OLD.code) AND strpos(OLD.code, 'E') = 0 THEN
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
$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;


COMMIT;
