-- Testing the copy code for GTU
\unset ECHO
\i unit_launch.sql
SELECT plan(3);

SELECT diag('Clear Referenced record with record_id and referenced_relation');

INSERT INTO taxonomy (name, level_ref) VALUES ('Méàleis Gùbularis&', 1);
INSERT INTO taxonomy (name, level_ref) VALUES ('Méàleis brol', 1);
INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment,comment_ts)
	(SELECT 'taxonomy',id,'name','Roooh c''est TOF ça',to_tsvector('') from taxonomy where name = 'Méàleis Gùbularis&');
INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment,comment_ts)
	(SELECT 'taxonomy',id,'name','béh c''est vide...',to_tsvector('') from taxonomy where name = 'Méàleis brol');
SELECT ok ( 2 = (SELECT count(*) from comments));

DELETE FROM taxonomy where id=(SELECT id from taxonomy WHERE name = 'Méàleis Gùbularis&');

SELECT ok ( 1 = (SELECT count(*) from comments),'Verify if the record is well deleted');
SELECT ok ( (SELECT id from taxonomy where name = 'Méàleis brol') = (SELECT record_id from comments),'the deleted record is the right one');



SELECT * FROM finish();
ROLLBACK;
