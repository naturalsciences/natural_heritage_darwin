set search_path=darwin2,public;

BEGIN;

DROP INDEX IF EXISTS idx_user_tracking_for_bbif_export;
CREATE INDEX idx_user_tracking_for_bbif_export ON users_tracking(referenced_relation, record_id, modification_date_time);

COMMIT;