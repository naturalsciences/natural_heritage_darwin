/***
* Trigger Function fct_clr_incrementMainCode
* Automaticaly add a incremented "main" code for a specimen
* When the collection of the specimen has the flag must_be_incremented
*/
CREATE OR REPLACE FUNCTION fct_clr_incrementMainCode() RETURNS trigger
as $$
DECLARE
	last_line specimens_codes%ROWTYPE;
	must_be_incremented collections.code_auto_increment%TYPE;
BEGIN
	SELECT collections.code_auto_increment INTO must_be_incremented FROM collections WHERE collections.id = NEW.collection_ref;
	IF must_be_incremented = true THEN
		SELECT * INTO last_line FROM specimens_codes WHERE code_category = 'main' AND specimen_ref=NEW.id;
		IF FOUND THEN
			RETURN NEW;
 		END IF;
 
 		SELECT specimens_codes.* into last_line FROM specimens_codes
				INNER JOIN specimens ON specimens_codes.specimen_ref = specimens.id
				WHERE specimens.collection_ref =  NEW.collection_ref
					AND code_category = 'main'
					ORDER BY specimens_codes.code DESC
					LIMIT 1;
		IF NOT FOUND THEN
			last_line.code := 0;
			last_line.code_category := 'main';
		END IF;
		
		last_line.code := last_line.code+1;
		INSERT INTO specimens_codes (specimen_ref, code_category, code_prefix, code, code_suffix)
			VALUES (NEW.id, 'main', last_line.code_prefix, last_line.code, last_line.code_suffix );
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;


/***
* Trigger Function fct_cpy_specimensMainCode
* Automaticaly copy the "main" code from the specimen to the specimen parts
* When the collection of the specimen has the flag code_part_code_auto_copy
*/
CREATE OR REPLACE FUNCTION fct_cpy_specimensMainCode() RETURNS trigger
as $$
DECLARE
	spec_code specimens_codes%ROWTYPE;
	must_be_copied collections.code_part_code_auto_copy%TYPE;
BEGIN
	SELECT collections.code_part_code_auto_copy INTO must_be_copied FROM collections 
			INNER JOIN specimens ON collections.id = specimens.collection_ref
			INNER JOIN specimen_individuals ON specimen_individuals.specimen_ref=specimens.id
				WHERE specimen_individuals.id = NEW.specimen_individual_ref;
	
	IF must_be_copied = true THEN
		SELECT specimens_codes.* into spec_code FROM specimens_codes
			INNER JOIN specimens ON specimens_codes.specimen_ref = specimens.id
			INNER JOIN specimen_individuals ON specimen_individuals.specimen_ref=specimens.id
			WHERE specimen_individuals.id = NEW.specimen_individual_ref
				AND code_category = 'main'
				ORDER BY specimens_codes.code DESC
					LIMIT 1;
		IF FOUND THEN
			INSERT INTO specimen_parts_codes (specimen_part_ref, code_category, code_prefix, code, code_suffix)
					VALUES (NEW.id, 'main', spec_code.code_prefix, spec_code.code , spec_code.code_suffix );
		END IF;
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;

/***
* Trigger Function fct_cpy_idToCode
* Automaticaly copy the code form the id if the code is null
*/
CREATE OR REPLACE FUNCTION fct_cpy_idToCode() RETURNS trigger
AS $$
BEGIN
	IF NEW.code is null THEN
		NEW.code := NEW.id;
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;

/***
* Function fct_chk_one_pref_language
* Check if there is only ONE prefered language for a user
*/
CREATE OR REPLACE FUNCTION fct_chk_one_pref_language(person people_languages.people_ref%TYPE, prefered people_languages.prefered_language%TYPE, table_prefix varchar) returns boolean
as $$
DECLARE
	response boolean default false;
	prefix varchar default coalesce(table_prefix, 'people');
        tabl varchar default prefix || '_languages';
	tableExist boolean default false;
BEGIN
	select count(*)::integer::boolean into tableExist from pg_tables where schemaname = 'darwin2' and tablename = tabl;
	IF tableExist THEN
	        IF prefered THEN
			EXECUTE 'select not count(*)::integer::boolean from ' || quote_ident(tabl) || ' where ' || quote_ident(prefix || '_ref') || ' = ' || $1 || ' and prefered_language = ' || $2 INTO response;
		ELSE
			response := true;
		END IF;
	END IF;
	return response;
END;
$$ LANGUAGE plpgsql;

/***
* Trigger Function fct_chk_one_pref_language
* Trigger that call the fct_chk_one_pref_language fct
*/
CREATE OR REPLACE FUNCTION fct_chk_one_pref_language(person people_languages.people_ref%TYPE, prefered people_languages.prefered_language%TYPE) returns boolean
as $$
DECLARE
        response boolean default false;
BEGIN
	response := fct_chk_one_pref_language(person, prefered, 'people');
	return response;
END;
$$ LANGUAGE plpgsql;


/***
* Function fullToIndex
* Remove all the accents special chars from a string
*/
CREATE OR REPLACE FUNCTION fullToIndex(to_indexed varchar) RETURNS varchar STRICT
AS $$
DECLARE
	temp_string varchar;
BEGIN
    -- Investigate https://launchpad.net/postgresql-unaccent
    temp_string := REPLACE(to_indexed, 'Œ', 'oe');
    temp_string := REPLACE(temp_string, 'Ӕ', 'ae');
    temp_string := REPLACE(temp_string, 'œ', 'oe');
    temp_string := REPLACE(temp_string, 'æ', 'ae');
    temp_string := TRANSLATE(temp_string,'Ð','d');
	temp_string := LOWER(
				public.to_ascii(
					CONVERT_TO(temp_string, 'iso-8859-15'),
					'iso-8859-15')
				);
	--Remove ALL none alphanumerical char
	temp_string := regexp_replace(temp_string,'[^[:alnum:]]','', 'g');
	return substring(temp_string from 0 for 40);
END;
$$ LANGUAGE plpgsql;

/***
* Trigger function fct_cpy_hierarchy_from_parents
* Version of function used to check what's coming from parents and what's coming from unit passed itself
*/
CREATE OR REPLACE FUNCTION fct_get_hierarchy_from_parents(table_name varchar, id integer) RETURNS RECORD
AS $$
DECLARE
	level_sys_name catalogue_levels.level_sys_name%TYPE;
	level_ref  template_classifications.level_ref%TYPE;
	name_indexed template_classifications.name_indexed%TYPE;
	parent_ref template_classifications.parent_ref%TYPE;
	result RECORD;
BEGIN

	EXECUTE 'SELECT level_ref, name_indexed, parent_ref FROM ' || quote_ident(table_name) || ' WHERE id = ' || id INTO level_ref, name_indexed, parent_ref;
	SELECT cl.level_sys_name INTO level_sys_name FROM catalogue_levels as cl WHERE cl.id = level_ref;
	IF table_name = 'chronostratigraphy' THEN
		SELECT 
			CASE
				WHEN level_sys_name = 'eon' THEN
					id
				ELSE
					pc.eon_ref
			END AS eon_ref,
			CASE
				WHEN level_sys_name = 'eon' THEN
					name_indexed
				ELSE
					pc.eon_indexed
			END AS eon_indexed,
			CASE
				WHEN level_sys_name = 'era' THEN
					id
				ELSE
					pc.era_ref
			END AS era_ref,
			CASE
				WHEN level_sys_name = 'era' THEN
					name_indexed
				ELSE
					pc.era_indexed
			END AS era_indexed,
			CASE
				WHEN level_sys_name = 'sub_era' THEN
					id
				ELSE
					pc.sub_era_ref
			END AS sub_era_ref,
			CASE
				WHEN level_sys_name = 'sub_era' THEN
					name_indexed
				ELSE
					pc.sub_era_indexed
			END AS sub_era_indexed,
			CASE
				WHEN level_sys_name = 'system' THEN
					id
				ELSE
					pc.system_ref
			END AS system_ref,
			CASE
				WHEN level_sys_name = 'system' THEN
					name_indexed
				ELSE
					pc.system_indexed
			END AS system_indexed,
			CASE
				WHEN level_sys_name = 'serie' THEN
					id
				ELSE
					pc.serie_ref
			END AS serie_ref,
			CASE
				WHEN level_sys_name = 'serie' THEN
					name_indexed
				ELSE
					pc.serie_indexed
			END AS serie_indexed,
			CASE
				WHEN level_sys_name = 'stage' THEN
					id
				ELSE
					pc.stage_ref
			END AS stage_ref,
			CASE
				WHEN level_sys_name = 'stage' THEN
					name_indexed
				ELSE
					pc.stage_indexed
			END AS stage_indexed,
			CASE
				WHEN level_sys_name = 'sub_stage' THEN
					id
				ELSE
					pc.sub_stage_ref
			END AS sub_stage_ref,
			CASE
				WHEN level_sys_name = 'sub_stage' THEN
					name_indexed
				ELSE
					pc.sub_stage_indexed
			END AS sub_stage_indexed,
			CASE
				WHEN level_sys_name = 'sub_level_1' THEN
					id
				ELSE
					pc.sub_level_1_ref
			END AS sub_level_1_ref,
			CASE
				WHEN level_sys_name = 'sub_level_1' THEN
					name_indexed
				ELSE
					pc.sub_level_1_indexed
			END AS sub_level_1_indexed,
			CASE
				WHEN level_sys_name = 'sub_level_2' THEN
					id
				ELSE
					pc.sub_level_2_ref
			END AS sub_level_2_ref,
			CASE
				WHEN level_sys_name = 'sub_level_2' THEN
					name_indexed
				ELSE
					pc.sub_level_2_indexed
			END AS sub_level_2_indexed
		INTO 
			result
		FROM chronostratigraphy AS pc
		WHERE pc.id = parent_ref;
	ELSIF table_name = 'lithostratigraphy' THEN
		SELECT 
			CASE
				WHEN level_sys_name = 'group' THEN
					id
				ELSE
					pl.group_ref
			END AS group_ref,
			CASE
				WHEN level_sys_name = 'group' THEN
					name_indexed
				ELSE
					pl.group_indexed
			END AS group_indexed,
			CASE
				WHEN level_sys_name = 'formation' THEN
					id
				ELSE
					pl.formation_ref
			END AS formation_ref,
			CASE
				WHEN level_sys_name = 'formation' THEN
					name_indexed
				ELSE
					pl.formation_indexed
			END AS formation_indexed,
			CASE
				WHEN level_sys_name = 'member' THEN
					id
				ELSE
					pl.member_ref
			END AS member_ref,
			CASE
				WHEN level_sys_name = 'member' THEN
					name_indexed
				ELSE
					pl.member_indexed
			END AS member_indexed,
			CASE
				WHEN level_sys_name = 'layer' THEN
					id
				ELSE
					pl.layer_ref
			END AS layer_ref,
			CASE
				WHEN level_sys_name = 'layer' THEN
					name_indexed
				ELSE
					pl.layer_indexed
			END AS layer_indexed,
			CASE
				WHEN level_sys_name = 'sub_level_1' THEN
					id
				ELSE
					pl.sub_level_1_ref
			END AS sub_level_1_ref,
			CASE
				WHEN level_sys_name = 'sub_level_1' THEN
					name_indexed
				ELSE
					pl.sub_level_1_indexed
			END AS sub_level_1_indexed,
			CASE
				WHEN level_sys_name = 'sub_level_2' THEN
					id
				ELSE
					pl.sub_level_2_ref
			END AS sub_level_2_ref,
			CASE
				WHEN level_sys_name = 'sub_level_2' THEN
					name_indexed
				ELSE
					pl.sub_level_2_indexed
			END AS sub_level_2_indexed
		INTO 
			result
		FROM lithostratigraphy AS pl
		WHERE pl.id = parent_ref;
	ELSIF table_name = 'lithology' THEN
		
	ELSIF table_name = 'mineralogy' THEN
		SELECT 
			CASE
				WHEN level_sys_name = 'unit_class' THEN
					id
				ELSE
					pm.unit_class_ref
			END AS unit_class_ref,
			CASE
				WHEN level_sys_name = 'unit_class' THEN
					name_indexed
				ELSE
					pm.unit_class_indexed
			END AS unit_class_indexed,
			CASE
				WHEN level_sys_name = 'unit_division' THEN
					id
				ELSE
					pm.unit_division_ref
			END AS unit_division_ref,
			CASE
				WHEN level_sys_name = 'unit_division' THEN
					name_indexed
				ELSE
					pm.unit_division_indexed
			END AS unit_division_indexed,
			CASE
				WHEN level_sys_name = 'unit_family' THEN
					id
				ELSE
					pm.unit_family_ref
			END AS unit_family_ref,
			CASE
				WHEN level_sys_name = 'unit_family' THEN
					name_indexed
				ELSE
					pm.unit_family_indexed
			END AS unit_family_indexed,
			CASE
				WHEN level_sys_name = 'unit_group' THEN
					id
				ELSE
					pm.unit_group_ref
			END AS unit_group_ref,
			CASE
				WHEN level_sys_name = 'unit_group' THEN
					name_indexed
				ELSE
					pm.unit_group_indexed
			END AS unit_group_indexed,
			CASE
				WHEN level_sys_name = 'unit_variety' THEN
					id
				ELSE
					pm.unit_variety_ref
			END AS unit_variety_ref,
			CASE
				WHEN level_sys_name = 'unit_variety' THEN
					name_indexed
				ELSE
					pm.unit_variety_indexed
			END AS unit_variety_indexed
		INTO 
			result
		FROM mineralogy AS pm
		WHERE pm.id = parent_ref;
	ELSIF table_name = 'taxa' THEN
		SELECT
			CASE
				WHEN level_sys_name = 'domain' THEN
					id
				ELSE
					pt.domain_ref
			END AS domain_ref,
			CASE
				WHEN level_sys_name = 'domain' THEN
					name_indexed
				ELSE
					pt.domain_indexed
			END AS domain_indexed,
			CASE
				WHEN level_sys_name = 'kingdom' THEN
					id
				ELSE
					pt.kingdom_ref
			END AS kingdom_ref,
			CASE
				WHEN level_sys_name = 'kingdom' THEN
					name_indexed
				ELSE
					pt.kingdom_indexed
			END AS kingdom_indexed,
			CASE
				WHEN level_sys_name = 'super_phylum' THEN
					id
				ELSE
					pt.super_phylum_ref
			END AS super_phylum_ref,
			CASE
				WHEN level_sys_name = 'super_phylum' THEN
					name_indexed
				ELSE
					pt.super_phylum_indexed
			END AS super_phylum_indexed,
			CASE
				WHEN level_sys_name = 'phylum' THEN
					id
				ELSE
					pt.phylum_ref
			END AS phylum_ref,
			CASE
				WHEN level_sys_name = 'phylum' THEN
					name_indexed
				ELSE
					pt.phylum_indexed
			END AS phylum_indexed,
			CASE
				WHEN level_sys_name = 'sub_phylum' THEN
					id
				ELSE
					pt.sub_phylum_ref
			END AS sub_phylum_ref,
			CASE
				WHEN level_sys_name = 'sub_phylum' THEN
					name_indexed
				ELSE
					pt.sub_phylum_indexed
			END AS sub_phylum_indexed,
			CASE
				WHEN level_sys_name = 'infra_phylum' THEN
					id
				ELSE
					pt.infra_phylum_ref
			END AS infra_phylum_ref,
			CASE
				WHEN level_sys_name = 'infra_phylum' THEN
					name_indexed
				ELSE
					pt.infra_phylum_indexed
			END AS infra_phylum_indexed,
			CASE
				WHEN level_sys_name = 'super_cohort_botany' THEN
					id
				ELSE
					pt.super_cohort_botany_ref
			END AS super_cohort_botany_ref,
			CASE
				WHEN level_sys_name = 'super_cohort_botany' THEN
					name_indexed
				ELSE
					pt.super_cohort_botany_indexed
			END AS super_cohort_botany_indexed,
			CASE
				WHEN level_sys_name = 'cohort_botany' THEN
					id
				ELSE
					pt.cohort_botany_ref
			END AS cohort_botany_ref,
			CASE
				WHEN level_sys_name = 'cohort_botany' THEN
					name_indexed
				ELSE
					pt.cohort_botany_indexed
			END AS cohort_botany_indexed,
			CASE
				WHEN level_sys_name = 'sub_cohort_botany' THEN
					id
				ELSE
					pt.sub_cohort_botany_ref
			END AS sub_cohort_botany_ref,
			CASE
				WHEN level_sys_name = 'sub_cohort_botany' THEN
					name_indexed
				ELSE
					pt.sub_cohort_botany_indexed
			END AS sub_cohort_botany_indexed,
			CASE
				WHEN level_sys_name = 'infra_cohort_botany' THEN
					id
				ELSE
					pt.infra_cohort_botany_ref
			END AS infra_cohort_botany_ref,
			CASE
				WHEN level_sys_name = 'infra_cohort_botany' THEN
					name_indexed
				ELSE
					pt.infra_cohort_botany_indexed
			END AS infra_cohort_botany_indexed,
			CASE
				WHEN level_sys_name = 'super_class' THEN
					id
				ELSE
					pt.super_class_ref
			END AS super_class_ref,
			CASE
				WHEN level_sys_name = 'super_class' THEN
					name_indexed
				ELSE
					pt.super_class_indexed
			END AS super_class_indexed,
			CASE
				WHEN level_sys_name = 'class' THEN
					id
				ELSE
					pt.class_ref
			END AS class_ref,
			CASE
				WHEN level_sys_name = 'class' THEN
					name_indexed
				ELSE
					pt.class_indexed
			END AS class_indexed,
			CASE
				WHEN level_sys_name = 'sub_class' THEN
					id
				ELSE
					pt.sub_class_ref
			END AS sub_class_ref,
			CASE
				WHEN level_sys_name = 'sub_class' THEN
					name_indexed
				ELSE
					pt.sub_class_indexed
			END AS sub_class_indexed,
			CASE
				WHEN level_sys_name = 'infra_class' THEN
					id
				ELSE
					pt.infra_class_ref
			END AS infra_class_ref,
			CASE
				WHEN level_sys_name = 'infra_class' THEN
					name_indexed
				ELSE
					pt.infra_class_indexed
			END AS infra_class_indexed,
			CASE
				WHEN level_sys_name = 'super_division' THEN
					id
				ELSE
					pt.super_division_ref
			END AS super_division_ref,
			CASE
				WHEN level_sys_name = 'super_division' THEN
					name_indexed
				ELSE
					pt.super_division_indexed
			END AS super_division_indexed,
			CASE
				WHEN level_sys_name = 'division' THEN
					id
				ELSE
					pt.division_ref
			END AS division_ref,
			CASE
				WHEN level_sys_name = 'division' THEN
					name_indexed
				ELSE
					pt.division_indexed
			END AS division_indexed,
			CASE
				WHEN level_sys_name = 'sub_division' THEN
					id
				ELSE
					pt.sub_division_ref
			END AS sub_division_ref,
			CASE
				WHEN level_sys_name = 'sub_division' THEN
					name_indexed
				ELSE
					pt.sub_division_indexed
			END AS sub_division_indexed,
			CASE
				WHEN level_sys_name = 'infra_division' THEN
					id
				ELSE
					pt.infra_division_ref
			END AS infra_division_ref,
			CASE
				WHEN level_sys_name = 'infra_division' THEN
					name_indexed
				ELSE
					pt.infra_division_indexed
			END AS infra_division_indexed,
			CASE
				WHEN level_sys_name = 'super_legion' THEN
					id
				ELSE
					pt.super_legion_ref
			END AS super_legion_ref,
			CASE
				WHEN level_sys_name = 'super_legion' THEN
					name_indexed
				ELSE
					pt.super_legion_indexed
			END AS super_legion_indexed,
			CASE
				WHEN level_sys_name = 'legion' THEN
					id
				ELSE
					pt.legion_ref
			END AS legion_ref,
			CASE
				WHEN level_sys_name = 'legion' THEN
					name_indexed
				ELSE
					pt.legion_indexed
			END AS legion_indexed,
			CASE
				WHEN level_sys_name = 'sub_legion' THEN
					id
				ELSE
					pt.sub_legion_ref
			END AS sub_legion_ref,
			CASE
				WHEN level_sys_name = 'sub_legion' THEN
					name_indexed
				ELSE
					pt.sub_legion_indexed
			END AS sub_legion_indexed,
			CASE
				WHEN level_sys_name = 'infra_legion' THEN
					id
				ELSE
					pt.infra_legion_ref
			END AS infra_legion_ref,
			CASE
				WHEN level_sys_name = 'infra_legion' THEN
					name_indexed
				ELSE
					pt.infra_legion_indexed
			END AS infra_legion_indexed,
			CASE
				WHEN level_sys_name = 'super_cohort_zoology' THEN
					id
				ELSE
					pt.super_cohort_zoology_ref
			END AS super_cohort_zoology_ref,
			CASE
				WHEN level_sys_name = 'super_cohort_zoology' THEN
					name_indexed
				ELSE
					pt.super_cohort_zoology_indexed
			END AS super_cohort_zoology_indexed,
			CASE
				WHEN level_sys_name = 'cohort_zoology' THEN
					id
				ELSE
					pt.cohort_zoology_ref
			END AS cohort_zoology_ref,
			CASE
				WHEN level_sys_name = 'cohort_zoology' THEN
					name_indexed
				ELSE
					pt.cohort_zoology_indexed
			END AS cohort_zoology_indexed,
			CASE
				WHEN level_sys_name = 'sub_cohort_zoology' THEN
					id
				ELSE
					pt.sub_cohort_zoology_ref
			END AS sub_cohort_zoology_ref,
			CASE
				WHEN level_sys_name = 'sub_cohort_zoology' THEN
					name_indexed
				ELSE
					pt.sub_cohort_zoology_indexed
			END AS sub_cohort_zoology_indexed,
			CASE
				WHEN level_sys_name = 'infra_cohort_zoology' THEN
					id
				ELSE
					pt.infra_cohort_zoology_ref
			END AS infra_cohort_zoology_ref,
			CASE
				WHEN level_sys_name = 'infra_cohort_zoology' THEN
					name_indexed
				ELSE
					pt.infra_cohort_zoology_indexed
			END AS infra_cohort_zoology_indexed,
			CASE
				WHEN level_sys_name = 'super_order' THEN
					id
				ELSE
					pt.super_order_ref
			END AS super_order_ref,
			CASE
				WHEN level_sys_name = 'super_order' THEN
					name_indexed
				ELSE
					pt.super_order_indexed
			END AS super_order_indexed,
			CASE
				WHEN level_sys_name = 'order' THEN
					id
				ELSE
					pt.order_ref
			END AS order_ref,
			CASE
				WHEN level_sys_name = 'order' THEN
					name_indexed
				ELSE
					pt.order_indexed
			END AS order_indexed,
			CASE
				WHEN level_sys_name = 'sub_order' THEN
					id
				ELSE
					pt.sub_order_ref
			END AS sub_order_ref,
			CASE
				WHEN level_sys_name = 'sub_order' THEN
					name_indexed
				ELSE
					pt.sub_order_indexed
			END AS sub_order_indexed,
			CASE
				WHEN level_sys_name = 'infra_order' THEN
					id
				ELSE
					pt.infra_order_ref
			END AS infra_order_ref,
			CASE
				WHEN level_sys_name = 'infra_order' THEN
					name_indexed
				ELSE
					pt.infra_order_indexed
			END AS infra_order_indexed,
			CASE
				WHEN level_sys_name = 'section_zoology' THEN
					id
				ELSE
					pt.section_zoology_ref
			END AS section_zoology_ref,
			CASE
				WHEN level_sys_name = 'section_zoology' THEN
					name_indexed
				ELSE
					pt.section_zoology_indexed
			END AS section_zoology_indexed,
			CASE
				WHEN level_sys_name = 'sub_section_zoology' THEN
					id
				ELSE
					pt.sub_section_zoology_ref
			END AS sub_section_zoology_ref,
			CASE
				WHEN level_sys_name = 'sub_section_zoology' THEN
					name_indexed
				ELSE
					pt.sub_section_zoology_indexed
			END AS sub_section_zoology_indexed,
			CASE
				WHEN level_sys_name = 'super_family' THEN
					id
				ELSE
					pt.super_family_ref
			END AS super_family_ref,
			CASE
				WHEN level_sys_name = 'super_family' THEN
					name_indexed
				ELSE
					pt.super_family_indexed
			END AS super_family_indexed,
			CASE
				WHEN level_sys_name = 'family' THEN
					id
				ELSE
					pt.family_ref
			END AS family_ref,
			CASE
				WHEN level_sys_name = 'family' THEN
					name_indexed
				ELSE
					pt.family_indexed
			END AS family_indexed,
			CASE
				WHEN level_sys_name = 'sub_family' THEN
					id
				ELSE
					pt.sub_family_ref
			END AS sub_family_ref,
			CASE
				WHEN level_sys_name = 'sub_family' THEN
					name_indexed
				ELSE
					pt.sub_family_indexed
			END AS sub_family_indexed,
			CASE
				WHEN level_sys_name = 'infra_family' THEN
					id
				ELSE
					pt.infra_family_ref
			END AS infra_family_ref,
			CASE
				WHEN level_sys_name = 'infra_family' THEN
					name_indexed
				ELSE
					pt.infra_family_indexed
			END AS infra_family_indexed,
			CASE
				WHEN level_sys_name = 'super_tribe' THEN
					id
				ELSE
					pt.super_tribe_ref
			END AS super_tribe_ref,
			CASE
				WHEN level_sys_name = 'super_tribe' THEN
					name_indexed
				ELSE
					pt.super_tribe_indexed
			END AS super_tribe_indexed,
			CASE
				WHEN level_sys_name = 'tribe' THEN
					id
				ELSE
					pt.tribe_ref
			END AS tribe_ref,
			CASE
				WHEN level_sys_name = 'tribe' THEN
					name_indexed
				ELSE
					pt.tribe_indexed
			END AS tribe_indexed,
			CASE
				WHEN level_sys_name = 'sub_tribe' THEN
					id
				ELSE
					pt.sub_tribe_ref
			END AS sub_tribe_ref,
			CASE
				WHEN level_sys_name = 'sub_tribe' THEN
					name_indexed
				ELSE
					pt.sub_tribe_indexed
			END AS sub_tribe_indexed,
			CASE
				WHEN level_sys_name = 'infra_tribe' THEN
					id
				ELSE
					pt.infra_tribe_ref
			END AS infra_tribe_ref,
			CASE
				WHEN level_sys_name = 'infra_tribe' THEN
					name_indexed
				ELSE
					pt.infra_tribe_indexed
			END AS infra_tribe_indexed,
			CASE
				WHEN level_sys_name = 'genus' THEN
					id
				ELSE
					pt.genus_ref
			END AS genus_ref,
			CASE
				WHEN level_sys_name = 'genus' THEN
					name_indexed
				ELSE
					pt.genus_indexed
			END AS genus_indexed,
			CASE
				WHEN level_sys_name = 'sub_genus' THEN
					id
				ELSE
					pt.sub_genus_ref
			END AS sub_genus_ref,
			CASE
				WHEN level_sys_name = 'sub_genus' THEN
					name_indexed
				ELSE
					pt.sub_genus_indexed
			END AS sub_genus_indexed,
			CASE
				WHEN level_sys_name = 'section_botany' THEN
					id
				ELSE
					pt.section_botany_ref
			END AS section_botany_ref,
			CASE
				WHEN level_sys_name = 'section_botany' THEN
					name_indexed
				ELSE
					pt.section_botany_indexed
			END AS section_botany_indexed,
			CASE
				WHEN level_sys_name = 'sub_section_botany' THEN
					id
				ELSE
					pt.sub_section_botany_ref
			END AS sub_section_botany_ref,
			CASE
				WHEN level_sys_name = 'sub_section_botany' THEN
					name_indexed
				ELSE
					pt.sub_section_botany_indexed
			END AS sub_section_botany_indexed,
			CASE
				WHEN level_sys_name = 'serie' THEN
					id
				ELSE
					pt.serie_ref
			END AS serie_ref,
			CASE
				WHEN level_sys_name = 'serie' THEN
					name_indexed
				ELSE
					pt.serie_indexed
			END AS serie_indexed,
			CASE
				WHEN level_sys_name = 'sub_serie' THEN
					id
				ELSE
					pt.sub_serie_ref
			END AS sub_serie_ref,
			CASE
				WHEN level_sys_name = 'sub_serie' THEN
					name_indexed
				ELSE
					pt.sub_serie_indexed
			END AS sub_serie_indexed,
			CASE
				WHEN level_sys_name = 'super_species' THEN
					id
				ELSE
					pt.super_species_ref
			END AS super_species_ref,
			CASE
				WHEN level_sys_name = 'super_species' THEN
					name_indexed
				ELSE
					pt.super_species_indexed
			END AS super_species_indexed,
			CASE
				WHEN level_sys_name = 'species' THEN
					id
				ELSE
					pt.species_ref
			END AS species_ref,
			CASE
				WHEN level_sys_name = 'species' THEN
					name_indexed
				ELSE
					pt.species_indexed
			END AS species_indexed,
			CASE
				WHEN level_sys_name = 'sub_species' THEN
					id
				ELSE
					pt.sub_species_ref
			END AS sub_species_ref,
			CASE
				WHEN level_sys_name = 'sub_species' THEN
					name_indexed
				ELSE
					pt.sub_species_indexed
			END AS sub_species_indexed,
			CASE
				WHEN level_sys_name = 'variety' THEN
					id
				ELSE
					pt.variety_ref
			END AS variety_ref,
			CASE
				WHEN level_sys_name = 'variety' THEN
					name_indexed
				ELSE
					pt.variety_indexed
			END AS variety_indexed,
			CASE
				WHEN level_sys_name = 'sub_variety' THEN
					id
				ELSE
					pt.sub_variety_ref
			END AS sub_variety_ref,
			CASE
				WHEN level_sys_name = 'sub_variety' THEN
					name_indexed
				ELSE
					pt.sub_variety_indexed
			END AS sub_variety_indexed,
			CASE
				WHEN level_sys_name = 'form' THEN
					id
				ELSE
					pt.form_ref
			END AS form_ref,
			CASE
				WHEN level_sys_name = 'form' THEN
					name_indexed
				ELSE
					pt.form_indexed
			END AS form_indexed,
			CASE
				WHEN level_sys_name = 'sub_form' THEN
					id
				ELSE
					pt.sub_form_ref
			END AS sub_form_ref,
			CASE
				WHEN level_sys_name = 'sub_form' THEN
					name_indexed
				ELSE
					pt.sub_form_indexed
			END AS sub_form_indexed,
			CASE
				WHEN level_sys_name = 'abberans' THEN
					id
				ELSE
					pt.abberans_ref
			END AS abberans_ref,
			CASE
				WHEN level_sys_name = 'abberans' THEN
					name_indexed
				ELSE
					pt.abberans_indexed
			END AS abberans_indexed
		INTO 
			result
		FROM taxa AS pt
		WHERE pt.id = parent_ref;
	END IF;
	RETURN result;
END;
$$ LANGUAGE plpgsql;

/***
* Trigger function fct_cpy_hierarchy_from_parents
* Version of function used to check what's coming from parents and what's coming from unit passed itself
*/
CREATE OR REPLACE FUNCTION fct_cpy_hierarchy_from_parents() RETURNS trigger
AS $$
DECLARE
	level_sys_name catalogue_levels.level_sys_name%TYPE;
BEGIN
	SELECT cl.level_sys_name INTO level_sys_name FROM catalogue_levels as cl WHERE cl.id = NEW.level_ref;
	IF TG_TABLE_NAME = 'chronostratigraphy' THEN
		SELECT 
			CASE
				WHEN level_sys_name = 'eon' THEN
					NEW.id
				ELSE
					pc.eon_ref
			END AS eon_ref,
			CASE
				WHEN level_sys_name = 'eon' THEN
					NEW.name_indexed
				ELSE
					pc.eon_indexed
			END AS eon_indexed,
			CASE
				WHEN level_sys_name = 'era' THEN
					NEW.id
				ELSE
					pc.era_ref
			END AS era_ref,
			CASE
				WHEN level_sys_name = 'era' THEN
					NEW.name_indexed
				ELSE
					pc.era_indexed
			END AS era_indexed,
			CASE
				WHEN level_sys_name = 'sub_era' THEN
					NEW.id
				ELSE
					pc.sub_era_ref
			END AS sub_era_ref,
			CASE
				WHEN level_sys_name = 'sub_era' THEN
					NEW.name_indexed
				ELSE
					pc.sub_era_indexed
			END AS sub_era_indexed,
			CASE
				WHEN level_sys_name = 'system' THEN
					NEW.id
				ELSE
					pc.system_ref
			END AS system_ref,
			CASE
				WHEN level_sys_name = 'system' THEN
					NEW.name_indexed
				ELSE
					pc.system_indexed
			END AS system_indexed,
			CASE
				WHEN level_sys_name = 'serie' THEN
					NEW.id
				ELSE
					pc.serie_ref
			END AS serie_ref,
			CASE
				WHEN level_sys_name = 'serie' THEN
					NEW.name_indexed
				ELSE
					pc.serie_indexed
			END AS serie_indexed,
			CASE
				WHEN level_sys_name = 'stage' THEN
					NEW.id
				ELSE
					pc.stage_ref
			END AS stage_ref,
			CASE
				WHEN level_sys_name = 'stage' THEN
					NEW.name_indexed
				ELSE
					pc.stage_indexed
			END AS stage_indexed,
			CASE
				WHEN level_sys_name = 'sub_stage' THEN
					NEW.id
				ELSE
					pc.sub_stage_ref
			END AS sub_stage_ref,
			CASE
				WHEN level_sys_name = 'sub_stage' THEN
					NEW.name_indexed
				ELSE
					pc.sub_stage_indexed
			END AS sub_stage_indexed,
			CASE
				WHEN level_sys_name = 'sub_level_1' THEN
					NEW.id
				ELSE
					pc.sub_level_1_ref
			END AS sub_level_1_ref,
			CASE
				WHEN level_sys_name = 'sub_level_1' THEN
					NEW.name_indexed
				ELSE
					pc.sub_level_1_indexed
			END AS sub_level_1_indexed,
			CASE
				WHEN level_sys_name = 'sub_level_2' THEN
					NEW.id
				ELSE
					pc.sub_level_2_ref
			END AS sub_level_2_ref,
			CASE
				WHEN level_sys_name = 'sub_level_2' THEN
					NEW.name_indexed
				ELSE
					pc.sub_level_2_indexed
			END AS sub_level_2_indexed
		INTO 
			NEW.eon_ref,
			NEW.eon_indexed,
			NEW.era_ref,
			NEW.era_indexed,
			NEW.sub_era_ref,
			NEW.sub_era_indexed,
			NEW.system_ref,
			NEW.system_indexed,
			NEW.serie_ref,
			NEW.serie_indexed,
			NEW.stage_ref,
			NEW.stage_indexed,
			NEW.sub_stage_ref,
			NEW.sub_stage_indexed,
			NEW.sub_level_1_ref,
			NEW.sub_level_1_indexed,
			NEW.sub_level_2_ref,
			NEW.sub_level_2_indexed
		FROM chronostratigraphy AS pc
		WHERE pc.id = NEW.parent_ref;
	ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
		SELECT 
			CASE
				WHEN level_sys_name = 'group' THEN
					NEW.id
				ELSE
					pl.group_ref
			END AS group_ref,
			CASE
				WHEN level_sys_name = 'group' THEN
					NEW.name_indexed
				ELSE
					pl.group_indexed
			END AS group_indexed,
			CASE
				WHEN level_sys_name = 'formation' THEN
					NEW.id
				ELSE
					pl.formation_ref
			END AS formation_ref,
			CASE
				WHEN level_sys_name = 'formation' THEN
					NEW.name_indexed
				ELSE
					pl.formation_indexed
			END AS formation_indexed,
			CASE
				WHEN level_sys_name = 'member' THEN
					NEW.id
				ELSE
					pl.member_ref
			END AS member_ref,
			CASE
				WHEN level_sys_name = 'member' THEN
					NEW.name_indexed
				ELSE
					pl.member_indexed
			END AS member_indexed,
			CASE
				WHEN level_sys_name = 'layer' THEN
					NEW.id
				ELSE
					pl.layer_ref
			END AS layer_ref,
			CASE
				WHEN level_sys_name = 'layer' THEN
					NEW.name_indexed
				ELSE
					pl.layer_indexed
			END AS layer_indexed,
			CASE
				WHEN level_sys_name = 'sub_level_1' THEN
					NEW.id
				ELSE
					pl.sub_level_1_ref
			END AS sub_level_1_ref,
			CASE
				WHEN level_sys_name = 'sub_level_1' THEN
					NEW.name_indexed
				ELSE
					pl.sub_level_1_indexed
			END AS sub_level_1_indexed,
			CASE
				WHEN level_sys_name = 'sub_level_2' THEN
					NEW.id
				ELSE
					pl.sub_level_2_ref
			END AS sub_level_2_ref,
			CASE
				WHEN level_sys_name = 'sub_level_2' THEN
					NEW.name_indexed
				ELSE
					pl.sub_level_2_indexed
			END AS sub_level_2_indexed
		INTO 
			NEW.group_ref,
			NEW.group_indexed,
			NEW.formation_ref,
			NEW.formation_indexed,
			NEW.member_ref,
			NEW.member_indexed,
			NEW.layer_ref,
			NEW.layer_indexed,
			NEW.sub_level_1_ref,
			NEW.sub_level_1_indexed,
			NEW.sub_level_2_ref,
			NEW.sub_level_2_indexed
		FROM lithostratigraphy AS pl
		WHERE pl.id = NEW.parent_ref;
	ELSIF TG_TABLE_NAME = 'lithology' THEN
		
	ELSIF TG_TABLE_NAME = 'mineralogy' THEN
		SELECT 
			CASE
				WHEN level_sys_name = 'unit_class' THEN
					NEW.id
				ELSE
					pm.unit_class_ref
			END AS unit_class_ref,
			CASE
				WHEN level_sys_name = 'unit_class' THEN
					NEW.name_indexed
				ELSE
					pm.unit_class_indexed
			END AS unit_class_indexed,
			CASE
				WHEN level_sys_name = 'unit_division' THEN
					NEW.id
				ELSE
					pm.unit_division_ref
			END AS unit_division_ref,
			CASE
				WHEN level_sys_name = 'unit_division' THEN
					NEW.name_indexed
				ELSE
					pm.unit_division_indexed
			END AS unit_division_indexed,
			CASE
				WHEN level_sys_name = 'unit_family' THEN
					NEW.id
				ELSE
					pm.unit_family_ref
			END AS unit_family_ref,
			CASE
				WHEN level_sys_name = 'unit_family' THEN
					NEW.name_indexed
				ELSE
					pm.unit_family_indexed
			END AS unit_family_indexed,
			CASE
				WHEN level_sys_name = 'unit_group' THEN
					NEW.id
				ELSE
					pm.unit_group_ref
			END AS unit_group_ref,
			CASE
				WHEN level_sys_name = 'unit_group' THEN
					NEW.name_indexed
				ELSE
					pm.unit_group_indexed
			END AS unit_group_indexed,
			CASE
				WHEN level_sys_name = 'unit_variety' THEN
					NEW.id
				ELSE
					pm.unit_variety_ref
			END AS unit_variety_ref,
			CASE
				WHEN level_sys_name = 'unit_variety' THEN
					NEW.name_indexed
				ELSE
					pm.unit_variety_indexed
			END AS unit_variety_indexed
		INTO 
			NEW.unit_class_ref,
			NEW.unit_class_indexed,
			NEW.unit_division_ref,
			NEW.unit_division_indexed,
			NEW.unit_family_ref,
			NEW.unit_family_indexed,
			NEW.unit_group_ref,
			NEW.unit_group_indexed,
			NEW.unit_variety_ref,
			NEW.unit_variety_indexed
		FROM mineralogy AS pm
		WHERE pm.id = NEW.parent_ref;
	ELSIF TG_TABLE_NAME = 'taxa' THEN
		SELECT
			CASE
				WHEN level_sys_name = 'domain' THEN
					NEW.id
				ELSE
					pt.domain_ref
			END AS domain_ref,
			CASE
				WHEN level_sys_name = 'domain' THEN
					NEW.name_indexed
				ELSE
					pt.domain_indexed
			END AS domain_indexed,
			CASE
				WHEN level_sys_name = 'kingdom' THEN
					NEW.id
				ELSE
					pt.kingdom_ref
			END AS kingdom_ref,
			CASE
				WHEN level_sys_name = 'kingdom' THEN
					NEW.name_indexed
				ELSE
					pt.kingdom_indexed
			END AS kingdom_indexed,
			CASE
				WHEN level_sys_name = 'super_phylum' THEN
					NEW.id
				ELSE
					pt.super_phylum_ref
			END AS super_phylum_ref,
			CASE
				WHEN level_sys_name = 'super_phylum' THEN
					NEW.name_indexed
				ELSE
					pt.super_phylum_indexed
			END AS super_phylum_indexed,
			CASE
				WHEN level_sys_name = 'phylum' THEN
					NEW.id
				ELSE
					pt.phylum_ref
			END AS phylum_ref,
			CASE
				WHEN level_sys_name = 'phylum' THEN
					NEW.name_indexed
				ELSE
					pt.phylum_indexed
			END AS phylum_indexed,
			CASE
				WHEN level_sys_name = 'sub_phylum' THEN
					NEW.id
				ELSE
					pt.sub_phylum_ref
			END AS sub_phylum_ref,
			CASE
				WHEN level_sys_name = 'sub_phylum' THEN
					NEW.name_indexed
				ELSE
					pt.sub_phylum_indexed
			END AS sub_phylum_indexed,
			CASE
				WHEN level_sys_name = 'infra_phylum' THEN
					NEW.id
				ELSE
					pt.infra_phylum_ref
			END AS infra_phylum_ref,
			CASE
				WHEN level_sys_name = 'infra_phylum' THEN
					NEW.name_indexed
				ELSE
					pt.infra_phylum_indexed
			END AS infra_phylum_indexed,
			CASE
				WHEN level_sys_name = 'super_cohort_botany' THEN
					NEW.id
				ELSE
					pt.super_cohort_botany_ref
			END AS super_cohort_botany_ref,
			CASE
				WHEN level_sys_name = 'super_cohort_botany' THEN
					NEW.name_indexed
				ELSE
					pt.super_cohort_botany_indexed
			END AS super_cohort_botany_indexed,
			CASE
				WHEN level_sys_name = 'cohort_botany' THEN
					NEW.id
				ELSE
					pt.cohort_botany_ref
			END AS cohort_botany_ref,
			CASE
				WHEN level_sys_name = 'cohort_botany' THEN
					NEW.name_indexed
				ELSE
					pt.cohort_botany_indexed
			END AS cohort_botany_indexed,
			CASE
				WHEN level_sys_name = 'sub_cohort_botany' THEN
					NEW.id
				ELSE
					pt.sub_cohort_botany_ref
			END AS sub_cohort_botany_ref,
			CASE
				WHEN level_sys_name = 'sub_cohort_botany' THEN
					NEW.name_indexed
				ELSE
					pt.sub_cohort_botany_indexed
			END AS sub_cohort_botany_indexed,
			CASE
				WHEN level_sys_name = 'infra_cohort_botany' THEN
					NEW.id
				ELSE
					pt.infra_cohort_botany_ref
			END AS infra_cohort_botany_ref,
			CASE
				WHEN level_sys_name = 'infra_cohort_botany' THEN
					NEW.name_indexed
				ELSE
					pt.infra_cohort_botany_indexed
			END AS infra_cohort_botany_indexed,
			CASE
				WHEN level_sys_name = 'super_class' THEN
					NEW.id
				ELSE
					pt.super_class_ref
			END AS super_class_ref,
			CASE
				WHEN level_sys_name = 'super_class' THEN
					NEW.name_indexed
				ELSE
					pt.super_class_indexed
			END AS super_class_indexed,
			CASE
				WHEN level_sys_name = 'class' THEN
					NEW.id
				ELSE
					pt.class_ref
			END AS class_ref,
			CASE
				WHEN level_sys_name = 'class' THEN
					NEW.name_indexed
				ELSE
					pt.class_indexed
			END AS class_indexed,
			CASE
				WHEN level_sys_name = 'sub_class' THEN
					NEW.id
				ELSE
					pt.sub_class_ref
			END AS sub_class_ref,
			CASE
				WHEN level_sys_name = 'sub_class' THEN
					NEW.name_indexed
				ELSE
					pt.sub_class_indexed
			END AS sub_class_indexed,
			CASE
				WHEN level_sys_name = 'infra_class' THEN
					NEW.id
				ELSE
					pt.infra_class_ref
			END AS infra_class_ref,
			CASE
				WHEN level_sys_name = 'infra_class' THEN
					NEW.name_indexed
				ELSE
					pt.infra_class_indexed
			END AS infra_class_indexed,
			CASE
				WHEN level_sys_name = 'super_division' THEN
					NEW.id
				ELSE
					pt.super_division_ref
			END AS super_division_ref,
			CASE
				WHEN level_sys_name = 'super_division' THEN
					NEW.name_indexed
				ELSE
					pt.super_division_indexed
			END AS super_division_indexed,
			CASE
				WHEN level_sys_name = 'division' THEN
					NEW.id
				ELSE
					pt.division_ref
			END AS division_ref,
			CASE
				WHEN level_sys_name = 'division' THEN
					NEW.name_indexed
				ELSE
					pt.division_indexed
			END AS division_indexed,
			CASE
				WHEN level_sys_name = 'sub_division' THEN
					NEW.id
				ELSE
					pt.sub_division_ref
			END AS sub_division_ref,
			CASE
				WHEN level_sys_name = 'sub_division' THEN
					NEW.name_indexed
				ELSE
					pt.sub_division_indexed
			END AS sub_division_indexed,
			CASE
				WHEN level_sys_name = 'infra_division' THEN
					NEW.id
				ELSE
					pt.infra_division_ref
			END AS infra_division_ref,
			CASE
				WHEN level_sys_name = 'infra_division' THEN
					NEW.name_indexed
				ELSE
					pt.infra_division_indexed
			END AS infra_division_indexed,
			CASE
				WHEN level_sys_name = 'super_legion' THEN
					NEW.id
				ELSE
					pt.super_legion_ref
			END AS super_legion_ref,
			CASE
				WHEN level_sys_name = 'super_legion' THEN
					NEW.name_indexed
				ELSE
					pt.super_legion_indexed
			END AS super_legion_indexed,
			CASE
				WHEN level_sys_name = 'legion' THEN
					NEW.id
				ELSE
					pt.legion_ref
			END AS legion_ref,
			CASE
				WHEN level_sys_name = 'legion' THEN
					NEW.name_indexed
				ELSE
					pt.legion_indexed
			END AS legion_indexed,
			CASE
				WHEN level_sys_name = 'sub_legion' THEN
					NEW.id
				ELSE
					pt.sub_legion_ref
			END AS sub_legion_ref,
			CASE
				WHEN level_sys_name = 'sub_legion' THEN
					NEW.name_indexed
				ELSE
					pt.sub_legion_indexed
			END AS sub_legion_indexed,
			CASE
				WHEN level_sys_name = 'infra_legion' THEN
					NEW.id
				ELSE
					pt.infra_legion_ref
			END AS infra_legion_ref,
			CASE
				WHEN level_sys_name = 'infra_legion' THEN
					NEW.name_indexed
				ELSE
					pt.infra_legion_indexed
			END AS infra_legion_indexed,
			CASE
				WHEN level_sys_name = 'super_cohort_zoology' THEN
					NEW.id
				ELSE
					pt.super_cohort_zoology_ref
			END AS super_cohort_zoology_ref,
			CASE
				WHEN level_sys_name = 'super_cohort_zoology' THEN
					NEW.name_indexed
				ELSE
					pt.super_cohort_zoology_indexed
			END AS super_cohort_zoology_indexed,
			CASE
				WHEN level_sys_name = 'cohort_zoology' THEN
					NEW.id
				ELSE
					pt.cohort_zoology_ref
			END AS cohort_zoology_ref,
			CASE
				WHEN level_sys_name = 'cohort_zoology' THEN
					NEW.name_indexed
				ELSE
					pt.cohort_zoology_indexed
			END AS cohort_zoology_indexed,
			CASE
				WHEN level_sys_name = 'sub_cohort_zoology' THEN
					NEW.id
				ELSE
					pt.sub_cohort_zoology_ref
			END AS sub_cohort_zoology_ref,
			CASE
				WHEN level_sys_name = 'sub_cohort_zoology' THEN
					NEW.name_indexed
				ELSE
					pt.sub_cohort_zoology_indexed
			END AS sub_cohort_zoology_indexed,
			CASE
				WHEN level_sys_name = 'infra_cohort_zoology' THEN
					NEW.id
				ELSE
					pt.infra_cohort_zoology_ref
			END AS infra_cohort_zoology_ref,
			CASE
				WHEN level_sys_name = 'infra_cohort_zoology' THEN
					NEW.name_indexed
				ELSE
					pt.infra_cohort_zoology_indexed
			END AS infra_cohort_zoology_indexed,
			CASE
				WHEN level_sys_name = 'super_order' THEN
					NEW.id
				ELSE
					pt.super_order_ref
			END AS super_order_ref,
			CASE
				WHEN level_sys_name = 'super_order' THEN
					NEW.name_indexed
				ELSE
					pt.super_order_indexed
			END AS super_order_indexed,
			CASE
				WHEN level_sys_name = 'order' THEN
					NEW.id
				ELSE
					pt.order_ref
			END AS order_ref,
			CASE
				WHEN level_sys_name = 'order' THEN
					NEW.name_indexed
				ELSE
					pt.order_indexed
			END AS order_indexed,
			CASE
				WHEN level_sys_name = 'sub_order' THEN
					NEW.id
				ELSE
					pt.sub_order_ref
			END AS sub_order_ref,
			CASE
				WHEN level_sys_name = 'sub_order' THEN
					NEW.name_indexed
				ELSE
					pt.sub_order_indexed
			END AS sub_order_indexed,
			CASE
				WHEN level_sys_name = 'infra_order' THEN
					NEW.id
				ELSE
					pt.infra_order_ref
			END AS infra_order_ref,
			CASE
				WHEN level_sys_name = 'infra_order' THEN
					NEW.name_indexed
				ELSE
					pt.infra_order_indexed
			END AS infra_order_indexed,
			CASE
				WHEN level_sys_name = 'section_zoology' THEN
					NEW.id
				ELSE
					pt.section_zoology_ref
			END AS section_zoology_ref,
			CASE
				WHEN level_sys_name = 'section_zoology' THEN
					NEW.name_indexed
				ELSE
					pt.section_zoology_indexed
			END AS section_zoology_indexed,
			CASE
				WHEN level_sys_name = 'sub_section_zoology' THEN
					NEW.id
				ELSE
					pt.sub_section_zoology_ref
			END AS sub_section_zoology_ref,
			CASE
				WHEN level_sys_name = 'sub_section_zoology' THEN
					NEW.name_indexed
				ELSE
					pt.sub_section_zoology_indexed
			END AS sub_section_zoology_indexed,
			CASE
				WHEN level_sys_name = 'super_family' THEN
					NEW.id
				ELSE
					pt.super_family_ref
			END AS super_family_ref,
			CASE
				WHEN level_sys_name = 'super_family' THEN
					NEW.name_indexed
				ELSE
					pt.super_family_indexed
			END AS super_family_indexed,
			CASE
				WHEN level_sys_name = 'family' THEN
					NEW.id
				ELSE
					pt.family_ref
			END AS family_ref,
			CASE
				WHEN level_sys_name = 'family' THEN
					NEW.name_indexed
				ELSE
					pt.family_indexed
			END AS family_indexed,
			CASE
				WHEN level_sys_name = 'sub_family' THEN
					NEW.id
				ELSE
					pt.sub_family_ref
			END AS sub_family_ref,
			CASE
				WHEN level_sys_name = 'sub_family' THEN
					NEW.name_indexed
				ELSE
					pt.sub_family_indexed
			END AS sub_family_indexed,
			CASE
				WHEN level_sys_name = 'infra_family' THEN
					NEW.id
				ELSE
					pt.infra_family_ref
			END AS infra_family_ref,
			CASE
				WHEN level_sys_name = 'infra_family' THEN
					NEW.name_indexed
				ELSE
					pt.infra_family_indexed
			END AS infra_family_indexed,
			CASE
				WHEN level_sys_name = 'super_tribe' THEN
					NEW.id
				ELSE
					pt.super_tribe_ref
			END AS super_tribe_ref,
			CASE
				WHEN level_sys_name = 'super_tribe' THEN
					NEW.name_indexed
				ELSE
					pt.super_tribe_indexed
			END AS super_tribe_indexed,
			CASE
				WHEN level_sys_name = 'tribe' THEN
					NEW.id
				ELSE
					pt.tribe_ref
			END AS tribe_ref,
			CASE
				WHEN level_sys_name = 'tribe' THEN
					NEW.name_indexed
				ELSE
					pt.tribe_indexed
			END AS tribe_indexed,
			CASE
				WHEN level_sys_name = 'sub_tribe' THEN
					NEW.id
				ELSE
					pt.sub_tribe_ref
			END AS sub_tribe_ref,
			CASE
				WHEN level_sys_name = 'sub_tribe' THEN
					NEW.name_indexed
				ELSE
					pt.sub_tribe_indexed
			END AS sub_tribe_indexed,
			CASE
				WHEN level_sys_name = 'infra_tribe' THEN
					NEW.id
				ELSE
					pt.infra_tribe_ref
			END AS infra_tribe_ref,
			CASE
				WHEN level_sys_name = 'infra_tribe' THEN
					NEW.name_indexed
				ELSE
					pt.infra_tribe_indexed
			END AS infra_tribe_indexed,
			CASE
				WHEN level_sys_name = 'genus' THEN
					NEW.id
				ELSE
					pt.genus_ref
			END AS genus_ref,
			CASE
				WHEN level_sys_name = 'genus' THEN
					NEW.name_indexed
				ELSE
					pt.genus_indexed
			END AS genus_indexed,
			CASE
				WHEN level_sys_name = 'sub_genus' THEN
					NEW.id
				ELSE
					pt.sub_genus_ref
			END AS sub_genus_ref,
			CASE
				WHEN level_sys_name = 'sub_genus' THEN
					NEW.name_indexed
				ELSE
					pt.sub_genus_indexed
			END AS sub_genus_indexed,
			CASE
				WHEN level_sys_name = 'section_botany' THEN
					NEW.id
				ELSE
					pt.section_botany_ref
			END AS section_botany_ref,
			CASE
				WHEN level_sys_name = 'section_botany' THEN
					NEW.name_indexed
				ELSE
					pt.section_botany_indexed
			END AS section_botany_indexed,
			CASE
				WHEN level_sys_name = 'sub_section_botany' THEN
					NEW.id
				ELSE
					pt.sub_section_botany_ref
			END AS sub_section_botany_ref,
			CASE
				WHEN level_sys_name = 'sub_section_botany' THEN
					NEW.name_indexed
				ELSE
					pt.sub_section_botany_indexed
			END AS sub_section_botany_indexed,
			CASE
				WHEN level_sys_name = 'serie' THEN
					NEW.id
				ELSE
					pt.serie_ref
			END AS serie_ref,
			CASE
				WHEN level_sys_name = 'serie' THEN
					NEW.name_indexed
				ELSE
					pt.serie_indexed
			END AS serie_indexed,
			CASE
				WHEN level_sys_name = 'sub_serie' THEN
					NEW.id
				ELSE
					pt.sub_serie_ref
			END AS sub_serie_ref,
			CASE
				WHEN level_sys_name = 'sub_serie' THEN
					NEW.name_indexed
				ELSE
					pt.sub_serie_indexed
			END AS sub_serie_indexed,
			CASE
				WHEN level_sys_name = 'super_species' THEN
					NEW.id
				ELSE
					pt.super_species_ref
			END AS super_species_ref,
			CASE
				WHEN level_sys_name = 'super_species' THEN
					NEW.name_indexed
				ELSE
					pt.super_species_indexed
			END AS super_species_indexed,
			CASE
				WHEN level_sys_name = 'species' THEN
					NEW.id
				ELSE
					pt.species_ref
			END AS species_ref,
			CASE
				WHEN level_sys_name = 'species' THEN
					NEW.name_indexed
				ELSE
					pt.species_indexed
			END AS species_indexed,
			CASE
				WHEN level_sys_name = 'sub_species' THEN
					NEW.id
				ELSE
					pt.sub_species_ref
			END AS sub_species_ref,
			CASE
				WHEN level_sys_name = 'sub_species' THEN
					NEW.name_indexed
				ELSE
					pt.sub_species_indexed
			END AS sub_species_indexed,
			CASE
				WHEN level_sys_name = 'variety' THEN
					NEW.id
				ELSE
					pt.variety_ref
			END AS variety_ref,
			CASE
				WHEN level_sys_name = 'variety' THEN
					NEW.name_indexed
				ELSE
					pt.variety_indexed
			END AS variety_indexed,
			CASE
				WHEN level_sys_name = 'sub_variety' THEN
					NEW.id
				ELSE
					pt.sub_variety_ref
			END AS sub_variety_ref,
			CASE
				WHEN level_sys_name = 'sub_variety' THEN
					NEW.name_indexed
				ELSE
					pt.sub_variety_indexed
			END AS sub_variety_indexed,
			CASE
				WHEN level_sys_name = 'form' THEN
					NEW.id
				ELSE
					pt.form_ref
			END AS form_ref,
			CASE
				WHEN level_sys_name = 'form' THEN
					NEW.name_indexed
				ELSE
					pt.form_indexed
			END AS form_indexed,
			CASE
				WHEN level_sys_name = 'sub_form' THEN
					NEW.id
				ELSE
					pt.sub_form_ref
			END AS sub_form_ref,
			CASE
				WHEN level_sys_name = 'sub_form' THEN
					NEW.name_indexed
				ELSE
					pt.sub_form_indexed
			END AS sub_form_indexed,
			CASE
				WHEN level_sys_name = 'abberans' THEN
					NEW.id
				ELSE
					pt.abberans_ref
			END AS abberans_ref,
			CASE
				WHEN level_sys_name = 'abberans' THEN
					NEW.name_indexed
				ELSE
					pt.abberans_indexed
			END AS abberans_indexed
		INTO 
			NEW.domain_ref,
			NEW.domain_indexed,
			NEW.kingdom_ref,
			NEW.kingdom_indexed,
			NEW.super_phylum_ref,
			NEW.super_phylum_indexed,
			NEW.phylum_ref,
			NEW.phylum_indexed,
			NEW.sub_phylum_ref,
			NEW.sub_phylum_indexed,
			NEW.infra_phylum_ref,
			NEW.infra_phylum_indexed,
			NEW.super_cohort_botany_ref,
			NEW.super_cohort_botany_indexed,
			NEW.cohort_botany_ref,
			NEW.cohort_botany_indexed,
			NEW.sub_cohort_botany_ref,
			NEW.sub_cohort_botany_indexed,
			NEW.infra_cohort_botany_ref,
			NEW.infra_cohort_botany_indexed,
			NEW.super_class_ref,
			NEW.super_class_indexed,
			NEW.class_ref,
			NEW.class_indexed,
			NEW.sub_class_ref,
			NEW.sub_class_indexed,
			NEW.infra_class_ref,
			NEW.infra_class_indexed,
			NEW.super_division_ref,
			NEW.super_division_indexed,
			NEW.division_ref,
			NEW.division_indexed,
			NEW.sub_division_ref,
			NEW.sub_division_indexed,
			NEW.infra_division_ref,
			NEW.infra_division_indexed,
			NEW.super_legion_ref,
			NEW.super_legion_indexed,
			NEW.legion_ref,
			NEW.legion_indexed,
			NEW.sub_legion_ref,
			NEW.sub_legion_indexed,
			NEW.infra_legion_ref,
			NEW.infra_legion_indexed,
			NEW.super_cohort_zoology_ref,
			NEW.super_cohort_zoology_indexed,
			NEW.cohort_zoology_ref,
			NEW.cohort_zoology_indexed,
			NEW.sub_cohort_zoology_ref,
			NEW.sub_cohort_zoology_indexed,
			NEW.infra_cohort_zoology_ref,
			NEW.infra_cohort_zoology_indexed,
			NEW.super_order_ref,
			NEW.super_order_indexed,
			NEW.order_ref,
			NEW.order_indexed,
			NEW.sub_order_ref,
			NEW.sub_order_indexed,
			NEW.infra_order_ref,
			NEW.infra_order_indexed,
			NEW.section_zoology_ref,
			NEW.section_zoology_indexed,
			NEW.sub_section_zoology_ref,
			NEW.sub_section_zoology_indexed,
			NEW.super_family_ref,
			NEW.super_family_indexed,
			NEW.family_ref,
			NEW.family_indexed,
			NEW.sub_family_ref,
			NEW.sub_family_indexed,
			NEW.infra_family_ref,
			NEW.infra_family_indexed,
			NEW.super_tribe_ref,
			NEW.super_tribe_indexed,
			NEW.tribe_ref,
			NEW.tribe_indexed,
			NEW.sub_tribe_ref,
			NEW.sub_tribe_indexed,
			NEW.infra_tribe_ref,
			NEW.infra_tribe_indexed,
			NEW.genus_ref,
			NEW.genus_indexed,
			NEW.sub_genus_ref,
			NEW.sub_genus_indexed,
			NEW.section_botany_ref,
			NEW.section_botany_indexed,
			NEW.sub_section_botany_ref,
			NEW.sub_section_botany_indexed,
			NEW.serie_ref,
			NEW.serie_indexed,
			NEW.sub_serie_ref,
			NEW.sub_serie_indexed,
			NEW.super_species_ref,
			NEW.super_species_indexed,
			NEW.species_ref,
			NEW.species_indexed,
			NEW.sub_species_ref,
			NEW.sub_species_indexed,
			NEW.variety_ref,
			NEW.variety_indexed,
			NEW.sub_variety_ref,
			NEW.sub_variety_indexed,
			NEW.form_ref,
			NEW.form_indexed,
			NEW.sub_form_ref,
			NEW.sub_form_indexed,
			NEW.abberans_ref,
			NEW.abberans_indexed
		FROM taxa AS pt
		WHERE pt.id = NEW.parent_ref;
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;

/***
* Trigger function fct_cpy_cascade_children_indexed_names
* Update the corresponding givenlevel_indexed and givenlevel_ref of related children when name of a catalogue unit have been updated
*/
CREATE OR REPLACE FUNCTION fct_cpy_cascade_children_indexed_names (table_name varchar, new_level_ref template_classifications.level_ref%TYPE, new_name_indexed template_classifications.name_indexed%TYPE, new_id integer) RETURNS boolean
AS $$
DECLARE
	level_prefix catalogue_levels.level_sys_name%TYPE;
	response boolean default false;
BEGIN

	SELECT level_sys_name INTO level_prefix FROM catalogue_levels WHERE id = new_level_ref;
	IF level_prefix IS NOT NULL THEN
		EXECUTE 'UPDATE ' || 
			quote_ident(table_name) || 
			' SET ' || quote_ident(level_prefix || '_indexed') || ' = ' || quote_literal(new_name_indexed) || 
			' WHERE ' || quote_ident(level_prefix || '_ref') || ' = ' || new_id || 
			'   AND ' || quote_ident('id') || ' <> ' || new_id ;
		response := true;
	END IF;
	return response;
EXCEPTION
	WHEN OTHERS THEN
		return response;
END;
$$ LANGUAGE plpgsql;

/***
* Trigger function fct_cpy_fullToIndex
* Call the fulltoIndex function for different tables
*/
CREATE OR REPLACE FUNCTION fct_cpy_fullToIndex() RETURNS trigger
AS $$
DECLARE
	oldValue varchar;
	oldCodePrefix varchar;
	oldCode varchar;
	oldCodeSuffix varchar;
BEGIN
	IF TG_OP = 'UPDATE' THEN
		IF TG_TABLE_NAME = 'catalogue_properties' THEN
			oldValue := OLD.property_tool;
			IF NEW.property_tool <> oldValue THEN
				NEW.property_tool_indexed := COALESCE(fullToIndex(NEW.property_tool),'');
			END IF;
			oldValue := OLD.property_sub_type;
			IF NEW.property_sub_type <> oldValue THEN
				NEW.property_sub_type_indexed := COALESCE(fullToIndex(NEW.property_sub_type),'');
			END IF;
			oldValue := OLD.property_method;
			IF NEW.property_method <> oldValue THEN
				NEW.property_method_indexed := COALESCE(fullToIndex(NEW.property_method),'');
			END IF;
		ELSIF TG_TABLE_NAME = 'chronostratigraphy' THEN
			oldValue := OLD.name;
			IF NEW.name <> oldValue THEN
				NEW.name_indexed := fullToIndex(NEW.name);
			END IF;
		ELSIF TG_TABLE_NAME = 'expeditions' THEN
			oldValue := OLD.name;
			IF NEW.name <> oldValue THEN
				NEW.name_indexed := fullToIndex(NEW.name);
			END IF;
		ELSIF TG_TABLE_NAME = 'habitats' THEN
			oldValue := OLD.code;
			IF NEW.code <> oldValue THEN
				NEW.code_indexed := fullToIndex(NEW.code);
			END IF;
		ELSIF TG_TABLE_NAME = 'identifications' THEN
			oldValue := OLD.value_defined;
			IF NEW.value_defined <> oldValue THEN
				NEW.value_defined_indexed := COALESCE(fullToIndex(NEW.value_defined),'');
			END IF;
		ELSIF TG_TABLE_NAME = 'lithology' THEN
			oldValue := OLD.name;
			IF NEW.name <> oldValue THEN
				NEW.name_indexed := fullToIndex(NEW.name);
			END IF;
		ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
			oldValue := OLD.name;
			IF NEW.name <> oldValue THEN
				NEW.name_indexed := fullToIndex(NEW.name);
			END IF;
		ELSIF TG_TABLE_NAME = 'mineralogy' THEN
			oldValue := OLD.name;
			IF NEW.name <> oldValue THEN
				NEW.name_indexed := fullToIndex(NEW.name);
			END IF;
			oldValue := OLD.formule;
			IF NEW.formule <> oldValue THEN
				NEW.formule_indexed := fullToIndex(NEW.formule);
			END IF;
		ELSIF TG_TABLE_NAME = 'multimedia' THEN
			oldValue := OLD.title;
			IF NEW.title <> oldValue THEN
				NEW.title_indexed := fullToIndex(NEW.title);
			END IF;
		ELSIF TG_TABLE_NAME = 'multimedia_keywords' THEN
			oldValue := OLD.keyword;
			IF NEW.keyword <> oldValue THEN
				NEW.keyword_indexed := fullToIndex(NEW.keyword);
			END IF;
		ELSIF TG_TABLE_NAME = 'people' THEN
			oldValue := OLD.formated_name;
			IF NEW.formated_name <> oldValue THEN
				NEW.formated_name_indexed := fullToIndex(NEW.formated_name);
			END IF;
		ELSIF TG_TABLE_NAME = 'multimedia_codes' THEN
			oldCodePrefix := OLD.code_prefix;
			oldCode := OLD.code;
			oldCodeSuffix := OLD.code_suffix;
			IF NEW.code <> oldCode OR NEW.code_prefix <> oldCodePrefix OR NEW.code_suffix <> oldCodeSuffix THEN
				NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
			END IF;
		ELSIF TG_TABLE_NAME = 'specimen_parts_codes' THEN
			oldCodePrefix := OLD.code_prefix;
			oldCode := OLD.code;
			oldCodeSuffix := OLD.code_suffix;
			IF NEW.code <> oldCode OR NEW.code_prefix <> oldCodePrefix OR NEW.code_suffix <> oldCodeSuffix THEN
				NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
			END IF;
		ELSIF TG_TABLE_NAME = 'specimens_codes' THEN
			oldCodePrefix := OLD.code_prefix;
			oldCode := OLD.code;
			oldCodeSuffix := OLD.code_suffix;
			--WARNING 	oldCode varchar;
			IF NEW.code <> oldCode::integer OR NEW.code_prefix <> oldCodePrefix OR NEW.code_suffix <> oldCodeSuffix THEN
				NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
			END IF;
		ELSIF TG_TABLE_NAME = 'tag_groups' THEN
			oldValue := OLD.group_name;
			IF NEW.group_name <> oldValue THEN
				NEW.group_name_indexed := fullToIndex(NEW.group_name);
			END IF;
		ELSIF TG_TABLE_NAME = 'tags' THEN
			oldValue := OLD.label;
			IF NEW.label <> oldValue THEN
				NEW.label_indexed := fullToIndex(NEW.label);
			END IF;
		ELSIF TG_TABLE_NAME = 'taxa' THEN
			oldValue := OLD.name;
			IF NEW.name <> oldValue THEN
				NEW.name_indexed := fullToIndex(NEW.name);
			END IF;
		ELSIF TG_TABLE_NAME = 'users' THEN
			oldValue := OLD.formated_name;
			IF NEW.formated_name <> oldValue THEN
				NEW.formated_name_indexed := fullToIndex(NEW.formated_name);
			END IF;
		ELSIF TG_TABLE_NAME = 'vernacular_names' THEN
			oldValue := OLD.name;
			IF NEW.name <> oldValue THEN
				NEW.name_indexed := fullToIndex(NEW.name);
			END IF;
		END IF;	
	ELSIF TG_OP = 'INSERT' THEN
		IF TG_TABLE_NAME = 'catalogue_properties' THEN
			NEW.property_tool_indexed := COALESCE(fullToIndex(NEW.property_tool),'');
			NEW.property_sub_type_indexed := COALESCE(fullToIndex(NEW.property_sub_type),'');
			NEW.property_method_indexed := COALESCE(fullToIndex(NEW.property_method),'');
		ELSIF TG_TABLE_NAME = 'chronostratigraphy' THEN
			NEW.name_indexed := fullToIndex(NEW.name);
		ELSIF TG_TABLE_NAME = 'expeditions' THEN
			NEW.name_indexed := fullToIndex(NEW.name);
		ELSIF TG_TABLE_NAME = 'habitats' THEN
			NEW.code_indexed := fullToIndex(NEW.code);
		ELSIF TG_TABLE_NAME = 'identifications' THEN
			NEW.value_defined_indexed := COALESCE(fullToIndex(NEW.value_defined),'');
		ELSIF TG_TABLE_NAME = 'lithology' THEN
			NEW.name_indexed := fullToIndex(NEW.name);
		ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
			NEW.name_indexed := fullToIndex(NEW.name);
		ELSIF TG_TABLE_NAME = 'mineralogy' THEN
			NEW.name_indexed := fullToIndex(NEW.name);
			NEW.formule_indexed := fullToIndex(NEW.formule);
		ELSIF TG_TABLE_NAME = 'multimedia' THEN
			NEW.title_indexed := fullToIndex(NEW.title);
		ELSIF TG_TABLE_NAME = 'multimedia_keywords' THEN
			NEW.keyword_indexed := fullToIndex(NEW.keyword);
		ELSIF TG_TABLE_NAME = 'people' THEN
			NEW.formated_name_indexed := fullToIndex(NEW.formated_name);
		ELSIF TG_TABLE_NAME = 'multimedia_codes' THEN
			NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
		ELSIF TG_TABLE_NAME = 'specimen_parts_codes' THEN
			NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
		ELSIF TG_TABLE_NAME = 'specimens_codes' THEN
			NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
		ELSIF TG_TABLE_NAME = 'tag_groups' THEN
			NEW.group_name_indexed := fullToIndex(NEW.group_name);
		ELSIF TG_TABLE_NAME = 'tags' THEN
			NEW.label_indexed := fullToIndex(NEW.label);
		ELSIF TG_TABLE_NAME = 'taxa' THEN
			NEW.name_indexed := fullToIndex(NEW.name);
		ELSIF TG_TABLE_NAME = 'users' THEN
			NEW.formated_name_indexed := fullToIndex(NEW.formated_name);
		ELSIF TG_TABLE_NAME = 'vernacular_names' THEN
			NEW.name_indexed := fullToIndex(NEW.name);
		ELSIF TG_TABLE_NAME = 'vernacular_names' THEN
			NEW.name_indexed := fullToIndex(NEW.name);
		END IF;	
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;




/***
* function fct_chk_collectionsInstitutionIsMoral
* Check if an institution referenced in collections is moral
* return Boolean
*/
CREATE OR REPLACE FUNCTION fct_chk_PeopleIsMoral(people_ref people.id%TYPE) RETURNS boolean
AS $$
DECLARE
	is_physical boolean;
BEGIN
	SELECT NOT people.is_physical INTO is_physical FROM people WHERE people.id=people_ref;
	return is_physical;
END;
$$ LANGUAGE plpgsql;

/***
* fct_clr_specialstatus
* Check the type(special status) on specimen_individuals and update the search and group type
* to be conform to the std
*/
CREATE OR REPLACE FUNCTION fct_clr_specialstatus() RETURNS TRIGGER
AS $$
BEGIN

	-- IF Type not changed
	IF TG_OP = 'UPDATE' THEN
		IF OLD.type = NEW.type THEN
			RETURN NEW;
		END IF;
	END IF;
	
	IF NEW.type = 'specimen' THEN
		NEW.type_search := '';
		NEW.type_group := '';
	END IF;
	
	IF NEW.type = 'type' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type';
	END IF;	
	
	IF NEW.type = 'subtype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type';
	END IF;

	IF NEW.type = 'allotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'allotype';
	END IF;

	IF NEW.type = 'cotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'syntype';
	END IF;

	IF NEW.type = 'genotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type';
	END IF;

	IF NEW.type = 'holotype' THEN
		NEW.type_search := 'holotype';
		NEW.type_group := 'holotype';
	END IF;

	IF NEW.type = 'hypotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'hypotype';
	END IF;

	IF NEW.type = 'lectotype' THEN
		NEW.type_search := 'lectotype';
		NEW.type_group := 'lectotype';
	END IF;

	IF NEW.type = 'locotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'locotype';
	END IF;

	IF NEW.type = 'neallotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type';
	END IF;

	IF NEW.type = 'neotype' THEN
		NEW.type_search := 'neotype';
		NEW.type_group := 'neotype';
	END IF;
	
	IF NEW.type = 'paralectotype' THEN
		NEW.type_search := 'paralectotype';
		NEW.type_group := 'paralectotype';
	END IF;

	IF NEW.type = 'paratype' THEN
		NEW.type_search := 'paratype';
		NEW.type_group := 'paratype';
	END IF;

	IF NEW.type = 'plastotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'plastotype';
	END IF;
	
	IF NEW.type = 'plesiotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'plesiotype';
	END IF;

	IF NEW.type = 'syntype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'syntype';
	END IF;
		
	IF NEW.type = 'topotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'topotype';
	END IF;
	
	IF NEW.type = 'type in litteris' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type in litteris';
	END IF;
	
	RETURN NEW;
EXCEPTION
	WHEN RAISE_EXCEPTION THEN
		return NULL;
END;
$$ LANGUAGE plpgsql;

/**
fct_compose_timestamp
Compose A timestamp with default value
-1 or null for hour, minute or second will take 0
0 or null for day, month or year will take 1
*/
CREATE OR REPLACE FUNCTION fct_compose_timestamp(day integer, month integer, year integer, hour integer, minute integer, second integer) RETURNS timestamp
AS $$
DECLARE
	nday integer;
	nmonth integer;
	nyear integer;
	nhour integer;
	nminute integer;
	nsecond integer;
	stamp_string varchar default '';
BEGIN

	IF day = 0 OR day IS NULL THEN
		nday := 1;
	ELSE
		nday := day;
	END IF;

	IF month = 0 OR month IS NULL THEN
		nmonth := 1;
	ELSE
		nmonth := month;
	END IF;
	
	IF year = 0 OR year IS NULL THEN
		nyear := 1;
	ELSE
		nyear := year;
	END IF;
			
	IF hour = -1 OR hour IS NULL THEN
		nhour := 0;
	ELSE
		nhour := hour;
	END IF;

	IF minute = -1 OR minute IS NULL THEN
		nminute := 0;
	ELSE
		nminute := day;
	END IF;

	IF second = -1 OR second IS NULL THEN
		nsecond := 0;
	ELSE
		nsecond := second;
	END IF;
	
	stamp_string := ''|| to_char(nyear,'0000') ||'-'|| nmonth ||'-'|| nday || ' ' ||
			to_char(nhour,'FM00') ||':'|| to_char(nminute,'FM00') ||':'|| to_char(nsecond,'FM00');
	
	RETURN stamp_string::TIMESTAMP;
END;
$$ LANGUAGE plpgsql;

/**
*fct_cpy_name_updt_impact_children
*When name of a unit is updated, impact the <given-level>_indexed field of related children.
*/

CREATE OR REPLACE FUNCTION fct_cpy_name_updt_impact_children() RETURNS trigger
AS $$
DECLARE
	level_prefix catalogue_levels.level_sys_name%TYPE;
BEGIN
	IF NEW.name_indexed <> OLD.name_indexed THEN
		IF TG_TABLE_NAME = 'chronostratigraphy' THEN
			SELECT
				CASE
					WHEN level_sys_name = 'eon' THEN
						NEW.name_indexed
					ELSE
						NEW.eon_indexed
				END as eon_indexed,
				CASE
                                        WHEN level_sys_name = 'era' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.era_indexed
                                END as era_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_era' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_era_indexed
                                END as sub_era_indexed,
				CASE
                                        WHEN level_sys_name = 'system' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.system_indexed
                                END as system_indexed,
				CASE
                                        WHEN level_sys_name = 'serie' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.serie_indexed
                                END as serie_indexed,
				CASE
                                        WHEN level_sys_name = 'stage' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.stage_indexed
                                END as stage_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_stage' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_stage_indexed
                                END as sub_stage_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_level_1' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_level_1_indexed
                                END as sub_level_1_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_level_2' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_level_2_indexed
                                END as sub_level_2_indexed
			INTO
				NEW.eon_indexed,
				NEW.era_indexed,
				NEW.sub_era_indexed,
				NEW.system_indexed,
				NEW.serie_indexed,
				NEW.stage_indexed,
				NEW.sub_stage_indexed,
				NEW.sub_level_1_indexed,
				NEW.sub_level_2_indexed
			FROM catalogue_levels as cl 
			WHERE cl.id = NEW.level_ref;
		ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
			SELECT
				CASE
					WHEN level_sys_name = 'group' THEN
						NEW.name_indexed
					ELSE
						NEW.group_indexed
				END as group_indexed,
				CASE
                                        WHEN level_sys_name = 'formation' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.formation_indexed
                                END as formation_indexed,
				CASE
                                        WHEN level_sys_name = 'member' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.member_indexed
                                END as member_indexed,
				CASE
                                        WHEN level_sys_name = 'layer' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.layer_indexed
                                END as layer_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_level_1' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_level_1_indexed
                                END as sub_level_1_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_level_2' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_level_2_indexed
                                END as sub_level_2_indexed
			INTO
				NEW.group_indexed,
				NEW.formation_indexed,
				NEW.member_indexed,
				NEW.layer_indexed,
				NEW.sub_level_1_indexed,
				NEW.sub_level_2_indexed
			FROM catalogue_levels
			WHERE id = NEW.level_ref;
		ELSIF TG_TABLE_NAME = 'mineralogy' THEN
			SELECT
				CASE
					WHEN level_sys_name = 'unit_class' THEN
						NEW.name_indexed
					ELSE
						NEW.unit_class_indexed
				END as unit_class_indexed,
				CASE
                                        WHEN level_sys_name = 'unit_division' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.unit_division_indexed
                                END as unit_division_indexed,
				CASE
                                        WHEN level_sys_name = 'unit_family' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.unit_family_indexed
                                END as unit_family_indexed,
				CASE
                                        WHEN level_sys_name = 'unit_group' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.unit_group_indexed
                                END as unit_group_indexed,
				CASE
                                        WHEN level_sys_name = 'unit_variety' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.unit_variety_indexed
                                END as unit_variety_indexed
			INTO
				NEW.unit_class_indexed,
				NEW.unit_division_indexed,
				NEW.unit_family_indexed,
				NEW.unit_group_indexed,
				NEW.unit_variety_indexed
			FROM catalogue_levels
			WHERE id = NEW.level_ref;
		ELSIF TG_TABLE_NAME = 'taxa' THEN
			SELECT
				CASE
					WHEN level_sys_name = 'domain' THEN
						NEW.name_indexed
					ELSE
						NEW.domain_indexed
				END as domain_indexed,
				CASE
                                        WHEN level_sys_name = 'kingdom' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.kingdom_indexed
                                END as kingdom_indexed,
				CASE
                                        WHEN level_sys_name = 'super_phylum' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_phylum_indexed
                                END as super_phylum_indexed,
				CASE
                                        WHEN level_sys_name = 'phylum' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.phylum_indexed
                                END as phylum_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_phylum' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_phylum_indexed
                                END as sub_phylum_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_phylum' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_phylum_indexed
                                END as infra_phylum_indexed,
				CASE
                                        WHEN level_sys_name = 'super_cohort_botany' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_cohort_botany_indexed
                                END as super_cohort_botany_indexed,
				CASE
                                        WHEN level_sys_name = 'cohort_botany' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.cohort_botany_indexed
                                END as cohort_botany_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_cohort_botany' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_cohort_botany_indexed
                                END as sub_cohort_botany_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_cohort_botany' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_cohort_botany_indexed
                                END as infra_cohort_botany_indexed,
				CASE
                                        WHEN level_sys_name = 'super_class' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_class_indexed
                                END as super_class_indexed,
				CASE
                                        WHEN level_sys_name = 'class' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.class_indexed
                                END as class_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_class' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_class_indexed
                                END as sub_class_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_class' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_class_indexed
                                END as infra_class_indexed,
				CASE
                                        WHEN level_sys_name = 'super_division' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_division_indexed
                                END as super_division_indexed,
				CASE
                                        WHEN level_sys_name = 'division' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.division_indexed
                                END as division_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_division' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_division_indexed
                                END as sub_division_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_division' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_division_indexed
                                END as infra_division_indexed,
				CASE
                                        WHEN level_sys_name = 'super_legion' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_legion_indexed
                                END as super_legion_indexed,
				CASE
                                        WHEN level_sys_name = 'legion' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.legion_indexed
                                END as legion_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_legion' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_legion_indexed
                                END as sub_legion_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_legion' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_legion_indexed
                                END as infra_legion_indexed,
				CASE
                                        WHEN level_sys_name = 'super_cohort_zoology' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_cohort_zoology_indexed
                                END as super_cohort_zoology_indexed,
				CASE
                                        WHEN level_sys_name = 'cohort_zoology' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.cohort_zoology_indexed
                                END as cohort_zoology_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_cohort_zoology' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_cohort_zoology_indexed
                                END as sub_cohort_zoology_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_cohort_zoology' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_cohort_zoology_indexed
                                END as infra_cohort_zoology_indexed,
				CASE
                                        WHEN level_sys_name = 'super_order' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_order_indexed
                                END as super_order_indexed,
				CASE
                                        WHEN level_sys_name = 'order' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.order_indexed
                                END as order_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_order' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_order_indexed
                                END as sub_order_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_order' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_order_indexed
                                END as infra_order_indexed,
				CASE
                                        WHEN level_sys_name = 'section_zoology' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.section_zoology_indexed
                                END as section_zoology_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_section_zoology' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_section_zoology_indexed
                                END as sub_section_zoology_indexed,
				CASE
                                        WHEN level_sys_name = 'super_family' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_family_indexed
                                END as super_family_indexed,
				CASE
                                        WHEN level_sys_name = 'family' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.family_indexed
                                END as family_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_family' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_family_indexed
                                END as sub_family_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_family' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_family_indexed
                                END as infra_family_indexed,
				CASE
                                        WHEN level_sys_name = 'super_tribe' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_tribe_indexed
                                END as super_tribe_indexed,
				CASE
                                        WHEN level_sys_name = 'tribe' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.tribe_indexed
                                END as tribe_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_tribe' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_tribe_indexed
                                END as sub_tribe_indexed,
				CASE
                                        WHEN level_sys_name = 'infra_tribe' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.infra_tribe_indexed
                                END as infra_tribe_indexed,
				CASE
                                        WHEN level_sys_name = 'genus' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.genus_indexed
                                END as genus_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_genus' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_genus_indexed
                                END as sub_genus_indexed,
				CASE
                                        WHEN level_sys_name = 'section_botany' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.section_botany_indexed
                                END as section_botany_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_section_botany' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_section_botany_indexed
                                END as sub_section_botany_indexed,
				CASE
                                        WHEN level_sys_name = 'serie' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.serie_indexed
                                END as serie_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_serie' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_serie_indexed
                                END as sub_serie_indexed,
				CASE
                                        WHEN level_sys_name = 'super_species' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.super_species_indexed
                                END as super_species_indexed,
				CASE
                                        WHEN level_sys_name = 'species' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.species_indexed
                                END as species_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_species' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_species_indexed
                                END as sub_species_indexed,
				CASE
                                        WHEN level_sys_name = 'variety' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.variety_indexed
                                END as variety_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_variety' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_variety_indexed
                                END as sub_variety_indexed,
				CASE
                                        WHEN level_sys_name = 'form' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.form_indexed
                                END as form_indexed,
				CASE
                                        WHEN level_sys_name = 'sub_form' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.sub_form_indexed
                                END as sub_form_indexed,
				CASE
                                        WHEN level_sys_name = 'abberans' THEN
                                                NEW.name_indexed
                                        ELSE
                                                NEW.abberans_indexed
                                END as abberans_indexed
			INTO
				NEW.domain_indexed,
				NEW.kingdom_indexed,
				NEW.super_phylum_indexed,
				NEW.phylum_indexed,
				NEW.sub_phylum_indexed,
				NEW.infra_phylum_indexed,
				NEW.super_cohort_botany_indexed,
				NEW.cohort_botany_indexed,
				NEW.sub_cohort_botany_indexed,
				NEW.infra_cohort_botany_indexed,
				NEW.super_class_indexed,
				NEW.class_indexed,
				NEW.sub_class_indexed,
				NEW.infra_class_indexed,
				NEW.super_division_indexed,
				NEW.division_indexed,
				NEW.sub_division_indexed,
				NEW.infra_division_indexed,
				NEW.super_legion_indexed,
				NEW.legion_indexed,
				NEW.sub_legion_indexed,
				NEW.infra_legion_indexed,
				NEW.super_cohort_zoology_indexed,
				NEW.cohort_zoology_indexed,
				NEW.sub_cohort_zoology_indexed,
				NEW.infra_cohort_zoology_indexed,
				NEW.super_order_indexed,
				NEW.order_indexed,
				NEW.sub_order_indexed,
				NEW.infra_order_indexed,
				NEW.section_zoology_indexed,
				NEW.sub_section_zoology_indexed,
				NEW.super_family_indexed,
				NEW.family_indexed,
				NEW.sub_family_indexed,
				NEW.infra_family_indexed,
				NEW.super_tribe_indexed,
				NEW.tribe_indexed,
				NEW.sub_tribe_indexed,
				NEW.infra_tribe_indexed,
				NEW.genus_indexed,
				NEW.sub_genus_indexed,
				NEW.section_botany_indexed,
				NEW.sub_section_botany_indexed,
				NEW.serie_indexed,
				NEW.sub_serie_indexed,
				NEW.super_species_indexed,
				NEW.species_indexed,
				NEW.sub_species_indexed,
				NEW.variety_indexed,
				NEW.sub_variety_indexed,
				NEW.form_indexed,
				NEW.sub_form_indexed,
				NEW.abberans_indexed
			FROM catalogue_levels
			WHERE id = NEW.level_ref;
		ELSIF TG_TABLE_NAME = 'lithology' THEN
		END IF;
		IF NOT fct_cpy_cascade_children_indexed_names (TG_TABLE_NAME::varchar, NEW.level_ref::integer, NEW.name_indexed::varchar, NEW.id::integer) THEN
			RAISE EXCEPTION 'Impossible to impact children names';
		END IF;
	END IF;
	RETURN NEW;
/*EXCEPTION
	WHEN OTHERS THEN
		RETURN OLD;
*/
END;
$$ LANGUAGE plpgsql;

/* 
fct_compose_date
Compose a date with default value call compose_timestamp
*/
CREATE OR REPLACE FUNCTION fct_compose_date(day integer, month integer, year integer) RETURNS date
AS $$
BEGIN
	RETURN fct_compose_timestamp(day, month, year, null, null, null)::date;
END;
$$ LANGUAGE plpgsql;

/**
* fct_clear_referencedRecord
* Clear referenced record id for a table on delete record
*/
CREATE OR REPLACE FUNCTION fct_clear_referencedRecord() RETURNS TRIGGER
AS $$
BEGIN
	DELETE FROM catalogue_authors WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM comments WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM catalogue_properties WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM identifications WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM expertises WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM class_vernacular_names WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM record_visibilities WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM users_workflow WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM collection_maintenance WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM associated_multimedia WHERE table_name = TG_TABLE_NAME AND record_id = OLD.id;
	RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_remove_array_elem(IN in_array anyarray, IN elem anyelement,OUT out_array anyarray)
AS $$
BEGIN
	SELECT array(select s FROM fct_explode_array (in_array)  as s WHERE s != elem) INTO out_array;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_explode_array(in_array anyarray) returns setof anyelement as
$$
    select ($1)[s] from generate_series(1,array_upper($1, 1)) as s;
$$
LANGUAGE sql immutable;

/**
* fct_clear_referencedPeople
* Clear referenced people id for a table on delete record
*/
CREATE OR REPLACE FUNCTION fct_clear_referencedPeople() RETURNS TRIGGER
AS $$
BEGIN
	UPDATE catalogue_relationships SET defined_by_ordered_ids_list = fct_remove_array_elem(defined_by_ordered_ids_list,OLD.id)
		WHERE defined_by_ordered_ids_list @> ARRAY[OLD.id];
		
	UPDATE catalogue_authors SET authors_ordered_ids_list = fct_remove_array_elem(authors_ordered_ids_list,OLD.id),
		defined_by_ordered_ids_list =  fct_remove_array_elem(defined_by_ordered_ids_list,OLD.id)
		WHERE authors_ordered_ids_list @> ARRAY[OLD.id] OR defined_by_ordered_ids_list @> ARRAY[OLD.id];
	
	UPDATE catalogue_properties SET defined_by_ordered_ids_list = fct_remove_array_elem(defined_by_ordered_ids_list,OLD.id)
		WHERE defined_by_ordered_ids_list @> ARRAY[OLD.id];
	
	UPDATE identifications SET identifiers_ordered_ids_list = fct_remove_array_elem(identifiers_ordered_ids_list,OLD.id),
		defined_by_ordered_ids_list =  fct_remove_array_elem(defined_by_ordered_ids_list,OLD.id)
		WHERE identifiers_ordered_ids_list @> ARRAY[OLD.id] OR defined_by_ordered_ids_list @> ARRAY[OLD.id];
	
	UPDATE expertises SET defined_by_ordered_ids_list = fct_remove_array_elem(defined_by_ordered_ids_list,OLD.id)
		WHERE defined_by_ordered_ids_list @> ARRAY[OLD.id];		
	
	UPDATE class_vernacular_names SET defined_by_ordered_ids_list = fct_remove_array_elem(defined_by_ordered_ids_list,OLD.id)
		WHERE defined_by_ordered_ids_list @> ARRAY[OLD.id];	
	
	UPDATE specimens_accompanying SET defined_by_ordered_ids_list = fct_remove_array_elem(defined_by_ordered_ids_list,OLD.id)
		WHERE defined_by_ordered_ids_list @> ARRAY[OLD.id];

	RETURN NEW;
END;
$$ LANGUAGE plpgsql;

/**
fct_cpy_toFullText
Copy the Full_text version of some fields
Use language if av. or 'simple' if not
*/
CREATE OR REPLACE FUNCTION fct_cpy_toFullText() RETURNS TRIGGER
AS
$$
BEGIN
	IF TG_OP = 'INSERT' THEN
		IF TG_TABLE_NAME = 'comments' THEN
			NEW.comment_ts := to_tsvector(NEW.comment_language_full_text::regconfig, NEW.comment);
		ELSEIF TG_TABLE_NAME = 'identifications' THEN
			NEW.value_defined_ts := to_tsvector(NEW.value_defined);
		ELSEIF TG_TABLE_NAME = 'people_addresses' THEN
			NEW.address_parts_ts := to_tsvector(NEW.address_parts);
		ELSEIF TG_TABLE_NAME = 'users_addresses' THEN
			NEW.address_parts_ts := to_tsvector(NEW.address_parts);
		ELSEIF TG_TABLE_NAME = 'multimedia' THEN
			NEW.descriptive_ts := to_tsvector(NEW.descriptive_language_full_text::regconfig, NEW.title ||' '|| NEW.subject);
		ELSEIF TG_TABLE_NAME = 'collection_maintenance' THEN
			NEW.description_ts := to_tsvector(NEW.language_full_text::regconfig,NEW.description);
		ELSEIF TG_TABLE_NAME = 'expeditions' THEN
			NEW.name_ts := to_tsvector(NEW.name_language_full_text::regconfig, NEW.name);
		ELSEIF TG_TABLE_NAME = 'habitats' THEN
			NEW.description_ts := to_tsvector(NEW.description_language_full_text::regconfig, NEW.description);
		ELSEIF TG_TABLE_NAME = 'vernacular_names' THEN
			NEW.name_ts := to_tsvector(NEW.country_language_full_text::regconfig, NEW.name);
		END IF;
	ELSE
		IF TG_TABLE_NAME = 'comments' THEN
			IF OLD.comment != NEW.comment OR OLD.comment_language_full_text != NEW.comment_language_full_text THEN
				NEW.comment_ts := to_tsvector(NEW.comment_language_full_text::regconfig, NEW.comment);
			END IF;
		ELSEIF TG_TABLE_NAME = 'identifications' THEN
			IF OLD.value_defined != NEW.value_defined THEN 
				NEW.value_defined_ts := to_tsvector(NEW.value_defined);
			END IF;
		ELSEIF TG_TABLE_NAME = 'people_addresses' THEN
			IF OLD.address_parts != NEW.address_parts THEN
				NEW.address_parts_ts := to_tsvector(NEW.address_parts);
			END IF;
		ELSEIF TG_TABLE_NAME = 'users_addresses' THEN
			IF OLD.address_parts != NEW.address_parts THEN
				NEW.address_parts_ts := to_tsvector(NEW.address_parts);
			END IF;
		ELSEIF TG_TABLE_NAME = 'multimedia' THEN
			IF OLD.title != NEW.title OR  OLD.subject != NEW.subject OR OLD.descriptive_language_full_text != NEW.descriptive_language_full_text THEN
				NEW.descriptive_ts := to_tsvector(NEW.descriptive_language_full_text::regconfig, NEW.title ||' '|| NEW.subject);
			END IF;
		ELSEIF TG_TABLE_NAME = 'collection_maintenance' THEN
			IF OLD.description != NEW.description OR OLD.language_full_text != NEW.language_full_text THEN
				NEW.description_ts := to_tsvector(NEW.language_full_text::regconfig, NEW.description);
			END IF;
		ELSEIF TG_TABLE_NAME = 'expeditions' THEN
			IF OLD.name != NEW.name OR OLD.name_language_full_text != NEW.name_language_full_text THEN
				NEW.name_ts := to_tsvector(NEW.name_language_full_text::regconfig, NEW.name);
			END IF;
		ELSEIF TG_TABLE_NAME = 'habitats' THEN
			IF OLD.description != NEW.descriptiont OR OLD.description_language_full_text != NEW.description_language_full_text THEN
				NEW.description_ts := to_tsvector(NEW.description_language_full_text::regconfig, NEW.description);
			END IF;
		ELSEIF TG_TABLE_NAME = 'vernacular_names' THEN
			IF OLD.name != NEW.name OR OLD.country_language_full_text != NEW.country_language_full_text THEN
				NEW.name_ts := to_tsvector(NEW.country_language_full_text::regconfig, NEW.name);
			END IF;
		END IF;
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;

/**
* fct_chk_possible_upper_levels
* When inserting or updating a hierarchical unit, checks, considering parent level, that unit level is ok (depending on definitions given in possible_upper_levels_table)
*/
CREATE OR REPLACE FUNCTION fct_chk_possible_upper_level (table_name varchar, new_parent_ref template_classifications.parent_ref%TYPE, new_level_ref template_classifications.level_ref%TYPE, new_id integer) RETURNS boolean
AS $$
DECLARE
	response boolean default false;
BEGIN
	IF new_id = 0 OR (new_parent_ref = 0 AND new_level_ref IN (1, 55, 64, 70)) THEN
		response := true;
	ELSE
		EXECUTE 'select count(*)::integer::boolean ' ||
			'from possible_upper_levels ' ||
			'where level_ref = ' || new_level_ref || 
			'  and level_upper_ref = (select level_ref from ' || quote_ident(table_name) || ' where id = ' || new_parent_ref || ')'
		INTO response;
	END IF;
	RETURN response;
EXCEPTION
	WHEN OTHERS THEN
		RETURN response;
END;
$$ LANGUAGE plpgsql;

/**
fct_cas_userType
Copy the new dbuser type if it's changed
users_login_infos db_user_type
*/
CREATE OR REPLACE FUNCTION fct_cas_userType() RETURNS TRIGGER
AS $$
DECLARE
	still_mgr boolean;
BEGIN

	IF NEW.db_user_type = OLD.db_user_type THEN
		RETURN NEW;
	END IF;
	
	/** Copy to other fields **/
	UPDATE record_visibilities SET db_user_type=NEW.db_user_type WHERE user_ref=NEW.user_ref;
	UPDATE collections_fields_visibilities SET db_user_type=NEW.db_user_type WHERE user_ref=NEW.user_ref;
	UPDATE users_coll_rights_asked SET db_user_type=NEW.db_user_type WHERE user_ref=NEW.user_ref;
	
	
	/** IF REVOKE ***/
	IF NEW.db_user_type < OLD.db_user_type THEN
		/*Each number of this suite represent a right on the collection: 1 for read, 2 for insert, 4 for update and 8 for delete*/
		/*db user type 1 for registered user, 2 for encoder, 4 for collection manager, 8 for system admin,*/
		IF OLD.db_user_type >= 4 THEN
			/** If retrograde from collection_man, remove all collection administrated **/
			SELECT count(*) != 0 INTO still_mgr FROM collections WHERE main_manager_ref = NEW.user_ref;
			IF still_mgr THEN
				RAISE EXCEPTION 'Still Manager in some Collections.';
			END IF;
			DELETE FROM collections_admin WHERE user_ref = NEW.user_ref;
		END IF;
		
		IF OLD.db_user_type >= 2 AND NEW.db_user_type = 1 THEN
			/** If retrograde to register , remove write/insert/update rights**/
			UPDATE collections_rights SET rights=1 WHERE user_ref=NEW.user_ref;
		END IF;
	END IF;
	RETURN NEW;
END;
$$
LANGUAGE plpgsql;

/**
* fct_cpy_update_levels_or_parent_cascade
* Test that new level and new parent definitions of a unit fits rules of "possible_upper_levels"
* If level updated, test that direct children can still be attached to unit updated
* If all tests passed, update hierarchical structure of unit updated and of all children related
*/
CREATE OR REPLACE FUNCTION fct_cpy_update_levels_or_parent_cascade() RETURNS TRIGGER
AS $$
DECLARE
	level_sys_name catalogue_levels.level_sys_name%TYPE;
	children_ready boolean default false;
BEGIN
	IF NEW.level_ref <> OLD.level_ref OR NEW.parent_ref <> OLD.parent_ref THEN
		IF NOT fct_chk_possible_upper_level(TG_TABLE_NAME::varchar, NEW.parent_ref::integer, NEW.level_ref::integer, NEW.id::integer) THEN
			RAISE EXCEPTION 'The modification of level and/or parent reference is not allowed, because unit modified won''t follow the rules of possible upper level attachement';
		END IF;
		IF NEW.level_ref <> OLD.level_ref THEN
			SELECT cl.level_sys_name INTO level_sys_name FROM catalogue_levels AS cl WHERE cl.id = NEW.level_ref;
			EXECUTE 'SELECT (SELECT COUNT(*) FROM (SELECT DISTINCT tab1.level_ref ' ||
				'			       FROM ' || quote_ident(TG_TABLE_NAME::varchar) || ' AS tab1 ' ||
				'			       WHERE tab1.level_ref IN (' || 
				'							SELECT pul1.level_ref ' ||
				'							FROM possible_upper_levels AS pul1 ' ||
				'							WHERE pul1.level_upper_ref = ' || OLD.level_ref ||
				'						       ) ' ||
				'				 AND parent_ref = ' || OLD.id ||
				'			      ) AS c1' ||
				'	) ' ||
				' = ' ||
				'	(SELECT COUNT(*) FROM (SELECT DISTINCT pul2.level_ref ' ||
				'			       FROM possible_upper_levels AS pul2 ' ||
				'			       WHERE pul2.level_upper_ref = ' || NEW.level_ref || ' ' ||
				'				 AND pul2.level_ref IN (SELECT DISTINCT tab2.level_ref ' || 
				'							FROM ' || quote_ident(TG_TABLE_NAME::varchar) || ' AS tab2 ' ||
				'							WHERE tab2.level_ref IN (' ||
				'										 SELECT pul3.level_ref ' ||
				'										 FROM possible_upper_levels AS pul3 ' ||
				'										 WHERE pul3.level_upper_ref = ' || OLD.level_ref ||
				'										) ' ||
				'							AND parent_ref = ' || OLD.id ||
				'						       ) ' ||
				'			      ) as c2 ' ||
				'	) '
			INTO children_ready;
			IF NOT children_ready THEN
				RAISE EXCEPTION 'Update of unit level break "possible_upper_levels" rule of direct children related. No modification of level for current unit allowed.';
			END IF;
			IF TG_TABLE_NAME = 'chronostratigraphy' THEN
				SELECT
					CASE 
						WHEN level_sys_name = 'eon' THEN
							NEW.eon_ref
						ELSE
							pc.eon_ref
					END AS eon_ref,
					CASE
                                                WHEN level_sys_name = 'eon' THEN
                                                        NEW.eon_indexed
                                                ELSE
                                                        pc.eon_indexed
                                        END AS eon_indexed,
					CASE 
						WHEN level_sys_name = 'era' THEN
							NEW.era_ref
						ELSE
							pc.era_ref
					END AS era_ref,
					CASE
                                                WHEN level_sys_name = 'era' THEN
                                                        NEW.era_indexed
                                                ELSE
                                                        pc.era_indexed
                                        END AS era_indexed,
					CASE 
						WHEN level_sys_name = 'sub_era' THEN
							NEW.sub_era_ref
						ELSE
							pc.sub_era_ref
					END AS sub_era_ref,
					CASE
                                                WHEN level_sys_name = 'sub_era' THEN
                                                        NEW.sub_era_indexed
                                                ELSE
                                                        pc.sub_era_indexed
                                        END AS sub_era_indexed,
					CASE 
						WHEN level_sys_name = 'system' THEN
							NEW.system_ref
						ELSE
							pc.system_ref
					END AS system_ref,
					CASE
                                                WHEN level_sys_name = 'system' THEN
                                                        NEW.system_indexed
                                                ELSE
                                                        pc.system_indexed
                                        END AS system_indexed,
					CASE 
						WHEN level_sys_name = 'serie' THEN
							NEW.serie_ref
						ELSE
							pc.serie_ref
					END AS serie_ref,
					CASE
                                                WHEN level_sys_name = 'serie' THEN
                                                        NEW.serie_indexed
                                                ELSE
                                                        pc.serie_indexed
                                        END AS serie_indexed,
					CASE 
						WHEN level_sys_name = 'stage' THEN
							NEW.stage_ref
						ELSE
							pc.stage_ref
					END AS stage_ref,
					CASE
                                                WHEN level_sys_name = 'stage' THEN
                                                        NEW.stage_indexed
                                                ELSE
                                                        pc.stage_indexed
                                        END AS stage_indexed,
					CASE 
						WHEN level_sys_name = 'sub_stage' THEN
							NEW.sub_stage_ref
						ELSE
							pc.sub_stage_ref
					END AS sub_stage_ref,
					CASE
                                                WHEN level_sys_name = 'sub_stage' THEN
                                                        NEW.sub_stage_indexed
                                                ELSE
                                                        pc.sub_stage_indexed
                                        END AS sub_stage_indexed,
					CASE 
						WHEN level_sys_name = 'sub_level_1' THEN
							NEW.sub_level_1_ref
						ELSE
							pc.sub_level_1_ref
					END AS sub_level_1_ref,
					CASE
                                                WHEN level_sys_name = 'sub_level_1' THEN
                                                        NEW.sub_level_1_indexed
                                                ELSE
                                                        pc.sub_level_1_indexed
                                        END AS sub_level_1_indexed,
					CASE 
						WHEN level_sys_name = 'sub_level_2' THEN
							NEW.sub_level_2_ref
						ELSE
							pc.sub_level_2_ref
					END AS sub_level_2_ref,
					CASE
                                                WHEN level_sys_name = 'sub_level_2' THEN
                                                        NEW.sub_level_2_indexed
                                                ELSE
                                                        pc.sub_level_2_indexed
                                        END AS sub_level_2_indexed
				INTO
					NEW.eon_ref,
					NEW.eon_indexed,
					NEW.era_ref,
					NEW.era_indexed,
					NEW.sub_era_ref,
					NEW.sub_era_indexed,
					NEW.system_ref,
					NEW.system_indexed,
					NEW.serie_ref,
					NEW.serie_indexed,
					NEW.stage_ref,
					NEW.stage_indexed,
					NEW.sub_stage_ref,
					NEW.sub_stage_indexed,
					NEW.sub_level_1_ref,
					NEW.sub_level_1_indexed,
					NEW.sub_level_2_ref,
					NEW.sub_level_2_indexed
				FROM chronostratigraphy AS pc
				WHERE id = NEW.parent_ref;
			END IF;
		END IF;
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;
