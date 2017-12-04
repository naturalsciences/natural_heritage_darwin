\unset ECHO
\i unit_launch.sql
SELECT plan(40);

INSERT INTO specimens (id, collection_ref,type) VALUES (1,1,'specimen');
SELECT ok( 'specimen' = (SELECT type_group FROM specimens WHERE id=1)) ;
SELECT ok( 'specimen' = (SELECT type_search FROM specimens WHERE id=1));

INSERT INTO specimens (id, collection_ref,type) VALUES (2,1,'type');
SELECT ok( 'type' = (SELECT type_group FROM specimens WHERE id=2)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=2));

INSERT INTO specimens (id, collection_ref,type) VALUES (3,1,'subtype');
SELECT ok( 'type' = (SELECT type_group FROM specimens WHERE id=3)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=3));

INSERT INTO specimens (id, collection_ref,type) VALUES (4,1,'allotype');
SELECT ok( 'allotype' = (SELECT type_group FROM specimens WHERE id=4)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=4));

INSERT INTO specimens (id, collection_ref,type) VALUES (5,1,'cotype');
SELECT ok( 'syntype' = (SELECT type_group FROM specimens WHERE id=5)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=5));

INSERT INTO specimens (id, collection_ref,type) VALUES (6,1,'genotype');
SELECT ok( 'type' = (SELECT type_group FROM specimens WHERE id=6)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=6));

INSERT INTO specimens (id, collection_ref,type) VALUES (7,1,'holotype');
SELECT ok( 'holotype' = (SELECT type_group FROM specimens WHERE id=7)) ;
SELECT ok( 'holotype' = (SELECT type_search FROM specimens WHERE id=7));

INSERT INTO specimens (id, collection_ref,type) VALUES (8,1,'hypotype');
SELECT ok( 'hypotype' = (SELECT type_group FROM specimens WHERE id=8)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=8));

INSERT INTO specimens (id, collection_ref,type) VALUES (9,1,'lectotype');
SELECT ok( 'lectotype' = (SELECT type_group FROM specimens WHERE id=9)) ;
SELECT ok( 'lectotype' = (SELECT type_search FROM specimens WHERE id=9));

INSERT INTO specimens (id, collection_ref,type) VALUES (10,1,'locotype');
SELECT ok( 'locotype' = (SELECT type_group FROM specimens WHERE id=10)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=10));

INSERT INTO specimens (id, collection_ref,type) VALUES (11,1,'neallotype');
SELECT ok( 'type' = (SELECT type_group FROM specimens WHERE id=11)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=11));

INSERT INTO specimens (id, collection_ref,type) VALUES (12,1,'type in litteris');
SELECT ok( 'type in litteris' = (SELECT type_group FROM specimens WHERE id=12)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=12));

INSERT INTO specimens (id, collection_ref,type) VALUES (13,1,'neotype');
SELECT ok( 'neotype' = (SELECT type_group FROM specimens WHERE id=13)) ;
SELECT ok( 'neotype' = (SELECT type_search FROM specimens WHERE id=13));

INSERT INTO specimens (id, collection_ref,type) VALUES (14,1,'paralectotype');
SELECT ok( 'paralectotype' = (SELECT type_group FROM specimens WHERE id=14)) ;
SELECT ok( 'paralectotype' = (SELECT type_search FROM specimens WHERE id=14));

INSERT INTO specimens (id, collection_ref,type) VALUES (15,1,'paratype');
SELECT ok( 'paratype' = (SELECT type_group FROM specimens WHERE id=15)) ;
SELECT ok( 'paratype' = (SELECT type_search FROM specimens WHERE id=15));

INSERT INTO specimens (id, collection_ref,type) VALUES (16,1,'plastotype');
SELECT ok( 'plastotype' = (SELECT type_group FROM specimens WHERE id=16)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=16));

INSERT INTO specimens (id, collection_ref,type) VALUES (17,1,'plesiotype');
SELECT ok( 'plesiotype' = (SELECT type_group FROM specimens WHERE id=17)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=17));

INSERT INTO specimens (id, collection_ref,type) VALUES (18,1,'syntype');
SELECT ok( 'syntype' = (SELECT type_group FROM specimens WHERE id=18)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=18));

INSERT INTO specimens (id, collection_ref,type) VALUES (19,1,'topotype');
SELECT ok( 'topotype' = (SELECT type_group FROM specimens WHERE id=19)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=19));

INSERT INTO specimens (id, collection_ref,type) VALUES (20,1,'caratype');
SELECT ok( 'type' = (SELECT type_group FROM specimens WHERE id=20)) ;
SELECT ok( 'type' = (SELECT type_search FROM specimens WHERE id=20));

-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;