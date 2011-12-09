\unset ECHO
\i unit_launch.sql
SELECT plan(3);

SELECT diag('Loans');
select fct_set_user(1);

insert into  loans (id,  name, description, from_date, to_date, effective_to_date)
  VALUES (1, 'Test loan', '', now(), now()+'12 days'::interval, null);

SELECT is(1, (select count(*)::int from loan_status where loan_ref = 1), 'Status was added');

INSERT INTO loan_status
      (loan_ref, user_ref, status, modification_date_time, comment, is_last)
      VALUES (1, 1, 'running', now(), 'test', true);

SELECT is(2, (select count(*)::int from loan_status where loan_ref = 1), 'Status was added');
SELECT is(1, (select count(*)::int from loan_status where loan_ref = 1 and is_last = true), 'Status was added');

-- select id, path from collections;


SELECT * FROM finish();
ROLLBACK;
