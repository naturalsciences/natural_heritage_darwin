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


ALTER TABLE specimen_collecting_methods DROP CONSTRAINT fk_specimen_collecting_methods_specimen;
ALTER TABLE specimen_collecting_tools DROP CONSTRAINT fk_specimen_collecting_tools_specimen;
ALTER TABLE specimen_individuals DROP CONSTRAINT fk_specimen_individuals_specimens;
ALTER TABLE specimens_accompanying DROP CONSTRAINT fk_specimens_accompanying_specimens;

DROP TABLE darwin_flat;

CREATE UNIQUE INDEX unq_specimens ON specimens (collection_ref, COALESCE(expedition_ref,0), COALESCE(gtu_ref,0), COALESCE(taxon_ref,0), COALESCE(litho_ref,0), COALESCE(chrono_ref,0), COALESCE(lithology_ref,0), COALESCE(mineral_ref,0), COALESCE(host_taxon_ref,0), acquisition_category, acquisition_date, COALESCE(ig_ref,0));


CREATE TRIGGER trg_update_specimens_darwin_flat BEFORE INSERT OR UPDATE
        ON specimens FOR EACH ROW
        EXECUTE PROCEDURE fct_update_specimen_flat();
 
CREATE TRIGGER trg_fct_count_units_individuals AFTER INSERT OR UPDATE OR DELETE
         ON specimen_individuals FOR EACH ROW
        EXECUTE PROCEDURE fct_count_units();

CREATE TRIGGER trg_fct_count_units_parts AFTER INSERT OR DELETE
         ON specimen_parts FOR EACH ROW
         EXECUTE PROCEDURE fct_count_units();


\i ../maintenance/recreate_flat.sql


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


