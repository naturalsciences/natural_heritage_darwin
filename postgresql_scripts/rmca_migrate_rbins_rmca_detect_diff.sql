-- Function: public.rmca_migrate_rbins_rmca_detect_diff()

-- DROP FUNCTION public.rmca_migrate_rbins_rmca_detect_diff();

CREATE OR REPLACE FUNCTION public.rmca_migrate_rbins_rmca_detect_diff()
  RETURNS integer AS
$BODY$
    Declare returned int;
    declare same_tables varchar[];
    declare i integer;
    declare size_array_same int;
    declare fields_to_copy varchar[]; 
    declare fields_to_create varchar[]; 
     text_var1 text;
  text_var2 text;
  text_var3 text;
    BEGIN
      returned:=1;
      same_tables:=
'{"people",
"catalogue_relationships",
"catalogue_people",
"catalogue_levels",
"possible_upper_levels",
"tag_groups",
"tags",
"properties",
"identifications",
"vernacular_names",
"expeditions",
"collections_rights",
"users_tracking",
"informative_workflow",
"classification_keywords",
"chronostratigraphy",
"lithostratigraphy",
"mineralogy",
"lithology",
"igs",
"collecting_methods",
"codes",
"insurances",
"specimens_relationships",
"specimen_collecting_methods",
"specimen_collecting_tools",
"flat_dict",
"staging_info",
"staging_tag_groups",
"staging_people",
"staging_collecting_methods",
"loan_actors",
"multimedia",
"loan_items",
"loan_rights",
"loan_status",
"loan_history",
"bibliography",
"catalogue_bibliography",
"multimedia_todelete"}';

size_array_same:=array_length(same_tables,1);

FOR i in 1..size_array_same LOOP
	--RAISE NOTICE '%', same_tables[i];
	SELECT array_agg(column_name::varchar order by ordinal_position) into fields_to_copy
		FROM information_schema.columns
		WHERE table_schema = 'darwin2_rbins_data'
		AND table_name   = same_tables[i]		
		;
		--RAISE NOTICE '%', fields_to_copy;
		if fields_to_copy is not null then
		BEGIN
		
		EXECUTE 'ALTER TABLE darwin2.'||same_tables[i]||' DISABLE TRIGGER ALL;
		DELETE FROM darwin2.'||same_tables[i]||';
		INSERT INTO darwin2.'||same_tables[i]||' ('||array_to_string(fields_to_copy,',')||') SELECT '||array_to_string(fields_to_copy,',')||' FROM darwin2_rbins_data.'||same_tables[i]||' LIMIT 1;
		DELETE FROM darwin2.'||same_tables[i]||';
		ALTER TABLE darwin2.'||same_tables[i]||' ENABLE TRIGGER ALL;' ;
		EXCEPTION WHEN OTHERS then
			RAISE NOTICE 'DIFF_IN_STRUCT_FOR : %',same_tables[i];
			SELECT array_agg(column_name::varchar order by ordinal_position) into fields_to_create
			FROM information_schema.columns
			WHERE table_schema = 'darwin2_rbins_data'
			AND table_name   = same_tables[i]
			AND column_name NOT IN (SELECT column_name FROM information_schema.columns
				WHERE table_schema = 'darwin2'
				AND table_name   = same_tables[i])	
			;
			  GET STACKED DIAGNOSTICS text_var1 = MESSAGE_TEXT,
                          text_var2 = PG_EXCEPTION_DETAIL,
                          text_var3 = PG_EXCEPTION_HINT;
                          RAISE NOTICE '%',text_var1;
			RAISE NOTICE 'COLUMNS TO CREATE = %',fields_to_create;
		END;
		--exit when i=5;
		end if;
		
		
END LOOP;




      
      return returned;
    END;
    $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.rmca_migrate_rbins_rmca_detect_diff()
  OWNER TO postgres;
