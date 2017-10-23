-- Testing the main manager update for collections
\unset ECHO
\i unit_launch.sql
SELECT plan(45);

INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100000, 'Jos Chevremont', 'Jos Chevremont', 4);
INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100001, 'Pollo LeFox', 'Pollo LeFox', 4);
INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100002, 'Ericky', 'Ericky', 4);
INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100003, 'Francka', 'Francka', 1);
INSERT INTO users(id, family_name, formated_name, db_user_type) VALUES (100004, 'Clown', 'Clown', 4);

INSERT INTO people(id, is_physical, sub_type, family_name, formated_name) VALUES (100002, false, 'Federal Institution', 'Institut des Cocinnelles', 'Institut des Cocinnelles');

INSERT INTO collections(id, code, name, institution_ref, main_manager_ref) VALUES (100000, 'Bulots', 'Bulots', 100002, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100001, 'Bulots Af.', 'Bulots d''Afrique', 100002, 100001, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref) VALUES (100002, 'Clowns', 'Clowns', 100002, 100004);

SELECT diag('Check collections well inserted and with right collections managers');

SELECT diag('Collection of Bulots');
SELECT ok(1 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100000),'Insertion of main managers for Bulots collection went well');
SELECT ok(1 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100000 and user_ref = 100000 and db_user_type = 4),'First manager is well Jos Chevremont');
SELECT diag('Collection of Bulots from Africa');
SELECT ok(2 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100001),'Insertion of main managers for Bulots of Africa collection went well');
SELECT ok(1 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100001 and user_ref = 100000 and db_user_type = 4),'First manager is well Jos Chevremont');
SELECT ok(1 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100001 and user_ref = 100001 and db_user_type = 4),'Second manager is well Pollo LeFox');

SELECT diag('On the second collection, modify main manager from one to an other -> should work');
SELECT lives_ok('UPDATE collections set main_manager_ref = 100000 where id = 100001', 'Update of collection main manager is ok');
SELECT ok(100000 = (SELECT main_manager_ref FROM collections WHERE id = 100001), 'Update of main manager is rightly done');

SELECT diag('Select an other collection manager as the new main manager and check he''s been added to collections_rights');
SELECT lives_ok('UPDATE collections set main_manager_ref = 100002 where id = 100001', 'Update of collection main manager is ok');
SELECT ok(3 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100001),'Insertion of a new main manager for Bulots of Africa collection went well');
SELECT ok(1 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100001 and user_ref = 100002 and db_user_type = 4),'Manager is well Ericky');

SELECT diag('Select a registered user as the new main manager and check he''s been added to collections_rights');
SELECT lives_ok('UPDATE collections set main_manager_ref = 100003 where id = 100001', 'Update of collection main manager is ok');
SELECT ok(4 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100001),'Insertion of a new main manager for Bulots of Africa collection went well');
SELECT ok(1 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100001 and user_ref = 100003 and db_user_type = 4),'Manager is well Francka');
SELECT ok(4 = (SELECT db_user_type FROM users WHERE id = 100003), 'Registered user Francka has been well promoted to collection manager');

SELECT diag('Trying to delete the new main manager from collections_rights and see... it fails ;)');
SELECT throws_ok('DELETE FROM collections_rights WHERE user_ref = 100003 and collection_ref = 100001');

SELECT diag('Deleting an other collection manager succeed');
SELECT lives_ok('DELETE FROM collections_rights WHERE collection_ref = 100001 and user_ref = 100002');
SELECT ok(3 = (SELECT COUNT(*) user_ref FROM collections_rights WHERE collection_ref = 100001),'Deletion of a main manager for Bulots of Africa collection went well');

SELECT diag('Check for Ericky that he''s been unpromoted to registered user, since he''s not in any collections anymore');
SELECT ok(1 = (SELECT db_user_type FROM users WHERE id = 100002), 'Ericky is well now registered user');

SELECT diag('Tryin to move the main manager from Bulots of Africa collection to Bulots collection and see... it fails ;)');
SELECT throws_ok('UPDATE collections_rights SET collection_ref = 100000 WHERE collection_ref = 100001 and user_ref = 100003');

SELECT diag('Tryin to change the main manager for Bulots of Africa collection from Francka to Pollo Le Fox and see... it fails ;)');
SELECT throws_ok('UPDATE collections_rights SET user_ref = 100001 WHERE collection_ref = 100001 and user_ref = 100003');

SELECT diag('Tryin to unpromote the main manager for Bulots of Africa collection (Francka) and see... it fails ;)');
SELECT throws_ok('UPDATE collections_rights SET db_user_type = 2 WHERE collection_ref = 100001 and user_ref = 100003');

SELECT diag('Tryin to promote the main manager for Bulots of Africa collection (Francka) and see... it succeed this time ;D');
SELECT lives_ok('UPDATE collections_rights SET db_user_type = 8 WHERE collection_ref = 100001 and user_ref = 100003');

SELECT diag('Back as a collection manager and still succeed...');
SELECT lives_ok('UPDATE collections_rights SET db_user_type = 4 WHERE collection_ref = 100001 and user_ref = 100003');

SELECT diag('Take care Francka is now admin and is not unpromoted !');
SELECT ok(8 = (SELECT db_user_type FROM users WHERE id = 100003), 'Francka Admin !');
SELECT diag('To get her back as a collection manager, it must be done manually by SQL');
SELECT lives_ok('UPDATE users SET db_user_type = 4 WHERE id = 100003');

SELECT diag('Unpromote Pollo LeFox and check this unpromotion has also an impact on his db_user_type in users');
SELECT ok(4 = (SELECT db_user_type FROM users WHERE id = 100001), 'db_user_type of Pollo LeFox is well collection manager');
SELECT lives_ok('UPDATE collections_rights SET db_user_type = 2 WHERE collection_ref = 100001 and user_ref = 100001');
SELECT ok(2 = (SELECT db_user_type FROM users WHERE id = 100001), 'New db_user_type of Pollo LeFox is well encoder');

SELECT diag('Move Pollo LeFox from Bulots of Africa collection to Bulots collection');
SELECT lives_ok('UPDATE collections_rights SET collection_ref = 100000 WHERE collection_ref = 100001 AND user_ref = 100001');

SELECT diag('Replace Pollo LeFox by Ericky and take the opportunity to update db_user_type back to collection manager');
SELECT lives_ok('UPDATE collections_rights SET user_ref = 100002, db_user_type = 4 WHERE collection_ref = 100000 AND user_ref = 100001');
SELECT ok(1 = (SELECT db_user_type FROM users WHERE id = 100001), 'New db_user_type of Pollo LeFox is now reg user because last occurence was removed');
SELECT ok(4 = (SELECT db_user_type FROM users WHERE id = 100002), 'New db_user_type of Ericky is well collection manager');

SELECT diag('Change parent collection of Bulots d''Afrique collection');
SELECT diag('New user Clown should be added to the collections rights of Bulots d''Afrique');
SELECT lives_ok('UPDATE collections SET parent_ref = 100002 where id = 100001');
SELECT ok(1 = (SELECT COUNT(*) FROM collections_rights WHERE collection_ref = 100001 and user_ref = 100004 and db_user_type = 4), 'Clown user well added as a collection manager of Bulots d''Afrique');

SELECT diag('Add preference and a fake parts saved search for Francka');
SELECT lives_ok('INSERT INTO preferences (user_ref, pref_key, pref_value) VALUES (100003, ''search_cols_specimen'', ''taxon|part|building|room|container_type'')', 'Insertion in preferences ok');
SELECT lives_ok('INSERT INTO my_saved_searches (user_ref, name, search_criterias, visible_fields_in_result, subject) VALUES (100003, ''spec search'', '''', ''taxon|part|building|room|container_type'', ''specimen'')', 'Insertion in my_saved_searches ok');
SELECT lives_ok('INSERT INTO my_saved_searches (user_ref, name, search_criterias, visible_fields_in_result, subject) VALUES (100003, ''spec search 2'', ''{s1:a11}'', ''taxon|floor|part|building|room|container|container_type'', ''specimen'')', 'Second insertion in my_saved_searches ok');

SELECT diag('Check cascade delete of collection well occurs and do the unpromotion job');
SELECT lives_ok('DELETE FROM collections WHERE id = 100001');
SELECT ok(0 = (SELECT COUNT(*) FROM collections_rights WHERE collection_ref = 100001), 'No more collection rights for the deleted collection');
SELECT ok(4 = (SELECT db_user_type FROM users WHERE id = 100000), 'Jos Chevremont still main manager');
SELECT ok(0 = (SELECT COUNT(*) FROM collections_rights WHERE user_ref = 100003), 'No more Francka referenced');
SELECT ok(1 = (SELECT db_user_type FROM users WHERE id = 100003), 'Francka unpromoted to registered user');
SELECT ok('taxon|part' = (SELECT pref_value FROM preferences WHERE user_ref = 100003 AND pref_key = 'search_cols_specimen'), 'Preferences - s columns not visible well removed');
SELECT ok('taxon|part' = (SELECT visible_fields_in_result FROM my_saved_searches WHERE user_ref = 100003 AND subject = 'specimen' AND name = 'spec search'), 'My saved searches - spec columns not visible well removed');
SELECT ok('taxon|part' = (SELECT visible_fields_in_result FROM my_saved_searches WHERE user_ref = 100003 AND subject = 'specimen' AND name = 'spec search 2'), 'My saved searches - spec columns not visible well removed');

-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;
