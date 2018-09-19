-- Function: fct_rmca_taxonomy_try_to_isolate_from_author(character varying)

-- DROP FUNCTION fct_rmca_taxonomy_try_to_isolate_from_author(character varying);

CREATE OR REPLACE FUNCTION fct_rmca_taxonomy_try_to_isolate_from_author(character varying)
  RETURNS character varying AS
$BODY$ SELECT 
regexp_replace(
trim(

regexp_replace($1,'(\(|\s[A-Z]).*$','')),

E' (von|van|de|l\')$',''
)

; $BODY$
  LANGUAGE sql VOLATILE
  COST 100;
ALTER FUNCTION fct_rmca_taxonomy_try_to_isolate_from_author(character varying)
  OWNER TO darwin2;

  
-- Function: fct_rmca_taxonomy_remove_last_word(character varying)

-- DROP FUNCTION fct_rmca_taxonomy_remove_last_word(character varying);

CREATE OR REPLACE FUNCTION fct_rmca_taxonomy_remove_last_word(character varying)
  RETURNS character varying AS
$BODY$ SELECT trim(
     
    regexp_replace(fct_rmca_taxonomy_try_to_isolate_from_author(fct_rmca_taxonomy_try_to_isolate_from_author($1)), '( [^ ]+$)', '')

    ); $BODY$
  LANGUAGE sql VOLATILE
  COST 100;
ALTER FUNCTION fct_rmca_taxonomy_remove_last_word(character varying)
  OWNER TO darwin2;
  
-- Function: rmca_taxonomy_create_missing_species_subspecies(character varying, integer)

-- DROP FUNCTION rmca_taxonomy_create_missing_species_subspecies(character varying, integer);

CREATE OR REPLACE FUNCTION rmca_taxonomy_create_missing_species_subspecies(taxon_name_param character varying, taxonomy_to_search integer DEFAULT NULL::integer)
  RETURNS integer AS
$BODY$
declare
 parent_rank integer[];
 parents integer[];
 returned integer;
 level_to_create int;
 parent_to_create int;
 
 
begin
	returned:=-1;
	IF taxonomy_to_search IS NOT NULL THEN
		SELECT  array_agg(distinct taxonomy.id order by taxonomy.id)  , array_agg(distinct level_ref)
		INTO parents, parent_rank FROM staging
		LEFT OUTER JOIN
		taxonomy
		ON fulltoindex(fct_rmca_taxonomy_remove_last_word(taxon_name_param), false
		)=taxonomy.name_indexed
		WHERE metadata_ref=  taxonomy_to_search
		GROUP by staging.taxon_name LIMIT 1;
	ELSE
	 SELECT  array_agg(distinct taxonomy.id order by taxonomy.id), array_agg(distinct level_ref), (array_agg(metadata_ref))[1]
		INTO parents, parent_rank, taxonomy_to_search
		FROM staging
		LEFT OUTER JOIN
		taxonomy
		ON fulltoindex(fct_rmca_taxonomy_remove_last_word(taxon_name_param), false
		)=taxonomy.name_indexed		
		GROUP by staging.taxon_name LIMIT 1;
        taxonomy_to_search:=0;
	
	END IF;
   --work only for subspecies-species or species->genus
	IF ARRAY_LENGTH(parents,1)=1 AND (parent_rank[1] = 41 OR parent_rank[1] = 48) THEN
		parent_to_create :=parents[1];

		IF parent_rank[1] = 41 THEN
			level_to_create :=48;
		ELSIF  parent_rank[1] = 48 THEN
			level_to_create :=49;
		END IF;
		--raise notice  'create % %',taxon_name_param, parent_to_create;
        
		INSERT INTO taxonomy (name, level_ref, parent_ref, metadata_ref) VALUES(taxon_name_param, level_to_create,
			parent_to_create, taxonomy_to_search) returning id into returned;
			return returned;
		
		
		
	END IF;
	
	
return returned;
	EXCEPTION 
	WHEN  others THEN
		RAISE NOTICE 'Error inserting %', taxon_name_param;
	return -1;
END

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION rmca_taxonomy_create_missing_species_subspecies(character varying, integer)
  OWNER TO darwin2;
  
-- Function: rmca_taxonomy_create_missing_species_subspecies_loop(integer)

-- DROP FUNCTION rmca_taxonomy_create_missing_species_subspecies_loop(integer);

CREATE OR REPLACE FUNCTION rmca_taxonomy_create_missing_species_subspecies_loop(import_id integer)
  RETURNS boolean AS
$BODY$
declare
	returned boolean;
	curs_staging CURSOR FOR select * FROM  staging WHERE  import_ref=import_id AND staging.status->'taxon'='not_found';
	rec_staging RECORD;
	taxonomy_ref integer;
	taxon_id integer;
        level_ref_imp integer;
        level_name_imp varchar;

	
begin
	returned:=false;
	SELECT specimen_taxonomy_ref INTO taxonomy_ref FROM imports WHERE id=import_id;
	OPEN curs_staging;
		LOOP
			FETCH curs_staging INTO rec_staging;
			
			
			EXIT WHEN NOT FOUND;
			
	
			
			SELECT rmca_taxonomy_create_missing_species_subspecies(rec_staging.taxon_name, taxonomy_ref) into taxon_id;
			IF taxon_id <> -1 THEN

				SELECT level_ref, level_name INTO level_ref_imp, level_name_imp FROM taxonomy INNER JOIN 				catalogue_levels ON
				level_ref=catalogue_levels.id WHERE taxonomy.id=taxon_id LIMIT 1;
				
				UPDATE staging set taxon_ref=taxon_id, taxon_name = rec_staging.taxon_name, taxon_level_ref=
				level_ref_imp,
				taxon_level_name=level_name_imp,
				status = delete(status,'taxon')
				WHERE
				taxon_name  IS NOT DISTINCT FROM  rec_staging.taxon_name
				AND status->'taxon'='not_found' 
				AND import_ref =  import_id;
        
			END IF;
			returned=true;
			
		END LOOP;
	CLOSE curs_staging;
	IF returned=true THEN
		SELECT fct_importer_abcd(import_id) into returned;
	END IF;
	return returned;
end
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION rmca_taxonomy_create_missing_species_subspecies_loop(integer)
  OWNER TO darwin2;
  
  
  