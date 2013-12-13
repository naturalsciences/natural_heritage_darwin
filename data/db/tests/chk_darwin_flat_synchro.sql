\unset ECHO
\i unit_launch.sql
SELECT plan(47);

SELECT diag('Darwin flat synchro tests');

-- Insertion of catalogues data
INSERT INTO users(id, family_name, formated_name) VALUES (100000, 'Jos Chevremont', 'Jos Chevremont');
INSERT INTO users(id, family_name, formated_name) VALUES (100001, 'Paul Damblon', 'Paul Damblon');
INSERT INTO people(id, is_physical, sub_type, family_name, formated_name) VALUES (100002, false, 'Federal Institution', 'Institut des Cocinnelles', 'Institut des Cocinnelles');
INSERT INTO people(id, is_physical, sub_type, family_name, formated_name) VALUES (100003, false, 'ASBL', 'Centre d''écologie urbaine', 'Centre d''écologie urbaine');
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref) VALUES (100000, 'Bulots', 'Bulots', 100002, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100001, 'Bulots Af.', 'Bulots d''Afrique', 100002, 100000, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100002, 'Bulots As.', 'Bulots d''Asie', 100002, 100000, 100000);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref) VALUES (100004, 'Crétins', 'Crétins', 100003, 100001);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100005, 'Crétins EU', 'Crétins européens', 100003, 100001, 100004);
INSERT INTO collections(id, code, name, institution_ref, main_manager_ref, parent_ref) VALUES (100006, 'Crétins US.', 'Crétins américains', 100003, 100001, 100004);
INSERT INTO expeditions(id, name) VALUES (100000, 'Atlantic city 2010');
INSERT INTO expeditions(id, name) VALUES (100001, 'Bruxelles-Brussels');
INSERT INTO gtu(id, code) VALUES (100000, 'BELGO');
INSERT INTO gtu(id, code) VALUES (100001, 'Bxl');
INSERT INTO gtu(id, code) VALUES (100002, 'Brugge');
INSERT INTO tag_groups(id, gtu_ref, group_name, sub_group_name, tag_value) VALUES (100000, 100000, 'Administrative area', 'Country', 'Belgique;Belgium;Belgïe');
INSERT INTO tag_groups(id, gtu_ref, group_name, sub_group_name, tag_value) VALUES (100001, 100001, 'Administrative area', 'Country', 'Belgique;Belgium;Belgïe');
INSERT INTO tag_groups(id, gtu_ref, group_name, sub_group_name, tag_value) VALUES (100002, 100001, 'Administrative area', 'City', 'Bruxelles;Brussel;Brussels;Brüsel');
INSERT INTO tag_groups(id, gtu_ref, group_name, sub_group_name, tag_value) VALUES (100003, 100002, 'Administrative area', 'Country', 'Belgique;Belgium;Belgïe');
INSERT INTO tag_groups(id, gtu_ref, group_name, sub_group_name, tag_value) VALUES (100004, 100002, 'Administrative area', 'City', 'Brugge;Bruge');
INSERT INTO taxonomy(id, name, level_ref, parent_ref) (select 100000, 'Anicracra', 2, id from taxonomy where name = 'Eucaryota');
INSERT INTO taxonomy(id, name, level_ref, parent_ref) (select 100001, 'Aniblabla', 2, id from taxonomy where name = 'Eucaryota');
INSERT INTO chronostratigraphy(id, name, level_ref) VALUES (100000, 'Devotien', 55);
INSERT INTO chronostratigraphy(id, name, level_ref) VALUES (100001, 'Chronocouche', 55);
INSERT INTO lithostratigraphy(id, name, level_ref) VALUES (100000, 'Croute basse', 64);
INSERT INTO lithostratigraphy(id, name, level_ref) VALUES (100001, 'Lithocroute', 64);
INSERT INTO lithology(id, name, level_ref) VALUES (100000, 'Petits cailloux', 75);
INSERT INTO lithology(id, name, level_ref) VALUES (100001, 'Gros rochers', 75);
INSERT INTO mineralogy(id, code, name, level_ref) VALUES (100000, 'CAM1', 'Camion', 70);
INSERT INTO mineralogy(id, code, name, level_ref) VALUES (100001, 'ON2', 'Onion', 70);
INSERT INTO igs(id, ig_num) VALUES (100000, '240275');
INSERT INTO igs(id, ig_num) VALUES (100001, '240276');
-- Insertion of specimens using these data
INSERT INTO specimens (id, collection_ref, expedition_ref, gtu_ref, taxon_ref, chrono_ref, litho_ref, lithology_ref, mineral_ref)
       VALUES (1,100001,100000,100001,100000,100000,100000,100000,100000);
INSERT INTO specimens (id, collection_ref, expedition_ref, gtu_ref, taxon_ref, chrono_ref, litho_ref, lithology_ref, mineral_ref, ig_ref)
       VALUES (2,100005,100001,100002,100001,100001,100001,100001,100001,100001);

SELECT is('physical' , (SELECT category FROM specimens WHERE id = 1), 'It''s well a "physical" specimen.');
SELECT is('mix' , (SELECT collection_type FROM specimens WHERE id = 1), 'Collection referenced type is well "mix".');
SELECT is('Bulots Af.' , (SELECT collection_code FROM specimens WHERE id = 1), 'Collection referenced code is well "Bulots Af.".');
SELECT is('Atlantic city 2010' , (SELECT expedition_name FROM specimens WHERE id = 1), 'Expedition is well "Atlantic city 2010".');
SELECT is(ARRAY['belgium','belgie','belgique','brussel','bruxelles','brussels','brusel']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 1), 'Tag list is correct');
SELECT is('Belgique;Belgium;Belgïe' , (SELECT gtu_country_tag_value FROM specimens WHERE id = 1), 'Country Tag value is correct');
SELECT is('Anicracra' , (SELECT taxon_name FROM specimens WHERE id = 1), 'Taxon is well "Anicracra".');
SELECT is('Devotien' , (SELECT chrono_name FROM specimens WHERE id = 1), 'Chrono unit is well "Devotien".');
SELECT is('Croute basse' , (SELECT litho_name FROM specimens WHERE id = 1), 'Litho unit is well "Croute basse".');
SELECT is('Petits cailloux' , (SELECT lithology_name FROM specimens WHERE id = 1), 'Lithology unit is well "Petits cailloux".');
SELECT is('Camion' , (SELECT mineral_name FROM specimens WHERE id = 1), 'Mineral unit is well "Camion".');
SELECT is('0' , (SELECT coalesce(ig_num,'0') FROM specimens WHERE id = 1), 'No ig num for specimen 1.');

SELECT is('physical' , (SELECT category FROM specimens WHERE id = 2), 'It''s well a "physical" specimen.');
SELECT is(100005 , (SELECT collection_ref FROM specimens WHERE id = 2), 'Collection referenced is well "100002".');
SELECT is('mix' , (SELECT collection_type FROM specimens WHERE id = 2), 'Collection referenced type is well "mix".');
SELECT is('Crétins EU' , (SELECT collection_code FROM specimens WHERE id = 2), 'Collection referenced code is well "Crétins EU".');
SELECT is('Bruxelles-Brussels' , (SELECT expedition_name FROM specimens WHERE id = 2), 'Expedition is well "Bruxelles-Brussels".');
SELECT is(ARRAY['belgium','belgie','belgique','brugge','bruge']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 2), 'Tag list is correct');
SELECT is('Belgique;Belgium;Belgïe' , (SELECT gtu_country_tag_value FROM specimens WHERE id = 2), 'Country Tag value is correct');
SELECT is('Aniblabla' , (SELECT taxon_name FROM specimens WHERE id = 2), 'Taxon is well "Aniblabla".');
SELECT is('Chronocouche' , (SELECT chrono_name FROM specimens WHERE id = 2), 'Crhono unit is well "Chronocouche".');
SELECT is('Lithocroute' , (SELECT litho_name FROM specimens WHERE id = 2), 'Litho unit is well "Lithocroute".');
SELECT is('Gros rochers' , (SELECT lithology_name FROM specimens WHERE id = 2), 'Lithology unit is well "Petits cailloux".');
SELECT is('Onion' , (SELECT mineral_name FROM specimens WHERE id = 2), 'Mineral unit is well "Onion".');
SELECT is('240276' , (SELECT ig_num FROM specimens WHERE id = 2), 'ig num "240276" for specimen 2.');

-- UPDATE of collection manager data -> for users trigger check

UPDATE users SET family_name = 'Jojoba', formated_name = 'Jojoba' WHERE id = 1;
UPDATE users SET family_name = 'Caloulou', formated_name = 'Caloulou' WHERE id = 2;


-- UPDATE of collection institution data -> for people trigger check

UPDATE people SET sub_type = 'Small Institution' WHERE id = 100002;
UPDATE people SET family_name = 'ECOLO', formated_name = 'ECOLO' WHERE id = 100003;

-- UPDATE of 3 collections institution and main manager reference data -> for collections trigger check

UPDATE collections SET institution_ref = 100003, main_manager_ref = 100001 WHERE id = 1;

-- UPDATE of tag_value of cities for gtu 100001 -> should have no impact on gtu_country_tag_value but well on gtu_tag_values_indexed

UPDATE tag_groups SET tag_value = 'Liège;Luik;Lutig' WHERE gtu_ref = 100001 AND group_name_indexed = 'administrativearea' AND sub_group_name_indexed = 'city';

SELECT is(ARRAY['belgium','belgie','belgique','liege','lutig','luik']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 1), 'Tag list is correct');
SELECT is('Belgique;Belgium;Belgïe' , (SELECT gtu_country_tag_value FROM specimens WHERE id = 1), 'Country Tag value is correct');
SELECT is(ARRAY['belgium','belgie','belgique','brugge','bruge']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 2), 'Tag list is correct');
SELECT is('Belgique;Belgium;Belgïe' , (SELECT gtu_country_tag_value FROM specimens WHERE id = 2), 'Country Tag value is correct');

-- UPDATE of tag_value of country for gtu 100001 -> should have impact either on gtu_country_tag_value but well on gtu_tag_values_indexed too

UPDATE tag_groups SET tag_value = 'Belgique;Belgium;Belgïe;Belgo' WHERE gtu_ref = 100001 AND group_name_indexed = 'administrativearea' AND sub_group_name_indexed = 'country';

SELECT is(ARRAY['liege','lutig','luik','belgium','belgo','belgie','belgique']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 1), 'Tag list is correct');
SELECT is('Belgique;Belgium;Belgïe;Belgo' , (SELECT gtu_country_tag_value FROM specimens WHERE id = 1), 'Country Tag value is correct');
SELECT is(ARRAY['belgium','belgie','belgique','brugge','bruge']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 2), 'Tag list is correct');
SELECT is('Belgique;Belgium;Belgïe' , (SELECT gtu_country_tag_value FROM specimens WHERE id = 2), 'Country Tag value is correct');

-- Delete the country tag group for gtu 100001

DELETE FROM tag_groups WHERE gtu_ref = 100001 AND group_name_indexed = 'administrativearea' AND sub_group_name_indexed = 'country';

SELECT is(ARRAY['liege','lutig','luik']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 1), 'Tag list correctly updated');
SELECT is('0' , (SELECT coalesce(gtu_country_tag_value,'0') FROM specimens WHERE id = 1), 'Country Tag value correctly updated');

-- Reset country sub group of gtu 100001 to Belgique;Belgïe;Belgium

INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value) VALUES (100001, 'Administrative area', 'Country', 'Belgique;Belgium;Belgïe');

SELECT is(ARRAY['belgium','belgie','belgique','liege','lutig','luik']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 1), 'Tag list is correct');
SELECT is('Belgique;Belgium;Belgïe' , (SELECT gtu_country_tag_value FROM specimens WHERE id = 1), 'Country Tag value is correct');

-- Redelete the country tag group for gtu 100001 by updating the sub group value to an idot one

-- UPDATE tag_groups SET group_name = 'Topographic', sub_group_name = 'Landscape' WHERE gtu_ref = 100001 AND group_name_indexed = 'administrativearea' AND sub_group_name_indexed = 'country';
--
-- SELECT is(ARRAY['belgium','belgie','belgique','liege','lutig','luik']::varchar[] = (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 1), 'Tag list correctly updated');
-- SELECT is('0' = (SELECT coalesce(gtu_country_tag_value,'0') FROM specimens WHERE id = 1), 'Country Tag value correctly updated');

-- UPDATE of taxon name and extinct of taxon 100001

UPDATE taxonomy SET name = 'Gloubiboulga', extinct = true where id = 100000;

SELECT is(true , (SELECT taxon_extinct FROM specimens WHERE id = 1), 'Taxon of specimen 2 is now extinct');
SELECT is('Gloubiboulga' , (SELECT taxon_name FROM specimens WHERE id = 1), 'And its name is correct');

-- UPDATE of collection_ref, IG num, taxon_ref and gtu_ref of specimen 2

UPDATE specimens SET collection_ref = 100006, gtu_ref = 100000, taxon_ref = 100000, ig_ref = 100000 where id = 2;

SELECT is('Crétins US.' , (SELECT collection_code FROM specimens WHERE id = 2), 'Collection referenced code is well "Crétins US".');
SELECT is('Crétins américains' , (SELECT collection_name FROM specimens WHERE id = 2), 'Collection referenced name is well "Crétins américains".');
SELECT is(ARRAY['belgium','belgie','belgique']::varchar[] , (SELECT gtu_tag_values_indexed FROM specimens WHERE id = 2), 'Tag list is correct');
SELECT is('Belgique;Belgium;Belgïe' , (SELECT gtu_country_tag_value FROM specimens WHERE id = 2), 'Country Tag value is correct');
SELECT is('BELGO' , (SELECT gtu_code FROM specimens WHERE id = 2), 'The gtu has been well updated ;)');
SELECT is('Gloubiboulga' , (SELECT taxon_name FROM specimens WHERE id = 2), 'Taxon is well "Anicracra".');
SELECT is('240275' , (SELECT ig_num FROM specimens WHERE id = 2), 'ig num "240275" for specimen 2.');

-- Test delete of ig num -> set to null value in specimens

SELECT throws_ok('DELETE FROM igs where ig_num = ''240275''');


SELECT * FROM finish();
ROLLBACK;
