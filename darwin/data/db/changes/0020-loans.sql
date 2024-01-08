SET search_path = darwin2, public;

\i ../createfunctions.sql

ALTER TABLE specimens DROP COLUMN multimedia_visible CASCADE;
ALTER TABLE comments DROP constraint unq_comments ;

ALTER TABLE multimedia RENAME TO old_multimedia;
ALTER TABLE old_multimedia ALTER COLUMN id SET DEFAULT NULL;
ALTER TABLE old_multimedia DROP CONSTRAINT pk_multimedia CASCADE;
DROP TRIGGER IF EXISTS trg_clr_referencerecord_multimedia ON old_multimedia;
DROP TRIGGER IF EXISTS trg_cpy_fulltoindex_multimedia ON old_multimedia;
DROP TRIGGER IF EXISTS trg_cpy_path_multimedia ON old_multimedia;
DROP TRIGGER IF EXISTS trg_cpy_tofulltext_multimedia ON old_multimedia;
DROP TRIGGER IF EXISTS trg_trk_log_table_multimedia ON old_multimedia;
DROP TRIGGER IF EXISTS trg_words_ts_cpy_multimedia ON old_multimedia;

-- To release only if possible to insert elec. publi of Cathy -- select setval('multimedia_new_id_seq', (select max(id)+1 from old_multimedia), false);

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
        creation_date date not null default '0001-01-01'::date,
        creation_date_mask integer not null default 0,
        mime_type varchar not null,
        visible boolean not null default true,
        publishable boolean not null default true,
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
comment on column multimedia.search_ts is 'tsvector form of title and description fields together';
comment on column multimedia.mime_type is 'Mime/Type of the linked digital object';
comment on column multimedia.visible is 'Flag telling if the related file has been chosen to be publically visible or not';
comment on column multimedia.publishable is 'Flag telling if the related file has been chosen as a prefered item for publication - Would be for example used for preselection of media published for Open Up project';

ALTER TABLE multimedia OWNER TO darwin2;
ALTER TABLE multimedia_id_seq OWNER TO darwin2;
GRANT USAGE ON SEQUENCE darwin2.multimedia_id_seq TO cebmpad;
GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.multimedia TO cebmpad;
GRANT SELECT ON darwin2.multimedia TO d2viewer;


create table multimedia_todelete (
  id serial,
  uri text,
  constraint pk_multimedia_todelete primary key (id)
);

comment on table multimedia_todelete is 'Table here to save deleted multimedia files waiting for a deletion on the disk';
comment on column multimedia_todelete.uri is 'URI of the file to delete';

ALTER TABLE multimedia_todelete OWNER TO darwin2;
ALTER TABLE multimedia_todelete_id_seq OWNER TO darwin2;
GRANT USAGE ON SEQUENCE darwin2.multimedia_todelete_id_seq TO cebmpad;
GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.multimedia_todelete TO cebmpad;

CREATE TRIGGER trg_cpy_deleted_file AFTER DELETE
  ON multimedia FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_deleted_file();

CREATE TRIGGER trg_clr_referencerecord_multimedia
  AFTER DELETE
  ON multimedia
  FOR EACH ROW
  EXECUTE PROCEDURE fct_clear_referencedrecord();


CREATE TRIGGER trg_cpy_toFullText_multimedia BEFORE INSERT OR UPDATE
  ON multimedia FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(search_ts, 'pg_catalog.simple', title, description);

CREATE TRIGGER trg_trk_log_table_multimedia
  AFTER INSERT OR UPDATE OR DELETE
  ON multimedia
  FOR EACH ROW
  EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_words_ts_cpy_multimedia
  BEFORE INSERT OR UPDATE
  ON multimedia
  FOR EACH ROW
  EXECUTE PROCEDURE fct_trg_word();

-- INSERT INTO multimedia(referenced_relation, record_id, id, parent_ref, is_digital,type, sub_type, title, description, uri, filename, creation_date, creation_date_mask, mime_type)
-- (
-- SELECT /*!!! No referenced relation and record id !!!*/, id, parent_ref, is_digital,type, sub_type, title, subject,  uri, '', creation_date, creation_date_mask, ''
-- FROM old_multimedia
-- );
-- 
-- DROP TABLE old_multimedia;

ALTER TABLE insurances add column date_from_mask integer not null default 0;
ALTER TABLE insurances add column date_from date not null default '0001-01-01'::date;
ALTER TABLE insurances add column date_to_mask integer not null default 0;
ALTER TABLE insurances add column date_to date not null default '2038-12-31'::date;
ALTER TABLE insurances add column contact_ref integer;

ALTER TABLE insurances DROP constraint unq_specimen_parts_insurances;

UPDATE insurances set date_from = DATE (insurance_year::varchar || '-01-01') , date_to = DATE (insurance_year || '-12-31'), date_from_mask = 32, date_to_mask = 32
where insurance_year != 0;

UPDATE insurances set date_from = default, date_to = default, date_from_mask = default, date_to_mask = default where insurance_year = 0;

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

ALTER TABLE loans OWNER TO darwin2;
ALTER TABLE loans_id_seq OWNER TO darwin2;
GRANT USAGE ON SEQUENCE darwin2.loans_id_seq TO cebmpad;
GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.loans TO cebmpad;
GRANT SELECT ON darwin2.loans TO d2viewer;
  
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

ALTER TABLE loan_items OWNER TO darwin2;
ALTER TABLE loan_items_id_seq OWNER TO darwin2;
GRANT USAGE ON SEQUENCE darwin2.loan_items_id_seq TO cebmpad;
GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.loan_items TO cebmpad;
GRANT SELECT ON darwin2.loan_items TO d2viewer;

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

ALTER TABLE loan_rights OWNER TO darwin2;
ALTER TABLE loan_rights_id_seq OWNER TO darwin2;
GRANT USAGE ON SEQUENCE darwin2.loan_rights_id_seq TO cebmpad;
GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.loan_rights TO cebmpad;
GRANT SELECT ON darwin2.loan_rights TO d2viewer;

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


ALTER TABLE loan_status OWNER TO darwin2;
ALTER TABLE loan_status_id_seq OWNER TO darwin2;
GRANT USAGE ON SEQUENCE darwin2.loan_status_id_seq TO cebmpad;
GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.loan_status TO cebmpad;
GRANT SELECT ON darwin2.loan_status TO d2viewer;

create table loan_history (
  id serial,
  loan_ref integer not null,
  referenced_table text not null,
  modification_date_time update_date_time,
  record_line hstore,
  constraint pk_loan_history primary key (id),
  constraint fk_loan_history_loan_ref foreign key (loan_ref) references loans(id) on delete cascade
);

comment on table loan_history is 'Table is a snapshot of an entire loan and related informations at a certain time';

comment on column loan_history.loan_ref is 'Mandatory Reference to a loan';
comment on column loan_history.referenced_table is 'Mandatory Reference to the table refereced';
comment on column loan_history.modification_date_time is 'date of the modification';
comment on column loan_history.record_line is 'hstore containing the whole line of referenced_table';

ALTER TABLE loan_history OWNER TO darwin2;
ALTER TABLE loan_history_id_seq OWNER TO darwin2;
GRANT USAGE ON SEQUENCE darwin2.loan_history_id_seq TO cebmpad;
GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.loan_history TO cebmpad;
GRANT SELECT ON darwin2.loan_history TO d2viewer;

DROP TRIGGER trg_cpy_toFullText_multimedia on multimedia;

CREATE TRIGGER trg_cpy_ig_to_loan_items AFTER UPDATE
  ON specimens FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_ig_to_loan_items();

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

DROP INDEX IF EXISTS idx_darwin_flat_collection_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_gtu_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_taxon_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_taxon_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_host_taxon_ref;
DROP INDEX IF EXISTS idx_darwin_flat_host_taxon_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_host_taxon_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_chrono_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_chrono_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_litho_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_litho_level_ref;
DROP INDEX IF EXISTS idx_darwin_flat_lithology_parent_ref;
DROP INDEX IF EXISTS idx_darwin_flat_lithology_level_ref;
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

DROP INDEX IF EXISTS idx_darwin_flat_gtu_code;

DROP INDEX IF EXISTS idx_my_widgets_group_name;
DROP INDEX IF EXISTS idx_my_widgets_order_by;
DROP INDEX IF EXISTS idx_my_widgets_icon_ref;
DROP INDEX IF EXISTS idx_my_widgets_collections;
DROP INDEX IF EXISTS idx_my_widgets_visible;
DROP INDEX IF EXISTS idx_my_widgets_is_available;
DROP INDEX IF EXISTS idx_catalogue_properties_property_method_indexed;
DROP INDEX IF EXISTS idx_catalogue_properties_property_tool_indexed;
DROP INDEX IF EXISTS idx_catalogue_properties_property_accuracy_unit;


/*** Batch 2 ***/

DROP INDEX IF EXISTS idx_specimens_collection_ref;
DROP INDEX IF EXISTS idx_specimen_individuals_type_search;
DROP INDEX IF EXISTS idx_specimen_individuals_type;
DROP INDEX IF EXISTS idx_specimen_individuals_stage;
DROP INDEX IF EXISTS idx_specimen_individuals_state;
DROP INDEX IF EXISTS idx_specimen_individuals_social_status;
DROP INDEX IF EXISTS idx_taxonomy_name_order_by;

DROP INDEX IF EXISTS idx_darwin_flat_taxon_name_order_by;
DROP INDEX IF EXISTS idx_darwin_flat_individual_type_group;
DROP INDEX IF EXISTS idx_darwin_flat_individual_type_search;
DROP INDEX IF EXISTS idx_darwin_flat_individual_state;
DROP INDEX IF EXISTS idx_darwin_flat_individual_social_status;
DROP INDEX IF EXISTS idx_darwin_flat_collection_is_public;
DROP INDEX IF EXISTS idx_darwin_flat_collection_name;
DROP INDEX IF EXISTS idx_gin_darwin_flat_gtu_country_tags;


CREATE INDEX CONCURRENTLY idx_specimen_individuals_type_search on specimen_individuals(type_search) WHERE type_search <> 'specimen';
CREATE INDEX CONCURRENTLY idx_specimen_individuals_state on specimen_individuals(state) WHERE state <> 'not applicable';
CREATE INDEX CONCURRENTLY idx_specimen_individuals_stage on specimen_individuals(stage) WHERE stage <> 'not applicable';
CREATE INDEX CONCURRENTLY idx_specimen_individuals_social_status on specimen_individuals(social_status)  WHERE social_status <> 'not applicable';

CREATE INDEX CONCURRENTLY idx_darwin_flat_host_taxon_ref on darwin_flat(host_taxon_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_type_group on darwin_flat(individual_type_group) WHERE individual_type_group <> 'specimen';
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_type_search on darwin_flat(individual_type_search) WHERE individual_type_search <> 'specimen';
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_state on darwin_flat(individual_state) WHERE individual_state <> 'not applicable';
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_social_status on darwin_flat(individual_social_status) WHERE individual_social_status <> 'not applicable';


/*** For Updating the toFullText TS Vector triggers everywhere ***/

DROP TRIGGER IF EXISTS trg_cpy_toFullText_collectionmaintenance ON collection_maintenance;

CREATE TRIGGER trg_cpy_toFullText_collectionmaintenance BEFORE INSERT OR UPDATE
  ON collection_maintenance FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(description_ts, 'pg_catalog.simple', description);

DROP TRIGGER IF EXISTS trg_cpy_toFullText_comments ON comments;

CREATE TRIGGER trg_cpy_toFullText_comments BEFORE INSERT OR UPDATE
  ON comments FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(comment_ts, 'pg_catalog.simple', comment);

DROP TRIGGER IF EXISTS trg_cpy_toFullText_expeditions ON expeditions;

CREATE TRIGGER trg_cpy_toFullText_expeditions BEFORE INSERT OR UPDATE
  ON expeditions FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(name_ts, 'pg_catalog.simple', name);

DROP TRIGGER IF EXISTS trg_cpy_toFullText_ext_links ON ext_links;

CREATE TRIGGER trg_cpy_toFullText_ext_links BEFORE INSERT OR UPDATE
  ON ext_links FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(comment_ts, 'pg_catalog.simple', comment);

DROP TRIGGER IF EXISTS trg_cpy_toFullText_habitats ON habitats;

CREATE TRIGGER trg_cpy_toFullText_habitats BEFORE INSERT OR UPDATE
  ON habitats FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(description_ts, 'pg_catalog.simple', description );

DROP TRIGGER IF EXISTS trg_cpy_toFullText_identifications ON identifications;

CREATE TRIGGER trg_cpy_toFullText_identifications BEFORE INSERT OR UPDATE
  ON identifications FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(value_defined_ts, 'pg_catalog.simple', value_defined);

DROP TRIGGER IF EXISTS trg_cpy_toFullText_multimedia ON multimedia;

CREATE TRIGGER trg_cpy_toFullText_multimedia BEFORE INSERT OR UPDATE
  ON multimedia FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(search_ts, 'pg_catalog.simple', title, description);

DROP TRIGGER IF EXISTS trg_cpy_toFullText_peopleaddresses ON people_addresses;

CREATE TRIGGER trg_cpy_toFullText_peopleaddresses BEFORE INSERT OR UPDATE
  ON people_addresses FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(address_parts_ts, 'pg_catalog.simple', entry, po_box, extended_address, locality, region, zip_code, country);

DROP TRIGGER IF EXISTS trg_cpy_toFullText_usersaddresses ON users_addresses;

CREATE TRIGGER trg_cpy_toFullText_usersaddresses BEFORE INSERT OR UPDATE
  ON users_addresses FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(address_parts_ts, 'pg_catalog.simple', entry, po_box, extended_address, locality, region, zip_code, country);

DROP TRIGGER IF EXISTS trg_cpy_toFullText_vernacularnames ON vernacular_names;

CREATE TRIGGER trg_cpy_toFullText_vernacularnames BEFORE INSERT OR UPDATE
  ON vernacular_names FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(name_ts, 'pg_catalog.simple', name);

DROP FUNCTION IF EXISTS fct_cpy_tofulltext();
