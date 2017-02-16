set search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION fct_get_taxonomy_parents_names (IN path TEXT, IN levelSysName VARCHAR DEFAULT '') RETURNS text IMMUTABLE LANGUAGE plpgsql AS $$
DECLARE
  taxonName TEXT := '';
  taxonNameTemp TEXT;
  taxonID VARCHAR;
BEGIN
  FOREACH taxonID IN ARRAY regexp_split_to_array(trim(path, '/'),'/') LOOP
    IF levelSysName = '' THEN
      SELECT name INTO taxonNameTemp FROM taxonomy WHERE id = taxonID::bigint;
      taxonName := taxonName || ' | ' || taxonNameTemp;
    ELSE
      SELECT t.name INTO taxonNameTemp FROM taxonomy t INNER JOIN catalogue_levels cl ON cl.id = t.level_ref and cl.level_type = 'taxonomy' and cl.level_sys_name = levelSysName WHERE t.id = taxonID::bigint;
      IF taxonNameTemp IS NOT NULL AND taxonNameTemp != '' THEN
        taxonName := taxonNameTemp;
        EXIT;
      END IF;
    END IF;
  END LOOP;
  taxonName := trim(taxonName,' | ');
  RETURN taxonName;
EXCEPTION
	WHEN OTHERS THEN
		RETURN NULL;
END;
$$;

DROP INDEX IF EXISTS idx_taxonomy_path_names;
DROP INDEX IF EXISTS idx_taxonomy_path_kingdom;
DROP INDEX IF EXISTS idx_taxonomy_path_phylum;
DROP INDEX IF EXISTS idx_taxonomy_path_class;
DROP INDEX IF EXISTS idx_taxonomy_path_order;
DROP INDEX IF EXISTS idx_taxonomy_path_family;
DROP INDEX IF EXISTS idx_taxonomy_path_genus;
DROP INDEX IF EXISTS idx_taxonomy_path_sub_genus;
DROP INDEX IF EXISTS idx_user_tracking_for_bbif_export;
DROP INDEX IF EXISTS idx_basisOfRecord;
CREATE INDEX idx_user_tracking_for_bbif_export ON users_tracking(referenced_relation, record_id, modification_date_time);
CREATE INDEX idx_basisOfRecord ON specimens ((CASE WHEN collection_ref = 3 OR collection_path LIKE '/3/%' THEN 'FossilSpecimen' WHEN collection_ref = 231 OR collection_path LIKE '/231/%' THEN 'GeologicalContext' ELSE 'PreservedSpecimen' END), collection_name);
CREATE INDEX idx_taxonomy_path_names ON specimens(fct_get_taxonomy_parents_names(taxon_path));
CREATE INDEX idx_taxonomy_path_kingdom ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'kingdom'));
CREATE INDEX idx_taxonomy_path_phylum ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'phylum'));
CREATE INDEX idx_taxonomy_path_class ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'class'));
CREATE INDEX idx_taxonomy_path_order ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'order'));
CREATE INDEX idx_taxonomy_path_family ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'family'));
CREATE INDEX idx_taxonomy_path_genus ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'genus'));
CREATE INDEX idx_taxonomy_path_sub_genus ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'sub_genus'));

COMMIT;