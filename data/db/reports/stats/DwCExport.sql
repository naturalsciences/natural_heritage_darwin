drop table if exists zzz_exportDwC;
select 
  'PhysicalObject'::text as "dcterms:type",
  coalesce((select max(modification_date_time) at time zone 'CET' from users_tracking where referenced_relation = 'specimens' and record_id = sp.id), current_timestamp at time zone 'CET') as "dcterms:modified",
  'en'::text as "dcterms:language",
  'https://creativecommons.org/licenses/by-nc/4.0/legalcode'::text as "dcterms:license",
  'Royal belgian Institute of natural Sciences'::text as "dcterms:rightsHolder",
  'http://biocol.org/urn:lsid:biocol.org:col:35271'::text as "institutionID",
  'http://grbio.org/cool/wt1g-5s4r'::text as "collectionID",
  'RBINS_DaRWIN'::text as "datasetID", -- Try to get a GUID from a platform that could offer that - Contact Anton for that
  'RBINS'::text as "institutionCode",
  translate(collection_code,'\/.()- ë', '_______e') as "collectionCode",
  collection_name as "datasetName",
  'RBINS'::text as "ownerInstitutionCode",
  CASE 
    WHEN collection_ref = 3 OR collection_path LIKE '/3/%' THEN
      'FossilSpecimen'
    WHEN collection_ref = 231 OR collection_path LIKE '/231/%' THEN
      'GeologicalContext'
    ELSE
      'PreservedSpecimen'
  END as "basisOfRecord",
  CASE
    WHEN station_visible = FALSE THEN
      'precise location information not given - country only'
    ELSE
      ''
  END as "informationWithheld",
  ''::text as "dynamicProperties", -- To Be filled once the Measurements or Facts have been completely explored
  'urn:catalog:RBINS:'||translate(collection_code,'\/.()- ë', '_______e')||':'||id as "occurenceID",
  coalesce((select array_to_string(array_agg(trim((case when code_prefix is not null and code_prefix != '' then case when code_prefix_separator is not null and code_prefix_separator != '' then code_prefix || code_prefix_separator else code_prefix end else '' end) || code || (case when code_suffix is not null and code_suffix != '' then case when code_suffix_separator is not null and code_suffix_separator != '' then code_suffix_separator || code_suffix else code_suffix end else '' end))),' | ') from codes where referenced_relation = 'specimens' and record_id = sp.id and code_category = 'main'),'') as "catalogNumer",
  coalesce((select array_to_string(array_agg(trim((case when code_prefix is not null and code_prefix != '' then case when code_prefix_separator is not null and code_prefix_separator != '' then code_prefix || code_prefix_separator else code_prefix end else '' end) || code || (case when code_suffix is not null and code_suffix != '' then case when code_suffix_separator is not null and code_suffix_separator != '' then code_suffix_separator || code_suffix else code_suffix end else '' end))),' | ') from codes where referenced_relation = 'specimens' and record_id = sp.id and code_category in ('inventory', 'code', 'Code')),'') as "recordNumber",
  coalesce((select array_to_string(array_agg(trim(p.formated_name)),' | ') from catalogue_people cp inner join people p on cp.people_ref = p.id where cp.people_type = 'collector' and cp.referenced_relation = 'specimens' and record_id = sp.id and p.is_physical = true),'') as "recordedBy",
  (case when specimen_count_min is not null then (specimen_count_max+specimen_count_min)/2 else null end)::integer as "individualCount",
  case when sex in ('unknown', 'non applicable', 'undefined', 'not stated', 'not applicable') then '' else sex end as "sex",
  case when stage in ('unknown', 'non applicable', 'undefined', 'not stated', 'not applicable') then '' else stage end as "lifeStage",
  case when state in ('unknown', 'non applicable', 'undefined', 'not stated', 'not applicable') then '' else state end as "reproductiveCondition",
  array_to_string(regexp_split_to_array(replace(replace(replace(regexp_replace(specimen_part, '\s+', ' ', 'g'), '&', '+'), ',', '+'), ';', '+'), '\+'), ' | ') as "preparations",
  specimen_status as "disposition",
  coalesce((
      select array_to_string(array_agg('https://darwin.naturalsciences.be/multimedia/downloadFile/id/'||id),' | ')
      from (
          select id
          from multimedia
          where referenced_relation = 'specimens' 
            and record_id = sp.id
/*          union
          select distinct m.id
          from catalogue_bibliography cb
          inner join bibliography b on cb.referenced_relation = 'specimens' and cb.record_id = sp.id  and cb.bibliography_ref = b.id
          inner join multimedia m on b.id = m.record_id and m.referenced_relation = 'bibliography'*/
      ) as zzz_temp
  ), '') as "associatedMedia",
  coalesce((select array_to_string(array_agg(distinct title), ' | ') from catalogue_bibliography inner join bibliography b on referenced_relation = 'specimens' and record_id = sp.id and bibliography_ref = b.id),'') as "associatedReferences",
  coalesce(
      (
       select array_to_string(array_agg(distinct denom),' | ') 
       from
       (
         select relationship_type || ': ' || t.name as denom
         from specimens_relationships sr
         inner join taxonomy t on sr.taxon_ref = t.id and sr.specimen_ref = sp.id and sr.relationship_type in ('host', 'associated specimen')
         union
         select relationship_type || ': ' || t.name as denom
         from specimens_relationships sr
         inner join specimens ssp on sr.specimen_ref = sp.id and sr.specimen_related_ref = ssp.id and sr.relationship_type in ('host', 'associated specimen')
         inner join taxonomy t on ssp.taxon_ref = t.id
       ) as subquery
      )
      ,''
  ) as "associatedTaxa",
  taxon_ref as "taxonID",
  taxon_name as "scientificName",
  coalesce(coalesce((select t.name 
            from taxonomy t 
            inner join classification_synonymies cs 
            on t.id = cs.record_id 
            and cs.referenced_relation = 'taxonomy' 
            and cs.is_basionym = true 
            and t.status='valid' 
            and cs.group_id = 
            (
              select group_id 
              from classification_synonymies 
              where referenced_relation = 'taxonomy' 
                and group_name = 'rename' 
                and record_id = sp.taxon_ref
            )
           ),
           (select sp.taxon_name 
            where 'valid' = (select status from taxonomy where id = sp.taxon_ref)
           )
          ),'') as "acceptedNameUsage",
  coalesce((select name from taxonomy where id = sp.taxon_parent_ref),'') as "parentNameUsage",
  coalesce(
    (
            select t.name 
            from taxonomy t 
            inner join classification_synonymies cs 
            on t.id = cs.record_id 
            and cs.referenced_relation = 'taxonomy' 
            and cs.is_basionym = true 
            and t.status='valid' 
            and cs.group_id = 
            (
              select group_id 
              from classification_synonymies 
              where referenced_relation = 'taxonomy' 
                and group_name = 'synonym' 
                and record_id = sp.taxon_ref
            )
    ),
    ''
  ) as "originalNameUsage",
  ''::text as "higherClassification"
INTO TABLE zzz_exportDwC
from specimens as sp
where collection_is_public = true 
--  and coalesce((select array_to_string(array_agg(trim((case when code_prefix is not null and code_prefix != '' then case when code_prefix_separator is not null and code_prefix_separator != '' then code_prefix || code_prefix_separator else code_prefix end else '' end) || code || (case when code_suffix is not null and code_suffix != '' then case when code_suffix_separator is not null and code_suffix_separator != '' then code_suffix || code_suffix_separator else code_suffix end else '' end))),' | ') from codes where referenced_relation = 'specimens' and record_id = sp.id and code_category in ('inventory', 'code', 'Code')),'') != ''
--  and coalesce((select array_to_string(array_agg(trim(p.formated_name)),' | ') from catalogue_people cp inner join people p on cp.people_ref = p.id where cp.people_type = 'collector' and cp.referenced_relation = 'specimens' and record_id = sp.id and p.is_physical = true),'') != ''
--  and (exists (select 1 from multimedia where referenced_relation = 'specimens' and record_id = sp.id)
--  or exists (select 1 from catalogue_bibliography where referenced_relation = 'specimens' and record_id = sp.id))
-- and exists (select 1 from specimens_relationships where specimen_ref = sp.id)
--and exists (select group_id from classification_synonymies where referenced_relation = 'taxonomy' and group_name = 'rename' and record_id = sp.taxon_ref)
and exists (select group_id 
              from classification_synonymies 
              where referenced_relation = 'taxonomy' 
                and group_name = 'synonym' 
                and record_id = sp.taxon_ref)
order by "basisOfRecord","datasetName"
limit 5000
;
select * from zzz_exportDwC;
