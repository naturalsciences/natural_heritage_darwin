-- Testing the copy code for GTU
\unset ECHO
\i unit_launch.sql
SELECT plan(14);

SELECT diag('Clear Referenced record with record_id and referenced_relation');

INSERT INTO taxonomy (name, level_ref) VALUES ('Méàleis Gùbularis&', 1);
INSERT INTO taxonomy (name, level_ref) VALUES ('Méàleis brol', 1);
INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
	(SELECT 'taxonomy',id,'name','Roooh c''est TOF ça' from taxonomy where name = 'Méàleis Gùbularis&');
INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
	(SELECT 'taxonomy',id,'name','béh c''est vide...' from taxonomy where name = 'Méàleis brol');
SELECT is( 2, (SELECT count(*)::int from comments));

DELETE FROM taxonomy where id=(SELECT id from taxonomy WHERE name = 'Méàleis Gùbularis&');

SELECT is( 1 , (SELECT count(*)::int from comments),'Verify if the record is well deleted');
SELECT is( (SELECT id from taxonomy where name = 'Méàleis brol'),  (SELECT record_id from comments),'the deleted record is the right one');


INSERT INTO igs(id, ig_num) VALUES ( 1458, '11');
INSERT INTO igs(id, ig_num) VALUES ( 1459, '12');
INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
        (SELECT 'igs',1458,'name','Roooh c''est TOF ça' );
SELECT is( 2 , (SELECT count(*)::int from comments),'Comment was added  on igs');
delete from igs where id = 1458;
SELECT is( 1 , (SELECT count(*)::int from comments),'Comment was deleted  on igs');

INSERT INTO multimedia(id, title, referenced_relation, record_id, mime_type) VALUES ( 444, 'test','igs', 1459,'text/plain');
INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
        (SELECT 'multimedia',444,'name','Roooh c''est TOF ça' );
SELECT is( 2 , (SELECT count(*)::int from comments),'Comment was added  on multimedia');
delete from multimedia where id = 444;
SELECT is( 1 , (SELECT count(*)::int from comments),'Comment was deleted  on multimedia');


insert into imports (id, user_ref, format, filename, collection_ref) VALUES (1,1,'brol','brol.xml',2);
insert into staging (id,import_ref, taxon_name) VALUES (5555,1,'Falco coco');
INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
        (SELECT 'staging',5555,'name','Roooh c''est TOF ça' );
SELECT is( 2 , (SELECT count(*)::int from comments),'Comment was added  on staging');
delete from staging where id = 5555;
SELECT is( 1 , (SELECT count(*)::int from comments),'Comment was deleted  on staging');


INSERT INTO taxonomy (id, name, level_ref) VALUES (12, 'Méàleis brolitus', 1);
INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
  values('taxonomy',12,'name','Roooh c''est TOF ça' );

SELECT lives_ok('update comments set comment=''WOOOt'' where record_id =12');

--2 comments
SELECT is( 1 , (SELECT count(*)::int from comments where record_id = 12),'taxo comment is added');
SELECT lives_ok('update taxonomy set id = 13 where id = 12');
SELECT is( 0 , (SELECT count(*)::int from comments where record_id = 12),'taxo comment was migrated 1');
SELECT is( 1 , (SELECT count(*)::int from comments where record_id = 13),'taxo comment was migrated 2');

SELECT * FROM finish();
ROLLBACK;
