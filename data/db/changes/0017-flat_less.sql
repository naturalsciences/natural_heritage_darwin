DROP TABLE habitats CASCADE;
DROP TABLE multimedia_keywords CASCADE;
DROP TABLE soortenregister CASCADE;


DROP TRIGGER trg_update_specimens_darwin_flat ON specimens;
DROP TRIGGER trg_update_specimen_individuals_darwin_flat ON specimen_individuals;
DROP TRIGGER trg_delete_specimen_individuals_darwin_flat ON specimen_individuals;
DROP TRIGGER trg_update_specimen_parts_darwin_flat ON specimen_parts;
DROP TRIGGER trg_delete_specimen_parts_darwin_flat ON specimen_parts;


\i  ../createfunctions.sql


DROP FUNCTION fct_delete_darwin_flat_ind_part() ;



ALTER TABLE specimens DROP CONSTRAINT fk_specimens_expeditions;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_gtu;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_collections;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_taxonomy;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_lithostratigraphy;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_lithology;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_mineralogy;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_chronostratigraphy;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_host_taxonomy;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_host_specimen;
ALTER TABLE specimens DROP CONSTRAINT fk_specimens_igs;

ALTER TABLE specimens DROP CONSTRAINT unq_specimens;

ALTER TABLE darwin_flat DROP CONSTRAINT fk_darwin_flat_spec_ref;

ALTER TABLE specimen_collecting_methods DROP CONSTRAINT fk_specimen_collecting_methods_specimen;
ALTER TABLE specimen_collecting_tools DROP CONSTRAINT fk_specimen_collecting_tools_specimen;
ALTER TABLE specimen_individuals DROP CONSTRAINT fk_specimen_individuals_specimens;
ALTER TABLE specimens_accompanying DROP CONSTRAINT fk_specimens_accompanying_specimens;
ALTER TABLE darwin_flat DROP CONSTRAINT fk_darwin_flat_host_specimen_ref;


ALTER TABLE specimens DROP CONSTRAINT pk_specimens;

ALTER TABLE specimens RENAME to old_spec;




create table specimens
       (
        id integer not null default nextval('specimens_id_seq'),
        category varchar not null default 'physical',
        collection_ref integer not null,
        expedition_ref integer,
        gtu_ref integer,
        taxon_ref integer,
        litho_ref integer,
        chrono_ref integer,
        lithology_ref integer,
        mineral_ref integer,
        host_taxon_ref integer,
        host_specimen_ref integer,
        host_relationship varchar,
        acquisition_category varchar not null default '',
        acquisition_date_mask integer not null default 0,
        acquisition_date date not null default '01/01/0001',
        station_visible boolean not null default true,
        multimedia_visible boolean not null default true,
        ig_ref integer,


    spec_ident_ids integer[] not null default '{}',
    spec_coll_ids integer[] not null default '{}',
    spec_don_sel_ids integer[] not null default '{}',
    with_types boolean  not null default false,
    with_individuals boolean not null default false,
    collection_type varchar,
    collection_code varchar,
    collection_name varchar,
    collection_is_public boolean,
    collection_parent_ref integer,
    collection_path varchar,
    expedition_name varchar,
    expedition_name_ts tsvector,
    expedition_name_indexed varchar,

    gtu_code varchar,
    gtu_parent_ref integer,
    gtu_path varchar,
    gtu_from_date_mask integer,
    gtu_from_date timestamp,
    gtu_to_date_mask integer,
    gtu_to_date timestamp,
    gtu_tag_values_indexed varchar[],
    gtu_country_tag_value varchar,
    gtu_country_tag_indexed varchar[],
    gtu_location GEOGRAPHY(POLYGON,4326),

    taxon_name varchar,
    taxon_name_indexed tsvector,
    taxon_name_order_by varchar,
    taxon_level_ref integer,
    taxon_level_name varchar,
    taxon_status varchar,
    taxon_path varchar,
    taxon_parent_ref integer,
    taxon_extinct boolean,

    litho_name varchar,
    litho_name_indexed tsvector,
    litho_name_order_by varchar,
    litho_level_ref integer,
    litho_level_name varchar,
    litho_status varchar,
    litho_local boolean,
    litho_color varchar,
    litho_path varchar,
    litho_parent_ref integer,

    chrono_name varchar,
    chrono_name_indexed tsvector,
    chrono_name_order_by varchar,
    chrono_level_ref integer,
    chrono_level_name varchar,
    chrono_status varchar,
    chrono_local boolean,
    chrono_color varchar,
    chrono_path varchar,
    chrono_parent_ref integer,

    lithology_name varchar,
    lithology_name_indexed tsvector,
    lithology_name_order_by varchar,
    lithology_level_ref integer,
    lithology_level_name varchar,
    lithology_status varchar,
    lithology_local boolean,
    lithology_color varchar,
    lithology_path varchar,
    lithology_parent_ref integer,

    mineral_name varchar,
    mineral_name_indexed tsvector,
    mineral_name_order_by varchar,
    mineral_level_ref integer,
    mineral_level_name varchar,
    mineral_status varchar,
    mineral_local boolean,
    mineral_color varchar,
    mineral_path varchar,
    mineral_parent_ref integer,

    host_taxon_name varchar,
    host_taxon_name_indexed tsvector,
    host_taxon_name_order_by varchar,
    host_taxon_level_ref integer,
    host_taxon_level_name varchar,
    host_taxon_status varchar,
    host_taxon_path varchar,
    host_taxon_parent_ref integer,
    host_taxon_extinct boolean,

    ig_num varchar,
    ig_num_indexed varchar,
    ig_date_mask integer,
    ig_date date,

        constraint pk_specimens primary key (id),
        constraint fk_specimens_expeditions foreign key (expedition_ref) references expeditions(id),
        constraint fk_specimens_gtu foreign key (gtu_ref) references gtu(id),
        constraint fk_specimens_collections foreign key (collection_ref) references collections(id),
        constraint fk_specimens_taxonomy foreign key (taxon_ref) references taxonomy(id),
        constraint fk_specimens_lithostratigraphy foreign key (litho_ref) references lithostratigraphy(id),
        constraint fk_specimens_lithology foreign key (lithology_ref) references lithology(id),
        constraint fk_specimens_mineralogy foreign key (mineral_ref) references mineralogy(id),
        constraint fk_specimens_chronostratigraphy foreign key (chrono_ref) references chronostratigraphy(id),
        constraint fk_specimens_host_taxonomy foreign key (host_taxon_ref) references taxonomy(id),
        constraint fk_specimens_host_specimen foreign key (host_specimen_ref) references specimens(id) on delete set null,
        constraint fk_specimens_igs foreign key (ig_ref) references igs(id)
       );


comment on table specimens is 'Specimens or batch of specimens stored in collection';
comment on column specimens.id is 'Unique identifier of a specimen or batch of specimens';
comment on column specimens.collection_ref is 'Reference of collection the specimen is grouped under - id field of collections table';
comment on column specimens.expedition_ref is 'When acquisition category is expedition, contains the reference of the expedition having conducted to the current specimen capture - id field of expeditions table';
comment on column specimens.gtu_ref is 'Reference of the sampling location the specimen is coming from - id field of gtu table';
comment on column specimens.litho_ref is 'When encoding a rock, mineral or paleontologic specimen, contains the reference of lithostratigraphic unit the specimen have been found into - id field of lithostratigraphy table';
comment on column specimens.chrono_ref is 'When encoding a rock, mineral or paleontologic specimen, contains the reference of chronostratigraphic unit the specimen have been found into - id field of chronostratigraphy table';
comment on column specimens.taxon_ref is 'When encoding a ''living'' specimen, contains the reference of the taxon unit defining the specimen - id field of taxonomy table';
comment on column specimens.host_relationship is 'When current specimen encoded is in a host relationship with an other specimen or taxon, this field contains the type of relationship between them: symbiosis, parasitism, saprophytism,...';
comment on column specimens.host_specimen_ref is 'When current specimen encoded is in a host relationship with an other specimen, this field contains reference of the host specimen - recursive reference';
comment on column specimens.acquisition_category is 'Describe how the specimen was collected: expedition, donation,...';
comment on column specimens.acquisition_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column specimens.acquisition_date is 'Date Composed (if possible) of the acquisition';
comment on column specimens.multimedia_visible is 'Flag telling if the multimedia attached to this specimen can be visible or not';
comment on column specimens.station_visible is 'Flag telling if the sampling location can be visible or must be hidden for the specimen encoded';
comment on column specimens.lithology_ref is 'Reference of a rock classification unit associated to the specimen encoded - id field of lithology table';
comment on column specimens.mineral_ref is 'Reference of a mineral classification unit associated to the specimen encoded - id field of mineralogy table';
comment on column specimens.host_taxon_ref is 'Reference of taxon definition defining the host which holds the current specimen - id field of taxonomy table';
comment on column specimens.ig_ref is 'Reference of ig number this specimen has been associated to';
comment on column specimens.category is 'Type of specimen encoded: a physical object stored in collections, an observation, a figurate specimen,...';




CREATE TRIGGER trg_update_specimens_darwin_flat BEFORE INSERT OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE fct_update_specimen_flat();
 
CREATE TRIGGER trg_fct_count_units_individuals AFTER INSERT OR UPDATE OR DELETE
         ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE fct_count_units();

CREATE TRIGGER trg_fct_count_units_parts AFTER INSERT OR DELETE
         ON specimen_parts FOR EACH ROW
         EXECUTE PROCEDURE fct_count_units();


INSERT INTO specimens
(
        id,
        category,
        collection_ref,
        expedition_ref,
        gtu_ref,
        taxon_ref,
        litho_ref,
        chrono_ref,
        lithology_ref,
        mineral_ref,
        host_taxon_ref,
        host_specimen_ref,
        host_relationship,
        acquisition_category,
        acquisition_date_mask,
        acquisition_date,
        station_visible,
        multimedia_visible,
        ig_ref
  )
(select 

        id,
        category,
        collection_ref,
        CASE WHEN expedition_ref = 0 THEN NULL ELSE expedition_ref END,
        CASE WHEN gtu_ref = 0 THEN NULL ELSE gtu_ref END ,
        CASE WHEN taxon_ref = 0 THEN NULL ELSE taxon_ref END ,
        CASE WHEN litho_ref = 0 THEN NULL ELSE litho_ref END ,
        CASE WHEN chrono_ref = 0 THEN NULL ELSE chrono_ref END,
        CASE WHEN lithology_ref = 0 THEN NULL ELSE lithology_ref END ,
        CASE WHEN mineral_ref = 0 THEN NULL ELSE mineral_ref END ,
        CASE WHEN host_taxon_ref = 0 THEN NULL ELSE host_taxon_ref END ,
        CASE WHEN host_specimen_ref = 0 THEN NULL ELSE host_specimen_ref END ,
        host_relationship,
        acquisition_category,
        acquisition_date_mask,
        acquisition_date,
        station_visible,
        multimedia_visible,
        CASE WHEN ig_ref = 0 THEN NULL ELSE ig_ref END 
  from old_spec
);

CREATE UNIQUE INDEX unq_specimens ON specimens (collection_ref, COALESCE(expedition_ref,0), COALESCE(gtu_ref,0), COALESCE(taxon_ref,0), COALESCE(litho_ref,0), COALESCE(chrono_ref,0), COALESCE(lithology_ref,0), COALESCE(mineral_ref,0), COALESCE(host_taxon_ref,0), acquisition_category, acquisition_date, COALESCE(ig_ref,0));
drop table old_specimens;

/*********** CHANGE spec_indiv ****************/

ALTER TABLE specimen_individuals ADD COLUMN ind_ident_ids integer[] not null default '{}';


UPDATE specimen_individuals ind SET
      with_parts = exists (select 1 from specimen_parts p WHERE p.specimen_individual_ref = ind.id ),
      ind_ident_ids = (SELECT array_accum(people_ref) FROM catalogue_people cp INNER JOIN identifications i ON cp.record_id = i.id AND cp.referenced_relation = 'identifications' 
                WHERE i.record_id = ind.id AND i.referenced_relation = 'specimen_individuals');
/*****************
 * REMOVE ZERO REFS
 *
 ******************/

ALTER TABLE possible_upper_levels ALTER COLUMN level_upper_ref DROP NOT NULL;
ALTER TABLE template_classifications ALTER COLUMN parent_ref DROP NOT NULL;
ALTER TABLE specimens_accompanying ALTER COLUMN taxon_ref DROP NOT NULL;
ALTER TABLE specimens_accompanying ALTER COLUMN mineral_ref DROP NOT NULL;

update possible_upper_levels SET level_upper_ref = null where level_upper_ref = 0;

ALTER TABLE taxonomy DISABLE TRIGGER USER;
update taxonomy SET parent_ref = null where parent_ref = 0;
ALTER TABLE taxonomy ENABLE TRIGGER USER;

ALTER TABLE gtu DISABLE TRIGGER USER;
update gtu SET parent_ref = null where parent_ref = 0;
ALTER TABLE gtu ENABLE TRIGGER USER;

ALTER TABLE lithostratigraphy DISABLE TRIGGER USER;
update lithostratigraphy SET parent_ref = null where parent_ref = 0;
ALTER TABLE lithostratigraphy ENABLE TRIGGER USER;

ALTER TABLE chronostratigraphy DISABLE TRIGGER USER;
update chronostratigraphy SET parent_ref = null where parent_ref = 0;
ALTER TABLE chronostratigraphy ENABLE TRIGGER USER;

ALTER TABLE lithology DISABLE TRIGGER USER;
update lithology SET parent_ref = null where parent_ref = 0;
ALTER TABLE lithology ENABLE TRIGGER USER;

ALTER TABLE mineralogy DISABLE TRIGGER USER;
update mineralogy SET parent_ref = null where parent_ref = 0;
ALTER TABLE mineralogy ENABLE TRIGGER USER;

update specimens_accompanying SET taxon_ref = null where taxon_ref = 0;
update specimens_accompanying SET mineral_ref = null where mineral_ref = 0;


DELETE from gtu where id = 0;
DELETE FROM expeditions  where id = 0;
DELETE FROM chronostratigraphy  where id = 0;
DELETE FROM lithostratigraphy where id = 0;
DELETE FROM mineralogy where id = 0;
DELETE FROM lithology where id = 0; 
DELETE FROM taxonomy where id = 0;
DELETE FROM users where id = 0;
DELETE FROM people  where id = 0;


/**** FINISH ****/


drop table darwin_flat;

create view darwin_flat as 
  select

 row_number() OVER (ORDER BY s.id) AS id, 

category,
collection_ref,
expedition_ref,
gtu_ref,
taxon_ref,
litho_ref,
chrono_ref,
lithology_ref,
mineral_ref,
host_taxon_ref,
host_specimen_ref,
host_relationship,
acquisition_category,
acquisition_date_mask,
acquisition_date,
station_visible,
multimedia_visible,
ig_ref,


collection_type,
collection_code,
collection_name,
collection_is_public,
collection_parent_ref,
collection_path,
expedition_name,
expedition_name_ts,
expedition_name_indexed,

gtu_code,
gtu_parent_ref,
gtu_path,
gtu_from_date_mask,
gtu_from_date,
gtu_to_date_mask,
gtu_to_date,
gtu_tag_values_indexed,
gtu_country_tag_value,
gtu_country_tag_indexed,
gtu_location,

taxon_name,
taxon_name_indexed,
taxon_name_order_by,
taxon_level_ref,
taxon_level_name,
taxon_status,
taxon_path,
taxon_parent_ref,
taxon_extinct,

litho_name,
litho_name_indexed,
litho_name_order_by,
litho_level_ref,
litho_level_name,
litho_status,
litho_local,
litho_color,
litho_path,
litho_parent_ref,

chrono_name,
chrono_name_indexed,
chrono_name_order_by,
chrono_level_ref,
chrono_level_name,
chrono_status,
chrono_local,
chrono_color,
chrono_path,
chrono_parent_ref,

lithology_name,
lithology_name_indexed,
lithology_name_order_by,
lithology_level_ref,
lithology_level_name,
lithology_status,
lithology_local,
lithology_color,
lithology_path,
lithology_parent_ref,

mineral_name,
mineral_name_indexed,
mineral_name_order_by,
mineral_level_ref,
mineral_level_name,
mineral_status,
mineral_local,
mineral_color,
mineral_path,
mineral_parent_ref,

host_taxon_name,
host_taxon_name_indexed,
host_taxon_name_order_by,
host_taxon_level_ref,
host_taxon_level_name,
host_taxon_status,
host_taxon_path,
host_taxon_parent_ref,
host_taxon_extinct,

ig_num,
ig_num_indexed,
ig_date_mask,
ig_date,
  s.id as spec_ref,

  s.spec_ident_ids,
  s.spec_coll_ids,
  s.spec_don_sel_ids,
  i.ind_ident_ids,

  s.with_types,
  s.with_individuals,
  COALESCE(i.with_parts,false) as with_parts,

  i.id as individual_ref,
  coalesce(i.type, 'specimen') as individual_type,
  coalesce(i.type_group, 'specimen') as individual_type_group,
  coalesce(i.type_search, 'specimen') as individual_type_search,
  coalesce(i.sex, 'undefined') as individual_sex,
  coalesce(i.state, 'not applicable') as individual_state,
  coalesce(i.stage, 'undefined') as individual_stage,
  coalesce(i.social_status, 'not applicable') as individual_social_status,
  coalesce(i.rock_form, 'not applicable') as individual_rock_form,
  coalesce(i.specimen_individuals_count_min, 1) as individual_count_min,
  coalesce(i.specimen_individuals_count_max, 1) as individual_count_max,
  p.id as part_ref,
  p.specimen_part as part,
  p.specimen_status as part_status,
  p.institution_ref,
  p.building,
  p.floor ,
  p.room ,
  p.row  ,
  p.shelf ,
  p.container ,
  p.sub_container ,
  p.container_type ,
  p.sub_container_type ,
  p.container_storage ,
  p.sub_container_storage ,
  p.specimen_part_count_min as part_count_min,
  p.specimen_part_count_max as part_count_max,
  p.specimen_status,
  p.complete,
  p.surnumerary


  from specimens s
  LEFT JOIN specimen_individuals  i ON s.id = i.specimen_ref 
  LEFT JOIN specimen_parts p ON i.id = p.specimen_individual_ref
;
