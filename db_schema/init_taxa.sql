DELETE FROM taxonomy;
DELETE from taxonomy_metadata;
ALTER SEQUENCE taxonomy_id_seq RESTART;
ALTER SEQUENCE taxonomy_metadata_id_seq RESTART;
insert into taxonomy_metadata (id,taxonomy_name, is_reference_taxonomy, creation_date)	values(1, 'Reference taxonomy', true, now());
insert into taxonomy (name, level_ref, path, metadata_ref) values('Eucaryota',1,'',1);
insert into taxonomy (name, level_ref, metadata_ref, path, parent_ref) values('Animalia',2,1,'/1/',1);

