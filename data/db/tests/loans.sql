\unset ECHO
\i unit_launch.sql
SELECT plan(8);

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

INSERT INTO specimens (id, collection_ref) VALUES (1,1);
INSERT INTO specimen_individuals (id, specimen_ref, type) VALUES (1,1,'holotype');
INSERT INTO specimen_parts (id, specimen_individual_ref, specimen_part) VALUES (1, 1, 'head');

insert into  loan_items (id, loan_ref, part_ref)
  VALUES (1, 2 , 1);

SELECT throws_ok('DELETE FROM specimen_parts WHERE ID = 1');

INSERT INTO loan_status
      (loan_ref, user_ref, status, modification_date_time, comment, is_last)
      VALUES (2, 1, 'closed', now(), 'test', true);

SELECT lives_ok('DELETE FROM specimen_parts WHERE ID = 1');

SELECT * FROM finish();
ROLLBACK;
