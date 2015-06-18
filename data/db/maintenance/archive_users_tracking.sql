SET search_path TO darwin2,"$user",public;
begin;

drop table if exists zzz_users_tracking_archived;

create table zzz_users_tracking_archived (id, referenced_relation, record_id, user_ref, "action", old_value, new_value, modification_date_time, specimen_individual_ref, specimen_ref)
as (select *, record_id::bigint, case when action = 'delete' then old_value -> 'specimen_ref' else new_value -> 'specimen_ref' end::bigint from users_tracking where referenced_relation = 'specimen_individuals');

alter table zzz_users_tracking_archived OWNER TO darwin2;

drop index if exists zzz_uta_specimen_individual_ref;
create index zzz_uta_specimen_individual_ref on zzz_users_tracking_archived (specimen_individual_ref);

insert into zzz_users_tracking_archived
   (
     select distinct ut.*, case when ut.action = 'delete' then ut.old_value -> 'specimen_individual_ref' else ut.new_value -> 'specimen_individual_ref' end::bigint, uta.specimen_ref
     from users_tracking as ut 
     left join zzz_users_tracking_archived as uta 
     on case when ut.action = 'delete' then ut.old_value -> 'specimen_individual_ref' else ut.new_value -> 'specimen_individual_ref' end::bigint = uta.specimen_individual_ref
     where ut.referenced_relation = 'specimen_parts'
   );
drop index if exists zzz_uta_record_id;
drop index if exists zzz_uta_referenced_relation;
drop index if exists zzz_uta_specimen_ref;
drop index if exists zzz_uta_user_ref;
drop index if exists zzz_uta_id;
drop index if exists zzz_uta_modification_date_time;

create index zzz_uta_record_id on zzz_users_tracking_archived (record_id);
create index zzz_uta_referenced_relation on zzz_users_tracking_archived (referenced_relation);
create index zzz_uta_specimen_ref on zzz_users_tracking_archived (specimen_ref);
create index zzz_uta_user_ref on zzz_users_tracking_archived (user_ref);
create index zzz_uta_id on zzz_users_tracking_archived (id);
create index zzz_uta_modification_date_time on zzz_users_tracking_archived (modification_date_time);

insert into zzz_users_tracking_archived
(select ut.*, null::bigint, ut.record_id::bigint
 from users_tracking as ut 
 inner join specimens on ut.record_id = specimens.id
 where referenced_relation = 'specimens'
   and not exists (select 1
                   from collections_rights
                   where collection_ref = specimens.collection_ref
                   and user_ref = ut.user_ref
                  )
);

delete from users_tracking
where id in (select id from zzz_users_tracking_archived);

commit;

