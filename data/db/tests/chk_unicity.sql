-- Testing the copy code for GTU
\unset ECHO
\i unit_launch.sql
SELECT plan(6);

INSERT INTO taxonomy (name, level_ref) VALUES ('Méàleis Gùbularis&', 1);
INSERT INTO taxonomy (name, level_ref,parent_ref) VALUES ('Brol', 2,1);

SELECT lives_ok('INSERT INTO classification_synonymies(referenced_relation, record_id, group_id, group_name) 
    (SELECT ''taxonomy'',id , 12, ''synonym'' from taxonomy where name = ''Méàleis Gùbularis&'') ;','Insert synonym is ok');

SELECT lives_ok('INSERT INTO classification_synonymies(referenced_relation, record_id, group_id, group_name) 
    (SELECT ''taxonomy'',id, 12, ''synonym'' from taxonomy where name = ''Brol'') ;','Insert synonym is ok');

SELECT lives_ok('INSERT INTO classification_synonymies(id, referenced_relation, record_id, group_id, group_name) 
    (SELECT 162, ''taxonomy'',id, 13, ''isonym'' from taxonomy where name = ''Brol'') ;','Insert synonym is ok');

SELECT throws_ok('INSERT INTO classification_synonymies(referenced_relation, record_id, group_id, group_name) 
    (SELECT ''taxonomy'',id, 12, ''synonym'' from taxonomy where name = ''Brol'')');

SELECT throws_ok('UPDATE classification_synonymies set group_name=''synonym'', group_id=12 WHERE id=162');

SELECT lives_ok('UPDATE classification_synonymies set is_basionym=true WHERE id=162','update synonym is ok');

-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;