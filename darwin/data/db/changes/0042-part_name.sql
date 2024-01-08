begin;
set search_path=darwin2,public;
\i  createfunctions.sql


ALTER TABLE specimen_parts add column object_name text;
ALTER TABLE specimen_parts add column object_name_indexed text not null default '';

ALTER TABLE staging add column object_name text;

CREATE TRIGGER trg_cpy_fullToIndex_specimen_parts BEFORE INSERT OR UPDATE
        ON specimen_parts FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE INDEX idx_specimen_parts_object_name_indexed on specimen_parts (object_name_indexed);

COMMIT;
