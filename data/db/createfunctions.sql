
CREATE AGGREGATE array_accum (anyelement)
(
  sfunc = array_append,
  stype = anyarray,
  initcond = '{}'
);


/***
* Trigger Function fct_cpy_specimensMainCode
* Automaticaly copy the "main" code from the specimen to the specimen parts
* When the collection of the specimen has the flag code_part_code_auto_copy
*/
CREATE OR REPLACE FUNCTION fct_cpy_specimensMainCode() RETURNS trigger
as $$
DECLARE
	spec_code codes%ROWTYPE;
	must_be_copied collections.code_part_code_auto_copy%TYPE;
BEGIN
	SELECT collections.code_part_code_auto_copy INTO must_be_copied FROM collections
			INNER JOIN specimens ON collections.id = specimens.collection_ref
			INNER JOIN specimen_individuals ON specimen_individuals.specimen_ref=specimens.id
				WHERE specimen_individuals.id = NEW.specimen_individual_ref;

	IF must_be_copied = true THEN

		INSERT INTO codes (referenced_relation, record_id, code_category, code_prefix, code_prefix_separator,  code_suffix_separator, code, code_suffix)
		(
			SELECT 'specimen_parts',NEW.id, code_category, code_prefix, code_prefix_separator, code_suffix_separator, code, code_suffix
                               FROM codes
        	               INNER JOIN specimens ON record_id = specimens.id
                	       INNER JOIN specimen_individuals ON specimen_individuals.specimen_ref=specimens.id
                        	WHERE referenced_relation = 'specimens'
		                  AND  specimen_individuals.id = NEW.specimen_individual_ref
                                  AND code_category = 'main'
		);
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
* function fct_chk_collectionsInstitutionIsMoral
* Check if an institution referenced in collections is moral
*/
CREATE OR REPLACE FUNCTION fct_chk_PeopleIsMoral() RETURNS TRIGGER
AS $$
DECLARE
  rec_exists boolean;
BEGIN
   SELECT is_physical FROM people WHERE id=NEW.institution_ref into rec_exists;
   
   IF rec_exists = TRUE THEN
    RAISE EXCEPTION 'You cannot link a moral person as Institution';
   END IF;

   RETURN NEW;
END;
$$
language plpgsql;


/***
* Function fct_chk_one_pref_language
* Check if there is only ONE preferred language for a user
* Return an error if there is already a preferred language true otherwise
*/
CREATE OR REPLACE FUNCTION fct_chk_one_pref_language() RETURNS TRIGGER
AS $$
DECLARE
  rec_exists integer;
BEGIN
    IF NEW.preferred_language = TRUE THEN
      rec_exists := 0;
      IF TG_TABLE_NAME = 'people_languages' THEN
        SELECT 1 INTO rec_exists WHERE EXISTS(
          SELECT id FROM people_languages WHERE people_ref = NEW.people_ref and preferred_language = NEW.preferred_language and id <> NEW.id
        );
      ELSIF TG_TABLE_NAME = 'users_languages' THEN
        SELECT 1 INTO rec_exists WHERE EXISTS(
         SELECT id FROM users_languages WHERE users_ref = NEW.users_ref and preferred_language = NEW.preferred_language and id <> NEW.id
        );
      END IF;
      IF rec_exists = 1 THEN
          RAISE EXCEPTION 'You cannot have more than 1 preferred language';
      END IF;
    END IF;

    RETURN NEW;
END;
$$
language plpgsql;


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
    temp_string := to_indexed;
    temp_string := translate(temp_string, 'âãäåāăąÁÂÃÄÅĀĂĄ', 'aaaaaaaaaaaaaaa');
    temp_string := translate(temp_string, 'èééêëēĕėęěĒĔĖĘĚ', 'eeeeeeeeeeeeeee');
    temp_string := translate(temp_string, 'ìíîïìĩīĭÌÍÎÏÌĨĪĬ', 'iiiiiiiiiiiiiiii');
    temp_string := translate(temp_string, 'óôõöōŏőÒÓÔÕÖŌŎŐ', 'ooooooooooooooo');
    temp_string := translate(temp_string, 'ùúûüũūŭůÙÚÛÜŨŪŬŮ', 'uuuuuuuuuuuuuuuu');
    temp_string := REPLACE(to_indexed, 'Œ', 'oe');
    temp_string := REPLACE(temp_string, 'Ӕ', 'ae');
    temp_string := REPLACE(temp_string, 'œ', 'oe');
    temp_string := REPLACE(temp_string, 'æ', 'ae');
    temp_string := REPLACE(temp_string, 'ë', 'e');
    temp_string := REPLACE(temp_string, 'ï', 'i');
    temp_string := REPLACE(temp_string, 'ö', 'o');
    temp_string := REPLACE(temp_string, 'ü', 'u');
--     temp_string := REPLACE(temp_string, E'\'', '');
--     temp_string := REPLACE(temp_string, '"', '');
    temp_string := REPLACE(temp_string, 'ñ', 'n');
    temp_string := REPLACE(temp_string,chr(946),'b');
    temp_string := TRANSLATE(temp_string,'Ð','d');
    temp_string := TRANSLATE(temp_string,'ó','o');
    temp_string := TRANSLATE(temp_string,'ę','e');
    temp_string := TRANSLATE(temp_string,'ā','a');
    temp_string := TRANSLATE(temp_string,'ē','e');
    temp_string := TRANSLATE(temp_string,'ī','i');
    temp_string := TRANSLATE(temp_string,'ō','o');
    temp_string := TRANSLATE(temp_string,'ū','u');
    temp_string := TRANSLATE(temp_string,'ş','s');
    temp_string := TRANSLATE(temp_string,'Ş','s');
--     temp_string := TRANSLATE(temp_string,'†','');
--     temp_string := TRANSLATE(temp_string,chr(52914),'');

    -- FROM 160 to 255 ASCII
    temp_string := TRANSLATE(temp_string, ' ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ',
      '  cL YS sCa  -R     Zu .z   EeY?AAAAAAACEEEEIIII NOOOOOxOUUUUYTBaaaaaaaceeeeiiii nooooo/ouuuuyty');
	--Remove ALL none alphanumerical char
    temp_string := lower(regexp_replace(temp_string,'[^[:alnum:]]','', 'g'));
    return substring(temp_string from 0 for 40);
END;
$$ LANGUAGE plpgsql IMMUTABLE;


CREATE OR REPLACE FUNCTION toUniqueStr(to_indexed varchar) RETURNS varchar STRICT
AS $$
DECLARE
    temp_string varchar;
BEGIN
    -- Investigate https://launchpad.net/postgresql-unaccent
    temp_string := to_indexed;
    temp_string := TRANSLATE(temp_string, E'  ¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿×&|@"\'#(§^!{})°$*][£µ`%+=~/.,?;:\\<>ł€¶ŧ←↓→«»¢“”_-',
      '');
      
        --Remove ALL none alphanumerical char
    temp_string := lower(temp_string);
    return substring(temp_string from 0 for 40);
END;
$$ LANGUAGE plpgsql IMMUTABLE;
/***
* Trigger function fct_cpy_fullToIndex
* Call the fulltoIndex function for different tables
*/
CREATE OR REPLACE FUNCTION fct_cpy_fullToIndex() RETURNS trigger
AS $$
BEGIN
	IF TG_TABLE_NAME = 'catalogue_properties' THEN
		NEW.property_tool_indexed := COALESCE(fullToIndex(NEW.property_tool),'');
		NEW.property_sub_type_indexed := COALESCE(fullToIndex(NEW.property_sub_type),'');
		NEW.property_method_indexed := COALESCE(fullToIndex(NEW.property_method),'');
		NEW.property_qualifier_indexed := COALESCE(fullToIndex(NEW.property_qualifier),'');
	ELSIF TG_TABLE_NAME = 'chronostratigraphy' THEN
		NEW.name_indexed := to_tsvector('simple', NEW.name);
		NEW.name_order_by := fullToIndex(NEW.name);
	ELSIF TG_TABLE_NAME = 'collections' THEN
		NEW.name_indexed := fullToIndex(NEW.name);		
	ELSIF TG_TABLE_NAME = 'expeditions' THEN
		NEW.name_indexed := fullToIndex(NEW.name);
	ELSIF TG_TABLE_NAME = 'habitats' THEN
		NEW.code_indexed := fullToIndex(NEW.code);
	ELSIF TG_TABLE_NAME = 'identifications' THEN
		NEW.value_defined_indexed := COALESCE(fullToIndex(NEW.value_defined),'');
	ELSIF TG_TABLE_NAME = 'lithology' THEN
		NEW.name_indexed := to_tsvector('simple', NEW.name);
		NEW.name_order_by := fullToIndex(NEW.name);
	ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
		NEW.name_indexed := to_tsvector('simple', NEW.name);
		NEW.name_order_by := fullToIndex(NEW.name);
	ELSIF TG_TABLE_NAME = 'mineralogy' THEN
		NEW.name_indexed := to_tsvector('simple', NEW.name);
		NEW.name_order_by := fullToIndex(NEW.name);
		NEW.formule_indexed := fullToIndex(NEW.formule);
	ELSIF TG_TABLE_NAME = 'multimedia' THEN
		NEW.title_indexed := fullToIndex(NEW.title);
	ELSIF TG_TABLE_NAME = 'multimedia_keywords' THEN
		NEW.keyword_indexed := fullToIndex(NEW.keyword);
	ELSIF TG_TABLE_NAME = 'people' THEN
		NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.name_formated_indexed := fulltoindex(coalesce(NEW.given_name,'') || coalesce(NEW.family_name,''));
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
	ELSIF TG_TABLE_NAME = 'codes' THEN
		IF NEW.code ~ '^[0-9]+$' THEN
		   NEW.code_num := NEW.code;
		ELSE
    		   NEW.code_num := null;
		END IF;
                NEW.full_code_indexed := to_tsvector('simple', COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,''));
		NEW.full_code_order_by := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
	ELSIF TG_TABLE_NAME = 'tag_groups' THEN
		NEW.group_name_indexed := fullToIndex(NEW.group_name);
		NEW.sub_group_name_indexed := fullToIndex(NEW.sub_group_name);
	ELSIF TG_TABLE_NAME = 'taxonomy' THEN
		NEW.name_indexed := to_tsvector('simple', NEW.name);
		NEW.name_order_by := fullToIndex(NEW.name);
	ELSIF TG_TABLE_NAME = 'classification_keywords' THEN
		NEW.keyword_indexed := fullToIndex(NEW.keyword);
	ELSIF TG_TABLE_NAME = 'users' THEN
		NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
	ELSIF TG_TABLE_NAME = 'class_vernacular_names' THEN
		NEW.community_indexed := fullToIndex(NEW.community);
	ELSIF TG_TABLE_NAME = 'vernacular_names' THEN
		NEW.name_indexed := fullToIndex(NEW.name);
	ELSIF TG_TABLE_NAME = 'igs' THEN
		NEW.ig_num_indexed := fullToIndex(NEW.ig_num);
       ELSIF TG_TABLE_NAME = 'collecting_methods' THEN
                NEW.method_indexed := fullToIndex(NEW.method);
       ELSIF TG_TABLE_NAME = 'collecting_tools' THEN
                NEW.tool_indexed := fullToIndex(NEW.tool);
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;


/***
* fct_clr_specialstatus
* Check the type(special status) on specimen_individuals and update the search and group type
* to be conform to the std
*/
CREATE OR REPLACE FUNCTION fct_clr_specialstatus() RETURNS TRIGGER
AS $$
DECLARE
  newType varchar := fullToIndex(NEW.type);
BEGIN

	-- IF Type not changed
	IF TG_OP = 'UPDATE' THEN
		IF fullToIndex(OLD.type) = newType THEN
			RETURN NEW;
		END IF;
	END IF;

	IF newType= 'specimen' THEN
		NEW.type_search := 'specimen';
		NEW.type_group := 'specimen';
	ELSIF newType= 'type' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type';
	ELSIF newType= 'subtype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type';
	ELSIF newType= 'allotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'allotype';
	ELSIF newType= 'cotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'syntype';
	ELSIF newType= 'genotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type';
	ELSIF newType= 'holotype' THEN
		NEW.type_search := 'holotype';
		NEW.type_group := 'holotype';
	ELSIF newType= 'hypotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'hypotype';
	ELSIF newType= 'lectotype' THEN
		NEW.type_search := 'lectotype';
		NEW.type_group := 'lectotype';
	ELSIF newType= 'locotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'locotype';
	ELSIF newType= 'neallotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type';
	ELSIF newType= 'neotype' THEN
		NEW.type_search := 'neotype';
		NEW.type_group := 'neotype';
	ELSIF newType= 'paralectotype' THEN
		NEW.type_search := 'paralectotype';
		NEW.type_group := 'paralectotype';
	ELSIF newType= 'paratype' THEN
		NEW.type_search := 'paratype';
		NEW.type_group := 'paratype';
	ELSIF newType= 'plastotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'plastotype';
	ELSIF newType= 'plesiotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'plesiotype';
	ELSIF newType= 'syntype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'syntype';
	ELSIF newType= 'topotype' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'topotype';
	ELSIF newType= 'typeinlitteris' THEN
		NEW.type_search := 'type';
		NEW.type_group := 'type in litteris';
	ELSE
    NEW.type_search := 'type';
    NEW.type_group := 'type';
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
	DELETE FROM catalogue_people WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM comments WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM catalogue_properties WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM identifications WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM class_vernacular_names WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM classification_synonymies WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM classification_keywords WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM users_workflow WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM collection_maintenance WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM associated_multimedia WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM codes WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
	DELETE FROM insurances WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
  DELETE FROM staging_people WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;	
	RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_remove_array_elem(IN in_array anyarray, IN elem anyarray,OUT out_array anyarray)
AS $$
BEGIN
	SELECT array(select s FROM fct_explode_array (in_array)  as s WHERE NOT elem @> ARRAY[s]) INTO out_array;
END;
$$
LANGUAGE plpgsql immutable;

CREATE OR REPLACE FUNCTION fct_explode_array(in_array anyarray) returns setof anyelement as
$$
    select ($1)[s] from generate_series(1,array_upper($1, 1)) as s;
$$
LANGUAGE sql immutable;


CREATE OR REPLACE FUNCTION fct_array_find(IN in_array anyarray, IN elem anyelement,OUT item_order integer)
AS $$
    select s from generate_series(1,array_upper($1, 1)) as s where $1[s] = $2;
$$
LANGUAGE sql immutable;

CREATE OR REPLACE FUNCTION fct_array_find(IN in_array varchar, IN elem anyelement,OUT item_order integer)
AS $$
    select fct_array_find(string_to_array($1,','), $2::text);
$$
LANGUAGE sql immutable;

/**
* fct_chk_possible_upper_levels
* When inserting or updating a hierarchical unit, checks, considering parent level, that unit level is ok (depending on definitions given in possible_upper_levels_table)
*/
CREATE OR REPLACE FUNCTION fct_chk_possible_upper_level (referenced_relation varchar, new_parent_ref template_classifications.parent_ref%TYPE, new_level_ref template_classifications.level_ref%TYPE, new_id integer) RETURNS boolean
AS $$
DECLARE
response boolean;
BEGIN
  IF new_id = 0 OR (new_parent_ref = 0 AND new_level_ref IN (1, 55, 64, 70, 75)) THEN
    RETURN TRUE;
  ELSE
    EXECUTE 'SELECT true WHERE EXISTS( SELECT * ' ||
      'from possible_upper_levels ' ||
      'where level_ref = ' || quote_literal(new_level_ref) ||
      '  and level_upper_ref = (select level_ref from ' || quote_ident(referenced_relation) || ' where id = ' || quote_literal(new_parent_ref) || '))'
      INTO response;
    IF response IS NULL THEN 
      RETURN FALSE;
    ELSE 
      RETURN TRUE;
    END IF;
  END IF;

  RETURN FALSE;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_trg_chk_possible_upper_level() RETURNS TRIGGER
AS $$
DECLARE
BEGIN
  IF fct_chk_possible_upper_level(TG_TABLE_NAME::text, NEW.parent_ref, NEW.level_ref, NEW.id) = false THEN
    RAISE EXCEPTION 'This record does not follow the level hierarchy';
  END IF;
  RETURN NEW;
END;
$$
language plpgsql;

CREATE OR REPLACE FUNCTION fct_chk_upper_level_for_childrens() RETURNS TRIGGER
AS $$
DECLARE
  rec_exists integer;
BEGIN

  EXECUTE 'SELECT count(id)  FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE parent_ref=' || quote_literal(NEW.id) || ' AND fct_chk_possible_upper_level('|| quote_literal(TG_TABLE_NAME::text) ||', parent_ref, level_ref, id) = false ' INTO rec_exists;
  
  IF rec_exists > 0 THEN
    RAISE EXCEPTION 'Children of this record does not follow the level hierarchy';
  END IF;
  RETURN NEW;

END;
$$
language plpgsql;

/**
* fct_chk_peopleType
* When removing author flag for a people, check if he is not referenced as author in a catalogue
*/
CREATE OR REPLACE FUNCTION fct_chk_peopleType() RETURNS TRIGGER
AS $$
DECLARE
BEGIN
  IF NEW.db_people_type IS DISTINCT FROM OLD.db_people_type THEN

    /** AUTHOR FLAG IS 2 **/
    IF NOT (NEW.db_people_type & 2)>0  THEN
      IF EXISTS( SELECT * FROM catalogue_people WHERE people_ref=NEW.id AND people_type='author')  THEN
        RAISE EXCEPTION 'Author still used as author.';
      END IF;
    END IF;

    /** IDENTIFIER FLAG IS 4 **/
    IF NOT (NEW.db_people_type & 4)>0  THEN
      IF EXISTS( SELECT * FROM catalogue_people WHERE people_ref=NEW.id AND people_type='identifier')  THEN
        RAISE EXCEPTION 'Identifier still used as identifier.';
      END IF;
    END IF;

    /** Expert Flag is 8 **/
    IF NOT (NEW.db_people_type & 8)>0  THEN
      IF EXISTS( SELECT * FROM catalogue_people WHERE people_ref=NEW.id AND people_type='expert')  THEN
        RAISE EXCEPTION 'Expert still used as expert.';
      END IF;
    END IF;

          /** COLLECTOR Flag is 16 **/
    IF NOT (NEW.db_people_type & 16)>0   THEN
      IF EXISTS( SELECT * FROM catalogue_people WHERE people_ref=NEW.id AND people_type='collector')  THEN
        RAISE EXCEPTION 'Collector still used as collector.';
      END IF;
    END IF;
  END IF;
  RETURN NEW;

END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_chk_ReferencedRecord() RETURNS TRIGGER
AS $$
DECLARE
  rec_exists integer;
BEGIN

  EXECUTE 'SELECT 1 WHERE EXISTS ( SELECT id FROM ' || quote_ident(NEW.referenced_relation)  || ' WHERE id=' || quote_literal(NEW.record_id) || ')' INTO rec_exists;
  IF rec_exists IS NULL THEN
    RAISE EXCEPTION 'The referenced record does not exists % %',NEW.referenced_relation, NEW.record_id;
  END IF;

  RETURN NEW;
END;
$$
language plpgsql;

CREATE OR REPLACE FUNCTION fct_chk_ReferencedRecordRelationShip() RETURNS TRIGGER
AS $$
DECLARE
  rec_exists integer;
BEGIN

  EXECUTE 'SELECT count(id)  FROM ' || quote_ident(NEW.referenced_relation)  || ' WHERE id=' || quote_literal(NEW.record_id_1) ||  ' OR id=' || quote_literal(NEW.record_id_2) INTO rec_exists;
  
  IF rec_exists != 2 THEN
    RAISE EXCEPTION 'The referenced record does not exists';
  END IF;

  RETURN NEW;

END;
$$
language plpgsql;


CREATE OR REPLACE FUNCTION fct_cpy_FormattedName() RETURNS TRIGGER
AS $$
BEGIN
	IF TG_OP ='UPDATE' THEN
		IF NEW.family_name = OLD.family_name AND NEW.given_name = OLD.given_name AND NEW.title = OLD.title THEN
			RETURN NEW;
		END IF;
	END IF;

	IF NEW.is_physical THEN
    IF COALESCE(NEW.title, '') = '' THEN
      NEW.formated_name := COALESCE(NEW.family_name,'') || ' ' || COALESCE(NEW.given_name,'');
		ELSE
		  NEW.formated_name := COALESCE(NEW.family_name,'') || ' ' || COALESCE(NEW.given_name,'') || ' (' || NEW.title || ')';
    END IF;
	ELSE
		NEW.formated_name := NEW.family_name;
	END IF;
	NEW.formated_name_indexed := fullToIndex(NEW.formated_name);
        NEW.formated_name_unique := toUniqueStr(NEW.formated_name);
	NEW.formated_name_ts := to_tsvector('simple', NEW.formated_name);
	RETURN NEW;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_cpy_path() RETURNS TRIGGER
AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        IF( TG_TABLE_NAME::text = 'multimedia' OR
          TG_TABLE_NAME::text = 'collections' OR
          TG_TABLE_NAME::text = 'gtu' OR
          TG_TABLE_NAME::text = 'habitats' OR
          TG_TABLE_NAME::text = 'specimen_parts' OR
          TG_TABLE_NAME::text = 'staging') THEN

          IF NEW.id = 0 THEN
            NEW.parent_ref = null;
          END IF;
          IF NEW.parent_ref IS NULL THEN
            NEW.path ='/';
          ELSE
            EXECUTE 'SELECT path || id || ' || quote_literal('/') ||' FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
          END IF;
        ELSIF TG_TABLE_NAME::text = 'people_relationships' THEN
          SELECT path || NEW.person_1_ref || '/' INTO NEW.path
            FROM people_relationships
            WHERE person_2_ref=NEW.person_1_ref;
          IF NEW.path is NULL THEN
            NEW.path = '/' || NEW.person_1_ref || '/';
          END IF;
        END IF;
      ELSIF TG_OP = 'UPDATE' THEN
        IF( TG_TABLE_NAME::text = 'multimedia' OR
          TG_TABLE_NAME::text = 'collections' OR
          TG_TABLE_NAME::text = 'gtu' OR
          TG_TABLE_NAME::text = 'habitats' OR
          TG_TABLE_NAME::text = 'specimen_parts' OR
          TG_TABLE_NAME::text = 'staging') THEN

          IF NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
            IF NEW.parent_ref IS NULL THEN
              NEW.path ='/';
            ELSIF COALESCE(OLD.parent_ref,0) = COALESCE(NEW.parent_ref,0) THEN
              RETURN NEW;
            ELSE
              EXECUTE 'SELECT path || id || ' || quote_literal('/') ||' FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
            END IF;

            EXECUTE 'UPDATE ' || quote_ident(TG_TABLE_NAME::text) || ' SET path=replace(path, ' ||  quote_literal(OLD.path || OLD.id || '/') ||' , ' || quote_literal( NEW.path || OLD.id || '/') || ') ' ||
              ' WHERE path like ' || quote_literal(OLD.path || OLD.id || '/%');
          END IF;
        ELSE
          IF NEW.person_1_ref IS DISTINCT FROM OLD.person_1_ref OR NEW.person_2_ref IS DISTINCT FROM OLD.person_2_ref THEN
            SELECT path ||  NEW.person_1_ref || '/' INTO NEW.path FROM people_relationships WHERE person_2_ref=NEW.person_1_ref;

              IF NEW.path is NULL THEN
                NEW.path = '/' || NEW.person_1_ref || '/';
              END IF;
              -- AND UPDATE CHILDRENS
              UPDATE people_relationships SET path=replace(path, OLD.path, NEW.path) WHERE person_1_ref=OLD.person_2_ref;
          END IF;
        END IF;
      END IF;
  RETURN NEW;
END;
$$
language plpgsql;

CREATE OR REPLACE FUNCTION fct_cpy_path_catalogs() RETURNS TRIGGER
AS $$
BEGIN
    IF TG_OP = 'INSERT' AND (TG_TABLE_NAME::text = 'taxonomy' OR
          TG_TABLE_NAME::text = 'lithology' OR
          TG_TABLE_NAME::text = 'lithostratigraphy' OR
          TG_TABLE_NAME::text = 'mineralogy' OR
          TG_TABLE_NAME::text = 'chronostratigraphy') THEN

          IF NEW.parent_ref = 0 THEN
            NEW.path ='/';
          ELSE
            EXECUTE 'SELECT path || id || ' || quote_literal('/') ||' FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
          END IF;

    ELSIF TG_OP = 'UPDATE' AND (TG_TABLE_NAME::text = 'taxonomy' OR
        TG_TABLE_NAME::text = 'lithology' OR
        TG_TABLE_NAME::text = 'lithostratigraphy' OR
        TG_TABLE_NAME::text = 'mineralogy' OR
        TG_TABLE_NAME::text = 'chronostratigraphy') THEN

        IF NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
          IF NEW.parent_ref = 0 THEN
            NEW.path ='/';
          ELSIF COALESCE(OLD.parent_ref,0) = COALESCE(NEW.parent_ref,0) THEN
            RETURN NEW;
          ELSE
            EXECUTE 'SELECT path || id ||' || quote_literal('/') ||'  FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
          END IF;

          EXECUTE 'UPDATE ' || quote_ident(TG_TABLE_NAME::text) || ' SET path=replace(path, ' ||  quote_literal(OLD.path || OLD.id || '/') ||' , ' || quote_literal( NEW.path || OLD.id || '/') || ') ' ||
            ' WHERE path like ' || quote_literal(OLD.path || OLD.id || '/%');
        END IF;
--         RAISE INFO 'nothing diff';
  END IF;
  RETURN NEW;
END;
$$
language plpgsql;




CREATE OR REPLACE FUNCTION get_setting(IN param text, OUT value text)
LANGUAGE plpgsql STABLE STRICT AS
$$BEGIN
  SELECT current_setting(param) INTO value;
  EXCEPTION
  WHEN UNDEFINED_OBJECT THEN
    value := NULL;
END;$$;


/**
 Set user id 
*/
CREATE OR REPLACE FUNCTION fct_set_user(userid integer) RETURNS void
language SQL AS
$$
  update users set last_seen = now() where id = $1 and  set_config('darwin.userid', $1::varchar, false) is distinct from 'noop';
$$;

CREATE OR REPLACE FUNCTION fct_trk_log_table() RETURNS TRIGGER
AS $$
DECLARE
	user_id integer;
        track_level integer;
	track_fields integer;
	trk_id bigint;
	tbl_row RECORD;
	new_val varchar;
	old_val varchar;
BEGIN



        SELECT COALESCE(get_setting('darwin.track_level'),'10')::integer INTO track_level;
        IF track_level = 0 THEN --NO Tracking
          RETURN NEW;
        ELSIF track_level = 1 THEN -- Track Only Main tables
          IF TG_TABLE_NAME::text NOT IN ('specimens', 'specimen_individuals', 'specimen_parts', 'taxonomy', 'chronostratigraphy', 'lithostratigraphy',
            'mineralogy', 'lithology', 'habitats', 'people') THEN
            RETURN NEW;
          END IF;
        END IF;

	SELECT COALESCE(get_setting('darwin.userid'),'0')::integer INTO user_id;
	IF user_id = 0 THEN
	  RETURN NEW;
	END IF;

	IF TG_OP = 'INSERT' THEN
		INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, new_value)
				VALUES (TG_TABLE_NAME::text, NEW.id, user_id, 'insert', now(), hstore(NEW)) RETURNING id into trk_id;
	ELSEIF TG_OP = 'UPDATE' THEN

	  IF ROW(NEW.*) IS DISTINCT FROM ROW(OLD.*) THEN
		INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, new_value, old_value)
		    VALUES (TG_TABLE_NAME::text, NEW.id, user_id, 'update', now(), hstore(NEW), hstore(OLD)) RETURNING id into trk_id;
	  ELSE
	    RAISE info 'unnecessary update on table "%" and id "%"', TG_TABLE_NAME::text, NEW.id;
	  END IF;

	ELSEIF TG_OP = 'DELETE' THEN
		INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, old_value)
 			VALUES (TG_TABLE_NAME::text, OLD.id, user_id, 'delete', now(), hstore(OLD));
	END IF;

	RETURN NULL;
END;
$$
LANGUAGE plpgsql;

/*
** fct_cpy_length_conversion
** Convert length into unified version (m - meter)
*/
CREATE OR REPLACE FUNCTION fct_cpy_length_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) RETURNS real
language SQL STABLE
AS
$$
	SELECT  CASE
			WHEN $2 = 'dm' THEN
				($1)*10^(-1)
			WHEN $2 = 'ft' THEN
				($1)*3.048*10^(-1)
			WHEN $2 = 'P' THEN
				($1)*3.24839385*10^(-1)
			WHEN $2 = 'yd' THEN
				($1)*9.144*10^(-1)
			WHEN $2 = 'cm' THEN
				($1)*10^(-2)
			WHEN $2 = 'in' THEN
				($1)*2.54*10^(-2)
			WHEN $2 = 'mm' THEN
				($1)*10^(-3)
			WHEN $2 = 'pica' THEN
				($1)*4.233333*10^(-3)
			WHEN $2 = 'p' THEN
				($1)*27.069949*10^(-3)
			WHEN $2 = 'mom' THEN
				($1)*10^(-4)
			WHEN $2 IN ('pt', 'point') THEN
				($1)*3.527778*10^(-4)
			WHEN $2 = 'mil' THEN
				($1)*2.54*10^(-5)
			WHEN $2 IN ('µm', 'µ') THEN
				($1)*10^(-6)
			WHEN $2 = 'twp' THEN
				($1)*17.639*10^(-6)
			WHEN $2 = 'cal' THEN
				($1)*254*10^(-6)
			WHEN $2 = 'nm' THEN
				($1)*10^(-9)
			WHEN $2 = 'Å' THEN
				($1)*10^(-10)
			WHEN $2 = 'pm' THEN
				($1)*10^(-12)
			WHEN $2 IN ('fm', 'fermi') THEN
				($1)*10^(-15)
			WHEN $2 = 'am' THEN
				($1)*10^(-18)
			WHEN $2 = 'zm' THEN
				($1)*10^(-21)
			WHEN $2 = 'ym' THEN
				($1)*10^(-24)
			WHEN $2 IN ('brasse', 'vadem') THEN
				($1)*1.8288
			WHEN $2 = 'fathom' THEN
				($1)*1.828804
			WHEN $2 = 'rd' THEN
				($1)*5.02921
			WHEN $2 = 'dam' THEN
				($1)*10
			WHEN $2 = 'ch' THEN
				($1)*20.11684
			WHEN $2 = 'arp' THEN
				($1)*58.471089295
			WHEN $2 IN ('hm', 'K') THEN
				($1)*10^2
			WHEN $2 = 'fur' THEN
				($1)*201.168
			WHEN $2 = 'km' THEN
				($1)*10^3
			WHEN $2 = 'mi' THEN
				($1)*1.609344*10^3
			WHEN $2 = 'nautical mi' THEN
				($1)*1.852*10^3
			WHEN $2 IN ('lieue', 'league') THEN
				($1)*4.828032*10^3
			WHEN $2 = 'mam' THEN
				($1)*10^4
			WHEN $2 = 'Mm' THEN
				($1)*10^6
			WHEN $2 = 'Gm' THEN
				($1)*10^9
			WHEN $2 = 'ua' THEN
				($1)*1.495979*10^11
			WHEN $2 = 'Tm' THEN
				($1)*10^12
			WHEN $2 = 'Pm' THEN
				($1)*10^15
			WHEN $2 = 'pc' THEN
				($1)*3.085678*10^16
			WHEN $2 IN ('ly', 'l.y.') THEN
				($1)*9.4607304725808*10^15
			WHEN $2 = 'Em' THEN
				($1)*10^18
			WHEN $2 = 'Zm' THEN
				($1)*10^21
			WHEN $2 = 'Ym' THEN
				($1)*10^24
			ELSE
				$1
		END::real;
$$;
/*
** fct_cpy_temperature_conversion
** Convert temperatures into unified version (K - Kelvin)
*/
CREATE OR REPLACE FUNCTION fct_cpy_temperature_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) RETURNS real
language SQL STABLE
AS
$$
	SELECT  CASE
			WHEN $2 = '°C' THEN
				($1)+273.15
			WHEN $2 = '°F' THEN
				(($1)+459.67)/1.8
			WHEN $2 = '°Ra' THEN
				($1)/1.8
			WHEN $2 in ('°Ré', '°r') THEN
				(($1)*5/4)+273.15
			WHEN $2 = '°N' THEN
				(($1)+273.15)*0.33
			WHEN $2 = '°Rø' THEN
				(((($1)-7.5)*40)/21)+273.15
			WHEN $2 = '°De' THEN
				373.15-(($1)*2/3)
			ELSE
				$1
		END::real;
$$;

/*
** fct_cpy_time_conversion
** Convert time values into unified one (s - second)
*/
CREATE OR REPLACE FUNCTION fct_cpy_time_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) RETURNS real
language SQL STABLE
AS
$$
	SELECT  CASE
			WHEN $2 = 'ns' THEN
				($1)*10^(-9)
			WHEN $2 = 'shake' THEN
				($1)*10^(-8)
			WHEN $2 = 'µs' THEN
				($1)*10^(-6)
			WHEN $2 = 'ms' THEN
				($1)*10^(-3)
			WHEN $2 = 'cs' THEN
				($1)*10^(-2)
			WHEN $2 = 't' THEN
				($1)/60
			WHEN $2 = 'ds' THEN
				($1)*10^(-1)
			WHEN $2 = 'min' THEN
				60*($1)
			WHEN $2 = 'h' THEN
				3600*($1)
			WHEN $2 IN ('d', 'j') THEN
				86400*($1)
			WHEN $2 IN ('y', 'year') THEN
				($1)*3.1536*10^7
			ELSE
				$1
		END::real;
$$;

/*
** fct_cpy_speed_conversion
** Convert windspeed values into unified one (m/s).
** If no unit or wrong unit provided value entered is returned so.
** If array of values is empty, empty array of values returned
*/
CREATE OR REPLACE FUNCTION fct_cpy_speed_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) RETURNS real
language SQL STABLE
AS
$$
	SELECT  CASE
			WHEN $2 = 'Kt' THEN
				($1)*0.51444444444444
			WHEN $2 = 'Beaufort' THEN
				CASE
					WHEN $1 = 0 THEN
						0.13888888888888
					WHEN $1 = 1 THEN
						3*0.27777777777778
					WHEN $1 = 2 THEN
						8*0.27777777777778
					WHEN $1 = 3 THEN
						15*0.27777777777778
					WHEN $1 = 4 THEN
						23.5*0.27777777777778
					WHEN $1 = 5 THEN
						33*0.27777777777778
					WHEN $1 = 6 THEN
						44*0.27777777777778
					WHEN $1 = 7 THEN
						55.5*0.27777777777778
					WHEN $1 = 8 THEN
						68*0.27777777777778
					WHEN $1 = 9 THEN
						81.5*0.27777777777778
					WHEN $1 = 10 THEN
						95.5*0.27777777777778
					WHEN $1 = 11 THEN
						110*0.27777777777778
					ELSE
						120*0.27777777777778
				END
			ELSE
				CASE
					WHEN strpos($2, '/') > 0 THEN
						fct_cpy_length_conversion($1, substr($2, 0, strpos($2, '/')))/fct_cpy_time_conversion(1, substr($2, strpos($2, '/')+1))
					ELSE
						$1
				END
		END::real;
$$;

/*
** fct_cpy_volume_conversion
** Convert volume into unified version (m³ - cube meter)
*/
CREATE OR REPLACE FUNCTION fct_cpy_volume_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) RETURNS real
language SQL STABLE
AS
$$
  SELECT  CASE
      WHEN $2 = 'l' THEN
        ($1)*10^(-3)
      WHEN $2 = 'cm³' OR $2 = 'ml' THEN
        ($1)*10^(-6)
      WHEN $2 = 'mm³' OR $2 = 'µl' THEN
        ($1)*10^(-9)
      WHEN $2 = 'µm³' THEN
        ($1)*10^(-18)
      WHEN $2 = 'km³' THEN
        ($1)*10^(9)
      WHEN $2 = 'Ml' THEN
        ($1)*10^(3)
      WHEN $2 = 'hl' THEN
        ($1)*10
      ELSE
        $1
    END::real;
$$;

/*
** fct_cpy_weight_conversion
** Convert weight into unified version (g - gram)
*/
CREATE OR REPLACE FUNCTION fct_cpy_weight_conversion (IN property real, IN property_unit catalogue_properties.property_unit%TYPE) RETURNS real
language SQL STABLE
AS
$$
  SELECT  CASE
      WHEN $2 = 'hg' THEN
        ($1)*10^(2)
      WHEN $2 = 'kg' THEN
        ($1)*10^(3)
      WHEN $2 = 'Mg' OR $2 = 'ton' THEN
        ($1)*10^(6)
      WHEN $2 = 'dg' THEN
        ($1)*10^(-1)
      WHEN $2 = 'cg' THEN
        ($1)*10^(-2)
      WHEN $2 = 'mg' THEN
        ($1)*10^(-3)
      WHEN $2 = 'lb' OR $2 = 'lbs' OR $2 = 'pound' THEN
        ($1)*453.59237
      WHEN $2 = 'ounce' THEN
        ($1)*28.349523125
      WHEN $2 = 'grain' THEN
        ($1)*6.479891*10^(-2)
      ELSE
        $1
    END::real;
$$;

/*
** convert_to_unified
* convert the unit to the unified form
*/
CREATE OR REPLACE FUNCTION convert_to_unified (IN property varchar, IN property_unit varchar, IN property_type varchar) RETURNS varchar
language plpgsql
AS
$$
DECLARE
    r_val real :=0;
BEGIN
    IF property is NULL THEN
        RETURN NULL;
    END IF;

    BEGIN
      r_val := property::real;
    EXCEPTION WHEN SQLSTATE '22P02' THEN
      RETURN '';
    END;

    IF property_type = 'speed' THEN
        RETURN fct_cpy_speed_conversion(r_val, property_unit)::text;
    END IF;

    IF property_type = 'weight' THEN
        RETURN fct_cpy_weight_conversion(r_val, property_unit)::text;
    END IF;

    IF property_type = 'volume' THEN
        RETURN fct_cpy_volume_conversion(r_val, property_unit)::text;
    END IF;

    IF property_type = 'temperature' AND property_unit IN ('K', '°C', '°F', '°Ra', '°Re', '°r', '°N', '°Rø', '°De') THEN
        RETURN fct_cpy_temperature_conversion(r_val, property_unit)::text;
    END IF;

    IF property_type = 'length' AND property_unit IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms') THEN
        RETURN fct_cpy_length_conversion(r_val, property_unit)::text;
    END IF;

    RETURN  property;
END;
$$;

/*
** fct_cpy_unified_values
** Used as a trigger in catalogue_properties table to transform values into unified common value
** Case by case function
*/
CREATE OR REPLACE FUNCTION fct_cpy_unified_values () RETURNS TRIGGER
language plpgsql
AS

$$
DECLARE
	property_line catalogue_properties%ROWTYPE;
BEGIN
    IF TG_TABLE_NAME ='properties_values' THEN
        SELECT * INTO property_line FROM  catalogue_properties WHERE id=NEW.property_ref;
        NEW.property_value_unified := convert_to_unified(NEW.property_value,  property_line.property_unit, property_line.property_sub_type_indexed);
        NEW.property_accuracy_unified := convert_to_unified(NEW.property_accuracy::varchar,  property_line.property_accuracy_unit, property_line.property_sub_type_indexed)::real;
    ELSIF TG_OP = 'UPDATE' THEN
      IF NEW.property_sub_type_indexed IS DISTINCT FROM OLD.property_sub_type_indexed 
      OR NEW.property_unit IS DISTINCT FROM OLD.property_unit
      OR NEW.property_accuracy_unit IS DISTINCT FROM OLD.property_accuracy_unit
      THEN
        UPDATE properties_values SET
            property_value_unified = convert_to_unified(property_value, NEW.property_unit, NEW.property_sub_type_indexed),
            property_accuracy_unified = convert_to_unified(property_accuracy::varchar,  NEW.property_accuracy_unit, NEW.property_sub_type_indexed)::real
            WHERE property_ref = NEW.id;
      END IF;
    END IF;
	RETURN NEW;
END;
$$;

/**
 Check That a parent is not attached twice in a path to avoid cycle
*/
CREATE OR REPLACE FUNCTION fct_chk_onceInPath(path varchar) RETURNS boolean
language plpgsql
AS
$$
BEGIN
    PERFORM * FROM regexp_split_to_table(path, E'\/') as i_id WHERE i_id != '' GROUP BY i_id HAVING COUNT(*)>1;
    IF FOUND THEN
        RETURN FALSE;
    END IF;
    RETURN true;
END;
$$;


/**
* Bloody mysql!
*/
CREATE OR REPLACE FUNCTION concat(text, text) RETURNS text AS $$
    SELECT $1 || $2;
$$ LANGUAGE 'sql';


CREATE OR REPLACE FUNCTION concat(text, text, text) RETURNS text AS $$
    SELECT $1 || $2 || $3;
$$ LANGUAGE 'sql';

/*
** Function used for encrypting passwords using pgcrypto function
*/

CREATE OR REPLACE FUNCTION sha1(bytea) RETURNS varchar LANGUAGE plpgsql AS
$$
BEGIN
        RETURN ENCODE(DIGEST($1, 'sha1'), 'hex');
END;
$$;

CREATE OR REPLACE FUNCTION ts_stat(tsvector, OUT word text, OUT ndoc integer, OUT nentry integer) RETURNS SETOF record AS
$$
    SELECT ts_stat('SELECT ' || quote_literal( $1::text ) || '::tsvector') WHERE $1 != to_tsvector('');
$$ LANGUAGE SQL RETURNS NULL ON NULL INPUT IMMUTABLE;

CREATE OR REPLACE FUNCTION fct_trg_word() RETURNS TRIGGER
AS
$$
BEGIN
/*
   IF TG_TABLE_NAME ='collection_maintenance' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.description_ts IS DISTINCT FROM NEW.description_ts THEN
          PERFORM fct_cpy_word('collection_maintenance','description_ts', NEW.description_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('collection_maintenance','description_ts', NEW.description_ts);
      END IF;

   ELSIF TG_TABLE_NAME ='comments' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.comment_ts IS DISTINCT FROM NEW.comment_ts THEN
          PERFORM fct_cpy_word('comments','comment_ts', NEW.comment_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('comments','comment_ts', NEW.comment_ts);
      END IF;
*/
   IF TG_TABLE_NAME ='vernacular_names' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.name_ts IS DISTINCT FROM NEW.name_ts THEN
          PERFORM fct_cpy_word('vernacular_names','name_ts', NEW.name_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('vernacular_names','name_ts', NEW.name_ts);
      END IF;
/*
   ELSIF TG_TABLE_NAME ='identifications' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.value_defined_ts IS DISTINCT FROM NEW.value_defined_ts THEN
          PERFORM fct_cpy_word('identifications','value_defined_ts', NEW.value_defined_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('identifications','value_defined_ts', NEW.value_defined_ts);
      END IF;

   ELSIF TG_TABLE_NAME ='multimedia' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.descriptive_ts IS DISTINCT FROM NEW.descriptive_ts THEN
          PERFORM fct_cpy_word('multimedia','descriptive_ts', NEW.descriptive_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('multimedia','descriptive_ts', NEW.descriptive_ts);
      END IF;
*/
   ELSIF TG_TABLE_NAME ='people' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.formated_name_ts IS DISTINCT FROM NEW.formated_name_ts THEN
          PERFORM fct_cpy_word('people','formated_name_ts', NEW.formated_name_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('people','formated_name_ts', NEW.formated_name_ts);
      END IF;

   ELSIF TG_TABLE_NAME ='users' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.formated_name_ts IS DISTINCT FROM NEW.formated_name_ts THEN
          PERFORM fct_cpy_word('users','formated_name_ts', NEW.formated_name_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('users','formated_name_ts', NEW.formated_name_ts);
      END IF;

   ELSIF TG_TABLE_NAME ='expeditions' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.name_ts IS DISTINCT FROM NEW.name_ts THEN
          PERFORM fct_cpy_word('expeditions','name_ts', NEW.name_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('expeditions','name_ts', NEW.name_ts);
      END IF;
/*
   ELSIF TG_TABLE_NAME ='habitats' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.description_ts IS DISTINCT FROM NEW.description_ts THEN
          PERFORM fct_cpy_word('habitats','description_ts', NEW.description_ts);
        END IF;
      ELSE
        PERFORM fct_cpy_word('habitats','description_ts', NEW.description_ts);
      END IF;
*/

   ELSIF TG_TABLE_NAME ='mineralogy' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.name_indexed IS DISTINCT FROM NEW.name_indexed THEN
          PERFORM fct_cpy_word('mineralogy','name_indexed', NEW.name_indexed);
        END IF;
      ELSE
        PERFORM fct_cpy_word('mineralogy','name_indexed', NEW.name_indexed);
      END IF;

   ELSIF TG_TABLE_NAME ='chronostratigraphy' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.name_indexed IS DISTINCT FROM NEW.name_indexed THEN
          PERFORM fct_cpy_word('chronostratigraphy','name_indexed', NEW.name_indexed);
        END IF;
      ELSE
        PERFORM fct_cpy_word('chronostratigraphy','name_indexed', NEW.name_indexed);
      END IF;

   ELSIF TG_TABLE_NAME ='lithostratigraphy' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.name_indexed IS DISTINCT FROM NEW.name_indexed THEN
          PERFORM fct_cpy_word('lithostratigraphy','name_indexed', NEW.name_indexed);
        END IF;
      ELSE
        PERFORM fct_cpy_word('lithostratigraphy','name_indexed', NEW.name_indexed);
      END IF;

   ELSIF TG_TABLE_NAME ='lithology' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.name_indexed IS DISTINCT FROM NEW.name_indexed THEN
          PERFORM fct_cpy_word('lithology','name_indexed', NEW.name_indexed);
        END IF;
      ELSE
        PERFORM fct_cpy_word('lithology','name_indexed', NEW.name_indexed);
      END IF;

   ELSIF TG_TABLE_NAME ='taxonomy' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.name_indexed IS DISTINCT FROM NEW.name_indexed THEN
          PERFORM fct_cpy_word('taxonomy','name_indexed', NEW.name_indexed);
        END IF;
      ELSE
        PERFORM fct_cpy_word('taxonomy','name_indexed', NEW.name_indexed);
      END IF;
/*
   ELSIF TG_TABLE_NAME ='codes' THEN

      IF TG_OP = 'UPDATE' THEN
        IF OLD.full_code_indexed IS DISTINCT FROM NEW.full_code_indexed THEN
          PERFORM fct_cpy_word('codes','full_code_indexed', NEW.full_code_indexed);
        END IF;
      ELSE
        PERFORM fct_cpy_word('codes','full_code_indexed', NEW.full_code_indexed);
      END IF;
*/
   END IF;

   RETURN NEW;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_cpy_word(tbl_name words.referenced_relation%TYPE, fld_name words.field_name%TYPE, word_ts tsvector) RETURNS boolean
AS
$$
DECLARE
  item varchar;
BEGIN
    FOR item IN SELECT word FROM ts_stat(word_ts) LOOP
      BEGIN
	INSERT INTO words (referenced_relation, field_name, word) VALUES (tbl_name, fld_name, item);
      EXCEPTION WHEN unique_violation THEN
	    -- Just Sleep and insert the next one
      END;
    END LOOP;
    RETURN true;
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION search_words_to_query(tbl_name words.referenced_relation%TYPE, fld_name words.field_name%TYPE, value varchar, op varchar) RETURNS tsquery AS
$$

  SELECT to_tsquery('simple',array_to_string( array(SELECT word FROM words
      WHERE referenced_relation = $1
	AND field_name = $2
	AND word % $3
        AND to_tsvector($3) <> to_tsvector('')
	AND word ilike
	  CASE WHEN $4 = 'begin' THEN $3 || '%'
	      WHEN $4 = 'end' THEN '%' || $3
	      WHEN $4 = 'contains' THEN '%' || $3 || '%'
	      ELSE word
	  END),
   ' | '));
$$
LANGUAGE SQL IMMUTABLE;

CREATE OR REPLACE FUNCTION fct_nbr_in_relation() RETURNS TRIGGER
AS
$$
DECLARE
  nbr integer = 0 ;
BEGIN
  SELECT count(record_id_2) INTO nbr FROM catalogue_relationships WHERE
      relationship_type = NEW.relationship_type
      AND record_id_1 = NEW.record_id_1
      AND referenced_relation = NEW.referenced_relation;

  IF NEW.relationship_type = 'current_name' THEN
    IF TG_OP = 'INSERT' THEN
      IF nbr > 0 THEN
	RAISE EXCEPTION 'Maximum number of renamed item reach';
      END IF;
    ELSE
      IF nbr > 1 THEN
	RAISE EXCEPTION 'Maximum number of renamed item reach';
      END IF;
    END IF;
  ELSEIF NEW.relationship_type = 'recombined from' THEN
    IF TG_OP = 'INSERT' THEN
      IF nbr > 1 THEN
	RAISE EXCEPTION 'Maximum number of recombined item reach';
      END IF;
    ELSE
      IF nbr > 2 THEN
	RAISE EXCEPTION 'Maximum number of recombined item reach';
      END IF;
    END IF;
  END IF;
  RETURN NEW;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_nbr_in_synonym() RETURNS TRIGGER
AS
$$
DECLARE
  nbr integer = 0 ;
BEGIN

  SELECT count(id) INTO nbr FROM classification_synonymies WHERE
      referenced_relation = NEW.referenced_relation
      AND record_id = NEW.record_id
      AND group_name = NEW.group_name;

  IF TG_OP = 'INSERT' THEN
    IF nbr > 1 THEN
      RAISE EXCEPTION 'You can ''t set this synonym twice!';
    END IF;
  ELSE
--     RAISE info 'nbr %', nbr;
    IF nbr > 2 THEN
      RAISE EXCEPTION 'You can ''t set this synonym twice!';
    END IF;
  END IF;

  RETURN NEW;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION datesOverlaps(start1 date, end1 date, start2 date, end2 date) RETURNS boolean LANGUAGE plpgsql
AS
$$
DECLARE
  response boolean := true;
BEGIN
  SELECT (start1, end1) OVERLAPS (start2, end2) INTO response;
  return response;
EXCEPTION
  WHEN OTHERS THEN
    response := false;
    RAISE NOTICE 'Error in datesOverlaps function: %', SQLERRM;
    return response;
END;
$$;

CREATE OR REPLACE FUNCTION lineToTagRows(IN line text) RETURNS SETOF varchar AS
$$
SELECT distinct(fulltoIndex(tags)) FROM regexp_split_to_table($1, ';') as tags WHERE fulltoIndex(tags) != '' ;
$$
LANGUAGE 'sql' IMMUTABLE STRICT;

CREATE OR REPLACE FUNCTION lineToTagArray(IN line text) RETURNS varchar[] AS
$$
select array_agg(tags_list) FROM (SELECT lineToTagRows($1) AS tags_list ) as x;
$$
LANGUAGE 'sql' IMMUTABLE STRICT;

CREATE OR REPLACE FUNCTION fct_cpy_gtuTags() RETURNS TRIGGER
language plpgsql
AS
$$
DECLARE
  curs_entry refcursor;
  entry_row RECORD;
  seen_el varchar[];
BEGIN
  IF TG_OP != 'DELETE' THEN
    OPEN curs_entry FOR SELECT distinct(fulltoIndex(tags)) as u_tag, trim(tags) as tags
                        FROM regexp_split_to_table(NEW.tag_value, ';') as tags
                        WHERE fulltoIndex(tags) != '';
    LOOP
      FETCH curs_entry INTO entry_row;
      EXIT WHEN NOT FOUND;

      seen_el := array_append(seen_el, entry_row.u_tag);

      PERFORM * FROM tags
                WHERE gtu_ref = NEW.gtu_ref
                  AND group_ref = NEW.id
                  AND tag_indexed = entry_row.u_tag
                LIMIT 1;
      IF FOUND THEN
        IF TG_OP = 'UPDATE' THEN
          IF OLD.sub_group_name != NEW.sub_group_name THEN
            UPDATE tags
            SET sub_group_type = NEW.sub_group_name
            WHERE group_ref = NEW.id;
          END IF;
        END IF;
        CONTINUE;
      ELSE
        INSERT INTO tags (gtu_ref, group_ref, tag_indexed, tag, group_type, sub_group_type )
        VALUES ( NEW.gtu_ref, NEW.id, entry_row.u_tag, entry_row.tags, NEW.group_name, NEW.sub_group_name);
      END IF;
    END LOOP;

    CLOSE curs_entry;

    UPDATE gtu
    SET tag_values_indexed = (SELECT array_agg(tags_list)
                              FROM (SELECT lineToTagRows(tag_agg) AS tags_list
                                    FROM (SELECT tag_value AS tag_agg
                                          FROM tag_groups
                                          WHERE id <> NEW.id
                                            AND gtu_ref = NEW.gtu_ref
                                          UNION
                                          SELECT NEW.tag_value
                                         ) as tag_list_selection
                                   ) as tags_rows
                             )
    WHERE id = NEW.gtu_ref;

    DELETE FROM tags
           WHERE group_ref = NEW.id
              AND gtu_ref = NEW.gtu_ref
              AND fct_array_find(seen_el, tag_indexed ) IS NULL;
    RETURN NEW;
  ELSE
    UPDATE gtu
    SET tag_values_indexed = (SELECT array_agg(tags_list)
                              FROM (SELECT lineToTagRows(tag_agg) AS tags_list
                                    FROM (SELECT tag_value AS tag_agg
                                          FROM tag_groups
                                          WHERE id <> OLD.id
                                            AND gtu_ref = OLD.gtu_ref
                                         ) as tag_list_selection
                                   ) as tags_rows
                             )
    WHERE id = OLD.gtu_ref;
    RETURN NULL;
  END IF;
END;
$$;

CREATE OR REPLACE FUNCTION fct_cpy_updateHosts() RETURNS TRIGGER
language plpgsql
AS
$$
BEGIN
  IF TG_OP = 'UPDATE' THEN
    IF NEW.taxon_ref IS DISTINCT FROM OLD.taxon_ref THEN
      UPDATE specimens SET host_taxon_ref = NEW.taxon_ref WHERE host_specimen_ref = NEW.id AND id <> NEW.id;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;


CREATE OR REPLACE FUNCTION fct_cpy_updateSpecHostImpact() RETURNS TRIGGER
language plpgsql
AS
$$
DECLARE
  newTaxonRef specimens.host_taxon_ref%TYPE := 0;
BEGIN
  IF TG_OP = 'UPDATE' THEN
    IF NEW.host_specimen_ref IS DISTINCT FROM OLD.host_specimen_ref AND NEW.host_specimen_ref IS NOT NULL THEN
      SELECT taxon_ref INTO STRICT NEW.host_taxon_ref FROM specimens WHERE id = NEW.host_specimen_ref;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;

/**
* When adding or changing a collection,
* Impact changes on rights
*/
CREATE OR REPLACE FUNCTION fct_cpy_updateCollectionRights() RETURNS TRIGGER
language plpgsql
AS
$$
DECLARE
	db_user_type_val integer ;
BEGIN
  IF TG_OP = 'INSERT' THEN
    INSERT INTO collections_rights (collection_ref, user_ref, db_user_type)
    (SELECT NEW.id as coll_ref, NEW.main_manager_ref as mgr_ref, 4 as user_type
     UNION
     SELECT NEW.id as coll_ref, user_ref as mgr_ref, db_user_type as user_type
     FROM collections_rights
     WHERE collection_ref = NEW.parent_ref
       AND db_user_type = 4
    );
  ELSIF TG_OP = 'UPDATE' THEN
    IF NEW.main_manager_ref IS DISTINCT FROM OLD.main_manager_ref THEN
      SELECT db_user_type INTO db_user_type_val FROM collections_rights WHERE collection_ref = NEW.id AND user_ref = NEW.main_manager_ref;
      IF FOUND AND db_user_type_val is distinct from 4 THEN
        UPDATE collections_rights
        SET db_user_type = 4
        WHERE collection_ref = NEW.id
          AND user_ref = NEW.main_manager_ref;
      ELSE
        INSERT INTO collections_rights (collection_ref, user_ref, db_user_type)
        VALUES(NEW.id,NEW.main_manager_ref,4);
      END IF;
    END IF;
    IF NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
      INSERT INTO collections_rights (collection_ref, user_ref, db_user_type)
      (
        SELECT NEW.id, user_ref, db_user_type
        FROM collections_rights
        WHERE collection_ref = NEW.parent_ref
          AND db_user_type = 4
          AND user_ref NOT IN
            (
              SELECT user_ref
              FROM collections_rights
              WHERE collection_ref = NEW.id
            )
      );
    END IF;
  END IF;

  RETURN NEW;

EXCEPTION
  WHEN OTHERS THEN
    RAISE NOTICE 'An error occured: %', SQLERRM;
    RETURN NEW;
END;
$$;

/*
  Before updating a collections rights entry,
  check we don't do a modification which wouldn't be in accordance with main manager of the concerned collection
*/
CREATE OR REPLACE FUNCTION fct_chk_canUpdateCollectionsRights() RETURNS TRIGGER
language plpgsql
AS
$$
DECLARE
  mgrName varchar;
  booContinue boolean := false;
BEGIN
  /*Check an unpromotion occurs by modifying db_user_type explicitely or implicitely by replacing a user by an other
    or moving a user from one collection to an other
  */
  IF (NEW.db_user_type < 4 AND OLD.db_user_type >=4) OR NEW.collection_ref IS DISTINCT FROM OLD.collection_ref OR NEW.user_ref IS DISTINCT FROM OLD.user_ref THEN
    SELECT formated_name INTO mgrName
    FROM collections INNER JOIN users ON users.id = collections.main_manager_ref
    WHERE collections.id = OLD.collection_ref
      AND main_manager_ref = OLD.user_ref;
    /*If user concerned still main manager of the collection, cannot be updated*/
    IF FOUND THEN
      RAISE EXCEPTION 'This manager (%) cannot be updated because he/she is still defined as a main manager for this collection', mgrName;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;

/*
  After inserting, updating or deleting a user in collections_rights -> impact user rights
*/
CREATE OR REPLACE FUNCTION fct_cpy_updateUserRights() RETURNS TRIGGER
language plpgsql
AS
$$
DECLARE
  db_user_type_val integer ;
  booCollFound boolean;
  booContinue boolean;
BEGIN
  /*When updating main manager ref -> impact potentially db_user_type
    of new user chosen as manager
  */
  IF TG_TABLE_NAME = 'collections' THEN
    /*We take in count only an update
      An insertion as it's creating an entry in collections_rights will trigger this current trigger again ;)
    */
    IF TG_OP = 'UPDATE' THEN
      IF NEW.main_manager_ref IS DISTINCT FROM OLD.main_manager_ref THEN
        UPDATE users
        SET db_user_type = 4
        WHERE id = NEW.main_manager_ref
          AND db_user_type < 4;
      END IF;
    END IF;
  ELSE -- trigger on collections_rights table
    IF TG_OP = 'INSERT' THEN
      /*If user is promoted by inserting her/him
        with a higher db_user_type than she/he is -> promote her/him
      */
      UPDATE users
      SET db_user_type = NEW.db_user_type
      WHERE id = NEW.user_ref
        AND db_user_type < NEW.db_user_type;
    END IF;
    IF TG_OP = 'UPDATE' THEN
      /*First case: replacing a user by an other*/
      IF NEW.user_ref IS DISTINCT FROM OLD.user_ref THEN
        /*Update the user db_user_type chosen as the new one as if it would be an insertion*/
        UPDATE users
        SET db_user_type = NEW.db_user_type
        WHERE id = NEW.user_ref
          AND db_user_type < NEW.db_user_type;
        /*Un promote the user replaced if necessary*/
        UPDATE users
          SET db_user_type = subq.db_user_type_max
          FROM (
                SELECT COALESCE(MAX(db_user_type),1) as db_user_type_max
                FROM collections_rights
                WHERE user_ref = OLD.user_ref
              ) subq
          WHERE id = OLD.user_ref
            AND db_user_type != 8;
      END IF;
      IF NEW.db_user_type IS DISTINCT FROM OLD.db_user_type THEN
        /* Promotion */
        IF NEW.db_user_type > OLD.db_user_type THEN
          UPDATE users
          SET db_user_type = NEW.db_user_type
          WHERE id = NEW.user_ref
            AND db_user_type < NEW.db_user_type;
        /* Unpromotion */
        ELSE
          UPDATE users
          SET db_user_type = subq.db_user_type_max
          FROM (
                SELECT COALESCE(MAX(db_user_type),1) as db_user_type_max
                FROM collections_rights
                WHERE user_ref = NEW.user_ref
              ) subq
          WHERE id = NEW.user_ref
            AND db_user_type != 8;
        END IF;
      END IF;
    END IF;
    IF TG_OP = 'DELETE' THEN
      IF OLD.db_user_type >=4 THEN
        SELECT true
        INTO booCollFound
        FROM collections
        WHERE id = OLD.collection_ref
          AND main_manager_ref = OLD.user_ref;
        IF FOUND THEN
          RAISE EXCEPTION 'You try to delete a manager who is still defined as a main manager of the current collection';
        END IF;
      END IF;
      UPDATE users
      SET db_user_type = subq.db_user_type_max
      FROM (
            SELECT COALESCE(MAX(db_user_type),1) as db_user_type_max
            FROM collections_rights
            WHERE user_ref = OLD.user_ref
           ) subq
      WHERE id = OLD.user_ref
        AND db_user_type != 8;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION getTagsIndexedAsArray(IN tagList varchar) returns varchar[] LANGUAGE SQL IMMUTABLE AS
$$
  SELECT array_agg(tags) FROM (SELECT lineToTagRows($1) as tags) as subQuery;
$$;


CREATE OR REPLACE FUNCTION fct_update_darwin_flat() returns TRIGGER
language plpgsql
AS
$$
DECLARE
  indCount INTEGER := 0;
  partCount INTEGER := 0;
  indType BOOLEAN := false;
BEGIN
  IF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'expeditions' THEN
    IF NEW.name_indexed IS DISTINCT FROM OLD.name_indexed THEN
      UPDATE specimens
      SET (expedition_name, expedition_name_ts, expedition_name_indexed) =
          (NEW.name, NEW.name_ts, NEW.name_indexed)
      WHERE expedition_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'collections' THEN
    IF OLD.collection_type IS DISTINCT FROM NEW.collection_type
    OR OLD.code IS DISTINCT FROM NEW.code
    OR OLD.name IS DISTINCT FROM NEW.name
    OR OLD.is_public IS DISTINCT FROM NEW.is_public
    OR OLD.path IS DISTINCT FROM NEW.path
    THEN
      UPDATE specimens
      SET (collection_type, collection_code, collection_name, collection_is_public,
          collection_parent_ref, collection_path
          ) =
          (NEW.collection_type, NEW.code, NEW.name, NEW.is_public,
           NEW.parent_ref, NEW.path
          )
      WHERE collection_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'gtu' THEN
    UPDATE specimens
    SET (gtu_code, gtu_parent_ref, gtu_path, gtu_from_date, gtu_from_date_mask,
        gtu_to_date, gtu_to_date_mask, gtu_tag_values_indexed, gtu_location
        ) =
        (NEW.code, NEW.parent_ref, NEW.path, NEW.gtu_from_date, NEW.gtu_from_date_mask,
        NEW.gtu_to_date, NEW.gtu_to_date_mask, NEW.tag_values_indexed, new.location
        )
    WHERE gtu_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'igs' THEN
    IF NEW.ig_num_indexed IS DISTINCT FROM OLD.ig_num_indexed OR NEW.ig_date IS DISTINCT FROM OLD.ig_date THEN
      UPDATE specimens
      SET (ig_num, ig_num_indexed, ig_date, ig_date_mask) =
          (NEW.ig_num, NEW.ig_num_indexed, NEW.ig_date, NEW.ig_date_mask)
      WHERE ig_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'taxonomy' THEN
    UPDATE specimens
    SET (taxon_name, taxon_name_indexed, taxon_name_order_by,
         taxon_level_ref, taxon_level_name,
         taxon_status, taxon_path, taxon_parent_ref, taxon_extinct
        ) =
        (NEW.name, NEW.name_indexed, NEW.name_order_by,
         NEW.level_ref, subq.level_name,
         NEW.status, NEW.path, NEW.parent_ref, NEW.extinct
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE taxon_ref = NEW.id;
    UPDATE specimens
    SET (host_taxon_name, host_taxon_name_indexed, host_taxon_name_order_by,
         host_taxon_level_ref, host_taxon_level_name,
         host_taxon_status, host_taxon_path, host_taxon_parent_ref, host_taxon_extinct
        ) =
        (NEW.name, NEW.name_indexed, NEW.name_order_by,
         NEW.level_ref, subq.level_name,
         NEW.status, NEW.path, NEW.parent_ref, NEW.extinct
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE host_taxon_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'chronostratigraphy' THEN
    UPDATE specimens
    SET (chrono_name, chrono_name_indexed, chrono_name_order_by,
         chrono_level_ref, chrono_level_name,
         chrono_status, 
         chrono_local, chrono_color,
         chrono_path, chrono_parent_ref
        ) =
        (NEW.name, NEW.name_indexed, NEW.name_order_by,
         NEW.level_ref, subq.level_name,
         NEW.status, 
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE chrono_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'lithostratigraphy' THEN
    UPDATE specimens
    SET (litho_name, litho_name_indexed, litho_name_order_by,
         litho_level_ref, litho_level_name,
         litho_status, 
         litho_local, litho_color,
         litho_path, litho_parent_ref
        ) =
        (NEW.name, NEW.name_indexed, NEW.name_order_by,
         NEW.level_ref, subq.level_name,
         NEW.status, 
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE litho_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'lithology' THEN
    UPDATE specimens
    SET (lithology_name, lithology_name_indexed, lithology_name_order_by,
         lithology_level_ref, lithology_level_name,
         lithology_status, 
         lithology_local, lithology_color,
         lithology_path, lithology_parent_ref
        ) =
        (NEW.name, NEW.name_indexed, NEW.name_order_by,
         NEW.level_ref, subq.level_name,
         NEW.status, 
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE lithology_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'mineralogy' THEN
    UPDATE specimens
    SET (mineral_name, mineral_name_indexed, mineral_name_order_by,
         mineral_level_ref, mineral_level_name,
         mineral_status, 
         mineral_local, mineral_color,
         mineral_path, mineral_parent_ref
        ) =
        (NEW.name, NEW.name_indexed, NEW.name_order_by,
         NEW.level_ref, subq.level_name,
         NEW.status, 
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE mineral_ref = NEW.id;
  
  ELSIF TG_TABLE_NAME = 'tag_groups' THEN
    IF TG_OP = 'INSERT' THEN
      IF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NEW.tag_value,
        gtu_country_tag_indexed = lineToTagArray(NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
    ELSIF TG_OP = 'UPDATE' THEN
      IF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NEW.tag_value,
        gtu_country_tag_indexed = lineToTagArray(NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;

      ELSIF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NULL,
        gtu_country_tag_indexed = NULL
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
    ELSIF TG_OP = 'DELETE' THEN
      IF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NULL,
        gtu_country_tag_indexed = NULL
        WHERE gtu_ref = OLD.gtu_ref;
      END IF;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;



CREATE OR REPLACE FUNCTION fct_update_specimen_flat() RETURNS TRIGGER
AS $$
DECLARE
  ROLD specimens%ROWTYPE;
  RNEW specimens%ROWTYPE;
BEGIN
  IF TG_OP = 'INSERT' THEN
    RNEW = NEW;
  ELSE
    RNEW = NEW;
    ROLD = OLD;
  END IF;

  IF ROLD.collection_ref IS DISTINCT FROM RNEW.collection_ref THEN
    SELECT collection_type, code, name, is_public, parent_ref, path
        INTO NEW.collection_type, NEW.collection_code, NEW.collection_name, NEW.collection_is_public,
          NEW.collection_parent_ref, NEW.collection_path
        FROM collections c
        WHERE c.id = NEW.collection_ref;
  END IF;

  IF ROLD.expedition_ref IS DISTINCT FROM RNEW.expedition_ref THEN
    SELECT  name , name_ts , name_indexed
        INTO NEW.expedition_name, NEW.expedition_name_ts, NEW.expedition_name_indexed
        FROM expeditions
        WHERE id = NEW.expedition_ref;
  END IF;
  
  IF ROLD.gtu_ref IS DISTINCT FROM RNEW.gtu_ref THEN
    SELECT code, parent_ref, path, location, gtu_from_date_mask, gtu_from_date,
          gtu_to_date_mask, gtu_to_date, tag_values_indexed, taggr.tag_value
      INTO NEW.gtu_code, NEW.gtu_parent_ref, NEW.gtu_path, NEW.gtu_location,
            NEW.gtu_from_date_mask, NEW.gtu_from_date, NEW.gtu_to_date_mask, NEW.gtu_to_date,
            NEW.gtu_tag_values_indexed, NEW.gtu_country_tag_value, NEW.gtu_country_tag_indexed
      FROM gtu g LEFT JOIN tag_groups taggr 
        ON g.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country'
      WHERE g.id = NEW.gtu_ref; 
  END IF;

  IF ROLD.taxon_ref IS DISTINCT FROM RNEW.taxon_ref THEN
    SELECT name, name_indexed, name_order_by, level_ref, taxon_level.level_name, status ,
          path, parent_ref, extinct
      INTO 
          NEW.taxon_name, NEW.taxon_name_indexed, NEW.taxon_name_order_by, NEW.taxon_level_ref,
          NEW.taxon_level_name, NEW.taxon_status, NEW.taxon_path, NEW.taxon_parent_ref, NEW.taxon_extinct
      FROM 
        taxonomy t INNER JOIN catalogue_levels taxon_level ON level_ref = taxon_level.id     
        WHERE t.id = NEW.taxon_ref;
  END IF;


  IF ROLD.chrono_ref IS DISTINCT FROM RNEW.chrono_ref THEN
    SELECT name, name_indexed , name_order_by, level_ref, 
        chrono_level.level_name, status, local_naming, color, path, parent_ref
      INTO 
          NEW.chrono_name, NEW.chrono_name_indexed, NEW.chrono_name_order_by, NEW.chrono_level_ref, NEW.chrono_level_name,
          NEW.chrono_status, NEW.chrono_local, NEW.chrono_color, NEW.chrono_path, NEW.chrono_parent_ref
      FROM 
        chronostratigraphy c INNER JOIN catalogue_levels chrono_level ON level_ref = chrono_level.id
        WHERE c.id = NEW.chrono_ref;
  END IF;

  IF ROLD.litho_ref IS DISTINCT FROM RNEW.litho_ref THEN

    SELECT name, name_indexed, name_order_by, level_ref, litho_level.level_name,
          status, local_naming, color, path, parent_ref
      INTO 
          NEW.litho_name, NEW.litho_name_indexed, NEW.litho_name_order_by, NEW.litho_level_ref, NEW.litho_level_name, NEW.litho_status,
          NEW.litho_local,  NEW.litho_color, NEW.litho_path,  NEW.litho_parent_ref
      FROM 
        lithostratigraphy l INNER JOIN catalogue_levels litho_level ON level_ref = litho_level.id
        WHERE l.id = NEW.litho_ref;
  END IF;

  IF ROLD.lithology_ref IS DISTINCT FROM RNEW.lithology_ref THEN
    SELECT name, name_indexed, name_order_by, level_ref, lithology_level.level_name, status,
          local_naming, color, path, parent_ref
      INTO 
          NEW.lithology_name, NEW.lithology_name_indexed, NEW.lithology_name_order_by, NEW.lithology_level_ref, NEW.lithology_level_name,
          NEW.lithology_status, NEW.lithology_local, NEW.lithology_color, NEW.lithology_path, NEW.lithology_parent_ref
      FROM 
        lithology l INNER JOIN catalogue_levels lithology_level ON level_ref = lithology_level.id
        WHERE l.id = NEW.lithology_ref;
  END IF;


  IF ROLD.mineral_ref IS DISTINCT FROM RNEW.mineral_ref THEN
    SELECT name, name_indexed, name_order_by, level_ref, mineral_level.level_name, status,
          local_naming, color, path, parent_ref
      INTO 
          NEW.mineral_name, NEW.mineral_name_indexed, NEW.mineral_name_order_by, NEW.mineral_level_ref, NEW.mineral_level_name, NEW.mineral_status,
          NEW.mineral_local, NEW.mineral_color, NEW.mineral_path, NEW.mineral_parent_ref
      FROM 
        mineralogy m INNER JOIN catalogue_levels mineral_level ON level_ref = mineral_level.id
        WHERE m.id = NEW.mineral_ref;
  END IF;


  IF ROLD.host_taxon_ref IS DISTINCT FROM RNEW.host_taxon_ref THEN
    SELECT name, name_indexed, name_order_by, level_ref, taxon_level.level_name, status ,
          path, parent_ref, extinct
      INTO 
          NEW.host_taxon_name, NEW.host_taxon_name_indexed, NEW.host_taxon_name_order_by, NEW.host_taxon_level_ref,
          NEW.host_taxon_level_name, NEW.host_taxon_status, NEW.host_taxon_path, NEW.host_taxon_parent_ref, NEW.host_taxon_extinct
      FROM 
        taxonomy t INNER JOIN catalogue_levels taxon_level ON level_ref = taxon_level.id     
        WHERE t.id = NEW.host_taxon_ref;
  END IF;

  IF ROLD.ig_ref IS DISTINCT FROM RNEW.ig_ref THEN
    SELECT ig_num, ig_date_mask, ig_date, ig_num_indexed
      INTO 
          NEW.ig_num, NEW.ig_date_mask, NEW.ig_date, NEW.ig_num_indexed
      FROM 
        igs
        WHERE id = NEW.ig_ref;
  END IF;

  RETURN NEW;
END;
$$
language plpgsql;

CREATE OR REPLACE FUNCTION convert_to_integer(v_input varchar) RETURNS INTEGER
AS $$
DECLARE v_int_value INTEGER DEFAULT 0;
BEGIN
    BEGIN
        v_int_value := v_input::INTEGER;
    EXCEPTION WHEN OTHERS THEN
/*        RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', v_input;*/
        RETURN 0;
    END;
RETURN v_int_value;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_searchCodes(VARIADIC varchar[]) RETURNS SETOF integer  AS $$
DECLARE
  sqlString varchar := E'select record_id from codes';
  sqlWhere varchar := '';
  code_part varchar;
  code_from varchar;
  code_to varchar;
  code_category varchar;
  relation varchar;
  word varchar;
BEGIN
  FOR i in 1 .. array_upper( $1, 1 ) BY 5 LOOP
    code_category := $1[i];
    code_part := $1[i+1];
    code_from := $1[i+2];
    code_to := $1[i+3];
    relation := $1[i+4] ;

    IF relation IS DISTINCT FROM '' AND i = 1 THEN
      sqlString := sqlString || ' where referenced_relation=' || quote_literal(relation) ;
    ELSIF i = 1 THEN
      sqlString := sqlString || E' where referenced_relation=\'specimens\''  ;
    END IF ;

    sqlWhere := sqlWhere || ' (code_category = ' || quote_literal(code_category) ;

    IF code_from ~ '^[0-9]+$' and code_to ~ '^[0-9]+$' THEN
      sqlWhere := sqlWhere || ' AND code_num BETWEEN ' || quote_literal(code_from) || ' AND ' || quote_literal(code_to) ;
    END IF;

    IF code_part != '' THEN
      sqlWhere := sqlWhere || ' AND (';
      FOR word IN (SELECT words FROM regexp_split_to_table(code_part, E'\\s+') as words) LOOP
        sqlWhere := sqlWhere || E' full_code_order_by like \'%\' || fullToIndex(' || quote_literal(word) || E') || \'%\' OR';
      END LOOP;
      sqlWhere := substr(sqlWhere,0,length(sqlWhere)-2) || ')';
    END IF;

    sqlWhere := sqlWhere || ') OR ';

  END LOOP;

  sqlString := sqlString || ' AND (' || substr(sqlWhere,0, length(sqlWhere)-2) || ')';
  RAISE INFO 'Sql : %',sqlString ;
  RETURN QUERY EXECUTE sqlString;
END;
$$ LANGUAGE plpgSQL STABLE;


CREATE OR REPLACE FUNCTION fct_search_tools (IN str_ids varchar) RETURNS SETOF integer
language SQL STABLE
AS
$$
    select distinct(specimen_ref) from specimen_collecting_tools where collecting_tool_ref in (select X::int from regexp_split_to_table($1,',') as X);
$$;

CREATE OR REPLACE FUNCTION fct_search_methods (IN str_ids varchar) RETURNS SETOF integer
language SQL STABLE
AS
$$
    select distinct(specimen_ref) from specimen_collecting_methods where collecting_method_ref in (select X::int from regexp_split_to_table($1,',') as X);
$$;

CREATE OR REPLACE FUNCTION dummy( in anyelement, inout anyelement )
AS $$
  select $2;
$$LANGUAGE 'sql' STABLE RETURNS NULL ON NULL INPUT;

CREATE AGGREGATE dummy_first (anyelement)
(
  sfunc = dummy,
  stype = anyelement
);



CREATE OR REPLACE FUNCTION fct_search_authorized_encoding_collections (user_id integer) RETURNS SETOF integer
language SQL STABLE
AS
$$
    select collection_ref from collections_rights where user_ref = $1 and db_user_type >= 2;
$$;

CREATE OR REPLACE FUNCTION fct_search_authorized_view_collections (user_id integer) RETURNS SETOF integer
language SQL STABLE
AS
$$
    select collection_ref from collections_rights where user_ref = $1

    UNION

    select id as collection_ref from collections where is_public = true;
$$;

/*
  Check when doing an action on specimen that the user is allowed to do it
*/
CREATE OR REPLACE FUNCTION fct_chk_specimenCollectionAllowed() RETURNS TRIGGER
language plpgSQL
AS
$$
DECLARE
  user_id integer;
  db_user_type_cpy smallint;
  col_ref integer;
BEGIN
  SELECT COALESCE(get_setting('darwin.userid'),'0')::integer INTO user_id;
  /*If no user id allows modification -> if we do a modif in SQL it should be possible*/
  IF user_id = 0 THEN
    IF TG_OP = 'DELETE' THEN
      RETURN OLD;
    END IF;
    RETURN NEW;
  END IF;
  /*If user_id <> 0, get db_user_type of user concerned*/
  SELECT db_user_type INTO db_user_type_cpy FROM users WHERE id = user_id;
  /*If admin allows whatever*/
  IF db_user_type_cpy = 8 THEN
    IF TG_OP = 'DELETE' THEN
      RETURN OLD;
    END IF;
    RETURN NEW;
  END IF;

  IF TG_TABLE_NAME = 'specimens' THEN
    IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
      IF NOT EXISTS (SELECT 1 FROM fct_search_authorized_encoding_collections (user_id) as r WHERE r = NEW.collection_ref) THEN
        RAISE EXCEPTION 'You don''t have the rights to insert into or update a specimen in this collection';
      END IF;
    ELSE /*Delete*/
      PERFORM true WHERE OLD.collection_ref::integer IN (SELECT * FROM fct_search_authorized_encoding_collections(user_id));
      IF NOT EXISTS (SELECT 1 FROM fct_search_authorized_encoding_collections (user_id) as r WHERE r = OLD.collection_ref) THEN
        RAISE EXCEPTION 'You don''t have the rights to delete a specimen from this collection';
      END IF;
    END IF;
  ELSIF TG_TABLE_NAME = 'specimen_individuals' THEN
    IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
      PERFORM true WHERE (SELECT collection_ref::integer FROM darwin_flat WHERE spec_ref = NEW.specimen_ref LIMIT 1) IN (SELECT * FROM fct_search_authorized_encoding_collections(user_id));
      IF NOT EXISTS(SELECT 1 from  specimens s
          INNER JOIN fct_search_authorized_encoding_collections (user_id) as r
            ON s.collection_ref = r
            WHERE s.id = NEW.specimen_ref) THEN
        RAISE EXCEPTION 'You don''t have the rights to insert into or update an individual in this collection';
      END IF;
    ELSE /*Delete*/
      IF EXISTS(SELECT 1 FROM specimens where id = OLD.specimen_ref) THEN
        IF NOT EXISTS(SELECT 1 from  specimens s
          INNER JOIN fct_search_authorized_encoding_collections (user_id) as r
            ON s.collection_ref = r
            WHERE s.id = OLD.specimen_ref) THEN
          RAISE EXCEPTION 'You don''t have the rights to delete an individual from this collection';
        END IF;
      END IF;
    END IF;
  ELSIF TG_TABLE_NAME = 'specimen_parts' THEN
    IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
      IF NOT EXISTS(SELECT 1 from  specimens s
          INNER JOIN specimen_individuals i on s.id = i.specimen_ref
          INNER JOIN fct_search_authorized_encoding_collections (user_id) as r
            ON s.collection_ref = r
            WHERE i.id = NEW.specimen_individual_ref) THEN

        RAISE EXCEPTION 'You don''t have the rights to insert into or update a part in this collection';
      END IF;
    ELSE /*Delete*/
      IF EXISTS(SELECT 1 FROM specimen_individuals where id = OLD.specimen_individual_ref) THEN
        IF NOT EXISTS(SELECT 1 from  specimens s
          INNER JOIN specimen_individuals i on s.id = i.specimen_ref
          INNER JOIN fct_search_authorized_encoding_collections (user_id) as r
            ON s.collection_ref = r
            WHERE i.id = NEW.specimen_individual_ref) THEN
          RAISE EXCEPTION 'You don''t have the rights to delete a part from this collection';
        END IF;
      END IF;
    END IF;
  END IF;
  IF TG_OP = 'DELETE' THEN
    RETURN OLD;
  END IF;
  RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION fct_cpy_location() RETURNS trigger
as $$
BEGIN

  IF TG_OP = 'INSERT' THEN
 -- IF (TG_OP = 'UPDATE' AND 
      NEW.location =  ST_Buffer(ST_GeographyFromText('SRID=4326;POINT(' || NEW.longitude || ' ' ||  NEW.latitude || ')'), NEW.lat_long_accuracy);
  ELSE IF NEW.longitude IS DISTINCT FROM OLD.longitude OR NEW.latitude IS DISTINCT FROM OLD.latitude OR NEW.lat_long_accuracy IS DISTINCT FROM OLD.lat_long_accuracy THEN
      NEW.location =  ST_Buffer(ST_GeographyFromText('SRID=4326;POINT(' || NEW.longitude || ' ' ||  NEW.latitude || ')'), NEW.lat_long_accuracy);
    END IF;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_filter_encodable_row(ids varchar, col_name varchar, user_id integer) RETURNS SETOF integer
AS $$
DECLARE
  rec_id integer;
BEGIN
    IF col_name = 'spec_ref' THEN    
      FOR rec_id IN SELECT id FROM specimens WHERE id in (select X::int from regexp_split_to_table(ids, '','' ) as X)
            AND collection_ref in (select X FROM fct_search_authorized_encoding_collections(user_id) as X)
      LOOP
        return next rec_id;
      END LOOP;

    ELSIF col_name = 'individual_ref' THEN
      FOR rec_id IN SELECT i.id FROM specimens s INNER JOIN specimen_individuals i ON s.id = i.specimen_ref
            WHERE i.id in (select X::int from regexp_split_to_table(ids, '','' ) as X)
            AND collection_ref in (select X FROM fct_search_authorized_encoding_collections(user_id) as X)
      LOOP
        return next rec_id;
      END LOOP;

    ELSIF col_name = 'part_ref' THEN
      FOR rec_id IN SELECT p.id FROM specimens s INNER JOIN specimen_individuals i ON s.id = i.specimen_ref
            INNER JOIN specimen_parts p ON i.id = p.specimen_individual_ref
            WHERE p.id in (select X::int from regexp_split_to_table(ids, '','' ) as X)
            AND collection_ref in (select X FROM fct_search_authorized_encoding_collections(user_id) as X)
      LOOP
        return next rec_id;
      END LOOP;
    END IF;

END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_cpy_updateMyWidgetsColl() RETURNS TRIGGER
language plpgsql
AS
$$
DECLARE
  booContinue boolean := false;
BEGIN
  IF TG_TABLE_NAME = 'collections_rights' THEN
    IF TG_OP = 'DELETE' THEN
      booContinue := true;
    ELSE
      IF OLD.collection_ref IS DISTINCT FROM NEW.collection_ref OR OLD.user_ref IS DISTINCT FROM NEW.user_ref THEN
        booContinue := true;
      END IF;
    END IF;
    IF booContinue THEN
      /*!!! Whats done is only removing the old collection reference from list of collections set in widgets !!!
        !!! We considered the add of widgets available for someone in a collection still be a manual action !!!
      */
      UPDATE my_widgets
      SET collections = regexp_replace(collections, E'\,' || OLD.collection_ref || E'\,', E'\,', 'g')
      WHERE user_ref = OLD.user_ref
        AND collections ~ (E'\,' || OLD.collection_ref || E'\,');
    END IF;
  END IF;
  IF TG_OP = 'DELETE' THEN
    RETURN OLD;
  END IF;
  RETURN NEW;
END;
$$;

/*Check that when specifying a parent collection the institution given is the same as the one used for parent*/
CREATE OR REPLACE FUNCTION fct_chk_parentCollInstitution() RETURNS TRIGGER
language plpgSQL
AS
$$
DECLARE
  institutionRef integer;
  booContinue boolean := false;
BEGIN
  IF TG_OP = 'INSERT' THEN
    booContinue := true;
  ELSIF TG_OP = 'UPDATE' THEN
    IF NEW.institution_ref IS DISTINCT FROM OLD.institution_ref OR NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
      booContinue := true;
    END IF;
  END IF;
  IF booContinue THEN
    IF NEW.parent_ref IS NOT NULL THEN
      SELECT institution_ref INTO institutionRef FROM collections WHERE id = NEW.parent_ref;
      IF institutionRef != NEW.institution_ref THEN
        RAISE EXCEPTION 'You tried to insert or update a collection with an other institution than the one given for the parent collection';
      END IF;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;

/*When updating institution reference of a collection -> impact this modification on all children collections*/
CREATE OR REPLACE FUNCTION fct_cpy_updateCollInstitutionCascade() RETURNS TRIGGER
language plpgsql
AS
$$
BEGIN
  IF NEW.institution_ref IS DISTINCT FROM OLD.institution_ref THEN
    UPDATE collections
    SET institution_ref = NEW.institution_ref
    WHERE id != NEW.id
      AND parent_ref = NEW.id;
  END IF;
  RETURN NEW;
END;
$$;

/*When unpromoting a user we have to remove the non availble columns from preferences*/
CREATE OR REPLACE FUNCTION fct_unpromotion_impact_prefs() RETURNS TRIGGER
language plpgSQL
AS
$$
DECLARE
  saved_search_row RECORD;
BEGIN
  IF NEW.db_user_type IS DISTINCT FROM OLD.db_user_type AND NEW.db_user_type = 1 THEN
    UPDATE preferences
    SET pref_value = subq.fields_available
    FROM (select array_to_string(array(select fields_list 
                                       from regexp_split_to_table((SELECT pref_value 
                                                                   FROM preferences 
                                                                   WHERE user_ref = NEW.id 
                                                                     AND pref_key = 'search_cols_part' 
                                                                   LIMIT 1
                                                                  ), E'\\|') as fields_list
                                       where fields_list not in ('institution_ref', 'building', 'floor', 'room', 'row', 'shelf', 'container', 'container_type', 'container_storage', 'sub_container', 'sub_container_type', 'sub_container_storage')
                                      ),'|'
                                ) as fields_available
         ) subq
    WHERE user_ref = NEW.id
      AND pref_key = 'search_cols_part';
    FOR saved_search_row IN SELECT id, visible_fields_in_result FROM my_saved_searches WHERE user_ref = NEW.id AND subject = 'part' LOOP
      UPDATE my_saved_searches
      SET visible_fields_in_result = subq.fields_available
      FROM (select array_to_string(array(select fields_list
                                         from regexp_split_to_table(saved_search_row.visible_fields_in_result, E'\\|') as fields_list 
                                         where fields_list not in ('institution_ref','building', 'floor', 'room', 'row', 'shelf', 'container', 'container_type', 'container_storage', 'sub_container', 'sub_container_type', 'sub_container_storage')
                                        ),'|'
                                  ) as fields_available
          ) subq
      WHERE id = saved_search_row.id;
    END LOOP;
  END IF;
  RETURN NEW;
END;
$$;

/*Function to split a catalogue unit path and try to find the corresponding unit opf a given level*/
create or replace function getSpecificParentForLevel(referenced_relation IN catalogue_levels.level_type%TYPE, path IN template_classifications.path%TYPE, level_searched IN catalogue_levels.level_name%TYPE) RETURNS template_classifications.name%TYPE LANGUAGE plpgsql AS
$$
DECLARE
  response template_classifications.name%TYPE := ''; 
BEGIN
  EXECUTE
  'SELECT name ' ||
  ' FROM ' 
  || quote_ident(lower(referenced_relation)) || ' cat '
  ' INNER JOIN catalogue_levels ON cat.level_ref = catalogue_levels.id '
  ' WHERE level_name = '
  || quote_literal(lower(level_searched)) || 
  '   AND cat.id IN (SELECT i_id::integer FROM regexp_split_to_table(' || quote_literal(path) || E', E''\/'') as i_id WHERE i_id != '''')' 
  INTO response;
  RETURN response;
EXCEPTION
  WHEN OTHERS THEN
    RAISE WARNING 'Error in getSpecificParentForLevel: %', SQLERRM;
    RETURN response;
END;
$$;


CREATE OR REPLACE FUNCTION fct_add_in_dict(ref_relation text, ref_field text, old_value text, dict_val text) RETURNS boolean
AS
$$
DECLARE
  query_str varchar;
BEGIN
  IF dict_val is NULL OR old_value IS NOT DISTINCT FROM dict_val THEN 
    RETURN TRUE;
  END IF;
    query_str := ' INSERT INTO flat_dict (referenced_relation, dict_field, dict_value)
    (
      SELECT ' || quote_literal(ref_relation) || ' , ' || quote_literal(ref_field) || ', ' || quote_literal(dict_val) || ' WHERE NOT EXISTS
      (SELECT id FROM flat_dict WHERE
        referenced_relation = ' || quote_literal(ref_relation) || '
        AND dict_field = ' || quote_literal(ref_field) || '
        AND dict_value = ' || quote_literal(dict_val) || ')
    );';
    execute query_str;
    RETURN true;
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_del_in_dict(ref_relation text, ref_field text, dict_val text, old_value text) RETURNS boolean
AS $$
DECLARE
  result integer;
  query_str text;
BEGIN
  IF dict_val is NULL OR old_value IS NOT DISTINCT FROM dict_val THEN 
    RETURN TRUE;
  END IF;
  query_str := ' SELECT 1 WHERE EXISTS( SELECT id from ' || quote_ident(ref_relation) || ' where ' || quote_ident(ref_field) || ' = ' || quote_literal(dict_val) || ');';
  execute query_str into result;

  IF result IS NULL THEN
    DELETE FROM flat_dict where 
          referenced_relation = ref_relation
          AND dict_field = ref_field
          AND dict_value = dict_val;
  END IF;
  RETURN TRUE;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION trg_del_dict() RETURNS TRIGGER
AS $$
DECLARE
  oldfield RECORD;
  newfield RECORD;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      oldfield = OLD;
      newfield = NEW;
    ELSE --DELETE
      oldfield = OLD;
      execute 'select * from ' || TG_TABLE_NAME::text || ' where id = -15 ' into newfield;
    END IF;
    IF TG_TABLE_NAME = 'codes' THEN
      PERFORM fct_del_in_dict('codes','code_prefix_separator', oldfield.code_prefix_separator, newfield.code_prefix_separator);
      PERFORM fct_del_in_dict('codes','code_suffix_separator', oldfield.code_suffix_separator, newfield.code_suffix_separator);
    ELSIF TG_TABLE_NAME = 'collection_maintenance' THEN
      PERFORM fct_del_in_dict('collection_maintenance','action_observation', oldfield.action_observation, newfield.action_observation);
    ELSIF TG_TABLE_NAME = 'identifications' THEN
      PERFORM fct_del_in_dict('identifications','determination_status', oldfield.determination_status, newfield.determination_status);
    ELSIF TG_TABLE_NAME = 'people' THEN
      PERFORM fct_del_in_dict('people','sub_type', oldfield.sub_type, newfield.sub_type);
      PERFORM fct_del_in_dict('people','title', oldfield.title, newfield.title);
    ELSIF TG_TABLE_NAME = 'people_addresses' THEN
      PERFORM fct_del_in_dict('people_addresses','country', oldfield.country, newfield.country);
    ELSIF TG_TABLE_NAME = 'insurances' THEN
      PERFORM fct_del_in_dict('insurances','insurance_currency', oldfield.insurance_currency, newfield.insurance_currency);
    ELSIF TG_TABLE_NAME = 'mineralogy' THEN
      PERFORM fct_del_in_dict('mineralogy','cristal_system', oldfield.cristal_system, newfield.cristal_system);
    ELSIF TG_TABLE_NAME = 'specimen_individuals' THEN
      PERFORM fct_del_in_dict('specimen_individuals','type', oldfield.type, newfield.type);
      PERFORM fct_del_in_dict('specimen_individuals','type_group', oldfield.type_group, newfield.type_group);
      PERFORM fct_del_in_dict('specimen_individuals','type_search', oldfield.type_search, newfield.type_search);
      PERFORM fct_del_in_dict('specimen_individuals','sex', oldfield.sex, newfield.sex);
      PERFORM fct_del_in_dict('specimen_individuals','state', oldfield.state, newfield.state);
      PERFORM fct_del_in_dict('specimen_individuals','stage', oldfield.stage, newfield.stage);
      PERFORM fct_del_in_dict('specimen_individuals','social_status', oldfield.social_status, newfield.social_status);
      PERFORM fct_del_in_dict('specimen_individuals','rock_form', oldfield.rock_form, newfield.rock_form);
    ELSIF TG_TABLE_NAME = 'specimens' THEN
      PERFORM fct_del_in_dict('specimens','host_relationship', oldfield.host_relationship, newfield.host_relationship);
    ELSIF TG_TABLE_NAME = 'specimens_accompanying' THEN
      PERFORM fct_del_in_dict('specimens_accompanying','form', oldfield.form, newfield.form);
    ELSIF TG_TABLE_NAME = 'users' THEN
      PERFORM fct_del_in_dict('users','title', oldfield.title, newfield.title);
      PERFORM fct_del_in_dict('users','sub_type', oldfield.sub_type, newfield.sub_type);
    ELSIF TG_TABLE_NAME = 'users_addresses' THEN
      PERFORM fct_del_in_dict('users_addresses','country', oldfield.country, newfield.country);
    ELSIF TG_TABLE_NAME = 'specimen_parts' THEN
      PERFORM fct_del_in_dict('specimen_parts','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_del_in_dict('specimen_parts','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_del_in_dict('specimen_parts','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_del_in_dict('specimen_parts','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_del_in_dict('specimen_parts','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_del_in_dict('specimen_parts','row', oldfield.row, newfield.row);
      PERFORM fct_del_in_dict('specimen_parts','room', oldfield.room, newfield.room);
      PERFORM fct_del_in_dict('specimen_parts','floor', oldfield.floor, newfield.floor);
      PERFORM fct_del_in_dict('specimen_parts','building', oldfield.specimen_status, newfield.building);

  END IF;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION trg_ins_update_dict() RETURNS TRIGGER
AS $$
DECLARE
  oldfield RECORD;
  newfield RECORD;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      oldfield = OLD;
      newfield = NEW;
    ELSE --INSERT
      newfield = NEW;
      execute 'select * from ' || TG_TABLE_NAME::text || ' where id = -15 ' into oldfield;
    END IF;
    IF TG_TABLE_NAME = 'codes' THEN
      PERFORM fct_add_in_dict('codes','code_prefix_separator', oldfield.code_prefix_separator, newfield.code_prefix_separator);
      PERFORM fct_add_in_dict('codes','code_suffix_separator', oldfield.code_suffix_separator, newfield.code_suffix_separator);
    ELSIF TG_TABLE_NAME = 'collection_maintenance' THEN
      PERFORM fct_add_in_dict('collection_maintenance','action_observation', oldfield.action_observation, newfield.action_observation);
    ELSIF TG_TABLE_NAME = 'identifications' THEN
      PERFORM fct_add_in_dict('identifications','determination_status', oldfield.determination_status, newfield.determination_status);
    ELSIF TG_TABLE_NAME = 'people' THEN
      PERFORM fct_add_in_dict('people','sub_type', oldfield.sub_type, newfield.sub_type);
      PERFORM fct_add_in_dict('people','title', oldfield.title, newfield.title);
    ELSIF TG_TABLE_NAME = 'people_addresses' THEN
      PERFORM fct_add_in_dict('people_addresses','country', oldfield.country, newfield.country);
    ELSIF TG_TABLE_NAME = 'insurances' THEN
      PERFORM fct_add_in_dict('insurances','insurance_currency', oldfield.insurance_currency, newfield.insurance_currency);
    ELSIF TG_TABLE_NAME = 'mineralogy' THEN
      PERFORM fct_add_in_dict('mineralogy','cristal_system', oldfield.cristal_system, newfield.cristal_system);
    ELSIF TG_TABLE_NAME = 'specimen_individuals' THEN
      PERFORM fct_add_in_dict('specimen_individuals','type', oldfield.type, newfield.type);
      PERFORM fct_add_in_dict('specimen_individuals','type_group', oldfield.type_group, newfield.type_group);
      PERFORM fct_add_in_dict('specimen_individuals','type_search', oldfield.type_search, newfield.type_search);
      PERFORM fct_add_in_dict('specimen_individuals','sex', oldfield.sex, newfield.sex);
      PERFORM fct_add_in_dict('specimen_individuals','state', oldfield.state, newfield.state);
      PERFORM fct_add_in_dict('specimen_individuals','stage', oldfield.stage, newfield.stage);
      PERFORM fct_add_in_dict('specimen_individuals','social_status', oldfield.social_status, newfield.social_status);
      PERFORM fct_add_in_dict('specimen_individuals','rock_form', oldfield.rock_form, newfield.rock_form);
    ELSIF TG_TABLE_NAME = 'specimens' THEN
      PERFORM fct_add_in_dict('specimens','host_relationship', oldfield.host_relationship, newfield.host_relationship);
    ELSIF TG_TABLE_NAME = 'specimens_accompanying' THEN
      PERFORM fct_add_in_dict('specimens_accompanying','form', oldfield.form, newfield.form);
    ELSIF TG_TABLE_NAME = 'users' THEN
      PERFORM fct_add_in_dict('users','title', oldfield.title, newfield.title);
      PERFORM fct_add_in_dict('users','sub_type', oldfield.sub_type, newfield.sub_type);
    ELSIF TG_TABLE_NAME = 'users_addresses' THEN
      PERFORM fct_add_in_dict('users_addresses','country', oldfield.country, newfield.country);
    ELSIF TG_TABLE_NAME = 'specimen_parts' THEN
      PERFORM fct_add_in_dict('specimen_parts','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict('specimen_parts','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_add_in_dict('specimen_parts','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_add_in_dict('specimen_parts','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_add_in_dict('specimen_parts','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_add_in_dict('specimen_parts','row', oldfield.row, newfield.row);
      PERFORM fct_add_in_dict('specimen_parts','room', oldfield.room, newfield.room);
      PERFORM fct_add_in_dict('specimen_parts','floor', oldfield.floor, newfield.floor);
      PERFORM fct_add_in_dict('specimen_parts','building', oldfield.specimen_status, newfield.building);
  END IF;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_upd_people_in_flat() RETURNS TRIGGER
AS
$$
DECLARE
  ref_field_id integer ;
  field_to_update varchar ;
  ref_field varchar := 'spec_ref' ;
  ref_relation varchar ;
  rec_exists boolean ;
BEGIN
/*
  IF TG_OP = 'DELETE' THEN
    IF OLD.people_type = 'collector' THEN
      field_to_update := 'spec_coll_ids';
      ref_field_id := OLD.record_id ;
    ELSIF OLD.people_type = 'donator' THEN
      field_to_update := 'spec_don_sel_ids';
      ref_field_id := OLD.record_id ; 
    ELSIF OLD.people_type = 'identifier' THEN
      SELECT record_id,referenced_relation INTO ref_field_id, ref_relation FROM identifications where id=OLD.record_id ;
      IF ref_field_id is NULL THEN
        RETURN OLD ;
      END IF ;      
      IF ref_relation = 'specimens' THEN
        field_to_update := 'spec_ident_ids';
      ELSIF ref_relation = 'specimen_individuals' THEN
        field_to_update := 'ind_ident_ids';
        ref_field := 'individual_ref' ;
      ELSE
        RETURN NEW;
      END IF ;
      EXECUTE 'SELECT true ' ||
              'FROM catalogue_people cp INNER JOIN identifications i ON cp.record_id = i.id AND cp.referenced_relation = ' || quote_literal('identifications') || ' ' ||
              'WHERE i.record_id = ' || quote_literal(ref_field_id) || 
              '  AND people_ref = ' || quote_literal(OLD.people_ref) || 
              '  AND i.referenced_relation = ' || quote_literal(ref_relation) INTO rec_exists ;
      IF rec_exists IS NOT NULL THEN
        RETURN OLD;
      END IF ;
    ELSE
      RETURN OLD ;
    END IF ;
  ELSE
    IF NEW.people_type = 'collector' THEN
      field_to_update := 'spec_coll_ids';
      ref_field_id := NEW.record_id;
    ELSIF NEW.people_type = 'donator' THEN
      field_to_update := 'spec_don_sel_ids';
      ref_field_id := NEW.record_id;  
    ELSIF NEW.people_type = 'identifier' THEN 
      SELECT record_id, referenced_relation INTO ref_field_id, ref_relation FROM identifications where id=NEW.record_id ;    
      IF (ref_relation = 'specimens') THEN
        field_to_update := 'spec_ident_ids';
      ELSIF ref_relation = 'specimen_individuals' THEN
        field_to_update := 'ind_ident_ids';
        ref_field := 'individual_ref' ;
      ELSE
        RETURN NEW;    
      END IF ;
    ELSE
      RETURN NEW ;      
    END IF;  
  END IF ;
  
  IF TG_OP = 'DELETE' THEN
    EXECUTE 'UPDATE darwin_flat ' ||
            'SET ' || quote_ident(field_to_update) || '= fct_remove_array_elem(' || quote_ident(field_to_update) || ',ARRAY[' || OLD.people_ref || ']) ' || 
            'WHERE ' || quote_ident(ref_field) || ' = ' || quote_literal(ref_field_id) ;
  ELSIF TG_OP = 'INSERT' THEN
    EXECUTE 'SELECT TRUE ' || 
            'WHERE EXISTS (SELECT id ' ||
            '              FROM darwin_flat ' ||
            '              WHERE ' || quote_ident(field_to_update) || ' && ARRAY[' || NEW.people_ref::integer || '] ' || 
            '                AND ' || quote_ident(ref_field) || ' = ' || quote_literal(ref_field_id) ||
            '             )' INTO rec_exists ;
    IF rec_exists = TRUE THEN
      RETURN NEW ;
    END IF;
    EXECUTE 'UPDATE darwin_flat ' ||
            'SET ' || quote_ident(field_to_update) || ' = array_append(' || quote_ident(field_to_update) || ',' || quote_literal(NEW.people_ref::integer) || ') ' || 
            'WHERE ' || quote_ident(ref_field) || ' = ' || quote_literal(ref_field_id) ;
  ELSE
    IF OLD.people_ref != NEW.people_ref THEN
      EXECUTE 'UPDATE darwin_flat ' ||
              'SET ' || quote_ident(field_to_update) || ' = array_append(fct_remove_array_elem(' || quote_ident(field_to_update) || ',ARRAY[' || OLD.people_ref || ']),' || quote_literal(NEW.people_ref::integer) || ') ' || 
              'WHERE ' || quote_ident(ref_field) || ' = ' || quote_literal(ref_field_id) ;
    END IF;
  END IF;  
*/
  RETURN NEW;
END;
$$ language plpgsql;

CREATE OR REPLACE FUNCTION fct_clear_identifiers_in_flat() RETURNS TRIGGER
AS
$$
DECLARE
  people_to_delete integer[] ;
  field_to_update varchar := 'spec_ident_ids';
  ref_field varchar := 'spec_ref' ;
BEGIN
    IF OLD.referenced_relation = 'specimen_individuals' THEN
      field_to_update := 'ind_ident_ids' ;
      ref_field := 'individual_ref' ;
    END IF;    
    /* 'IF FALSE SO THERE NO identifier associated to this identification' */
    IF NOT EXISTS(SELECT true FROM catalogue_people cp INNER JOIN identifications i ON cp.record_id = i.id AND cp.referenced_relation = 'identifications' where i.id=OLD.id) THEN
      RETURN OLD ;
    END IF ;     
    EXECUTE 'SELECT array_accum(people_ref) FROM catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND i.id =' || OLD.id || ' AND people_ref NOT in
    (SELECT people_ref from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation =' ||  quote_literal(TG_TABLE_NAME) ||
    ' AND p.people_type=' || quote_literal('identifier') || ' where i.record_id=' || OLD.record_id || ' AND i.referenced_relation=' || 
    quote_literal(OLD.referenced_relation) || ' AND i.id !=' || OLD.id || ')' INTO people_to_delete ;
    EXECUTE 'UPDATE darwin_flat
      SET ' || quote_ident(field_to_update) || '= fct_remove_array_elem(' || quote_ident(field_to_update) || ',' || quote_literal(people_to_delete) ||
      '::int[]) WHERE ' || quote_ident(ref_field) || ' = ' || OLD.record_id ;	      
  RETURN OLD;
END;
$$ language plpgsql;


CREATE OR REPLACE FUNCTION get_import_row() RETURNS integer AS $$

UPDATE imports SET state = 'loading' FROM (
  SELECT * FROM (
    SELECT  * FROM imports i1 WHERE i1.state = 'to_be_loaded' ORDER BY i1.created_at asc, id asc OFFSET 0 --thats important
  ) i2
  WHERE pg_try_advisory_lock('imports'::regclass::integer, i2.id)
  LIMIT 1
) i3
WHERE imports.id = i3.id RETURNING i3.id;
$$
LANGUAGE sql SECURITY DEFINER;


CREATE OR REPLACE FUNCTION fct_imp_checker_catalogue(line staging, catalogue_table text, prefix text)  RETURNS boolean
AS $$
DECLARE
  result_nbr integer :=0;
  ref_record RECORD;
  rec_id integer := null;
  line_store hstore;
  field_name text;
  field_level_name text;
  test text;
  ref refcursor;
BEGIN
    line_store := hstore(line);
    field_name := prefix || '_name';
    field_name := line_store->field_name;
    field_level_name := prefix || '_level_name';
    field_level_name := coalesce(line_store->field_level_name,'');

    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t 
    INNER JOIN catalogue_levels c on t.level_ref = c.id 
    WHERE name = ' || quote_literal( field_name) || ' AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END 
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;

    IF result_nbr = 1 THEN -- It's Ok!
      
      PERFORM fct_imp_checker_catalogues_parents(line,rec_id, catalogue_table, prefix);
      RETURN true;
    END IF;

    IF result_nbr >= 2 THEN
      UPDATE staging SET status = (status || (prefix => 'too_much')) where id= line.id;
      RETURN true;
    END IF;

    CLOSE ref;

  /*** Then CHECK fuzzy name ***/

  result_nbr := 0;
    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t 
    INNER JOIN catalogue_levels c on t.level_ref = c.id 
    WHERE name_order_by like fullToIndex(' || quote_literal( field_name) || ') || ''%'' AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END 
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;

    IF result_nbr = 1 THEN -- It's Ok!
      PERFORM fct_imp_checker_catalogues_parents(line,rec_id, catalogue_table, prefix);
      RETURN true;
    END IF;

    IF result_nbr >= 2 THEN
      UPDATE staging SET status = (status || (prefix => 'too_much')) where id= line.id;
      RETURN true;
    END IF;

    IF result_nbr = 0 THEN
      UPDATE staging SET status = (status || (prefix => 'not_found')) where id=line.id;
      RETURN true;
    END IF;

    CLOSE ref;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_manager(line staging)  RETURNS boolean
AS $$
BEGIN
  IF line.taxon_name IS NOT NULL AND line.taxon_name is distinct from '' AND line.taxon_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'taxonomy','taxon');
  END IF;
  
  IF line.chrono_name IS NOT NULL AND line.chrono_name is not null AND line.chrono_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'chronostratigraphy','chrono');
  END IF;
  
  IF line.lithology_name IS NOT NULL AND line.lithology_name is distinct from '' AND line.lithology_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'lithology','lithology');
  END IF;

  IF line.mineral_name IS NOT NULL AND line.mineral_name is distinct from '' AND line.mineral_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'mineralogy','mineral');
  END IF;

  IF line.litho_name IS NOT NULL AND line.litho_name is distinct from '' AND line.litho_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'lithostratigraphy','litho');
  END IF;



  PERFORM fct_imp_checker_igs(line);
  PERFORM fct_imp_checker_expeditions(line);
  PERFORM fct_imp_checker_gtu(line);
  PERFORM fct_imp_checker_people(line);
  RETURN true;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_imp_checker_catalogues_parents(line staging, rec_id integer, catalogue_table text, prefix text) RETURNS boolean
AS $$
DECLARE
  result_nbr integer :=0;
  row_record record;
  lvl_name varchar;
  lvl_value varchar;
  rec_parents hstore;
  line_store hstore;
  field_name text;
BEGIN
  line_store := hstore(line);
  field_name := prefix || '_parents';
  rec_parents := line_store->field_name;

  IF rec_parents is not null AND rec_parents != ''::hstore  AND rec_id is not null THEN
    EXECUTE 'select * from '|| quote_ident(catalogue_table) || ' where id = ' || rec_id into row_record ;

    FOR lvl_name in SELECT s FROM fct_explode_array(akeys(rec_parents)) as s
    LOOP 
      lvl_value := rec_parents->lvl_name;
      EXECUTE 'SELECT count(*) from ' || quote_ident(catalogue_table) || ' t
        INNER JOIN catalogue_levels c on t.level_ref = c.id
        WHERE level_sys_name = ' || quote_literal(lvl_name) || ' AND 
          name_order_by like fullToIndex( ' || quote_literal(lvl_value) || '  ) || ''%''
          AND ' || quote_literal(row_record.path) || 'like t.path || t.id || ''/%'' ' INTO result_nbr;
      IF result_nbr = 0 THEN
        EXECUTE 'UPDATE staging SET status = (status || ('|| quote_literal(prefix) || ' => ''bad_hierarchy'')), ' || prefix || '_ref = null where id=' || line.id;
        RETURN TRUE;
      END IF;
    END LOOP;
  END IF;
  EXECUTE 'UPDATE staging SET status = delete(status, ' || quote_literal(prefix) ||'), ' || prefix|| '_ref = ' || rec_id || ' where id=' || line.id; 

  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_igs(line staging, import boolean default false)  RETURNS boolean
AS $$
DECLARE
  ref_rec integer :=0;
BEGIN
  IF line.ig_num is null OR  line.ig_num = '' OR line.ig_ref is not null THEN
    RETURN true;
  END IF;

  select id into ref_rec from igs where ig_num = line.ig_num  and ig_date = COALESCE(line.ig_date,'01/01/0001');
  IF NOT FOUND THEN
    IF import THEN
        INSERT INTO igs (ig_num, ig_date_mask, ig_date)
        VALUES (line.ig_num,  COALESCE(line.ig_date_mask,line.ig_date_mask,'0'), COALESCE(line.ig_date,'01/01/0001'))
        RETURNING id INTO ref_rec;
    ELSE
    --UPDATE staging SET status = (status || ('igs' => 'not_found')), ig_ref = null where id=line.id;
      RETURN TRUE;
    END IF;
  END IF;

  UPDATE staging SET status = delete(status,'igs'), ig_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_imp_checker_expeditions(line staging, import boolean default false)  RETURNS boolean
AS $$
DECLARE
  ref_rec integer :=0;
BEGIN
  IF line.expedition_name is null OR line.expedition_name ='' OR line.expedition_ref is not null THEN
    RETURN true;
  END IF;

  select id into ref_rec from expeditions where name_indexed = fulltoindex(line.expedition_name) and 
    expedition_from_date = COALESCE(line.expedition_from_date,'01/01/0001') AND
    expedition_to_date = COALESCE(line.expedition_to_date,'31/12/2038');
  IF NOT FOUND THEN
      IF import THEN
        INSERT INTO expeditions (name, expedition_from_date, expedition_to_date, expedition_from_date_mask,expedition_to_date_mask)
        VALUES (
          line.expedition_name, COALESCE(line.expedition_from_date,'01/01/0001'),
          COALESCE(line.expedition_to_date,'31/12/2038'), COALESCE(line.expedition_from_date_mask,0),
          COALESCE(line.expedition_to_date_mask,0)
        )
        RETURNING id INTO ref_rec;
      ELSE
        RETURN TRUE;
      END IF;
  END IF;

  UPDATE staging SET status = delete(status,'expedition'), expedition_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$ LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION fct_imp_checker_gtu(line staging, import boolean default false)  RETURNS boolean
AS $$
DECLARE
  ref_rec integer :=0;
BEGIN
  IF line.gtu_code is null OR line.gtu_code  ='' OR line.gtu_ref is not null THEN
    RETURN true;
  END IF;

  select id into ref_rec from gtu where
    COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
    COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
    gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
    gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038')
    AND CASE WHEN (line.gtu_longitude is null and line.gtu_from_date is null and line.gtu_to_date is null) THEN line.gtu_code ELSE code END
      = code
    AND id != 0 LIMIT 1;
  

  IF NOT FOUND THEN
      IF import THEN
        INSERT into gtu
          (code, gtu_from_date_mask, gtu_from_date,gtu_to_date_mask, gtu_to_date, latitude, longitude, lat_long_accuracy, elevation, elevation_accuracy)
        VALUES
          (line.gtu_code, COALESCE(line.gtu_from_date_mask,0), COALESCE(line.gtu_from_date, '01/01/0001'),
          COALESCE(line.gtu_to_date_mask,0), COALESCE(line.gtu_to_date, '31/12/2038')
          , line.gtu_latitude, line.gtu_longitude, line.gtu_lat_long_accuracy, line.gtu_elevation, line.gtu_elevation_accuracy)
        RETURNING id INTO ref_rec;
        INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
          (
            SELECT ref_rec,group_name, sub_group_name, tag_value
              FROM staging_tag_groups WHERE staging_ref = line.id
          );
      ELSE
        RETURN TRUE;
      END IF;
  END IF;

  UPDATE staging SET status = delete(status,'gtu'), gtu_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_look_for_people(fullname text) RETURNS integer
AS $$
DECLARE
  ref_record integer :=0;
  result_nbr integer;
  searched_name text;
BEGIN
    result_nbr := 0;
    searched_name := fulltoindex(fullname)|| '%'  ;
    FOR ref_record IN SELECT id from people p 
      WHERE 
        formated_name_indexed like searched_name 
        OR  name_formated_indexed like searched_name LIMIT 2
    LOOP
      result_nbr := result_nbr +1;
    END LOOP;

    IF result_nbr = 1 THEN -- It's Ok!
      return ref_record;
    END IF;

    IF result_nbr >= 2 THEN
      return -1 ;-- To Much
      continue;
    END IF;
  RETURN 0;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_look_for_institution(fullname text) RETURNS integer
AS $$
DECLARE
  ref_record integer :=0;
  result_nbr integer;

BEGIN
    result_nbr := 0;
    FOR ref_record IN SELECT id from people p 
      WHERE is_physical = false  AND formated_name_indexed like fulltoindex(fullname) || '%'  LIMIT 2
    LOOP
      result_nbr := result_nbr +1;
    END LOOP;

    IF result_nbr = 1 THEN -- It's Ok!
      return ref_record;
    END IF;

    IF result_nbr >= 2 THEN
      return -1 ;-- To Much
      continue;
    END IF;
  RETURN 0;
END;
$$ LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION fct_imp_checker_people(line staging) RETURNS boolean
AS $$
DECLARE
  ref_record integer :=0;
  cnt integer :=-1;
  p_name text;
  merge_status integer :=1;
  ident_line RECORD;
  people_line RECORD ;
BEGIN


  --  Donators and collectors

  FOR people_line IN select * from staging_people WHERE referenced_relation ='staging' AND record_id = line.id 
  LOOP
    IF people_line.people_ref is not null THEN
      continue;
    END IF;
    SELECT fct_look_for_people(people_line.formated_name) into ref_record;
    CASE ref_record
      WHEN -1,0 THEN merge_status := -1 ;
      --WHEN 0 THEN merge_status := 0;
      ELSE
        UPDATE staging_people SET people_ref = ref_record WHERE id=people_line.id ;
    END CASE;
  END LOOP;
  IF merge_status = 1 THEN 
    UPDATE staging SET status = delete(status,'people') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('people' => 'people')) where id= line.id;  
  END IF;
  
  -- Indentifiers
   
  merge_status := 1 ; 
  FOR ident_line in select * from identifications where referenced_relation ='staging' AND  record_id = line.id
  LOOP
    FOR people_line IN select * from staging_people WHERE referenced_relation ='identifications' AND record_id = ident_line.id 
    LOOP
      IF people_line.people_ref is not null THEN
        continue;
      END IF;
      SELECT fct_look_for_people(people_line.formated_name) into ref_record;
      CASE ref_record
        WHEN -1,0 THEN merge_status := -1 ;
        --WHEN 0 THEN merge_status := 0;
        ELSE
          UPDATE staging_people SET people_ref = ref_record WHERE id=people_line.id ;
      END CASE;
    END LOOP;
  END LOOP;

  IF merge_status = 1 THEN 
    UPDATE staging SET status = delete(status,'identifiers') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('identifiers' => 'people')) where id= line.id;  
  END IF;

  /**********
  * Institution
  **********/
  IF line.institution_name IS NOT NULL and line.institution_name  != '' AND line.institution_ref is null THEN
  
    SELECT fct_look_for_institution(line.institution_name) into ref_record ;
      CASE ref_record
	WHEN -1 THEN 
	  UPDATE staging SET status = (status || ('institution' => 'too_much')) where id= line.id;  
	WHEN 0 THEN
	  UPDATE staging SET status = (status || ('institution' => 'not_found')) where id= line.id;  
	ELSE
	  UPDATE staging SET status = delete(status,'institution'), institution_ref = ref_record where id=line.id;
      END CASE;
  END IF;
  
  RETURN true;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_importer_dna(req_import_ref integer)  RETURNS boolean
AS $$
DECLARE
  prev_levels hstore default '';
  rec_id integer;
  people_id integer;
  line RECORD;
  s_line RECORD;
  people_line RECORD;
  staging_line staging;
  old_level int;
BEGIN
  FOR line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id 
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore  and parent_ref is null AND i.is_finished =  FALSE
  LOOP
    /************
    *
    *  DON'T FORGET TO MAKE A CHECK !
    *
     ***/
    IF exists(SELECT * from staging where path like '/' || line.id || '/%' and (status != ''::hstore or to_import = false) ) THEN
      --If line has childer with error, don't try to import it
      raise info 'Children with errors';
      continue;
    END IF;

    BEGIN
      --Import Specimen

      -- I know it's dumb but....
      select * into staging_line from staging where id = line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);
      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=line.id;

      BEGIN
	IF line.spec_ref is NULL THEN
	  rec_id := nextval('specimens_id_seq');
	  INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
	      host_taxon_ref, host_specimen_ref, host_relationship, acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref)
	  VALUES (rec_id, COALESCE(line.category,'physical') , line.collection_ref, COALESCE(line.expedition_ref,0),
          COALESCE(line.gtu_ref,0),
	    COALESCE(line.taxon_ref,0), COALESCE(line.litho_ref,0), COALESCE(line.chrono_ref,0),
	    COALESCE(line.lithology_ref,0), COALESCE(line.mineral_ref,0), COALESCE(line.host_taxon_ref,0),
	    line.host_specimen_ref, line.host_relationship, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0),
	    COALESCE(line.acquisition_date,'01/01/0001'), COALESCE(line.station_visible,true),  line.ig_ref
	  );
	  UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
    -- Import identifiers whitch identification have been updated to specimen
    INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
    SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
    DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;	  
	ELSE
	  rec_id = line.spec_ref;
	END IF;
	prev_levels := prev_levels || ('specimen' => rec_id::text);
      EXCEPTION WHEN unique_violation THEN
	SELECT id INTO rec_id FROM specimens WHERE
	  category = COALESCE(line.category,'physical')
	  AND collection_ref=line.collection_ref
	  AND expedition_ref= COALESCE(line.expedition_ref,0)
	  AND gtu_ref= COALESCE(line.gtu_ref,0)
	  AND taxon_ref= COALESCE(line.taxon_ref,0)
	  AND litho_ref = COALESCE(line.litho_ref,0)
	  AND chrono_ref = COALESCE(line.chrono_ref,0)
	  AND lithology_ref = COALESCE(line.lithology_ref,0)
	  AND mineral_ref = COALESCE(line.mineral_ref,0)
	  AND host_taxon_ref = COALESCE(line.host_taxon_ref,0)
	  AND host_specimen_ref = line.host_specimen_ref
	  AND host_relationship = line.host_relationship
	  AND acquisition_category = COALESCE(line.acquisition_category,'')
	  AND acquisition_date_mask = COALESCE(line.acquisition_date_mask,0)
	  AND acquisition_date = COALESCE(line.acquisition_date,'01/01/0001')
	  AND station_visible = COALESCE(line.station_visible,true)
	  AND ig_ref = line.ig_ref;	
	UPDATE staging SET status=(status || ('duplicate' => rec_id::text)) , to_import=false WHERE id = (prev_levels->'specimen')::integer;
	UPDATE staging SET to_import=false where path like '/' || line.id || '/%';
	CONTINUE;
    END;
      --Import lower levels
      FOR s_line IN  SELECT * from staging s where path like '/' || line.id || '/%' ORDER BY path || s.id
      LOOP
        IF s_line.level = 'individual' THEN
          rec_id := nextval('specimen_individuals_id_seq');
          INSERT INTO specimen_individuals (id, specimen_ref, type, sex, stage, state, social_status, rock_form, specimen_individuals_count_min, specimen_individuals_count_max)
          VALUES (
            rec_id,(prev_levels->'specimen')::integer, COALESCE(s_line.individual_type,'specimen'), 
            COALESCE(s_line.individual_sex,'undefined'), COALESCE( s_line.individual_state,'not applicable'),
            COALESCE(s_line.individual_stage,'undefined'), COALESCE(s_line.individual_social_status,'not applicable'),
            COALESCE(s_line.individual_rock_form,'not applicable'),
            COALESCE(s_line.individual_count_min,'1'), COALESCE(s_line.individual_count_max,'1')
          );       
          UPDATE template_table_record_ref SET referenced_relation ='specimen_individuals' , record_id = rec_id where referenced_relation ='staging' and record_id = s_line.id;
           -- Import identifiers whitch identification have been updated to specimen
          INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
          SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimen_individuals' ;
          DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimen_individuals') ;                 
          prev_levels := (prev_levels || ('individual' => rec_id::text));

        ELSIF lower(s_line.level) in ('specimen part','tissue part','dna part') THEN /*** @TODO:CHECK THIS!!**/
          rec_id := nextval('specimen_parts_id_seq');
          IF  lower(s_line.level) = 'specimen part' THEN
            old_level := null;
          ELSIF lower(s_line.level) = 'tissue part' THEN
            old_level :=  prev_levels->'specimen part';
          ELSIF lower(s_line.level) = 'dna part' THEN
            old_level :=  prev_levels->'tissue part';
          END IF;
          ALTER TABLE specimen_parts DISABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;
          INSERT INTO specimen_parts (id, parent_ref, specimen_individual_ref, specimen_part, complete, institution_ref, building, floor, room, row, shelf,
            container, sub_container, container_type, sub_container_type, container_storage, sub_container_storage, surnumerary, specimen_status,
              specimen_part_count_min, specimen_part_count_max)
          VALUES (
            rec_id, old_level, (prev_levels->'individual')::integer,
            COALESCE(s_line.part,'specimen'), COALESCE(s_line.complete,true),
            s_line.institution_ref, s_line.building ,s_line.floor, s_line.room, s_line.row, s_line.shelf,
            s_line.container, s_line.sub_container,
            COALESCE(s_line.container_type,'container'),  COALESCE(s_line.sub_container_type, 'container'), 
            COALESCE(s_line.container_storage,'dry'),  COALESCE(s_line.sub_container_storage,'dry'),
            COALESCE(s_line.surnumerary,false),  COALESCE(s_line.specimen_status,'good state'), 
            COALESCE(s_line.part_count_min,1),  COALESCE(s_line.part_count_max,2)
          );
          UPDATE template_table_record_ref SET referenced_relation ='specimen_parts' , record_id = rec_id where referenced_relation ='staging' and record_id = s_line.id;

          prev_levels := (prev_levels || (s_line.level => rec_id::text));

          ALTER TABLE specimen_parts ENABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;
        END IF; 
      END LOOP;
      -- Import staging people into catalogue people
      FOR people_line IN SELECT * from staging_people WHERE referenced_relation in ('specimens','specimen_individuals','specimen_parts') 
      LOOP     
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
      END LOOP;
      DELETE FROM staging_people WHERE referenced_relation in ('specimens','specimen_individuals','specimen_parts') ;

      DELETE from staging where path like '/' || line.id || '/%' OR  id = line.id;
    EXCEPTION WHEN unique_violation THEN
      RAISE info 'Error uniq_violation: %', SQLERRM;
      UPDATE staging SET status=(status || ('duplicate' => '0')) , to_import=false WHERE id = (prev_levels->'specimen')::integer;
      UPDATE staging SET to_import=false where path like '/' || line.id || '/%';

    END;
  END LOOP;

  IF EXISTS( select id FROM  staging WHERE import_ref = req_import_ref) THEN
    UPDATE imports set state = 'pending' where id = req_import_ref;
  ELSE
    UPDATE imports set state = 'finished', is_finished = true where id = req_import_ref;
  END IF;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_upd_people_staging_fields() RETURNS TRIGGER
AS $$
DECLARE
  import_id integer;
BEGIN
 IF get_setting('darwin.upd_people_ref') is null OR  get_setting('darwin.upd_people_ref') = '' THEN
    PERFORM set_config('darwin.upd_people_ref', 'ok', true);
    IF OLD.referenced_relation = 'staging' THEN
      select s.import_ref INTO import_id FROM staging s, staging_people sp WHERE sp.id=OLD.id AND sp.record_id = s.id ;
    ELSE
      select s.import_ref INTO import_id FROM staging s, staging_people sp, identifications i WHERE sp.id=OLD.id 
      AND sp.record_id = i.id AND i.record_id = s.id ;    
    END IF;
    
    UPDATE staging_people SET people_ref = NEW.people_ref WHERE id IN (
      SELECT sp.id from staging_people sp, identifications i, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id
      AND i.record_id = s.id AND sp.referenced_relation = 'identifications' AND sp.record_id = i.id 
      UNION
      SELECT sp.id from staging_people sp, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id AND
      sp.record_id = s.id AND sp.referenced_relation = 'staging' 
    ); 
    -- update status field, if all error people are corrected, statut 'people' or 'identifiers' will be removed
    PERFORM fct_imp_checker_people(s.*) FROM staging s WHERE import_ref = import_id AND (status::hstore ? 'people' OR status::hstore ? 'identifiers')  ; 
    PERFORM set_config('darwin.upd_imp_ref', NULL, true);
  END IF;
  RETURN NEW;     
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_upd_staging_fields() RETURNS TRIGGER
AS $$
BEGIN
  IF get_setting('darwin.upd_imp_ref') is null OR  get_setting('darwin.upd_imp_ref') = '' THEN
    PERFORM set_config('darwin.upd_imp_ref', 'ok', true);
    IF OLD.taxon_ref IS DISTINCT FROM NEW.taxon_ref AND  NEW.taxon_ref is not null THEN
        SELECT t.id ,t.name, t.level_ref , cl.level_name, t.status, t.extinct
        INTO NEW.taxon_ref,NEW.taxon_name, NEW.taxon_level_ref, NEW.taxon_level_name, NEW.taxon_status, NEW.taxon_extinct
        FROM taxonomy t, catalogue_levels cl 
        WHERE cl.id=t.level_ref AND t.id = NEW.taxon_ref;

        UPDATE staging set taxon_ref=NEW.taxon_ref, taxon_name = new.taxon_name, taxon_level_ref=new.taxon_level_ref, 
          taxon_level_name=new.taxon_level_name, taxon_status=new.taxon_status, taxon_extinct=new.taxon_extinct,
        status = delete(status,'taxon')

        WHERE 
          taxon_name  IS NOT DISTINCT FROM  old.taxon_name AND  taxon_level_ref IS NOT DISTINCT FROM old.taxon_level_ref AND 
          taxon_level_name IS NOT DISTINCT FROM old.taxon_level_name AND  taxon_status IS NOT DISTINCT FROM old.taxon_status 
          AND  taxon_extinct IS NOT DISTINCT FROM old.taxon_extinct
          AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'taxon');
    END IF;

    IF OLD.chrono_ref IS DISTINCT FROM NEW.chrono_ref  AND  NEW.chrono_ref is not null THEN
      SELECT c.id, c.name, c.level_ref, cl.level_name, c.status, c.local_naming, c.color, c.upper_bound, c.lower_bound
        INTO NEW.chrono_ref, NEW.chrono_name, NEW.chrono_level_ref, NEW.chrono_level_name, NEW.chrono_status, NEW.chrono_local, NEW.chrono_color, NEW.chrono_upper_bound, NEW.chrono_lower_bound
        FROM chronostratigraphy c, catalogue_levels cl 
        WHERE cl.id=c.level_ref AND c.id = NEW.chrono_ref ; 

        UPDATE staging set chrono_ref=NEW.chrono_ref, chrono_name = NEW.chrono_name, chrono_level_ref=NEW.chrono_level_ref, chrono_level_name=NEW.chrono_level_name, chrono_status=NEW.chrono_status,
        chrono_local=NEW.chrono_local, chrono_color=NEW.chrono_color, chrono_upper_bound=NEW.chrono_upper_bound, chrono_lower_bound=NEW.chrono_lower_bound,
        status = delete(status,'chrono')

        WHERE 
        chrono_name  IS NOT DISTINCT FROM  OLD.chrono_name AND  chrono_level_ref IS NOT DISTINCT FROM OLD.chrono_level_ref AND 
        chrono_level_name IS NOT DISTINCT FROM OLD.chrono_level_name AND  chrono_status IS NOT DISTINCT FROM OLD.chrono_status AND 
        chrono_local IS NOT DISTINCT FROM OLD.chrono_local AND  chrono_color IS NOT DISTINCT FROM OLD.chrono_color AND 
        chrono_upper_bound IS NOT DISTINCT FROM OLD.chrono_upper_bound AND  chrono_lower_bound IS NOT DISTINCT FROM OLD.chrono_lower_bound
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'chrono');

    END IF;

    IF OLD.litho_ref IS DISTINCT FROM NEW.litho_ref  AND  NEW.litho_ref is not null  THEN
      SELECT l.id,l.name, l.level_ref, cl.level_name, l.status, l.local_naming, l.color
      INTO NEW.litho_ref, NEW.litho_name, NEW.litho_level_ref, NEW.litho_level_name, NEW.litho_status, NEW.litho_local, NEW.litho_color
      FROM lithostratigraphy l, catalogue_levels cl 
      WHERE cl.id=l.level_ref AND l.id = NEW.litho_ref ; 

      UPDATE staging set 
        litho_ref=NEW.litho_ref, litho_name=NEW.litho_name, litho_level_ref=NEW.litho_level_ref, litho_level_name=NEW.litho_level_name,
        litho_status=NEW.litho_status, litho_local=NEW.litho_local, litho_color=NEW.litho_color,
        status = delete(status,'litho')

      WHERE 
        litho_name IS NOT DISTINCT FROM  OLD.litho_name AND litho_level_ref IS NOT DISTINCT FROM  OLD.litho_level_ref AND 
        litho_level_name IS NOT DISTINCT FROM  OLD.litho_level_name AND 
        NEW.litho_status IS NOT DISTINCT FROM  OLD.litho_status AND litho_local IS NOT DISTINCT FROM  OLD.litho_local AND litho_color=NEW.litho_color
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'litho');

    END IF;


    IF OLD.lithology_ref IS DISTINCT FROM NEW.lithology_ref  AND  NEW.lithology_ref is not null THEN
      SELECT l.id, l.name, l.level_ref, cl.level_name, l.status, l.local_naming, l.color
      INTO NEW.lithology_ref, NEW.lithology_name, NEW.lithology_level_ref, NEW.lithology_level_name, NEW.lithology_status, NEW.lithology_local, NEW.lithology_color
      FROM lithology l, catalogue_levels cl 
      WHERE cl.id=l.level_ref AND l.id = NEW.lithology_ref ; 

      UPDATE staging set 
        lithology_ref=NEW.lithology_ref, lithology_name=NEW.lithology_name, lithology_level_ref=NEW.lithology_level_ref,
        lithology_level_name=NEW.lithology_level_name, lithology_status=NEW.lithology_status, lithology_local=NEW.lithology_local,
        lithology_color=NEW.lithology_color,
        status = delete(status,'lithology')

      WHERE 
        lithology_name IS NOT DISTINCT FROM OLD.lithology_name AND  lithology_level_ref IS NOT DISTINCT FROM OLD.lithology_level_ref AND 
        lithology_level_name IS NOT DISTINCT FROM OLD.lithology_level_name AND  lithology_status IS NOT DISTINCT FROM OLD.lithology_status AND  lithology_local IS NOT DISTINCT FROM OLD.lithology_local AND 
        lithology_color IS NOT DISTINCT FROM OLD.lithology_color
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'lithology');

    END IF;


    IF OLD.mineral_ref IS DISTINCT FROM NEW.mineral_ref  AND  NEW.mineral_ref is not null THEN
      SELECT m.id, m.name, m.level_ref, cl.level_name, m.status, m.local_naming, m.color, m.path
      INTO NEW.mineral_ref, NEW.mineral_name, NEW.mineral_level_ref, NEW.mineral_level_name, NEW.mineral_status, NEW.mineral_local, NEW.mineral_color, NEW.mineral_path
      FROM mineralogy m, catalogue_levels cl 
      WHERE cl.id=m.level_ref AND m.id = NEW.mineral_ref ; 

      UPDATE staging set 
        mineral_ref=NEW.mineral_ref, mineral_name=NEW.mineral_name, mineral_level_ref=NEW.mineral_level_ref,
        mineral_level_name=NEW.mineral_level_name, mineral_status=NEW.mineral_status, mineral_local=NEW.mineral_local, 
        mineral_color=NEW.mineral_color, mineral_path=NEW.mineral_path,
        status = delete(status,'mineral')

      WHERE 
        mineral_name IS NOT DISTINCT FROM OLD.mineral_name AND  mineral_level_ref IS NOT DISTINCT FROM OLD.mineral_level_ref AND 
        mineral_level_name IS NOT DISTINCT FROM OLD.mineral_level_name AND  mineral_status IS NOT DISTINCT FROM OLD.mineral_status AND  mineral_local IS NOT DISTINCT FROM OLD.mineral_local AND  
        mineral_color IS NOT DISTINCT FROM OLD.mineral_color AND  mineral_path IS NOT DISTINCT FROM OLD.mineral_path
        AND import_ref = NEW.import_ref;

        NEW.status = delete(NEW.status,'mineral');

    END IF;

    IF OLD.expedition_ref IS DISTINCT FROM NEW.expedition_ref  AND  NEW.expedition_ref is not null THEN
      SELECT id, "name", expedition_from_date, expedition_to_date, expedition_from_date_mask , expedition_to_date_mask
      INTO NEW.expedition_ref, NEW.expedition_name, NEW.expedition_from_date, NEW.expedition_to_date, NEW.expedition_from_date_mask , NEW.expedition_to_date_mask
      FROM expeditions
      WHERE id = NEW.expedition_ref ;

      UPDATE staging set 
        expedition_ref=NEW.expedition_ref, expedition_name=NEW.expedition_name, expedition_from_date=NEW.expedition_from_date,
        expedition_to_date=NEW.expedition_to_date, expedition_from_date_mask=NEW.expedition_from_date_mask , expedition_to_date_mask=NEW.expedition_to_date_mask
      WHERE 
        expedition_name IS NOT DISTINCT FROM OLD.expedition_name AND  expedition_from_date IS NOT DISTINCT FROM OLD.expedition_from_date AND 
        expedition_to_date IS NOT DISTINCT FROM OLD.expedition_to_date AND  expedition_from_date_mask IS NOT DISTINCT FROM OLD.expedition_from_date_mask  AND
        expedition_to_date_mask IS NOT DISTINCT FROM OLD.expedition_to_date_mask
        AND import_ref = NEW.import_ref;

    END IF; 

    IF OLD.institution_ref IS DISTINCT FROM NEW.institution_ref  AND  NEW.institution_ref is not null THEN
      SELECT formated_name INTO NEW.institution_name FROM people WHERE id = NEW.institution_ref ;

      UPDATE staging set institution_ref = NEW.institution_ref, institution_name=NEW.institution_name,
        status = delete(status,'institution')
        WHERE
        institution_name IS NOT DISTINCT FROM OLD.institution_name
        AND import_ref = NEW.import_ref;

        NEW.status = delete(NEW.status,'institution');

    END IF;    

    PERFORM set_config('darwin.upd_imp_ref', NULL, true);
  END IF;
  RETURN NEW;        
END;
$$ LANGUAGE plpgsql;

create or replace function upsert (tableName in varchar, keyValues in hstore) returns text language plpgsql as
$$
declare
  insert_stmt varchar := 'insert into ' || quote_ident(tableName) || ' (';
  update_stmt varchar := 'update ' || quote_ident(tableName) || ' SET (';
  where_stmt varchar := ' WHERE ';
  iloop integer := 0;
  recUnqFields RECORD;
  newhst RECORD;
  lowerKeyValues hstore;
begin
  for newhst in (select * from each(keyValues)) loop
    if iloop = 0 then
      lowerKeyValues := hstore(lower(newhst.key), newhst.value);
    else
      lowerKeyValues := lowerKeyValues || hstore(lower(newhst.key), newhst.value);
    end if;
    iloop := iloop +1;
  end loop;
  iloop := 0;
  insert_stmt := insert_stmt || array_to_string(akeys(lowerKeyValues), ',') || ') VALUES (' || chr(39) || array_to_string(avals(lowerKeyValues), (chr(39) || ',' || chr(39))::text) || chr(39) ||')';
  begin
    execute insert_stmt;
  exception
    when unique_violation then
      begin
        for recUnqFields IN (select x.vals as field, column_default as defaultVal 
                             from (select regexp_split_to_table(trim(substr(indexdef,strpos(indexdef, '(')+1),')'),', ') as vals 
                                   from pg_indexes 
                                   where tablename = tableName 
                                     and strpos(indexdef, 'UNIQUE') > 0 
                                     and indexname = (select conname 
                                                      from pg_class inner join pg_constraint on pg_class.oid = pg_constraint.conrelid and relname = tableName and contype = 'p'
                                                     )
                                  ) as x 
                             inner join 
                             information_schema.columns on x.vals = column_name and table_name = tableName) loop
          if iloop > 0 then
            where_stmt := where_stmt || ' AND ';
          end if;
          iloop := iloop + 1;
          if lowerKeyValues ? recUnqFields.field then
            where_stmt := where_stmt || quote_ident(recUnqFields.field) || ' = ' || quote_literal(lowerKeyValues -> recUnqFields.field);
          else
            where_stmt := where_stmt || quote_ident(recUnqFields.field) || ' = ' || quote_literal(coalesce(recUnqFields.defaultVal,''));
          end if;
        end loop;
        update_stmt := update_stmt || array_to_string(akeys(lowerKeyValues), ',') || ') = (' || chr(39) || array_to_string(avals(lowerKeyValues), (chr(39) || ',' || chr(39))::text) || chr(39) ||')' || where_stmt;
        execute update_stmt;
        return 'updated';
      exception
        when others then
          return 'SQL error is: '::text || SQLERRM;
      end;
      return 'SQL error is: '::text || SQLERRM;
  end;
  return 'inserted';
end;
$$;
