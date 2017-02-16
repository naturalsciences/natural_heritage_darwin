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
  gtu_ref as "eventID",
  gtu_code as "fieldNumber",
  case
    when gtu_from_date_mask in (32,48,56,60,62,63) then
      case
       when gtu_from_date_mask <= 56 then
         case
           when gtu_from_date_mask = 32 then
             to_char(gtu_from_date, 'YYYY')
           when gtu_from_date_mask = 48 then
             to_char(gtu_from_date, 'YYYY-MM')
           when gtu_from_date_mask = 56 then
             to_char(gtu_from_date, 'YYYY-MM-DD')
         end
       else
         (gtu_from_date at time zone 'CET')::text
      end
    else
      ''
  end::text ||
  case
    when gtu_from_date_mask in (32,48,56,60,62,63) and gtu_to_date_mask in (32,48,56,60,62,63) then
      '/' ||
      case
       when gtu_to_date_mask <= 56 then
         case
           when gtu_to_date_mask = 32 then
             to_char(gtu_to_date, 'YYYY')
           when gtu_to_date_mask = 48 then
             to_char(gtu_to_date, 'YYYY-MM')
           when gtu_to_date_mask = 56 then
             to_char(gtu_to_date, 'YYYY-MM-DD')
         end
       else
         (gtu_to_date at time zone 'CET')::text
      end
    else
      ''
  end::text as "eventDate",
  case
    when gtu_from_date_mask in (32,48,56,60,62,63) then
      case 
        when gtu_to_date_mask in (32,48,56,60,62,63) then
          case
            when extract(year from gtu_from_date) = extract(year from gtu_to_date) then
              extract(year from gtu_from_date)
            else
              null
          end
        else
          extract(year from gtu_from_date)
      end
    else
      null
  end::integer as "year",
  case
    when gtu_from_date_mask in (48,56,60,62,63) then
      case 
        when gtu_to_date_mask in (48,56,60,62,63) then
          case
            when extract(year from gtu_from_date) = extract(year from gtu_to_date) and extract(month from gtu_from_date) = extract(month from gtu_to_date) then
              extract(month from gtu_from_date)
            else
              null
          end
        else
          extract(month from gtu_from_date)
      end
    else
      null
  end::integer as "month",
  case
    when gtu_from_date_mask in (56,60,62,63) then
      case 
        when gtu_to_date_mask in (56,60,62,63) then
          case
            when extract(year from gtu_from_date) = extract(year from gtu_to_date) and extract(month from gtu_from_date) = extract(month from gtu_to_date) and extract(day from gtu_from_date) = extract(day from gtu_to_date) then
              extract(day from gtu_from_date)
            else
              null
          end
        else
          extract(day from gtu_from_date)
      end
    else
      null
  end::integer as "day",
  coalesce((select method from specimen_collecting_methods scm inner join collecting_methods cm on cm.id = scm.collecting_method_ref where specimen_ref = sp.id limit 1),'')::text as "samplingProtocol",
  case
    when station_visible = true then
      (select array_to_string(array_agg(notion_concerned || ': ' || comment),E'\n') from comments where referenced_relation = 'gtu' and record_id = gtu_ref and comment is not null and comment != '' and notion_concerned not in ('position information', 'exact_site'))
    else
      null
  end::text as "eventRemarks",
  gtu_ref as "locationID",
  coalesce((select tag from tags where gtu_ref = sp.gtu_ref and sub_group_type IN ('continent', 'Continent') limit 1),'')::text as "continent",
  case when station_visible = true then coalesce((select tag || ' ('|| sub_group_type ||')' from tags where gtu_ref = sp.gtu_ref and group_type = 'hydrographic' limit 1),'') else '' end::text as "waterBody",
  case when station_visible = true then coalesce((select tag from tags where gtu_ref = sp.gtu_ref and sub_group_type in ('archipel', 'archipelago', 'Archipelago', 'islands', 'volcanic archipelago') limit 1),'') else '' end::text as "islandGroup",
  case when station_visible = true then coalesce((select tag from tags where gtu_ref = sp.gtu_ref and sub_group_type in ('island', 'Island', 'island station', 'summit of the island ; sommet de l''île') limit 1),'') else '' end::text as "island",
  coalesce((select tag from tags where gtu_ref = sp.gtu_ref and sub_group_type in ('Autonomous constituent country', 'autonomous country', 'constituent country', 'country', 'Country', 'historical country', 'island country', 'Island country') limit 1),'')::text as "country",
  case when station_visible = true then coalesce((select tag from tags where gtu_ref = sp.gtu_ref and sub_group_type in ('province', 'Province', 'administrative region', 'autonomous region', 'Bundesländ; deelstaat; state', 'canton', 'Department', 'department - département', 'region', 'Region', 'gewest ; communauté ; region', 'Historical region', 'historic state', 'overseas region', 'regional unit', 'Region or district', 'state', 'State or province', 'State or territory', 'subregion', 'Landkreis ; provincie ; district ; comté', 'kreis; district; arrondissement', 'legal district', 'district', 'District', 'arrondissement', 'first order administrative division', 'second order administrative division') limit 1),'') else '' end::text as "stateProvince",
  case when station_visible = true then coalesce((select tag from tags where gtu_ref = sp.gtu_ref and sub_group_type in ('county', 'County', 'Sub-County, Constituency') limit 1),'') else '' end::text as "county",
  case when station_visible = true then coalesce((select tag from tags where gtu_ref = sp.gtu_ref and sub_group_type in ('City', 'commune', 'commune à facilités; faciliteitengemeente', 'dorp ; village', 'gemeente/commune', 'Gemeinde ; gemeenschap ; municipality ; communauté', 'Gemeinde ; municipality', 'gehucht/hameau/hamlet', 'Hameau', 'hamlet', 'hamlet/hameau', 'municipality', 'Municipality', 'petite commune/deelgemeente', 'stad ; ville ; town', 'village', 'Ville métropolitaine', 'capital', 'administrative division', 'populated place', 'town') limit 1),'') else '' end::text as "municipality",
  case when station_visible = true then coalesce((select array_to_string(array_agg(tag),' | ') from tags where gtu_ref = sp.gtu_ref and group_type in ('populated', 'administrative area') and sub_group_type not in ('City', 'commune', 'commune à facilités; faciliteitengemeente', 'dorp ; village', 'gemeente/commune', 'Gemeinde ; gemeenschap ; municipality ; communauté', 'Gemeinde ; municipality', 'gehucht/hameau/hamlet', 'Hameau', 'hamlet', 'hamlet/hameau', 'municipality', 'Municipality', 'petite commune/deelgemeente', 'stad ; ville ; town', 'village', 'Ville métropolitaine', 'county', 'County', 'Sub-County, Constituency', 'province', 'Province', 'administrative region', 'autonomous region', 'Bundesländ; deelstaat; state', 'canton', 'Department', 'department - département', 'region', 'Region', 'gewest ; communauté ; region', 'Historical region', 'historic state', 'overseas region', 'regional unit', 'Region or district', 'state', 'State or province', 'State or territory', 'subregion', 'Landkreis ; provincie ; district ; comté', 'kreis; district; arrondissement', 'legal district', 'district', 'District', 'Autonomous constituent country', 'autonomous country', 'constituent country', 'country', 'Country', 'historical country', 'island country', 'Island country', 'administrative division', 'capital', 'populated place', 'town', 'arrondissement', 'independent political entity', 'first order administrative division', 'second order administrative division', 'continent', 'Continent')),'') else '' end::text as "locality",
  case when station_visible = true then
  case
    when gtu_elevation is not null and gtu_elevation > 0 then
      case when gtu_elevation_accuracy is not null and gtu_elevation_accuracy != 0 then
         case
           when (gtu_elevation - gtu_elevation_accuracy) < 0 then
             gtu_elevation
           else
             (gtu_elevation - gtu_elevation_accuracy)
         end
      else
        gtu_elevation
      end
    else
      null
  end
  else
    null
  end::float as "minimumElevationInMeters",
  case when station_visible = true then
    case
    when gtu_elevation is not null and gtu_elevation > 0 then
      case when gtu_elevation_accuracy is not null and gtu_elevation_accuracy != 0 then
         case
           when (gtu_elevation - gtu_elevation_accuracy) < 0 then
             gtu_elevation
           else
             (gtu_elevation + gtu_elevation_accuracy)
         end
      else
        gtu_elevation
      end
    else
      null
  end
  else
    null
  end::float as "maximumElevationInMeters",
  case when station_visible = true then
  case
    when gtu_elevation is not null and gtu_elevation < 0 then
      case when gtu_elevation_accuracy is not null and gtu_elevation_accuracy != 0 then
         case
           when (gtu_elevation + gtu_elevation_accuracy) > 0 then
             gtu_elevation
           else
             (gtu_elevation - gtu_elevation_accuracy)
         end
      else
        gtu_elevation
      end
    else
      null
  end
  else
    null
  end::float as "minimumDepthInMeters",
  case when station_visible = true then
    case
    when gtu_elevation is not null and gtu_elevation < 0 then
      case when gtu_elevation_accuracy is not null and gtu_elevation_accuracy != 0 then
         case
           when (gtu_elevation + gtu_elevation_accuracy) > 0 then
             gtu_elevation
           else
             (gtu_elevation + gtu_elevation_accuracy)
         end
      else
        gtu_elevation
      end
    else
      null
  end
  else
    null
  end::float as "maximumDepthInMeters",
    case
    when station_visible = true then
      (select array_to_string(array_agg(notion_concerned || ': ' || comment),E'\n') from comments where referenced_relation = 'gtu' and record_id = gtu_ref and comment is not null and comment != '' and notion_concerned in ('position information', 'exact_site'))
    else
      null
  end::text as "locationRemarks",
  case when station_visible = true then (select latitude from gtu where id = sp.gtu_ref) else null end::float as "decimalLatitude",
  case when station_visible = true then (select longitude from gtu where id = sp.gtu_ref) else null end::float as "decimalLongitude",
  case when station_visible = true then 'WGS84' else null end::text as "geodeticDatum",
  (select array_to_string(array_agg(p.formated_name), ' | ') from catalogue_people cp inner join people p on cp.people_ref = p.id where referenced_relation = 'specimens' and record_id = sp.id and cp.people_type = 'collector')::text as "georeferencedBy",
  
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
  (SELECT fct_get_taxonomy_parents_names(taxon_path)) as "higherClassification",
  CASE WHEN (select level_sys_name from catalogue_levels WHERE id = sp.taxon_level_ref and level_type = 'taxonomy') = 'kingdom' THEN taxon_name ELSE (SELECT fct_get_taxonomy_parents_names(taxon_path, 'kingdom')) END as "kingdom",
  CASE WHEN (select level_sys_name from catalogue_levels WHERE id = sp.taxon_level_ref and level_type = 'taxonomy') = 'phylum' THEN taxon_name ELSE (SELECT fct_get_taxonomy_parents_names(taxon_path, 'phylum')) END as "phylum",
  CASE WHEN (select level_sys_name from catalogue_levels WHERE id = sp.taxon_level_ref and level_type = 'taxonomy') = 'class' THEN taxon_name ELSE (SELECT fct_get_taxonomy_parents_names(taxon_path, 'class')) END as "class",
  CASE WHEN (select level_sys_name from catalogue_levels WHERE id = sp.taxon_level_ref and level_type = 'taxonomy') = 'order' THEN taxon_name ELSE (SELECT fct_get_taxonomy_parents_names(taxon_path, 'order')) END as "order",
  CASE WHEN (select level_sys_name from catalogue_levels WHERE id = sp.taxon_level_ref and level_type = 'taxonomy') = 'family' THEN taxon_name ELSE (SELECT fct_get_taxonomy_parents_names(taxon_path, 'family')) END as "family",
  CASE WHEN (select level_sys_name from catalogue_levels WHERE id = sp.taxon_level_ref and level_type = 'taxonomy') = 'genus' THEN taxon_name ELSE (SELECT fct_get_taxonomy_parents_names(taxon_path, 'genus')) END as "genus",
  CASE WHEN (select level_sys_name from catalogue_levels WHERE id = sp.taxon_level_ref and level_type = 'taxonomy') = 'sub_genus' THEN taxon_name ELSE (SELECT fct_get_taxonomy_parents_names(taxon_path, 'sub_genus')) END as "subgenus",
  COALESCE((select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'SpeciesEpithet' LIMIT 1), '')::text as "specificEpithet",
  COALESCE((select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'InfraspecificEpithet' LIMIT 1), (select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'SubspeciesEpithet' LIMIT 1))::text as "infraspecificEpithet",
  taxon_level_name as "taxonRank",
  trim(COALESCE((select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'AuthorTeamOriginalAndYear' LIMIT 1),COALESCE((select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'AuthorTeamAndYear' LIMIT 1),COALESCE((select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'AuthorTeamParenthesisAndYear' LIMIT 1),(select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'AuthorTeam' LIMIT 1))))) as "scientificNameAutorship",
  COALESCE((select name from vernacular_names where referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and community = 'English' LIMIT 1),'')::text as "vernacularName",
  'ICZN'::text as "nomenclaturalCode",
  taxon_status as "taxonomicStatus",
  (select array_to_string(array_agg(notion_concerned || ': ' || comment),E'\n') from comments where referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and comment is not null and comment != '')::text as "taxonRemarks"
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
--and gtu_to_date_mask > 56      
--and (select count(*) from comments where referenced_relation = 'gtu' and record_id = sp.gtu_ref) >= 1
--and station_visible = false
order by "basisOfRecord","datasetName"
limit 5000
;
select * from zzz_exportDwC;
