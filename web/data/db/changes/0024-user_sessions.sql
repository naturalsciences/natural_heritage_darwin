SET search_path = darwin2, public;


alter table users_login_infos add column last_seen timestamp;

update users_login_infos l set last_seen = (select last_seen from users u where id = l.user_ref);
alter table users drop column last_seen;

\i ../createfunctions.sql
