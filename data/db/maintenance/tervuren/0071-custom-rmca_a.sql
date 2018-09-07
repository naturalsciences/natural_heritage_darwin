 DROP FUNCTION rmca_delete_specimens_from_collection(integer);

CREATE OR REPLACE FUNCTION rmca_delete_specimens_from_collection(p_coll_ref integer)
  RETURNS void AS
$BODY$
BEGIN 


ALTER table properties disable trigger user; 
ALTER table comments disable trigger user; 
RAISE NOTICE 'before delete properties %', (SELECT count(*) FROM properties);
DELETE FROM properties WHERE record_id IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref) AND referenced_relation= 'specimens';
RAISE NOTICE 'after delete properties (specimens) %', (SELECT count(*) FROM properties);


RAISE NOTICE 'before delete comments %', (SELECT count(*) FROM comments);
DELETE FROM comments WHERE record_id IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref) AND referenced_relation='specimens';
RAISE NOTICE 'afet delete comments (specimens) %', (SELECT count(*) FROM comments);


RAISE NOTICE 'before delete properties %', (SELECT count(*) FROM properties);
DELETE FROM properties WHERE record_id IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) AND referenced_relation= 'staging';
RAISE NOTICE 'after delete properties (staging) %', (SELECT count(*) FROM properties);

RAISE NOTICE 'before delete comments %', (SELECT count(*) FROM comments);
DELETE FROM comments WHERE record_id IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) AND referenced_relation= 'staging';
RAISE NOTICE 'after delete comments (staging) %', (SELECT count(*) FROM comments);

RAISE NOTICE 'before delete properties %', (SELECT count(*) FROM comments);
DELETE
  FROM properties where referenced_relation ='staging_info' and record_id in (SELECT id FROM staging_info WHERE staging_ref IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) )  AND referenced_relation= 'staging_info';
RAISE NOTICE 'after delete properties (staging_info) %', (SELECT count(*) FROM properties);

RAISE NOTICE 'before delete comments %', (SELECT count(*) FROM comments);
DELETE
  FROM comments where referenced_relation ='staging_info' and record_id in (SELECT id FROM staging_info WHERE staging_ref IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) )  AND referenced_relation= 'staging_info';
RAISE NOTICE 'after  delete comments (staging_info) %', (SELECT count(*) FROM comments);

ALTER table properties enable trigger user; 
ALTER table comments enable trigger user; 

RAISE NOTICE 'before delete tags %', (SELECT count(*) FROM tags);
DELETE FROM tags WHERE gtu_ref IN (SELECT id FROM gtu WHERE id in (SELECT gtu_ref FROM specimens WHERE collection_ref=p_coll_ref) );
RAISE NOTICE 'after delete tags %', (SELECT count(*) FROM tags);
/*
RAISE NOTICE 'before delete tag_groups %', (SELECT count(*) FROM tag_groups);
DELETE FROM tag_groups WHERE gtu_ref IN (SELECT id FROM gtu WHERE id in (SELECT gtu_ref FROM specimens WHERE collection_ref=p_coll_ref) );
RAISE NOTICE 'after delete tag_groups %', (SELECT count(*) FROM tag_groups);
*/


RAISE NOTICE 'before delete staging_info %', (SELECT count(*) FROM staging_info);
DELETE FROM staging_info WHERE staging_ref IN (SELECT distinct gtu_ref FROM staging   WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref));
RAISE NOTICE 'after delete staging_info %', (SELECT count(*) FROM staging_info);

ALTER TABLE identifications DISABLE TRIGGER user ;

RAISE NOTICE 'before delete identifications (specimens) %', (SELECT count(*) FROM identifications);
DELETE FROM identifications WHERE referenced_relation='specimens' AND record_id IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref);

RAISE NOTICE 'after delete identifications (specimens) %', (SELECT count(*) FROM identifications);

ALTER TABLE identifications ENABLE  TRIGGER user;

RAISE NOTICE 'before delete identifications (staging) %', (SELECT count(*) FROM identifications);
DELETE FROM identifications WHERE referenced_relation='staging' AND record_id IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref));
RAISE NOTICE 'atfer delete identifications (staging) %', (SELECT count(*) FROM identifications);


--DELETE FROM taxonomy WHERE id IN (SELECT taxon)

--DELETE FROM gtu WHERE id in (SELECT gtu_ref FROM specimens WHERE collection_ref=p_coll_ref) ;

--DELETE FROM igs WHERE id in (SELECT if_ref FROM specimens WHERE collection_ref=p_coll_ref) ;

ALTER TABLE specimens DISABLE TRIGGER trg_chk_specimencollectionallowed;

RAISE NOTICE 'update specimens nullify FKs';
UPDATE specimens SET gtu_ref=NULL, taxon_ref=NULL, ig_ref=NULL WHERE collection_ref=p_coll_ref;

ALTER TABLE specimens ENABLE TRIGGER trg_chk_specimencollectionallowed;

RAISE NOTICE 'update staging nullify FKs';
UPDATE staging SET gtu_ref=NULL, taxon_ref=NULL, ig_ref=NULL WHERE id IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) ;


ALTER TABLE taxonomy DISABLE TRIGGER user;
RAISE NOTICE 'before delete taxonomy %', (SELECT count(*) FROM taxonomy);
DELETE FROM taxonomy WHERE coalesce(id,-1) NOT in (SELECT coalesce(taxon_ref,-2) FROM specimens) AND coalesce(id,-1) not in (SELECT coalesce(parent_ref, -2) FROM taxonomy) ;--AND id NOT in (SELECT taxon_ref FROM staging);
RAISE NOTICE 'after delete taxonomy %', (SELECT count(*) FROM taxonomy);
ALTER TABLE taxonomy ENABLE TRIGGER user;


RAISE NOTICE 'before delete gtu %', (SELECT count(*) FROM gtu);
DELETE FROM gtu WHERE id NOT in (SELECT gtu_ref FROM specimens) AND id NOT in (SELECT gtu_ref FROM staging);
RAISE NOTICE 'ater delete gtu %', (SELECT count(*) FROM gtu);


RAISE NOTICE 'before delete igs %', (SELECT count(*) FROM igs);
DELETE FROM igs WHERE id NOT in (SELECT ig_ref FROM specimens) AND id NOT in (SELECT ig_ref FROM staging);

RAISE NOTICE 'after delete igs %', (SELECT count(*) FROM igs);

RAISE NOTICE 'before delete specimens_relationshipes %', (SELECT count(*) FROM specimens_relationships);
DELETE FROM specimens_relationships WHERE unit_type='specimens' AND (specimen_ref IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref) OR specimen_related_ref IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref));
RAISE NOTICE 'after delete specimens_relationshipes %', (SELECT count(*) FROM specimens_relationships);

RAISE NOTICE 'before delete staging %', (SELECT count(*) FROM staging);
DELETE FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref);
RAISE NOTICE 'after  delete staging %', (SELECT count(*) FROM staging);


ALTER TABLE specimens DISABLE TRIGGER user;
RAISE NOTICE 'before delete specimens %', (SELECT count(*) FROM specimens);
DELETE FROM specimens WHERE collection_ref=p_coll_ref;
RAISE NOTICE 'after delete specimens %', (SELECT count(*) FROM specimens);
--DELETE FROM collections WHERE id=p_coll_ref;
ALTER TABLE specimens ENABLE TRIGGER user;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION rmca_delete_specimens_from_collection(integer)
  OWNER TO darwin2;
