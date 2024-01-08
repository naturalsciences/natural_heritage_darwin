begin;
set search_path=darwin2,public;

ALTER TABLE imports add column template_version text ;

COMMIT ;
