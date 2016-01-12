select mtax.id, 
       mtax.name, 
       cl.id as level_id, 
       cl.level_name, 
       mtax.path,
       (select string_agg(subtax.name, ';')
        from taxonomy as subtax inner join (select unnest(string_to_array(substring(mtax.path || mtax.id from 2), '/')) as id) as taxids
        on subtax.id = taxids.id::integer
        where taxids.id != ''
       ) as taxonomic_path
  from taxonomy mtax inner join catalogue_levels cl on mtax.level_ref = cl.id
  where mtax.id in (select stax.id 
                    from taxonomy stax 
                    where exists (select 1
                                  from taxonomy as innertax
                                  inner join specimens on innertax.id = specimens.taxon_ref
                                  inner join collections on collections.id = specimens.collection_ref
                                  where collections.name_indexed = 'pisces'
                                    and strpos(innertax.path, '/' || stax.id || '/') != 0
                                  limit 1
                                 ) 
                      and stax.level_ref = 12
                   )
     and mtax.level_ref = 12;
