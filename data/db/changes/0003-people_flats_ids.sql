create sequence darwin_flat_id_seq;

alter table darwin_flat
  ADD spec_ident_ids integer[],
  ADD ind_ident_ids integer[],
  ADD spec_coll_ids integer[],
  ADD spec_don_sel_ids integer[] ;


comment on column darwin_flat.spec_ident_ids is 'Array of identifiers referenced in this specimen';
comment on column darwin_flat.ind_ident_ids is 'Array of identifiers referenced in this individual';
comment on column darwin_flat.spec_coll_ids is 'Array of collectors referenced in this specimen';
comment on column darwin_flat.spec_don_sel_ids is 'Array of donators or sellers referenced in this specimen';

update darwin_flat f 
set spec_coll_ids  = ( select array_accum(DISTINCT people_ref) from catalogue_people where referenced_relation='specimens' and people_type='collector' and record_id = f.spec_ref),
spec_don_sel_ids  = ( select array_accum(DISTINCT people_ref) from catalogue_people where referenced_relation='specimens' and people_type='donator' and record_id = f.spec_ref),
spec_ident_ids  = ( select array_accum(DISTINCT people_ref) from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications' where i.referenced_relation='specimens' and p.people_type='identifier' and i.record_id = f.spec_ref ),
ind_ident_ids  = ( select array_accum(DISTINCT people_ref) from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications' where i.referenced_relation='specimen_individuals'
and p.people_type='identifier' and i.record_id = f.individual_ref );
