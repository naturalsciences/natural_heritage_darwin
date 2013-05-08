\unset ECHO
\i unit_launch.sql
SELECT plan(7);

select diag('Find in array');
select ok(1 = fct_array_find('aa,bb,cc,dd','aa'::text), 'Trying with a string as table');
select ok(3 = fct_array_find('aa,bb,cc,dd','cc'::text), 'try more elements');
select ok(fct_array_find('a,b,c,d','ee'::text) is null, 'try unkown elements');
select ok(2 = fct_array_find(ARRAY['aa','bb','cc','dd'],'bb'::text), 'try with array');

SELECT diag('Remove a people');

INSERT INTO taxonomy (name, level_ref) VALUES ('Méàleis Gùbularis&', 1);

insert into people (id, is_physical, formated_name, formated_name_indexed, family_name, given_name, birth_date, gender, end_date) VALUES (3, true, 'sdf', 'doesfdjohn', 'qsd', 'qsd', DATE 'June 20, 1989', 'M', DEFAULT);
insert into people (id, is_physical, formated_name, formated_name_indexed, family_name, given_name, birth_date, gender, end_date) VALUES (4, true, 'Doe Jsssohn', 'sssss', 'Dssoe', 'Johdn', DATE 'June 20, 1979', 'M', DEFAULT);
insert into people (id, is_physical, formated_name, family_name, given_name, birth_date, gender) VALUES (5, true, 'd f', 'sssvfddss', 'f', DATE 'June 20, 1979', 'M');
INSERT INTO catalogue_people (id,referenced_relation, record_id, people_type, order_by, people_ref) (select 5,'taxonomy', id ,'expertise', 0 , 3 from taxonomy where name = 'Méàleis Gùbularis&');

INSERT INTO catalogue_people (id,referenced_relation, record_id,people_type,order_by, people_ref)
 VALUES
(7, 'catalogue_people', 5, 'defined_by',1,5),
(8, 'catalogue_people', 5, 'defined_by',2,4),
(9, 'catalogue_people', 5, 'defined_by',3,3);

SELECT ok(array[5,4,3] = array(SELECT people_ref FROM catalogue_people WHERE referenced_relation = 'catalogue_people' AND record_id = 5 ORDER BY order_by),'Check if the array is well defined');

DELETE FROM catalogue_people WHERE id = 7;

SELECT ok(array[4,3] = array(SELECT people_ref FROM catalogue_people WHERE referenced_relation = 'catalogue_people' AND record_id = 5 ORDER BY order_by),'Check if the persone who define is deleted');


DELETE FROM people WHERE id>2;
SELECT ok( 0 = (select count(*) from catalogue_people) ,'Check if they are all removed');

SELECT * FROM finish();
ROLLBACK;
