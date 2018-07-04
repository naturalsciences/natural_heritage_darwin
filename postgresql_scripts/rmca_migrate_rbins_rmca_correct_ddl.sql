-- Function: public.rmca_migrate_rbins_rmca_correct()

-- DROP FUNCTION public.rmca_migrate_rbins_rmca_correct();

CREATE OR REPLACE FUNCTION public.rmca_migrate_rbins_rmca_correct()
  RETURNS integer AS
$BODY$
    Declare returned int;
    declare same_tables varchar[];
    declare i integer;
    declare size_array_same int;
    declare fields_to_copy varchar[]; 
     text_var1 text;
  text_var2 text;
  text_var3 text;

  metadata_tmp integer;
    BEGIN
    --attention, the index esof specimens in the rbins are bound to some functions
    SET search_path TO darwin2;
      returned:=1;
     -- same_tables:='{"gtu"}';
     same_tables:='{"collections"}';




size_array_same:=array_length(same_tables,1);
raise notice 'begin %', timeofday()::varchar;





FOR i in 1..size_array_same LOOP
	RAISE NOTICE '%', same_tables[i];
	SELECT array_agg(column_name::varchar order by ordinal_position) into fields_to_copy
		FROM information_schema.columns
		WHERE table_schema = 'darwin2_rbins_data'
		AND table_name   = same_tables[i]		
		;
		RAISE NOTICE '%', fields_to_copy;
		if fields_to_copy is not null then
		BEGIN

                 

		 EXECUTE 'INSERT INTO public.count_migration VALUES (''darwin2_rbins_data.'||same_tables[i]||''',''before_copy'',(SELECT COUNT (*) FROM darwin2_rbins_data.'||same_tables[i]||'));';


		 RAISE NOTICE 'ALTER TABLE darwin2.% DISABLE TRIGGER ALL;
		INSERT INTO darwin2.% (%) SELECT % FROM darwin2_rbins_data.%;
		ALTER TABLE darwin2.% ENABLE TRIGGER ALL;', same_tables[i], same_tables[i], array_to_string(fields_to_copy,','), array_to_string(fields_to_copy,','), same_tables[i], same_tables[i];

		EXECUTE 'ALTER TABLE darwin2.'||same_tables[i]||' DISABLE TRIGGER ALL;
		
		INSERT INTO darwin2.'||same_tables[i]||' ('||array_to_string(fields_to_copy,',')||') SELECT '||array_to_string(fields_to_copy,',')||' FROM darwin2_rbins_data.'||same_tables[i]||';
		ALTER TABLE darwin2.'||same_tables[i]||' ENABLE TRIGGER ALL;' ;

				EXECUTE 'INSERT INTO public.count_migration VALUES (
''darwin2.'||same_tables[i]||''' ,
''after_copy'',
(SELECT COUNT (*) FROM darwin2.'||same_tables[i]||'));';
		
		EXCEPTION WHEN OTHERS then
		 GET STACKED DIAGNOSTICS text_var1 = MESSAGE_TEXT,
                          text_var2 = PG_EXCEPTION_DETAIL,
                          text_var3 = PG_EXCEPTION_HINT;
                          RAISE NOTICE '%',text_var1;
			RAISE NOTICE 'DIFF_IN_FIELD_FOR : %',same_tables[i];
		END;
		
		end if;
		
		
END LOOP;
BEGIN

 

EXCEPTION WHEN OTHERS then
		 GET STACKED DIAGNOSTICS text_var1 = MESSAGE_TEXT,
                          text_var2 = PG_EXCEPTION_DETAIL,
                          text_var3 = PG_EXCEPTION_HINT;
                          RAISE NOTICE '%',text_var1;
			RAISE NOTICE 'DIFF_IN_FIELD_FOR : %',same_tables[i];
		END;
      return returned;


      
    END;
    $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.rmca_migrate_rbins_rmca_correct()
  OWNER TO postgres;
