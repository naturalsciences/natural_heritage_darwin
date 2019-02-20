select
  coalesce(codes.code_prefix,'') ||
  coalesce(codes.code_prefix_separator, '') ||
  coalesce(codes.code,'') ||
  coalesce(codes.code_suffix_separator, '') ||
  coalesce(codes.code_suffix, '') as "specimenID", -- valid only if only one code
  '' as "additionalID",
  '' as code,
  '' as "accessionNumber",
  '' as "datasetName",
  zzz_rotifer_specimens.category as "isPhysical",
  acquisition_category as "acquisitionType",
  donator_peo.formated_name as "acquiredFrom", -- valid only if only one donator
  '' as "acquisitionDay",
  '' as "acquisitionMonth",
  '' as "acquisitionYear",
  gtu.code as "samplingCode",
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'ocean'),'') as ocean,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'continent'),'') as continent,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'sea'),'') as sea,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'country'),'') as country,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'state'),'') as state_territory,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'province'),'') as province,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'region'),'') as region,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'archipelago'),'') as archipelago,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'district'),'') as district,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'county'),'') as county,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'Department'),'') as department,
  (select array_to_string(array_agg(tag_value), ' ; ')
   from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'island'
  ) as island,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'city'),'') as city,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'municipality'),'') as municipality,
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'populatedplace'),'') as "populatedPlace",
  coalesce((select tag_value from tag_groups where tag_groups.gtu_ref = zzz_rotifer_specimens.gtu_ref and sub_group_name_indexed = 'naturalsite'),'') as "naturalSite",
  '' as "exactSite",
  gtu.elevation as "elevationInMeters",
  '' as "depthInMeters", 
  gtu.latitude as latitude,
  gtu.longitude as longitude,
  (
    select array_to_string(array_agg(people.formated_name), ' ; ')
    from catalogue_people inner join people on people_ref = people.id and people_type = 'collector' and referenced_relation = 'zzz_rotifer_specimens' and record_id = zzz_rotifer_specimens.id
  ) as "collectedBy",
  case when gtu.gtu_from_date_mask >= 56 then
    extract (DAY from gtu.gtu_from_date)
  else
    null
  end::integer as "collectionStartDay",
  case when gtu.gtu_from_date_mask >= 48 then
    extract (MONTH from gtu.gtu_from_date)
  else
    null
  end::integer as "collectionStartMonth",
  case when gtu.gtu_from_date_mask >= 32 then
    extract (YEAR from gtu.gtu_from_date)
  else
    null
  end::integer as "collectionStartYear",
  '' as "collectionStartTimeH",
  '' as "collectionStartTimeM",
  case when gtu.gtu_to_date_mask >= 56 then
    extract (DAY from gtu.gtu_to_date)
  else
    null
  end::integer as "collectionEndDay",
  case when gtu.gtu_to_date_mask >= 48 then
    extract (MONTH from gtu.gtu_to_date)
  else
    null
  end::integer as "collectionEndMonth",
  case when gtu.gtu_to_date_mask >= 32 then
    extract (YEAR from gtu.gtu_to_date)
  else
    null
  end::integer as "collectionEndYear",
  '' as "collectionEndTimeH",
  '' as "collectionEndTimeM",
  expedition_name as "expedition_project",
  (
    select array_to_string(array_agg("method"), ' ; ')
    from specimen_collecting_methods inner join collecting_methods on collecting_method_ref = collecting_methods.id
    where specimen_ref = zzz_rotifer_specimens.id    
  ) as "samplingMethod",
  '' as fixation,
  '' as ecology,
  '' as "siteProperty_1", -- to check in the future into properties
  '' as "sitePropertyValue_1",
  '' as "siteProperty_2",
  '' as "sitePropertyValue_2",
  '' as "siteProperty_3",
  '' as "sitePropertyValue_3",
  '' as "siteProperty_4",
  '' as "sitePropertyValue_4",
  '' as "siteProperty_5",
  '' as "sitePropertyValue_5",
  '' as "siteProperty_6",
  '' as "sitePropertyValue_6",
  '' as "siteProperty_7",
  '' as "sitePropertyValue_7",
  '' as "siteProperty_8",
  '' as "sitePropertyValue_8",
  '' as "siteProperty_9",
  '' as "sitePropertyValue_9",
  '' as "siteProperty_10",
  '' as "sitePropertyValue_10",
  '' as "localityNotes",
  taxon_name as "taxonFullName",
  (
    select array_to_string(array_agg(people.formated_name), ' ; ')
    from people inner join catalogue_people on catalogue_people.people_ref = people.id and catalogue_people.referenced_relation = 'identifications' and catalogue_people.record_id = identifications.id
  ) as "identifiedBy",
  case 
    when notion_date_mask >= 56 then
      extract (DAY from notion_date)
    else
      null
  end::integer as "identificationDay",
  case 
    when notion_date_mask >= 48 then
      extract (MONTH from notion_date)
    else
      null
  end::integer as "identificationMonth",
  case 
    when notion_date_mask >= 32 then
      extract (YEAR from notion_date)
    else
      null
  end::integer as "identificationYear",
  '' as "identificationMethod",
  '' as "identificationHistory",
  '' as "referenceString", -- to check in the future into bibliography
  '' as "identificationNotes", -- to check in the future in case of comments associated to an identification
  '' as "publicationString", -- to check in the future into bibliography
  '' as "urlPicture", -- to check in the future into multimedia
  '' as "externalLink", -- to check in the future into ext_links
  specimen_part as "kindOfUnit",
  "type" as "statusType",
  sex,
  stage as "lifeStage",
  social_status as "socialStatus",
  (coalesce(specimen_count_min,0) + coalesce(specimen_count_max,0))/2 as "totalNumber",
  case
    when lower(sex) = 'male' then
      (coalesce(specimen_count_min,0) + coalesce(specimen_count_max,0))/2
    else
      null
  end::integer as "maleCount",
  case
    when lower(sex) = 'female' then
      (coalesce(specimen_count_min,0) + coalesce(specimen_count_max,0))/2
    else
      null
  end::integer as "femaleCount",
    case
    when lower(sex) IN ('undefined','unknown') then
      (coalesce(specimen_count_min,0) + coalesce(specimen_count_max,0))/2
    else
      null
  end::integer as "sexUnknownCount",
  '' as "specimenProperty_1", -- to check in the future into properties
  '' as "specimenPropertyValue_1",
  '' as "specimenProperty_2",
  '' as "specimenPropertyValue_2",
  '' as "specimenProperty_3",
  '' as "specimenPropertyValue_3",
  '' as "specimenProperty_4",
  '' as "specimenPropertyValue_4",
  '' as "specimenProperty_5",
  '' as "specimenPropertyValue_5",
  '' as "specimenProperty_6",
  '' as "specimenPropertyValue_6",
  '' as "specimenProperty_7",
  '' as "specimenPropertyValue_7",
  '' as "specimenProperty_8",
  '' as "specimenPropertyValue_8",
  '' as "specimenProperty_9",
  '' as "specimenPropertyValue_9",
  '' as "specimenProperty_10",
  '' as "specimenPropertyValue_10",
  '' as "specimenProperty_11",
  '' as "specimenPropertyValue_11",
  '' as "specimenProperty_12",
  '' as "specimenPropertyValue_12",
  '' as "specimenProperty_13",
  '' as "specimenPropertyValue_13",
  '' as "hostClassis",
  '' as "hostOrdo",
  '' as "hostFamilia",
  '' as "hostGenus",
  '' as "hostSpecies",
  '' as "hostAuthor_year",
  '' as "hostRemark",
  '' as "associatedUnitInstitution",
  '' as "associatedUnitCollection",
  '' as "associatedUnitID",
  '' as "associationType",
  '' as "barcode",
  '' as "conservation",
  'Royal Belgian Institute of natural Sciences' as "institutionStorage", -- to be selected from db in case of something else
  building as "buildingStorage",
  "floor" as "floorStorage",
  "room" as "roomStorage",
  "row" as "laneStorage",
  '' as "columnStorage",
  shelf as "shelfStorage",
  container,
  container_type as "containerType",
  container_storage as "containerStorage",
  sub_container,
  sub_container_type as "subcontainerType",
  sub_container_storage as "subcontainerStorage",
  '' as notes
from zzz_rotifer_specimens
inner join codes on codes.referenced_relation = 'zzz_rotifer_specimens' and codes.record_id = zzz_rotifer_specimens.id
inner join gtu on zzz_rotifer_specimens.gtu_ref = gtu.id
left join (
            catalogue_people as donators 
            inner join people as donator_peo on donators.people_ref = donator_peo.id
          ) on zzz_rotifer_specimens.id = donators.record_id and donators.referenced_relation = 'zzz_rotifer_specimens' and donators.people_type = 'donator'
left join identifications on identifications.referenced_relation = 'zzz_rotifer_specimens' and identifications.record_id = zzz_rotifer_specimens.id