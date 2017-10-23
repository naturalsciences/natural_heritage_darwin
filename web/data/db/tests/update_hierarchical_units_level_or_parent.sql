\unset ECHO
\i unit_launch.sql
SELECT plan(73);

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

INSERT INTO taxonomy (id, name, level_ref) VALUES (10, 'TOP', 1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (20, 'A', 2, 10);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (30, 'AA', 4, 20);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (40, 'AAA', 12, 30);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (50, 'AAAA', 28, 40);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (60, 'AAAAA', 34, 50);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (70, 'AAAAAA', 41, 60);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (80, 'AAAAAAA', 42, 70);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (90, 'AAAAAAAA', 48, 80);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (100, 'AAAAAAAAA', 49, 90);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (110, 'B', 2, 10);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (120, 'C', 2, 10);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (130, 'CA', 4, 120);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (140, 'D', 2, 10);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (150, 'DA', 4, 140);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (160, 'DAA', 12, 150);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (170, 'E', 2, 10);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (180, 'EA', 4, 170);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (190, 'EAA', 12, 180);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (200, 'EAAA', 28, 190);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (210, 'KINGDOM', 2, 10);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (220, 'PHYLUM', 4, 210);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (230, 'CLASS', 12, 220);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (240, 'ORDER', 28, 230);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (250, 'FAMILY', 34, 240);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (260, 'GENUS', 41, 250);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (270, 'SUBGENUS', 42, 260);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (280, 'SECTIONBOTA', 43, 270);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (290, 'SUBSECTIONBOTA', 44, 280);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (300, 'SERIE', 45, 290);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (310, 'SUPERSPECIES', 47, 300);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (320, 'SPECIES', 48, 310);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (330, 'GENUSDUB', 41, 250);

SELECT ok('/' = (SELECT path FROM taxonomy WHERE id = 10), 'Path of unit 10: /');
SELECT ok('/10/' = (SELECT path FROM taxonomy WHERE id = 20), 'Path of unit 20: /10/');
SELECT ok('/10/20/' = (SELECT path FROM taxonomy WHERE id = 30), 'Path of unit 30: /10/20/');
SELECT ok('/10/20/30/' = (SELECT path FROM taxonomy WHERE id = 40), 'Path of unit 40: /10/20/30/');
SELECT ok('/10/20/30/40/' = (SELECT path FROM taxonomy WHERE id = 50), 'Path of unit 50: /10/20/30/40/');
SELECT ok('/10/20/30/40/50/' = (SELECT path FROM taxonomy WHERE id = 60), 'Path of unit 60: /10/20/30/40/50/');
SELECT ok('/10/20/30/40/50/60/' = (SELECT path FROM taxonomy WHERE id = 70), 'Path of unit 70: /10/20/30/40/50/60/');
SELECT ok('/10/20/30/40/50/60/70/' = (SELECT path FROM taxonomy WHERE id = 80), 'Path of unit 80: /10/20/30/40/50/60/70/');
SELECT ok('/10/20/30/40/50/60/70/80/' = (SELECT path FROM taxonomy WHERE id = 90), 'Path of unit 90: /10/20/30/40/50/60/70/80/');
SELECT ok('/10/20/30/40/50/60/70/80/90/' = (SELECT path FROM taxonomy WHERE id = 100), 'Path of unit 10: /10/20/30/40/50/60/70/80/90/');

SELECT lives_ok('UPDATE taxonomy SET parent_ref = 110 WHERE id = 30', 'Unit 30 (phylum) moved from parent unit 20 to parent unit 110');

SELECT ok('/10/' = (SELECT path FROM taxonomy WHERE id = 110), 'Path of unit 110: /10/');
SELECT ok('/10/110/' = (SELECT path FROM taxonomy WHERE id = 30), 'Path of unit 30: /10/110/');
SELECT ok('/10/110/30/' = (SELECT path FROM taxonomy WHERE id = 40), 'Path of unit 40: /10/110/30/');
SELECT ok('/10/110/30/40/' = (SELECT path FROM taxonomy WHERE id = 50), 'Path of unit 50: /10/110/30/40/');
SELECT ok('/10/110/30/40/50/' = (SELECT path FROM taxonomy WHERE id = 60), 'Path of unit 60: /10/110/30/40/50/');
SELECT ok('/10/110/30/40/50/60/' = (SELECT path FROM taxonomy WHERE id = 70), 'Path of unit 70: /10/110/30/40/50/60/');
SELECT ok('/10/110/30/40/50/60/70/' = (SELECT path FROM taxonomy WHERE id = 80), 'Path of unit 80: /10/110/30/40/50/60/70/');
SELECT ok('/10/110/30/40/50/60/70/80/' = (SELECT path FROM taxonomy WHERE id = 90), 'Path of unit 90: /10/110/30/40/50/60/70/80/');
SELECT ok('/10/110/30/40/50/60/70/80/90/' = (SELECT path FROM taxonomy WHERE id = 100), 'Path of unit 100: /10/110/30/40/50/60/70/80/90/');

SELECT lives_ok('UPDATE taxonomy SET parent_ref = 130 WHERE id = 40', 'Unit 40 (class) moved from parent unit 30 to parent unit 130');

SELECT lives_ok('UPDATE taxonomy SET parent_ref = 160 WHERE id = 50', 'Unit 50 moved from parent unit 40 to parent unit 160');

SELECT lives_ok('UPDATE taxonomy SET parent_ref = 200 WHERE id = 60', 'Unit 60 moved from parent unit 50 to parent unit 200');

SELECT lives_ok('UPDATE taxonomy SET level_ref = 41, parent_ref = 60 WHERE id = 80', 'Unit 80 moved from parent unit 70 to parent unit 60 and changed from level 42 (sub_genus) to level 41 (genus)');

SELECT lives_ok('UPDATE taxonomy SET name = ''NEOSUBGENUS'', level_ref = 42, parent_ref = 330 WHERE id = 290', 'Unit 290 moved from parent unit 280 to parent unit 330 and changed from level 44 (sub_section_botany) to level 42 (sub_genus)');

SELECT ok('/10/210/220/230/240/250/' = (SELECT path FROm taxonomy WHERE id = 330), 'Path of unit 33: /10/210/220/230/240/250/');
SELECT ok('/10/210/220/230/240/250/330/' = (SELECT path from taxonomy WHERE id = 290), 'Path of unit 29: /10/210/220/230/240/250/330/');
SELECT ok('/10/210/220/230/240/250/330/290/' = (SELECT path from taxonomy WHERE id = 300), 'Path of unit 30: /10/210/220/230/240/250/330/290/');
SELECT ok('/10/210/220/230/240/250/330/290/300/' = (SELECT path from taxonomy WHERE id = 310), 'Path of unit 31: /10/210/220/230/240/250/330/290/300/');
SELECT ok('/10/210/220/230/240/250/330/290/300/310/' = (SELECT path from taxonomy WHERE id = 320), 'Path of unit 32: /10/210/220/230/240/250/330/290/300/310/');

INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (340, 'THEDOMAIN', 1, null);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (350, 'THEKINGDOM', 2, 340);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (360, 'THESUPERPHYL', 3, 350);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (370, 'THEPHYLUM', 4, 360);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (380, 'THESUBPHYLUM', 5, 370);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (390, 'THECOHORTBOTANY', 8, 380);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (400, 'THESUBCOHORTBOTANY', 9, 390);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (410, 'THESUPERCLASS', 11, 400);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (420, 'THECLASS', 12, 410);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (430, 'THESECPHYLUM', 4, 360);

SELECT lives_ok('UPDATE taxonomy SET level_ref = 5, parent_ref = 430 WHERE id = 400', 'Unit 400 moved from parent unit 390 to parent unit 430 and changed from level 9 (sub_cohort_botany) to level 5 (sub_phylum)');


SELECT diag('Update people_relationships path');
INSERT INTO people_relationships (person_1_ref, person_2_ref) VALUES (2,1);

SELECT ok('/2/' = (SELECT path FROM people_relationships where person_1_ref = 2 AND person_2_ref = 1 ));

DELETE FROM people_relationships where person_1_ref = 2 AND person_2_ref = 1;

INSERT INTO people_relationships (person_1_ref, person_2_ref) VALUES (1,2);

SELECT ok('/1/' = (SELECT path FROM people_relationships where person_1_ref = 1 AND person_2_ref = 2 ));

insert into people (id, is_physical, formated_name, formated_name_indexed, family_name, given_name, birth_date, gender, end_date)
VALUES (3, true, 'sdf', 'doesfdjohn', 'qsd', 'qsd', DATE 'June 20, 1989', 'M',DEFAULT);

INSERT INTO people_relationships (person_1_ref, person_2_ref) VALUES (2,3);

SELECT ok('/1/2/' = (SELECT path FROM people_relationships where person_1_ref = 2 AND person_2_ref = 3 ));

insert into people (id, is_physical, formated_name, formated_name_indexed, family_name, given_name, birth_date, gender, end_date) 
VALUES (4, true, 'Doe Jsssohn', 'sssss', 'Dssoe', 'Johdn', DATE 'June 20, 1979', 'M', DEFAULT);
--, (id, is_physical, formated_name, family_name, given_name, birth_date, gender) VALUES (5,6, true, 'd f', 'sssvfddss', 'f', DATE 'June 20, 1979', 'M');

UPDATE people_relationships SET person_1_ref = 4 WHERE person_1_ref = 1 AND person_2_ref = 2;

SELECT ok('/4/' = (SELECT path FROM people_relationships where person_1_ref = 4 AND person_2_ref = 2 ));
SELECT ok('/4/2/' = (SELECT path FROM people_relationships where person_1_ref = 2 AND person_2_ref = 3 ));

SELECT * FROM finish();
ROLLBACK;
