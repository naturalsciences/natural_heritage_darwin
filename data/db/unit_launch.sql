\set ECHO none
\set QUIET 1
-- Turn off echo and keep things quiet.

-- Format the output for nice TAP.
\pset format unaligned
\pset tuples_only true
\pset pager

-- Revert all changes on failure.
\set ON_ERROR_ROLLBACK 1
\set ON_ERROR_STOP true

BEGIN;

SET search_path TO "unittest","public";
SET client_min_messages TO warning; -- notice;


-- Load test functions
-- \i lib/pgtap.sql
-- Load type,tables, functions and triggers
\i createtables.sql
\i initiate_data.sql
\i createfunctions.sql
\i createtriggers.sql
\i addchecks.sql
-- Load Fixtures data
\i tests_fixtures.sql
