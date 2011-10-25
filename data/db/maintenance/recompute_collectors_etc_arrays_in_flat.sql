drop index if exists idx_gin_darwin_flat_spec_coll_ids;
drop index if exists idx_gin_darwin_flat_spec_don_sel_ids;
drop index if exists idx_gin_darwin_flat_spec_ident_ids;
drop index if exists idx_gin_darwin_flat_ind_ident_ids;

update darwin_flat as df
set spec_coll_ids = (
		     SELECT array_agg(distinct cp.people_ref)
		     FROM catalogue_people as cp 
		     WHERE cp.record_id = df.spec_ref
		       and cp.referenced_relation = 'specimens' 
		       and cp.people_type = 'collector'
		     group by cp.referenced_relation, cp.record_id, cp.people_type
	            ),
    spec_don_sel_ids = (
		        SELECT array_agg(distinct cp.people_ref)
		        FROM catalogue_people as cp 
		        WHERE cp.record_id = df.spec_ref
		          and cp.referenced_relation = 'specimens' 
		          and cp.people_type = 'donator'
		        group by cp.referenced_relation, cp.record_id, cp.people_type
	               ), 
    spec_ident_ids = (
		       SELECT array_agg(distinct cp.people_ref)
		       FROM catalogue_people as cp inner join identifications 
		       WHERE cp.record_id = df.spec_ref
		         and cp.referenced_relation = 'identifications' 
		         and cp.people_type = 'identifier'
		       group by cp.referenced_relation, cp.record_id, cp.people_type
	             ),
   ind_ident_ids = (
		    SELECT array_agg(distinct cp.people_ref)
		    FROM catalogue_people as cp 
		    WHERE cp.record_id = df.spec_ref
		      and cp.referenced_relation = 'identifications' 
		      and cp.people_type = 'identifier'
		    group by cp.referenced_relation, cp.record_id, cp.people_type
	           )
  ;

CREATE INDEX idx_gin_darwin_flat_spec_coll_ids
  ON darwin_flat
  USING gin
  (spec_coll_ids);

CREATE INDEX idx_gin_darwin_flat_spec_don_sel_ids
  ON darwin_flat
  USING gin
  (spec_don_sel_ids);

CREATE INDEX idx_gin_darwin_flat_spec_ident_ids
  ON darwin_flat
  USING gin
  (spec_ident_ids);

CREATE INDEX idx_gin_darwin_flat_ind_ident_ids
  ON darwin_flat
  USING gin
  (ind_ident_ids);
