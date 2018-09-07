select distinct ON (taxonomic_path, level_ref)
      case
        when specimens.id is null then 0 
        else 1 
      end as is_explicitly_linked_with_a_specimen,
      (select string_agg(subtax.name, ';')
       from taxonomy as subtax inner join (select unnest(string_to_array(substring(tax.path || tax.id from 2), '/')) as id) as taxids
       on subtax.id = taxids.id::integer
       where taxids.id != ''
      ) as taxonomic_path
from taxonomy as tax left join specimens on tax.id = specimens.taxon_ref
where tax.id in (select sstax.id 
                from taxonomy sstax
                where sstax.id in (118, 112, 55252, 55250, 115)
               )
      or strpos	(tax.path, (select ssstax.path || ssstax.id 
                            from taxonomy ssstax 
                            where ssstax.id = 118 
                           )
                ) != 0
      or strpos	(tax.path, (select ssstax.path || ssstax.id 
                            from taxonomy ssstax 
                            where ssstax.id = 112 
                           )
                ) != 0
      or strpos	(tax.path, (select ssstax.path || ssstax.id 
                            from taxonomy ssstax 
                            where ssstax.id = 55252 
                           )
                ) != 0
      or strpos	(tax.path, (select ssstax.path || ssstax.id 
                            from taxonomy ssstax 
                            where ssstax.id = 55250 
                           )
                ) != 0
      or strpos	(tax.path, (select ssstax.path || ssstax.id 
                            from taxonomy ssstax 
                            where ssstax.id = 115 
                           )
                ) != 0
order by taxonomic_path, level_ref;
