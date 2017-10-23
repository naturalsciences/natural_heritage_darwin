create sequence staging_people_id_seq;

create table staging_people
       (
        id integer not null default nextval('staging_people_id_seq'),
        people_type varchar not null default 'author',
        people_sub_type varchar not null default '',
        order_by integer not null default 1,
        people_ref integer,
        formated_name varchar,
        constraint pk_staging_people primary key (id),
        constraint fk_staging_people_list_person foreign key (people_ref) references people(id) on delete cascade
       )
inherits (template_table_record_ref);
comment on table staging_people is 'List of people of staging units';
comment on column staging_people.id is 'Unique identifier of record';
comment on column staging_people.referenced_relation is 'Identifier-Name of table the units come from';
comment on column staging_people.record_id is 'Identifier of record concerned in table concerned';
comment on column staging_people.people_type is 'Type of "people" associated to the staging unit: authors, collectors, defined,  ...';
comment on column staging_people.people_sub_type is 'Type of "people" associated to the staging unit: Main author, corrector, taking the sense from,...';
comment on column staging_people.people_ref is 'Reference of person concerned - id field of people table';
comment on column staging_people.order_by is 'Integer used to order the persons in a list';
comment on column staging_people.formated_name is 'full name of the people';

CREATE INDEX CONCURRENTLY idx_staging_people_record ON staging_people(record_id,referenced_relation) ;

CREATE TRIGGER trg_upd_people_ref_staging_people AFTER UPDATE
        ON staging_people FOR EACH ROW
        EXECUTE PROCEDURE fct_upd_people_staging_fields();
        
