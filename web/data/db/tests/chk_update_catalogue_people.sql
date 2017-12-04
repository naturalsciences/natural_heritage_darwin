\unset ECHO
\i unit_launch.sql
SELECT plan(30);
INSERT INTO specimens (id, collection_ref, taxon_ref) (SELECT 10005,1,id from taxonomy where name = 'Bacteria');
SELECT diag('Insert a record in catalogue people');
SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) VALUES(''specimens'', 10005,''collector'','''',1,1)','Add a collector');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_coll_ids @> ARRAY[1]),'check if the collector created appear in spec_coll_ids');
SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) VALUES(''specimens'', 10005,''donator'','''',1,1)','Add a donator');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_don_sel_ids @> ARRAY[1]),'check if the collector created appear in spec_coll_ids');

SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) VALUES(''specimens'', 10005,''donator'','''',2,2)','Add another donator');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_don_sel_ids @> ARRAY[1,2]),'check if the collector created appear in spec_don_sel_ids');

INSERT INTO identifications (id, referenced_relation, record_id, notion_concerned, value_defined) 
  VALUES (100, 'specimens', 10005, 'Expertise', 'Jé #spéè!');

SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) 
  VALUES(''identifications'', 100,''identifier'','''',1,1)','Add a identifier');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids @> ARRAY[1]),'check if ident');

INSERT INTO identifications (id, referenced_relation, record_id, notion_concerned, value_defined) 
  VALUES (101, 'specimens', 10005, 'taxonomy', 'Jé #spéè!');

SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) 
  VALUES(''identifications'', 101,''identifier'','''',1,1)','Add a identifier');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = ARRAY[1]),'check if ident is still alone');

INSERT INTO specimens (id, collection_ref, type) VALUES (1000, 1, 'holotype');

INSERT INTO identifications (id, referenced_relation, record_id, notion_concerned, value_defined) 
  VALUES (102, 'specimens', 1000, 'type', 'Jé #spéè!');
SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) 
  VALUES(''identifications'', 102,''identifier'','''',1,1)','Add a identifier for ind');
SELECT throws_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) 
  VALUES(''identifications'', 102,''identifier'','''',1,1)');

SELECT is(2 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids @> ARRAY[1]),'check if ident in indiv');
INSERT INTO identifications (id, referenced_relation, record_id, notion_concerned, value_defined) 
  VALUES (103, 'specimens', 1000, 'sex', 'Jé #spéè!');
SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) 
  VALUES(''identifications'', 103,''identifier'','''',1,1)','Add a identifier for same ind');
SELECT is(2 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = ARRAY[1]),'check if ident in indiv');
SELECT lives_ok('insert into catalogue_people(referenced_relation,record_id, people_type,people_sub_type, order_by,people_ref) 
  VALUES(''identifications'', 103,''identifier'','''',1,2)','Add a identifier for same ind');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = ARRAY[1,2]),'check if ident in indiv2');


-------- UPDATE 
insert into people (id, is_physical, family_name, given_name, birth_date, gender) VALUES (5, true, 'sssvfddss', 'f', DATE 'June 20, 1979', 'M');

SELECT lives_ok('update catalogue_people set people_ref = 2 where record_id = 10005 and people_type = ''collector'' ');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_coll_ids = ARRAY[2]),'spec collector field is updated');

SELECT lives_ok('update catalogue_people set people_ref = 5 where people_ref= 2 and record_id = 10005 and people_type = ''donator'' ');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_don_sel_ids = ARRAY[1,5]),'spec donator field is updated');

SELECT lives_ok('update catalogue_people set people_ref = 5 where people_ref= 1 and record_id = 100 and people_type = ''identifier'' ');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = ARRAY[1,5]),'spec ident field is updated');


SELECT lives_ok('update catalogue_people set people_ref = 5 where people_ref=1 and record_id = 102 and people_type = ''identifier'' ');
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = ARRAY[1,2,5]),'ind ident field is updated');

--- DELETE 

DELETE FROM catalogue_people where people_ref=5 and record_id = 102 and people_type = 'identifier';

SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = ARRAY[1,2]),'ind ident field is delete');

DELETE FROM catalogue_people where people_ref=5 and record_id = 100 and people_type = 'identifier';
SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = ARRAY[1]),'spec ident field is delete');

DELETE FROM identifications where id = 103;

SELECT is(1 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = '{}'::integer[]),'ind');

delete from identifications;

SELECT is(2 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = '{}'::integer[]),'ind');

INSERT INTO identifications (id, referenced_relation, record_id, notion_concerned, value_defined) 
  VALUES (101, 'specimens', 10005, 'taxonomy', 'Jé #spéè!');

delete from identifications;

SELECT is(2 , (SELECT count(*)::int FROM specimens WHERE spec_ident_ids = '{}'::integer[]),'ind');

SELECT * FROM finish();
ROLLBACK;
