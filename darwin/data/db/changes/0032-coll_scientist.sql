set search_path=darwin2;
alter table collections add column staff_ref integer;
alter table collections add constraint fk_collections_staff foreign key (staff_ref) references users(id) on delete set null;
comment on column collections.staff_ref is 'Reference of staff member, scientist responsible - id field of users table';
