set search_path=darwin2,public;

BEGIN;

ALTER TABLE collections
  ADD COLUMN code_mask varchar;

COMMENT ON COLUMN collections.code_mask IS 'A mask that should be applied to help encode following a specific structured way';

COMMIT;
