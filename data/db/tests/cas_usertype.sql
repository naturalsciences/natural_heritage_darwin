\unset ECHO
\i unit_launch.sql
SELECT plan(9);

SELECT diag('Check changing db type');
UPDATE users SET db_user_type=4 WHERE id=1;
UPDATE users SET db_user_type=4 WHERE id=2;

SELECT throws_ok('UPDATE users SET db_user_type=2 WHERE id=1','Still Manager in some Collections.','Can''t change main_mgr usertype');

SELECT lives_ok('UPDATE users SET db_user_type=2 WHERE id=2','With not referenced user');

UPDATE users SET db_user_type=4 WHERE id=2;

INSERT INTO collections_rights (collection_ref,user_ref,db_user_type) VALUES (1,2,4);

SELECT lives_ok('UPDATE users SET db_user_type=2 WHERE id=2','With  reference int collection admins');
SELECT ok(0 = (SELECT count(*) FROM collections_rights WHERE user_ref=2 AND db_user_type >=4 ),'Removed from collections admins');


INSERT INTO record_visibilities (referenced_relation, record_id, db_user_type, user_ref, visible)
 	VALUES ('taxonomy', 0, 2, 2, true);
INSERT INTO collections_fields_visibilities (collection_ref, user_ref, field_group_name, db_user_type, visible)
	VALUES (1, 2, 'taxonomic_name', 2, false);
INSERT INTO users_coll_rights_asked (collection_ref, user_ref, field_group_name, db_user_type, searchable, visible, motivation, asking_date_time)
	VALUES (2, 2, 'taxonomic_name', 2, false, false, 'JE VEUX!', now());

INSERT INTO collections_rights (collection_ref, user_ref) VALUES (3,2);
SELECT lives_ok('UPDATE users SET db_user_type=1 WHERE id=2','From encoder to registered');
SELECT ok( 0 = (SELECT count(*) FROM collections_rights WHERE collection_ref=3 AND user_ref=2), 'Check no more rights on collection he was encoder on');

SELECT ok( 1 = (SELECT db_user_type FROM record_visibilities WHERE record_id=0 AND user_ref=2), '');
SELECT ok( 1 = (SELECT db_user_type FROM collections_fields_visibilities WHERE collection_ref=1 AND user_ref=2), '');
SELECT ok( 1 = (SELECT db_user_type FROM users_coll_rights_asked WHERE collection_ref=2 AND user_ref=2), '');

SELECT * FROM finish();
ROLLBACK;
