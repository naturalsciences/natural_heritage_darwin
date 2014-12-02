begin;
set search_path=darwin2,public;

alter table imports drop constraint if exists fk_imports_collections ;

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
        'manualy' ;
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

commit;
