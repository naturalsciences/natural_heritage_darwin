-- ON SERV RUN:
-- AFTER MAKE INSTALL
--sudo -u postgres pg_dump darwin2 -h tyro -U cebmpad -t possible_upper_levels -t catalogue_relationships -t comments -t ext_links -t catalogue_properties -t properties_values -t vernacular_names -t expeditions -t classification_keywords -t classification_synonymies -t taxonomy  -t collecting_tools -t collecting_methods -t words --disable-triggers > demo.out
--sudo -u postgres psql darwin2

SET search_path = darwin2,public;
DELETE FROM possible_upper_levels;
DELETE FROM catalogue_relationships;
DELETE FROM comments;
DELETE FROM ext_links;
DELETE FROM catalogue_properties;
DELETE FROM properties_values;
DELETE FROM vernacular_names;
DELETE FROM expeditions;
DELETE FROM classification_keywords;
DELETE FROM classification_synonymies;
DELETE FROM taxonomy;
DELETE FROM collecting_tools;
DELETE FROM collecting_methods;
DELETE FROM words;


set session_replication_role=replica;
\i ./demo.out

\i /var/www/darwin/data/db/maintenance/sequences.sql
---
--THEN
---


DELETE FROM catalogue_relationships where referenced_relation != 'taxonomy';
DELETE FROM comments where referenced_relation != 'taxonomy';
DELETE FROM ext_links where referenced_relation != 'taxonomy';
DELETE FROM catalogue_properties where referenced_relation != 'taxonomy';
DELETE FROM vernacular_names where referenced_relation != 'taxonomy';
DELETE FROM classification_keywords where referenced_relation != 'taxonomy';
DELETE FROM classification_synonymies where referenced_relation != 'taxonomy';
DELETE FROM words where referenced_relation != 'taxonomy';

set session_replication_role=origin;

insert into users (id, is_physical,family_name, given_name, birth_date, gender)
 VALUES 
  (2,true, 'Register', 'Mister', DATE 'June 20, 1989', 'M'),
  (3,true, 'Encoder', 'Mister', DATE 'June 21, 1989', 'M'),
  (4,true, 'Manager', 'Mister', DATE 'June 22, 1989', 'M');

INSERT INTO users_login_infos (user_ref, user_name, password) 
VALUES
  (2, 'user', sha1('mySecret$alt' || 'user'::bytea)),
  (3, 'encoder', sha1('mySecret$alt' || 'encoder'::bytea)),
  (4, 'admin', sha1('mySecret$alt' || 'admin'::bytea));

insert into people (id, is_physical, family_name) VALUES
(1, false, 'A Demo');

insert into collections (id, code, name, institution_ref, main_manager_ref) values (1, 'demo', 'demo', 1, 4);
insert into collections_rights (collection_ref, user_ref, db_user_type) values (1, 3, 2);
insert into collections_rights (collection_ref, user_ref, db_user_type) values (1, 2, 1);



--- Run php symfony darwin:add-widget --reset 2
--- Run php symfony darwin:add-widget --reset 3
--- Run php symfony darwin:add-widget --reset 4


-- Then sudo -u postgres pg_dumpall -c -o -U postgres | gzip -c > /var/www/db_dumps/demo_db.gz

