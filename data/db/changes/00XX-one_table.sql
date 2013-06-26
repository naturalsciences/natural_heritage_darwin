begin;
set search_path=darwin2,public;
\i  createfunctions.sql

/***
* Move Codes from Spec to parts
**/
CREATE OR REPLACE FUNCTION move_refs() RETURNS boolean
AS $$
DECLARE
  tmp RECORD;
  tmp2 RECORD;
  source_ref INTEGER;
  cnt INTEGER;
BEGIN
   RAISE INFO 'Insert missing indiv';
    INSERT INTO specimen_individuals(specimen_ref)
    (
      SELECT id from specimens s
        where not exists( select 1 from specimen_individuals i where i.specimen_ref = s.id)
    );

    RAISE INFO 'Insert missing parts';
    INSERT INTO specimen_parts(specimen_individual_ref)
    (
      SELECT id from specimen_individuals i
      where not exists( select 1 from specimen_parts p where i.id = p.specimen_individual_ref)
    );
 
   RAISE INFO 'Start moving template_ref from spec with 1 part';
   FOR tmp IN SELECT *, p.id as part_id, s.id as spec_id from 
    specimens s
    INNER JOIN specimen_individuals i on s.id = i.specimen_ref
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref
    where  (select count(*) from spspecimen_individuals i2
      inner join specimen_parts p2 on i2.id = p2.specimen_individual_ref
      where i2.specimen_ref = s.id) = 1
      LOOP
      BEGIN
        UPDATE template_table_record_ref set referenced_relation='specimen_parts', record_id = tmp.part_id
          WHERE  referenced_relation ='specimens' AND record_id=tmp.spec_id;
      EXCEPTION
        when unique_violation then
         --IIIK Nothing
      END;
   END LOOP;

   RAISE INFO 'Start moving template_ref from ind with 1 part';
   FOR tmp IN SELECT *, p.id as part_id, i.id as ind_id from 
    specimen_individuals i
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref

    where  (select count(*) from specimen_parts p2
      where p2.specimen_individual_ref = i.id) = 1
    LOOP
      BEGIN
        UPDATE template_table_record_ref set referenced_relation='specimen_parts', record_id = tmp.part_id
          WHERE  referenced_relation ='specimen_individuals' AND record_id=tmp.ind_id;
      EXCEPTION
        when unique_violation then
         --IIIK Nothing
      END;
   END LOOP;
   


   cnt := 0;
   RAISE INFO 'Start moving Code from spec with 1 code';
   -- Move Codes in spec that have 1 code
   FOR tmp IN select subqry.p_id as p_id, subqry.s_id as s_id, c.* from
      (select s.id as s_id, p.id as p_id from specimen_parts p
      inner join specimen_individuals si on p.specimen_individual_ref = si.id
      inner join specimens s on si.specimen_ref = s.id
      where 1 = (select count(*) from codes where s.id = record_id and referenced_relation = 'specimens')
    ) as subqry inner join
      codes c on subqry.s_id = record_id and referenced_relation = 'specimens'
      LOOP
      BEGIN
      cnt := cnt + 1;

      INSERT INTO codes(
            referenced_relation, record_id, code_category, code_prefix, 
            code_prefix_separator, code, code_suffix, code_suffix_separator, 
            full_code_indexed, code_date, code_date_mask, 
            code_num)
        VALUES('specimen_parts', tmp.p_id, tmp.code_category, tmp.code_prefix, 
            tmp.code_prefix_separator, tmp.code, tmp.code_suffix, tmp.code_suffix_separator, 
            tmp.full_code_indexed, tmp.code_date, tmp.code_date_mask, 
            tmp.code_num);
       IF cnt % 40000 = 0 THEN
        RAISE INFO 'CTN % ' , cnt ;
       END IF;
      EXCEPTION
        when unique_violation then
--          DELETE FROM codes  where id = tmp.code_id;
      END;
   END LOOP;

   --- Move suspicious Rbins Codes
   --- Remove ALL
   RAISE INFO 'Remove codes';
    delete from codes c where referenced_relation ='specimens';


/**********/


   source_ref := -1;
   cnt := 0;
   RAISE INFO 'Start moving template_ref from spec ';
   FOR tmp IN SELECT s.id as source_ref, p.id as p_id from specimens s
     INNER JOIN specimen_individuals i on s.id = i.specimen_ref
     INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref
    LOOP
      cnt := cnt + 1;

      INSERT INTO comments(referenced_relation, record_id, notion_concerned, comment, comment_indexed) 
        (SELECT 'specimen_parts', tmp.p_id, notion_concerned, comment, comment_indexed
          FROM comments c WHERE c.referenced_relation='specimens' and record_id=tmp.source_ref  );


      INSERT INTO ext_links(referenced_relation, record_id, url, comment, comment_indexed) 
        (SELECT 'specimen_parts', tmp.p_id, url, comment, comment_indexed
          FROM ext_links c WHERE c.referenced_relation='specimens' and record_id=tmp.source_ref
            AND not exists( 
              select 1 from ext_links c2 where  referenced_relation='specimens' and record_id=tmp.source_ref and c2.url=c.url
             ) 
         );

      INSERT INTO catalogue_bibliography(referenced_relation, record_id, bibliography_ref) 
        (SELECT 'specimen_parts', tmp.p_id, bibliography_ref
          FROM catalogue_bibliography c WHERE c.referenced_relation='specimens' and record_id=tmp.source_ref  ); 

      INSERT INTO catalogue_people(
            referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        ( SELECT 'specimen_parts', tmp.p_id, people_type, people_sub_type, order_by, people_ref
         FROM catalogue_people c WHERE c.referenced_relation='specimens' and record_id=tmp.source_ref  ); 

   FOR tmp2 IN SELECT * from catalogue_properties c where referenced_relation='specimens' and record_id=tmp.source_ref
   LOOP
      BEGIN
      INSERT INTO catalogue_properties(referenced_relation, record_id, 
        property_type, property_sub_type, 
        property_sub_type_indexed, property_qualifier, property_qualifier_indexed, 
        date_from_mask, date_from, date_to_mask, date_to, property_unit, 
        property_accuracy_unit, property_method, property_method_indexed, 
        property_tool, property_tool_indexed
      ) 
        VALUES ('specimen_parts', tmp.p_id,
          tmp2.property_type, tmp2.property_sub_type,
          tmp2.property_sub_type_indexed, tmp2.property_qualifier, tmp2.property_qualifier_indexed, 
          tmp2.date_from_mask, tmp2.date_from, tmp2.date_to_mask, tmp2.date_to, tmp2.property_unit, 
          tmp2.property_accuracy_unit, tmp2.property_method, tmp2.property_method_indexed, 
          tmp2.property_tool, tmp2.property_tool_indexed
         );
      
      INSERT INTO properties_values(
          property_ref, property_value, property_value_unified, property_accuracy, 
          property_accuracy_unified)
        (
          SELECT currval('catalogue_properties_id_seq'), property_value, property_value_unified, property_accuracy, 
            property_accuracy_unified from properties_values where property_ref = tmp2.id
        );
      EXCEPTION
        when unique_violation then --NOOP
      END;
   END LOOP;


   FOR tmp2 IN SELECT * from identifications c where referenced_relation='specimens' and record_id=tmp.source_ref
   LOOP

      INSERT INTO identifications (referenced_relation, record_id, 
        notion_concerned, notion_date, 
        notion_date_mask, value_defined, value_defined_indexed, determination_status, 
        order_by
      ) 
        VALUES ('specimen_parts', tmp.p_id,
          tmp2.notion_concerned, tmp2.notion_date, 
          tmp2.notion_date_mask, tmp2.value_defined, tmp2.value_defined_indexed, tmp2.determination_status, 
          tmp2.order_by
         );
      
        INSERT INTO catalogue_people(record_id, referenced_relation,  people_type, people_sub_type, 
            order_by, people_ref
            )
          (SELECT currval('identifications_id_seq'), referenced_relation, people_type, people_sub_type, 
            order_by, people_ref
            from catalogue_people where record_id = tmp2.id and referenced_relation = 'identifications'
          );
            
   END LOOP;
      IF cnt % 10000 = 0 THEN
        RAISE INFO 'CTN %', cnt ;
      END IF;
 END LOOP;

  RAISE INFO 'Delete spec comments';
  DELETE FROM comments where referenced_relation='specimens';
  RAISE INFO 'Delete spec ext_links';
  DELETE FROM ext_links where referenced_relation='specimens';
  RAISE INFO 'Delete spec properties';
  DELETE FROM catalogue_properties where referenced_relation='specimens';
  RAISE INFO 'Delete spec ident';
  DELETE FROM identifications where referenced_relation='specimens';
  DELETE FROM catalogue_people where referenced_relation='specimens';
  DELETE FROM catalogue_bibliography where referenced_relation='specimens';

  /*******
  * End of SPEC START OF INDIV
  *****/


   source_ref := -1;
   RAISE INFO 'Start moving template_ref from indiv ';
   FOR tmp IN SELECT i.id as source_ref, p.id as p_id from specimen_individuals
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref
    LOOP

      INSERT INTO comments(referenced_relation, record_id, notion_concerned, comment, comment_indexed) 
        (SELECT 'specimen_parts', tmp.p_id, notion_concerned, comment, comment_indexed
          FROM comments c WHERE c.referenced_relation='specimen_individuals' and record_id=tmp.source_ref  );


      INSERT INTO ext_links(referenced_relation, record_id, url, comment, comment_indexed) 
        (SELECT 'specimen_parts', tmp.p_id, url, comment, comment_indexed
          FROM ext_links c WHERE c.referenced_relation='specimen_individuals' and record_id=tmp.source_ref
            AND not exists( 
              select 1 from ext_links c2 where  referenced_relation='specimen_individuals' and record_id=tmp.source_ref and c2.url=c.url
             ) 
         );

         
   FOR tmp2 IN SELECT * from catalogue_properties c where referenced_relation='specimen_individuals' and record_id=tmp.source_ref
   LOOP

      INSERT INTO catalogue_properties(referenced_relation, record_id, 
        property_type, property_sub_type, 
        property_sub_type_indexed, property_qualifier, property_qualifier_indexed, 
        date_from_mask, date_from, date_to_mask, date_to, property_unit, 
        property_accuracy_unit, property_method, property_method_indexed, 
        property_tool, property_tool_indexed
      ) 
        VALUES ('specimen_parts', tmp.p_id,
          tmp2.property_type, tmp2.property_sub_type,
          tmp2.property_sub_type_indexed, tmp2.property_qualifier, tmp2.property_qualifier_indexed, 
          tmp2.date_from_mask, tmp2.date_from, tmp2.date_to_mask, tmp2.date_to, tmp2.property_unit, 
          tmp2.property_accuracy_unit, tmp2.property_method, tmp2.property_method_indexed, 
          tmp2.property_tool, tmp2.property_tool_indexed
         );
      
        INSERT INTO properties_values(
            property_ref, property_value, property_value_unified, property_accuracy, 
            property_accuracy_unified)
        (
          SELECT currval('catalogue_properties_id_seq'), property_value, property_value_unified, property_accuracy, 
            property_accuracy_unified from properties_values where property_ref = tmp2.id
        );
            
   END LOOP;
   --END LOOP;

   FOR tmp2 IN SELECT * from identifications c where referenced_relation='specimen_individuals' and record_id=tmp.source_ref
   LOOP

      INSERT INTO identifications (referenced_relation, record_id, 
        notion_concerned, notion_date, 
        notion_date_mask, value_defined, value_defined_indexed, determination_status, 
        order_by
      ) 
        VALUES ('specimen_parts', tmp.p_id,
          tmp2.notion_concerned, tmp2.notion_date, 
          tmp2.notion_date_mask, tmp2.value_defined, tmp2.value_defined_indexed, tmp2.determination_status, 
          tmp2.order_by
         );
      
        INSERT INTO catalogue_people(record_id, referenced_relation,  people_type, people_sub_type, 
            order_by, people_ref
            )
          (SELECT currval('identifications_id_seq'), referenced_relation, people_type, people_sub_type, 
            order_by, people_ref
            from catalogue_people where record_id = tmp2.id and referenced_relation = 'identifications'
          );
            
   END LOOP;
 END LOOP;
   
  RAISE INFO 'Delete ind comments';
  DELETE FROM comments where referenced_relation='specimen_individuals';
  RAISE INFO 'Delete ind ext_links';
  DELETE FROM ext_links where referenced_relation='specimen_individuals';
  RAISE INFO 'Delete ind properties';
  DELETE FROM catalogue_properties where referenced_relation='specimen_individuals';
  RAISE INFO 'Delete ind ident';
  DELETE FROM identifications where referenced_relation='specimen_individuals';
/*****/


   RETURN TRUE;
END;
$$
language plpgsql;

SET SESSION session_replication_role = replica;

--select move_refs();

SET SESSION session_replication_role = origin;

ALTER TABLE collections DROP COLUMN code_part_code_auto_copy;


/** Cleanup migration scripts ****/
drop function move_refs();




alter INDEX pk_specimens rename to pk_old_specimens;



create table new_specimens
       (
        id serial,
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
        ig_ref integer,

        type varchar not null default 'specimen',
        type_group varchar not null default 'specimen',
        type_search varchar not null default 'specimen',
        sex varchar not null default 'undefined',
        stage varchar not null default 'undefined',
        state varchar not null default 'not applicable',
        social_status varchar not null default 'not applicable',
        rock_form varchar not null default 'not applicable',


        specimen_part varchar not null default 'specimen',
        complete boolean not null default true,
        institution_ref integer,
        building varchar,
        floor varchar,
        room varchar,
        row varchar,
        shelf varchar,
        container varchar,
        sub_container varchar,
        container_type varchar not null default 'container',
        sub_container_type varchar not null default 'container',
        container_storage varchar not null default 'dry',
        sub_container_storage varchar not null default 'dry',
        surnumerary boolean not null default false,
        specimen_status varchar not null default 'good state',
        specimen_count_min integer not null default 1,
        specimen_count_max integer not null default 1,
        object_name text,
        object_name_indexed text not null default '',


    spec_ident_ids integer[] not null default '{}',
    spec_coll_ids integer[] not null default '{}',
    spec_don_sel_ids integer[] not null default '{}',
    
    collection_type varchar,
    collection_code varchar,
    collection_name varchar,
    collection_is_public boolean,
    collection_parent_ref integer,
    collection_path varchar,
    expedition_name varchar,
    expedition_name_indexed varchar,

    gtu_code varchar,
    gtu_from_date_mask integer,
    gtu_from_date timestamp,
    gtu_to_date_mask integer,
    gtu_to_date timestamp,
    gtu_tag_values_indexed varchar[],
    gtu_country_tag_value varchar,
    gtu_country_tag_indexed varchar[],
    gtu_province_tag_value varchar,
    gtu_province_tag_indexed varchar[],
    gtu_others_tag_value varchar,
    gtu_others_tag_indexed varchar[],
    gtu_elevation double precision,
    gtu_elevation_accuracy double precision,
    gtu_location GEOGRAPHY(POLYGON,4326),

    taxon_name varchar,
    taxon_name_indexed varchar,
    taxon_level_ref integer,
    taxon_level_name varchar,
    taxon_status varchar,
    taxon_path varchar,
    taxon_parent_ref integer,
    taxon_extinct boolean,

    litho_name varchar,
    litho_name_indexed varchar,
    litho_level_ref integer,
    litho_level_name varchar,
    litho_status varchar,
    litho_local boolean,
    litho_color varchar,
    litho_path varchar,
    litho_parent_ref integer,

    chrono_name varchar,
    chrono_name_indexed varchar,
    chrono_level_ref integer,
    chrono_level_name varchar,
    chrono_status varchar,
    chrono_local boolean,
    chrono_color varchar,
    chrono_path varchar,
    chrono_parent_ref integer,

    lithology_name varchar,
    lithology_name_indexed varchar,
    lithology_level_ref integer,
    lithology_level_name varchar,
    lithology_status varchar,
    lithology_local boolean,
    lithology_color varchar,
    lithology_path varchar,
    lithology_parent_ref integer,

    mineral_name varchar,
    mineral_name_indexed varchar,
    mineral_level_ref integer,
    mineral_level_name varchar,
    mineral_status varchar,
    mineral_local boolean,
    mineral_color varchar,
    mineral_path varchar,
    mineral_parent_ref integer,

    host_taxon_name varchar,
    host_taxon_name_indexed varchar,
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
    spec_id integer,
    ind_id integer,

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
        constraint fk_specimens_igs foreign key (ig_ref) references igs(id),

        constraint fk_specimen_institutions foreign key (institution_ref) references people(id) ON DELETE no action,
        constraint chk_chk_specimens_minmax check (specimen_count_min <= specimen_count_max),
        constraint chk_chk_specimens_min check (specimen_count_min >= 0)
       );



comment on table new_specimens is 'Specimens or batch of specimens stored in collection';
comment on column new_specimens.id is 'Unique identifier of a specimen or batch of specimens';
comment on column new_specimens.collection_ref is 'Reference of collection the specimen is grouped under - id field of collections table';
comment on column new_specimens.expedition_ref is 'When acquisition category is expedition, contains the reference of the expedition having conducted to the current specimen capture - id field of expeditions table';
comment on column new_specimens.gtu_ref is 'Reference of the sampling location the specimen is coming from - id field of gtu table';
comment on column new_specimens.litho_ref is 'When encoding a rock, mineral or paleontologic specimen, contains the reference of lithostratigraphic unit the specimen have been found into - id field of lithostratigraphy table';
comment on column new_specimens.chrono_ref is 'When encoding a rock, mineral or paleontologic specimen, contains the reference of chronostratigraphic unit the specimen have been found into - id field of chronostratigraphy table';
comment on column new_specimens.taxon_ref is 'When encoding a ''living'' specimen, contains the reference of the taxon unit defining the specimen - id field of taxonomy table';
comment on column new_specimens.host_relationship is 'When current specimen encoded is in a host relationship with an other specimen or taxon, this field contains the type of relationship between them: symbiosis, parasitism, saprophytism,...';
comment on column new_specimens.host_specimen_ref is 'When current specimen encoded is in a host relationship with an other specimen, this field contains reference of the host specimen - recursive reference';
comment on column new_specimens.acquisition_category is 'Describe how the specimen was collected: expedition, donation,...';
comment on column new_specimens.acquisition_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column new_specimens.acquisition_date is 'Date Composed (if possible) of the acquisition';
comment on column new_specimens.station_visible is 'Flag telling if the sampling location can be visible or must be hidden for the specimen encoded';
comment on column new_specimens.lithology_ref is 'Reference of a rock classification unit associated to the specimen encoded - id field of lithology table';
comment on column new_specimens.mineral_ref is 'Reference of a mineral classification unit associated to the specimen encoded - id field of mineralogy table';
comment on column new_specimens.host_taxon_ref is 'Reference of taxon definition defining the host which holds the current specimen - id field of taxonomy table';
comment on column new_specimens.ig_ref is 'Reference of ig number this specimen has been associated to';
comment on column new_specimens.category is 'Type of specimen encoded: a physical object stored in collections, an observation, a figurate specimen,...';

comment on column new_specimens.type is 'Special status given to specimen: holotype, paratype,...';
comment on column new_specimens.type_group is 'For some special status, a common appelation is used - ie: topotype and cotype are joined into a common appelation of syntype';
comment on column new_specimens.type_search is 'On the interface, the separation in all special status is not suggested for non official appelations. For instance, an unified grouping name is provided: type for non official appelation,...';
comment on column new_specimens.sex is 'sex: male , female,...';
comment on column new_specimens.stage is 'stage: adult, juvenile,...';
comment on column new_specimens.state is 'state - a sex complement: ovigerous, pregnant,...';
comment on column new_specimens.social_status is 'For social specimens, give the social status/role of the specimen in colony';
comment on column new_specimens.rock_form is 'For rock specimens, a descriptive form can be given: polygonous,...';

comment on column new_specimens.specimen_part is 'Description of the part stored in conservatory: the whole specimen or a given precise part such as skelleton, head, fur,...';
comment on column new_specimens.building is 'Building the specimen is stored in';
comment on column new_specimens.floor is 'Floor the specimen is stored in';
comment on column new_specimens.room is 'Room the specimen is stored in';
comment on column new_specimens.row is 'Row the specimen is stored in';
comment on column new_specimens.shelf is 'Shelf the specimen is stored in';
comment on column new_specimens.container is 'Container the specimen is stored in';
comment on column new_specimens.sub_container is 'Sub-Container the specimen is stored in';
comment on column new_specimens.container_type is 'Type of container: box, plateau-caisse,...';
comment on column new_specimens.sub_container_type is 'Type of sub-container: slide, needle,...';
comment on column new_specimens.container_storage is 'Conservative medium used: formol, alcohool, dry,...';
comment on column new_specimens.sub_container_storage is 'Conservative medium used: formol, alcohool, dry,...';
comment on column new_specimens.surnumerary is 'Tells if this specimen has been added after first inventory';
comment on column new_specimens.specimen_status is 'Specimen status: good state, lost, damaged,...';
comment on column new_specimens.specimen_count_min is 'Minimum number of specimens';
comment on column new_specimens.specimen_count_max is 'Maximum number of specimens';
comment on column new_specimens.complete is 'Flag telling if specimen is complete or not';


INSERT INTO new_specimens (
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
        ig_ref,

        type,
        type_group,
        type_search,
        sex,
        stage,
        state,
        social_status,
        rock_form,

        specimen_part,
        specimen_status,
        institution_ref,
        building,
        floor,
        room,
        row,
        shelf,
        container,
        sub_container,
        container_type,
        sub_container_type,
        container_storage,
        sub_container_storage,
        surnumerary,
        specimen_count_min,
        specimen_count_max,
        object_name,
        object_name_indexed,
        complete,


    spec_ident_ids,
    spec_coll_ids,
    spec_don_sel_ids,


    collection_type,
    collection_code,
    collection_name,
    collection_is_public,
    collection_parent_ref,
    collection_path,
    expedition_name,
    expedition_name_indexed,

    gtu_code,
    gtu_from_date_mask,
    gtu_from_date,
    gtu_to_date_mask,
    gtu_to_date,
    gtu_tag_values_indexed,
    gtu_country_tag_value,
    gtu_country_tag_indexed,
    gtu_province_tag_value,
    gtu_province_tag_indexed,
    gtu_others_tag_value,
    gtu_others_tag_indexed ,
    gtu_elevation,
    gtu_elevation_accuracy,
    gtu_location,

    taxon_name,
    taxon_name_indexed,
    taxon_level_ref,
    taxon_level_name,
    taxon_status,
    taxon_path,
    taxon_parent_ref,
    taxon_extinct,

    litho_name,
    litho_name_indexed,
    litho_level_ref,
    litho_level_name,
    litho_status,
    litho_local,
    litho_color,
    litho_path,
    litho_parent_ref ,

    chrono_name,
    chrono_name_indexed,
    chrono_level_ref,
    chrono_level_name,
    chrono_status,
    chrono_local,
    chrono_color,
    chrono_path,
    chrono_parent_ref,

    lithology_name,
    lithology_name_indexed,
    lithology_level_ref,
    lithology_level_name,
    lithology_status,
    lithology_local,
    lithology_color,
    lithology_path,
    lithology_parent_ref,

    mineral_name,
    mineral_name_indexed,
    mineral_level_ref,
    mineral_level_name,
    mineral_status,
    mineral_local,
    mineral_color,
    mineral_path,
    mineral_parent_ref,

    host_taxon_name,
    host_taxon_name_indexed,
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
    spec_id,
    ind_id
)
(
SELECT 
  p.id,
  f.category,
  f.collection_ref,
  f.expedition_ref,
  f.gtu_ref,
  f.taxon_ref,
  f.litho_ref,
  f.chrono_ref,
  f.lithology_ref,
  f.mineral_ref,
  f.host_taxon_ref,
  f.host_specimen_ref,
  f.host_relationship,
  f.acquisition_category,
  f.acquisition_date_mask,
  f.acquisition_date,
  f.station_visible,
  f.ig_ref,

  f.individual_type,
  f.individual_type_group,
  f.individual_type_search,
  f.individual_sex,
  f.individual_state,
  f.individual_stage,
  f.individual_social_status,
  f.individual_rock_form,

  p.specimen_part,
  p.specimen_status,
  p.institution_ref,
  p.building,
  p.floor,
  p.room,
  p.row,
  p.shelf,
  p.container,
  p.sub_container,
  p.container_type,
  p.sub_container_type,
  p.container_storage,
  p.sub_container_storage,
  p.surnumerary,
  p.specimen_part_count_min,
  p.specimen_part_count_max,
  p.object_name,
  p.object_name_indexed,
  p.complete,
  
  
  spec_ident_ids,
  spec_coll_ids,
  spec_don_sel_ids,
  

  f.collection_type,
  f.collection_code,
  f.collection_name,
  f.collection_is_public,
  f.collection_parent_ref,
  f.collection_path,
  f.expedition_name,
  f.expedition_name_indexed,

  f.gtu_code,
  f.gtu_from_date_mask,
  f.gtu_from_date,
  f.gtu_to_date_mask,
  f.gtu_to_date,
  f.gtu_tag_values_indexed,
  f.gtu_country_tag_value,
  f.gtu_country_tag_indexed,
  f.gtu_province_tag_value,
  f.gtu_province_tag_indexed,
  f.gtu_others_tag_value,
  f.gtu_others_tag_indexed,
  f.gtu_elevation,
  f.gtu_elevation_accuracy,
  f.gtu_location,

  f.taxon_name,
  f.taxon_name_indexed,
  f.taxon_level_ref,
  f.taxon_level_name,
  f.taxon_status,
  f.taxon_path,
  f.taxon_parent_ref,
  f.taxon_extinct,

  f.litho_name,
  f.litho_name_indexed,
  f.litho_level_ref,
  f.litho_level_name,
  f.litho_status,
  f.litho_local,
  f.litho_color,
  f.litho_path,
  f.litho_parent_ref,

  f.chrono_name,
  f.chrono_name_indexed,
  f.chrono_level_ref,
  f.chrono_level_name,
  f.chrono_status,
  f.chrono_local,
  f.chrono_color,
  f.chrono_path,
  f.chrono_parent_ref,

  f.lithology_name,
  f.lithology_name_indexed,
  f.lithology_level_ref,
  f.lithology_level_name,
  f.lithology_status,
  f.lithology_local,
  f.lithology_color,
  f.lithology_path,
  f.lithology_parent_ref,

  f.mineral_name,
  f.mineral_name_indexed,
  f.mineral_level_ref,
  f.mineral_level_name,
  f.mineral_status,
  f.mineral_local,
  f.mineral_color,
  f.mineral_path,
  f.mineral_parent_ref,

  f.host_taxon_name,
  f.host_taxon_name_indexed,
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
  f.spec_ref, 
  f.individual_ref


FROM
darwin_flat f
inner join specimen_parts p  ON f.part_ref = p.id
);



ALTER TABLE specimen_parts DROP CONSTRAINT fk_specimen_parts_parent_ref;
ALTER TABLE loan_items DROP CONSTRAINT fk_loan_items_part_ref;

ALTER TABLE loan_items RENAME COLUMN part_ref TO specimen_ref;


ALTER TABLE specimen_collecting_methods DROP CONSTRAINT fk_specimen_collecting_methods_specimen;
ALTER TABLE specimen_collecting_tools DROP CONSTRAINT fk_specimen_collecting_tools_specimen;
ALTER TABLE specimens_accompanying DROP CONSTRAINT fk_specimens_accompanying_specimens;


drop view labeling;
drop view darwin_flat;


drop table specimen_parts;
drop table specimen_individuals;
drop table specimens_flat;

alter table specimens drop constraint fk_specimens_host_specimen;
drop table specimens;

alter table new_specimens rename to specimens;

ALTER TABLE loan_items
  ADD CONSTRAINT fk_loan_items_specimen_ref FOREIGN KEY (specimen_ref)
      REFERENCES specimens (id) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE SET NULL;

CREATE INDEX  idx_gin_specimens_gtu_tag_values_indexed on specimens using gin(gtu_tag_values_indexed);
CREATE INDEX  idx_gin_specimens_gtu_country_tag_indexed_indexed on specimens using gin(gtu_country_tag_indexed);
CREATE INDEX  idx_gist_specimens_gtu_location ON specimens USING GIST ( gtu_location );
CREATE INDEX  idx_gist_specimens_gtu_location_geom ON specimens USING GIST ( (gtu_location::geometry) );

CREATE INDEX  idx_specimens_host_specimen_ref on specimens(host_specimen_ref);
CREATE INDEX  idx_specimens_host_taxon_ref on specimens(host_taxon_ref);

CREATE INDEX  idx_specimens_category on specimens(category);

/*** BTree indexes for search purposes in Darwin flat table ***/
CREATE INDEX  idx_specimens_station_visible on specimens(station_visible);
CREATE INDEX  idx_darwin_flat_gtu_code ON specimens USING gin
  (gtu_code gin_trgm_ops);
CREATE INDEX  idx_specimens_gtu_from_date_mask on specimens(gtu_from_date_mask);
CREATE INDEX  idx_specimens_gtu_to_date_mask on specimens(gtu_to_date_mask);
CREATE INDEX  idx_specimens_gtu_to_date on specimens(gtu_to_date);
CREATE INDEX  idx_specimens_gtu_from_date on specimens(gtu_from_date);
CREATE INDEX  idx_specimens_taxon_name_indexed on specimens(taxon_name_indexed);

CREATE INDEX  idx_specimens_collection_is_public on specimens(collection_is_public);
CREATE INDEX  idx_specimens_collection_name on specimens(collection_name);

CREATE INDEX  idx_gin_trgm_specimens_expedition_name_indexed on specimens using gin(expedition_name_indexed gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_specimens_taxon_name_indexed on specimens using gin(taxon_name_indexed gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_specimens_taxon_path on specimens using gin(taxon_path gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_specimens_ig_num ON specimens USING gin (ig_num_indexed gin_trgm_ops);

CREATE INDEX  idx_gin_specimens_spec_ident_ids on specimens using gin(spec_ident_ids);
CREATE INDEX  idx_gin_specimens_spec_coll_ids on specimens using gin(spec_coll_ids);
CREATE INDEX  idx_gin_specimens_spec_don_sel_ids on specimens using gin(spec_don_sel_ids);



CREATE INDEX idx_specimens_chrono_ref ON specimens  (chrono_ref)  WHERE chrono_ref <> 0;
CREATE INDEX idx_specimens_expedition_ref ON specimens  (expedition_ref) WHERE expedition_ref <> 0;
CREATE INDEX idx_specimens_gtu_ref ON specimens  (gtu_ref) WHERE gtu_ref <> 0;
CREATE INDEX idx_specimens_ig_ref ON specimens  (ig_ref);
CREATE INDEX idx_specimens_litho_ref ON specimens  (litho_ref) WHERE litho_ref <> 0;
CREATE INDEX idx_specimens_lithology_ref ON specimens  (lithology_ref) WHERE lithology_ref <> 0;
CREATE INDEX idx_specimens_mineral_ref ON specimens  (mineral_ref) WHERE mineral_ref <> 0;
CREATE INDEX idx_specimens_taxon_ref ON specimens  (taxon_ref) WHERE taxon_ref <> 0;

CREATE TRIGGER fct_cpy_trg_del_dict_specimens AFTER UPDATE OR DELETE
  ON specimens FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();
  
CREATE TRIGGER fct_cpy_trg_ins_update_dict_specimens AFTER INSERT OR UPDATE
  ON specimens FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER trg_chk_specimencollectionallowed BEFORE INSERT OR UPDATE OR DELETE
  ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_chk_specimencollectionallowed();

CREATE TRIGGER trg_clr_referencerecord_specimens AFTER DELETE
  ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();

CREATE TRIGGER trg_cpy_ig_to_loan_items AFTER UPDATE
  ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_cpy_ig_to_loan_items();

CREATE TRIGGER trg_cpy_updatehosts AFTER UPDATE ON specimens
  FOR EACH ROW EXECUTE PROCEDURE fct_cpy_updatehosts();

CREATE TRIGGER trg_cpy_updatespechostimpact  BEFORE UPDATE
  ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_cpy_updatespechostimpact();

CREATE TRIGGER trg_trk_log_table_specimens AFTER INSERT OR UPDATE OR DELETE
  ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_cpy_fulltoindex_s  BEFORE INSERT OR UPDATE
  ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();

CREATE TRIGGER trg_update_specimens_darwin_flat BEFORE INSERT OR UPDATE
        ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_update_specimen_flat();

CREATE INDEX idx_labeling_specimens_type ON specimens
  USING gin (labeling_individual_type_for_indexation(type));

CREATE INDEX idx_specimen_rock_form ON specimens (rock_form);

CREATE INDEX idx_specimens_sex ON specimens (sex)
  WHERE sex::text <> ALL (ARRAY['undefined', 'unknown']::text[]);
  
CREATE INDEX idx_specimens_social_status ON specimens (social_status)
  WHERE social_status::text <> 'not applicable'::text;

CREATE INDEX idx_specimens_stage ON specimens  (stage)
  WHERE stage::text <> ALL (ARRAY['undefined', 'unknown']::text[]);
  
CREATE INDEX idx_specimens_state ON specimens (state)
  WHERE state::text <> 'not applicable'::text;

CREATE INDEX idx_specimens_type_search ON specimens (type_search)
  WHERE type_search::text <> 'specimen'::text;



CREATE INDEX idx_labeling_code ON specimens USING gin (labeling_code_for_indexation(id)) ;
CREATE INDEX idx_labeling_part ON specimens USING gin (labeling_part_for_indexation(specimen_part) );
CREATE INDEX idx_specimens_container ON specimens (container ) WHERE NOT container IS NULL;
CREATE INDEX idx_specimens_container_storage ON specimens (container_storage );
CREATE INDEX idx_specimens_container_type ON specimens (container_type );
CREATE INDEX idx_specimens_object_name_indexed ON specimens (object_name_indexed );
CREATE INDEX idx_specimens_room ON specimens(room ) WHERE NOT room IS NULL;
CREATE INDEX idx_specimens_row ON specimens("row" ) WHERE NOT "row" IS NULL;
CREATE INDEX idx_specimens_shelf ON specimens (shelf ) WHERE NOT shelf IS NULL;
CREATE INDEX idx_specimens_ ON specimens (specimen_part);
CREATE INDEX idx_specimens_institution_ref ON specimens (institution_ref);
CREATE INDEX idx_specimens_sub_container ON specimens (sub_container) WHERE NOT sub_container IS NULL;
CREATE INDEX idx_specimens_sub_container_storage ON specimens (sub_container_storage );
CREATE INDEX idx_specimens_sub_container_type ON specimens (sub_container_type );

/******* End of creation of spec **********/
CREATE INDEX  idx_specimens_spec_id on specimens(spec_id);


update my_saved_searches set subject='specimens';
delete from preferences where pref_key in('search_cols_specimen', 'search_cols_individual', 'gtu_google_activated') ;
update preferences set pref_key='search_cols_specimen' where pref_key='search_cols_part';
update flat_dict set referenced_relation='specimens' where referenced_relation in ('specimen_individuals', 'specimen_part');


alter table specimen_collecting_methods add column old_id integer;
ALTER TABLE specimen_collecting_tools add column old_id integer;
ALTER TABLE specimens_accompanying add column old_id integer;

update specimen_collecting_methods set old_id = specimen_ref;
update specimen_collecting_tools set old_id = specimen_ref;
update specimens_accompanying set old_id = specimen_ref;

insert into specimen_collecting_methods (specimen_ref, collecting_method_ref)
( select s.id , m.collecting_method_ref
  from specimens s 
  inner join specimen_collecting_methods m on m.old_id = s.spec_id );

insert into specimen_collecting_tools (specimen_ref, collecting_tool_ref)
( select s.id , m.collecting_tool_ref 
  from specimens s 
  inner join specimen_collecting_tools m on m.old_id = s.spec_id );
  
insert into specimens_accompanying (specimen_ref, accompanying_type, taxon_ref, mineral_ref, form, quantity, unit)
( select s.id , a.accompanying_type, a.taxon_ref, a.mineral_ref, a.form, a.quantity, a.unit
  from specimens s 
  inner join specimens_accompanying a on a.old_id = s.spec_id );
  
delete from specimen_collecting_methods where old_id is not null;
delete from specimen_collecting_tools where old_id is not null;
delete from specimens_accompanying where old_id is not null;

alter table specimen_collecting_methods drop column old_id;
alter table specimen_collecting_tools drop column old_id;
alter table specimens_accompanying drop column old_id;

ALTER TABLE specimen_collecting_methods
  ADD CONSTRAINT fk_specimen_collecting_methods_specimen FOREIGN KEY (specimen_ref)
      REFERENCES specimens (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;
      
ALTER TABLE specimen_collecting_tools
  ADD CONSTRAINT fk_specimen_collecting_tools_specimen FOREIGN KEY (specimen_ref)
      REFERENCES specimens (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE specimens_accompanying
  ADD CONSTRAINT fk_specimens_accompanying_specimens FOREIGN KEY (specimen_ref)
      REFERENCES specimens (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;


update specimens s1 set host_specimen_ref = (select id from specimens s2 where s2.spec_id = s1.host_specimen_ref limit 1)
  where host_specimen_ref is not null;

alter table specimens add constraint fk_specimens_host_specimen foreign key (host_specimen_ref) references specimens(id) on delete set null;
-- 
--- Move Files
--- Move  my_widgets
-- ADD labeling back!
-- import

DROP INDEX idx_specimens_spec_id;
alter table specimens drop column ind_id;
alter table specimens drop column spec_id;
drop function chk_specimens_not_loaned();


\i  createfunctions.sql


rollback;