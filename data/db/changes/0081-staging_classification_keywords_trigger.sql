set search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION fct_catalogue_import_keywords_update()
  RETURNS TRIGGER LANGUAGE plpgsql AS
$$
DECLARE
  booContinue BOOLEAN := FALSE;
  intDiag INTEGER;
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
  ELSEIF TG_TABLE_NAME = 'staging' THEN
    IF TG_OP IN ('INSERT', 'UPDATE') THEN
      IF COALESCE(NEW.taxon_ref,0) != 0 AND COALESCE(NEW.taxon_level_ref,0) != 0 THEN
        IF TG_OP = 'UPDATE' THEN
          IF COALESCE(NEW.taxon_ref,0) != COALESCE(OLD.taxon_ref,0) THEN
            booContinue := TRUE;
          END IF;
        ELSE
          booContinue := TRUE;
        END IF;
        IF booContinue = TRUE THEN
          UPDATE classification_keywords as mck
          SET
            referenced_relation = 'taxonomy',
            record_id = NEW.taxon_ref
          WHERE mck.referenced_relation = TG_TABLE_NAME
                AND mck.record_id = NEW.id
                AND mck.keyword_type IN (
            'GenusOrMonomial',
            'Subgenus',
            'SpeciesEpithet',
            'FirstEpiteth',
            'SubspeciesEpithet',
            'InfraspecificEpithet',
            'AuthorTeamAndYear',
            'AuthorTeam',
            'AuthorTeamOriginalAndYear',
            'AuthorTeamParenthesisAndYear',
            'SubgenusAuthorAndYear',
            'CultivarGroupName',
            'CultivarName',
            'Breed',
            'CombinationAuthorTeamAndYear',
            'NamedIndividual'
          )
                AND NOT EXISTS (
              SELECT 1
              FROM classification_keywords as sck
              WHERE sck.referenced_relation = 'taxonomy'
                    AND sck.record_id = NEW.taxon_ref
                    AND sck.keyword_type = mck.keyword_type
                    AND sck.keyword_indexed = mck.keyword_indexed
          );
        END IF;
      ELSEIF COALESCE(NEW.mineral_ref,0) != 0 AND COALESCE(NEW.mineral_level_ref,0) != 0 THEN
        IF TG_OP = 'UPDATE' THEN
          IF COALESCE(NEW.mineral_ref,0) != COALESCE(OLD.mineral_ref,0) THEN
            booContinue := TRUE;
          END IF;
        ELSE
          booContinue := TRUE;
        END IF;
        IF booContinue = TRUE THEN
          UPDATE classification_keywords as mck
          SET
            referenced_relation = 'mineralogy',
            record_id = NEW.mineral_ref
          WHERE mck.referenced_relation = TG_TABLE_NAME
                AND mck.record_id = NEW.id
                AND mck.keyword_type IN (
            'AuthorTeamAndYear',
            'AuthorTeam',
            'NamedIndividual'
          )
                AND NOT EXISTS (
              SELECT 1
              FROM classification_keywords as sck
              WHERE sck.referenced_relation = 'mineralogy'
                    AND sck.record_id = NEW.mineral_ref
                    AND sck.keyword_type = mck.keyword_type
                    AND sck.keyword_indexed = mck.keyword_indexed
          );
        END IF;
      END IF;
      RETURN NEW;
    ELSE
      DELETE FROM classification_keywords
      WHERE referenced_relation = 'staging'
            AND record_id = OLD.id;
      RETURN NULL;
    END IF;
  END IF;
END;
$$;

DROP TRIGGER IF EXISTS trg_catalogue_import_keywords_update ON staging;

CREATE TRIGGER trg_catalogue_import_keywords_update AFTER INSERT OR UPDATE OR DELETE
ON staging FOR EACH ROW
EXECUTE PROCEDURE fct_catalogue_import_keywords_update();

COMMIT;