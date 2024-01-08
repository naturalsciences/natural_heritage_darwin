--- USER LANG
alter table users add column selected_lang varchar not null default 'en';
comment on column users.selected_lang is 'Lang of the interface for the user en,fr,nl ,....';

update users u set selected_lang = (select language_country from users_languages where preferred_language = true and users_ref = u.id)
where exists ( select 1  from users_languages where preferred_language = true and users_ref = u.id);
-- INSTITUTION in parts
--ALTER table specimen_parts add column institution_ref integer;

-- update specimen_parts set institution_ref = 47859

alter table people add column name_formated_indexed text not null default '';
update people set name_formated_indexed = fulltoindex(coalesce(given_name,'') || coalesce(family_name,''));

alter table darwin_flat add column  institution_ref integer;

create sequence imports_id_seq;

create table imports
  (
    id integer not null default nextval('imports_id_seq'),
    user_ref integer not null,
    format varchar not null,
    collection_ref integer not null default 0,
    filename varchar not null,
    state varchar not null default '',
    created_at timestamp not null default now(),
    updated_at timestamp,
    initial_count integer not null default 0,
    is_finished boolean  not null default false,
    constraint pk_import primary key (id) ,
    constraint fk_imports_collections foreign key (collection_ref) references collections(id) on delete cascade,
    constraint fk_imports_users foreign key (user_ref) references users(id) on delete cascade
  );

comment on table imports is 'Table used to check the state of the date coming from an uploaded file';
comment on column imports.user_ref is 'The referenced user id';
comment on column imports.format is 'The import template to use for the imported file';
comment on column imports.filename is 'The filename of the file to proceed';
comment on column imports.collection_ref is 'The collection associated';
comment on column imports.state is 'the state of the processing the file';
comment on column imports.created_at is 'Creation of the file';
comment on column imports.updated_at is 'When the data has been modified lately';
comment on column imports.initial_count is 'Number of rows of staging when the import was created';
comment on column imports.is_finished is 'Boolean to mark if the import is finished or still need some operations';


create sequence staging_id_seq;

create table staging
  (
    id integer not null default nextval('staging_id_seq'),
    import_ref integer not null,
    parent_ref integer,
    path varchar,
    level varchar not null,
    spec_ref integer,
    category varchar,
    expedition_ref integer,
    expedition_name varchar,
    expedition_from_date date,
    expedition_from_date_mask integer,
    expedition_to_date date,
    expedition_to_date_mask integer,
    station_visible boolean,
    gtu_ref integer,
    gtu_code varchar,
    gtu_from_date_mask integer,
    gtu_from_date timestamp,
    gtu_to_date_mask integer,
    gtu_to_date timestamp,
    gtu_latitude float,
    gtu_longitude float,
    gtu_lat_long_accuracy float,
    gtu_elevation float,
    gtu_elevation_accuracy float,
    taxon_ref integer,
    taxon_name varchar,
    taxon_level_ref integer,
    taxon_level_name varchar,
    taxon_status varchar,
    taxon_extinct boolean,
    taxon_parents hstore,
    litho_ref integer,
    litho_name varchar,
    litho_level_ref integer,
    litho_level_name varchar,
    litho_status varchar,
    litho_local boolean,
    litho_color varchar,   
    litho_parents hstore,
    chrono_ref integer,
    chrono_name varchar,
    chrono_level_ref integer,
    chrono_level_name varchar,
    chrono_status varchar,
    chrono_local boolean,
    chrono_color varchar,
    chrono_upper_bound numeric(10,3),
    chrono_lower_bound numeric(10,3),
    chrono_parents hstore,
    lithology_ref integer,
    lithology_name varchar,
    lithology_level_ref integer,
    lithology_level_name varchar,
    lithology_status varchar,
    lithology_local boolean,
    lithology_color varchar,
    lithology_parents hstore,
    mineral_ref integer,
    mineral_name varchar,
    mineral_level_ref integer,
    mineral_level_name varchar,
    mineral_status varchar,
    mineral_local boolean,
    mineral_color varchar,
    mineral_path varchar,
    mineral_parents hstore,
    host_taxon_ref integer,
    host_relationship varchar,
    host_taxon_name varchar,
    host_taxon_level_ref integer,
    host_taxon_level_name varchar,
    host_taxon_status varchar,
    host_specimen_ref integer,
    ig_ref integer,
    ig_num varchar,
    ig_date_mask integer,
    ig_date date,
    acquisition_category varchar,
    acquisition_date_mask integer,
    acquisition_date date,
    individual_type varchar,
    individual_sex  varchar,
    individual_state varchar,
    individual_stage varchar,
    individual_social_status varchar,
    individual_rock_form varchar,
    individual_count_min integer,
    individual_count_max integer,
    part varchar,
    part_status varchar,

    institution_ref integer,
    institution_name varchar,
    building varchar,
    floor varchar,
    room varchar,
    row varchar,
    shelf varchar,
    container_type varchar,
    container_storage varchar,
    container varchar,
    sub_container_type varchar,
    sub_container_storage varchar,
    sub_container varchar,
    part_count_min integer,
    part_count_max integer,
    specimen_status varchar,
    complete boolean,
    surnumerary boolean,
    status hstore not null default '',
    to_import boolean default false,
    constraint pk_staging primary key (id),
    constraint fk_staging_import foreign key (import_ref) references imports(id) on delete cascade,
    constraint fk_parent_ref foreign key (parent_ref) references staging(id) on delete cascade
  );

create sequence staging_tag_groups_id_seq;

create table  staging_tag_groups
       (
        id bigint not null default nextval('staging_tag_groups_id_seq'),
        staging_ref integer not null,
        group_name varchar not null,
        sub_group_name varchar not null,
        tag_value varchar not null,
        constraint pk_staging_tag_groups primary key (id)
       );

comment on table staging_tag_groups is 'List of grouped tags for an imported row (copy of tag group)';
comment on column staging_tag_groups.id is 'Unique identifier of a grouped tag';
comment on column staging_tag_groups.staging_ref is 'Ref of an imported line';
comment on column staging_tag_groups.group_name is 'Group name under which the tag is grouped: Administrative area, Topographic structure,...';
comment on column staging_tag_groups.sub_group_name is 'Sub-Group name under which the tag is grouped: Country, River, Mountain,...';
comment on column staging_tag_groups.tag_value is 'Ensemble of Tags';



\i  ../createfunctions.sql

CREATE INDEX CONCURRENTLY idx_staging_import_ref ON staging(import_ref);
CREATE TRIGGER trg_cpy_path_staging BEFORE INSERT OR UPDATE
        ON staging FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_path();

CREATE TRIGGER trg_upd_fields_staging BEFORE UPDATE
        ON staging FOR EACH ROW
        EXECUTE PROCEDURE fct_upd_staging_fields();  
