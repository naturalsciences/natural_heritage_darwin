begin;
set search_path=darwin2,public;

create table reports
       (
        id serial,
        user_ref integer not null,
        name varchar not null,
        uri varchar,
        lang char(2) not null,
        format varchar not null default 'csv',
        parameters hstore,
        CONSTRAINT pk_reports PRIMARY KEY (id),
        CONSTRAINT fk_reports_users FOREIGN KEY (user_ref)
        REFERENCES users (id) MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE CASCADE
       );
comment on table reports is 'Table to handle users reports asking';
comment on column reports.user_ref is 'The referenced user id';
comment on column reports.name is 'The report name';
comment on column reports.uri is 'The path where the report file is stored, if uri is not null then the report has already been launched';
comment on column reports.lang is 'The lang asked for this report';
comment on column reports.format is 'The file type of the report file, generaly csv or xls';
comment on column reports.parameters is 'if the report required some information (such as collection_ref), they are here'

CREATE OR REPLACE FUNCTION fct_imp_checker_gtu(line staging, import boolean default false)  RETURNS boolean
AS $$
DECLARE
  ref_rec integer :=0;
  tags staging_tag_groups ;
  tags_tag RECORD;
  update_count integer;
  tag_groups_line RECORD;
BEGIN
  IF import THEN
    /* If gtu_ref already defined, that means that check was already 
       made for the line and there's no need to reassociate it 
    */
    IF line.gtu_ref is not null THEN
      RETURN true;
    END IF;
    /* If no code is given, not even from date and not even tags (tag_groups here), 
       that means there's not enough information to associate a gtu 
    */
    IF (line.gtu_code is null OR COALESCE(fullToIndex(line.gtu_code),'')  = '') AND (line.gtu_from_date is null) AND NOT EXISTS (select 1 from staging_tag_groups g where g.staging_ref = line.id ) THEN
      RETURN true;
    END IF;
    /* Otherwise, we should try to associate a gtu_ref */
    select substr.id into ref_rec from (
      /* This part try to select gtu id for line.gtu_code NULL or line.gtu_code = '' making the comparison on all the
         other fields ensuring uniqueness (latitude, longitude, from_date and to_date)
         The criteria position('import/' in code) > 0 filter also on the already imported gtu without code only
      */
      select id from gtu g where
        position('import/' in code) > 0 AND
        COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
        COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
        COALESCE(fullToIndex(line.gtu_code), '') = '' AND
        fct_mask_date(gtu_from_date,gtu_from_date_mask) = fct_mask_date(COALESCE(line.gtu_from_date, '01/01/0001')::timestamp,line.gtu_from_date_mask) AND
        fct_mask_date(gtu_to_date,gtu_to_date_mask) = fct_mask_date(COALESCE(line.gtu_to_date, '31/12/2038')::timestamp,line.gtu_to_date_mask)
      /* if we're not in the case of already imported gtu without code,
         we've got to find a gtu that correspond to the criterias of the current line
      */
      union
      select id from gtu g where
        position('import/' in code) = 0 AND
        COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
        COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
        COALESCE(fullToIndex(code),'') = COALESCE(fullToIndex(line.gtu_code),'') AND
        fct_mask_date(gtu_from_date,gtu_from_date_mask) = fct_mask_date(COALESCE(line.gtu_from_date, '01/01/0001')::timestamp,line.gtu_from_date_mask) AND
        fct_mask_date(gtu_to_date,gtu_to_date_mask) = fct_mask_date(COALESCE(line.gtu_to_date, '31/12/2038')::timestamp,line.gtu_to_date_mask)
        LIMIT 1
      ) as substr
      WHERE substr.id != 0 LIMIT 1;

    /* If no corresponding gtu found and we've chosen to import... insert the new gtu */
    IF NOT FOUND THEN
      INSERT into gtu
        (code, 
         gtu_from_date_mask, 
         gtu_from_date,
         gtu_to_date_mask, 
         gtu_to_date, 
         latitude, 
         longitude, 
         lat_long_accuracy, 
         elevation, 
         elevation_accuracy
        )
      VALUES
      (
        CASE COALESCE(fullToIndex(line.gtu_code),'') WHEN '' THEN 'import/'|| line.import_ref || '/' || line.id ELSE line.gtu_code END, 
        COALESCE(line.gtu_from_date_mask,0), 
        COALESCE(line.gtu_from_date, '01/01/0001'),
        COALESCE(line.gtu_to_date_mask,0), 
        COALESCE(line.gtu_to_date, '31/12/2038'),
        line.gtu_latitude, 
        line.gtu_longitude, 
        line.gtu_lat_long_accuracy, 
        line.gtu_elevation, 
        line.gtu_elevation_accuracy
      )
      RETURNING id INTO line.gtu_ref;
      /* The new id is returned in line.gtu_ref and stored in ref_rec so it can be used further on */
      ref_rec := line.gtu_ref;
      /* Browse all tags to try importing them one by one and associate them with the newly created gtu */
      FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        BEGIN
          INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
          SELECT ref_rec,tags.group_name, tags.sub_group_name, tags.tag_value;
          --  DELETE FROM staging_tag_groups WHERE staging_ref = line.id;
          EXCEPTION WHEN OTHERS THEN
            RAISE NOTICE 'Error in fct_imp_checker_gtu (case non existing gtu): %', SQLERRM;
            -- nothing
        END ;
      END LOOP ;
    ELSE
      /* Define gtu_ref of the line object, so it can be used afterwards in the perform to bring correctly
         the additional comments and additional properties
      */
      line.gtu_ref = ref_rec;
      /* ELSE ADDED HERE TO CHECK IF THE TAGS (and the staging infos) OF THE EXISTING GTU EXISTS TOO */
      /* This case happens when a gtu that correspond to info entered in staging has been found */
      /* Browse all tags to try importing them one by one and associate them with the newly created gtu */
      FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        /* We split all the tags entered by ; as it's the case in the interface */
        FOR tags_tag IN SELECT trim(regexp_split_to_table(tags.tag_value, E';+')) as value LOOP
          BEGIN
            /* We use an upsert here.
               Ideally, we should use locking, but we consider it's isolated.
             */
            UPDATE tag_groups 
            SET tag_value = tag_value || ';' || tags.tag_value 
            WHERE gtu_ref = ref_rec
              AND group_name_indexed = fullToIndex(tags.group_name)
              AND sub_group_name_indexed = fullToIndex(tags.sub_group_name)
              AND fullToIndex(tags_tag.value) NOT IN (SELECT fullToIndex(regexp_split_to_table(tag_value, E';+')));
            GET DIAGNOSTICS update_count = ROW_COUNT;
            IF update_count = 0 THEN
              INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
              SELECT ref_rec,tags.group_name, tags.sub_group_name, tags_tag.value
              WHERE NOT EXISTS (SELECT id 
                                FROM tag_groups 
                                WHERE gtu_ref = ref_rec
                                  AND group_name_indexed = fullToIndex(tags.group_name)
                                  AND sub_group_name_indexed = fullToIndex(tags.sub_group_name)
                                LIMIT 1
                               );
            END IF;
          --  DELETE FROM staging_tag_groups WHERE staging_ref = line.id;
            EXCEPTION WHEN OTHERS THEN
              RAISE NOTICE 'Error in fct_imp_checker_gtu (case from existing gtu): %', SQLERRM;
              RAISE NOTICE 'gtu_ref is %', ref_rec;
              RAISE NOTICE 'group name is %', tags.group_name;
              RAISE NOTICE 'subgroup name is %', tags.sub_group_name;
              RAISE NOTICE 'tag value is %', tags_tag.value;
              -- nothing
          END ;
        END LOOP;
      END LOOP ;
    END IF;
    /* Execute (perform = execute without any output) the update of reference_relation 
       for the current staging line and for the gtu type of relationship.
       Referenced relation currently named 'staging_info' is replaced by gtu
       and record_id currently set to line.id (staging id) is replaced by line.gtu_ref (id of the new gtu created)
    */
    PERFORM fct_imp_checker_staging_info(line, 'gtu');

    /* Associate the gtu_ref in the staging and erase in hstore status the gtu tag signaling gtu has still to be treated */
    UPDATE staging SET status = delete(status,'gtu'), gtu_ref = ref_rec where id=line.id;

  END IF;

  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_expeditions(line staging, import boolean DEFAULT false)
  RETURNS boolean AS
$$
DECLARE
  result_nbr integer :=0;
  ref_record RECORD;
  ref_rec integer :=0;
  ref refcursor;
BEGIN
  IF line.expedition_name is null OR line.expedition_name ='' OR line.expedition_ref is not null THEN
    RETURN true;
  END IF;
  OPEN ref FOR select * from expeditions where name_indexed = fulltoindex(line.expedition_name) ; 
  LOOP
    FETCH ref INTO ref_record ;    
    IF NOT FOUND THEN
  EXIT ;
    END IF;
    ref_rec = ref_record.id ;
    result_nbr := result_nbr +1;
  END LOOP ;
  IF result_nbr = 0 THEN
    IF import THEN
      INSERT INTO expeditions (name, expedition_from_date, expedition_to_date, expedition_from_date_mask,expedition_to_date_mask)
      VALUES (
        line.expedition_name, COALESCE(line.expedition_from_date,'01/01/0001'),
        COALESCE(line.expedition_to_date,'31/12/2038'), COALESCE(line.expedition_from_date_mask,0),
        COALESCE(line.expedition_to_date_mask,0)
      )
      RETURNING id INTO line.expedition_ref;

      ref_rec := line.expedition_ref;
      PERFORM fct_imp_checker_staging_info(line, 'expeditions');
    ELSE
      RETURN TRUE;
    END IF;
  END IF;
  IF result_nbr >= 2 THEN
    UPDATE staging SET status = (status || ('expedition' => 'too_much')) where id= line.id;
    RETURN true;
  END IF; 
  /* So result_nbr = 1 ! */
  UPDATE staging SET status = delete(status,'expedition'), expedition_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$
  LANGUAGE plpgsql ;

commit ;