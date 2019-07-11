create table db_version (
 id integer not null,
 update_at timestamp default now()
 
);

comment on table db_version is 'Table holding the database version and update date';

insert into db_version(id) values(31);
