-- Testing the interaction with my_widgets
\unset ECHO
\i unit_launch.sql
SELECT plan(10);

INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100000, 'Jos Chevremont', 'Jos Chevremont', 4);
INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100001, 'Francka', 'Francka', 1);
INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100002, 'Laika', 'Laika', 1);

INSERT INTO people(id, is_physical, sub_type, family_name, formated_name) VALUES (100002, false, 'Federal Institution', 'Institut des Cocinnelles', 'Institut des Cocinnelles');

INSERT INTO collections(id, code, name, institution_ref, main_manager_ref) VALUES (100000, 'Bulots', 'Bulots', 100002, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100001, 'Bulots Af.', 'Bulots d''Afrique', 100002, 100000, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100002, 'Bulots Europe', 'Bulots d''Europe', 100002, 100000, 100000);

INSERT INTO my_widgets ( user_ref, category, group_name, order_by, col_num, mandatory, visible, opened, color, is_available, icon_ref, title_perso, collections) VALUES (100001, 'catalogue_methods_and_tools_widget', 'properties', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',100000,100001,');
INSERT INTO my_widgets ( user_ref, category, group_name, order_by, col_num, mandatory, visible, opened, color, is_available, icon_ref, title_perso, collections) VALUES (100001, 'catalogue_methods_and_tools_widget', 'comment', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',100000,100001,');
INSERT INTO my_widgets ( user_ref, category, group_name, order_by, col_num, mandatory, visible, opened, color, is_available, icon_ref, title_perso, collections) VALUES (100001, 'part_widget', 'partCount', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Count', ',100000,100001,');

INSERT INTO collections_rights (collection_ref, user_ref) VALUES (100001, 100001);

SELECT diag('Collections referenced for widgets of Francka');

SELECT ok(3 = (SELECT COUNT(*) FROM my_widgets WHERE user_ref = 100001 AND collections ~ E'\,100001\,'), 'Collection is well referenced "3" times in widgets of Francka');

SELECT diag('Check that moving Francka user from one collection to the other, removes reference of old collection from My Widgets');

SELECT lives_ok('UPDATE collections_rights SET collection_ref = 100002 WHERE collection_ref = 100001 AND user_ref = 100001');
SELECT ok(0 = (SELECT COUNT(*) FROM my_widgets WHERE user_ref = 100001 AND collections ~ E'\,100001\,'), 'Collection Bulots of Africa has been well removed from widgets of Francka');
SELECT ok(',100000,' = (SELECT collections FROM my_widgets WHERE user_ref = 100001 LIMIT 1), 'But the main collection (Bulots) remains :)');

UPDATE my_widgets SET collections = ',100000,100002,' WHERE user_ref = 100001;

SELECT diag('Check that replacing the user by another removes well collections from replaced user widgets');

SELECT lives_ok('UPDATE collections_rights SET user_ref = 100002 WHERE collection_ref = 100002 AND user_ref = 100001');
SELECT ok(0 = (SELECT COUNT(*) FROM my_widgets WHERE user_ref = 100001 AND collections ~ E'\,100002\,'), 'Collection Bulots of Europe has been well removed from widgets of Francka');
SELECT ok(',100000,' = (SELECT collections FROM my_widgets WHERE user_ref = 100001 LIMIT 1), 'But the main collection (Bulots) remains :)');

INSERT INTO my_widgets ( user_ref, category, group_name, order_by, col_num, mandatory, visible, opened, color, is_available, icon_ref, title_perso, collections) VALUES (100002, 'catalogue_methods_and_tools_widget', 'properties', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',100000,100002,');
INSERT INTO my_widgets ( user_ref, category, group_name, order_by, col_num, mandatory, visible, opened, color, is_available, icon_ref, title_perso, collections) VALUES (100002, 'catalogue_methods_and_tools_widget', 'comment', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',100000,100002,');
INSERT INTO my_widgets ( user_ref, category, group_name, order_by, col_num, mandatory, visible, opened, color, is_available, icon_ref, title_perso, collections) VALUES (100002, 'part_widget', 'partCount', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Count', ',100000,100002,');

SELECT diag('Check that deleting Laika user removes well collections from her widgets');

SELECT lives_ok('DELETE FROM collections_rights WHERE collection_ref = 100002 AND user_ref = 100002');
SELECT ok(0 = (SELECT COUNT(*) FROM my_widgets WHERE user_ref = 100002 AND collections ~ E'\,100002\,'), 'Collection Bulots of Europe has been well removed from widgets of Laika');
SELECT ok(',100000,' = (SELECT collections FROM my_widgets WHERE user_ref = 100002 LIMIT 1), 'But the main collection (Bulots) remains :)');

-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;
