\unset ECHO
\i unit_launch.sql
SELECT plan(44);

SELECT diag('Check the fill in, update and fill out of dictonaries entries');

SELECT diag('Codes');

SELECT lives_ok('insert into codes (referenced_relation, record_id, code_prefix_separator, code, code_suffix_separator) values (''collections'', 1, ''/'', ''1'', ''-'')', 'Code inserted');
SELECT is('/' , (SELECT code_prefix_separator FROM codes WHERE referenced_relation = 'collections' AND record_id = 1), 'Code prefix separator is well /');
SELECT is('-' , (SELECT code_suffix_separator FROM codes WHERE referenced_relation = 'collections' AND record_id = 1), 'Code suffix separator is well -');
SELECT is('/' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'codes' AND dict_field = 'code_prefix_separator'), 'Code prefix has been well entered in dictionnary');
SELECT is('-' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'codes' AND dict_field = 'code_suffix_separator'), 'Code suffix has been well entered in dictionnary');
SELECT lives_ok('update codes set code_prefix_separator = ''-'', code_suffix_separator = ''/'' where referenced_relation = ''collections'' and record_id = 1', 'Code prefix and suffix well updated');
SELECT is('-' , (SELECT code_prefix_separator FROM codes WHERE referenced_relation = 'collections' AND record_id = 1), 'Code prefix separator is well -');
SELECT is('/' , (SELECT code_suffix_separator FROM codes WHERE referenced_relation = 'collections' AND record_id = 1), 'Code suffix separator is well /');
SELECT is('-' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'codes' AND dict_field = 'code_prefix_separator'), 'Code prefix has been well modified in dictionnary');
SELECT is('/' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'codes' AND dict_field = 'code_suffix_separator'), 'Code suffix has been well modified in dictionnary');
SELECT lives_ok('delete from codes where referenced_relation = ''collections'' and record_id = 1', 'Code deleted');
SELECT is(0 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'codes'), 'No more entries in dictionnary for codes');

SELECT diag('Collection Maintenance');
SELECT lives_ok('insert into collection_maintenance (referenced_relation, record_id, people_ref, category, action_observation) values (''collections'', 1, 1, ''observation'', ''Refill alcool'')', 'Maintenance inserted');
SELECT is('Refill alcool' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'collection_maintenance' AND dict_field = 'action_observation'), 'Action or Observation well entered');
SELECT lives_ok('update collection_maintenance set action_observation = ''Refill with alcool'' where referenced_relation = ''collections'' and record_id = 1', 'Maintenance action-observation well updated');
SELECT is('Refill with alcool' ,  (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'collection_maintenance' AND dict_field = 'action_observation'), 'Action or Observation well updated in dictionnary');
SELECT lives_ok('delete from collection_maintenance where referenced_relation = ''collections'' and record_id = 1', 'Maintenance deleted');
SELECT is(0 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'collection_maintenance'), 'No more entries in dictionnary for collection_maintenance');

SELECT diag('Specimens');
--SELECT lives_ok('INSERT INTO specimens (id, collection_ref, taxon_ref) (select 10000,1, id from taxonomy where name = ' || chr(39) || 'Eucaryota' || chr(39) || ')', 'Specimen well inserted');
SELECT lives_ok('INSERT INTO specimens (id, collection_ref, type, sex, stage, state, social_status, rock_form) VALUES (999, 1, ''Holotype'', ''Male'', ''Adult'', DEFAULT, DEFAULT, DEFAULT)', 'Individual well inserted');
SELECT is('Holotype' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'type'), 'Type field well inserted into dictionnary');
SELECT is('Male' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'sex'), 'Sex field well inserted into dictionnary');
SELECT is('Adult' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'stage'), 'Stage field well inserted into dictionnary');
SELECT is('not applicable' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'state'), 'State field well inserted into dictionnary');
SELECT is('not applicable' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'social_status'), 'Social status field well inserted into dictionnary');
SELECT is('not applicable' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'rock_form'), 'Rock form field well inserted into dictionnary');
SELECT lives_ok('UPDATE specimens SET sex = ''Female'', state = ''Ovigerous'', social_status = ''Worker'' WHERE id = 999', 'Updated the sex, state and social status fields');
SELECT is('Holotype' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'type'), 'Didn''t tuch type in dictionnary -> OK!');
SELECT is('Female' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'sex'), 'Modified well sex in dictionnary');
SELECT is('Adult' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'stage'), 'Didn''t tuch stage in dictionnary -> OK!');
SELECT is('Ovigerous' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'state'), 'Modified well state in dictionnary');
SELECT is('Worker' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'social_status'), 'Modified well social_status in dictionnary');
SELECT is('not applicable' , (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimens' AND dict_field = 'rock_form'), 'Didn''t tuch stage in dictionnary -> OK!');
SELECT lives_ok('DELETE FROM specimens WHERE id = 999', 'Individual well deleted');
SELECT is(0 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens'), 'No more entries in dictionnary for specimens');


SELECT diag('Parts Depends');

--INSERT INTO specimens (id, type, sex, stage, state, social_status, rock_form) VALUES (10000, 'Holotype', 'Male', 'Adult', DEFAULT, DEFAULT, DEFAULT);
INSERT INTO specimens (id, collection_ref, container_storage, container_type) VALUES (12, 1, 'STOOOR', 'TYPEZ');
INSERT INTO specimens (id, collection_ref, container_storage, container_type) VALUES (13, 1, 'STOOORB', 'TYPEY');
INSERT INTO specimens (id, collection_ref, container_storage, container_type) VALUES (14, 1, 'STOOORB', 'TYPEZ');

SELECT is(2 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_type'), 'Values added into spec_parts type');
SELECT is(3 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_storage'), 'Values added into spec_parts');

INSERT INTO specimens (id, collection_ref, container_storage, container_type) VALUES (15, 1, 'STOOORB', 'TYPEZ');
SELECT is(2 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_type'), 'Values added into spec_parts type not dup');
SELECT is(3 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_storage'), 'Values added into spec_parts not dup');

UPDATE specimens SET container_storage = 'NEwSTOOR' where id = 14;

SELECT is(2 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_type'), 'Values added into spec_parts type after update');
SELECT is(4 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_storage'), 'Values added into spec_parts after update');

UPDATE specimens SET container_type = 'TYPEZ' where id = 13;

SELECT is(1 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_type'), 'Values added into spec_parts type after update');
SELECT is(3 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_storage'), 'Values added into spec_parts after update');

delete from specimens;

SELECT is(0 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_type'), 'Values added into spec_parts type after update');
SELECT is(0 , (SELECT COUNT(*)::int FROM flat_dict WHERE referenced_relation = 'specimens' and dict_field = 'container_storage'), 'Values added into spec_parts after update');

/*Finish the tests*/
SELECT * FROM finish();

ROLLBACK;
