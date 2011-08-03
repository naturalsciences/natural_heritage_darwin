-- Testing the copy code for GTU
\unset ECHO
\i unit_launch.sql
SELECT plan(2);

INSERT INTO  gtu (id,parent_ref) VALUES (1,0);
SELECT ok('1' = (SELECT code FROM gtu WHERE id=1),'Copy id to code when no code given');

INSERT INTO  gtu (id,parent_ref,code) VALUES (2,1,'bru66');
SELECT ok('bru66' = (SELECT code FROM gtu WHERE id=2),'Let the code if given');

-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;