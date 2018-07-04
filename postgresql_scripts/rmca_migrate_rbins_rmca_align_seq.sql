-- Function: public.rmca_migrate_rbins_rmca_align_seq()

-- DROP FUNCTION public.rmca_migrate_rbins_rmca_align_seq();

CREATE OR REPLACE FUNCTION public.rmca_migrate_rbins_rmca_align_seq()
  RETURNS integer AS
$BODY$
    Declare returned int;
    rec_seq record;
 
   cur_seq  CURSOR FOR SELECT REPLACE(REPLACE(column_default,'nextval(''',''),'''::regclass)','') as seq_name, table_name, column_name from information_schema.columns where column_default like 'nextval%' and table_schema='darwin2' order by table_name;



  metadata_tmp integer;
    BEGIN
       set search_path='darwin2';
    returned:=-1;
    --attention, the index esof specimens in the rbins are bound to some functions
    SET search_path TO darwin2;
      OPEN cur_seq;
 
   LOOP
    -- fetch row into the film
      FETCH cur_seq INTO rec_seq;
    -- exit when no more row to fetch
      EXIT WHEN NOT FOUND;
	RAISE notice E'seq: % \t table:% \ t count', rec_seq.seq_name,rec_seq.table_name ;
       EXECUTE 'SELECT setval(''darwin2.'||rec_seq.seq_name||''', (SELECT MAX('||rec_seq.column_name||')+1 FROM darwin2.'||rec_seq.table_name||') , false);';
 
   END LOOP;
	returned:=0;
      return returned;


      
    END;
    $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.rmca_migrate_rbins_rmca_align_seq()
  OWNER TO postgres;
