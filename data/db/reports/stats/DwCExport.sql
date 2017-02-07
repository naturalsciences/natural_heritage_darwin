select distinct
  'PhysicalObject' as "dcterms:type",
  coalesce((select max(modification_date_time) at time zone 'CET' from users_tracking where referenced_relation = 'specimens' and record_id = sp.id), current_timestamp at time zone 'CET') as "dcterms:modified",
  'en' as "dcterms:language",
  'https://creativecommons.org/licenses/by-nc/4.0/legalcode' as "dcterms:license",
  'Royal belgian Institute of natural Sciences' as "dcterms:rightsHolder",
  'http://biocol.org/urn:lsid:biocol.org:col:35271' as "institutionID",
  'http://grbio.org/cool/wt1g-5s4r' as "collectionID",
  'RBINS_DaRWIN' as "datasetID", -- Try to get a GUID from a platform that could offer that - Contact Anton for that
  'RBINS' as "institutionCode",
  translate(collection_code,'\/.()- ë', '_______e') as "collectionCode",
  collection_name as "datasetName",
  'RBINS' as "ownerInstitutionCode",
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
  '' as "dynamicProperties", -- To Be filled once the Measurements or Facts have been completely explored
  'urn:catalog:RBINS:'||translate(collection_code,'\/.()- ë', '_______e')||':'||id as "occurenceID",
  coalesce((select array_to_string(array_agg(trim((case when code_prefix is not null and code_prefix != '' then case when code_prefix_separator is not null and code_prefix_separator != '' then code_prefix || code_prefix_separator else code_prefix end else '' end) || code || (case when code_suffix is not null and code_suffix != '' then case when code_suffix_separator is not null and code_suffix_separator != '' then code_suffix || code_suffix_separator else code_suffix end else '' end))),' | ') from codes where referenced_relation = 'specimens' and record_id = sp.id and code_category in ('main','code','Code')),'') as "catalogNumer",
  coalesce((select array_to_string(array_agg(trim((case when code_prefix is not null and code_prefix != '' then case when code_prefix_separator is not null and code_prefix_separator != '' then code_prefix || code_prefix_separator else code_prefix end else '' end) || code || (case when code_suffix is not null and code_suffix != '' then case when code_suffix_separator is not null and code_suffix_separator != '' then code_suffix || code_suffix_separator else code_suffix end else '' end))),' | ') from codes where referenced_relation = 'specimens' and record_id = sp.id and code_category in ('inventory', 'secondary', 'Secondary')),'') as "recordNumber",
  coalesce((select array_to_string(array_agg(trim(p.formated_name)),' | ') from catalogue_people cp inner join people p on cp.people_ref = p.id where cp.people_type = 'collector' and cp.referenced_relation = 'specimens' and record_id = sp.id and p.is_physical = true),'') as "recordedBy",
  (case when specimen_count_min is not null then (specimen_count_max+specimen_count_min)/2 else null end)::integer as "individualCount",
  case when sex in ('unknown', 'non applicable', 'undefined', 'not stated', 'not applicable') then '' else sex end as "sex",
  case when stage in ('unknown', 'non applicable', 'undefined', 'not stated', 'not applicable') then '' else stage end as "lifeStage",
  case when state in ('unknown', 'non applicable', 'undefined', 'not stated', 'not applicable') then '' else state end as "reproductiveCondition",
  array_to_string(regexp_split_to_array(replace(replace(replace(regexp_replace(specimen_part, '\s+', ' ', 'g'), '&', '+'), ',', '+'), ';', '+'), '\+'), ' | ') as "preparations",
  specimen_status as "disposition",
  '' as "associatedMedia"
from specimens as sp
where collection_is_public = true and strpos(specimen_part, '&') != 0
--order by "basisOfRecord","datasetName"
limit 200
;