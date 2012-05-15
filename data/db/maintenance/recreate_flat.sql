SET search_path = darwin2, public;

BEGIN;

DROP TABLE IF EXISTS specimens_flat CASCADE;

CREATE TABLE specimens_flat (
    specimen_ref integer not null,

    category varchar not null,
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
    acquisition_category varchar not null,
    acquisition_date_mask integer not null,
    acquisition_date date not null,
    station_visible boolean not null,
    ig_ref integer,

    spec_ident_ids integer[] not null default '{}'::integer[],
    spec_coll_ids integer[] not null default '{}'::integer[],
    spec_don_sel_ids integer[] not null default '{}'::integer[],
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
    gtu_from_date_mask integer,
    gtu_from_date timestamp,
    gtu_to_date_mask integer,
    gtu_to_date timestamp,
    gtu_elevation double precision,
    gtu_elevation_accuracy double precision,
    gtu_tag_values_indexed varchar[],
    gtu_country_tag_value varchar,
    gtu_country_tag_indexed varchar[],
    gtu_province_tag_value varchar,
    gtu_province_tag_indexed varchar[],
    gtu_others_tag_value varchar,
    gtu_others_tag_indexed varchar[],
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
    constraint pk_specimens_flat primary key (specimen_ref),
    constraint fk_specimens_flat_specimen_ref foreign key (specimen_ref) references specimens(id) on delete cascade
);


ALTER TABLE specimens_flat OWNER TO darwin2;

GRANT ALL ON specimens_flat TO darwin2;
GRANT INSERT, UPDATE, DELETE, SELECT ON specimens_flat TO cebmpad;
GRANT SELECT ON specimens_flat TO d2viewer;

    INSERT INTO specimens_flat
    (specimen_ref,category, host_relationship, acquisition_category, acquisition_date_mask,
     acquisition_date, station_visible,
     collection_ref,collection_type,collection_code,collection_name, collection_is_public,
     collection_parent_ref,collection_path,
     expedition_ref,expedition_name,expedition_name_ts,expedition_name_indexed,
     gtu_ref,gtu_code,gtu_location,
     spec_ident_ids, spec_coll_ids, spec_don_sel_ids,
     gtu_from_date_mask,gtu_from_date,gtu_to_date_mask,gtu_to_date,
     gtu_elevation, gtu_elevation_accuracy,
     gtu_tag_values_indexed,gtu_country_tag_value,gtu_country_tag_indexed,gtu_province_tag_value,gtu_province_tag_indexed,gtu_others_tag_value,gtu_others_tag_indexed,
     taxon_ref,taxon_name,taxon_name_indexed,taxon_name_order_by,taxon_level_ref,taxon_level_name,taxon_status,
     taxon_path,taxon_parent_ref,taxon_extinct,
     chrono_ref,chrono_name,chrono_name_indexed,chrono_name_order_by,chrono_level_ref,chrono_level_name,chrono_status,
     chrono_local,chrono_color,
     chrono_path,chrono_parent_ref,
     litho_ref,litho_name,litho_name_indexed,litho_name_order_by,litho_level_ref,litho_level_name,litho_status,
     litho_local,litho_color,
     litho_path,litho_parent_ref,
     lithology_ref,lithology_name,lithology_name_indexed,lithology_name_order_by,lithology_level_ref,lithology_level_name,lithology_status,
     lithology_local,lithology_color,
     lithology_path,lithology_parent_ref,
     mineral_ref,mineral_name,mineral_name_indexed,mineral_name_order_by,mineral_level_ref,mineral_level_name,mineral_status,
     mineral_local,mineral_color,
     mineral_path,mineral_parent_ref,
     host_taxon_ref,host_taxon_name,host_taxon_name_indexed,host_taxon_name_order_by,host_taxon_level_ref,host_taxon_level_name,host_taxon_status,
     host_taxon_path,host_taxon_parent_ref,host_taxon_extinct,
     host_specimen_ref,ig_ref, ig_num, ig_num_indexed, ig_date_mask, ig_date, with_types, with_individuals )
    (SELECT spec.id,  spec.category,  spec.host_relationship,  spec.acquisition_category,  spec.acquisition_date_mask,
            spec.acquisition_date,  spec.station_visible,
            spec.collection_ref, coll.collection_type, coll.code, coll.name, coll.is_public,
            coll.parent_ref, coll.path,
            spec.expedition_ref, expe.name, expe.name_ts, expe.name_indexed,
            spec.gtu_ref, gtu.code, gtu.location,
            coalesce(( select array_agg(DISTINCT people_ref) from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications' where i.referenced_relation='specimens' and p.people_type='identifier' and i.record_id = spec.id ), '{}'::integer[]),
            coalesce((select array_agg(DISTINCT people_ref) from catalogue_people where referenced_relation='specimens' and people_type='collector' and record_id = spec.id), '{}'::integer[]),
            coalesce((select array_agg(DISTINCT people_ref) from catalogue_people where referenced_relation='specimens' and people_type='donator' and record_id = spec.id), '{}'::integer[]),
            gtu.gtu_from_date_mask, gtu.gtu_from_date, gtu.gtu_to_date_mask, gtu.gtu_to_date,
            gtu.elevation, gtu.elevation_accuracy,
            gtu.tag_values_indexed,
            taggr_countries.tag_value,  lineToTagArray(taggr_countries.tag_value),
            taggr_provinces.tag_value,  lineToTagArray(taggr_provinces.tag_value),
            (select array_to_string(array(select tag from tags where gtu_ref = gtu.id and sub_group_type not in ('country', 'province')), ';')) as other_gtu_values,
            (select array(select distinct fullToIndex(tag) from tags where gtu_ref = gtu.id and sub_group_type not in ('country', 'province'))) as other_gtu_values_array,
            spec.taxon_ref, taxon.name, taxon.name_indexed, taxon.name_order_by, taxon.level_ref, taxon_level.level_name, taxon.status,
            taxon.path, taxon.parent_ref, taxon.extinct,
            spec.chrono_ref, chrono.name, chrono.name_indexed, chrono.name_order_by, chrono.level_ref, chrono_level.level_name, chrono.status,
            chrono.local_naming, chrono.color,
            chrono.path, chrono.parent_ref,
            spec.litho_ref, litho.name, litho.name_indexed, litho.name_order_by, litho.level_ref, litho_level.level_name, litho.status,
            litho.local_naming,litho.color,
            litho.path, litho.parent_ref,
            spec.lithology_ref, lithology.name, lithology.name_indexed, lithology.name_order_by, lithology.level_ref, lithology_level.level_name, lithology.status,
            lithology.local_naming,lithology.color,
            lithology.path, lithology.parent_ref,
            spec.mineral_ref, mineral.name, mineral.name_indexed, mineral.name_order_by, mineral.level_ref, mineral_level.level_name, mineral.status,
            mineral.local_naming,mineral.color,
            mineral.path, mineral.parent_ref,
            spec.host_taxon_ref, host_taxon.name, host_taxon.name_indexed, host_taxon.name_order_by, host_taxon.level_ref, host_taxon_level.level_name, host_taxon.status,
            host_taxon.path, host_taxon.parent_ref, host_taxon.extinct,
            spec.host_specimen_ref, spec.ig_ref, igs.ig_num, igs.ig_num_indexed, igs.ig_date_mask, igs.ig_date, exists(select 1 from specimen_individuals where specimen_ref = spec.id and type_group != 'specimen'), exists(select 1 from specimen_individuals where specimen_ref = spec.id)
     FROM specimens spec 
      INNER JOIN
          collections coll ON spec.collection_ref = coll.id
      LEFT JOIN
          igs ON igs.id = spec.ig_ref
      LEFT JOIN
        expeditions expe ON expe.id = spec.expedition_ref
      LEFT JOIN
        (gtu LEFT JOIN tag_groups taggr_countries ON gtu.id = taggr_countries.gtu_ref AND taggr_countries.group_name_indexed = 'administrativearea' AND taggr_countries.sub_group_name_indexed = 'country'
             LEFT JOIN tag_groups taggr_provinces ON gtu.id = taggr_provinces.gtu_ref AND taggr_provinces.group_name_indexed = 'administrativearea' AND taggr_provinces.sub_group_name_indexed = 'province'
        )
        ON gtu.id = spec.gtu_ref
      LEFT JOIN 
        (taxonomy taxon INNER JOIN catalogue_levels taxon_level ON taxon.level_ref = taxon_level.id)
        ON taxon.id=spec.taxon_ref
      LEFT JOIN 
        (taxonomy host_taxon INNER JOIN catalogue_levels host_taxon_level ON host_taxon.level_ref = host_taxon_level.id)
        ON host_taxon.id=spec.host_taxon_ref
      LEFT JOIN 
        (chronostratigraphy chrono INNER JOIN catalogue_levels chrono_level ON chrono.level_ref = chrono_level.id)
        ON chrono.id=spec.chrono_ref
      LEFT JOIN 
        (lithostratigraphy litho INNER JOIN catalogue_levels litho_level ON litho.level_ref = litho_level.id)
        ON litho.id=spec.litho_ref
      LEFT JOIN 
        (lithology INNER JOIN catalogue_levels lithology_level ON lithology.level_ref = lithology_level.id)
        ON lithology.id=spec.lithology_ref
      LEFT JOIN 
        (mineralogy mineral INNER JOIN catalogue_levels mineral_level ON mineral.level_ref = mineral_level.id)
        ON mineral.id=spec.mineral_ref
    );

commit;

ANALYZE specimens_flat;

\i ../createindexes_darwinflat.sql


/********** RESET VIEW *************/

create view darwin_flat as 
  select

 row_number() OVER (ORDER BY s.id) AS id, 

  s.category,
  s.collection_ref,
  s.expedition_ref,
  s.gtu_ref,
  s.taxon_ref,
  s.litho_ref,
  s.chrono_ref,
  s.lithology_ref,
  s.mineral_ref,
  s.host_taxon_ref,
  s.host_specimen_ref,
  s.host_relationship,
  s.acquisition_category,
  s.acquisition_date_mask,
  s.acquisition_date,
  s.station_visible,
  s.ig_ref,


  f.collection_type,
  f.collection_code,
  f.collection_name,
  f.collection_is_public,
  f.collection_parent_ref,
  f.collection_path,
  f.expedition_name,
  f.expedition_name_ts,
  f.expedition_name_indexed,

  f.gtu_code,
  f.gtu_from_date_mask,
  f.gtu_from_date,
  f.gtu_to_date_mask,
  f.gtu_to_date,
  f.gtu_elevation,
  f.gtu_elevation_accuracy,
  f.gtu_tag_values_indexed,
  f.gtu_country_tag_value,
  f.gtu_country_tag_indexed,
  f.gtu_province_tag_value,
  f.gtu_province_tag_indexed,
  f.gtu_others_tag_value,
  f.gtu_others_tag_indexed,
  f.gtu_location,

  f.taxon_name,
  f.taxon_name_indexed,
  f.taxon_name_order_by,
  f.taxon_level_ref,
  f.taxon_level_name,
  f.taxon_status,
  f.taxon_path,
  f.taxon_parent_ref,
  f.taxon_extinct,

  f.litho_name,
  f.litho_name_indexed,
  f.litho_name_order_by,
  f.litho_level_ref,
  f.litho_level_name,
  f.litho_status,
  f.litho_local,
  f.litho_color,
  f.litho_path,
  f.litho_parent_ref,

  f.chrono_name,
  f.chrono_name_indexed,
  f.chrono_name_order_by,
  f.chrono_level_ref,
  f.chrono_level_name,
  f.chrono_status,
  f.chrono_local,
  f.chrono_color,
  f.chrono_path,
  f.chrono_parent_ref,

  f.lithology_name,
  f.lithology_name_indexed,
  f.lithology_name_order_by,
  f.lithology_level_ref,
  f.lithology_level_name,
  f.lithology_status,
  f.lithology_local,
  f.lithology_color,
  f.lithology_path,
  f.lithology_parent_ref,

  f.mineral_name,
  f.mineral_name_indexed,
  f.mineral_name_order_by,
  f.mineral_level_ref,
  f.mineral_level_name,
  f.mineral_status,
  f.mineral_local,
  f.mineral_color,
  f.mineral_path,
  f.mineral_parent_ref,

  f.host_taxon_name,
  f.host_taxon_name_indexed,
  f.host_taxon_name_order_by,
  f.host_taxon_level_ref,
  f.host_taxon_level_name,
  f.host_taxon_status,
  f.host_taxon_path,
  f.host_taxon_parent_ref,
  f.host_taxon_extinct,

  f.ig_num,
  f.ig_num_indexed,
  f.ig_date_mask,
  f.ig_date,

  s.id as spec_ref,

  spec_ident_ids,
  spec_coll_ids,
  spec_don_sel_ids,
  i.ind_ident_ids as ind_ident_ids,

  f.with_types,
  f.with_individuals,
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
  INNER JOIN specimens_flat f on f.specimen_ref = s.id
  LEFT JOIN specimen_individuals  i ON s.id = i.specimen_ref 
  LEFT JOIN specimen_parts p ON i.id = p.specimen_individual_ref
;

ALTER VIEW darwin_flat OWNER TO darwin2;

GRANT ALL ON darwin_flat TO darwin2;
GRANT SELECT ON darwin_flat TO d2viewer;

\i ../reports/ticketing/labeling.sql
