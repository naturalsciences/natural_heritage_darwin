SET search_path TO darwin2,"$user",public;

begin;

create table if not exists zzz_taxa_imported_not_cleaned (taxon_ref integer, reason varchar);

create or replace function clean_imports(IN imports.id%TYPE) returns boolean language plpgsql AS
$$
declare
  recStaging RECORD;
  intCount integer := 0;
begin
  FOR recStaging IN SELECT DISTINCT taxon_ref, taxon_level_name FROM staging where import_ref = 61 and taxon_level_ref is not null order by taxon_level_name desc LOOP
    SELECT COUNT(*) INTO intCount 
    FROM taxonomy INNER JOIN specimens ON taxonomy.id = specimens.taxon_ref
    WHERE taxonomy.id = recStaging.taxon_ref::integer;
    IF intCount = 0 THEN
      SELECT COUNT(*) INTO intCount FROM taxonomy INNER JOIN specimens ON taxonomy.id = specimens.taxon_parent_ref WHERE specimens.taxon_parent_ref = recStaging.taxon_ref::integer;
      IF intCount = 0 THEN
        begin
          DELETE FROM taxonomy WHERE id = recStaging.taxon_ref::integer;
        exception when foreign_key_violation then
          INSERT INTO zzz_taxa_imported_not_cleaned VALUES (recStaging.taxon_ref::integer, 'Other taxa depends on this taxon.');
        end;
      ELSE
        INSERT INTO zzz_taxa_imported_not_cleaned VALUES (recStaging.taxon_ref::integer, 'Taxon linked as taxon_parent_ref to ' || intCount::varchar || ' specimens');
      END IF;
    ELSE
      INSERT INTO zzz_taxa_imported_not_cleaned VALUES (recStaging.taxon_ref::integer, 'Taxon linked as taxon_ref to ' || intCount::varchar || ' specimens');
    END IF;
    intCount:=0;
  END LOOP;
  return true;
exception
  when others then
    RAISE WARNING 'An error occured: %', SQLERRM;
    return false;
end;
$$;

SELECT clean_imports(61);

commit;
