begin;
set search_path=darwin2, public;
\i createfunctions.sql

alter table loans add column search_indexed text;
update loans set search_indexed = fullToIndex(COALESCE(name,'') || COALESCE(description,''));

alter TABLE loans ALTER column search_indexed set not NULL;

CREATE INDEX  idx_gin_trgm_loans_search on loans  using gin ("search_indexed" gin_trgm_ops);

commit;
