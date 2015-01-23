-- Testing the interaction of code auto insertion and of code insertion, update, delete depending the flags set at collection level
\unset ECHO
\i unit_launch.sql
SELECT plan(46);


SELECT diag('Check for insertion of specimen in a collection without code auto insertion activated');

SELECT diag('Collection of Vertebrates');

SELECT ok(1 = (SELECT COUNT(*) FROM collections WHERE id = 1 AND code_auto_increment = FALSE), 'Check well collection of vertebrates is not code auto incremented activated');
SELECT lives_ok('INSERT INTO specimens (collection_ref) VALUES (1);', 'Insertion of new specimen');
SELECT ok(1 = (SELECT COUNT(*) FROM specimens WHERE collection_ref = 1), 'Check that new specimen has been well inserted for the collection vertebrates');
SELECT ok(0 = (SELECT fct_after_save_add_code(1, 1)), 'Mimic tentative of new code insertion after specimen insertion');
SELECT ok(0 = (SELECT COUNT(*) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 1 AND code_category = 'main'), 'Check that no code has been inserted for the newly inserted specimen');
SELECT diag('Activate the code auto insertion for vertebrates collection');
SELECT lives_ok('UPDATE collections SET code_auto_increment = TRUE WHERE id = 1', 'Set code auto incrementation of vertebrates collection to true');
SELECT ok(true = (SELECT code_auto_increment FROM collections WHERE id = 1), 'Check value has been well updated');
SELECT lives_ok('INSERT INTO specimens (collection_ref) VALUES (1);', 'Insertion of new specimen');
SELECT ok(2 = (SELECT COUNT(*) FROM specimens WHERE collection_ref = 1), 'Check that we have got well two specimens for the collection vertebrates');
SELECT ok(0 = (SELECT fct_after_save_add_code(1, 2)), 'Mimic tentative of new code insertion after specimen insertion');
SELECT ok(1 = (SELECT COUNT(*) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 2 AND code_category = 'main'), 'Check that a new code has been inserted for the newly inserted specimen');
SELECT ok('1' = (SELECT code FROM codes WHERE referenced_relation = 'specimens' AND record_id = 2 AND code_category = 'main'), 'Check that the new code inserted is well 1');
SELECT ok(1 = (SELECT code_last_value FROM collections WHERE id = 1), 'Check well code of collections has been auto incremented');
SELECT lives_ok('INSERT INTO specimens (collection_ref) VALUES (1);', 'Insertion of new specimen');
SELECT ok(3 = (SELECT COUNT(*) FROM specimens WHERE collection_ref = 1), 'Check that we have got well two specimens for the collection vertebrates');
SELECT ok(0 = (SELECT fct_after_save_add_code(1, 3)), 'Mimic tentative of new code insertion after specimen insertion');
SELECT ok(2 = (SELECT code_last_value FROM collections WHERE id = 1), 'Check well code of collections has been auto incremented');
SELECT lives_ok('DELETE FROM specimens WHERE id = 3', 'Delete the last inserted specimen to see if code_last_value for collection is well set back to default value because no more code exists for this collection');
SELECT ok(0 = (SELECT COUNT(*) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 3 AND code_category = 'main'), 'Check that the code has been well cascade deleted');
SELECT ok(1 = (SELECT COUNT(*) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 2 AND code_category = 'main'), 'Check code for specimen 2 is well still there');
SELECT ok(1 = (SELECT code_last_value FROM collections WHERE id = 1), 'Code last value should be set back to default value (0)');
SELECT lives_ok('DELETE FROM specimens WHERE id = 2', 'Delete the last specimen to see if code_last_value for collection is well set back to default value because no more code exists for this collection');
SELECT ok(0 = (SELECT COUNT(*) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 2 AND code_category = 'main'), 'Check that the code has been well cascade deleted');
SELECT ok(0 = (SELECT code_last_value FROM collections WHERE id = 1), 'Code last value should be set back to default value (0)');
SELECT lives_ok('INSERT INTO specimens (collection_ref) VALUES (1);', 'Insertion of new specimen');
SELECT ok(2 = (SELECT COUNT(*) FROM specimens WHERE collection_ref = 1), 'Check that we have got well one specimens for the collection vertebrates');
SELECT ok(0 = (SELECT fct_after_save_add_code(1, 4)), 'Mimic tentative of new code insertion after specimen insertion');
SELECT ok(1 = (SELECT code_last_value FROM collections WHERE id = 1), 'Check well code of collections has been auto incremented');
SELECT ok('main' = (SELECT code_category FROM codes WHERE referenced_relation = 'specimens' and record_id = 4), 'Check well code category is main');
UPDATE codes SET code_category = 'secondary' WHERE referenced_relation = 'specimens' AND record_id = 4;
SELECT ok('secondary' = (SELECT code_category FROM codes WHERE referenced_relation = 'specimens' and record_id = 4), 'Check well code category is main');
SELECT ok(0 = (SELECT code_last_value FROM collections WHERE id = 1), 'Changing the code category should have modified the code last value');
INSERT INTO codes (referenced_relation, record_id, code) VALUES ('specimens', 4, '124'), ('specimens', 4, '125');
SELECT ok(125 = (SELECT code_last_value FROM collections WHERE id = 1), 'Check that insertion of code manually has been well triggered the update of collection code last value');
DELETE FROM codes WHERE referenced_relation = 'specimens' AND record_id = 4 AND code_category = 'main' AND code_num = 125;
SELECT ok(124 = (SELECT code_last_value FROM collections WHERE id = 1), 'Check that deletion of code max value for the given collection let the code last value fall back on 124');
UPDATE specimens SET collection_ref = 2 WHERE id = 4;
SELECT ok(0 = (SELECT code_last_value FROM collections WHERE id = 1), 'Moving specimen from vertebrates to mammalia cause the code last value to be reseted for vertebrates...');
SELECT ok(124 = (SELECT code_last_value FROM collections WHERE id = 2), '... and to be set to 124 for mammalia');
UPDATE specimens SET collection_ref = 1 WHERE id = 4;
UPDATE collections SET code_auto_increment_for_insert_only = FALSE WHERE id = 1;
SELECT ok(124 = (SELECT code_last_value FROM collections WHERE id = 1), 'Set back to 124 for vertebrates...');
SELECT ok(0 = (SELECT code_last_value FROM collections WHERE id = 2), '... and 0 for mammalia');
SELECT ok(0 = (SELECT fct_after_save_add_code(1, 4)), 'Mimic tentative of new code insertion after specimen update (what did not happen ;))');
SELECT ok(124 = (SELECT code_last_value FROM collections WHERE id = 1), 'The code still stay 124 because it is already a number');
SELECT ok(1 = (SELECT COUNT(*) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 4 AND code_category = 'main'), 'And there is still 1 code for specimen 4');
UPDATE codes SET code = 'bisounours' WHERE referenced_relation = 'specimens' AND record_id = 4;
SELECT ok(0 = (SELECT COALESCE(code_num, 0) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 4 AND code_category = 'main'), 'A NULL has been effectively set for numerical code for code 124bis');
SELECT ok(0 = (SELECT code_last_value FROM collections WHERE id = 1), 'The last numeric code is now back to 0');
UPDATE codes SET code = '124bis' WHERE referenced_relation = 'specimens' AND record_id = 4;
SELECT ok(124 = (SELECT COALESCE(code_num, 0) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 4 AND code_category = 'main'), 'A 124 has been effectively set for numerical code for code 124bis');
DELETE FROM codes WHERE referenced_relation = 'specimens' AND record_id = 4 AND code_category = 'main';
SELECT ok(0 = (SELECT fct_after_save_add_code(1, 4)), 'Mimic again tentative of new code insertion after specimen update (what did not happen ;))');
SELECT diag(code_auto_increment || ' ' || code_last_value) FROM collections WHERE id = 1;
SELECT ok(125 = (SELECT code_last_value FROM collections WHERE id = 1), 'Code last value being effectively been set now to 1');
SELECT ok(1 = (SELECT COUNT(*) FROM codes WHERE referenced_relation = 'specimens' AND record_id = 4 AND code_category = 'main'), 'And there are effectively 2 codes for specimen 4');

-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;
