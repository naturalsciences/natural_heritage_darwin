begin;
set search_path=darwin2,public;

alter table people_relationships NO INHERIT template_people_users_rel_common;
alter table users_addresses NO INHERIT template_people_users_rel_common;

drop table template_people_users_rel_common;


alter table collections_rights NO INHERIT template_collections_users;

drop table template_collections_users;


alter table people_languages NO INHERIT template_people_languages;

drop table template_people_languages;

COMMIT;
