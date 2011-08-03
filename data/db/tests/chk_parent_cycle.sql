-- Testing that only one preferred language is provided for people/users
\unset ECHO
\i unit_launch.sql
SELECT plan(9);

SELECT ok(fct_chk_onceInPath('/1/2/3/') = true , 'is only once');
SELECT ok(fct_chk_onceInPath('/1/2/1/') = false , 'is twice');
SELECT ok(fct_chk_onceInPath('/1/1/1/') = false , 'is many times');
SELECT ok(fct_chk_onceInPath('/1/2/3') = true , 'is once without trailing /');
SELECT ok(fct_chk_onceInPath('/1/2/1') = false , 'is twice without trailing /');


SELECT lives_ok('INSERT INTO multimedia(id, title) VALUES (1,''Testing'');');
SELECT lives_ok('INSERT INTO multimedia(id, title,parent_ref) VALUES (2,''Testing child and parent'',1);');
SELECT lives_ok('INSERT INTO multimedia(id, title,parent_ref) VALUES (3,''Testing testing child'',2);');

SELECT throws_ok('UPDATE multimedia SET parent_ref=3 WHERE id=1;');



