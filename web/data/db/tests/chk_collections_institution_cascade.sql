-- Testing the interaction with my_widgets
\unset ECHO
\i unit_launch.sql
SELECT plan(5);

INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100000, 'Jos Chevremont', 'Jos Chevremont', 4);

INSERT INTO people(id, is_physical, sub_type, family_name, formated_name) VALUES (100002, false, 'Federal Institution', 'Institut des Cocinnelles', 'Institut des Cocinnelles');
INSERT INTO people(id, is_physical, sub_type, family_name, formated_name) VALUES (100003, false, 'Federal Institution', 'Institut des Fourmis', 'Institut des Fourmis');

INSERT INTO collections(id, code, name, institution_ref, main_manager_ref) VALUES (100000, 'Bulots', 'Bulots', 100002, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100001, 'Bulots C.', 'Bulots Continentaux', 100002, 100000, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100002, 'Bulots Europe', 'Bulots d''Europe', 100002, 100000, 100001);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref) VALUES (100003, 'Croutons', 'Croutons', 100003, 100000);

SELECT diag('Check cannot update parent of Bulots C. collection without also modifying institution ref (the one of parent)');

SELECT throws_ok('UPDATE collections SET parent_ref = 100003 WHERE id = 100001');
SELECT throws_ok('UPDATE collections SET institution_ref = 100003 WHERE id = 100001');

SELECT diag('Check that modifying both parent and institution with good values impact children too');

SELECT lives_ok('UPDATE collections SET institution_ref = 100003, parent_ref = 100003 WHERE id = 100001');
SELECT ok('/100003/100001/' = (SELECT path FROM collections WHERE id = 100002), 'Path is ok for collections of Bulots d''Europe');
SELECT ok(100003 = (SELECT institution_ref FROM collections WHERE id = 100002), 'Institution is ok for collections of Bulots d''Europe');

-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;
