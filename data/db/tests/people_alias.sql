\unset ECHO
\i unit_launch.sql
SELECT plan(4);

INSERT INTO taxonomy (id, name, level_ref) VALUES (1, 'Méàleis Gùbularis&', 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (2, 'AMéàleis Gùbularis&', 2, 1);


SELECT diag('Checking db_people_type');
UPDATE people SET db_people_type = 6 WHERE id=2;
UPDATE people SET db_people_type = 6 WHERE id=1;

INSERT INTO catalogue_people (referenced_relation, record_id, people_ref,order_by)
	VALUES ('taxonomy', '1',1,0),
            ('taxonomy', '1',2,1);

SELECT throws_ok('UPDATE people SET db_people_type = 4 WHERE id=2');

DELETE FROM catalogue_people WHERE referenced_relation='taxonomy' AND record_id=1 AND people_ref=2;

SELECT lives_ok('UPDATE people SET db_people_type = 4 WHERE id=2');

SELECT diag('Checking IF all author are authors :)');

SELECT throws_ok('INSERT INTO catalogue_people (referenced_relation, record_id, people_type, order_by, people_ref)
 VALUES (''taxonomy'', 1, ''author'',0,2)');

UPDATE people SET db_people_type = 6 WHERE id=2;
SELECT lives_ok('INSERT INTO catalogue_people (referenced_relation, record_id, people_type, order_by, people_ref)
 VALUES (''taxonomy'', 1, ''author'',0,2)');


SELECT * FROM finish();
ROLLBACK;
