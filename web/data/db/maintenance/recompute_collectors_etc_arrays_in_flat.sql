
drop index if exists idx_gin_darwin_flat_spec_coll_ids;
drop index if exists idx_gin_darwin_flat_spec_don_sel_ids;
drop index if exists idx_gin_darwin_flat_spec_ident_ids;
drop index if exists idx_gin_specimens_flat_spec_coll_ids;
drop index if exists idx_gin_specimens_flat_spec_don_sel_ids;
drop index if exists idx_gin_specimens_flat_spec_ident_ids;

update specimens_flat as sf
set spec_coll_ids = (
                      SELECT array_agg(distinct cp.people_ref)
                      FROM catalogue_people as cp 
                      WHERE cp.record_id = sf.specimen_ref
                        and cp.referenced_relation = 'specimens' 
                        and cp.people_type = 'collector'
                      group by cp.referenced_relation, cp.record_id, cp.people_type
                    )
where spec_coll_ids != (
                        SELECT array_agg(distinct cp.people_ref)
                        FROM catalogue_people as cp 
                        WHERE cp.record_id = sf.specimen_ref
                          and cp.referenced_relation = 'specimens' 
                          and cp.people_type = 'collector'
                        group by cp.referenced_relation, cp.record_id, cp.people_type
                       );

update specimens_flat as df
set spec_don_sel_ids = (
                        SELECT array_agg(distinct cp.people_ref)
                        FROM catalogue_people as cp 
                        WHERE cp.record_id = sf.specimen_ref
                          and cp.referenced_relation = 'specimens' 
                          and cp.people_type = 'donator'
                        group by cp.referenced_relation, cp.record_id, cp.people_type
                       )
where spec_don_sel_ids != (
                            SELECT array_agg(distinct cp.people_ref)
                            FROM catalogue_people as cp 
                            WHERE cp.record_id = sf.specimen_ref
                              and cp.referenced_relation = 'specimens' 
                              and cp.people_type = 'donator'
                            group by cp.referenced_relation, cp.record_id, cp.people_type
                          );

update specimens_flat as df
set spec_ident_ids = (
                      SELECT array_agg(distinct cp.people_ref)
                      FROM catalogue_people as cp inner join identifications as ident on cp.record_id = ident.id and cp.referenced_relation = 'identifications' and cp.people_type = 'identifier'
                      WHERE ident.referenced_relation = 'specimens' 
                        and ident.record_id = sf.specimen_ref
                      group by ident.referenced_relation, ident.record_id, cp.people_type
                     )
where spec_ident_ids != (
                          SELECT array_agg(distinct cp.people_ref)
                          FROM catalogue_people as cp inner join identifications as ident on cp.record_id = ident.id and cp.referenced_relation = 'identifications' and cp.people_type = 'identifier'
                          WHERE ident.referenced_relation = 'specimens' 
                            and ident.record_id = sf.specimen_ref
                          group by ident.referenced_relation, ident.record_id, cp.people_type
                        );

CREATE INDEX idx_gin_specimens_flat_spec_coll_ids
  ON specimens_flat
  USING gin
  (spec_coll_ids);

CREATE INDEX idx_gin_specimens_flat_spec_don_sel_ids
  ON specimens_flat
  USING gin
  (spec_don_sel_ids);

CREATE INDEX idx_gin_specimens_flat_spec_ident_ids
  ON specimens_flat
  USING gin
  (spec_ident_ids);
