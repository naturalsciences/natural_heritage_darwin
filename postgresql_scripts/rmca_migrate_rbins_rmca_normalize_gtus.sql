-- Function: public.rmca_migrate_rbins_rmca_normalize_gtus()

-- DROP FUNCTION public.rmca_migrate_rbins_rmca_normalize_gtus();

CREATE OR REPLACE FUNCTION public.rmca_migrate_rbins_rmca_normalize_gtus()
  RETURNS integer AS
$BODY$
    Declare returned int;
    text_var1 varchar;
    text_var2 varchar;
    text_var3 varchar;
BEGIN
 SET search_path TO darwin2;


BEGIN 
returned:=-1;

RAISE NOTICE 'backups %', timeofday()::varchar;
create table darwin2.gtu_bck2018 as SELECT * FROM darwin2_rbins_data.gtu;
create table  darwin2.specimens_bck2018 as select * from  darwin2_rbins_data.specimens;
  INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, is_available, title_perso)
SELECT id, 'specimen_widget', 'gtuDate', 0,2, true, 'Gathering date' FROM users where id NOT in (SELECT user_ref FROM my_widgets WHERE category='specimen_widget'  AND group_name ='Gathering date');


RAISE NOTICE 'widgets %', timeofday()::varchar;
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, is_available, title_perso)
SELECT id, 'specimensearch_widget', 'gtuDate', 0,2, true, 'Gathering date' FROM users where id NOT in (SELECT user_ref FROM my_widgets WHERE category='specimensearch_widget'  AND group_name ='Gathering date');

RAISE NOTICE 'nullify specimens %', timeofday()::varchar;
UPDATE specimens SET gtu_from_date_mask=0 where gtu_from_date_mask is null;

ALTER TABLE specimens ALTER COLUMN gtu_from_date_mask SET NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_from_date_mask SET DEFAULT 0;
COMMENT ON COLUMN specimens.gtu_from_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';

UPDATE specimens SET gtu_from_date='0001-01-01 00:00:00'::timestamp without time zone where gtu_from_date is null;
ALTER TABLE specimens ALTER COLUMN gtu_from_date SET NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_from_date SET DEFAULT '0001-01-01 00:00:00'::timestamp without time zone;
COMMENT ON COLUMN specimens.gtu_from_date IS 'composed from date of the GTU';

UPDATE specimens SET gtu_to_date_mask=0 where gtu_to_date_mask is null;
ALTER TABLE specimens ALTER COLUMN gtu_to_date_mask SET NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_to_date_mask SET DEFAULT 0;
COMMENT ON COLUMN specimens.gtu_to_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';

UPDATE specimens SET gtu_to_date='2038-12-31 00:00:00'::timestamp without time zone where gtu_to_date is null;
ALTER TABLE specimens ALTER COLUMN gtu_to_date SET NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_to_date SET DEFAULT '2038-12-31 00:00:00'::timestamp without time zone;
COMMENT ON COLUMN specimens.gtu_to_date IS 'composed to date of the GTU';


ALTER TABLE specimens ALTER COLUMN gtu_to_date SET NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_to_date SET DEFAULT '2038-12-31 00:00:00'::timestamp without time zone;
COMMENT ON COLUMN specimens.gtu_to_date IS 'composed to date of the GTU';

RAISE NOTICE 'allow nulls %', timeofday()::varchar;


ALTER TABLE gtu ALTER COLUMN gtu_from_date_mask DROP NOT NULL;
ALTER TABLE gtu ALTER COLUMN gtu_from_date DROP NOT NULL;
ALTER TABLE gtu ALTER COLUMN gtu_to_date_mask DROP NOT NULL;
ALTER TABLE gtu ALTER COLUMN gtu_to_date DROP NOT NULL;

ALTER TABLE specimens ALTER COLUMN gtu_from_date_mask DROP NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_from_date DROP NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_to_date_mask DROP NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_to_date DROP NOT NULL;



RAISE NOTICE 'UNSERT NORMALIZED GTUS %', timeofday()::varchar;
ALTER TABLE specimens disable trigger ALL;

UPDATe specimens set gtu_ref=(
SELECT ref_id FROM
(
SELECt distinct array_id[1] as ref_id,unnest(array_id) as replacement FROM(SELECT count(*) as cpt, array_sort(array_agg(id order by id)) as array_id, code, tag_values_indexed, latitude, longitude, lat_long_accuracy, 
       location::varchar, elevation, elevation_accuracy, latitude_dms_degree, 
       latitude_dms_minutes, latitude_dms_seconds, latitude_dms_direction, 
       longitude_dms_degree, longitude_dms_minutes, longitude_dms_seconds, 
       longitude_dms_direction, latitude_utm, longitude_utm, utm_zone, 
       coordinates_source, elevation_unit 
  FROM gtu

  GROUP BY  code, tag_values_indexed, latitude, longitude, lat_long_accuracy, 
       location::varchar, elevation, elevation_accuracy, latitude_dms_degree, 
       latitude_dms_minutes, latitude_dms_seconds, latitude_dms_direction, 
       longitude_dms_degree, longitude_dms_minutes, longitude_dms_seconds, 
       longitude_dms_direction, latitude_utm, longitude_utm, utm_zone, 
       coordinates_source, elevation_unit  
) as a )
as b WHERE gtu_ref=replacement
) 
WHERE gtu_ref is not null;

ALTER TABLE specimens enable trigger ALL;


RAISE NOTICE 'NULLIFY DATE IN GTU %', timeofday()::varchar;
ALTER table gtu disable trigger user;
--SELECT set_config('darwin.userid', '1', true);
UPDATE gtu SET  gtu_from_date_mask=null, gtu_from_date=null, gtu_to_date_mask=null,
 gtu_to_date=null;

 ALTER table gtu enable trigger user;
 
-- check /***************/
/*SELECT  distinct a.gtu_code, a.gtu_ref, c.tag_values_indexed , b.gtu_ref,  d.tag_values_indexed , a.gtu_from_date_mask, 
       a.gtu_from_date, a.gtu_to_date_mask, a.gtu_to_date, a.gtu_tag_values_indexed, a.gtu_location::text
  FROM specimens_bck2018 a
inner join specimens b
on a.id=b.id
LEFT JOIN gtu_bck2018 c
ON c.id=a.gtu_ref
LEFT JOIN gtu d
ON d.id=b.gtu_ref

   where a.gtu_ref not in (SELECT distinct gtu_ref from specimens b where a.id=b.id)
  order by gtu_code;
  */
  ---------------------
  


---------------

RAISE NOTICE 'RESTORE NOT NULL %', timeofday()::varchar;

ALTER TABLE gtu ALTER COLUMN gtu_from_date_mask DROP NOT NULL;
ALTER TABLE gtu ALTER COLUMN gtu_from_date DROP NOT NULL;
ALTER TABLE gtu ALTER COLUMN gtu_to_date_mask DROP NOT NULL;
ALTER TABLE gtu ALTER COLUMN gtu_to_date DROP NOT NULL;

ALTER TABLE specimens ALTER COLUMN gtu_from_date_mask DROP NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_from_date DROP NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_to_date_mask DROP NOT NULL;
ALTER TABLE specimens ALTER COLUMN gtu_to_date DROP NOT NULL;


RAISE NOTICE 'delete remaining duplicates gtu (when date null) %', timeofday()::varchar;
ALTER TABLE gtu disable trigger all;

DELETE FROM darwin2.gtu where

id not in (
SELECT unnest(ids[2:1000]) FROM (

(SELECT array_agg(id order by id) as ids
  FROM darwin2_rbins_data.gtu
  GROUP BY code, tag_values_indexed, latitude, longitude, lat_long_accuracy, 
       elevation, elevation_accuracy, location::varchar
       ORDER BY count(*) desc
) )a) and id  NOT in (SELECT gtu_Ref from darwin2.specimens);


ALTER TABLE gtu enable trigger all;

ALTEr table tags disable trigger all;

DELETE FROM tags where gtu_ref not in (select id from gtu);

ALTEr table tags enable trigger all;

ALTEr table tag_groups disable trigger all;

DELETE FROM tag_groups where gtu_ref not in (select id from gtu);

ALTEr table tag_groups enable trigger all;

RAISE NOTICE 'end of function %', timeofday()::varchar;
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
ALTER FUNCTION public.rmca_migrate_rbins_rmca_normalize_gtus()
  OWNER TO postgres;
