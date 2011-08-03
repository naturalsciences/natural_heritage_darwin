\unset ECHO
\i unit_launch.sql
SELECT plan(2);
INSERT INTO specimens (id, collection_ref, taxon_ref) VALUES (10005,1,-2);
SELECT diag('Insert a record in catalogue people');
SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) VALUES(''specimens'', 10005,''collector'','''',1,1)','Add a collector');
SELECT ok(1 = (SELECT count(*) FROM darwin_flat WHERE spec_coll_ids @> ARRAY[1]),'check if the collector created appear in sepc_coll_ids');
SELECT * FROM finish();
ROLLBACK;
