\unset ECHO
\i unit_launch.sql
SELECT plan(30);

SELECT diag('fct_cpy_path - Collection');

insert into collections  (id,code, name, institution_ref, main_manager_ref, parent_ref)
    VALUES
            (11,'arachno', 'Arachnomorphes', 1, 1, null),
            (12, 'amp', 'Amphibia', 1, 1, 11),
            (13, 'ave', 'Aves', 1, 1, 11),
            (14, 'fav', 'Fossile Aves', 1, 1, 13),
            (15, 'mol', 'Molusca', 1, 1, null);

SELECT ok('/' = (SELECT path FROM collections WHERE id = 11), 'Path of collections 1');
SELECT ok('/11/' = (SELECT path FROM collections WHERE id = 12), 'Path of collections 2');
SELECT ok('/11/' = (SELECT path FROM collections WHERE id = 13), 'Path of collections 3');
SELECT ok('/11/13/' = (SELECT path FROM collections WHERE id = 14), 'Path of collections 4');
SELECT ok('/' = (SELECT path FROM collections WHERE id = 15), 'Path of collections 5');

UPDATE collections SET parent_ref = 15 WHERE id=11;

SELECT ok('/15/' = (SELECT path FROM collections WHERE id = 11), 'Path of collections 1');
SELECT ok('/15/11/' = (SELECT path FROM collections WHERE id = 12), 'Path of collections 2');
SELECT ok('/15/11/' = (SELECT path FROM collections WHERE id = 13), 'Path of collections 3');
SELECT ok('/15/11/13/' = (SELECT path FROM collections WHERE id = 14), 'Path of collections 4');
SELECT ok('/' = (SELECT path FROM collections WHERE id = 15), 'Path of collections 5');
-- select id, path from collections;

UPDATE collections SET parent_ref = null  WHERE id=11;

SELECT ok('/' = (SELECT path FROM collections WHERE id = 11), 'Path of collections 1');
SELECT ok('/11/' = (SELECT path FROM collections WHERE id = 12), 'Path of collections 2');
SELECT ok('/11/' = (SELECT path FROM collections WHERE id = 13), 'Path of collections 3');
SELECT ok('/11/13/' = (SELECT path FROM collections WHERE id = 14), 'Path of collections 4');
SELECT ok('/' = (SELECT path FROM collections WHERE id = 15), 'Path of collections 5');
-- select id, path from collections;

SELECT diag('fct_cpy_path - Multimedia');

insert into multimedia  (id,title, parent_ref)
    VALUES
            (11,'une araignée', null),
            (12, 'sur le planché', 11),
            (13, 'se tricotait', 11),
            (14, 'des bottes', 13),
            (15, 'sur le plafond', null);
            
SELECT ok('/' = (SELECT path FROM multimedia WHERE id = 11), 'Path of multimedia 1');
SELECT ok('/11/' = (SELECT path FROM multimedia WHERE id = 12), 'Path of multimedia 2');
SELECT ok('/11/' = (SELECT path FROM multimedia WHERE id = 13), 'Path of multimedia 3');
SELECT ok('/11/13/' = (SELECT path FROM multimedia WHERE id = 14), 'Path of multimedia 4');
SELECT ok('/' = (SELECT path FROM multimedia WHERE id = 15), 'Path of multimedia 5');



UPDATE multimedia SET parent_ref = 15 WHERE id=11;

SELECT ok('/15/' = (SELECT path FROM multimedia WHERE id = 11), 'Path of multimedia 1');
SELECT ok('/15/11/' = (SELECT path FROM multimedia WHERE id = 12), 'Path of multimedia 2');
SELECT ok('/15/11/' = (SELECT path FROM multimedia WHERE id = 13), 'Path of multimedia 3');
SELECT ok('/15/11/13/' = (SELECT path FROM multimedia WHERE id = 14), 'Path of multimedia 4');
SELECT ok('/' = (SELECT path FROM multimedia WHERE id = 15), 'Path of multimedia 5');

UPDATE multimedia SET parent_ref = null  WHERE id=11;

SELECT ok('/' = (SELECT path FROM multimedia WHERE id = 11), 'Path of multimedia 1');
SELECT ok('/11/' = (SELECT path FROM multimedia WHERE id = 12), 'Path of multimedia 2');
SELECT ok('/11/' = (SELECT path FROM multimedia WHERE id = 13), 'Path of multimedia 3');
SELECT ok('/11/13/' = (SELECT path FROM multimedia WHERE id = 14), 'Path of multimedia 4');
SELECT ok('/' = (SELECT path FROM multimedia WHERE id = 15), 'Path of multimedia 5');

