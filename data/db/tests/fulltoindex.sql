\unset ECHO
\i unit_launch.sql
SELECT plan(24);

SELECT diag('FulltoIndex Function');
SELECT ok('msdfnjrt' = fullToIndex('MsdfnJrt'),'With Majuscule and minuscule');
SELECT ok('mealonyeob' = fullToIndex('Méalo-nÿeø@ß€'),'With Accents and special chars');
SELECT ok('elowyorleds' = fullToIndex('ÉLo Wÿorléds!'),'Majuscule, minuscule, spaces, accents and punctuation');
SELECT ok( 'aaaaaaceeeeiiiinoooooxouuuuy' = fullToIndex('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖ×ØÙÚÛÜÝ'),'a Bunch of weird chars');
SELECT ok( 'aaaaaaceeeeiiiinoooooouuuuy'= fullToIndex('àáâãäåçèéêëìíîïñòóôõöøùúûüý'),'another Bunch of lower weird chars');
SELECT ok( 'aeoeaeoe' = fullToIndex('æ œ Ӕ Œ'),'ligature ae-oe');
SELECT ok( fullToIndex(null) is null,'With null argument');


SELECT diag('FulltoIndex Trigger');
INSERT INTO taxonomy (id, name,level_ref) VALUES (10, 'Brol',1);

INSERT INTO properties (referenced_relation, record_id, property_type, property_unit, date_from, date_to, lower_value, upper_value) 
  VALUES ('taxonomy',10,'Ph', 'm', TIMESTAMP '0001-01-01 00:00:00', TIMESTAMP '0001-01-01 00:00:00', 12, '');
SELECT ok( '' = (SELECT applies_to_indexed FROM properties WHERE record_id=10),'FulltoIndex on properties null - applies_to_indexed');
SELECT ok( '' = (SELECT method_indexed FROM properties WHERE record_id=10),'FulltoIndex on properties null - property_method_indexed');

INSERT INTO properties (referenced_relation, record_id, property_type, date_from, date_to, property_unit, method_indexed, applies_to_indexed , lower_value, upper_value)
  VALUES ('taxonomy',10,'Temperature',NOW(),NOW(), 'degC','cra hé', 'Lambert 72' , 12, '');
SELECT ok( '' = (SELECT applies_to_indexed FROM properties WHERE record_id=10 AND property_type='Temperature'),'FulltoIndex on properties null - applies_to_indexed');
SELECT ok( '' = (SELECT method_indexed FROM properties WHERE record_id=10 AND property_type='Temperature'),'FulltoIndex on properties null - property_method_indexed');

INSERT INTO chronostratigraphy (id, name, level_ref ) VALUES (1,'ÉLo Wÿorléds', 55);
SELECT ok( fullToIndex('ÉLo Wÿorléds') = (SELECT name_indexed FROM chronostratigraphy WHERE id=1),'FulltoIndex on chronostratigraphy');

INSERT INTO expeditions (id, name ) VALUES (1,'ÉLo Wÿorléds');
SELECT ok( 'elowyorleds' = (SELECT name_indexed FROM expeditions WHERE id=1),'FulltoIndex on expeditions');

INSERT INTO identifications (referenced_relation, record_id, notion_concerned, value_defined) VALUES ('taxonomy', 10, 'Expertise', 'Jé #spéè!');
SELECT ok( 'jespee' = (SELECT value_defined_indexed FROM identifications WHERE record_id=10),'FulltoIndex on identifications');

INSERT INTO identifications (referenced_relation, record_id, notion_concerned, value_defined) VALUES ('taxonomy', 10, 'Taxonomic identification' , null);
SELECT ok( '' = (SELECT value_defined_indexed FROM identifications WHERE record_id=10 AND notion_concerned='Taxonomic identification'),'FulltoIndex on identifications with null');

INSERT INTO lithology (id, name, level_ref) VALUES (1,'éLoow !', 1);
SELECT ok( fullToIndex('éLoow !') = (SELECT name_indexed FROM lithology WHERE id=1),'FulltoIndex on lithology');

INSERT INTO lithostratigraphy (id,name, level_ref) VALUES (1, 'Méalo-nÿeø@ß€', 64);
SELECT ok( fullToIndex('Méalo-nÿeø@ß€') = (SELECT name_indexed FROM lithostratigraphy WHERE id=1),'FulltoIndex on lithostratigraphy');

INSERT INTO mineralogy (id, name, code, level_ref) VALUES (1, 'Lé bou/ caiéoui', 0, 70);
SELECT ok( fullToIndex('Lé bou/ caiéoui') = (SELECT name_indexed FROM mineralogy WHERE id=1),'FulltoIndex on mineralogy');

INSERT INTO codes (referenced_relation, record_id, code_prefix, code) VALUES ('mineralogy',1, '12é-MOL7385',6847);
SELECT ok( '12emol73856847' = (SELECT full_code_indexed FROM codes WHERE record_id = 1 AND referenced_relation = 'mineralogy' ),'FulltoIndex on code');

insert into people (id, is_physical, formated_name, formated_name_indexed, family_name, birth_date, end_date ) VALUES
(3, true, 'The Expert', 'theexpert', 'The Expert', '0001-01-01', DATE '0001-01-01');

INSERT INTO specimens (id, collection_ref, type, specimen_part) VALUES (1,1, 'holotype', 'head');

INSERT INTO  gtu (id, code) VALUES (10,'bru12');
INSERT INTO  gtu (id, code) VALUES (1,'bru66');

INSERT INTO tag_groups (id, gtu_ref,group_name,sub_group_name,tag_value) VALUES (1, 1, 'Rév#ers','','La ''mèr'' Nwàre');
SELECT ok( 'revers' = (SELECT group_name_indexed FROM tag_groups WHERE id=1),'FulltoIndex on tags_groups');

INSERT INTO taxonomy (id, name, level_ref) VALUES (11, 'Méàleis Gùbularis&', 1);
SELECT ok( fullToIndex('Méàleis Gùbularis&') = (SELECT name_indexed FROM taxonomy WHERE id=11),'FulltoIndex on taxonomy name');

insert into users (id, is_physical, family_name, given_name, birth_date, gender) VALUES (3, true, 'Maréchal', 'Bill', NOW(), 'M');
insert into people (id, is_physical, family_name, given_name, birth_date, gender) VALUES (4, true, 'Maréchal', 'Bill', NOW(), 'M');
insert into people (id, is_physical, family_name, given_name, birth_date, gender) VALUES (5, true, 'Marechal', 'Bill', NOW(), 'M');


INSERT INTO vernacular_names (referenced_relation, record_id, community, name) VALUES ('taxonomy',11, 'tést','Éléphant!');

SELECT ok( 'elephant' = (SELECT name_indexed FROM vernacular_names WHERE record_id = 11),'FulltoIndex on vernacular_names');
SELECT ok ( fullToIndex('Éléphant') = (SELECT name_indexed FROM vernacular_names WHERE record_id = 11),'Full TEXT on vernacular_names');
SELECT ok( 'test' = (SELECT community_indexed FROM vernacular_names WHERE record_id = 11),'FulltoIndex on vernacular_names');

SELECT * FROM finish();
ROLLBACK;
