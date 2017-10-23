\unset ECHO
\i unit_launch.sql
SELECT plan(21);

INSERT INTO gtu(id,code) VALUES( 1, '001');
INSERT INTO gtu(id,code) VALUES( 2, '002');

select ok(0 = (select count(*) from tags) ,'There is currently no tags');

INSERT INTO tag_groups(id, gtu_ref, group_name, sub_group_name, tag_value) VALUES
    (1, 1, 'my_grp','another_grp','Brussels; Braine-l''alleud ; Bruxelles, test');

select ok(3 = (select count(*) from tags) ,'we add 3 tags');
select ok('brainelalleud' = (select tag_indexed from tags where group_ref=1 ORDER BY tag_indexed ASC LIMIT 1),'Tags are added with fulltoIndex form');
select ok('brussels' = (select tag_indexed from tags where group_ref=1 ORDER BY tag_indexed ASC LIMIT 1  OFFSET 1),'Tags are added with fulltoIndex form');
select ok('bruxellestest' = (select tag_indexed from tags where group_ref=1 ORDER BY tag_indexed ASC LIMIT 1 OFFSET 2),'Tags are added with fulltoIndex form');


UPDATE tag_groups SET tag_value = 'Brussels; Braine-l''alleud ; Bruxelles; test' WHERE id=1;

select ok(4 = (select count(*) from tags) ,'we add 1 tags');
select ok('brainelalleud' = (select tag_indexed from tags where group_ref=1 ORDER BY tag_indexed ASC LIMIT 1),'tag stays here');
select ok('brussels' = (select tag_indexed from tags where group_ref=1 ORDER BY tag_indexed ASC LIMIT 1  OFFSET 1),'tag stays here');
select ok('bruxelles' = (select tag_indexed from tags where group_ref=1 ORDER BY tag_indexed ASC LIMIT 1 OFFSET 2),'Tag is splitted');
select ok('test' = (select tag_indexed from tags where group_ref=1 ORDER BY tag_indexed ASC LIMIT 1 OFFSET 3),'Tag is splitted (2)');

INSERT INTO tag_groups(id, gtu_ref, group_name, sub_group_name, tag_value) VALUES
    (2, 1, 'my_grp','first_grp','Brussels; brol');

select ok(6 = (select count(*) from tags) ,'we add 2 tags');
select ok('brol' = (select tag_indexed from tags where group_ref=2 ORDER BY tag_indexed ASC LIMIT 1),'tag here');
select ok('brussels' = (select tag_indexed from tags where group_ref=2 ORDER BY tag_indexed ASC LIMIT 1 OFFSET 1),'tag here');

INSERT INTO tag_groups(id, gtu_ref, group_name, sub_group_name, tag_value) VALUES
    (3, 2, 'my_grp','first_grp','Brussels; brol');

select ok(8 = (select count(*) from tags) ,'we add 2 tags');
select ok('brol' = (select tag_indexed from tags where group_ref=3 ORDER BY tag_indexed ASC LIMIT 1),'tag here');
select ok('brussels' = (select tag_indexed from tags where group_ref=3 ORDER BY tag_indexed ASC LIMIT 1 OFFSET 1),'tag here');

DELETE FROM tag_groups WHERE id=3;
select ok(6 = (select count(*) from tags) ,'we deleted 2 tags');


SELECT lives_ok('INSERT INTO gtu(id, code, latitude, longitude, lat_long_accuracy) VALUES( 3, ''003'', 0,0 , 2);', 'test different lat/long accuracy');
SELECT lives_ok('INSERT INTO gtu(id, code, latitude, longitude, lat_long_accuracy) VALUES( 4, ''004'', 84 , 178 , 200);', 'test different lat/long accuracy');
SELECT lives_ok('INSERT INTO gtu(id, code, latitude, longitude, lat_long_accuracy) VALUES( 5, ''005'', 55 , 109 , 0.05);', 'test different lat/long accuracy');
SELECT lives_ok('INSERT INTO gtu(id, code, latitude, longitude, lat_long_accuracy) VALUES( 6, ''006'', -84,178 , 2000);', 'test different lat/long accuracy');

SELECT * FROM finish();
ROLLBACK;