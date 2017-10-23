\unset ECHO
\i unit_launch.sql
SELECT plan(49);

SELECT diag('Chronostratigraphy level/parent update tests');

SELECT lives_ok('INSERT INTO chronostratigraphy (id, name, level_ref ) VALUES (1,''ÉLo Wÿorléds'', 55)', 'Insertion of unit 1 with level eon (55) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (2, ''ÉLoWÿ'', 56, 1)', 'Insertion of unit 2 with level era (56) linked to unit 1 with level eon (55) OK');
SELECT lives_ok('INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (3, ''BÉLoWÿ'', 58, 2)', 'Insertion of unit 3 with level system (58) linked to unit 2 with level era (56) OK');
INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (4, 'KÉLoWÿ', 56, 1);

SELECT ok(true = (SELECT fct_chk_possible_upper_level('chronostratigraphy', 2, 57, 3)), 'Move unit 3 (of level 58 (system)) to level 57 (sub era) allowed -> parent is an era !');
SELECT ok(true = (SELECT fct_chk_possible_upper_level('chronostratigraphy', 4, 57, 3)), 'Move unit 3 (of level 58 (system)) to level 57 (sub era) and parent 4 (era) allowed !');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('chronostratigraphy', 2, 59, 3)), 'Move unit 3 (of level 58 (system)) to level 59 (serie) not allowed -> parent is an era !');
SELECT throws_ok('UPDATE chronostratigraphy SET level_ref = 59 WHERE id = 3');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('chronostratigraphy', 1, 58, 3)), 'Move unit 3 to parent unit 1 (eon) not allowed -> A sub-era cannot be linked to an eon !');
SELECT throws_ok('UPDATE chronostratigraphy SET parent_ref = 1 WHERE id = 3');

SELECT diag('Lithostratigraphy level/parent update tests');

SELECT lives_ok('INSERT INTO lithostratigraphy (id, name, level_ref ) VALUES (1,''ÉLo Wÿorléds'', 64)', 'Insertion of unit 1 with level group (64) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO lithostratigraphy (id, name, level_ref, parent_ref) VALUES (2, ''ÉLoWÿ'', 65, 1)', 'Insertion of unit 2 with level formation (65) linked to unit 1 with level group (64) OK');
SELECT lives_ok('INSERT INTO lithostratigraphy (id, name, level_ref, parent_ref) VALUES (3, ''BÉLoWÿ'', 66, 2)', 'Insertion of unit 3 with level member (66) linked to unit 2 with level formation (65) OK');
INSERT INTO lithostratigraphy (id, name, level_ref, parent_ref) VALUES (4, 'KÉLoWÿ', 65, 1);

SELECT ok(true = (SELECT fct_chk_possible_upper_level('lithostratigraphy', 4, 66, 3)), 'Move unit 3 (of level 66 (member)) to parent 4 (formation) allowed !');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('lithostratigraphy', 2, 67, 3)), 'Move unit 3 (of level 66 (member)) to level 67 (layer) not allowed -> parent is a formation !');
SELECT throws_ok('UPDATE lithostratigraphy SET level_ref = 67 WHERE id = 3');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('lithostratigraphy', 1, 66, 3)), 'Move unit 3 to parent unit 1 (group) not allowed -> A member cannot be linked to a group !');
SELECT throws_ok('UPDATE lithostratigraphy SET parent_ref = 1 WHERE id = 3');

SELECT diag('Lithology level/parent update tests');

SELECT lives_ok('INSERT INTO lithology (id, name, level_ref ) VALUES (1,''ÉLo Wÿorléds'', 75)', 'Insertion of unit 1 with level main group (75) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO lithology (id, name, level_ref, parent_ref) VALUES (2, ''ÉLoWÿ'', 76, 1)', 'Insertion of unit 2 with level group (76) linked to unit 1 with level main group (75) OK');
SELECT lives_ok('INSERT INTO lithology (id, name, level_ref, parent_ref) VALUES (3, ''BÉLoWÿ'', 77, 2)', 'Insertion of unit 3 with level sub-group (77) linked to unit 2 with level group (76) OK');
INSERT INTO lithology (id, name, level_ref, parent_ref) VALUES (4, 'KÉLoWÿ', 76, 1);

SELECT ok(true = (SELECT fct_chk_possible_upper_level('lithology', 4, 77, 3)), 'Move unit 3 (of level 77 (sub-group)) to parent 4 (group) allowed !');
SELECT ok(true = (SELECT fct_chk_possible_upper_level('lithology', 2, 78, 3)), 'Move unit 3 (of level 77 (sub-group)) to level 78 (rock) allowed !');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('lithology', 1, 77, 3)), 'Move unit 3 to parent unit 1 (main group) not allowed -> A sub-group cannot be linked to a main group !');
SELECT throws_ok('UPDATE lithology SET parent_ref = 1 WHERE id = 3');

SELECT diag('Mineralogy level/parent update tests');

SELECT lives_ok('INSERT INTO mineralogy (id, code, name, level_ref ) VALUES (1, ''1'', ''ÉLo Wÿorléds'', 70)', 'Insertion of unit 1 with level class (70) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO mineralogy (id, code, name, level_ref, parent_ref) VALUES (2, ''2'', ''ÉLoWÿ'', 71, 1)', 'Insertion of unit 2 with level division (71) linked to unit 1 with level class (70) OK');
SELECT lives_ok('INSERT INTO mineralogy (id, code, name, level_ref, parent_ref) VALUES (3, ''3'', ''BÉLoWÿ'', 72, 2)', 'Insertion of unit 3 with level family (72) linked to unit 2 with level division (71) OK');
INSERT INTO mineralogy (id, code, name, level_ref, parent_ref) VALUES (4, '4', 'KÉLoWÿ', 71, 1);

SELECT ok(true = (SELECT fct_chk_possible_upper_level('mineralogy', 4, 72, 3)), 'Move unit 3 (of level 72 (family)) to parent 4 (division) allowed !');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('mineralogy', 2, 73, 3)), 'Move unit 3 (of level 72 (family)) to level 73 (group) not allowed -> parent is a division !');
SELECT throws_ok('UPDATE mineralogy SET level_ref = 73 WHERE id = 3');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('mineralogy', 1, 72, 3)), 'Move unit 3 to parent unit 1 (class) not allowed -> A family cannot be linked to a class !');
SELECT throws_ok('UPDATE mineralogy SET parent_ref = 1 WHERE id = 3');

SELECT diag('Taxonomy level/parent update tests');

SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref) VALUES (10, ''Méàleis Gùbularis&'', 1)', 'Insertion of unit 1 with level domain (1) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (20, ''AMéàleis Gùbularis&'', 2, 10)', 'Insertion of unit 20 with level kingdom (2) linked to unit 10 with level domain (1) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (30, ''BMéàleis Gùbularis&'', 4, 20)', 'Insertion of unit 30 with level phylum (4) linked to unit 20 with level kingdom (2) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (40, ''CMéàleis Gùbularis&'', 12, 30)', 'Insertion of unit 40 with level class (12) linked to unit 30 with level phylum (4) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (50, ''DMéàleis Gùbularis&'', 28, 40)', 'Insertion of unit 50 with level order (28) linked to unit 40 with level class (12) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (60, ''EMéàleis Gùbularis&'', 34, 50)', 'Insertion of unit 60 with level family (34) linked to unit 50 with level order (28) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (70, ''FMéàleis Gùbularis&'', 41, 60)', 'Insertion of unit 70 with level genus (41) linked to unit 60 with level family (34) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (80, ''GMéàleis Gùbularis&'', 42, 70)', 'Insertion of unit 80 with level sub genus (42) linked to unit 70 with level genus (41) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (90, ''HMéàleis Gùbularis&'', 48, 80)', 'Insertion of unit 90 with level species (48) linked to unit 80 with level sub genus (42) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (100, ''IMéàleis Gùbularis&'', 49, 90)', 'Insertion of unit 100 with level sub species (49) linked to unit 90 with level species (48) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (110, ''TMéàleis Gùbularis&'', 12, 30)', 'Insertion of unit 110 with level class (12) linked to unit 30 with level phylum (4) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (120, ''SMéàleis Gùbularis&'', 48, 70)', 'Insertion of unit 120 with level species (48) linked to unit 70 with level genus (42) is OK !');

SELECT ok(true = (SELECT fct_chk_possible_upper_level('taxonomy', 70, 45, 80)), 'Move unit 80 (of level 42 (sub genus)) to level 45 (serie) allowed ! -> parent is a genus');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('taxonomy', 70, 41, 80)), 'Move unit 80 (of level 42 (sub genus)) to level 41 (genus) not allowed -> parent is a genus itself !');
SELECT throws_ok('UPDATE taxonomy SET level_ref = 41 WHERE id = 80');
SELECT lives_ok('UPDATE taxonomy SET parent_ref = 110 WHERE id = 50');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('taxonomy', 70, 49, 120)), 'Move unit 120 (of level 48 (species)) to level 49 (sub species) not allowed ! -> a sub species cannot be attached to a genus directly !');


SELECT * FROM finish();
ROLLBACK;
