SET search_path=darwin2, public;

DROP TRIGGER trg_update_specimens_darwin_flat ON specimens;
DROP TRIGGER trg_update_specimen_individuals_darwin_flat ON specimen_individuals;
DROP TRIGGER trg_delete_specimen_individuals_darwin_flat ON specimen_individuals;
DROP TRIGGER trg_update_specimen_parts_darwin_flat ON specimen_parts;
DROP TRIGGER trg_delete_specimen_parts_darwin_flat ON specimen_parts;
DROP FUNCTION IF EXISTS fct_update_darwin_flat() CASCADE;

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

DROP INDEX IF EXISTS unq_specimens;

ALTER TABLE specimen_collecting_methods DROP CONSTRAINT fk_specimen_collecting_methods_specimen;
ALTER TABLE specimen_collecting_tools DROP CONSTRAINT fk_specimen_collecting_tools_specimen;
ALTER TABLE specimen_individuals DROP CONSTRAINT fk_specimen_individuals_specimens;
ALTER TABLE specimens_accompanying DROP CONSTRAINT fk_specimens_accompanying_specimens;

DROP TABLE darwin_flat CASCADE;

ALTER TABLE specimens
ALTER COLUMN expedition_ref DROP NOT NULL,
ALTER COLUMN expedition_ref SET DEFAULT NULL,
ALTER COLUMN gtu_ref DROP NOT NULL,
ALTER COLUMN gtu_ref SET DEFAULT NULL,
ALTER COLUMN chrono_ref DROP NOT NULL,
ALTER COLUMN chrono_ref SET DEFAULT NULL,
ALTER COLUMN litho_ref DROP NOT NULL,
ALTER COLUMN litho_ref SET DEFAULT NULL,
ALTER COLUMN lithology_ref DROP NOT NULL,
ALTER COLUMN lithology_ref SET DEFAULT NULL,
ALTER COLUMN mineral_ref DROP NOT NULL,
ALTER COLUMN mineral_ref SET DEFAULT NULL,
ALTER COLUMN taxon_ref DROP NOT NULL,
ALTER COLUMN taxon_ref SET DEFAULT NULL,
ALTER COLUMN host_taxon_ref DROP NOT NULL,
ALTER COLUMN host_taxon_ref SET DEFAULT NULL
;

UPDATE specimens
SET expedition_ref = DEFAULT
WHERE expedition_ref = 0;

UPDATE specimens
SET gtu_ref = DEFAULT
WHERE gtu_ref = 0;

UPDATE specimens
SET taxon_ref = DEFAULT
WHERE taxon_ref = 0;

UPDATE specimens
SET host_taxon_ref = DEFAULT
WHERE host_taxon_ref = 0;

UPDATE specimens
SET chrono_ref = DEFAULT
WHERE chrono_ref = 0;

UPDATE specimens
SET litho_ref = DEFAULT
WHERE litho_ref = 0;

UPDATE specimens
SET lithology_ref = DEFAULT
WHERE lithology_ref = 0;

UPDATE specimens
SET mineral_ref = DEFAULT
WHERE mineral_ref = 0;

CREATE UNIQUE INDEX unq_specimens ON specimens (collection_ref, COALESCE(expedition_ref,0), COALESCE(gtu_ref,0), COALESCE(taxon_ref,0), COALESCE(litho_ref,0), COALESCE(chrono_ref,0), COALESCE(lithology_ref,0), COALESCE(mineral_ref,0), COALESCE(host_taxon_ref,0), acquisition_category, acquisition_date, COALESCE(ig_ref,0));

ALTER TABLE specimen_individuals ADD COLUMN ind_ident_ids integer[] not null default '{}'::integer[];

UPDATE specimen_individuals ind SET
      with_parts = exists (select 1 from specimen_parts p WHERE p.specimen_individual_ref = ind.id ),
      ind_ident_ids = coalesce((SELECT array_agg(DISTINCT people_ref) FROM catalogue_people cp INNER JOIN identifications i ON cp.record_id = i.id AND cp.referenced_relation = 'identifications'
                WHERE i.record_id = ind.id AND i.referenced_relation = 'specimen_individuals'), '{}'::integer[]);

CREATE TRIGGER trg_update_specimens_darwin_flat AFTER INSERT OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE fct_update_specimen_flat();
 
CREATE TRIGGER trg_fct_count_units_individuals AFTER INSERT OR UPDATE OR DELETE
         ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE fct_count_units();

CREATE TRIGGER trg_fct_count_units_parts AFTER INSERT OR DELETE
         ON specimen_parts FOR EACH ROW
         EXECUTE PROCEDURE fct_count_units();

\i ../maintenance/recreate_flat.sql


/*****************
 * REMOVE ZERO REFS
 *
 ******************/

ALTER TABLE possible_upper_levels ALTER COLUMN level_upper_ref DROP NOT NULL;
ALTER TABLE template_classifications ALTER COLUMN parent_ref DROP NOT NULL,
                                     ALTER COLUMN parent_ref SET DEFAULT NULL;
ALTER TABLE specimens_accompanying ALTER COLUMN taxon_ref DROP NOT NULL;
ALTER TABLE specimens_accompanying ALTER COLUMN mineral_ref DROP NOT NULL;

update possible_upper_levels SET level_upper_ref = null where level_upper_ref = 0;

insert into possible_upper_levels (select 65, null where not exists (select 1 from possible_upper_levels where level_ref = 65));

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

/*** Reset catalogues triggers ***/

create trigger trg_update_chronostratigraphy_darwin_flat after update on chronostratigraphy for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_collections_darwin_flat after update on collections for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_expeditions_darwin_flat after update on expeditions for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_gtu_darwin_flat after update on gtu for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_igs_darwin_flat after update on igs for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_lithology_darwin_flat after update on lithology for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_lithostratigraphy_darwin_flat after update on lithostratigraphy for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_mineralogy_darwin_flat after update on mineralogy for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_tag_groups_darwin_flat after insert or update or delete on tag_groups for each row execute procedure fct_update_specimens_flat_related();
create trigger trg_update_taxonomy_darwin_flat after update on taxonomy for each row execute procedure fct_update_specimens_flat_related();

/*** RESET CONSTRAINTS ***/

ALTER TABLE specimens ADD CONSTRAINT fk_specimens_expeditions FOREIGN KEY (expedition_ref) REFERENCES expeditions(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_gtu FOREIGN KEY (gtu_ref) REFERENCES gtu(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_collections FOREIGN KEY (collection_ref) REFERENCES collections(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_taxonomy FOREIGN KEY (taxon_ref) REFERENCES taxonomy(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_lithostratigraphy FOREIGN KEY (litho_ref) REFERENCES lithostratigraphy(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_lithology FOREIGN KEY (lithology_ref) REFERENCES lithology(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_mineralogy FOREIGN KEY (mineral_ref) REFERENCES mineralogy(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_chronostratigraphy FOREIGN KEY (chrono_ref) REFERENCES chronostratigraphy(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_host_taxonomy FOREIGN KEY (host_taxon_ref) REFERENCES taxonomy(id);
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_host_specimen FOREIGN KEY (host_specimen_ref) REFERENCES specimens(id) on delete set null;
ALTER TABLE specimens ADD CONSTRAINT fk_specimens_igs FOREIGN KEY (ig_ref) REFERENCES igs(id);


ALTER TABLE specimen_collecting_methods ADD CONSTRAINT fk_specimen_collecting_methods_specimen FOREIGN KEY (specimen_ref) REFERENCES specimens(id);
ALTER TABLE specimen_collecting_tools ADD CONSTRAINT fk_specimen_collecting_tools_specimen FOREIGN KEY (specimen_ref) REFERENCES specimens(id);
ALTER TABLE specimen_individuals ADD CONSTRAINT fk_specimen_individuals_specimens FOREIGN KEY (specimen_ref) REFERENCES specimens(id);
ALTER TABLE specimens_accompanying ADD CONSTRAINT fk_specimens_accompanying_specimens FOREIGN KEY (specimen_ref) REFERENCES specimens(id);

/*** REMOVE domains ****/

ALTER TABLE comments drop column comment_language_full_text;
ALTER TABLE ext_links drop column comment_language_full_text;
ALTER TABLE expeditions drop column name_language_full_text;



alter table informative_workflow alter column modification_date_time type timestamp;
alter table informative_workflow alter column modification_date_time set default now();
alter table informative_workflow alter column modification_date_time set not null;

alter table users_tracking alter column modification_date_time type timestamp;
alter table users_tracking alter column modification_date_time set default now();
alter table users_tracking alter column modification_date_time set not null;

alter table collection_maintenance alter column modification_date_time type timestamp;
alter table collection_maintenance alter column modification_date_time set default now();
alter table collection_maintenance alter column modification_date_time set not null;

alter table my_saved_searches alter column modification_date_time type timestamp;
alter table my_saved_searches alter column modification_date_time set default now();
alter table my_saved_searches alter column modification_date_time set not null;

alter table loan_status alter column modification_date_time type timestamp;
alter table loan_status alter column modification_date_time set default now();
alter table loan_status alter column modification_date_time set not null;

alter table loan_history alter column modification_date_time type timestamp;
alter table loan_history alter column modification_date_time set default now();
alter table loan_history alter column modification_date_time set not null;



CREATE INDEX CONCURRENTLY idx_specimen_parts_specimen_part_institution_ref on specimen_parts(institution_ref);
alter table specimen_parts add constraint fk_specimen_parts_institutions foreign key (institution_ref) references people(id) ON DELETE no action;
/*** Cleanup ***/

DROP AGGREGATE array_accum(anyelement);
DROP AGGREGATE dummy_first(anyelement);
DROP FUNCTION dummy( in anyelement, inout anyelement );

/**** FINISH ****/
