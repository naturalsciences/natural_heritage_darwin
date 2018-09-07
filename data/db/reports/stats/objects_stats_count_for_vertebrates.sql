select * 
from
(
  select 'Amphibiens non types' as title,
         sum(specimen_count_min) as min_count,
         sum(specimen_count_max) as max_count
  from specimens
  where collection_ref = 5
    and exists (
                 select 1
		 from taxonomy tax1
		 where tax1.id = taxon_ref
		   and case when taxon_ref = 110 then true
		       else strpos(tax1.path, (select tax2.path||tax2.id from taxonomy tax2 where tax2.id = 110)) = 1
		       end
               )
   union
   select 'Amphibiens types' as title,
         sum(specimen_count_min) as min_count,
         sum(specimen_count_max) as max_count
  from specimens
  where collection_ref = 10
    and exists (
                 select 1
		 from taxonomy tax1
		 where tax1.id = taxon_ref
		   and case when taxon_ref = 110 then true
		       else strpos(tax1.path, (select tax2.path||tax2.id from taxonomy tax2 where tax2.id = 110)) = 1
		       end
               )
  union
  select 'Reptiles non types' as title,
         sum(specimen_count_min) as min_count,
         sum(specimen_count_max) as max_count
  from specimens
  where collection_ref in (9, 181)
    and exists (
                 select 1
		 from taxonomy tax1
		 where tax1.id = taxon_ref
		   and case when taxon_ref = 114 then true
		       else strpos(tax1.path, (select tax2.path||tax2.id from taxonomy tax2 where tax2.id = 114)) = 1
		       end
               )
  union
  select 'Reptiles types' as title,
         sum(specimen_count_min) as min_count,
         sum(specimen_count_max) as max_count
  from specimens
  where collection_ref = 10
    and exists (
                 select 1
		 from taxonomy tax1
		 where tax1.id = taxon_ref
		   and case when taxon_ref = 114 then true
		       else strpos(tax1.path, (select tax2.path||tax2.id from taxonomy tax2 where tax2.id = 114)) = 1
		       end
               )
) as subqry
order by title;
