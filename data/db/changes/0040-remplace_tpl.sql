begin;
set search_path=darwin2,public;

alter table people_relationships NO INHERIT template_people_users_rel_common;
alter table users_addresses NO INHERIT template_people_users_rel_common;

drop table template_test;


COMMIT;