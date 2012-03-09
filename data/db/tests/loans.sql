\unset ECHO
\i unit_launch.sql
SELECT plan(6);

SELECT diag('Loans');


set darwin.userid = 0;

insert into  loans (id,  name, description, from_date, to_date)
  VALUES (1, 'Test loan', '', now(), now()+'12 days'::interval);
SELECT is(0, (select count(*)::int from loan_status where loan_ref = 1), 'Status was not added');
SELECT is(0, (select count(*)::int from loan_rights where loan_ref = 1), 'User was not added');


select fct_set_user(1);

insert into  loans (id,  name, description, from_date, to_date)
  VALUES (2, 'Test loan', '', now(), now()+'12 days'::interval);

SELECT is(1, (select count(*)::int from loan_status where loan_ref = 2), 'Status was added');

INSERT INTO loan_status
      (loan_ref, user_ref, status, modification_date_time, comment, is_last)
      VALUES (2, 1, 'running', now(), 'test', true);

SELECT is(2, (select count(*)::int from loan_status where loan_ref = 2), 'Status was added');
SELECT is(1, (select count(*)::int from loan_status where loan_ref = 2 and is_last = true), 'only one is last');
SELECT is(1, (select count(*)::int from loan_rights where loan_ref = 2), 'User was added');


SELECT * FROM finish();
ROLLBACK;
