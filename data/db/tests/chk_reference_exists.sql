\unset ECHO
\i unit_launch.sql
SELECT plan(14);
INSERT INTO taxonomy (id, name, level_ref) VALUES (1, 'Méàleis Gùbularis&', 1);

SELECT lives_ok('INSERT INTO users_tracking (referenced_relation, record_id,user_ref, modification_date_time)
				VALUES (''taxonomy'',1,1,NOW())');
SELECT lives_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type)
	VALUES (''taxonomy'', 0, 1, ''current_name'')');
SELECT throws_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2)
	VALUES (''taxonomy'', 0, 69)');

INSERT INTO taxonomy (id, name, level_ref) VALUES (2, 'rgerererg', 1);
INSERT INTO taxonomy (id, name, level_ref) VALUES (3, 'sdsdfsdfg', 1);

SELECT throws_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type)
	VALUES (''taxonomy'', 0, 2, ''current_name'')');

SELECT throws_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type)
	VALUES (''taxonomy'', 2, 2, ''current_name'')');

SELECT lives_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type)
	VALUES (''taxonomy'', 1, 2, ''recombined from'')');
SELECT lives_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type)
	VALUES (''taxonomy'', 1, 3, ''recombined from'')');

SELECT lives_ok('INSERT INTO catalogue_relationships (id,referenced_relation, record_id_1, record_id_2, relationship_type)
	VALUES (162, ''taxonomy'', 1, 3, ''current_name'')');

SELECT throws_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type)
	VALUES (''taxonomy'', 1, 0, ''recombined from'')');

SELECT throws_ok('UPDATE catalogue_relationships relationship_type=''recombined from'' WHERE id=162');
SELECT lives_ok('UPDATE catalogue_relationships SET id=12 WHERE id=162');


SELECT lives_ok('INSERT INTO codes (referenced_relation, record_id, code)
        VALUES (''taxonomy'', 3, ''Booorl'')');

SELECT throws_ok('INSERT INTO codes (referenced_relation, record_id, code)
        VALUES (''taxonomy'', 4, ''Booorl'')');

SELECT throws_ok('INSERT INTO codes (referenced_relation, record_id, code)
        VALUES (''taxonomyz'', 3, ''Booorl'')');

SELECT * FROM finish();
ROLLBACK;
