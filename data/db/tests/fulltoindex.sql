\unset ECHO
\i unit_launch.sql
SELECT plan(32);

SELECT diag('FulltoIndex Function');
SELECT ok('msdfnjrt' = fullToIndex('MsdfnJrt'),'With Majuscule and minuscule');
SELECT ok('mealonyeob' = fullToIndex('Méalo-nÿeø@ß€'),'With Accents and special chars');
SELECT ok('elowyorleds' = fullToIndex('ÉLo Wÿorléds!'),'Majuscule, minuscule, spaces, accents and punctuation');
SELECT ok( 'aaaaaaceeeeiiiinoooooxouuuuy' = fullToIndex('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖ×ØÙÚÛÜÝ'),'a Bunch of weird chars');
SELECT ok( 'aaaaaaceeeeiiiinoooooouuuuy'= fullToIndex('àáâãäåçèéêëìíîïñòóôõöøùúûüý'),'another Bunch of lower weird chars');
SELECT ok( 'aeoeaeoe' = fullToIndex('æ œ Ӕ Œ'),'ligature ae-oe');
SELECT ok( 'sfdfdfggdfklmfklmgfdndgndgnfnnfnfngndfg' = fullToIndex('sfdfdfggdfklmfklmgfd,ndgndgnfnnfnfngndfgndgfndfnvbvloùsdop osdf,n'),'More than 40 chars');
SELECT ok( fullToIndex(null) is null,'With null argument');


SELECT diag('FulltoIndex Trigger');


INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_unit, date_from, date_to) VALUES ('taxonomy',0,'Ph', 'm', TIMESTAMP '0001-01-01 00:00:00', TIMESTAMP '0001-01-01 00:00:00');
SELECT ok( '' = (SELECT property_sub_type_indexed FROM catalogue_properties WHERE record_id=0),'FulltoIndex on catalogue_properties null - property_sub_type_indexed');
SELECT ok( '' = (SELECT property_method_indexed FROM catalogue_properties WHERE record_id=0),'FulltoIndex on catalogue_properties null - property_method_indexed');
SELECT ok( '' = (SELECT property_tool_indexed FROM catalogue_properties WHERE record_id=0),'FulltoIndex on catalogue_properties null - property_tool_indexed');

INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, date_from, date_to, property_unit, property_tool_indexed, property_method_indexed, property_sub_type_indexed ) VALUES ('taxonomy',0,'Temperature',NOW(),NOW(), 'degC', 'ham ér', 'cra hé', 'Lambert 72');
SELECT ok( '' = (SELECT property_sub_type_indexed FROM catalogue_properties WHERE record_id=0 AND property_type='Temperature'),'FulltoIndex on catalogue_properties null - property_sub_type_indexed');
SELECT ok( '' = (SELECT property_method_indexed FROM catalogue_properties WHERE record_id=0 AND property_type='Temperature'),'FulltoIndex on catalogue_properties null - property_method_indexed');
SELECT ok( '' = (SELECT property_tool_indexed FROM catalogue_properties WHERE record_id=0 AND property_type='Temperature'),'FulltoIndex on catalogue_properties null - property_tool_indexed');

INSERT INTO chronostratigraphy (id, name, level_ref ) VALUES (1,'ÉLo Wÿorléds', 55);
SELECT ok( to_tsvector('simple', 'ÉLo Wÿorléds') = (SELECT name_indexed FROM chronostratigraphy WHERE id=1),'FulltoIndex on chronostratigraphy');

INSERT INTO expeditions (id, name ) VALUES (1,'ÉLo Wÿorléds');
SELECT ok( 'elowyorleds' = (SELECT name_indexed FROM expeditions WHERE id=1),'FulltoIndex on expeditions');
SELECT ok( to_tsvector('simple', 'ÉLo Wÿorléds') = (SELECT name_ts FROM expeditions WHERE id=1),'To TextSearch expeditions');

INSERT INTO habitats (id, code, description) VALUES (1,'Lé Hâbitôt','Lé Hâbitôt');
SELECT ok( 'lehabitot' = (SELECT code_indexed FROM habitats WHERE id=1),'FulltoIndex on habitats');
SELECT ok ( to_tsvector('simple', 'Lé Hâbitôt') = (SELECT description_ts FROM habitats WHERE id=1),'full text on habitats');

INSERT INTO identifications (referenced_relation, record_id, notion_concerned, value_defined) VALUES ('taxonomy', 0, 'Expertise', 'Jé #spéè!');
SELECT ok( 'jespee' = (SELECT value_defined_indexed FROM identifications WHERE record_id=0),'FulltoIndex on identifications');

INSERT INTO identifications (referenced_relation, record_id, notion_concerned, value_defined) VALUES ('taxonomy', 0, 'Taxonomic identification' , null);
SELECT ok( '' = (SELECT value_defined_indexed FROM identifications WHERE record_id=0 AND notion_concerned='Taxonomic identification'),'FulltoIndex on identifications with null');

INSERT INTO lithology (id, name, level_ref) VALUES (1,'éLoow !', 1);
SELECT ok( to_tsvector('simple', 'éLoow !') = (SELECT name_indexed FROM lithology WHERE id=1),'FulltoIndex on lithology');

INSERT INTO lithostratigraphy (id,name, level_ref) VALUES (1, 'Méalo-nÿeø@ß€', 64);
SELECT ok( to_tsvector('simple', 'Méalo-nÿeø@ß€') = (SELECT name_indexed FROM lithostratigraphy WHERE id=1),'FulltoIndex on lithostratigraphy');

INSERT INTO mineralogy (id, name, code, level_ref) VALUES (1, 'Lé bou/ caiéoui', 0, 70);
SELECT ok( to_tsvector('simple', 'Lé bou/ caiéoui') = (SELECT name_indexed FROM mineralogy WHERE id=1),'FulltoIndex on mineralogy');

INSERT INTO multimedia (id, title) VALUES (1, 'À L''énorme Tické!');
SELECT ok( 'alenormeticke' = (SELECT title_indexed FROM multimedia WHERE id=1),'FulltoIndex on multimedia');
SELECT ok( to_tsvector('simple', 'À L''énorme Tické!') = (SELECT descriptive_ts FROM multimedia WHERE id=1),'fulltext on multimedia');

INSERT INTO multimedia_keywords (object_ref,keyword) VALUES (1,'La ''mèr'' Nwàre') ;
SELECT ok( 'lamernware' = (SELECT keyword_indexed FROM multimedia_keywords WHERE object_ref=1),'FulltoIndex on multimedia_keywords');

INSERT INTO codes (referenced_relation, record_id, code_prefix, code) VALUES ('multimedia',1, '12é-MOL7385',6847);
SELECT ok( '12emol73856847' = (SELECT full_code_order_by FROM codes WHERE record_id = 1 AND referenced_relation = 'multimedia' ),'FulltoIndex on multimedia_codes');

insert into people (id, is_physical, formated_name, formated_name_indexed, formated_name_ts, family_name, birth_date, end_date ) VALUES
(3, true, 'The Expert', 'theexpert', to_tsvector('simple', 'The Expert'),  'The Expert', '0001-01-01', DATE '0001-01-01');

INSERT INTO specimens (id, collection_ref) VALUES (1,1);


INSERT INTO specimen_individuals (id, specimen_ref, type) VALUES (1,1,'holotype');
INSERT INTO specimen_parts (id, specimen_individual_ref, specimen_part) VALUES (1, 1, 'head');


INSERT INTO tag_groups (id, gtu_ref,group_name,sub_group_name,tag_value) VALUES (1, 0, 'Rév#ers','','La ''mèr'' Nwàre');
SELECT ok( 'revers' = (SELECT group_name_indexed FROM tag_groups WHERE id=1),'FulltoIndex on tags_groups');

INSERT INTO taxonomy (id, name, level_ref) VALUES (1, 'Méàleis Gùbularis&', 1);
SELECT ok( to_tsvector('simple', 'Méàleis Gùbularis&') = (SELECT name_indexed FROM taxonomy WHERE id=1),'FulltoIndex on taxonomy name');

insert into users (id, is_physical, family_name, given_name, birth_date, gender) VALUES (3, true, 'Maréchal', 'Bill', NOW(), 'M');
insert into people (id, is_physical, family_name, given_name, birth_date, gender) VALUES (4, true, 'Maréchal', 'Bill', NOW(), 'M');
insert into people (id, is_physical, family_name, given_name, birth_date, gender) VALUES (5, true, 'Marechal', 'Bill', NOW(), 'M');


INSERT INTO class_vernacular_names (referenced_relation, record_id, id, community) VALUES ('taxonomy',0,1,'testlang');
INSERT INTO vernacular_names (vernacular_class_ref, name) VALUES (1,'Éléphant!');
SELECT ok( 'elephant' = (SELECT name_indexed FROM vernacular_names WHERE vernacular_class_ref=1),'FulltoIndex on vernacular_names');
SELECT ok ( to_tsvector('simple', 'Éléphant') = (SELECT name_ts FROM vernacular_names WHERE vernacular_class_ref=1),'Full TEXT on vernacular_names');

SELECT * FROM finish();
ROLLBACK;
