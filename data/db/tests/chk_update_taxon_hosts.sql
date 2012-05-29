\unset ECHO
\i unit_launch.sql
SELECT plan(5);

SELECT diag('Hosting update tests');

-- TESTING specimen hosting
INSERT INTO specimens (id, collection_ref, taxon_ref) (select 10000,1, id from taxonomy where name = 'Eucaryota');
INSERT INTO specimens (id, collection_ref, host_specimen_ref, host_taxon_ref) (select 10002,1,10000, id from taxonomy where name = 'Eucaryota');
INSERT INTO specimens (id, collection_ref, taxon_ref) (select 10003,1,id from taxonomy where name = 'Bacteria');


SELECT lives_ok('UPDATE specimens SET taxon_ref = (select id from taxonomy where name = ' || chr(39) || 'Virus' || chr(39) || ') WHERE id = 10000', 'The modification of taxon ref has been well executed');
SELECT ok((select id from taxonomy where name = 'Virus') = (SELECT host_taxon_ref FROM specimens WHERE id = 10002), 'Update of hoster taxon id has been well an impact on host_taxon_ref');
SELECT ok(10000 = (SELECT host_specimen_ref FROM specimens WHERE id = 10002), 'Host specimen ref of specimen 10002 is well specimen 10000');
SELECT lives_ok('UPDATE specimens SET host_specimen_ref = 10003 WHERE id = 10002', 'Modification of host specimen ref has been well executed');
SELECT ok(10003 = (SELECT host_specimen_ref FROM specimens WHERE id = 10002), 'Update of hoster has been well done');

SELECT * FROM finish();
ROLLBACK;
