CREATE OR REPLACE FUNCTION fct_get_taxonomy_parents_names (IN path TEXT, IN levelSysName VARCHAR DEFAULT '') RETURNS text IMMUTABLE LANGUAGE plpgsql AS $$
DECLARE
  taxonName TEXT := '';
  taxonNameTemp TEXT;
  taxonID VARCHAR;
BEGIN
  FOREACH taxonID IN ARRAY regexp_split_to_array(trim(path, '/'),'/') LOOP
    IF levelSysName = '' THEN
      SELECT name INTO taxonNameTemp FROM taxonomy WHERE id = taxonID::bigint;
      taxonName := taxonName || ' | ' || taxonNameTemp;
    ELSE
      SELECT t.name INTO taxonNameTemp FROM taxonomy t INNER JOIN catalogue_levels cl ON cl.id = t.level_ref and cl.level_type = 'taxonomy' and cl.level_sys_name = levelSysName WHERE t.id = taxonID::bigint;
      IF taxonNameTemp IS NOT NULL AND taxonNameTemp != '' THEN
        taxonName := taxonNameTemp;
        EXIT;
      END IF;
    END IF;
  END LOOP;
  taxonName := trim(taxonName,' | ');
  RETURN taxonName;
EXCEPTION
	WHEN OTHERS THEN
		RETURN NULL;
END;
$$;

CREATE OR REPLACE FUNCTION fct_get_chrono_parents_names (IN path TEXT, IN levelSysName VARCHAR DEFAULT '') RETURNS text IMMUTABLE LANGUAGE plpgsql AS $$
DECLARE
  chronoName TEXT := '';
  chronoNameTemp TEXT;
  chronoID VARCHAR;
BEGIN
  FOREACH chronoID IN ARRAY regexp_split_to_array(trim(path, '/'),'/') LOOP
    IF levelSysName = '' THEN
      SELECT name INTO chronoNameTemp FROM taxonomy WHERE id = chronoID::bigint;
      chronoName := chronoName || ' | ' || chronoNameTemp;
    ELSE
      SELECT t.name INTO chronoNameTemp FROM chronostratigraphy t INNER JOIN catalogue_levels cl ON cl.id = t.level_ref and cl.level_type = 'chronostratigraphy' and cl.level_sys_name = levelSysName WHERE t.id = chronoID::bigint;
      IF chronoNameTemp IS NOT NULL AND chronoNameTemp != '' THEN
        chronoName := chronoNameTemp;
        EXIT;
      END IF;
    END IF;
  END LOOP;
  chronoName := trim(chronoName,' | ');
  RETURN chronoName;
EXCEPTION
	WHEN OTHERS THEN
		RETURN NULL;
END;
$$;

CREATE OR REPLACE FUNCTION fct_get_litho_parents_names (IN path TEXT, IN levelSysName VARCHAR DEFAULT '') RETURNS text IMMUTABLE LANGUAGE plpgsql AS $$
DECLARE
  lithoName TEXT := '';
  lithoNameTemp TEXT;
  lithoID VARCHAR;
BEGIN
  FOREACH lithoID IN ARRAY regexp_split_to_array(trim(path, '/'),'/') LOOP
    IF levelSysName = '' THEN
      SELECT name INTO lithoNameTemp FROM taxonomy WHERE id = lithoID::bigint;
      lithoName := lithoName || ' | ' || lithoNameTemp;
    ELSE
      SELECT t.name INTO lithoNameTemp FROM lithostratigraphy t INNER JOIN catalogue_levels cl ON cl.id = t.level_ref and cl.level_type = 'lithostratigraphy' and cl.level_sys_name = levelSysName WHERE t.id = lithoID::bigint;
      IF lithoNameTemp IS NOT NULL AND lithoNameTemp != '' THEN
        lithoName := lithoNameTemp;
        EXIT;
      END IF;
    END IF;
  END LOOP;
  lithoName := trim(lithoName,' | ');
  RETURN lithoName;
EXCEPTION
        WHEN OTHERS THEN
                RETURN NULL;
END;
$$;

DROP INDEX IF EXISTS idx_specimen_collection_path;
DROP INDEX IF EXISTS idx_taxonomy_path_names;
DROP INDEX IF EXISTS idx_taxonomy_path_kingdom;
DROP INDEX IF EXISTS idx_taxonomy_path_phylum;
DROP INDEX IF EXISTS idx_taxonomy_path_class;
DROP INDEX IF EXISTS idx_taxonomy_path_order;
DROP INDEX IF EXISTS idx_taxonomy_path_family;
DROP INDEX IF EXISTS idx_taxonomy_path_genus;
DROP INDEX IF EXISTS idx_taxonomy_path_sub_genus;
DROP INDEX IF EXISTS idx_chrono_path_names;
DROP INDEX IF EXISTS idx_chrono_path_eon;
DROP INDEX IF EXISTS idx_chrono_path_era;
DROP INDEX IF EXISTS idx_chrono_path_system;
DROP INDEX IF EXISTS idx_chrono_path_serie;
DROP INDEX IF EXISTS idx_chrono_path_stage;
DROP INDEX IF EXISTS idx_litho_path_supergroup;
DROP INDEX IF EXISTS idx_litho_path_group;
DROP INDEX IF EXISTS idx_litho_path_formation;
DROP INDEX IF EXISTS idx_litho_path_member;
DROP INDEX IF EXISTS idx_litho_path_layer;
DROP INDEX IF EXISTS idx_litho_path_subLevel1;
DROP INDEX IF EXISTS idx_litho_path_subLevel2;
DROP INDEX IF EXISTS idx_user_tracking_for_bbif_export;
DROP INDEX IF EXISTS idx_basisOfRecord;
DROP INDEX IF EXISTS idx_datasetName;
CREATE INDEX idx_user_tracking_for_bbif_export ON users_tracking(referenced_relation, record_id, modification_date_time);
create index idx_datasetName ON specimens(collection_name);
CREATE INDEX idx_specimen_collection_path ON specimens(collection_path);
CREATE INDEX idx_basisOfRecord ON specimens ((CASE WHEN collection_ref = 3 OR collection_path LIKE '/3/%' THEN 'FossilSpecimen' WHEN collection_ref = 231 OR collection_path LIKE '/231/%' THEN 'GeologicalContext' ELSE 'PreservedSpecimen' END), collection_name);
CREATE INDEX idx_taxonomy_path_names ON specimens(fct_get_taxonomy_parents_names(taxon_path));
CREATE INDEX idx_taxonomy_path_kingdom ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'kingdom'));
CREATE INDEX idx_taxonomy_path_phylum ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'phylum'));
CREATE INDEX idx_taxonomy_path_class ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'class'));
CREATE INDEX idx_taxonomy_path_order ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'order'));
CREATE INDEX idx_taxonomy_path_family ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'family'));
CREATE INDEX idx_taxonomy_path_genus ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'genus'));
CREATE INDEX idx_taxonomy_path_sub_genus ON specimens(fct_get_taxonomy_parents_names(taxon_path, 'sub_genus'));
CREATE INDEX idx_chrono_path_names ON specimens(fct_get_chrono_parents_names(chrono_path)) WHERE chrono_ref != 0;
CREATE INDEX idx_chrono_path_eon ON specimens(fct_get_chrono_parents_names(chrono_path, 'eon')) WHERE chrono_ref != 0;
CREATE INDEX idx_chrono_path_era ON specimens(fct_get_chrono_parents_names(chrono_path, 'era')) WHERE chrono_ref != 0;
CREATE INDEX idx_chrono_path_system ON specimens(fct_get_chrono_parents_names(chrono_path, 'system')) WHERE chrono_ref != 0;
CREATE INDEX idx_chrono_path_serie ON specimens(fct_get_chrono_parents_names(chrono_path, 'serie')) WHERE chrono_ref != 0;
CREATE INDEX idx_chrono_path_stage ON specimens(fct_get_chrono_parents_names(chrono_path, 'stage')) WHERE chrono_ref != 0;
CREATE INDEX idx_litho_path_supergroup ON specimens(fct_get_litho_parents_names(litho_path, 'supergroup')) WHERE litho_ref != 0;
CREATE INDEX idx_litho_path_group ON specimens(fct_get_litho_parents_names(litho_path, 'group')) WHERE litho_ref != 0;
CREATE INDEX idx_litho_path_formation ON specimens(fct_get_litho_parents_names(litho_path, 'formation')) WHERE litho_ref != 0;
CREATE INDEX idx_litho_path_member ON specimens(fct_get_litho_parents_names(litho_path, 'member')) WHERE litho_ref != 0;
CREATE INDEX idx_litho_path_layer ON specimens(fct_get_litho_parents_names(litho_path, 'layer')) WHERE litho_ref != 0;
CREATE INDEX idx_litho_path_subLevel1 ON specimens(fct_get_litho_parents_names(litho_path, 'sub_level_1')) WHERE litho_ref != 0;
CREATE INDEX idx_litho_path_subLevel2 ON specimens(fct_get_litho_parents_names(litho_path, 'sub_level_2')) WHERE litho_ref != 0;

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
    /*WHEN collection_ref = 231 OR collection_path LIKE '/231/%' THEN
      'GeologicalContext'*/
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
  'urn:catalog:RBINS:'||translate(collection_code,'\/.()- ë', '_______e')||':'||id as "occurrenceID",
  coalesce((select array_to_string(array_agg(trim((case when code_prefix is not null and code_prefix != '' then case when code_prefix_separator is not null and code_prefix_separator != '' then code_prefix || code_prefix_separator else code_prefix end else '' end) || code || (case when code_suffix is not null and code_suffix != '' then case when code_suffix_separator is not null and code_suffix_separator != '' then code_suffix_separator || code_suffix else code_suffix end else '' end))),' | ') from codes where referenced_relation = 'specimens' and record_id = sp.id and code_category = 'main'),'') as "catalogNumber",
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
  trim(
   case
     when chrono_ref is not null and chrono_ref != 0 then
       'chronoID-'||chrono_ref
     else ''
   end ||
   '|' ||
   case
     when litho_ref is not null and litho_ref != 0 then
       'lithoID-'||litho_ref
     else ''
   end,
   '|'
  )::text as "geologicalContextID",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'eon' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'eon')) 
      END
  end::text as "earliestEonOrLowestEonothem",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'eon' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'eon')) 
      END
  end::text as "latestEonOrHighestEonothem",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'era' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'era')) 
      END
  end::text as "earliestEraOrLowestErathem",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'era' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'era')) 
      END
  end::text as "latestEraOrHighestErathem",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'system' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'system')) 
      END
  end::text as "earliestPeriodOrLowestSystem",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'system' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'system')) 
      END
  end::text as "latestPeriodOrHighestSystem",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'serie' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'serie')) 
      END
  end::text as "earliestEpochOrLowestSeries",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'serie' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'serie')) 
      END
  end::text as "latestEpochOrHighestSeries",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'stage' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'stage')) 
      END
  end::text as "earliestAgeOrLowestStage",
  case
    when chrono_ref is null or chrono_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.chrono_level_ref and level_type = 'chronostratigraphy') = 'stage' THEN chrono_name 
        ELSE (SELECT fct_get_chrono_parents_names(chrono_path, 'stage')) 
      END
  end::text as "latestAgeOrHighestStage",
  case
    when litho_ref is null or litho_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.litho_level_ref and level_type = 'lithostratigraphy') IN ('supergroup','group') THEN litho_name 
        ELSE coalesce((SELECT fct_get_litho_parents_names(litho_path, 'group')), (SELECT fct_get_litho_parents_names(litho_path, 'supergroup')))
      END
  end::text as "group",
  case
    when litho_ref is null or litho_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.litho_level_ref and level_type = 'lithostratigraphy') = 'formation' THEN litho_name 
        ELSE (SELECT fct_get_litho_parents_names(litho_path, 'formation'))
      END
  end::text as "formation",
  case
    when litho_ref is null or litho_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.litho_level_ref and level_type = 'lithostratigraphy') = 'member' THEN litho_name 
        ELSE (SELECT fct_get_litho_parents_names(litho_path, 'member')) 
      END
  end::text as "member",
  case
    when litho_ref is null or litho_ref = 0 then
      ''
    else
      CASE 
        WHEN (select level_sys_name from catalogue_levels WHERE id = sp.litho_level_ref and level_type = 'lithostratigraphy') IN ('layer', 'sub_level_1', 'sub_level_2') THEN litho_name 
        ELSE coalesce(coalesce((SELECT fct_get_litho_parents_names(litho_path, 'layer')),(SELECT fct_get_litho_parents_names(litho_path, 'sub_level_1'))),(SELECT fct_get_litho_parents_names(litho_path, 'sub_level_2')))
      END
  end::text as "bed",
  (select id from identifications where referenced_relation = 'specimens' and record_id = sp.id and notion_concerned in ('all', 'taxonomy', 'lithology', 'mineralogy') order by case when notion_date_mask != 0 then notion_date else '0001-01-01'::timestamp end desc, id limit 1)::bigint as "identificationID",
  coalesce((select substring(value_defined from position('aff.' in value_defined)) from identifications where referenced_relation = 'specimens' and record_id = sp.id and notion_concerned in ('all', 'taxonomy', 'lithology', 'mineralogy') and position('aff.' in value_defined) != 0 order by case when notion_date_mask != 0 then notion_date else '0001-01-01'::timestamp end desc, id limit 1),(select substring(value_defined from position('cf.' in value_defined)) from identifications where referenced_relation = 'specimens' and record_id = sp.id and notion_concerned in ('all', 'taxonomy', 'lithology', 'mineralogy') and position('cf.' in value_defined) != 0 order by case when notion_date_mask != 0 then notion_date else '0001-01-01'::timestamp end desc, id limit 1))::text as "identificationQualifier",
  case 
    when sp.type != 'specimen' then
      (sp.type || coalesce((select ' of '|| value_defined from identifications where referenced_relation = 'specimens' and record_id = sp.id and notion_concerned in ('all', 'taxonomy', 'lithology', 'mineralogy') and trim(value_defined) != '-' order by case when notion_date_mask != 0 then notion_date else '0001-01-01'::timestamp end desc, id limit 1),''))::text
    else ''
  end as "typeStatus",
  (select array_to_string(array_agg(formated_name), ' | ') from people p inner join catalogue_people cp on p.id = cp.people_ref where referenced_relation = 'identifications' and record_id in (select id from identifications where referenced_relation = 'specimens' and record_id = sp.id and notion_concerned in ('all', 'taxonomy', 'lithology', 'mineralogy') order by case when notion_date_mask != 0 then notion_date else '0001-01-01'::timestamp end desc, id limit 1))::text as "identifiedBy",
  (
  select 
  case
    when notion_date_mask in (32,48,56,60,62,63) then
      case
       when notion_date_mask <= 56 then
         case
           when notion_date_mask = 32 then
             to_char(notion_date, 'YYYY')
           when notion_date_mask = 48 then
             to_char(notion_date, 'YYYY-MM')
           when notion_date_mask = 56 then
             to_char(notion_date, 'YYYY-MM-DD')
         end
       else
         (notion_date at time zone 'CET')::text
      end
    else
      ''
  end
  from identifications 
  where referenced_relation = 'specimens' and record_id = sp.id and notion_concerned in ('all', 'taxonomy', 'lithology', 'mineralogy') order by case when notion_date_mask != 0 then notion_date else '0001-01-01'::timestamp end desc, id limit 1
  )::text as "dateIdentified",
  (
    select determination_status
    from identifications 
    where referenced_relation = 'specimens' and record_id = sp.id and notion_concerned in ('all', 'taxonomy', 'lithology', 'mineralogy') and determination_status != '' and determination_status is not null order by case when notion_date_mask != 0 then notion_date else '0001-01-01'::timestamp end desc, id limit 1
  )::text as "identificationVerificationStatus",
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
  trim(COALESCE((select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'AuthorTeamOriginalAndYear' LIMIT 1),COALESCE((select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'AuthorTeamAndYear' LIMIT 1),COALESCE((select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'AuthorTeamParenthesisAndYear' LIMIT 1),(select keyword FROM classification_keywords WHERE referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and keyword_type = 'AuthorTeam' LIMIT 1))))) as "scientificNameAuthorship",
  COALESCE((select name from vernacular_names where referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and community = 'English' LIMIT 1),'')::text as "vernacularName",
  'ICZN'::text as "nomenclaturalCode",
  taxon_status as "taxonomicStatus",
  (select array_to_string(array_agg(notion_concerned || ': ' || comment),E'\n') from comments where referenced_relation = 'taxonomy' and record_id = sp.taxon_ref and comment is not null and comment != '')::text as "taxonRemarks"
INTO TABLE zzz_exportDwC
from specimens as sp
where collection_is_public = true
  and collection_ref != 231
  and collection_path NOT LIKE '/231/%'
order by "basisOfRecord", "datasetName";
