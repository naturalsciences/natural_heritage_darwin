set search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION fct_catalogue_import_keywords_update()
  RETURNS TRIGGER LANGUAGE plpgsql AS
$$
BEGIN
  IF TG_TABLE_NAME = 'staging_catalogue' THEN
    IF TG_OP IN ('INSERT', 'UPDATE') THEN
      IF COALESCE(NEW.catalogue_ref,0) != 0 AND COALESCE(NEW.level_ref,0) != 0 THEN
        UPDATE classification_keywords as mck
        SET
          referenced_relation = (
            SELECT level_type
            FROM catalogue_levels
            WHERE id = NEW.level_ref
          ),
          record_id = NEW.catalogue_ref
        WHERE mck.referenced_relation = TG_TABLE_NAME
              AND mck.record_id = NEW.id
              AND NOT EXISTS (
            SELECT 1
            FROM classification_keywords as sck
            WHERE sck.referenced_relation = (
              SELECT level_type
              FROM catalogue_levels
              WHERE id = NEW.level_ref
            )
                  AND sck.record_id = NEW.catalogue_ref
                  AND sck.keyword_type = mck.keyword_type
                  AND sck.keyword_indexed = mck.keyword_indexed
        );
      END IF;
      RETURN NEW;
    ELSE
      DELETE FROM classification_keywords
      WHERE referenced_relation = 'staging_catalogue'
            AND record_id = OLD.id;
      RETURN NULL;
    END IF;
  END IF;
END;
$$;

DROP TRIGGER IF EXISTS trg_catalogue_import_keywords_update ON staging_catalogue;

CREATE TRIGGER trg_catalogue_import_keywords_update AFTER INSERT OR UPDATE OR DELETE
ON staging_catalogue FOR EACH ROW
EXECUTE PROCEDURE fct_catalogue_import_keywords_update();

COMMIT;
