\i ../createfunctions.sql


ALTER TABLE RENAME multimedia TO old_multimedia;

create sequence multimedia_id_seq;

create table multimedia
       (
        id integer not null default nextval('multimedia_id_seq'),
        is_digital boolean not null default true,
        type varchar not null default 'image',
        sub_type varchar,
        title varchar not null,
        description varchar not null default '',
        uri varchar,
        filename varchar,
        search_ts tsvector not null,
        creation_date date not null default '01/01/0001',
        creation_date_mask integer not null default 0,
        mime_type varchar not null,
        constraint pk_multimedia primary key (id)
      )
      inherits (template_table_record_ref);

comment on table multimedia is 'Stores all multimedia objects encoded in DaRWIN 2.0';
comment on column multimedia.referenced_relation is 'Reference-Name of table concerned';
comment on column multimedia.record_id is 'Identifier of record concerned';
comment on column multimedia.id is 'Unique identifier of a multimedia object';
comment on column multimedia.is_digital is 'Flag telling if the object is digital (true) or physical (false)';
comment on column multimedia.type is 'Main multimedia object type: image, sound, video,...';
comment on column multimedia.sub_type is 'Characterization of object type: article, publication in serie, book, glass plate,...';
comment on column multimedia.title is 'Title of the multimedia object';
comment on column multimedia.description is 'Description of the current object';
comment on column multimedia.uri is 'URI of object if digital';
comment on column multimedia.filename is 'The original name of the saved file';
comment on column multimedia.creation_date is 'Object creation date';
comment on column multimedia.creation_date_mask is 'Mask used for object creation date display';
comment on column multimedia.search_ts is 'tsvector form of title and subject fields together';
comment on column multimedia.mime_type is 'Mime/Type of the linked digital object';


INSERT INTO multimedia(is_digital,type, sub_type, title, description, uri, filename, creation_date, creation_date_mask, mime_type)
AS (
SELECT is_digital,type, sub_type, title, subject,  uri, '', creation_date, creation_date_mask, ''
FROM old_multimedia
);

DROP TABLE old_multimedia;


ALTER TABLE insurances add column date_from_mask integer not null default 0;
ALTER TABLE insurances add column date_from date not null default '01/01/0001';
ALTER TABLE insurances add column date_to_mask integer not null default 0;
ALTER TABLE insurances add column date_to date not null default '31/12/2038';
ALTER TABLE insurances add column contact_ref integer;

ALTER TABLE insurances DROP constraint unq_specimen_parts_insurance;

UPDATE insurances set date_from = '01/01/' || insurance_year, date_to = '01/01/' || insurance_year, date_from_mask = 32, date_to_mask = 32
where insurance_year != 0;


ALTER TABLE insurances ADD constraint unq_specimen_parts_insurances unique (referenced_relation, record_id, date_from, date_to, insurer_ref);
ALTER TABLE insurances ADD constraint fk_specimen_parts_insurances_contact foreign key (contact_ref) references people(id) on delete set null;

ALTER TABLE insurances DROP column insurance_year;



create sequence loans_id_seq;

create table loans (
  id integer not null default nextval('loans_id_seq'),
  name varchar not null default '',
  description varchar not null default '',
  description_ts tsvector not null,
  from_date date,
  to_date date,
  effective_to_date date,
  extended_to_date date,
  constraint pk_loans primary key (id)
  );

comment on table loans is 'Table holding an entire loan made of multiple loan items may also be linked to other table as comment, properties , ...';

comment on column loans.id is 'Unique identifier of record';
comment on column loans.name is 'Global name of the loan. May be a sort of code of other naming scheme';
comment on column loans.description is 'Description of the meaning of the loan';
comment on column loans.description_ts is 'tsvector getting Description and title of the loan';
comment on column loans.from_date  is 'Date of the start of the loan';
comment on column loans.to_date  is 'Planned date of the end of the loan';
comment on column loans.effective_to_date is 'Effective end date of the loan or null if it''s running';


  
create sequence loan_items_id_seq;

create table loan_items (
  id integer not null default nextval('loan_items_id_seq'),
  loan_ref integer not null,
  ig_ref integer,
  from_date date,
  to_date date,
  part_ref integer,
  details varchar default '',
  
  constraint pk_loan_items primary key (id),
  constraint fk_loan_items_ig foreign key (ig_ref) references igs(id),
  constraint fk_loan_items_loan_ref foreign key (loan_ref) references loans(id),
  constraint fk_loan_items_part_ref foreign key (part_ref) references specimen_parts(id) on delete set null,

  constraint unq_loan_items unique(loan_ref, part_ref)
); 


comment on table loan_items is 'Table holding an item of a loan. It may be a part from darwin or only an generic item';

comment on column loan_items.id is 'Unique identifier of record';
comment on column loan_items.loan_ref is 'Mandatory Reference to a loan';
comment on column loan_items.from_date is 'Date when the item was sended';
comment on column loan_items.to_date is 'Date when the item was recieved back';
comment on column loan_items.ig_ref is 'Optional ref to an IG stored in the igs table';
comment on column loan_items.part_ref is 'Optional reference to a Darwin Part';
comment on column loan_items.details is 'Textual details describing the item';


create sequence loan_rights_id_seq;

create table loan_rights (
  id integer not null default nextval('loan_rights_id_seq'),
  loan_ref integer not null,
  user_ref integer not null,
  has_encoding_right boolean not null default false,

  constraint pk_loan_rights primary key (id),
  constraint fk_loan_rights_loan_ref foreign key (loan_ref) references loans(id) on delete cascade,
  constraint fk_loan_rights_user_ref foreign key (user_ref) references users(id) on delete cascade,
  constraint unq_loan_rights unique (loan_ref, user_ref)
);


comment on table loan_rights is 'Table describing rights into an entire loan (if user is in the table he has at least viewing rights)';

comment on column loan_rights.id is 'Unique identifier of record';
comment on column loan_rights.loan_ref is 'Mandatory Reference to a loan';
comment on column loan_rights.user_ref is 'Mandatory Reference to a user';
comment on column loan_rights.has_encoding_right is 'Bool saying if the user can edit a loan';




create sequence loan_status_id_seq;

create table loan_status (
  id integer not null default nextval('loan_status_id_seq'),
  loan_ref integer not null,
  user_ref integer not null,
  status varchar not null default 'new',
  modification_date_time update_date_time,
  comment varchar not null default '',
  is_last boolean not null default true,
  constraint pk_loan_status primary key (id),
  constraint fk_loan_status_loan_ref foreign key (loan_ref) references loans(id) on delete cascade,
  constraint fk_loan_status_user_ref foreign key (user_ref) references users(id) on delete cascade

);

comment on table loan_status is 'Table describing various states of a loan';

comment on column loan_status.id is 'Unique identifier of record';
comment on column loan_status.loan_ref is 'Mandatory Reference to a loan';
comment on column loan_status.user_ref is 'Mandatory Reference to a user';
comment on column loan_status.status is 'Current status of the loan in a list (new, closed, running, ...)';
comment on column loan_status.modification_date_time is 'date of the modification';
comment on column loan_status.comment is 'comment of the status modification';
comment on column loan_status.is_last is 'flag telling which line is the current line';







DROP TRIGGER trg_cpy_toFullText_multimedia on multimedia


CREATE TRIGGER trg_cpy_fullToIndex_loans BEFORE INSERT OR UPDATE
  ON loans FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_clr_referenceRecord_loans AFTER DELETE
        ON loans FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_clr_referenceRecord_loan_items AFTER DELETE
        ON loan_items FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();


 CREATE TRIGGER trg_cpy_toFullText_multimedia BEFORE INSERT OR UPDATE
       ON multimedia FOR EACH ROW
       EXECUTE PROCEDURE tsvector_update_trigger(search_ts, 'pg_catalog.simple', title);

CREATE TRIGGER trg_words_ts_cpy_loans BEFORE INSERT OR UPDATE
        ON loans FOR EACH ROW
        EXECUTE PROCEDURE fct_trg_word();


CREATE TRIGGER fct_cpy_trg_ins_update_dict_loan_status AFTER INSERT OR UPDATE
        ON loan_status FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();


CREATE TRIGGER fct_cpy_trg_del_dict_loan_status AFTER DELETE OR UPDATE
        ON loan_status FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();


CREATE trigger trg_chk_is_last_loan_status BEFORE INSERT
        ON loan_status FOR EACH ROW
        EXECUTE PROCEDURE fct_remove_last_flag_loan();

CREATE trigger trg_add_status_history after INSERT
        ON loans FOR EACH ROW
        EXECUTE PROCEDURE fct_auto_insert_status_history();














DROP INDEX IF EXISTS idx_users_workflow_user_ref;
DROP INDEX IF EXISTS idx_collections_collection_type;
DROP INDEX IF EXISTS idx_collections_collection_name;
DROP INDEX IF EXISTS idx_collection_name_indexed;
DROP INDEX IF EXISTS idx_insurances_insurance_year;
DROP INDEX IF EXISTS idx_multimedia_ref;
DROP INDEX IF EXISTS idx_people_title;
DROP INDEX IF EXISTS idx_specimens_category;
DROP INDEX IF EXISTS idx_users_title;
DROP INDEX IF EXISTS idx_users_sub_type;
DROP INDEX IF EXISTS idx_users_workflow_user_status;
DROP INDEX IF EXISTS idx_gist_multimedia_descriptive_ts;



CREATE INDEX CONCURRENTLY idx_informative_workflow_user_ref on informative_workflow(user_ref);
CREATE INDEX CONCURRENTLY idx_insurances_contact_ref on insurances(contact_ref);
CREATE INDEX CONCURRENTLY idx_chronostratigraphy_name_order_by_txt_op on chronostratigraphy USING btree ( name_order_by text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_vernacular_names_vernacular_class_ref on vernacular_names (vernacular_class_ref);
CREATE INDEX CONCURRENTLY idx_lithostratigraphy_name_order_by_txt_op on lithostratigraphy USING btree ( name_order_by text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_lithology_name_order_by_txt_op on lithology USING btree ( name_order_by text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_mineralogy_name_order_by_txt_op on mineralogy USING btree ( name_order_by text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_taxonomy_name_order_by_txt_op on taxonomy USING btree ( name_order_by text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_tag_groups_group_name_indexed_txt_op on tag_groups(group_name_indexed text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_informative_workflow_user_status on informative_workflow(user_ref, status);
CREATE INDEX CONCURRENTLY idx_gist_multimedia_description_ts on multimedia using gist(search_ts);
/** LOANS **/
CREATE INDEX CONCURRENTLY idx_loan_items_loan_ref on loan_items(loan_ref);
CREATE INDEX CONCURRENTLY idx_loan_items_ig_ref on loan_items(ig_ref);
CREATE INDEX CONCURRENTLY idx_loan_items_part_ref on loan_items(part_ref);
CREATE INDEX CONCURRENTLY idx_loan_rights_ig_ref on loan_rights(loan_ref);
CREATE INDEX CONCURRENTLY idx_loan_rights_part_ref on loan_rights(user_ref);
CREATE INDEX CONCURRENTLY idx_loan_status_user_ref on loan_status(user_ref);
CREATE INDEX CONCURRENTLY idx_loan_status_loan_ref on loan_status(loan_ref);
CREATE INDEX CONCURRENTLY idx_loan_status_loan_ref_is_last on loan_status(loan_ref,is_last);



/************ FLAT *******************/
CREATE INDEX CONCURRENTLY idx_darwin_flat_host_specimen_ref on darwin_flat(host_specimen_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_category on darwin_flat(category);



DROP INDEX IF EXISTS idx_darwin_flat_collection_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_gtu_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_taxon_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_taxon_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_host_taxon_ref;
DROP INDEX IF EXISTS idx_darwin_flat_host_taxon_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_host_taxon_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_chrono_ref;
DROP INDEX IF EXISTS idx_darwin_flat_chrono_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_chrono_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_litho_ref;
DROP INDEX IF EXISTS idx_darwin_flat_litho_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_litho_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_lithology_ref;
DROP INDEX IF EXISTS idx_darwin_flat_lithology_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_lithology_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_mineral_ref;
DROP INDEX IF EXISTS idx_darwin_flat_mineral_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_mineral_level_ref;

DROP INDEX IF EXISTS idx_darwin_flat_category;
DROP INDEX IF EXISTS idx_darwin_flat_chrono_name_order_by;
DROP INDEX IF EXISTS idx_darwin_flat_litho_name_order_by;
DROP INDEX IF EXISTS idx_darwin_flat_lithology_name_order_by;
DROP INDEX IF EXISTS idx_darwin_flat_mineral_name_order_by;
DROP INDEX IF EXISTS idx_darwin_flat_mineral_path;
DROP INDEX IF EXISTS idx_darwin_flat_taxon_extinct;
DROP INDEX IF EXISTS idx_darwin_flat_acquisition_category;
DROP INDEX IF EXISTS idx_darwin_flat_individual_count_min;
DROP INDEX IF EXISTS idx_darwin_flat_individual_count_max;
DROP INDEX IF EXISTS idx_darwin_flat_part_count_min;
DROP INDEX IF EXISTS idx_darwin_flat_part_count_max;

/*** Indexes created for the f***ing necessary group by when searching in darwin_flat ***/
/**** For specimen search ****/
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_category;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_coll_name;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_chrono_name;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_litho_name;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_lithology_name;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_mineral_name;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_expedition_name;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_with_types;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_with_individuals;
DROP INDEX IF EXISTS idx_darwin_flat_spec_ref_with_parts;
DROP INDEX IF EXISTS idx_darwin_flat_spec_host_specimens;

/**** For individual search ****/
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_category;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_coll_name;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_taxon_name;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_chrono_name;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_litho_name;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_lithology_name;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_mineral_name;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_expedition_name;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_type;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_type_group;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_type_search;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_sex;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_state;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_stage;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_social_status;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_rock_form;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_individual_count_max;
DROP INDEX IF EXISTS idx_darwin_flat_individual_ref_with_parts;

/**** For part search ****/
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_category;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_coll_name;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_taxon_name;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_chrono_name;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_litho_name;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_lithology_name;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_mineral_name;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_expedition_name;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_type;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_type_group;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_type_search;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_sex;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_state;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_stage;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_social_status;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_rock_form;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_individual_count_max;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_part;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_part_status;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_building;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_floor;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_room;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_row;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_container_type;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_container_storage;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_sub_container_type;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_container;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_sub_container_storage;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_sub_container;
DROP INDEX IF EXISTS idx_darwin_flat_part_ref_part_count_max;
DROP INDEX IF EXISTS idx_gin_darwin_flat_chrono_name_indexed;
DROP INDEX IF EXISTS idx_gin_darwin_flat_litho_name_indexed;
DROP INDEX IF EXISTS idx_gin_darwin_flat_lithology_name_indexed;
DROP INDEX IF EXISTS idx_gin_darwin_flat_mineral_name_indexed;