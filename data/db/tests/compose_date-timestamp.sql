-- Testing the copy code for GTU
\unset ECHO
\i unit_launch.sql
SELECT plan(5);

SELECT diag('compose date FUNCTION');
SELECT ok(DATE '02-Jan-1999' = fct_compose_date(2,1,1999),'compose date with good params');
SELECT ok(DATE '02-Jan-0066' = fct_compose_date(2,1,66),'compose date in 66 AFTER JC');
SELECT throws_ok('fct_compose_date(31,24,1999)');
SELECT throws_ok('fct_compose_date(31,2,1999)');
SELECT throws_ok('fct_compose_date(0,2,1999)');


-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;