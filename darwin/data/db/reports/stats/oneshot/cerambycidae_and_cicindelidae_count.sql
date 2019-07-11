select
  (select count (distinct taxonomy.id)
   from taxonomy
   inner join
   (specimens inner join collections on specimens.collection_ref = collections.id)
   on specimens.taxon_ref = taxonomy.id
   where level_ref in (47, 48)
     and taxonomy.path like '%/109042/%'
     and (collections.path like '%/49/%' or collections.id = 49)
  ) as Cerambycidae_Species,
  (select count (distinct taxonomy.id)
   from taxonomy
   inner join
   (specimens inner join collections on specimens.collection_ref = collections.id)
   on specimens.taxon_ref = taxonomy.id
   where level_ref > 48
     and taxonomy.path like '%/109042/%'
     and (collections.path like '%/49/%' or collections.id = 49)
  ) as Cerambycidae_Sub_Species,
  (select count (distinct taxonomy.id)
   from taxonomy
   inner join
   (specimens inner join collections on specimens.collection_ref = collections.id)
   on specimens.taxon_ref = taxonomy.id
   where level_ref in (47, 48)
     and taxonomy.path like '%/2741/%'
     and (collections.path like '%/49/%' or collections.id = 49)
  ) as Cicindelidae_Species,
  (select count (distinct taxonomy.id)
   from taxonomy
   inner join
   (specimens inner join collections on specimens.collection_ref = collections.id)
   on specimens.taxon_ref = taxonomy.id
   where level_ref > 48
     and taxonomy.path like '%/2741/%'
     and (collections.path like '%/49/%' or collections.id = 49)
  ) as Cicindelidae_Sub_Species
;
