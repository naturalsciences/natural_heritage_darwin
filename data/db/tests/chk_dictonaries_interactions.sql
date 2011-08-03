\unset ECHO
\i unit_launch.sql
SELECT plan(35);

SELECT diag('Check the fill in, update and fill out of dictonaries entries');

SELECT diag('Codes');

SELECT lives_ok('insert into codes (referenced_relation, record_id, code_prefix_separator, code, code_suffix_separator) values (''collections'', 1, ''/'', ''1'', ''-'')', 'Code inserted');
SELECT ok('/' = (SELECT code_prefix_separator FROM codes WHERE referenced_relation = 'collections' AND record_id = 1), 'Code prefix separator is well /');
SELECT ok('-' = (SELECT code_suffix_separator FROM codes WHERE referenced_relation = 'collections' AND record_id = 1), 'Code suffix separator is well -');
SELECT ok('/' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'codes' AND dict_field = 'code_prefix_separator'), 'Code prefix has been well entered in dictionnary');
SELECT ok('-' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'codes' AND dict_field = 'code_suffix_separator'), 'Code suffix has been well entered in dictionnary');
SELECT lives_ok('update codes set code_prefix_separator = ''-'', code_suffix_separator = ''/'' where referenced_relation = ''collections'' and record_id = 1', 'Code prefix and suffix well updated');
SELECT ok('-' = (SELECT code_prefix_separator FROM codes WHERE referenced_relation = 'collections' AND record_id = 1), 'Code prefix separator is well -');
SELECT ok('/' = (SELECT code_suffix_separator FROM codes WHERE referenced_relation = 'collections' AND record_id = 1), 'Code suffix separator is well /');
SELECT ok('-' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'codes' AND dict_field = 'code_prefix_separator'), 'Code prefix has been well modified in dictionnary');
SELECT ok('/' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'codes' AND dict_field = 'code_suffix_separator'), 'Code suffix has been well modified in dictionnary');
SELECT lives_ok('delete from codes where referenced_relation = ''collections'' and record_id = 1', 'Code deleted');
SELECT ok(0 = (SELECT COUNT(*) FROM flat_dict WHERE referenced_relation = 'codes'), 'No more entries in dictionnary for codes');

SELECT diag('Collection Maintenance');
SELECT lives_ok('insert into collection_maintenance (referenced_relation, record_id, people_ref, category, action_observation) values (''collections'', 1, 1, ''observation'', ''Refill alcool'')', 'Maintenance inserted');
SELECT ok('Refill alcool' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'collection_maintenance' AND dict_field = 'action_observation'), 'Action or Observation well entered');
SELECT lives_ok('update collection_maintenance set action_observation = ''Refill with alcool'' where referenced_relation = ''collections'' and record_id = 1', 'Maintenance action-observation well updated');
SELECT ok('Refill with alcool' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'collection_maintenance' AND dict_field = 'action_observation'), 'Action or Observation well updated in dictionnary');
SELECT lives_ok('delete from collection_maintenance where referenced_relation = ''collections'' and record_id = 1', 'Maintenance deleted');
SELECT ok(0 = (SELECT COUNT(*) FROM flat_dict WHERE referenced_relation = 'collection_maintenance'), 'No more entries in dictionnary for collection_maintenance');

SELECT diag('Specimens');
SELECT lives_ok('INSERT INTO specimens (id, collection_ref, taxon_ref) VALUES (10000,1,-1)', 'Specimen well inserted');
SELECT lives_ok('INSERT INTO specimen_individuals (specimen_ref, type, sex, stage, state, social_status, rock_form) VALUES (10000, ''Holotype'', ''Male'', ''Adult'', DEFAULT, DEFAULT, DEFAULT)', 'Individual well inserted');
SELECT ok('Holotype' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'type'), 'Type field well inserted into dictionnary');
SELECT ok('Male' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'sex'), 'Sex field well inserted into dictionnary');
SELECT ok('Adult' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'stage'), 'Stage field well inserted into dictionnary');
SELECT ok('not applicable' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'state'), 'State field well inserted into dictionnary');
SELECT ok('not applicable' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'social_status'), 'Social status field well inserted into dictionnary');
SELECT ok('not applicable' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'rock_form'), 'Rock form field well inserted into dictionnary');
SELECT lives_ok('UPDATE specimen_individuals SET sex = ''Female'', state = ''Ovigerous'', social_status = ''Worker'' WHERE id = 1', 'Updated the sex, state and social status fields');
SELECT ok('Holotype' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'type'), 'Didn''t tuch type in dictionnary -> OK!');
SELECT ok('Female' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'sex'), 'Modified well sex in dictionnary');
SELECT ok('Adult' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'stage'), 'Didn''t tuch stage in dictionnary -> OK!');
SELECT ok('Ovigerous' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'state'), 'Modified well state in dictionnary');
SELECT ok('Worker' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'social_status'), 'Modified well social_status in dictionnary');
SELECT ok('not applicable' = (SELECT dict_value FROM flat_dict WHERE referenced_relation = 'specimen_individuals' AND dict_field = 'rock_form'), 'Didn''t tuch stage in dictionnary -> OK!');
SELECT lives_ok('DELETE FROM specimen_individuals WHERE id = 1', 'Individual well deleted');
SELECT ok(0 = (SELECT COUNT(*) FROM flat_dict WHERE referenced_relation = 'specimen_individuals'), 'No more entries in dictionnary for specimen_individuals');

/*Finish the tests*/
SELECT * FROM finish();

ROLLBACK;
