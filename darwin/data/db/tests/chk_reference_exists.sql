\unset ECHO
\i unit_launch.sql
SELECT plan(14);
INSERT INTO taxonomy (id, name, level_ref) VALUES (10, 'Méàleis Gùbularis&', 1);

INSERT INTO taxonomy (id, name, level_ref) VALUES (11, 'Brol', 1);

SELECT lives_ok('INSERT INTO users_tracking (referenced_relation, record_id,user_ref, modification_date_time) (select ''taxonomy'', id ,1,NOW() from taxonomy where name = ''Méàleis Gùbularis&'')');
SELECT lives_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type) (select ''taxonomy'', id, (select id from taxonomy where name = ''Méàleis Gùbularis&''), ''current_name'' from taxonomy where name = ''Brol'')');
SELECT throws_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2) (select ''taxonomy'', id, 69  from taxonomy where name = ''Brol'')');

INSERT INTO taxonomy (id, name, level_ref) VALUES (20, 'rgerererg', 1);
INSERT INTO taxonomy (id, name, level_ref) VALUES (30, 'sdsdfsdfg', 1);

SELECT throws_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type) VALUES (''taxonomy'', 20, 20, ''current_name'')');

SELECT throws_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type) VALUES (''taxonomy'', 20, 20, ''current_name'')');

SELECT lives_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type) (select ''taxonomy'', id, 20, ''recombined from'' from taxonomy where name = ''Méàleis Gùbularis&'')');
SELECT lives_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type) (select ''taxonomy'', id, 30, ''recombined from'' from taxonomy where name = ''Méàleis Gùbularis&'')');

SELECT lives_ok('INSERT INTO catalogue_relationships (id,referenced_relation, record_id_1, record_id_2, relationship_type) (select 162, ''taxonomy'', id, 30, ''current_name'' from taxonomy where name = ''Méàleis Gùbularis&'')');

SELECT throws_ok('INSERT INTO catalogue_relationships (referenced_relation, record_id_1, record_id_2, relationship_type) (select ''taxonomy'', id, 20, ''recombined from'' from taxonomy where name = ''Méàleis Gùbularis&'')');

SELECT throws_ok('UPDATE catalogue_relationships relationship_type=''recombined from'' WHERE id=162');
SELECT lives_ok('UPDATE catalogue_relationships SET id=12 WHERE id=162');

SELECT lives_ok('INSERT INTO codes (referenced_relation, record_id, code) (select ''taxonomy'', 30, ''Booorl'')');

SELECT throws_ok('INSERT INTO codes (referenced_relation, record_id, code) (select ''taxonomy'', 40, ''Booorl'')');

SELECT throws_ok('INSERT INTO codes (referenced_relation, record_id, code) (select ''taxonomyz'', 30, ''Booorl'')');

SELECT * FROM finish();
ROLLBACK;
