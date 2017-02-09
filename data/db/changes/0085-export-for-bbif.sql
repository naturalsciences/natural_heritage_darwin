set search_path=darwin2,public;

BEGIN;

DROP INDEX IF EXISTS idx_user_tracking_for_bbif_export;
DROP INDEX IF EXISTS idx_basisOfRecord;
CREATE INDEX idx_user_tracking_for_bbif_export ON users_tracking(referenced_relation, record_id, modification_date_time);
CREATE INDEX idx_basisOfRecord ON specimens ((CASE WHEN collection_ref = 3 OR collection_path LIKE '/3/%' THEN 'FossilSpecimen' WHEN collection_ref = 231 OR collection_path LIKE '/231/%' THEN 'GeologicalContext' ELSE 'PreservedSpecimen' END), collection_name);

COMMIT;