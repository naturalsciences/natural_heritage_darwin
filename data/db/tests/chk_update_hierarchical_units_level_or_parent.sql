\unset ECHO
\i unit_launch.sql
SELECT plan(50);

SELECT diag('Chronostratigraphy level/parent update tests');

SELECT lives_ok('INSERT INTO chronostratigraphy (id, name, level_ref ) VALUES (1,''ÉLo Wÿorléds'', 55)', 'Insertion of unit 1 with level eon (55) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (2, ''ÉLoWÿ'', 56, 1)', 'Insertion of unit 2 with level era (56) linked to unit 1 with level eon (55) OK');
SELECT lives_ok('INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (3, ''BÉLoWÿ'', 58, 2)', 'Insertion of unit 3 with level system (58) linked to unit 2 with level era (56) OK');
INSERT INTO chronostratigraphy (id, name, level_ref, parent_ref) VALUES (4, 'KÉLoWÿ', 56, 1);

SELECT ok(true = (SELECT fct_chk_possible_upper_level('chronostratigraphy', 2, 57, 3)), 'Move unit 3 (of level 58 (system)) to level 57 (sub era) allowed -> parent is an era !');
SELECT ok(true = (SELECT fct_chk_possible_upper_level('chronostratigraphy', 4, 57, 3)), 'Move unit 3 (of level 58 (system)) to level 57 (sub era) and parent 4 (era) allowed !');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('chronostratigraphy', 2, 59, 3)), 'Move unit 3 (of level 58 (system)) to level 59 (serie) not allowed -> parent is an era !');
SELECT throws_ok('UPDATE chronostratigraphy SET level_ref = 59 WHERE id = 3', 'The modification of level and/or parent reference is not allowed, because unit modified won''t follow the rules of possible upper level attachement');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('chronostratigraphy', 1, 58, 3)), 'Move unit 3 to parent unit 1 (eon) not allowed -> A sub-era cannot be linked to an eon !');
SELECT throws_ok('UPDATE chronostratigraphy SET parent_ref = 1 WHERE id = 3', 'The modification of level and/or parent reference is not allowed, because unit modified won''t follow the rules of possible upper level attachement');

SELECT diag('Lithostratigraphy level/parent update tests');

SELECT lives_ok('INSERT INTO lithostratigraphy (id, name, level_ref ) VALUES (1,''ÉLo Wÿorléds'', 64)', 'Insertion of unit 1 with level group (64) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO lithostratigraphy (id, name, level_ref, parent_ref) VALUES (2, ''ÉLoWÿ'', 65, 1)', 'Insertion of unit 2 with level formation (65) linked to unit 1 with level group (64) OK');
SELECT lives_ok('INSERT INTO lithostratigraphy (id, name, level_ref, parent_ref) VALUES (3, ''BÉLoWÿ'', 66, 2)', 'Insertion of unit 3 with level member (66) linked to unit 2 with level formation (65) OK');
INSERT INTO lithostratigraphy (id, name, level_ref, parent_ref) VALUES (4, 'KÉLoWÿ', 65, 1);

SELECT ok(true = (SELECT fct_chk_possible_upper_level('lithostratigraphy', 4, 66, 3)), 'Move unit 3 (of level 66 (member)) to parent 4 (formation) allowed !');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('lithostratigraphy', 2, 67, 3)), 'Move unit 3 (of level 66 (member)) to level 67 (layer) not allowed -> parent is a formation !');
SELECT throws_ok('UPDATE lithostratigraphy SET level_ref = 67 WHERE id = 3', 'P0001');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('lithostratigraphy', 1, 66, 3)), 'Move unit 3 to parent unit 1 (group) not allowed -> A member cannot be linked to a group !');
SELECT throws_ok('UPDATE lithostratigraphy SET parent_ref = 1 WHERE id = 3', 'P0001');

SELECT diag('Lithology level/parent update tests');

SELECT lives_ok('INSERT INTO lithology (id, name, level_ref ) VALUES (1,''ÉLo Wÿorléds'', 75)', 'Insertion of unit 1 with level main group (75) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO lithology (id, name, level_ref, parent_ref) VALUES (2, ''ÉLoWÿ'', 76, 1)', 'Insertion of unit 2 with level group (76) linked to unit 1 with level main group (75) OK');
SELECT lives_ok('INSERT INTO lithology (id, name, level_ref, parent_ref) VALUES (3, ''BÉLoWÿ'', 77, 2)', 'Insertion of unit 3 with level sub-group (77) linked to unit 2 with level group (76) OK');
INSERT INTO lithology (id, name, level_ref, parent_ref) VALUES (4, 'KÉLoWÿ', 76, 1);

SELECT ok(true = (SELECT fct_chk_possible_upper_level('lithology', 4, 77, 3)), 'Move unit 3 (of level 77 (sub-group)) to parent 4 (group) allowed !');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('lithology', 2, 78, 3)), 'Move unit 3 (of level 77 (sub-group)) to level 78 (rock) not allowed -> parent is a group !');
SELECT throws_ok('UPDATE lithology SET level_ref = 78 WHERE id = 3', 'P0001');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('lithology', 1, 77, 3)), 'Move unit 3 to parent unit 1 (main group) not allowed -> A sub-group cannot be linked to a main group !');
SELECT throws_ok('UPDATE lithology SET parent_ref = 1 WHERE id = 3', 'P0001');

SELECT diag('Mineralogy level/parent update tests');

SELECT lives_ok('INSERT INTO mineralogy (id, code, name, level_ref ) VALUES (1, ''1'', ''ÉLo Wÿorléds'', 70)', 'Insertion of unit 1 with level class (70) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO mineralogy (id, code, name, level_ref, parent_ref) VALUES (2, ''2'', ''ÉLoWÿ'', 71, 1)', 'Insertion of unit 2 with level division (71) linked to unit 1 with level class (70) OK');
SELECT lives_ok('INSERT INTO mineralogy (id, code, name, level_ref, parent_ref) VALUES (3, ''3'', ''BÉLoWÿ'', 72, 2)', 'Insertion of unit 3 with level family (72) linked to unit 2 with level division (71) OK');
INSERT INTO mineralogy (id, code, name, level_ref, parent_ref) VALUES (4, '4', 'KÉLoWÿ', 71, 1);

SELECT ok(true = (SELECT fct_chk_possible_upper_level('mineralogy', 4, 72, 3)), 'Move unit 3 (of level 72 (family)) to parent 4 (division) allowed !');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('mineralogy', 2, 73, 3)), 'Move unit 3 (of level 72 (family)) to level 73 (group) not allowed -> parent is a division !');
SELECT throws_ok('UPDATE mineralogy SET level_ref = 73 WHERE id = 3', 'P0001');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('mineralogy', 1, 72, 3)), 'Move unit 3 to parent unit 1 (class) not allowed -> A family cannot be linked to a class !');
SELECT throws_ok('UPDATE mineralogy SET parent_ref = 1 WHERE id = 3', 'P0001');

SELECT diag('Taxonomy level/parent update tests');

SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref) VALUES (1, ''Méàleis Gùbularis&'', 1)', 'Insertion of unit 1 with level domain (1) linked to unit 0 (without any level) allowed !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (2, ''AMéàleis Gùbularis&'', 2, 1)', 'Insertion of unit 2 with level kingdom (2) linked to unit 1 with level domain (1) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (3, ''BMéàleis Gùbularis&'', 4, 2)', 'Insertion of unit 3 with level phylum (4) linked to unit 2 with level kingdom (2) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (4, ''CMéàleis Gùbularis&'', 12, 3)', 'Insertion of unit 4 with level class (12) linked to unit 3 with level phylum (4) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (5, ''DMéàleis Gùbularis&'', 28, 4)', 'Insertion of unit 5 with level order (28) linked to unit 4 with level class (12) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (6, ''EMéàleis Gùbularis&'', 34, 5)', 'Insertion of unit 6 with level family (34) linked to unit 5 with level order (28) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (7, ''FMéàleis Gùbularis&'', 41, 6)', 'Insertion of unit 7 with level genus (41) linked to unit 6 with level family (34) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (8, ''GMéàleis Gùbularis&'', 42, 7)', 'Insertion of unit 8 with level sub genus (42) linked to unit 7 with level genus (41) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (9, ''HMéàleis Gùbularis&'', 48, 8)', 'Insertion of unit 9 with level species (48) linked to unit 8 with level sub genus (42) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (10, ''IMéàleis Gùbularis&'', 49, 9)', 'Insertion of unit 10 with level sub species (49) linked to unit 9 with level species (48) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (11, ''TMéàleis Gùbularis&'', 12, 3)', 'Insertion of unit 11 with level class (12) linked to unit 3 with level phylum (4) is OK !');
SELECT lives_ok('INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (12, ''SMéàleis Gùbularis&'', 48, 7)', 'Insertion of unit 12 with level species (48) linked to unit 7 with level genus (42) is OK !');

SELECT ok(true = (SELECT fct_chk_possible_upper_level('taxonomy', 7, 45, 8)), 'Move unit 8 (of level 42 (sub genus)) to level 45 (serie) allowed ! -> parent is a genus');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('taxonomy', 7, 41, 8)), 'Move unit 8 (of level 42 (sub genus)) to level 41 (genus) not allowed -> parent is a genus itself !');
SELECT throws_ok('UPDATE taxonomy SET level_ref = 41 WHERE id = 8', 'P0001');
SELECT lives_ok('UPDATE taxonomy SET parent_ref = 11 WHERE id = 5', 'Change parent of unit 5 (with level order (24)) from parent 4 to parent 11 (both class levels (12)) -> OK!');
SELECT ok(false = (SELECT fct_chk_possible_upper_level('taxonomy', 7, 49, 12)), 'Move unit 12 (of level 48 (species)) to level 49 (sub species) NO allowed ! -> a sub species cannot be attached to a genus directly !');


SELECT * FROM finish();
ROLLBACK;
