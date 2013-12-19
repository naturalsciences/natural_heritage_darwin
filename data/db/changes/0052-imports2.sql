begin;
set search_path=darwin2,public;

ALTER TABLE import add column template_version text ;

COMMIT ;