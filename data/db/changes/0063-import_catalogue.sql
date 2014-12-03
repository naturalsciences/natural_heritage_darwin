begin;
set search_path=darwin2,public;

CREATE INDEX idx_staging_gtu_code ON staging (gtu_code) WHERE gtu_code IS NOT NULL;
CREATE INDEX idx_staging_gtu_code_fullToIndex ON staging (fullToIndex(gtu_code)) WHERE gtu_code IS NOT NULL;
CREATE INDEX idx_gtu_code_search_for_import ON gtu (position('import/' in code), COALESCE(latitude,0), COALESCE(longitude,0), COALESCE(fullToIndex(code), ''));

alter table imports drop constraint if exists fk_imports_collections ;
alter table imports alter column  updated_at set default now() ;

create table staging_catalogue
       (
        id serial,
        import_ref integer not null,
        name varchar not null,
        level_ref integer,
        parent_ref integer,
        catalogue_ref integer,
        constraint pk_staging_catalogue primary key (id),
        constraint fk_stg_catalogue_level_ref foreign key (level_ref) references catalogue_levels(id),
        constraint fk_stg_catalogue_import_ref foreign key (import_ref) references imports(id) on delete cascade,
        constraint fk_stg_catalogue_parent_ref foreign key (parent_ref) references staging_catalogue(id) on delete cascade
       );

CREATE TRIGGER trg_update_import AFTER UPDATE ON imports FOR EACH ROW EXECUTE PROCEDURE fct_update_import();
CREATE OR REPLACE function fct_update_import() RETURNS trigger AS $$
BEGIN
  if OLD.state IS DISTINCT FROM NEW.state THEN
  UPDATE imports set updated_at= now() where id=NEW.id ;
  END IF ;
  return new ;
END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION fct_importer_catalogue(req_import_ref integer,referenced_relation text)  RETURNS boolean
AS $$
DECLARE
  parent_id integer := null;
  catalogue_id integer := null;
  all_line staging_catalogue ;
  result_nbr integer;
  error_msg text;
BEGIN    
  FOR all_line IN SELECT * from staging_catalogue WHERE import_ref = req_import_ref ORDER BY id 
  LOOP     
    if all_line.parent_ref IS Null THEN  -- so this is the first catalogue, we have to attach it to an existant
      EXECUTE 'select count(*),id from '|| quote_ident(referenced_relation)||' where level_ref = $1 AND name_indexed like fullToIndex( $2 ) GROUP BY id ;'
        into result_nbr,catalogue_id
        USING all_line.level_ref, all_line.name ;
      IF result_nbr IS NULL THEN
        error_msg := 'Could not import this file, ' || all_line.name ||
        ' does not exist in DaRWIN and cannot be attached, correct your file or create this ' || quote_ident(referenced_relation) ||
        ' manually' ;
        EXECUTE 'Update imports set errors_in_import = $1,
          state=''error''
          WHERE id=$2'
        USING error_msg, req_import_ref ;
        RETURN true ;
      END IF ;
    ELSE -- else the direct parent is in the file, so we take the parent catalogue_ref from there
      EXECUTE 'select count(*),id from '|| quote_ident(referenced_relation)||' where level_ref = $1 AND name_indexed like fullToIndex( $2 ) 
        AND parent_ref IS NOT DISTINCT FROM $3 GROUP BY id ;'
        into result_nbr,catalogue_id
        USING all_line.level_ref, all_line.name, parent_id ;
    END IF ;
    IF result_nbr > 1 THEN
      error_msg := 'Could not import this file, ' || all_line.name || ' exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree';
      EXECUTE 'Update imports set errors_in_import = $1,
        state=''error''
        WHERE id=$2'
      USING error_msg, req_import_ref ;
      RETURN true ;
    END IF ;
    IF result_nbr IS NULL THEN -- target not found, let's create it
      EXECUTE 'INSERT INTO ' || quote_ident(referenced_relation) || '(id,name,level_ref,parent_ref) VALUES(DEFAULT,$1,$2,$3) returning id;'
        into catalogue_id 
        using all_line.name,all_line.level_ref,parent_id;
    END IF; 
    -- update the staging line to put the new or existing catalogue_ref
    update staging_catalogue set catalogue_ref=catalogue_id Where id = all_line.id ;
    parent_id := catalogue_id ;
  END LOOP;  
    EXECUTE 'update imports set state=''finished'',is_finished=true where id=$1'
    USING req_import_ref ;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_gtu(line staging, import boolean default false)  RETURNS boolean
AS $$
DECLARE
  ref_rec integer :=0;
  tags staging_tag_groups ;
BEGIN
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
      gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
      gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038')
    /* if we're not in the case of already imported gtu without code,
       we've got to find a gtu that correspond to the criterias of the current line
    */
    union
    select id from gtu g where
      position('import/' in code) = 0 AND
      COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
      COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
      COALESCE(fullToIndex(code),'') = COALESCE(fullToIndex(line.gtu_code),'') AND
      gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
      gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038')
      LIMIT 1
    ) as substr
    WHERE substr.id != 0 LIMIT 1;

  /* If no corresponding gtu found and we've chosen to import... insert the new gtu */
  IF NOT FOUND THEN
      IF import THEN
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
            Values(ref_rec,tags.group_name, tags.sub_group_name, tags.tag_value );
        --  DELETE FROM staging_tag_groups WHERE staging_ref = line.id;
          EXCEPTION WHEN unique_violation THEN
            -- nothing
        END ;
        END LOOP ;
        /* Execute (perform = execute without any output) the update of reference_relation 
           for the current staging line and for the gtu type of relationship.
           Referenced relation currently named 'staging_info' is replaced by gtu
           and record_id currently set to line.id (staging id) is replaced by line.gtu_ref (id of the new gtu created)
        */
        PERFORM fct_imp_checker_staging_info(line, 'gtu');
      ELSE
        RETURN TRUE;
      END IF;
  ELSE
  /* ELSE ADDED HERE TO CHECK IF THE TAGS (and the staging infos) OF THE EXISTING GTU EXISTS TOO */
  /* This case happens when a gtu that correspond to info entered in staging has been found */
    IF import THEN
      FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
      BEGIN
        INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
          Values(ref_rec,tags.group_name, tags.sub_group_name, tags.tag_value );
        EXCEPTION WHEN unique_violation THEN
          -- nothing
      END ;
      END LOOP ;
      /* Execute (perform = execute without any output) the update of reference_relation 
         for the current staging line and for the gtu type of relationship.
         Referenced relation currently named 'staging_info' is replaced by gtu
         and record_id currently set to line.id (staging id) is replaced by line.gtu_ref (id of the new gtu created)
      */
      PERFORM fct_imp_checker_staging_info(line, 'gtu');
    ELSE
      RETURN TRUE;      
    END IF;
  END IF;

  /* Associate the gtu_ref in the staging and erase in hstore status the gtu tag signaling gtu has still to be treated */
  UPDATE staging SET status = delete(status,'gtu'), gtu_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$ LANGUAGE plpgsql;

commit;
