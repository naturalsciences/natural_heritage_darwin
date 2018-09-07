begin;
set search_path=darwin2,public;

ALTER TABLE specimen_parts add column category text default 'physical';

COMMIT;
