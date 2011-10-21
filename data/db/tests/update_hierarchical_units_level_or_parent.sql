\unset ECHO
\i unit_launch.sql
SELECT plan(81);

SELECT diag('Chronostratigraphy level/parent update tests');

INSERT INTO chronostratigraphy (id, name, level_ref ) VALUES (1,'ÉLo Wÿorléds', 55);
INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (2, 'ÉLoWÿ', 56, 1);
INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (3, 'KÉLoWÿ', 56, 1);
INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (4, 'BÉLoWÿ', 58, 2);

SELECT ok('/' = (SELECT path FROM chronostratigraphy WHERE id = 1), 'Path of unit 1: /');
SELECT ok('/1/' = (SELECT path FROM chronostratigraphy WHERE id = 2), 'Path of unit 2: /1/');
SELECT ok('/1/' = (SELECT path FROM chronostratigraphy WHERE id = 3), 'Path of unit 3: /1/');
SELECT ok('/1/2/' = (SELECT path FROM chronostratigraphy WHERE id = 4), 'Path of unit 4: /1/2/');

SELECT lives_ok('UPDATE chronostratigraphy SET level_ref = 57, parent_ref = 3 WHERE id = 2', 'Level of unit 2 have been changed from 56 (era) to 57 (sub era) and attached to a new era - unit 3 -> allowed because direct children (unit 4 of system level) is attachable to a sub-era');

SELECT ok('/1/3/' = (SELECT path FROM chronostratigraphy WHERE id = 2), 'Path of unit 2: /1/3/');

SELECT ok('/1/3/2/' = (SELECT path FROM chronostratigraphy WHERE id = 4), 'Path of unit 4: /1/3/2/');

SELECT throws_ok('UPDATE chronostratigraphy SET level_ref = 58 WHERE id = 2');

SELECT diag('Lithostratigraphy level/parent update tests');

INSERT INTO lithostratigraphy (id,name, level_ref) VALUES (1, 'Méalo-nÿeø@ß€', 64);
INSERT INTO lithostratigraphy (id,name, level_ref, parent_ref) VALUES (2, 'Méalo-nÿeø@ß€A', 65, 1);
INSERT INTO lithostratigraphy (id,name, level_ref, parent_ref) VALUES (3, 'Méalo-nÿeø@ß€B', 65, 1);
INSERT INTO lithostratigraphy (id,name, level_ref, parent_ref) VALUES (4, 'Méalo-nÿeø@ß€C', 66, 3);

SELECT ok('/' = (SELECT path FROM lithostratigraphy WHERE id = 1), 'Path of unit 1: /');
SELECT ok('/1/' = (SELECT path FROM lithostratigraphy WHERE id = 2), 'Path of unit 2: /1/');
SELECT ok('/1/' = (SELECT path FROM lithostratigraphy WHERE id = 3), 'Path of unit 3: /1/');
SELECT ok('/1/3/' = (SELECT path FROM lithostratigraphy WHERE id = 4), 'Path of unit 4: /1/3/');

SELECT lives_ok('UPDATE lithostratigraphy SET level_ref = 66, parent_ref = 3 WHERE id = 2', 'Level of unit 2 have been changed from 65 (formation) to 66 (member) and attached to a new formation - unit 3 -> allowed because no children attached yet');

SELECT ok('/1/3/' = (SELECT path FROM lithostratigraphy WHERE id = 2), 'Path of unit 2: /1/3/');

SELECT lives_ok('UPDATE lithostratigraphy SET level_ref = 67, parent_ref = 2 WHERE id = 4', 'Level of unit 4 have been changed from 66 (member) to 67 (layer) and attached to a new member - unit 2 -> allowed because no children attached yet');

SELECT ok('/1/3/2/' = (SELECT path FROM lithostratigraphy WHERE id = 4), 'Path of unit 4: /1/3/2/');

SELECT throws_ok('UPDATE lithostratigraphy SET level_ref = 65, parent_ref = 1 WHERE id = 2');

SELECT diag('Lithology level/parent update tests');

INSERT INTO lithology (id,name, level_ref) VALUES (1, 'Méalo-nÿeø@ß€', 75);
INSERT INTO lithology (id,name, level_ref, parent_ref) VALUES (2, 'Méalo-nÿeø@ß€A', 76, 1);
INSERT INTO lithology (id,name, level_ref, parent_ref) VALUES (3, 'Méalo-nÿeø@ß€B', 76, 1);
INSERT INTO lithology (id,name, level_ref, parent_ref) VALUES (4, 'Méalo-nÿeø@ß€C', 77, 3);

SELECT ok('/' = (SELECT path FROM lithology WHERE id = 1), 'Path of unit 1: /');
SELECT ok('/1/' = (SELECT path FROM lithology WHERE id = 2), 'Path of unit 2: /1/');
SELECT ok('/1/' = (SELECT path FROM lithology WHERE id = 3), 'Path of unit 3: /1/');
SELECT ok('/1/3/' = (SELECT path FROM lithology WHERE id = 4), 'Path of unit 4: /1/3/');

SELECT lives_ok('UPDATE lithology SET level_ref = 77, parent_ref = 3 WHERE id = 2', 'Level of unit 2 have been changed from 76 (unit_group) to 77 (unit_sub_group) and attached to a new unit_group - unit 3 -> allowed because no children attached yet');


SELECT ok('/1/3/' = (SELECT path FROM lithology WHERE id = 2), 'Path of unit 2: /1/3/');

SELECT lives_ok('UPDATE lithology SET level_ref = 78, parent_ref = 2 WHERE id = 4', 'Level of unit 4 have been changed from 77 (unit_sub_group) to 78 (unit_rock) and attached to a new unit_sub_group - unit 2 -> allowed because no children attached yet');

SELECT ok('/1/3/2/' = (SELECT path FROM lithology WHERE id = 4), 'Path of unit 4: /1/3/2/');

SELECT lives_ok('UPDATE lithology SET level_ref = 76, parent_ref = 1 WHERE id = 2', 'Update of unit 2 have been changed from 77 (unit_sub_group) to 76 (unit_group) and attached tto a new unit main group - unit 1 -> allowed even if unit rock with id 4 is related to it - a rock can be directly linked to a unit group.');

SELECT ok('/1/' = (SELECT path FROM lithology WHERE id = 2), 'Path of unit 2: /1/');
SELECT ok('/1/2/' = (SELECT path FROM lithology WHERE id = 4), 'Path of unit 4: /1/2/');

SELECT diag('Mineralogy level/parent update tests');

INSERT INTO mineralogy (id,name, code, level_ref) VALUES (1, 'Méalo-nÿeø@ß€', 'A', 70);
INSERT INTO mineralogy (id,name, code, level_ref, parent_ref) VALUES (2, 'Méalo-nÿeø@ß€A', 'AA', 71, 1);
INSERT INTO mineralogy (id,name, code, level_ref, parent_ref) VALUES (3, 'Méalo-nÿeø@ß€B', 'B', 71, 1);
INSERT INTO mineralogy (id,name, code, level_ref, parent_ref) VALUES (4, 'Méalo-nÿeø@ß€C', 'C', 72, 3);

SELECT ok('/' = (SELECT path FROM mineralogy WHERE id = 1), 'Path of unit 1: /');
SELECT ok('/1/' = (SELECT path FROM mineralogy WHERE id = 2), 'Path of unit 2: /1/');
SELECT ok('/1/' = (SELECT path FROM mineralogy WHERE id = 3), 'Path of unit 3: /1/');
SELECT ok('/1/3/' = (SELECT path FROM mineralogy WHERE id = 4), 'Path of unit 4: /1/3/');

SELECT lives_ok('UPDATE mineralogy SET level_ref = 72, parent_ref = 3 WHERE id = 2', 'Level of unit 2 have been changed from 71 (unit_division) to 72 (unit_family) and attached to a new unit_division - unit 3 -> allowed because no children attached yet');

SELECT ok('/1/3/' = (SELECT path FROM mineralogy WHERE id = 2), 'Path of unit 2: /1/3/');

SELECT lives_ok('UPDATE mineralogy SET level_ref = 73, parent_ref = 2 WHERE id = 4', 'Level of unit 4 have been changed from 72 (unit_family) to 73 (unit_group) and attached to a new unit_family - unit 2 -> allowed because no children attached yet');

SELECT ok('/1/3/2/' = (SELECT path FROM mineralogy WHERE id = 4), 'Path of unit 4: /1/3/2/');

SELECT throws_ok('UPDATE mineralogy SET level_ref = 71, parent_ref = 1 WHERE id = 2');

SELECT diag('Taxonomy level/parent update tests');

INSERT INTO taxonomy (id, name, level_ref) VALUES (1, 'TOP', 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (2, 'A', 2, 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (3, 'AA', 4, 2);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (4, 'AAA', 12, 3);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (5, 'AAAA', 28, 4);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (6, 'AAAAA', 34, 5);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (7, 'AAAAAA', 41, 6);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (8, 'AAAAAAA', 42, 7);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (9, 'AAAAAAAA', 48, 8);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (10, 'AAAAAAAAA', 49, 9);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (11, 'B', 2, 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (12, 'C', 2, 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (13, 'CA', 4, 12);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (14, 'D', 2, 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (15, 'DA', 4, 14);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (16, 'DAA', 12, 15);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (17, 'E', 2, 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (18, 'EA', 4, 17);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (19, 'EAA', 12, 18);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (20, 'EAAA', 28, 19);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (21, 'KINGDOM', 2, 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (22, 'PHYLUM', 4, 21);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (23, 'CLASS', 12, 22);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (24, 'ORDER', 28, 23);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (25, 'FAMILY', 34, 24);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (26, 'GENUS', 41, 25);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (27, 'SUBGENUS', 42, 26);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (28, 'SECTIONBOTA', 43, 27);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (29, 'SUBSECTIONBOTA', 44, 28);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (30, 'SERIE', 45, 29);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (31, 'SUPERSPECIES', 47, 30);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (32, 'SPECIES', 48, 31);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (33, 'GENUSDUB', 41, 25);

SELECT ok('/' = (SELECT path FROM taxonomy WHERE id = 1), 'Path of unit 1: /');
SELECT ok('/1/' = (SELECT path FROM taxonomy WHERE id = 2), 'Path of unit 2: /1/');
SELECT ok('/1/2/' = (SELECT path FROM taxonomy WHERE id = 3), 'Path of unit 3: /1/2/');
SELECT ok('/1/2/3/' = (SELECT path FROM taxonomy WHERE id = 4), 'Path of unit 4: /1/2/3/');
SELECT ok('/1/2/3/4/' = (SELECT path FROM taxonomy WHERE id = 5), 'Path of unit 5: /1/2/3/4/');
SELECT ok('/1/2/3/4/5/' = (SELECT path FROM taxonomy WHERE id = 6), 'Path of unit 6: /1/2/3/4/5/');
SELECT ok('/1/2/3/4/5/6/' = (SELECT path FROM taxonomy WHERE id = 7), 'Path of unit 7: /1/2/3/4/5/6/');
SELECT ok('/1/2/3/4/5/6/7/' = (SELECT path FROM taxonomy WHERE id = 8), 'Path of unit 8: /1/2/3/4/5/6/7/');
SELECT ok('/1/2/3/4/5/6/7/8/' = (SELECT path FROM taxonomy WHERE id = 9), 'Path of unit 9: /1/2/3/4/5/6/7/8/');
SELECT ok('/1/2/3/4/5/6/7/8/9/' = (SELECT path FROM taxonomy WHERE id = 10), 'Path of unit 10: /1/2/3/4/5/6/7/8/9/');

SELECT lives_ok('UPDATE taxonomy SET parent_ref = 11 WHERE id = 3', 'Unit 3 (phylum) moved from parent unit 2 to parent unit 11');

SELECT ok('/1/' = (SELECT path FROM taxonomy WHERE id = 11), 'Path of unit 11: /1/');
SELECT ok('/1/11/' = (SELECT path FROM taxonomy WHERE id = 3), 'Path of unit 3: /1/11/');
SELECT ok('/1/11/3/' = (SELECT path FROM taxonomy WHERE id = 4), 'Path of unit 4: /1/11/3/');
SELECT ok('/1/11/3/4/' = (SELECT path FROM taxonomy WHERE id = 5), 'Path of unit 5: /1/11/3/4/');
SELECT ok('/1/11/3/4/5/' = (SELECT path FROM taxonomy WHERE id = 6), 'Path of unit 6: /1/11/3/4/5/');
SELECT ok('/1/11/3/4/5/6/' = (SELECT path FROM taxonomy WHERE id = 7), 'Path of unit 7: /1/11/3/4/5/6/');
SELECT ok('/1/11/3/4/5/6/7/' = (SELECT path FROM taxonomy WHERE id = 8), 'Path of unit 8: /1/11/3/4/5/6/7/');
SELECT ok('/1/11/3/4/5/6/7/8/' = (SELECT path FROM taxonomy WHERE id = 9), 'Path of unit 9: /1/11/3/4/5/6/7/8/');
SELECT ok('/1/11/3/4/5/6/7/8/9/' = (SELECT path FROM taxonomy WHERE id = 10), 'Path of unit 10: /1/11/3/4/5/6/7/8/9/');

SELECT lives_ok('UPDATE taxonomy SET parent_ref = 13 WHERE id = 4', 'Unit 4 (class) moved from parent unit 3 to parent unit 13');


SELECT lives_ok('UPDATE taxonomy SET parent_ref = 16 WHERE id = 5', 'Unit 5 moved from parent unit 4 to parent unit 16');

SELECT lives_ok('UPDATE taxonomy SET parent_ref = 20 WHERE id = 6', 'Unit 6 moved from parent unit 5 to parent unit 20');

SELECT lives_ok('UPDATE taxonomy SET level_ref = 41, parent_ref = 6 WHERE id = 8', 'Unit 8 moved from parent unit 7 to parent unit 6 and changed from level 42 (sub_genus) to level 41 (genus)');

SELECT lives_ok('UPDATE taxonomy SET name = ''NEOSUBGENUS'', level_ref = 42, parent_ref = 33 WHERE id = 29', 'Unit 29 moved from parent unit 28 to parent unit 33 and changed from level 44 (sub_section_botany) to level 42 (sub_genus)');

SELECT ok('/1/21/22/23/24/25/' = (SELECT path FROm taxonomy WHERE id = 33), 'Path of unit 33: /1/21/22/23/24/25/');
SELECT ok('/1/21/22/23/24/25/33/' = (SELECT path from taxonomy WHERE id = 29), 'Path of unit 29: /1/21/22/23/24/25/33/');
SELECT ok('/1/21/22/23/24/25/33/29/' = (SELECT path from taxonomy WHERE id = 30), 'Path of unit 30: /1/21/22/23/24/25/33/29/');
SELECT ok('/1/21/22/23/24/25/33/29/30/' = (SELECT path from taxonomy WHERE id = 31), 'Path of unit 31: /1/21/22/23/24/25/33/29/30/');
SELECT ok('/1/21/22/23/24/25/33/29/30/31/' = (SELECT path from taxonomy WHERE id = 32), 'Path of unit 32: /1/21/22/23/24/25/33/29/30/31/');

INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (34, 'THEDOMAIN', 1, null);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (35, 'THEKINGDOM', 2, 34);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (36, 'THESUPERPHYL', 3, 35);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (37, 'THEPHYLUM', 4, 36);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (38, 'THESUBPHYLUM', 5, 37);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (39, 'THECOHORTBOTANY', 8, 38);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (40, 'THESUBCOHORTBOTANY', 9, 39);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (41, 'THESUPERCLASS', 11, 40);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (42, 'THECLASS', 12, 41);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (43, 'THESECPHYLUM', 4, 36);

SELECT lives_ok('UPDATE taxonomy SET level_ref = 5, parent_ref = 43 WHERE id = 40', 'Unit 40 moved from parent unit 39 to parent unit 43 and changed from level 9 (sub_cohort_botany) to level 5 (sub_phylum)');

SELECT diag('Update Multimedia');

INSERT INTO multimedia(id, title) VALUES (1,'Testing');
INSERT INTO multimedia(id, title) VALUES (2,'TestingBis');
INSERT INTO multimedia(id, title,parent_ref) VALUES (3,'Testing child and parent',1);
INSERT INTO multimedia(id, title,parent_ref) VALUES (4,'Testing testing child',3);

SELECT ok('/' = (SELECT path from multimedia WHERE id=1), 'Path is right initialised');
SELECT ok('/' = (SELECT path from multimedia WHERE id=2), 'Path is right initialised with other');
SELECT ok('/1/' = (SELECT path from multimedia WHERE id=3), 'Path take parent_ref');
SELECT ok('/1/3/' = (SELECT path from multimedia WHERE id=4), 'Path add parent to previous path');

UPDATE multimedia SET parent_ref=2 WHERE id=3;

SELECT ok('/2/' = (SELECT path from multimedia WHERE id=3), 'Path is updated');
SELECT ok('/2/3/' = (SELECT path from multimedia WHERE id=4),'Path is updated for children too');

UPDATE multimedia SET parent_ref=NULL WHERE id=3;
SELECT ok('/' = (SELECT path from multimedia WHERE id=3),'Path is set to / with null parent');
SELECT ok('/3/' = (SELECT path from multimedia WHERE id=4), 'Childrens path is updated too');


SELECT diag('Update people_relationships path');
INSERT INTO people_relationships (person_1_ref, person_2_ref) VALUES (2,1);

SELECT ok('/2/' = (SELECT path FROM people_relationships where person_1_ref = 2 AND person_2_ref = 1 ));

DELETE FROM people_relationships where person_1_ref = 2 AND person_2_ref = 1;

INSERT INTO people_relationships (person_1_ref, person_2_ref) VALUES (1,2);

SELECT ok('/1/' = (SELECT path FROM people_relationships where person_1_ref = 1 AND person_2_ref = 2 ));

insert into people (id, is_physical, formated_name, formated_name_indexed, formated_name_ts, family_name, given_name, birth_date, gender, end_date)
VALUES (3, true, 'sdf', 'doesfdjohn', to_tsvector('simple', 'sd'), 'qsd', 'qsd', DATE 'June 20, 1989', 'M',DEFAULT);

INSERT INTO people_relationships (person_1_ref, person_2_ref) VALUES (2,3);

SELECT ok('/1/2/' = (SELECT path FROM people_relationships where person_1_ref = 2 AND person_2_ref = 3 ));

insert into people (id, is_physical, formated_name, formated_name_indexed, formated_name_ts, family_name, given_name, birth_date, gender, end_date) 
VALUES (4, true, 'Doe Jsssohn', 'sssss', to_tsvector('simple', 'Doe qsdqsd'), 'Dssoe', 'Johdn', DATE 'June 20, 1979', 'M', DEFAULT);
--, (id, is_physical, formated_name, family_name, given_name, birth_date, gender) VALUES (5,6, true, 'd f', 'sssvfddss', 'f', DATE 'June 20, 1979', 'M');

UPDATE people_relationships SET person_1_ref = 4 WHERE person_1_ref = 1 AND person_2_ref = 2;

SELECT ok('/4/' = (SELECT path FROM people_relationships where person_1_ref = 4 AND person_2_ref = 2 ));
SELECT ok('/4/2/' = (SELECT path FROM people_relationships where person_1_ref = 2 AND person_2_ref = 3 ));

SELECT * FROM finish();
ROLLBACK;
