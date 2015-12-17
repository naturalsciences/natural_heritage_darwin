
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
    temp_string := REPLACE(temp_string, 'Œ', 'oe');
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
    return temp_string;
END;
$$ LANGUAGE plpgsql IMMUTABLE;


CREATE OR REPLACE FUNCTION toUniqueStr(to_indexed varchar) RETURNS varchar STRICT
AS $$
DECLARE
    temp_string varchar;
BEGIN
    -- Investigate https://launchpad.net/postgresql-unaccent
    temp_string := to_indexed;
    temp_string := TRANSLATE(temp_string, E'  ¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿×&|@"\'#(§^!{})°$*][£µ`%+=~/.,?;:\\<>ł€¶ŧ←↓→«»¢“”_-','');
     --Remove ALL none alphanumerical char like # or '
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
DECLARE
  codeNum varchar;
BEGIN
        IF TG_TABLE_NAME = 'properties' THEN
                NEW.applies_to_indexed := COALESCE(fullToIndex(NEW.applies_to),'');
                NEW.method_indexed := COALESCE(fullToIndex(NEW.method),'');
        ELSIF TG_TABLE_NAME = 'chronostratigraphy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'collections' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'expeditions' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'bibliography' THEN
                NEW.title_indexed := fullToIndex(NEW.title);
        ELSIF TG_TABLE_NAME = 'identifications' THEN
                NEW.value_defined_indexed := COALESCE(fullToIndex(NEW.value_defined),'');
        ELSIF TG_TABLE_NAME = 'lithology' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'mineralogy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
                NEW.formule_indexed := fullToIndex(NEW.formule);
        ELSIF TG_TABLE_NAME = 'people' THEN
                NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.name_formated_indexed := fulltoindex(coalesce(NEW.given_name,'') || coalesce(NEW.family_name,''));
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
        ELSIF TG_TABLE_NAME = 'codes' THEN
                codeNum := coalesce(trim(regexp_replace(NEW.code, '[^0-9]','','g')), '');
                IF codeNum = '' THEN
                  NEW.code_num := 0;
                ELSE
                  NEW.code_num := codeNum::bigint;
                END IF;
                NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
        ELSIF TG_TABLE_NAME = 'tag_groups' THEN
                NEW.group_name_indexed := fullToIndex(NEW.group_name);
                NEW.sub_group_name_indexed := fullToIndex(NEW.sub_group_name);
        ELSIF TG_TABLE_NAME = 'taxonomy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'classification_keywords' THEN
                NEW.keyword_indexed := fullToIndex(NEW.keyword);
        ELSIF TG_TABLE_NAME = 'users' THEN
                NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
        ELSIF TG_TABLE_NAME = 'vernacular_names' THEN
                NEW.community_indexed := fullToIndex(NEW.community);
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'igs' THEN
                NEW.ig_num_indexed := fullToIndex(NEW.ig_num);
        ELSIF TG_TABLE_NAME = 'collecting_methods' THEN
                NEW.method_indexed := fullToIndex(NEW.method);
        ELSIF TG_TABLE_NAME = 'collecting_tools' THEN
                NEW.tool_indexed := fullToIndex(NEW.tool);
        ELSIF TG_TABLE_NAME = 'loans' THEN
                NEW.search_indexed := fullToIndex(COALESCE(NEW.name,'') || COALESCE(NEW.description,''));
        ELSIF TG_TABLE_NAME = 'multimedia' THEN
                NEW.search_indexed := fullToIndex ( COALESCE(NEW.title,'') ||  COALESCE(NEW.description,'') || COALESCE(NEW.extracted_info,'') ) ;
        ELSIF TG_TABLE_NAME = 'comments' THEN
                NEW.comment_indexed := fullToIndex(NEW.comment);
        ELSIF TG_TABLE_NAME = 'ext_links' THEN
                NEW.comment_indexed := fullToIndex(NEW.comment);
        ELSIF TG_TABLE_NAME = 'specimens' THEN
                NEW.object_name_indexed := fullToIndex(COALESCE(NEW.object_name,'') );
        END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;


/***
* fct_clr_specialstatus
* Check the type(special status) on specimens and update the search and group type
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

  IF newType = 'specimen' THEN
    NEW.type_search := 'specimen';
    NEW.type_group := 'specimen';
  ELSIF newType = 'type' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  ELSIF newType = 'subtype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  ELSIF newType = 'allotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'allotype';
  ELSIF newType = 'cotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'syntype';
  ELSIF newType = 'genotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  ELSIF newType = 'holotype' THEN
    NEW.type_search := 'holotype';
    NEW.type_group := 'holotype';
  ELSIF newType = 'hypotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'hypotype';
  ELSIF newType = 'lectotype' THEN
    NEW.type_search := 'lectotype';
    NEW.type_group := 'lectotype';
  ELSIF newType = 'locotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'locotype';
  ELSIF newType = 'neallotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  ELSIF newType = 'neotype' THEN
    NEW.type_search := 'neotype';
    NEW.type_group := 'neotype';
  ELSIF newType = 'paralectotype' THEN
    NEW.type_search := 'paralectotype';
    NEW.type_group := 'paralectotype';
  ELSIF newType = 'paratype' THEN
    NEW.type_search := 'paratype';
    NEW.type_group := 'paratype';
  ELSIF newType = 'plastotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'plastotype';
  ELSIF newType = 'plesiotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'plesiotype';
  ELSIF newType = 'syntype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'syntype';
  ELSIF newType = 'topotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'topotype';
  ELSIF newType = 'typeinlitteris' THEN
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
* fct_clear_referencedRecord
* Clear referenced record id for a table on delete record
*/
CREATE OR REPLACE FUNCTION fct_clear_referencedRecord() RETURNS TRIGGER
AS $$
BEGIN
  IF TG_OP ='UPDATE' THEN
    IF NEW.id != OLD.id THEN
      UPDATE template_table_record_ref SET record_id = NEW.id WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
    END IF;
  ELSEIF TG_OP = 'DELETE' THEN
    DELETE FROM template_table_record_ref where referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
  END IF;
  RETURN NULL;
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
  EXECUTE 'SELECT true WHERE EXISTS( SELECT * ' ||
          'from possible_upper_levels ' ||
          'where level_ref = ' || quote_literal(new_level_ref) ||
          '  and coalesce(level_upper_ref,0) = case when ' || quote_literal(coalesce(new_parent_ref,0)) || ' != '|| quote_literal(0) || ' then (select level_ref from ' || quote_ident(referenced_relation) || ' where id = ' || quote_literal(coalesce(new_parent_ref,0)) || ') else ' || quote_literal(coalesce(new_parent_ref,0)) || ' end' ||
          '                              )'
    INTO response;
  IF response IS NULL THEN
    RETURN FALSE;
  ELSE
    RETURN TRUE;
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

  EXECUTE 'SELECT count(id)  FROM ' || quote_ident(TG_TABLE_NAME::text) ||
    ' WHERE parent_ref=' || quote_literal(NEW.id) ||
    ' AND fct_chk_possible_upper_level('|| quote_literal(TG_TABLE_NAME::text) ||
    ', parent_ref, level_ref, id) = false ' INTO rec_exists;

  IF rec_exists > 0 THEN
    RAISE EXCEPTION 'Children of this record does not follow the level hierarchy';
  END IF;
  RETURN NEW;

END;
$$
language plpgsql;


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
  RETURN NEW;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_cpy_path() RETURNS TRIGGER
AS $$
BEGIN
  IF TG_OP = 'INSERT' THEN
      IF TG_TABLE_NAME::text = 'collections' THEN

        IF NEW.id = 0 THEN
          NEW.parent_ref := null;
        END IF;
        IF NEW.parent_ref IS NULL THEN
          NEW.path :='/';
        ELSE
          EXECUTE 'SELECT path || id || ' || quote_literal('/') ||' FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
        END IF;
      ELSIF TG_TABLE_NAME::text = 'people_relationships' THEN
        SELECT path || NEW.person_1_ref || '/' INTO NEW.path
          FROM people_relationships
          WHERE person_2_ref=NEW.person_1_ref;
        IF NEW.path is NULL THEN
          NEW.path := '/' || NEW.person_1_ref || '/';
        END IF;
      END IF;
    ELSIF TG_OP = 'UPDATE' THEN
      IF TG_TABLE_NAME::text = 'collections' THEN

        IF NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
          IF NEW.parent_ref IS NULL THEN
            NEW.path := '/';
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
              NEW.path := '/' || NEW.person_1_ref || '/';
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

          IF NEW.parent_ref IS NULL THEN
            NEW.path ='/';
          ELSE
            EXECUTE 'SELECT path || id || ''/'' FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
          END IF;
    ELSIF TG_OP = 'UPDATE' AND (TG_TABLE_NAME::text = 'taxonomy' OR
        TG_TABLE_NAME::text = 'lithology' OR
        TG_TABLE_NAME::text = 'lithostratigraphy' OR
        TG_TABLE_NAME::text = 'mineralogy' OR
        TG_TABLE_NAME::text = 'chronostratigraphy') THEN

        IF NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
          IF NEW.parent_ref IS NULL THEN
            NEW.path ='/';
          ELSIF OLD.parent_ref IS NOT DISTINCT FROM NEW.parent_ref THEN
            RETURN NEW;
          ELSE
            EXECUTE 'SELECT  path || id || ''/''  FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
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

DROP FUNCTION IF EXISTS fct_set_user(integer);

/**
 Set user id
*/
CREATE OR REPLACE FUNCTION fct_set_user(userid integer) RETURNS void
language SQL AS
$$
  select set_config('darwin.userid', $1::varchar, false) ;
  select CASE WHEN get_setting('application_name') ~ ' uid:\d+'
    THEN set_config('application_name', regexp_replace(get_setting('application_name') ,'uid:\d+',  'uid:' || $1::varchar), false)
    ELSE set_config('application_name', get_setting('application_name')  || ' uid:' || $1::varchar, false)
    END;
  update users_login_infos set last_seen = now() where user_ref = $1  and login_type='local';
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
  returnedRow RECORD;
BEGIN
  IF TG_OP IN ('INSERT', 'UPDATE') THEN
    returnedRow := NEW;
  ELSE
    returnedRow := OLD;
  END IF;
  SELECT COALESCE(CASE WHEN get_setting('darwin.track_level') = '' THEN NULL ELSE get_setting('darwin.track_level') END,'10')::integer INTO track_level;
  IF track_level = 0 THEN --NO Tracking
    RETURN returnedRow;
  ELSIF track_level = 1 THEN -- Track Only Main tables
    IF TG_TABLE_NAME::text NOT IN ('specimens', 'taxonomy', 'chronostratigraphy', 'lithostratigraphy',
      'mineralogy', 'lithology', 'people', 'loans', 'loan_items') THEN
      RETURN returnedRow;
    END IF;
  END IF;

  SELECT COALESCE(CASE WHEN get_setting('darwin.userid') = '' THEN NULL ELSE get_setting('darwin.userid') END,'0')::integer INTO user_id;
  IF user_id = 0 OR  user_id = -1 THEN
    RETURN returnedRow;
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
CREATE OR REPLACE FUNCTION fct_cpy_length_conversion (IN property real, IN property_unit text) RETURNS real
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
CREATE OR REPLACE FUNCTION fct_cpy_temperature_conversion (IN property real, IN property_unit text) RETURNS real
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
CREATE OR REPLACE FUNCTION fct_cpy_time_conversion (IN property real, IN property_unit text) RETURNS real
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
CREATE OR REPLACE FUNCTION fct_cpy_speed_conversion (IN property real, IN property_unit text) RETURNS real
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
CREATE OR REPLACE FUNCTION fct_cpy_volume_conversion (IN property real, IN property_unit text) RETURNS real
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
CREATE OR REPLACE FUNCTION fct_cpy_weight_conversion (IN property real, IN property_unit text) RETURNS real
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
CREATE OR REPLACE FUNCTION convert_to_unified (IN property varchar, IN property_unit varchar) RETURNS float
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
      RETURN null;

      WHEN OTHERS THEN
        RETURN null;
    END;

    IF property_unit IN ('Kt', 'Beaufort', 'm/s') THEN
        RETURN fct_cpy_speed_conversion(r_val, property_unit)::text;
    END IF;

    IF property_unit IN ( 'g', 'hg', 'kg', 'ton', 'dg', 'cg', 'mg', 'lb', 'lbs', 'pound' , 'ounce' , 'grain') THEN
        RETURN fct_cpy_weight_conversion(r_val, property_unit)::text;
    END IF;

    IF property_unit IN ('m³', 'l', 'cm³', 'ml', 'mm³' ,'µl' , 'µm³' , 'km³', 'Ml' , 'hl') THEN
        RETURN fct_cpy_volume_conversion(r_val, property_unit)::text;
    END IF;

    IF property_unit IN ('K', '°C', '°F', '°Ra', '°Re', '°r', '°N', '°Rø', '°De') THEN
        RETURN fct_cpy_temperature_conversion(r_val, property_unit)::text;
    END IF;

    IF property_unit IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms') THEN
        RETURN fct_cpy_length_conversion(r_val, property_unit)::text;
    END IF;

    RETURN  property;

END;
$$;


CREATE OR REPLACE FUNCTION is_property_unit_in_group(searched_unit text, property_unit text) RETURNS boolean
LANGUAGE SQL IMMUTABLE AS
$$

  SELECT CASE
  WHEN $1 IN ('Kt', 'Beaufort', 'm/s')
    AND  $2  IN ('Kt', 'Beaufort', 'm/s')
    THEN TRUE
  WHEN $1 IN ( 'g', 'hg', 'kg', 'ton', 'dg', 'cg', 'mg', 'lb', 'lbs', 'pound' , 'ounce' , 'grain')
    AND  $2  IN ( 'g', 'hg', 'kg', 'ton', 'dg', 'cg', 'mg', 'lb', 'lbs', 'pound' , 'ounce' , 'grain')
    THEN TRUE

  WHEN $1 IN ('m³', 'l', 'cm³', 'ml', 'mm³' ,'µl' , 'µm³' , 'km³', 'Ml' , 'hl')
    AND  $2  IN ( 'g', 'hg', 'kg', 'ton', 'dg', 'cg', 'mg', 'lb', 'lbs', 'pound' , 'ounce' , 'grain')
    THEN TRUE

  WHEN $1 IN ('K', '°C', '°F', '°Ra', '°Re', '°r', '°N', '°Rø', '°De')
    AND  $2  IN ('K', '°C', '°F', '°Ra', '°Re', '°r', '°N', '°Rø', '°De')
    THEN TRUE

  WHEN $1 IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms')
    AND  $2  IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms')
    THEN TRUE
  ELSE FALSE END;
$$;



/*
** convert_to_unified
* convert the unit to the unified form
*/
CREATE OR REPLACE FUNCTION convert_to_unified (IN property varchar, IN property_unit varchar, IN property_type varchar) RETURNS float
language plpgsql IMMUTABLE
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
      RETURN null;
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

    IF property_type IN ('length') AND property_unit IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms') THEN
        RETURN fct_cpy_length_conversion(r_val, property_unit)::text;
    END IF;

    RETURN  property;
END;
$$;

/*
** fct_cpy_unified_values
** Used as a trigger in properties table to transform values into unified common value
** Case by case function
*/
CREATE OR REPLACE FUNCTION fct_cpy_unified_values () RETURNS TRIGGER
language plpgsql
AS

$$
DECLARE
  property_line properties%ROWTYPE;
BEGIN
  NEW.lower_value_unified = convert_to_unified(NEW.lower_value, NEW.property_unit, NEW.property_type);
  NEW.upper_value_unified = convert_to_unified(CASE WHEN NEW.upper_value = '' THEN NEW.lower_value ELSE NEW.upper_value END, NEW.property_unit, NEW.property_type);
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
* Only for postgresql < 9.1
*/
CREATE OR REPLACE FUNCTION concat(VARIADIC text[]) RETURNS text AS $$
    SELECT array_to_string($1,'');
$$ LANGUAGE SQL;

/*
** Function used for encrypting passwords using pgcrypto function
*/

CREATE OR REPLACE FUNCTION sha1(bytea) RETURNS varchar LANGUAGE plpgsql AS
$$
BEGIN
        RETURN ENCODE(DIGEST($1, 'sha1'), 'hex');
END;
$$;

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


CREATE OR REPLACE FUNCTION lineToTagRows(IN line text) RETURNS SETOF varchar AS
$$
SELECT distinct(fulltoIndex(tags)) FROM regexp_split_to_table($1, ';') as tags WHERE fulltoIndex(tags) != '' ;
$$
LANGUAGE 'sql' IMMUTABLE STRICT;

CREATE OR REPLACE FUNCTION lineToTagRowsFormatConserved(IN line text) RETURNS SETOF varchar AS
$$
SELECT distinct on (fulltoIndex(tags)) tags FROM regexp_split_to_table($1, ';') as tags WHERE fulltoIndex(tags) != '' ;
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
                        FROM regexp_split_to_table(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value, ';') as tags
                        WHERE fulltoIndex(tags) != '';
    LOOP
      FETCH curs_entry INTO entry_row;
      EXIT WHEN NOT FOUND;

      seen_el := array_append(seen_el, entry_row.u_tag);

     IF EXISTS( SELECT 1 FROM tags
                WHERE gtu_ref = NEW.gtu_ref
                  AND group_ref = NEW.id
                  AND tag_indexed = entry_row.u_tag) THEN
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
                                    FROM (SELECT case when international_name != '' THEN international_name || ';' ELSE '' END || tag_value AS tag_agg
                                          FROM tag_groups
                                          WHERE id <> NEW.id
                                            AND gtu_ref = NEW.gtu_ref
                                          UNION
                                          SELECT case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value
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


CREATE OR REPLACE FUNCTION fct_update_specimens_flat_related() returns TRIGGER
language plpgsql
AS
$$
DECLARE
  indCount INTEGER := 0;
  indType BOOLEAN := false;
  tmp_user text;
BEGIN
 SELECT COALESCE(get_setting('darwin.userid'),'0') INTO tmp_user;
  PERFORM set_config('darwin.userid', '-1', false) ;

  IF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'expeditions' THEN
    IF NEW.name_indexed IS DISTINCT FROM OLD.name_indexed THEN
      UPDATE specimens
      SET (expedition_name, expedition_name_indexed) =
          (NEW.name, NEW.name_indexed)
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
    SET (gtu_code, gtu_from_date, gtu_from_date_mask,
         gtu_to_date, gtu_to_date_mask,
         gtu_elevation, gtu_elevation_accuracy,
         gtu_tag_values_indexed, gtu_location
        ) =
        (NEW.code, NEW.gtu_from_date, NEW.gtu_from_date_mask,
         NEW.gtu_to_date, NEW.gtu_to_date_mask,
         NEW.elevation, NEW.elevation_accuracy,
         NEW.tag_values_indexed, NEW.location
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
    SET (taxon_name, taxon_name_indexed,
         taxon_level_ref, taxon_level_name,
         taxon_status, taxon_path, taxon_parent_ref, taxon_extinct
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status, NEW.path, NEW.parent_ref, NEW.extinct
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE taxon_ref = NEW.id;

  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'chronostratigraphy' THEN
    UPDATE specimens
    SET (chrono_name, chrono_name_indexed,
         chrono_level_ref, chrono_level_name,
         chrono_status,
         chrono_local, chrono_color,
         chrono_path, chrono_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
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
    SET (litho_name, litho_name_indexed,
         litho_level_ref, litho_level_name,
         litho_status,
         litho_local, litho_color,
         litho_path, litho_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
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
    SET (lithology_name, lithology_name_indexed,
         lithology_level_ref, lithology_level_name,
         lithology_status,
         lithology_local, lithology_color,
         lithology_path, lithology_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
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
    SET (mineral_name, mineral_name_indexed,
         mineral_level_ref, mineral_level_name,
         mineral_status,
         mineral_local, mineral_color,
         mineral_path, mineral_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
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
        SET gtu_country_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_country_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_province_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.sub_group_name_indexed NOT IN ('country','province') THEN
      /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
    ELSIF TG_OP = 'UPDATE' THEN
      IF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'country' AND NEW.sub_group_name_indexed != 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NULL,
            gtu_country_tag_indexed = NULL
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'province' AND NEW.sub_group_name_indexed != 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = NULL,
            gtu_province_tag_indexed = NULL
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
      IF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_country_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END ||NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_province_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
      IF NEW.sub_group_name_indexed NOT IN ('country','province') THEN
      /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
    ELSIF TG_OP = 'DELETE' THEN
      IF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NULL,
            gtu_country_tag_indexed = NULL
        WHERE gtu_ref = OLD.gtu_ref;
      ELSIF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = NULL,
            gtu_province_tag_indexed = NULL
        WHERE gtu_ref = OLD.gtu_ref;
      ELSE
        /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = OLD.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = OLD.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = OLD.gtu_ref;
      END IF;
    END IF;
  END IF;
  PERFORM set_config('darwin.userid', tmp_user, false) ;
  RETURN NEW;
END;
$$;



CREATE OR REPLACE FUNCTION fct_update_specimen_flat() RETURNS TRIGGER
AS $$
DECLARE
  cnt integer;
  old_val specimens%ROWTYPE;
  new_val specimens%ROWTYPE;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      old_val = OLD;
      new_val = NEW;
    ELSE --INSERT
      new_val = NEW;
    END IF;

    IF old_val.taxon_ref IS DISTINCT FROM new_val.taxon_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, path, parent_ref, extinct
        INTO NEW.taxon_name, NEW.taxon_name_indexed, NEW.taxon_level_ref, NEW.taxon_level_name, NEW.taxon_status,
          NEW.taxon_path, NEW.taxon_parent_ref, NEW.taxon_extinct
        FROM taxonomy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.taxon_ref;
    END IF;

    IF old_val.chrono_ref IS DISTINCT FROM new_val.chrono_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
      INTO NEW.chrono_name, NEW.chrono_name_indexed, NEW.chrono_level_ref, NEW.chrono_level_name, NEW.chrono_status,
          NEW.chrono_local, NEW.chrono_color, NEW.chrono_path, NEW.chrono_parent_ref
        FROM chronostratigraphy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.chrono_ref;
    END IF;

    IF old_val.litho_ref IS DISTINCT FROM new_val.litho_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.litho_name, NEW.litho_name_indexed, NEW.litho_level_ref, NEW.litho_level_name, NEW.litho_status,
          NEW.litho_local, NEW.litho_color, NEW.litho_path, NEW.litho_parent_ref
        FROM lithostratigraphy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.litho_ref;
    END IF;

    IF old_val.lithology_ref IS DISTINCT FROM new_val.lithology_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.lithology_name, NEW.lithology_name_indexed, NEW.lithology_level_ref, NEW.lithology_level_name, NEW.lithology_status,
          NEW.lithology_local, NEW.lithology_color, NEW.lithology_path, NEW.lithology_parent_ref
        FROM lithology c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.lithology_ref;
    END IF;

    IF old_val.mineral_ref IS DISTINCT FROM new_val.mineral_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.mineral_name, NEW.mineral_name_indexed, NEW.mineral_level_ref, NEW.mineral_level_name, NEW.mineral_status,
          NEW.mineral_local, NEW.mineral_color, NEW.mineral_path, NEW.mineral_parent_ref
        FROM mineralogy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.mineral_ref;
    END IF;


    IF old_val.expedition_ref IS DISTINCT FROM new_val.expedition_ref THEN
      SELECT  name, name_indexed
        INTO NEW.expedition_name, NEW.expedition_name_indexed
        FROM expeditions c
        WHERE c.id = new_val.expedition_ref;
    END IF;

    IF old_val.collection_ref IS DISTINCT FROM new_val.collection_ref THEN
      SELECT collection_type, code, name, is_public, parent_ref, path
        INTO NEW.collection_type, NEW.collection_code, NEW.collection_name, NEW.collection_is_public,
          NEW.collection_parent_ref, NEW.collection_path
        FROM collections c
        WHERE c.id = new_val.collection_ref;
    END IF;

    IF old_val.ig_ref IS DISTINCT FROM new_val.ig_ref THEN
      SELECT  ig_num, ig_num_indexed, ig_date, ig_date_mask
        INTO NEW.ig_num, NEW.ig_num_indexed, NEW.ig_date, NEW.ig_date_mask
        FROM igs c
        WHERE c.id = new_val.ig_ref;
    END IF;

    IF old_val.gtu_ref IS DISTINCT FROM new_val.gtu_ref THEN
      SELECT  code, gtu_from_date, gtu_from_date_mask,
         gtu_to_date, gtu_to_date_mask,
         elevation, elevation_accuracy,
         tag_values_indexed, location,

         taggr_countries.tag_value, lineToTagArray(taggr_countries.tag_value),
         taggr_provinces.tag_value, lineToTagArray(taggr_provinces.tag_value),
         (select array_to_string(array(select tag from tags where gtu_ref = c.id and sub_group_type not in ('country', 'province')), ';')) as other_gtu_values,
         (select array(select distinct fullToIndex(tag) from tags where gtu_ref = c.id and sub_group_type not in ('country', 'province'))) as other_gtu_values_array

        INTO NEW.gtu_code, NEW.gtu_from_date, NEW.gtu_from_date_mask, NEW.gtu_to_date, NEW.gtu_to_date_mask,
         NEW.gtu_elevation, NEW.gtu_elevation_accuracy, NEW.gtu_tag_values_indexed, NEW.gtu_location,
         NEW.gtu_country_tag_value, NEW.gtu_country_tag_indexed, NEW.gtu_province_tag_value,
         NEW.gtu_province_tag_indexed, NEW.gtu_others_tag_value, NEW.gtu_others_tag_indexed
        FROM gtu c
          LEFT JOIN tag_groups taggr_countries ON c.id = taggr_countries.gtu_ref AND taggr_countries.group_name_indexed = 'administrativearea' AND taggr_countries.sub_group_name_indexed = 'country'
          LEFT JOIN tag_groups taggr_provinces ON c.id = taggr_provinces.gtu_ref AND taggr_provinces.group_name_indexed = 'administrativearea' AND taggr_provinces.sub_group_name_indexed = 'province'
        WHERE c.id = new_val.gtu_ref;
    END IF;

  RETURN NEW;
END;
$$
language plpgsql;

CREATE OR REPLACE FUNCTION convert_to_integer(v_input varchar) RETURNS INTEGER IMMUTABLE
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

CREATE OR REPLACE FUNCTION convert_to_real(v_input varchar) RETURNS REAL IMMUTABLE
AS $$
DECLARE v_int_value REAL DEFAULT 0;
BEGIN
    BEGIN
        v_int_value := v_input::REAL;
    EXCEPTION WHEN OTHERS THEN
/*        RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', v_input;*/
        RETURN 0;
    END;
RETURN v_int_value;
END;
$$ LANGUAGE plpgsql;

/** Deprecated ... might be removed **/
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
        sqlWhere := sqlWhere || E' full_code_indexed like \'%\' || fullToIndex(' || quote_literal(word) || E') || \'%\' OR';
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

  IF user_id = -1 THEN
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
  END IF;
  IF TG_OP = 'DELETE' THEN
    RETURN OLD;
  END IF;
  RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION fct_cpy_location() RETURNS trigger
language plpgSQL
as $$
BEGIN
  NEW.location := POINT(NEW.latitude, NEW.longitude);
  RETURN NEW;
END;
$$;


CREATE OR REPLACE FUNCTION fct_filter_encodable_row(ids varchar, col_name varchar, user_id integer) RETURNS SETOF integer
AS $$
DECLARE
  rec_id integer;
BEGIN
    IF col_name = 'spec_ref' THEN
      FOR rec_id IN SELECT id FROM specimens WHERE id in (select X::int from regexp_split_to_table(ids, ',' ) as X)
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
                                                                     AND pref_key = 'search_cols_specimen'
                                                                   LIMIT 1
                                                                  ), E'\\|') as fields_list
                                       where fields_list not in ('institution_ref', 'building', 'floor', 'room', 'row', 'shelf', 'col', 'container', 'container_type', 'container_storage', 'sub_container', 'sub_container_type', 'sub_container_storage')
                                      ),'|'
                                ) as fields_available
         ) subq
    WHERE user_ref = NEW.id
      AND pref_key = 'search_cols_specimen';
    FOR saved_search_row IN SELECT id, visible_fields_in_result FROM my_saved_searches WHERE user_ref = NEW.id LOOP
      UPDATE my_saved_searches
      SET visible_fields_in_result = subq.fields_available
      FROM (select array_to_string(array(select fields_list
                                         from regexp_split_to_table(saved_search_row.visible_fields_in_result, E'\\|') as fields_list
                                         where fields_list not in ('institution_ref','building', 'floor', 'room', 'row', 'shelf', 'col', 'container', 'container_type', 'container_storage', 'sub_container', 'sub_container_type', 'sub_container_storage')
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
create or replace function getSpecificParentForLevel(referenced_relation IN catalogue_levels.level_type%TYPE, path IN template_classifications.path%TYPE, level_searched IN catalogue_levels.level_name%TYPE) RETURNS template_classifications.name%TYPE LANGUAGE plpgsql IMMUTABLE AS
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

CREATE OR REPLACE FUNCTION fct_find_tax_level(tax_path text, searched_level integer) RETURNS int as
$$
   SELECT id FROM taxonomy where  level_ref = $2 and id in (select i::int from regexp_split_to_table($1, E'\/') as i where i != '');
$$
LANGUAGE sql stable;

CREATE OR REPLACE FUNCTION fct_add_in_dict(ref_relation text, ref_field text, old_value text, new_val text) RETURNS boolean
AS
$$
DECLARE
  query_str varchar;
BEGIN
  IF new_val is NULL OR old_value IS NOT DISTINCT FROM new_val THEN
    RETURN TRUE;
  END IF;
    query_str := ' INSERT INTO flat_dict (referenced_relation, dict_field, dict_value)
    (
      SELECT ' || quote_literal(ref_relation) || ' , ' || quote_literal(ref_field) || ', ' || quote_literal(new_val) || ' WHERE NOT EXISTS
      (SELECT id FROM flat_dict WHERE
        referenced_relation = ' || quote_literal(ref_relation) || '
        AND dict_field = ' || quote_literal(ref_field) || '
        AND dict_value = ' || quote_literal(new_val) || ')
    );';
    execute query_str;
    RETURN true;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_add_in_dict_dept(ref_relation text, ref_field text, old_value text, new_val text,
     depending_old_value text, depending_new_value text) RETURNS boolean
AS
$$
DECLARE
  query_str varchar;
  dpt_new_val varchar;
BEGIN
  IF new_val is NULL OR ( old_value IS NOT DISTINCT FROM new_val AND depending_old_value IS NOT DISTINCT FROM depending_new_value ) THEN
    RETURN TRUE;
  END IF;
  dpt_new_val := coalesce(depending_new_value,'');

    query_str := ' INSERT INTO flat_dict (referenced_relation, dict_field, dict_value, dict_depend)
    (
      SELECT ' || quote_literal(ref_relation) || ' , ' || quote_literal(ref_field) || ', ' || quote_literal(new_val) || ', '
        || quote_literal(dpt_new_val) || ' WHERE NOT EXISTS
      (SELECT id FROM flat_dict WHERE
        referenced_relation = ' || quote_literal(ref_relation) || '
        AND dict_field = ' || quote_literal(ref_field) || '
        AND dict_value = ' || quote_literal(new_val) || '
        AND dict_depend = ' || quote_literal(dpt_new_val) || '
      )
    );';
    --RAISE info 'hem %' ,  dpt_new_val;
    execute query_str;
    RETURN true;
END;
$$
LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION fct_del_in_dict(ref_relation text, ref_field text, old_value text, new_val text) RETURNS boolean
AS $$
DECLARE
  result boolean;
  query_str text;
BEGIN
  IF old_value IS null OR old_value IS NOT DISTINCT FROM new_val THEN
    RETURN TRUE;
  END IF;
  query_str := ' SELECT EXISTS( SELECT 1 from ' || quote_ident(ref_relation) || ' where ' || quote_ident(ref_field) || ' = ' || quote_literal(old_value) || ');';
  execute query_str into result;

  IF result = false THEN
    DELETE FROM flat_dict where
          referenced_relation = ref_relation
          AND dict_field = ref_field
          AND dict_value = old_value;
  END IF;
  RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_del_in_dict_dept(ref_relation text, ref_field text, old_value text, new_val text,
     depending_old_value text,  depending_new_value text, depending_field text) RETURNS boolean
AS $$
DECLARE
  result boolean;
  query_str text;
BEGIN
  IF old_value is NULL OR ( old_value IS NOT DISTINCT FROM new_val AND depending_old_value IS NOT DISTINCT FROM depending_new_value ) THEN
    RETURN TRUE;
  END IF;
  query_str := ' SELECT EXISTS( SELECT id from ' || quote_ident(ref_relation) || ' where ' || quote_ident(ref_field) || ' = ' || quote_literal(old_value)
  || ' AND ' || quote_ident(depending_field) || ' = ' || quote_literal(depending_old_value) || ' );';
  execute query_str into result;

  IF result = false THEN
    DELETE FROM flat_dict where
          referenced_relation = ref_relation
          AND dict_field = ref_field
          AND dict_value = old_value
          AND dict_depend = depending_old_value;
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
    ELSIF TG_TABLE_NAME = 'specimens' THEN
      PERFORM fct_del_in_dict('specimens','type', oldfield.type, newfield.type);
      PERFORM fct_del_in_dict('specimens','type_group', oldfield.type_group, newfield.type_group);
      PERFORM fct_del_in_dict('specimens','type_search', oldfield.type_search, newfield.type_search);
      PERFORM fct_del_in_dict('specimens','sex', oldfield.sex, newfield.sex);
      PERFORM fct_del_in_dict('specimens','state', oldfield.state, newfield.state);
      PERFORM fct_del_in_dict('specimens','stage', oldfield.stage, newfield.stage);
      PERFORM fct_del_in_dict('specimens','social_status', oldfield.social_status, newfield.social_status);
      PERFORM fct_del_in_dict('specimens','rock_form', oldfield.rock_form, newfield.rock_form);

      PERFORM fct_del_in_dict('specimens','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_del_in_dict('specimens','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_del_in_dict('specimens','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_del_in_dict('specimens','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_del_in_dict('specimens','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_del_in_dict('specimens','col', oldfield.col, newfield.col);
      PERFORM fct_del_in_dict('specimens','row', oldfield.row, newfield.row);
      PERFORM fct_del_in_dict('specimens','room', oldfield.room, newfield.room);
      PERFORM fct_del_in_dict('specimens','floor', oldfield.floor, newfield.floor);
      PERFORM fct_del_in_dict('specimens','building', oldfield.building, newfield.building);

      PERFORM fct_del_in_dict_dept('specimens','container_storage', oldfield.container_storage, newfield.container_storage,
        oldfield.container_type, newfield.container_type, 'container_type' );
      PERFORM fct_del_in_dict_dept('specimens','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
        oldfield.sub_container_type, newfield.sub_container_type, 'sub_container_type' );

    ELSIF TG_TABLE_NAME = 'specimens_relationships' THEN
      PERFORM fct_del_in_dict('specimens_relationships','relationship_type', oldfield.relationship_type, newfield.relationship_type);
    ELSIF TG_TABLE_NAME = 'users' THEN
      PERFORM fct_del_in_dict('users','title', oldfield.title, newfield.title);
      PERFORM fct_del_in_dict('users','sub_type', oldfield.sub_type, newfield.sub_type);
    ELSIF TG_TABLE_NAME = 'users_addresses' THEN
      PERFORM fct_del_in_dict('users_addresses','country', oldfield.country, newfield.country);

    ELSIF TG_TABLE_NAME = 'loan_status' THEN
      PERFORM fct_del_in_dict('loan_status','status', oldfield.status, newfield.status);

    ELSIF TG_TABLE_NAME = 'properties' THEN

      PERFORM fct_del_in_dict_dept('properties','property_type', oldfield.property_type, newfield.property_type,
        oldfield.referenced_relation, newfield.referenced_relation, 'referenced_relation' );
      PERFORM fct_del_in_dict_dept('properties','applies_to', oldfield.applies_to, newfield.applies_to,
        oldfield.property_type, newfield.property_type, 'property_type' );
      PERFORM fct_del_in_dict_dept('properties','property_unit', oldfield.property_unit, newfield.property_unit,
        oldfield.property_type, newfield.property_type, 'property_type' );

    ELSIF TG_TABLE_NAME = 'tag_groups' THEN
      PERFORM fct_del_in_dict_dept('tag_groups','sub_group_name', oldfield.sub_group_name, newfield.sub_group_name,
        oldfield.group_name, newfield.group_name, 'group_name' );
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
    ELSIF TG_TABLE_NAME = 'specimens' THEN
      PERFORM fct_add_in_dict('specimens','type', oldfield.type, newfield.type);
      PERFORM fct_add_in_dict('specimens','type_group', oldfield.type_group, newfield.type_group);
      PERFORM fct_add_in_dict('specimens','type_search', oldfield.type_search, newfield.type_search);
      PERFORM fct_add_in_dict('specimens','sex', oldfield.sex, newfield.sex);
      PERFORM fct_add_in_dict('specimens','state', oldfield.state, newfield.state);
      PERFORM fct_add_in_dict('specimens','stage', oldfield.stage, newfield.stage);
      PERFORM fct_add_in_dict('specimens','social_status', oldfield.social_status, newfield.social_status);
      PERFORM fct_add_in_dict('specimens','rock_form', oldfield.rock_form, newfield.rock_form);

      PERFORM fct_add_in_dict('specimens','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict('specimens','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_add_in_dict('specimens','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_add_in_dict('specimens','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_add_in_dict('specimens','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_add_in_dict('specimens','col', oldfield.col, newfield.col);
      PERFORM fct_add_in_dict('specimens','row', oldfield.row, newfield.row);
      PERFORM fct_add_in_dict('specimens','room', oldfield.room, newfield.room);
      PERFORM fct_add_in_dict('specimens','floor', oldfield.floor, newfield.floor);
      PERFORM fct_add_in_dict('specimens','building', oldfield.building, newfield.building);

      PERFORM fct_add_in_dict_dept('specimens','container_storage', oldfield.container_storage, newfield.container_storage,
        oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict_dept('specimens','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
        oldfield.sub_container_type, newfield.sub_container_type);

    ELSIF TG_TABLE_NAME = 'specimens_relationships' THEN
      PERFORM fct_add_in_dict('specimens_relationships','relationship_type', oldfield.relationship_type, newfield.relationship_type);
    ELSIF TG_TABLE_NAME = 'users' THEN
      PERFORM fct_add_in_dict('users','title', oldfield.title, newfield.title);
      PERFORM fct_add_in_dict('users','sub_type', oldfield.sub_type, newfield.sub_type);
    ELSIF TG_TABLE_NAME = 'users_addresses' THEN
      PERFORM fct_add_in_dict('users_addresses','country', oldfield.country, newfield.country);

    ELSIF TG_TABLE_NAME = 'loan_status' THEN
      PERFORM fct_add_in_dict('loan_status','status', oldfield.status, newfield.status);

    ELSIF TG_TABLE_NAME = 'properties' THEN

      PERFORM fct_add_in_dict_dept('properties','property_type', oldfield.property_type, newfield.property_type,
        oldfield.referenced_relation, newfield.referenced_relation);
      PERFORM fct_add_in_dict_dept('properties','applies_to', oldfield.applies_to, newfield.applies_to,
        oldfield.property_type, newfield.property_type);
      PERFORM fct_add_in_dict_dept('properties','property_unit', oldfield.property_unit, newfield.property_unit,
        oldfield.property_type, newfield.property_type);

    ELSIF TG_TABLE_NAME = 'tag_groups' THEN
      PERFORM fct_add_in_dict_dept('tag_groups','sub_group_name', oldfield.sub_group_name, newfield.sub_group_name,
        oldfield.group_name, newfield.group_name);

    END IF;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_upd_people_in_flat() RETURNS TRIGGER
AS
$$
DECLARE
  spec_row RECORD;
  ident RECORD;
  tmp_user text;
BEGIN
 SELECT COALESCE(get_setting('darwin.userid'),'0') INTO tmp_user;
  PERFORM set_config('darwin.userid', '-1', false) ;


  IF TG_OP = 'DELETE' THEN
    IF OLD.people_type = 'collector' THEN
      UPDATE specimens s SET spec_coll_ids = fct_remove_array_elem(spec_coll_ids,ARRAY[OLD.people_ref])
        WHERE id  = OLD.record_id;
    ELSIF OLD.people_type = 'donator' THEN
      UPDATE specimens s SET spec_don_sel_ids = fct_remove_array_elem(spec_don_sel_ids,ARRAY[OLD.people_ref])
        WHERE id  = OLD.record_id;
    ELSIF OLD.people_type = 'identifier' THEN
      SELECT * into ident FROM identifications where id = OLD.record_id;
      IF NOT FOUND Then
        PERFORM set_config('darwin.userid', tmp_user, false) ;
        RETURN OLD;
      END IF;

      UPDATE specimens s SET spec_ident_ids = fct_remove_array_elem(spec_ident_ids,ARRAY[OLD.people_ref])
        WHERE id  = ident.record_id
            AND NOT exists (
              SELECT true FROM catalogue_people cp INNER JOIN identifications i ON cp.record_id = i.id AND cp.referenced_relation = 'identifications'
                WHERE i.record_id = ident.id AND people_ref = OLD.people_ref AND i.referenced_relation = 'specimens'
            );
    END IF;

  ELSIF TG_OP = 'INSERT' THEN --- INSERT

    IF NEW.people_type = 'collector' THEN
      UPDATE specimens s SET spec_coll_ids = array_append(spec_coll_ids,NEW.people_ref)
        WHERE id  = NEW.record_id and NOT (spec_coll_ids && ARRAY[ NEW.people_ref::integer ]);
    ELSIF NEW.people_type = 'donator' THEN
      UPDATE specimens s SET spec_don_sel_ids = array_append(spec_don_sel_ids,NEW.people_ref)
        WHERE id  = NEW.record_id  and NOT (spec_don_sel_ids && ARRAY[ NEW.people_ref::integer ]);
    ELSIF NEW.people_type = 'identifier' THEN
      SELECT * into ident FROM identifications where id = NEW.record_id;

      UPDATE specimens s SET spec_ident_ids = array_append(spec_ident_ids,NEW.people_ref)
          WHERE id  = ident.record_id and NOT (spec_ident_ids && ARRAY[ NEW.people_ref::integer ]);
    END IF;

  ELSIF OLD.people_ref != NEW.people_ref THEN --UPDATE

    IF NEW.people_type = 'collector' THEN
      UPDATE specimens s SET spec_coll_ids = array_append(fct_remove_array_elem(spec_coll_ids ,ARRAY[OLD.people_ref]),NEW.people_ref::integer)
        WHERE id  = NEW.record_id;
    ELSIF NEW.people_type = 'donator' THEN
      UPDATE specimens s SET spec_don_sel_ids = array_append(fct_remove_array_elem(spec_don_sel_ids ,ARRAY[OLD.people_ref]),NEW.people_ref::integer)
        WHERE id  = NEW.record_id;

    ELSIF NEW.people_type = 'identifier' THEN
      SELECT * into ident FROM identifications where id = NEW.record_id;

        SELECT id, spec_ident_ids INTO spec_row FROM specimens WHERE id = ident.record_id;

        IF NOT exists (SELECT 1 from identifications i INNER JOIN catalogue_people c ON c.record_id = i.id AND c.referenced_relation = 'identifications'
          WHERE i.record_id = spec_row.id AND people_ref = OLD.people_ref AND i.referenced_relation = 'specimens' AND c.id != OLD.id
        ) THEN
          spec_row.spec_ident_ids := fct_remove_array_elem(spec_row.spec_ident_ids ,ARRAY[OLD.people_ref]);
        END IF;

        IF NOT spec_row.spec_ident_ids && ARRAY[ NEW.people_ref::integer ] THEN
          spec_row.spec_ident_ids := array_append(spec_row.spec_ident_ids ,NEW.people_ref);
        END IF;

        UPDATE specimens SET spec_ident_ids = spec_row.spec_ident_ids WHERE id = spec_row.id;
    END IF;
    --else  raise info 'ooh';
  END IF;

  PERFORM set_config('darwin.userid', tmp_user, false) ;
  RETURN NEW;
END;
$$ language plpgsql;

CREATE OR REPLACE FUNCTION fct_clear_identifiers_in_flat() RETURNS TRIGGER
AS
$$
DECLARE
  tmp_user text;
BEGIN
 SELECT COALESCE(get_setting('darwin.userid'),'0') INTO tmp_user;
  PERFORM set_config('darwin.userid', '-1', false) ;

  IF EXISTS(SELECT true FROM catalogue_people cp WHERE cp.record_id = OLD.id AND cp.referenced_relation = 'identifications') THEN
    -- There's NO identifier associated to this identification'
    UPDATE specimens SET spec_ident_ids = fct_remove_array_elem(spec_ident_ids,
      (
        select array_agg(people_ref) FROM catalogue_people p  INNER JOIN identifications i ON p.record_id = i.id AND i.id = OLD.id
        AND people_ref NOT in
          (
            SELECT people_ref from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications'
            AND p.people_type='identifier' where i.record_id=OLD.record_id AND i.referenced_relation=OLD.referenced_relation AND i.id != OLD.id
          )
      ))
      WHERE id = OLD.record_id;
  END IF;

  PERFORM set_config('darwin.userid', tmp_user, false) ;
  RETURN OLD;

END;
$$ language plpgsql;

CREATE OR REPLACE FUNCTION get_import_row() RETURNS integer AS $$

UPDATE imports SET state = 'aloaded' FROM (
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
  IF catalogue_table = 'mineralogy' THEN
    /*
     * @ToDo: We'll need to evaluate if we keep the fuzzyness here or if we apply the same as it's for the other catalogues (=)
     */
    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t
    INNER JOIN catalogue_levels c on t.level_ref = c.id
    WHERE name_indexed like fullToIndex(' || quote_literal( field_name) || ') || ''%'' AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;
  ELSE
    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t
    INNER JOIN catalogue_levels c on t.level_ref = c.id
    WHERE name_indexed = fullToIndex(' || quote_literal( field_name) || ') AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;
  END IF ;

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
    PERFORM fct_imp_create_catalogues_and_parents(line, 'taxonomy','taxon');
    PERFORM fct_imp_checker_catalogue(line,'taxonomy','taxon');
  END IF;

  IF line.chrono_name IS NOT NULL AND line.chrono_name is distinct from '' AND line.chrono_ref is null THEN
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
          name_indexed like fullToIndex( ' || quote_literal(lvl_value) || '  ) || ''%''
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


CREATE OR REPLACE FUNCTION fct_imp_create_catalogues_and_parents(line staging, catalogue_table text, prefix text) RETURNS boolean
AS $$
DECLARE
  result_nbr integer :=0;
  row_record record;
  lvl_name varchar;
  lvl_value varchar;
  lvl_id integer;

  old_parent_id integer;
  parent_id integer;
  rec_parents hstore;
  line_store hstore;
  field_name1 text;
  field_name2 text;

  tmp text;
BEGIN
  line_store := hstore(line);
  field_name1 := prefix || '_parents';
  rec_parents := line_store->field_name1;

  IF line.create_taxon AND rec_parents is not null AND rec_parents != ''::hstore  THEN
    BEGIN
      field_name2 := prefix || '_name';
      field_name1 := prefix || '_level_name';

      IF line_store->field_name2 != '' THEN
        rec_parents = rec_parents || hstore(line_store->field_name1, line_store->field_name2);
      END IF;

      FOR row_record in SELECT s.key as lvl_name, s.value as lvl_value, l.id as lvl_id
        FROM each(rec_parents) as s LEFT JOIN catalogue_levels l on s.key = l.level_sys_name
        ORDER BY l.level_order ASC
      LOOP
        old_parent_id := parent_id;
        EXECUTE 'SELECT count(*), min(t.id) as id from ' || quote_ident(catalogue_table) || ' t
          INNER JOIN catalogue_levels c on t.level_ref = c.id
          WHERE level_sys_name = ' || quote_literal(row_record.lvl_name) || ' AND
            name_indexed like fullToIndex( ' || quote_literal(row_record.lvl_value) || '  ) || ''%'' '
          INTO result_nbr, parent_id;

        IF result_nbr = 0 THEN
          IF old_parent_id IS NULL THEN
            RAISE EXCEPTION 'Unable to create taxon with no common parents';
          END IF;
          EXECUTE 'INSERT INTO ' || quote_ident(catalogue_table) || '  (name, level_ref, parent_ref) VALUES
            (' || quote_literal(row_record.lvl_value) || ', ' ||
            quote_literal(row_record.lvl_id) ||', '|| quote_literal(old_parent_id) ||') returning ID' into parent_id ;

          -- We are at the last level
          IF lvl_name = line_store->field_name1 THEN
            PERFORM fct_imp_checker_staging_info(line, 'taxonomy');
          END IF;
        END IF;
      END LOOP;

    EXCEPTION WHEN OTHERS THEN
      UPDATE staging set create_taxon = false where id = line.id;
      RETURN TRUE;
    END;
  END IF;
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

  select id into ref_rec from igs where ig_num = line.ig_num ;
  IF NOT FOUND THEN
    IF import THEN
        INSERT INTO igs (ig_num, ig_date_mask, ig_date)
        VALUES (line.ig_num,  COALESCE(line.ig_date_mask,line.ig_date_mask,'0'), COALESCE(line.ig_date,'01/01/0001'))
        RETURNING id INTO line.ig_ref;

        ref_rec := line.ig_ref;
        PERFORM fct_imp_checker_staging_info(line, 'igs');
    ELSE
    --UPDATE staging SET status = (status || ('igs' => 'not_found')), ig_ref = null where id=line.id;
      RETURN TRUE;
    END IF;
  END IF;

  UPDATE staging SET status = delete(status,'igs'), ig_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_imp_checker_expeditions(line staging, import boolean default false) RETURNS boolean
AS $$
DECLARE
  result_nbr integer :=0;
  ref_record RECORD;
  ref_rec integer :=0;
  ref refcursor;
BEGIN
  IF line.expedition_name is null OR line.expedition_name ='' OR line.expedition_ref is not null THEN
    RETURN true;
  END IF;
  OPEN ref FOR select * from expeditions where name_indexed = fulltoindex(line.expedition_name) ;
  LOOP
    FETCH ref INTO ref_record ;
    IF NOT FOUND THEN
      EXIT ;
    END IF;
    ref_rec = ref_record.id ;
    result_nbr := result_nbr +1;
  END LOOP ;
  IF result_nbr = 0 THEN
    IF import THEN
      INSERT INTO expeditions (name, expedition_from_date, expedition_to_date, expedition_from_date_mask,expedition_to_date_mask)
      VALUES (
        line.expedition_name, COALESCE(line.expedition_from_date,'01/01/0001'),
        COALESCE(line.expedition_to_date,'31/12/2038'), COALESCE(line.expedition_from_date_mask,0),
        COALESCE(line.expedition_to_date_mask,0)
      )
      RETURNING id INTO line.expedition_ref;

      ref_rec := line.expedition_ref;
      PERFORM fct_imp_checker_staging_info(line, 'expeditions');
    ELSE
      RETURN TRUE;
    END IF;
  END IF;
  IF result_nbr >= 2 THEN
    UPDATE staging SET status = (status || ('expedition' => 'too_much')) where id= line.id;
    RETURN true;
  END IF;
/* So result_nbr = 1 ! */
  UPDATE staging SET status = delete(status,'expedition'), expedition_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_gtu(line staging, import boolean default false)  RETURNS boolean
AS $$
DECLARE
  ref_rec integer :=0;
  tags staging_tag_groups ;
  tags_tag RECORD;
  update_count integer;
  tag_groups_line RECORD;
BEGIN
  IF import THEN
    /* If gtu_ref already defined, that means that check was already
       made for the line and there's no need to reassociate it
    */
    IF line.gtu_ref is not null THEN
      RETURN true;
    END IF;
    /* If no code is given, not even from date and not even tags (tag_groups here),
       that means there's not enough information to associate a gtu
    */
    IF (line.gtu_code is null OR COALESCE(fullToIndex(line.gtu_code),'')  = '') AND (line.gtu_from_date is null) AND NOT EXISTS (select 1 from staging_tag_groups g where g.staging_ref = line.id ) THEN
      RETURN true;
    END IF;
    /* Otherwise, we should try to associate a gtu_ref */
    select substr.id into ref_rec from (
       /* This part try to select gtu id for line.gtu_code NULL or line.gtu_code = '' making the comparison on all the
          other fields ensuring uniqueness (latitude, longitude, from_date and to_date)
          The criteria position('import/' in code) > 0 filter also on the already imported gtu without code only
       */
       select id from gtu g where
         position('import/' in code) > 0 AND
         COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
         COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
         COALESCE(fullToIndex(line.gtu_code), '') = '' AND
         fct_mask_date(gtu_from_date,gtu_from_date_mask) = fct_mask_date(COALESCE(line.gtu_from_date, '01/01/0001')::timestamp,line.gtu_from_date_mask) AND
         fct_mask_date(gtu_to_date,gtu_to_date_mask) = fct_mask_date(COALESCE(line.gtu_to_date, '31/12/2038')::timestamp,line.gtu_to_date_mask) AND
         COALESCE(elevation,0) = COALESCE(line.gtu_elevation,0)
         /* if we're not in the case of already imported gtu without code,
            we've got to find a gtu that correspond to the criterias of the current line
         */
       union
       select id from gtu g where
         position('import/' in code) = 0 AND
         COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
         COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
         COALESCE(fullToIndex(code),'') = COALESCE(fullToIndex(line.gtu_code),'') AND
         fct_mask_date(gtu_from_date,gtu_from_date_mask) = fct_mask_date(COALESCE(line.gtu_from_date, '01/01/0001')::timestamp,line.gtu_from_date_mask) AND
         fct_mask_date(gtu_to_date,gtu_to_date_mask) = fct_mask_date(COALESCE(line.gtu_to_date, '31/12/2038')::timestamp,line.gtu_to_date_mask) AND
         COALESCE(elevation,0) = COALESCE(line.gtu_elevation,0)
       LIMIT 1
      ) as substr
    WHERE substr.id != 0 LIMIT 1;

    /* If no corresponding gtu found and we've chosen to import... insert the new gtu */
    IF NOT FOUND THEN
      INSERT into gtu
      (code,
       gtu_from_date_mask,
       gtu_from_date,
       gtu_to_date_mask,
       gtu_to_date,
       latitude,
       longitude,
       lat_long_accuracy,
       elevation,
       elevation_accuracy
      )
      VALUES
        (
          CASE COALESCE(fullToIndex(line.gtu_code),'') WHEN '' THEN 'import/'|| line.import_ref || '/' || line.id ELSE line.gtu_code END,
          COALESCE(line.gtu_from_date_mask,0),
          COALESCE(line.gtu_from_date, '01/01/0001'),
          COALESCE(line.gtu_to_date_mask,0),
          COALESCE(line.gtu_to_date, '31/12/2038'),
          line.gtu_latitude,
          line.gtu_longitude,
          line.gtu_lat_long_accuracy,
          line.gtu_elevation,
          line.gtu_elevation_accuracy
        )
      RETURNING id INTO line.gtu_ref;
      /* The new id is returned in line.gtu_ref and stored in ref_rec so it can be used further on */
      ref_rec := line.gtu_ref;
      /* Browse all tags to try importing them one by one and associate them with the newly created gtu */
      FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        BEGIN
          INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
            SELECT ref_rec,tags.group_name, tags.sub_group_name, tags.tag_value;
          EXCEPTION WHEN OTHERS THEN
            RAISE NOTICE 'Error in fct_imp_checker_gtu (case non existing gtu): %', SQLERRM;
            /* Do nothing and continue */
        END ;
      END LOOP ;
    ELSE
      /* Define gtu_ref of the line object, so it can be used afterwards in the perform to bring correctly
         the additional comments and additional properties
      */
      line.gtu_ref = ref_rec;
      /* ELSE ADDED HERE TO CHECK IF THE TAGS (and the staging infos) OF THE EXISTING GTU EXISTS TOO */
      /* This case happens when a gtu that correspond to info entered in staging has been found */
      /* Browse all tags to try importing them one by one and associate them with the newly created gtu */
      FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        /* We split all the tags entered by ; as it's the case in the interface */
        FOR tags_tag IN SELECT trim(regexp_split_to_table(tags.tag_value, E';+')) as value LOOP
          BEGIN
            /* We use an upsert here.
               Ideally, we should use locking, but we consider it's isolated.
             */
            UPDATE tag_groups
            SET tag_value = tag_value || ';' || tags.tag_value
            WHERE gtu_ref = ref_rec
                  AND group_name_indexed = fullToIndex(tags.group_name)
                  AND sub_group_name_indexed = fullToIndex(tags.sub_group_name)
                  AND fullToIndex(tags_tag.value) NOT IN (SELECT fullToIndex(regexp_split_to_table(tag_value, E';+')));
            GET DIAGNOSTICS update_count = ROW_COUNT;
            IF update_count = 0 THEN
              INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
                SELECT ref_rec,tags.group_name, tags.sub_group_name, tags_tag.value
                WHERE NOT EXISTS (SELECT id
                                  FROM tag_groups
                                  WHERE gtu_ref = ref_rec
                                        AND group_name_indexed = fullToIndex(tags.group_name)
                                        AND sub_group_name_indexed = fullToIndex(tags.sub_group_name)
                                  LIMIT 1
                );
            END IF;
            EXCEPTION WHEN OTHERS THEN
              RAISE NOTICE 'Error in fct_imp_checker_gtu (case from existing gtu): %', SQLERRM;
              RAISE NOTICE 'gtu_ref is %', ref_rec;
              RAISE NOTICE 'group name is %', tags.group_name;
              RAISE NOTICE 'subgroup name is %', tags.sub_group_name;
              RAISE NOTICE 'tag value is %', tags_tag.value;
              /* Do nothing here */
          END ;
        END LOOP;
      END LOOP ;
    END IF;
    /* Execute (perform = execute without any output) the update of reference_relation
       for the current staging line and for the gtu type of relationship.
       Referenced relation currently named 'staging_info' is replaced by gtu
       and record_id currently set to line.id (staging id) is replaced by line.gtu_ref (id of the new gtu created)
    */
    PERFORM fct_imp_checker_staging_info(line, 'gtu');

    /* Associate the gtu_ref in the staging and erase in hstore status the gtu tag signaling gtu has still to be treated */
    UPDATE staging SET status = delete(status,'gtu'), gtu_ref = ref_rec where id=line.id;

  END IF;

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
      WHERE is_physical = false  AND
      ( formated_name_indexed like fulltoindex(fullname) || '%' OR fulltoindex(additional_names) =  fulltoindex(fullname) )
      LIMIT 2
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

  -- Identifiers

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

  -- Sequencers

  merge_status := 1 ;
  FOR ident_line in select * from collection_maintenance where referenced_relation ='staging' AND  record_id = line.id
  LOOP
    FOR people_line IN select * from staging_people WHERE referenced_relation ='collection_maintenance' AND record_id = ident_line.id
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
    UPDATE staging SET status = delete(status,'operator') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('operator' => 'people')) where id= line.id;
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

  /**********
  * Institution in staging_relationship
  **********/
  FOR ident_line in select * from staging_relationship where record_id = line.id
  LOOP
    IF ident_line.institution_name IS NOT NULL and ident_line.institution_name  != '' AND ident_line.institution_ref is null AND ident_line.institution_name  != 'Not defined' THEN
      SELECT fct_look_for_institution(ident_line.institution_name) into ref_record;
      CASE ref_record
      WHEN -1 THEN
        UPDATE staging SET status = (status || ('institution_relationship' => 'too_much')) where id= line.id;
      WHEN 0 THEN
        UPDATE staging SET status = (status || ('institution_relationship' => 'not_found')) where id= line.id;
        ELSE
          UPDATE staging_relationship SET institution_ref = ref_record WHERE id=ident_line.id ;
          UPDATE staging SET status = delete(status,'institution_relationship') where id=line.id;
      END CASE;
    END IF;
  END LOOP;

  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_importer_abcd(req_import_ref integer)  RETURNS boolean
AS $$
DECLARE
  userid integer;
  rec_id integer;
  people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
  maintenance_line collection_maintenance;
  staging_line staging;
  id_to_delete integer ARRAY;
  id_to_keep integer ARRAY ;
  collection collections%ROWTYPE;
  code_count integer;
BEGIN
  SELECT * INTO collection FROM collections WHERE id = (SELECT collection_ref FROM imports WHERE id = req_import_ref AND is_finished = FALSE LIMIT 1);
  select user_ref into userid from imports where id=req_import_ref ;
  PERFORM set_config('darwin.userid',userid::varchar, false) ;
  INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword)
          (
            SELECT DISTINCT ON (referenced_relation, taxon_ref, keyword_type, keyword_indexed)
                  'taxonomy',
                  taxon_ref,
                  keyword_type,
                  "keyword"
            FROM staging INNER JOIN classification_keywords as ckmain ON ckmain.referenced_relation = 'staging'
                                                                     AND staging.id = ckmain.record_id
                         INNER JOIN imports as i ON i.id = staging.import_ref
            WHERE import_ref = req_import_ref
              AND to_import=true
              AND status = ''::hstore
              AND i.is_finished =  FALSE
              AND NOT EXISTS (
                              SELECT 1
                              FROM classification_keywords
                              WHERE referenced_relation = 'taxonomy'
                                AND record_id = staging.taxon_ref
                                AND keyword_type = ckmain.keyword_type
                                AND keyword_indexed = ckmain.keyword_indexed
              )
          );
  EXECUTE 'DELETE FROM classification_keywords
           WHERE referenced_relation = ''staging''
             AND record_id IN (
                                SELECT s.id
                                FROM staging s INNER JOIN imports i ON  s.import_ref = i.id
                                WHERE import_ref = $1
                                  AND to_import=true
                                  AND status = ''''::hstore
                                  AND i.is_finished =  FALSE
                             )'
  USING req_import_ref;
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
      -- I know it's dumb but....
      -- @ToDo: We need to correct this to avoid reselecting from the staging table !!!
      select * into staging_line from staging where id = all_line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);

      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=all_line.id;

    rec_id := nextval('specimens_id_seq');
    IF line.spec_ref IS NULL THEN
      INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
          acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref, type, sex, stage, state, social_status, rock_form,
          specimen_part, complete, institution_ref, building, floor, room, row, col, shelf, container, sub_container,container_type, sub_container_type,
          container_storage, sub_container_storage, surnumerary, specimen_status, specimen_count_min, specimen_count_max, object_name)
      VALUES (rec_id, COALESCE(line.category,'physical') , all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
        line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
        COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
        COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
        COALESCE(line.individual_rock_form,'not applicable'), COALESCE(line.part,'specimen'), COALESCE(line.complete,true), line.institution_ref, line.building,
        line.floor, line.room, line.row,  line.col, line.shelf, line.container, line.sub_container,COALESCE(line.container_type,'container'),
        COALESCE(line.sub_container_type, 'container'), COALESCE(line.container_storage,''),COALESCE(line.sub_container_storage,''),
        COALESCE(line.surnumerary,false), COALESCE(line.specimen_status,''),COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,line.part_count_min,1), line.object_name
      );
      FOR maintenance_line IN SELECT * from collection_maintenance where referenced_relation = 'staging' AND record_id=line.id
      LOOP
        SELECT people_ref into people_id FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
        UPDATE collection_maintenance set people_ref=people_id where id=maintenance_line.id ;
        DELETE FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
      END LOOP;

      SELECT COUNT(*) INTO code_count FROM codes WHERE referenced_relation = 'staging' AND record_id = line.id AND code_category = 'main' AND code IS NOT NULL;
      IF code_count = 0 THEN
        PERFORM fct_after_save_add_code(all_line.collection_ref, rec_id);
      ELSE
        UPDATE codes SET referenced_relation = 'specimens', 
                         record_id = rec_id, 
                         code_prefix = CASE WHEN code_prefix IS NULL THEN collection.code_prefix ELSE code_prefix END,
                         code_prefix_separator = CASE WHEN code_prefix_separator IS NULL THEN collection.code_prefix_separator ELSE code_prefix_separator END,
                         code_suffix = CASE WHEN code_suffix IS NULL THEN collection.code_suffix ELSE code_suffix END,
                         code_suffix_separator = CASE WHEN code_suffix_separator IS NULL THEN collection.code_suffix_separator ELSE code_suffix_separator END
        WHERE referenced_relation = 'staging'
          AND record_id = line.id
          AND code_category = 'main';
      END IF;

      UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      --UPDATE collection_maintenance SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      -- Import identifiers whitch identification have been updated to specimen
      INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref 
        FROM staging_people s, identifications i 
        WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
      DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
      -- Import collecting_methods
      INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
        SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref 
        FROM staging_collecting_methods 
        WHERE staging_ref = line.id;
      
      DELETE FROM staging_collecting_methods where staging_ref = line.id;
      UPDATE staging set spec_ref=rec_id WHERE id=all_line.id;

      FOR people_line IN SELECT * from staging_people WHERE referenced_relation = 'specimens'
      LOOP
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
      END LOOP;
      DELETE FROM staging_people WHERE referenced_relation = 'specimens' ;
    END IF ;
    id_to_delete = array_append(id_to_delete,all_line.id) ;
    END;
  END LOOP;
  select fct_imp_checker_staging_relationship() into id_to_keep ;
  IF id_to_keep IS NOT NULL THEN
    DELETE from staging where (id = ANY (id_to_delete)) AND NOT (id = ANY (id_to_keep)) ;
  else
    DELETE from staging where (id = ANY (id_to_delete)) ;
  END IF ;
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
    ELSEIF OLD.referenced_relation = 'identifications' THEN
      select s.import_ref INTO import_id FROM staging s, staging_people sp, identifications i WHERE sp.id=OLD.id
      AND sp.record_id = i.id AND i.record_id = s.id ;
    ELSE
      select s.import_ref INTO import_id FROM staging s, staging_people sp, collection_maintenance c WHERE sp.id=OLD.id
      AND sp.record_id = c.id AND c.record_id = s.id ;
    END IF;

    UPDATE staging_people SET people_ref = NEW.people_ref WHERE id IN (
      SELECT sp.id from staging_people sp, identifications i, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id
      AND i.record_id = s.id AND sp.referenced_relation = 'identifications' AND sp.record_id = i.id
      UNION
      SELECT sp.id from staging_people sp, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id AND
      sp.record_id = s.id AND sp.referenced_relation = 'staging'
      UNION
      SELECT sp.id from staging_people sp, collection_maintenance c, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id
      AND c.record_id = s.id AND sp.referenced_relation = 'collection_maintenance' AND sp.record_id = c.id
    );
    -- update status field, if all error people are corrected, statut 'people', 'operator' or 'identifiers' will be removed
    PERFORM fct_imp_checker_people(s.*) FROM staging s WHERE import_ref = import_id AND (status::hstore ? 'people' OR status::hstore ? 'identifiers'  OR status::hstore ? 'operator') ;
    PERFORM set_config('darwin.upd_imp_ref', NULL, true);
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_upd_institution_staging_relationship() RETURNS TRIGGER
AS $$
DECLARE
  import_id integer;
  line RECORD ;
BEGIN
 IF get_setting('darwin.upd_people_ref') is null OR  get_setting('darwin.upd_people_ref') = '' THEN
    PERFORM set_config('darwin.upd_people_ref', 'ok', true);
    select s.import_ref INTO import_id FROM staging s, staging_relationship sr WHERE sr.id=OLD.id AND sr.record_id = s.id ;
    UPDATE staging_relationship SET institution_ref = NEW.institution_ref WHERE id IN (
      SELECT sr.id from staging_relationship sr, staging s WHERE sr.institution_name = OLD.institution_name AND s.import_ref = import_id AND
      sr.record_id = s.id
    );
    FOR line IN SELECT s.* FROM staging s, staging_relationship sr WHERE s.id=sr.record_id AND sr.institution_ref = NEW.institution_ref
    LOOP
      UPDATE staging SET status = delete(status,'institution_relationship') where id=line.id;
    END LOOP ;
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
      SELECT t.id ,t.name, t.level_ref , cl.level_sys_name, t.status, t.extinct
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
        litho_status IS NOT DISTINCT FROM  OLD.litho_status AND litho_local IS NOT DISTINCT FROM  OLD.litho_local AND litho_color IS NOT DISTINCT FROM OLD.litho_color
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
        expedition_to_date=NEW.expedition_to_date, expedition_from_date_mask=NEW.expedition_from_date_mask , expedition_to_date_mask=NEW.expedition_to_date_mask,
        status = delete(status,'expedition')
      WHERE
        expedition_name IS NOT DISTINCT FROM OLD.expedition_name AND  expedition_from_date IS NOT DISTINCT FROM OLD.expedition_from_date AND
        expedition_to_date IS NOT DISTINCT FROM OLD.expedition_to_date AND  expedition_from_date_mask IS NOT DISTINCT FROM OLD.expedition_from_date_mask  AND
        expedition_to_date_mask IS NOT DISTINCT FROM OLD.expedition_to_date_mask
        AND import_ref = NEW.import_ref;
      NEW.status = delete(NEW.status,'expedition');
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

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_comments (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                  targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                  new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                  new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE comments as mc
SET referenced_relation = $3, record_id = $4
WHERE mc.referenced_relation = $1
  AND record_id = $2
  AND NOT EXISTS(SELECT 1
                 FROM comments AS sc
                 WHERE sc.referenced_relation = $3
                       AND sc.record_id = $4
                       AND sc.notion_concerned = mc.notion_concerned
                       AND sc.comment_indexed = mc.comment_indexed
                 LIMIT 1
                );
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_properties (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                    new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE properties as mp
SET referenced_relation = $3, record_id = $4
WHERE mp.referenced_relation = $1
  AND record_id = $2
  AND NOT EXISTS(SELECT 1
                 FROM properties AS sp
                 WHERE sp.referenced_relation = $3
                       AND sp.record_id = $4
                       AND sp.property_type = mp.property_type
                       AND sp.applies_to = mp.applies_to
                       AND sp.date_from_mask = mp.date_from_mask
                       AND sp.date_from = mp.date_from
                       AND sp.date_to_mask = mp.date_to_mask
                       AND sp.date_to = mp.date_to
                       AND sp.is_quantitative = mp.is_quantitative
                       AND sp.property_unit = mp.property_unit
                       AND sp.method_indexed = mp.method_indexed
                       AND sp.lower_value = mp.lower_value
                       AND sp.upper_value = mp.upper_value
                       AND sp.property_accuracy = mp.property_accuracy
                );
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_ext_links (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                   targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                   new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                   new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE ext_links as mel
SET referenced_relation = $3, record_id = $4
WHERE mel.referenced_relation = $1
  AND record_id = $2
  AND NOT EXISTS(SELECT 1
                 FROM ext_links AS sel
                 WHERE sel.referenced_relation = $3
                       AND sel.record_id = $4
                       AND sel.url = mel.url
                );
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_multimedia (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                    new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE multimedia as mm
SET referenced_relation = $3, record_id = $4
WHERE mm.referenced_relation = $1
  AND record_id = $2
  AND NOT EXISTS(SELECT 1
                 FROM multimedia AS sm
                 WHERE sm.referenced_relation = $3
                       AND sm.record_id = $4
                       AND sm.mime_type = mm.mime_type
                       AND sm.search_indexed = mm.search_indexed
                );
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_insurances (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                    new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE insurances as mi
SET referenced_relation = $3, record_id = $4
WHERE mi.referenced_relation = $1
  AND record_id = $2
  AND NOT EXISTS(SELECT 1
                 FROM insurances AS si
                 WHERE si.referenced_relation = $3
                   AND si.record_id = $4
                   AND si.insurance_value = mi.insurance_value
                   AND si.insurance_currency = mi.insurance_currency
                   AND si.date_from_mask = mi.date_from_mask
                   AND si.date_from = mi.date_from
                   AND si.date_to_mask = mi.date_to_mask
                   AND si.date_to = mi.date_to
                   AND COALESCE(si.insurer_ref,0) = COALESCE(mi.insurer_ref,0)
                );
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info(line staging, st_type text) RETURNS boolean
AS $$
DECLARE
  info_line staging_info ;
  record_line RECORD ;
BEGIN
  FOR info_line IN select * from staging_info WHERE staging_ref = line.id AND referenced_relation = st_type
  LOOP
    CASE info_line.referenced_relation
      WHEN 'gtu' THEN
      IF line.gtu_ref IS NOT NULL THEN

        PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.gtu_ref);
        PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.gtu_ref);

        PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.gtu_ref);
        PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.gtu_ref);

      END IF;
      WHEN 'taxonomy' THEN
      IF line.taxon_ref IS NOT NULL THEN

        PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.taxon_ref);
        PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.taxon_ref);

        PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.taxon_ref);
        PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.taxon_ref);

      END IF;
      WHEN 'expeditions' THEN
      IF line.expedition_ref IS NOT NULL THEN

        PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.expedition_ref);
        PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.expedition_ref);

        PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.expedition_ref);

      END IF;
      WHEN 'lithostratigraphy' THEN
      IF line.litho_ref IS NOT NULL THEN

        PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.litho_ref);
        PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.litho_ref);

        PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.litho_ref);
        PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.litho_ref);

      END IF;
      WHEN 'lithology' THEN
      IF line.lithology_ref IS NOT NULL THEN

        PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.lithology_ref);
        PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.lithology_ref);

        PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.lithology_ref);
        PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.lithology_ref);

      END IF;
      WHEN 'chronostratigraphy' THEN
      IF line.chrono_ref IS NOT NULL THEN

        PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.chrono_ref);
        PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.chrono_ref);

        PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.chrono_ref);
        PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.chrono_ref);

      END IF;
      WHEN 'mineralogy' THEN
      IF line.mineral_ref IS NOT NULL THEN

        PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.mineral_ref);
        PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.mineral_ref);

        PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.mineral_ref);
        PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.mineral_ref);

      END IF;
      WHEN 'igs' THEN
      IF line.ig_ref IS NOT NULL THEN

        PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.ig_ref);
        PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.ig_ref);

        PERFORM fct_imp_checker_staging_info_insurances('staging_info', info_line.id, info_line.referenced_relation, line.ig_ref);

      END IF;
    ELSE continue ;
    END CASE ;
  END LOOP;
  DELETE FROM staging_info WHERE staging_ref = line.id ;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_relationship() RETURNS integer ARRAY
AS $$
DECLARE
  relation_line RECORD ;
  specimen_ref INTEGER ;
  id_array integer ARRAY ;
BEGIN

  FOR relation_line IN select sr.*, s.spec_ref from staging_relationship sr, staging s WHERE sr.record_id = s.id AND s.spec_ref IS NOT NULL
  LOOP
    IF relation_line.staging_related_ref IS NOT NULL THEN
      SELECT spec_ref INTO specimen_ref FROM staging where id=relation_line.staging_related_ref ;
      IF specimen_ref IS NULL THEN
        id_array := array_append(id_array, relation_line.record_id);
        continue ;
      ELSE
        INSERT INTO specimens_relationships(id, specimen_ref, relationship_type, unit_type, specimen_related_ref, institution_ref)
        SELECT nextval('specimens_relationships_id_seq'), relation_line.spec_ref, relation_line.relationship_type, unit_type, specimen_ref, institution_ref
        from staging_relationship where id=relation_line.id AND staging_related_ref=relation_line.staging_related_ref;
      END IF;
    ELSE
    INSERT INTO specimens_relationships(id, specimen_ref, relationship_type, unit_type, institution_ref,taxon_ref, mineral_ref, source_name,
    source_id, quantity, unit)
        SELECT nextval('specimens_relationships_id_seq'), relation_line.spec_ref, relation_line.relationship_type, unit_type, institution_ref,
        taxon_ref, mineral_ref, source_name, source_id, quantity, unit
        from staging_relationship where id=relation_line.id ;
    END IF ;
    DELETE FROM staging_relationship WHERE id = relation_line.id ;
  END LOOP;
  RETURN id_array;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_mask_date(date_fld timestamp , mask_fld integer) RETURNS text as
$$

  SELECT
CASE WHEN ($2 & 32)!=0 THEN date_part('year',$1)::text ELSE 'xxxx' END || '-' ||
CASE WHEN ($2 & 16)!=0 THEN date_part('month',$1)::text ELSE 'xx' END || '-' ||
CASE WHEN ($2 & 8)!=0 THEN date_part('day',$1)::text ELSE 'xx' END;
$$
LANGUAGE sql immutable;


CREATE OR REPLACE function fct_remove_last_flag() RETURNS TRIGGER
language plpgsql
AS
$$
BEGIN
    UPDATE informative_workflow
    SET is_last = false
    WHERE referenced_relation = NEW.referenced_relation
      AND record_id = NEW.record_id;
  RETURN NEW;
END;
$$;

CREATE OR REPLACE function fct_informative_reset_last_flag() RETURNS TRIGGER
language plpgsql
AS
$$
BEGIN
    UPDATE informative_workflow
    SET is_last = true
    WHERE referenced_relation = OLD.referenced_relation
      AND record_id = OLD.record_id
      AND id = (select id from informative_workflow
        WHERE referenced_relation = OLD.referenced_relation AND record_id = OLD.record_id ORDER BY modification_date_time desc LIMIT 1)
    ;
  RETURN NEW;
END;
$$;

CREATE OR REPLACE function fct_remove_last_flag_loan() RETURNS TRIGGER
language plpgsql
AS
$$
BEGIN
    UPDATE loan_status
    SET is_last = false
    WHERE loan_ref = NEW.loan_ref;
  RETURN NEW;
END;
$$;


CREATE OR REPLACE function chk_specimens_not_loaned() RETURNS TRIGGER
language plpgsql
AS
$$
BEGIN

    IF exists( SELECT 1 FROM loan_items i INNER JOIN loan_status s on i.loan_ref = s.loan_ref
        WHERE s.is_last= true AND s.status != 'closed' AND i.specimen_ref = OLD.id ) THEN
      RAISE EXCEPTION 'The Part is currently used in an ongoing loan';
    END IF;
    RETURN OLD;
END;
$$;

CREATE OR REPLACE function fct_auto_insert_status_history() RETURNS TRIGGER
language plpgsql
AS
$$
DECLARE
 user_id int;
BEGIN
    SELECT COALESCE(get_setting('darwin.userid'),'0')::integer INTO user_id;
    IF user_id = 0 THEN
      RETURN NEW;
    END IF;

    INSERT INTO loan_status
      (loan_ref, user_ref, status, modification_date_time, comment, is_last)
      VALUES
      (NEW.id, user_id, 'new', now(), '', true);

    INSERT INTO loan_rights
      (loan_ref, user_ref, has_encoding_right)
      VALUES
      (NEW.id, user_id, true);

  RETURN NEW;
END;
$$;


CREATE OR REPLACE FUNCTION fct_cpy_ig_to_loan_items() RETURNS trigger
AS $$
BEGIN
  IF OLD.ig_ref is distinct from NEW.ig_ref THEN
    UPDATE loan_items li SET ig_ref = NEW.ig_ref
    WHERE specimen_ref = NEW.ID
    AND li.ig_ref IS NOT DISTINCT FROM OLD.ig_ref;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_cpy_deleted_file() RETURNS trigger
AS $$
BEGIN
  INSERT INTO multimedia_todelete (uri) VALUES (OLD.uri);
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_cpy_loan_history(loan_id integer) RETURNS boolean
AS $$
BEGIN

  -- LOAN
  INSERT INTO loan_history (loan_ref, referenced_table, record_line)
  (
    select loan_id, 'loans', hstore(l.*) from loans l where l.id = loan_id

    UNION

    select loan_id, 'catalogue_people', hstore(p.*) from catalogue_people p where
      (referenced_relation='loans'  AND record_id = loan_id) OR (referenced_relation='loan_items'  AND record_id in (select id from loan_items l where l.loan_ref = loan_id) )

    UNION

    select loan_id, 'properties', hstore(c.*) from properties c where
      (referenced_relation='loans'  AND record_id = loan_id) OR (referenced_relation='loan_items'  AND record_id in (select id from loan_items l where l.loan_ref = loan_id) )

  );


  --ITEMS
  INSERT INTO loan_history (loan_ref, referenced_table, record_line)
  (
    select loan_id, 'loan_items', hstore(l.*) from loan_items l where l.loan_ref = loan_id

    UNION

    select loan_id, 'specimens', hstore(sfl.*) from specimens sfl
      where sfl.id in (select specimen_ref from loan_items l where l.loan_ref = loan_id)
  );

  -- BOTH
  INSERT INTO loan_history (loan_ref, referenced_table, record_line)
  (
    select loan_id, 'people', hstore(p.*) from people p where id in (select (record_line->'people_ref')::int from loan_history where loan_ref = loan_id
      and referenced_table='catalogue_people' and modification_date_time = now())

    UNION

    select loan_id, 'people_addresses', hstore(p.*) from people_addresses p where person_user_ref in (select (record_line->'id')::int from loan_history where loan_ref = loan_id
      and referenced_table='people' and modification_date_time = now())
  );
  RETURN true;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_cast_to_real(element text) RETURNS real
AS $$
DECLARE r_val real;
BEGIN
    BEGIN
      r_val := element::real;
      return r_val;
    EXCEPTION WHEN SQLSTATE '22P02' THEN
      RETURN null;
    END;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION point_equal ( POINT, POINT )
RETURNS boolean AS
'SELECT
CASE WHEN $1[0] = $2[0] AND $1[1] = $2[1] THEN true
ELSE false END;'
LANGUAGE SQL IMMUTABLE;


CREATE OR REPLACE FUNCTION isnumeric(text) RETURNS BOOLEAN AS $$
DECLARE x NUMERIC;
BEGIN
    x = $1::NUMERIC;
    RETURN TRUE;
EXCEPTION WHEN others THEN
    RETURN FALSE;
END;
$$ LANGUAGE plpgsql IMMUTABLE;


CREATE OPERATOR =  (LEFTARG = POINT,  RIGHTARG = POINT, PROCEDURE = point_equal);

CREATE OR REPLACE FUNCTION check_auto_increment_code_in_spec()
  RETURNS trigger AS
  $BODY$
DECLARE
  col collections%ROWTYPE;
  number BIGINT ;
BEGIN
  IF TG_OP != 'DELETE' THEN
    IF NEW.referenced_relation = 'specimens' THEN
      SELECT c.* INTO col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=NEW.record_id;
      IF FOUND THEN
        IF NEW.code_category = 'main' THEN
          IF isnumeric(NEW.code) AND strpos(NEW.code, 'E') = 0 THEN
            number := NEW.code::bigint;
            IF number > col.code_last_value THEN
              UPDATE collections set code_last_value = number WHERE id=col.id ;
            END IF;
          ELSE
            UPDATE collections
            SET code_last_value = (SELECT max(code_num)
                                   FROM codes inner join specimens
                                     ON codes.referenced_relation = 'specimens'
                                     AND codes.record_id = specimens.id
                                   WHERE codes.code_category = 'main'
                                     AND specimens.collection_ref = col.id
                                     AND codes.code_num IS NOT NULL
                                  )
            WHERE id = col.id
              AND EXISTS (SELECT 1
                          FROM codes inner join specimens
                            ON codes.referenced_relation = 'specimens'
                            AND codes.record_id = specimens.id
                          WHERE codes.code_category = 'main'
                            AND specimens.collection_ref = col.id
                            AND codes.code_num IS NOT NULL
                          LIMIT 1
                         );
            IF NOT FOUND THEN
              UPDATE collections
              SET code_last_value = DEFAULT
              WHERE id=col.id;
            END IF;
          END IF;
        ELSEIF TG_OP = 'UPDATE' THEN
          IF OLD.code_category = 'main' THEN
            IF isnumeric(OLD.code) AND strpos(OLD.code, 'E') = 0 THEN
              number := OLD.code::bigint;
              IF number = col.code_last_value THEN
                UPDATE collections
                SET code_last_value = (SELECT max(code_num)
                                       FROM codes inner join specimens
                                         ON codes.referenced_relation = 'specimens'
                                         AND codes.record_id = specimens.id
                                       WHERE codes.code_category = 'main'
                                         AND specimens.collection_ref = col.id
                                         AND codes.code_num IS NOT NULL
                                      )
                WHERE id = col.id
                  AND EXISTS (SELECT 1
                              FROM codes inner join specimens
                                ON codes.referenced_relation = 'specimens'
                                AND codes.record_id = specimens.id
                              WHERE codes.code_category = 'main'
                                AND specimens.collection_ref = col.id
                                AND codes.code_num IS NOT NULL
                              LIMIT 1
                             );
                IF NOT FOUND THEN
                  UPDATE collections
                  SET code_last_value = DEFAULT
                  WHERE id=col.id;
                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
      END IF;
    END IF ;
    RETURN NEW;
  ELSE
    IF OLD.referenced_relation = 'specimens' AND OLD.code_category = 'main' THEN
      SELECT c.* INTO col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=OLD.record_id;
      IF FOUND AND isnumeric(OLD.code) AND strpos(OLD.code, 'E') = 0 THEN
        UPDATE collections
        SET code_last_value = (SELECT max(code_num)
                               FROM codes INNER JOIN specimens
                                 ON  codes.referenced_relation = 'specimens'
                                 AND codes.record_id = specimens.id
                               WHERE codes.code_category = 'main'
                                 AND specimens.collection_ref = col.id
                                 AND codes.code_num IS NOT NULL
                              )
        WHERE id=col.id
          AND EXISTS (SELECT 1
                      FROM codes inner join specimens
                        ON codes.referenced_relation = 'specimens'
                        AND codes.record_id = specimens.id
                      WHERE codes.code_category = 'main'
                        AND specimens.collection_ref = col.id
                        AND codes.code_num IS NOT NULL
                      LIMIT 1
                     );
        IF NOT FOUND THEN
          UPDATE collections
          SET code_last_value = DEFAULT
          WHERE id=col.id;
        END IF;
      END IF;
    END IF ;
    RETURN OLD;
  END IF;
END;
$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;

CREATE OR REPLACE FUNCTION fct_after_save_add_code(IN collectionId collections.id%TYPE, IN specimenId specimens.id%TYPE) RETURNS integer
AS $$
DECLARE
  col collections%ROWTYPE;
BEGIN
  SELECT c.* INTO col FROM collections c WHERE c.id = collectionId;
  IF FOUND THEN
    IF col.code_auto_increment = TRUE THEN
      INSERT INTO codes (referenced_relation, record_id, code_prefix, code_prefix_separator, code, code_suffix_separator, code_suffix)
      SELECT 'specimens', specimenId, col.code_prefix, col.code_prefix_separator, (col.code_last_value+1)::varchar, col.code_suffix_separator, col.code_suffix
      WHERE NOT EXISTS (SELECT 1 
                        FROM codes 
                        WHERE referenced_relation = 'specimens'
                          AND record_id = specimenId
                          AND code_category = 'main'
                          AND code_num IS NOT NULL
                        LIMIT 1
                       );
    END IF;
  END IF;
  RETURN 0;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION update_collections_code_last_val() RETURNS trigger 
AS $$
BEGIN
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = NEW.collection_ref
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = NEW.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = NEW.collection_ref
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = OLD.collection_ref
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = OLD.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = OLD.collection_ref
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  IF NOT FOUND THEN
    UPDATE collections
    SET code_last_value = DEFAULT
    WHERE id = OLD.collection_ref;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION update_collections_code_last_val_after_spec_del() RETURNS trigger 
AS $$
BEGIN
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = OLD.collection_ref
                           AND specimens.id != OLD.id
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = OLD.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = OLD.collection_ref
                  AND specimens.id != OLD.id
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  IF NOT FOUND THEN
    UPDATE collections
    SET code_last_value = DEFAULT
    WHERE id = OLD.collection_ref;
  END IF;
  RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_importer_catalogue(req_import_ref integer,referenced_relation text,exclude_invalid_entries boolean default false) RETURNS BOOLEAN
LANGUAGE plpgsql
AS
  $$
  DECLARE
    staging_catalogue_line staging_catalogue;
    where_clause_complement_1 text := ' ';
    where_clause_complement_2 text := ' ';
    where_clause_complement_3 text := ' ';
    where_clause_complement_3_bis text := ' ';
    where_clause_complement_4 text := ' ';
    where_clause_complement_5 text := ' ';
    where_clause_exclude_invalid text := ' ';
    recCatalogue RECORD;
    parent_path template_classifications.path%TYPE;
    parentRef staging_catalogue.parent_ref%TYPE;
    parent_level catalogue_levels.id%TYPE;
    catalogueRef staging_catalogue.catalogue_ref%TYPE;
    levelRef staging_catalogue.level_ref%TYPE;
    error_msg TEXT := '';
    children_move_forward BOOLEAN := FALSE;
    level_naming TEXT;
    tempSQL TEXT;
  BEGIN
    -- Browse all staging_catalogue lines
    FOR staging_catalogue_line IN SELECT * from staging_catalogue WHERE import_ref = req_import_ref ORDER BY level_ref, fullToIndex(name)
    LOOP
      IF trim(touniquestr(staging_catalogue_line.name)) = '' THEN
        RAISE EXCEPTION E'Case 0, Could not import this file, % is not a valid name.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
      END IF;
      SELECT parent_ref, catalogue_ref, level_ref INTO parentRef, catalogueRef, levelRef FROM staging_catalogue WHERE id = staging_catalogue_line.id;
      IF catalogueRef IS NULL THEN
        -- Check if we're at a top taxonomic entry in the template/staging_catalogue line
        IF parentRef IS NULL THEN
          -- If top entry, we have not parent defined and we therefore have no other filtering criteria
          where_clause_complement_1 := ' ';
          where_clause_complement_2 := ' ';
          where_clause_complement_3 := ' ';
          where_clause_complement_3_bis := ' ';
        ELSE
          -- If a child entry, we've got to use the informations from the already matched or created parent
          where_clause_complement_1 := '  AND tax.parent_ref = ' || parentRef || ' ';
          where_clause_complement_2 := '  AND tax.parent_ref != ' || parentRef || ' ';
          -- Select the path from parent catalogue unit
          EXECUTE 'SELECT path, level_ref FROM ' || quote_ident(referenced_relation) || ' WHERE id = $1'
          INTO parent_path, parent_level
          USING parentRef;
          where_clause_complement_3 := '  AND position (' || quote_literal(parent_path) || ' IN tax.path) = 1 ';
          where_clause_complement_3_bis := '  AND (select t2.level_ref from ' || quote_ident(referenced_relation) || ' as t2 where t2.id = tax.parent_ref) > ' || parent_level || ' ';
        END IF;
        where_clause_complement_4 := '  AND left(substring(tax.name from length(trim(' ||
                                     quote_literal(staging_catalogue_line.name) || '))+1),1) IN (' ||
                                     quote_literal(' ') || ', ' || quote_literal(',') || ') ';
        where_clause_complement_5 := '  AND left(substring(' || quote_literal(staging_catalogue_line.name) ||
                                     ' from length(trim(tax.name))+1),1) IN (' ||
                                     quote_literal(' ') || ', ' || quote_literal(',') || ') ';
        -- Set the invalid where clause if asked
        IF exclude_invalid_entries = TRUE THEN
          where_clause_exclude_invalid := '  AND tax.status != ' || quote_literal('invalid') || ' ';
        END IF;
        -- Check a perfect match entry
        -- Take care here, a limit 1 has been set, we only kept the EXIT in case the limit would be accidently removed
        FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                    'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                    'WHERE tax.level_ref = $1 ' ||
                                    '  AND tax.name_indexed = fullToIndex( $2 ) ' ||
                                    where_clause_exclude_invalid ||
                                    where_clause_complement_1 ||
                                    'LIMIT 1;'
        USING staging_catalogue_line.level_ref, staging_catalogue_line.name
        LOOP
          -- If more than one entry found, we set an error...
          IF recCatalogue.total_count > 1 THEN
            RAISE EXCEPTION E'Case 1, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
          END IF;
          EXIT;
        END LOOP;
        -- No perfect match occured with the same parent (if it applies - doesn't apply for top taxonomic entry in template)
        IF NOT FOUND THEN
          -- For this step, as it depends upon the existence of a parent, we test well we are on that case
          -- It concerns a perfect match with parents differents but with a path common
          -- That means, if only one entry exists, that they are the same but with a more detailed hierarchy in the
          -- already existing entry
          IF parentRef IS NOT NULL THEN
            FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                        'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                        'WHERE tax.level_ref = $1 ' ||
                                        '  AND tax.name_indexed = fullToIndex( $2 ) ' ||
                                        where_clause_exclude_invalid ||
                                        where_clause_complement_2 ||
                                        where_clause_complement_3 ||
                                        where_clause_complement_3_bis ||
                                        'LIMIT 1;'
            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
            LOOP
              -- If for this kind of perfect match with different parent but kind of same path start, we get multiple
              -- possibilities, then fail
              IF recCatalogue.total_count > 1 THEN
                RAISE EXCEPTION E'Case 2, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
              END IF;
              EXIT;
            END LOOP;
            -- If it gave no result, we've got to move forward and try the next option
            IF NOT FOUND THEN
              children_move_forward := TRUE;
            END IF;
          END IF;
          IF parentRef IS NULL OR children_move_forward = TRUE THEN
            -- This next option try a fuzzy match, with, if it's a child entry in the template, a verification that
            -- the parent specified in the template and the path of the potential corresponding entry in catalogue
            -- have a common path...
            tempSQL := 'SELECT COUNT(id) OVER () as total_count, * ' ||
                       'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                       'WHERE tax.level_ref = $1 ' ||
                       '  AND tax.name_indexed LIKE fullToIndex( $2 ) || ' || quote_literal('%') ||
                       where_clause_exclude_invalid ||
                       where_clause_complement_3 ||
                       where_clause_complement_4;
            IF parentRef IS NOT NULL THEN
              tempSQL := tempSQL || where_clause_complement_1;
            END IF;
            tempSQL := tempSQL || 'LIMIT 1;';
            FOR recCatalogue IN EXECUTE tempSQL
            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
            LOOP
              -- If we're on the case of a top entry in the template, we cannot afford the problem of multiple entries
              IF recCatalogue.total_count > 1 THEN
                RAISE EXCEPTION E'Case 3, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
              END IF;
              EXIT;
            END LOOP;
            -- Last chance is to try to find if the entry in DaRWIN shouldn't be completed
            -- This entry should be "alone" of its kind - check the NOT EXIST clause
            IF NOT FOUND THEN
              FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                          'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                          'WHERE tax.level_ref = $1 ' ||
                                          '  AND position(tax.name_indexed IN fullToIndex( $2 )) = 1 ' ||
                                          where_clause_exclude_invalid ||
                                          '  AND NOT EXISTS (SELECT 1 ' ||
                                          '                  FROM ' || quote_ident(referenced_relation) || ' as stax ' ||
                                          '                  WHERE stax.id != tax.id ' ||
                                          '                  AND stax.level_ref = tax.level_ref ' ||
                                          '                  AND stax.path = tax.path ' ||
                                          '                  AND stax.name_indexed LIKE tax.name_indexed || ' || quote_literal('%') ||
                                          '                  LIMIT 1 ' ||
                                          '                 ) ' ||
                                          where_clause_complement_3 ||
                                          where_clause_complement_5 ||
                                          'LIMIT 1;'
              USING staging_catalogue_line.level_ref, staging_catalogue_line.name
              LOOP
                IF recCatalogue.total_count > 1 THEN
                  RAISE EXCEPTION E'Case 4, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
                ELSE
                  -- If only one entry is found, we can replace the name of this entry
                  EXECUTE 'UPDATE ' || quote_ident(referenced_relation) || ' ' ||
                          'SET name = ' || quote_literal(staging_catalogue_line.name) || ' ' ||
                          'WHERE id = ' || recCatalogue.id || ';';
                END IF;
                EXIT;
              END LOOP;
              IF NOT FOUND THEN
                IF parentRef IS NOT NULL THEN
                  EXECUTE 'INSERT INTO ' || quote_ident(referenced_relation) || '(id,name,level_ref,parent_ref) ' ||
                          'VALUES(DEFAULT,$1,$2,$3) ' ||
                          'RETURNING *;'
                  INTO recCatalogue
                  USING staging_catalogue_line.name,staging_catalogue_line.level_ref,parentRef;
                -- tell to update the staging line to set the catalogue_ref with the id found
                ELSE
                  SELECT level_name INTO level_naming FROM catalogue_levels WHERE id = staging_catalogue_line.level_ref;
                  RAISE EXCEPTION 'Could not import this file, % (level %) does not exist in DaRWIN and cannot be attached, correct your file or create this % manually', staging_catalogue_line.name,  level_naming, quote_ident(referenced_relation);
                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
        -- update the staging line to set the catalogue_ref with the id found
        -- update the staging children lines
        WITH staging_catalogue_updated(updated_id/*, catalogue_ref_updated*/) AS (
          UPDATE staging_catalogue as sc
          SET catalogue_ref = recCatalogue.id
          WHERE sc.import_ref = staging_catalogue_line.import_ref
                AND sc.name = staging_catalogue_line.name
                AND sc.level_ref = staging_catalogue_line.level_ref
          RETURNING id
        )
        UPDATE staging_catalogue as msc
        SET parent_ref = recCatalogue.id,
          parent_updated = TRUE
        WHERE msc.import_ref = staging_catalogue_line.import_ref
              AND msc.parent_ref IN (
          SELECT updated_id FROM staging_catalogue_updated
        )
              AND parent_updated = FALSE;
      END IF;
      children_move_forward := FALSE;
    END LOOP;
    RETURN TRUE;
    EXCEPTION WHEN OTHERS THEN
    IF SQLERRM = 'This record does not follow the level hierarchy' THEN
      SELECT level_name INTO level_naming FROM catalogue_levels WHERE id = staging_catalogue_line.level_ref;
      RAISE EXCEPTION E'Could not import this file, % (level %) does not follow the accepted level hierarchy in DaRWIN an cannot be attached nor created.\nPlease correct your file.\nStaging Catalogue Line: %', staging_catalogue_line.name,  level_naming, staging_catalogue_line.id;
    ELSE
      RAISE EXCEPTION '%', SQLERRM;
    END IF;
  END;
  $$;

CREATE OR REPLACE FUNCTION fct_listing_taxonomy (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "domain" TEXT,
  "kingdom" TEXT,
  "super_phylum" TEXT,
  "phylum" TEXT,
  "sub_phylum" TEXT,
  "infra_phylum" TEXT,
  "super_cohort_botany" TEXT,
  "cohort_botany" TEXT,
  "sub_cohort_botany" TEXT,
  "infra_cohort_botany" TEXT,
  "super_class" TEXT,
  "class" TEXT,
  "sub_class" TEXT,
  "infra_class" TEXT,
  "super_division" TEXT,
  "division" TEXT,
  "sub_division" TEXT,
  "infra_division" TEXT,
  "super_legion" TEXT,
  "legion" TEXT,
  "sub_legion" TEXT,
  "infra_legion" TEXT,
  "super_cohort_zoology" TEXT,
  "cohort_zoology" TEXT,
  "sub_cohort_zoology" TEXT,
  "infra_cohort_zoology" TEXT,
  "super_order" TEXT,
  "order" TEXT,
  "sub_order" TEXT,
  "infra_order" TEXT,
  "section_zoology" TEXT,
  "sub_section_zoology" TEXT,
  "super_family" TEXT,
  "family" TEXT,
  "sub_family" TEXT,
  "infra_family" TEXT,
  "super_tribe" TEXT,
  "tribe" TEXT,
  "sub_tribe" TEXT,
  "infra_tribe" TEXT,
  "genus" TEXT,
  "sub_genus" TEXT,
  "section_botany" TEXT,
  "sub_section_botany" TEXT,
  "serie" TEXT,
  "sub_serie" TEXT,
  "super_species" TEXT,
  "species" TEXT,
  "sub_species" TEXT,
  "variety" TEXT,
  "sub_variety" TEXT,
  "form" TEXT,
  "sub_form" TEXT,
  "abberans" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of taxon ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    taxon_id INTEGER;
    recTaxonomic_levels RECORD;
  BEGIN
    -- First, test that there's at least one taxon to search the hierarchy from
    IF array_length(taxon_ids, 1) > 0 THEN
      -- Compose the list of taxon ids as comma separated string
      where_first_list_of_ids := array_to_string(taxon_ids, ',');
      -- Loop through these taxon ids to compose the second part of the sql where clause
      FOREACH taxon_id IN ARRAY taxon_ids LOOP
        where_second_sql := 'or strpos	(tax.path, (select ssstax.path || ssstax.id
                                                    from taxonomy ssstax
                                                    where ssstax.id = ' || taxon_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where tax.id in (select sstax.id
                                          from taxonomy sstax
                                          where sstax.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((tax.path || tax.id), tax.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all taxonomic levels and for each of them, include a select clause
      FOR recTaxonomic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'taxonomy' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select subtax.name
                             from taxonomy as subtax inner join (select unnest(string_to_array(substring(tax.path || tax.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as taxids
                             on subtax.id = taxids.id::integer
                             where taxids.id != ' || CHR(39) || CHR(39) || '
                               and subtax.level_ref = ' || recTaxonomic_levels.id || '
                            )::text as "' || recTaxonomic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from taxonomy as tax left join specimens on tax.id = specimens.taxon_ref ';
      order_by_sql_part := 'order by (tax.path || tax.id), tax.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_listing_zoology (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "domain" TEXT,
  "kingdom" TEXT,
  "super_phylum" TEXT,
  "phylum" TEXT,
  "sub_phylum" TEXT,
  "infra_phylum" TEXT,
  "super_class" TEXT,
  "class" TEXT,
  "sub_class" TEXT,
  "infra_class" TEXT,
  "super_division" TEXT,
  "division" TEXT,
  "sub_division" TEXT,
  "infra_division" TEXT,
  "super_legion" TEXT,
  "legion" TEXT,
  "sub_legion" TEXT,
  "infra_legion" TEXT,
  "super_cohort_zoology" TEXT,
  "cohort_zoology" TEXT,
  "sub_cohort_zoology" TEXT,
  "infra_cohort_zoology" TEXT,
  "super_order" TEXT,
  "order" TEXT,
  "sub_order" TEXT,
  "infra_order" TEXT,
  "section_zoology" TEXT,
  "sub_section_zoology" TEXT,
  "super_family" TEXT,
  "family" TEXT,
  "sub_family" TEXT,
  "infra_family" TEXT,
  "super_tribe" TEXT,
  "tribe" TEXT,
  "sub_tribe" TEXT,
  "infra_tribe" TEXT,
  "genus" TEXT,
  "sub_genus" TEXT,
  "serie" TEXT,
  "sub_serie" TEXT,
  "super_species" TEXT,
  "species" TEXT,
  "sub_species" TEXT,
  "variety" TEXT,
  "sub_variety" TEXT,
  "form" TEXT,
  "sub_form" TEXT,
  "abberans" TEXT
  )
AS $$
  SELECT "referenced_by_at_least_one_specimen","domain","kingdom","super_phylum","phylum","sub_phylum","infra_phylum","super_class","class","sub_class","infra_class","super_division","division","sub_division","infra_division","super_legion","legion","sub_legion","infra_legion","super_cohort_zoology","cohort_zoology","sub_cohort_zoology","infra_cohort_zoology","super_order","order","sub_order","infra_order","section_zoology","sub_section_zoology","super_family","family","sub_family","infra_family","super_tribe","tribe","sub_tribe","infra_tribe","genus","sub_genus","serie","sub_serie","super_species","species","sub_species","variety","sub_variety","form","sub_form","abberans" from fct_listing_taxonomy($1, variadic $2);
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_listing_botany (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "domain" TEXT,
  "kingdom" TEXT,
  "super_phylum" TEXT,
  "phylum" TEXT,
  "sub_phylum" TEXT,
  "infra_phylum" TEXT,
  "super_cohort_botany" TEXT,
  "cohort_botany" TEXT,
  "sub_cohort_botany" TEXT,
  "infra_cohort_botany" TEXT,
  "super_class" TEXT,
  "class" TEXT,
  "sub_class" TEXT,
  "infra_class" TEXT,
  "super_division" TEXT,
  "division" TEXT,
  "sub_division" TEXT,
  "infra_division" TEXT,
  "super_legion" TEXT,
  "legion" TEXT,
  "sub_legion" TEXT,
  "infra_legion" TEXT,
  "super_order" TEXT,
  "order" TEXT,
  "sub_order" TEXT,
  "infra_order" TEXT,
  "super_family" TEXT,
  "family" TEXT,
  "sub_family" TEXT,
  "infra_family" TEXT,
  "super_tribe" TEXT,
  "tribe" TEXT,
  "sub_tribe" TEXT,
  "infra_tribe" TEXT,
  "genus" TEXT,
  "sub_genus" TEXT,
  "section_botany" TEXT,
  "sub_section_botany" TEXT,
  "serie" TEXT,
  "sub_serie" TEXT,
  "super_species" TEXT,
  "species" TEXT,
  "sub_species" TEXT,
  "variety" TEXT,
  "sub_variety" TEXT,
  "form" TEXT,
  "sub_form" TEXT,
  "abberans" TEXT
  )
AS $$
  SELECT "referenced_by_at_least_one_specimen","domain","kingdom","super_phylum","phylum","sub_phylum","infra_phylum","super_cohort_botany","cohort_botany","sub_cohort_botany","infra_cohort_botany","super_class","class","sub_class","infra_class","super_division","division","sub_division","infra_division","super_legion","legion","sub_legion","infra_legion","super_order","order","sub_order","infra_order","super_family","family","sub_family","infra_family","super_tribe","tribe","sub_tribe","infra_tribe","genus","sub_genus","section_botany","sub_section_botany","serie","sub_serie","super_species","species","sub_species","variety","sub_variety","form","sub_form","abberans" from fct_listing_taxonomy($1, variadic $2);
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_listing_chronostratigraphy (IN nbr_records INTEGER, VARIADIC chrono_unit_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "eon" TEXT,
  "era" TEXT,
  "sub_era" TEXT,
  "system" TEXT,
  "serie" TEXT,
  "stage" TEXT,
  "sub_stage" TEXT,
  "sub_level_1" TEXT,
  "sub_level_2" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of chrono_unit ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    chrono_unit_id INTEGER;
    recChronostratigraphic_levels RECORD;
  BEGIN
    -- First, test that there's at least one chrono_unit to search the hierarchy from
    IF array_length(chrono_unit_ids, 1) > 0 THEN
      -- Compose the list of chrono_unit ids as comma separated string
      where_first_list_of_ids := array_to_string(chrono_unit_ids, ',');
      -- Loop through these chrono_unit ids to compose the second part of the sql where clause
      FOREACH chrono_unit_id IN ARRAY chrono_unit_ids LOOP
        where_second_sql := 'or strpos	(chronos.path, (select ssschronos.path || ssschronos.id
                                                    from chronostratigraphy ssschronos
                                                    where ssschronos.id = ' || chrono_unit_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where chronos.id in (select sschronos.id
                                          from chronostratigraphy sschronos
                                          where sschronos.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((chronos.path || chronos.id), chronos.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all chronostratigraphic levels and for each of them, include a select clause
      FOR recChronostratigraphic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'chronostratigraphy' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select subchronos.name
                             from chronostratigraphy as subchronos inner join (select unnest(string_to_array(substring(chronos.path || chronos.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as chronosids
                             on subchronos.id = chronosids.id::integer
                             where chronosids.id != ' || CHR(39) || CHR(39) || '
                               and subchronos.level_ref = ' || recChronostratigraphic_levels.id || '
                            )::text as "' || recChronostratigraphic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from chronostratigraphy as chronos left join specimens on chronos.id = specimens.chrono_ref ';
      order_by_sql_part := 'order by (chronos.path || chronos.id), chronos.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_listing_lithostratigraphy (IN nbr_records INTEGER, VARIADIC litho_unit_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "supergroup" TEXT,
  "group" TEXT,
  "formation" TEXT,
  "member" TEXT,
  "layer" TEXT,
  "sub_level_1" TEXT,
  "sub_level_2" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of litho_unit ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    litho_unit_id INTEGER;
    reclithostratigraphic_levels RECORD;
  BEGIN
    -- First, test that there's at least one litho_unit to search the hierarchy from
    IF array_length(litho_unit_ids, 1) > 0 THEN
      -- Compose the list of litho_unit ids as comma separated string
      where_first_list_of_ids := array_to_string(litho_unit_ids, ',');
      -- Loop through these litho_unit ids to compose the second part of the sql where clause
      FOREACH litho_unit_id IN ARRAY litho_unit_ids LOOP
        where_second_sql := 'or strpos	(lithos.path, (select ssslithos.path || ssslithos.id
                                                    from lithostratigraphy ssslithos
                                                    where ssslithos.id = ' || litho_unit_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where lithos.id in (select sslithos.id
                                          from lithostratigraphy sslithos
                                          where sslithos.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((lithos.path || lithos.id), lithos.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all lithostratigraphic levels and for each of them, include a select clause
      FOR reclithostratigraphic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'lithostratigraphy' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select sublithos.name
                             from lithostratigraphy as sublithos inner join (select unnest(string_to_array(substring(lithos.path || lithos.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as lithosids
                             on sublithos.id = lithosids.id::integer
                             where lithosids.id != ' || CHR(39) || CHR(39) || '
                               and sublithos.level_ref = ' || reclithostratigraphic_levels.id || '
                            )::text as "' || reclithostratigraphic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from lithostratigraphy as lithos left join specimens on lithos.id = specimens.litho_ref ';
      order_by_sql_part := 'order by (lithos.path || lithos.id), lithos.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_listing_mineralogy (IN nbr_records INTEGER, VARIADIC mineralo_unit_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "unit_class" TEXT,
  "unit_sub_class" TEXT,
  "unit_series" TEXT,
  "unit_variety" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of mineralo_unit ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    mineralo_unit_id INTEGER;
    recmineralogic_levels RECORD;
  BEGIN
    -- First, test that there's at least one mineralo_unit to search the hierarchy from
    IF array_length(mineralo_unit_ids, 1) > 0 THEN
      -- Compose the list of mineralo_unit ids as comma separated string
      where_first_list_of_ids := array_to_string(mineralo_unit_ids, ',');
      -- Loop through these mineralo_unit ids to compose the second part of the sql where clause
      FOREACH mineralo_unit_id IN ARRAY mineralo_unit_ids LOOP
        where_second_sql := 'or strpos	(mineralos.path, (select sssmineralos.path || sssmineralos.id
                                                    from mineralogy sssmineralos
                                                    where sssmineralos.id = ' || mineralo_unit_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where mineralos.id in (select ssmineralos.id
                                          from mineralogy ssmineralos
                                          where ssmineralos.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((mineralos.path || mineralos.id), mineralos.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all mineralogic levels and for each of them, include a select clause
      FOR recmineralogic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'mineralogy' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select submineralos.name
                             from mineralogy as submineralos inner join (select unnest(string_to_array(substring(mineralos.path || mineralos.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as mineralosids
                             on submineralos.id = mineralosids.id::integer
                             where mineralosids.id != ' || CHR(39) || CHR(39) || '
                               and submineralos.level_ref = ' || recmineralogic_levels.id || '
                            )::text as "' || recmineralogic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from mineralogy as mineralos left join specimens on mineralos.id = specimens.mineral_ref ';
      order_by_sql_part := 'order by (mineralos.path || mineralos.id), mineralos.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_listing_lithology (IN nbr_records INTEGER, VARIADIC litholo_unit_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "unit_main_group" TEXT,
  "unit_group" TEXT,
  "unit_sub_group" TEXT,
  "unit_rock" TEXT,
  "unit_main_class" TEXT,
  "unit_class" TEXT,
  "unit_clan" TEXT,
  "unit_category" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of litholo_unit ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    litholo_unit_id INTEGER;
    reclithologic_levels RECORD;
  BEGIN
    -- First, test that there's at least one litholo_unit to search the hierarchy from
    IF array_length(litholo_unit_ids, 1) > 0 THEN
      -- Compose the list of litholo_unit ids as comma separated string
      where_first_list_of_ids := array_to_string(litholo_unit_ids, ',');
      -- Loop through these litholo_unit ids to compose the second part of the sql where clause
      FOREACH litholo_unit_id IN ARRAY litholo_unit_ids LOOP
        where_second_sql := 'or strpos	(litholos.path, (select ssslitholos.path || ssslitholos.id
                                                    from lithology ssslitholos
                                                    where ssslitholos.id = ' || litholo_unit_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where litholos.id in (select sslitholos.id
                                          from lithology sslitholos
                                          where sslitholos.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((litholos.path || litholos.id), litholos.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all lithologic levels and for each of them, include a select clause
      FOR reclithologic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'lithology' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select sublitholos.name
                             from lithology as sublitholos inner join (select unnest(string_to_array(substring(litholos.path || litholos.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as litholosids
                             on sublitholos.id = litholosids.id::integer
                             where litholosids.id != ' || CHR(39) || CHR(39) || '
                               and sublitholos.level_ref = ' || reclithologic_levels.id || '
                            )::text as "' || reclithologic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from lithology as litholos left join specimens on litholos.id = specimens.lithology_ref ';
      order_by_sql_part := 'order by (litholos.path || litholos.id), litholos.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

/* Reporting Functions */
create or replace function fct_report_loans_transporters (loan_id loans.id%TYPE, transporter_side TEXT DEFAULT 'sender', lang TEXT DEFAULT 'en')
  returns
    table
    (
    transport_dispatched_by TEXT,
    transport_transporter_names TEXT,
    transport_track_ids TEXT
    )
AS
  $$
    with
    transporters as (
        select
          case
          when cp.people_type = 'sender' then
            case
              when $3 = 'fr' then
                'prêteur'
              when $3 = 'nl' then
                'lener'
              else
                'loaner'
            end
          else
            case
              when $3 = 'fr' then
                'emprunteur'
              when $3 = 'nl' then
                'lener'
              else
                'borrower'
            end
          end as transport_dispatched_by,
          p.formated_name as transport_transporter_name
        from loans inner join catalogue_people cp
                   on cp.referenced_relation = 'loans'
                      and cp.record_id = loans.id
                      and cp.people_type IN ('sender', 'receiver')
                      and people_sub_type::integer&64 != 0
                   inner join people p on cp.people_ref = p.id
        where loans.id = $1
          and case
                when $2 IN ('sender', 'loaner') then
                  cp.people_type = 'sender'
                when $2 IN ('receiver', 'borrower') then
                  cp.people_type = 'receiver'
              else
                  false
              end
        order by cp.people_type, cp.order_by
    )
    select distinct on (transport_dispatched_by)
      transport_dispatched_by,
      trim(array_to_string(array_agg(transport_transporter_name) OVER (PARTITION BY transport_dispatched_by), ', '), ', ') as transport_transporter_names,
      case
        when transport_dispatched_by = 'loaner' then
          (
            select trim(array_to_string(array_agg(lower_value), ', '), ', ') as tracking_id
            from properties
            where referenced_relation = 'loans'
              and record_id = $1
              and fullToIndex(property_type) = 'trackingid'
              and applies_to_indexed = 'sender'
            group by fullToIndex(property_type)
            limit 1
          )
        else
        (
          select trim(array_to_string(array_agg(lower_value), ', '), ', ') as tracking_id
          from properties
          where referenced_relation = 'loans'
                and record_id = $1
                and fullToIndex(property_type) = 'trackingid'
                and applies_to_indexed = 'receiver'
          group by fullToIndex(property_type)
          limit 1
        )
      end as transport_track_ids
    from transporters;
  $$
language SQL;

create or replace function fct_report_loans_return_to (loan_id loans.id%TYPE, lang TEXT default 'en')
  returns
    TABLE
    (
    return_message TEXT
    )
AS
  $$
  with communications as
  (
      select entry, comm_type, tag
      from collection_maintenance
        inner join people on collection_maintenance.people_ref = people.id
        inner join people_comm on people.id = people_comm.person_user_ref
      where referenced_relation = 'loans'
            and record_id = $1
            and action_observation = 'approval'
            and strpos(tag, 'work') > 0
  )
  select
    case
      when $2 = 'fr' then
        'Veuillez retourner une copie de ce formulaire par FAX au '
      when $2 = 'nl' then
        'Stuur een kopie van dit formulier per fax naar '
      else
        'Return a copy of this form by FAX at '
    end ||
    coalesce((select trim(array_to_string(array_agg(entry), ', '), ', ') from communications where comm_type = 'phone/fax' and strpos(tag, 'fax') > 0), '+32(0)2.627.41.13.') ||
    coalesce((select
                case
                  when $2 = 'fr' then
                    E'\nou par email à '
                  when $2 = 'nl' then
                    E'\nof bij email naar '
                  else
                    E'\nor by email at '
                end
                || trim(array_to_string(array_agg(entry), ', '), ', ') from communications where comm_type = 'e-mail'
             ), ''
            ) as return_message
  $$
language sql;

create or replace function fct_report_loans_maintenances (loan_id loans.id%TYPE, maintenance_type TEXT)
  returns
    table
    (
    maintenance_date TEXT,
    maintenance_people TEXT,
    maintenance_people_functions TEXT
    )
AS
  $$
    with maintenance_people as (
        SELECT
          DISTINCT ON (maintenance_date, formated_name)
          CASE
          WHEN modification_date_time IN ('0001-01-01' :: TIMESTAMP, '2038-12-31' :: TIMESTAMP)
            THEN
              NULL
          ELSE
            TO_CHAR(modification_date_time, 'DD/MM/YYYY')
          END::TEXT                                      AS maintenance_date,
          regexp_replace(formated_name, '\s+', ' ', 'g') AS formated_name,
          case
            when person_user_role = '' then
              '*'
            else
              person_user_role
          end::text AS people_function
        FROM
          collection_maintenance
          INNER JOIN people
            ON collection_maintenance.people_ref = people.id
          LEFT JOIN people_relationships pr
            ON people.id = pr.person_2_ref
               AND pr.relationship_type IN ('works for', 'belongs to')
        WHERE collection_maintenance.referenced_relation = 'loans'
          AND collection_maintenance.record_id = $1
          AND collection_maintenance.action_observation = $2
        ORDER BY
          maintenance_date DESC,
          formated_name,
          pr.activity_date_to DESC,
          pr.activity_date_from DESC,
          case when person_user_role = '' then 'zzz' else person_user_role end::TEXT
    )
    select distinct on (maintenance_date)
      maintenance_date,
      trim(array_to_string(array_agg(formated_name) OVER (PARTITION BY maintenance_date), ', '), ', ') as maintenance_people,
      case
        when trim(array_to_string(array_agg(people_function) OVER (PARTITION BY maintenance_date), ', '), ', ') = '*' then
          null
        else
          trim(array_to_string(array_agg(people_function) OVER (PARTITION BY maintenance_date), ', '), ', ')
      end as maintenance_people
    from maintenance_people
    order by maintenance_date desc;
  $$
language sql;

create or replace function fct_report_loans_addresses (loan_id loans.id%TYPE, target_copy TEXT)
  returns
    table
    (
    people_name text,
    institution_name text,
    address text
    )
AS
  $$
  with
  people_infos as
  (
    select regexp_replace(p.formated_name, '\s+', ' ', 'g') as formated_name,
          regexp_replace(pp.formated_name, '\s+', ' ', 'g') as institution_name,
          case
          when (ppa.entry is not null
                AND trim(ppa.entry) != ''
                AND ppa.locality is not null
                AND trim(ppa.locality) != ''
                AND ppa.country is not null
                AND trim(ppa.country) != ''
          ) then
            ppa.entry ||
            case when (ppa.po_box is not null AND trim(ppa.po_box) != '') then
              ', ' || ppa.po_box
            else
              ''
            end ||
            case when (ppa.extended_address is not null AND trim(ppa.extended_address) != '') then
              E'\n' || ppa.extended_address
            else
              ''
            end ||
            case when (ppa.zip_code is not null AND trim(ppa.zip_code) != '') then
              E'\n' || ppa.zip_code || ' ' || ppa.locality ||
              case when (ppa.region is not null and trim(ppa.region) != '') then
                ' - ' || ppa.region
              else
                ''
              end
            else
              E'\n' || ppa.locality ||
              case when (ppa.region is not null and trim(ppa.region) != '') then
                ' - ' || ppa.region
              else
                ''
              end
            end ||
            E'\n' || ppa.country
          when (pa.entry is not null
                     AND trim(pa.entry) != ''
                     AND pa.locality is not null
                     AND trim(pa.locality) != ''
                     AND pa.country is not null
                     AND trim(pa.country) != ''
          ) then
            pa.entry ||
            case when (pa.po_box is not null AND trim(pa.po_box) != '') then
              ', ' || pa.po_box
            else
              ''
            end ||
            case when (pa.extended_address is not null AND trim(pa.extended_address) != '') then
              E'\n' || pa.extended_address
            else
              ''
            end ||
            case when (pa.zip_code is not null AND trim(pa.zip_code) != '') then
              E'\n' || pa.zip_code || ' ' || pa.locality ||
              case when (pa.region is not null and trim(pa.region) != '') then
                ' - ' || pa.region
              else
                ''
              end
            else
              E'\n' || pa.locality ||
              case when (pa.region is not null and trim(pa.region) != '') then
                ' - ' || pa.region
              else
                ''
              end
            end ||
            E'\n' || pa.country
          else
            null
          end::text as address
    from catalogue_people cp inner join people p on cp.people_ref = p.id
                             left join people_addresses pa on p.id = pa.person_user_ref and strpos(pa.tag, 'work') > 0
                             left join (
                                        people_relationships pr
                                        inner join
                                        people pp on pr.person_1_ref = pp.id and NOT pp.is_physical
                                        inner join people_addresses ppa on pp.id = ppa.person_user_ref
                                       ) on pr.person_2_ref = p.id and pr.relationship_type IN ('works for', 'belongs to')
    where referenced_relation = 'loans'
      and record_id = $1
      and people_type = 'receiver'
      and case when $2 IN ('Responsible copy', 'Copie responsable', 'Verantwoordelijk copie') then
            people_sub_type::integer&2 != 0
          else
            people_sub_type::integer&4 != 0
          end
      and p.is_physical
    order by order_by,(strpos(pa.tag, 'work') > 0),pr.activity_date_from desc
  ),
  institution_address as
  (
    select p.formated_name::text as name,
          case when (pa.entry is not null
                     AND trim(pa.entry) != ''
                     AND pa.locality is not null
                     AND trim(pa.locality) != ''
                     AND pa.country is not null
                     AND trim(pa.country) != ''
                    ) then
              pa.entry ||
              case when (pa.po_box is not null AND trim(pa.po_box) != '') then
                  ', ' || pa.po_box
              else
                  ''
              end ||
              case when (pa.extended_address is not null AND trim(pa.extended_address) != '') then
                  E'\n' || pa.extended_address
              else
                  ''
              end ||
              case when (pa.zip_code is not null AND trim(pa.zip_code) != '') then
                  E'\n' || pa.zip_code || ' ' || pa.locality ||
                  case when (pa.region is not null and trim(pa.region) != '') then
                    ' - ' || pa.region
                  else
                    ''
                  end
              else
                  E'\n' || pa.locality ||
                  case when (pa.region is not null and trim(pa.region) != '') then
                  ' - ' || pa.region
                  else
                  ''
                  end
              end ||
              E'\n' || pa.country
          else
              null
          end as address
    from catalogue_people cp inner join people p on cp.people_ref = p.id
                             left join people_addresses pa on p.id = pa.person_user_ref
    where referenced_relation = 'loans'
      and record_id = $1
      and people_type = 'receiver'
      and case when $2 IN ('Responsible copy', 'Copie responsable', 'Verantwoordelijk copie') then
            people_sub_type::integer&2 != 0
          else
            people_sub_type::integer&4 != 0
          end
      and NOT p.is_physical
    order by order_by
    limit 1
  )
  select
    array_to_string(array(select distinct on (formated_name) formated_name from people_infos),', ') as people_name,
    coalesce(
        (select name from institution_address),
        (select institution_name from people_infos where institution_name is not null limit 1)
    ) as institution_name,
    coalesce(
        (select address from institution_address),
        (select address from people_infos where address is not null limit 1)
    ) as address
$$
language sql;

create or replace function fct_report_loans_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text, targeted_catalogues text, with_addr boolean default false, lang text default 'en')
  returns TABLE (
  target_copy TEXT,
  loan_id loans.id%TYPE,
  loan_name loans.name%TYPE,
  loan_description loans.description%TYPE,
  loan_purposes TEXT,
  loan_conditions TEXT,
  loan_reception_conditions TEXT,
  loan_return_conditions TEXT,
  loan_from_date TEXT,
  loan_to_date TEXT,
  loan_extended_to_date TEXT,
  loan_receiver_name text,
  loan_receiver_institution_name text,
  loan_receiver_address text,
  loan_items_id TEXT,
  loan_items_name loan_items.details%TYPE,
  loan_items_description comments.comment%TYPE,
  loan_items_value insurances.insurance_value%TYPE,
  loan_phantom_id TEXT,
  loan_rbins_phantom_id TEXT
  )
AS
  $$
select vals.val as target_copy,
       loans.id,
       loans.name,
       loans.description,
       (select array_to_string(array_agg(comment), E'\n') from comments where referenced_relation = 'loans' and record_id = $1 and notion_concerned = 'usage') as loan_purposes,
       (select array_to_string(array_agg(comment), E'\n') from comments where referenced_relation = 'loans' and record_id = $1 and notion_concerned = 'state_observation') as loan_conditions,
       (select array_to_string(array_agg(comment), E'\n') from comments where referenced_relation = 'loans' and record_id = $1 and notion_concerned = 'reception_state_observation') as loan_reception_conditions,
       (select array_to_string(array_agg(comment), E'\n') from comments where referenced_relation = 'loans' and record_id = $1 and notion_concerned = 'return_state_observation') as loan_return_conditions,
       to_char(loans.from_date,'DD/MM/YYYY'),
       to_char(loans.to_date,'DD/MM/YYYY'),
       to_char(loans.extended_to_date,'DD/MM/YYYY'),
       case
        when $6 then
          (select people_name from fct_report_loans_addresses($1,vals.val))::text
        else
          ''::text
       end as loan_receiver_name,
       case
        when $6 then
          (select institution_name from fct_report_loans_addresses($1,vals.val))
        else
          ''::text
       end as loan_receiver_institution_name,
       case
        when $6 then
          (select address from fct_report_loans_addresses($1,vals.val))
        else
          ''::text
       end as loan_receiver_address,
       case
        when specimen_ref is null then
          coalesce (
              (
                select
                  case
                    when $7 = 'fr' then
                      'Codes temporaires: '
                    when $7 = 'nl' then
                      'Tijdelijke codes: '
                    else
                      'Temporary codes: '
                  end
                  ||
                  trim(
                       array_to_string(
                           array_agg(
                                       case
                                       when coalesce(code_prefix,'') != '' then
                                         code_prefix || coalesce(code_prefix_separator,'')
                                       else
                                         ''
                                       end ||
                                       coalesce(code,'') ||
                                       case
                                       when coalesce(code_suffix,'') != '' then
                                         coalesce(code_suffix_separator,'') || code_suffix
                                       else
                                         ''
                                       end
                                     ),
                           ', '
                       ),
                       ', '
                  )
                from codes
                where referenced_relation = 'loan_items'
                      and record_id = loan_items.id
                      and code_category = 'main'
                limit 3
              ), '')
        else
          'RBINS ID: ' || specimens.id  ||
          coalesce (
          (
            select E'\nCodes: ' || trim(array_to_string(array_agg(
              case
                when coalesce(code_prefix,'') != '' then
                  code_prefix || coalesce(code_prefix_separator,'')
                else
                  ''
              end ||
              coalesce(code,'') ||
              case
              when coalesce(code_suffix,'') != '' then
                coalesce(code_suffix_separator,'') || code_suffix
              else
                ''
              end
            ), ', '), ', ')
            from codes
            where referenced_relation = 'specimens'
              and record_id = specimens.id
              and code_category = 'main'
            limit 3
          ), '')
       end as loan_items_id,
       case
        when loan_items.specimen_ref is null then
          loan_items.details
        else
           trim(
             CASE
             WHEN 'taxonomy' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(taxon_name, '') != ''
               THEN
                 taxon_name || E'\n'
             ELSE
               E'\n'
             END ||
             CASE
             WHEN 'chronostratigraphy' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(chrono_name, '') != ''
               THEN
                 chrono_name || E'\n'
             ELSE
               E'\n'
             END ||
             CASE
             WHEN 'lithostratigraphy' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(litho_name, '') != ''
               THEN
                 litho_name || E'\n'
             ELSE
               E'\n'
             END ||
             CASE
             WHEN 'lithology' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(lithology_name, '') != ''
               THEN
                 lithology_name || E'\n'
             ELSE
               E'\n'
             END ||
             CASE
             WHEN 'mineralogy' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(mineral_name, '') != ''
               THEN
                 mineral_name || E'\n'
             ELSE
               E'\n'
             END
           ,E'\n')
        end::text as loan_items_name,
        coalesce
        (
           (
             select trim(array_to_string(array_agg(comment), E'\n'), E'\n')
             from comments
             where referenced_relation = 'loan_items'
               and record_id = loan_items.id
               and notion_concerned = 'description'
             limit 3
           )
          ,
           (
             select trim(array_to_string(array_agg(comment), E'\n'), E'\n')
             from comments
             where referenced_relation = 'specimens'
                   and record_id = loan_items.specimen_ref
                   and notion_concerned = 'description'
             limit 3
           )
        ) as loan_items_description,
        coalesce
       (
            (
              select insurance_value
              from insurances
              where referenced_relation = 'loan_items'
                and record_id = loan_items.id
                and insurance_currency = '€'
                order by date_to desc
              limit 1
            )
          ,
            (
              select insurance_value
              from insurances
              where referenced_relation = 'specimens'
                    and record_id = loan_items.specimen_ref
                    and insurance_currency = '€'
              order by date_to desc
              limit 1
            )
        ) as loan_items_value,
       case
        when vals.val IN ('RBINS copy', 'Copie RBINS', 'RBINS copie') then
         loan_items.id::text
        else
         trim(coalesce(to_char(loans.from_date,'YY/MM-'),'') || loans.name || '-' || row_number() over (PARTITION BY vals.val ORDER BY vals.val_index, loans.id, loan_items.id))
       end as loan_phantom_id,
       case
        when vals.val IN ('RBINS copy', 'Copie RBINS', 'RBINS copie') then
          case
            when $7 = 'fr' then
              'ID item prêté: '
            when $7 = 'nl' then
              'ID geleend item: '
            else
              'Loan item ID: '
          end
          ||  loan_items.id || E'\n' ||
          case
            when $7 = 'fr' then
              'ID Fantôme: '
            else
              'Phantom ID: '
          end
          || trim(coalesce(to_char(loans.from_date,'YY/MM-'),'') || loans.name || '-' || row_number() over (PARTITION BY vals.val ORDER BY vals.val_index, loans.id, loan_items.id))
        else
          null::text
       end as loan_rbins_phantom_id
from ( select unnest(array_vals.val) as val, generate_series(1,array_vals.val_index) as val_index
       from (select case when exists ( select 1
                                       from catalogue_people
                                       where referenced_relation = 'loans'
                                         and record_id = $1
                                         and people_type = 'receiver'
                                         and people_sub_type::integer&2 != 0
                                       limit 1
                                     ) then
                      string_to_array(trim($2,'[]'), ', ')
                    else
                      string_to_array(trim($3,'[]'), ', ')
                    end as val,
                    case when exists ( select 1
                                       from catalogue_people
                                       where referenced_relation = 'loans'
                                         and record_id = $1
                                         and people_type = 'receiver'
                                         and people_sub_type::integer&2 != 0
                                       limit 1
                                     ) then
                      array_length(string_to_array(trim($2,'[]'), ', '),1)
                    else
                      array_length(string_to_array(trim($3,'[]'), ', '),1)
                    end as val_index
            ) as array_vals
     ) as vals,
loans
inner join loan_items on loans.id = loan_items.loan_ref
left join specimens on loan_items.specimen_ref = specimens.id
where loans.id = $1
  and exists(select 1
             from catalogue_people
             where referenced_relation = 'loans'
               and record_id = $1
               and people_type = 'receiver'
               and people_sub_type::integer&4 != 0
             limit 1
            )
  and vals.val IN ( select unnest(string_to_array(trim($4,'[]'), ', ')) )
order by vals.val_index,loans.id,row_number() over (PARTITION BY vals.val ORDER BY vals.val_index, loans.id, loan_items.id);
$$
language sql;

CREATE OR REPLACE FUNCTION fct_duplicate_loans (loan_id loans.id%TYPE) RETURNS loans.id%TYPE
  AS
  $$
  DECLARE
    new_loan_id loans.id%TYPE;
    new_loan_item_id loan_items.id%TYPE;
    rec_loan_items RECORD;
  BEGIN
    INSERT INTO loans (name, description)
      (SELECT name, description FROM loans WHERE id = loan_id)
    RETURNING id INTO new_loan_id;
    INSERT INTO loan_rights (loan_ref, user_ref, has_encoding_right)
      (SELECT new_loan_id, user_ref, has_encoding_right from loan_rights where loan_ref = loan_id);
    INSERT INTO catalogue_people (referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
      (
        SELECT referenced_relation, new_loan_id, people_type, people_sub_type, order_by, people_ref
        FROM catalogue_people
        WHERE referenced_relation = 'loans'
          AND record_id = loan_id
      );
    INSERT INTO insurances (referenced_relation,
                            record_id,
                            insurance_value,
                            insurance_currency,
                            insurer_ref,
                            date_from_mask,
                            date_from,
                            date_to_mask,
                            date_to,
                            contact_ref)
      (SELECT
         referenced_relation,
         new_loan_id,
         insurance_value,
         insurance_currency,
         insurer_ref,
         date_from_mask,
         date_from,
         date_to_mask,
         date_to,
         contact_ref
       FROM insurances
        WHERE referenced_relation = 'loans'
          AND record_id = loan_id
      );
    INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
      (SELECT referenced_relation, new_loan_id, notion_concerned, comment from comments where referenced_relation = 'loans' AND record_id = loan_id);
    INSERT INTO properties (
      referenced_relation,
      record_id,
      property_type,
      applies_to,
      date_from_mask,
      date_from,
      date_to_mask,
      date_to,
      is_quantitative,
      property_unit,
      method,
      lower_value,
      upper_value,
      property_accuracy
    )
      (
        SELECT
          referenced_relation,
          new_loan_id,
          property_type,
          applies_to,
          date_from_mask,
          date_from,
          date_to_mask,
          date_to,
          is_quantitative,
          property_unit,
          method,
          lower_value,
          upper_value,
          property_accuracy
        FROM properties
        WHERE referenced_relation = 'loans'
          AND record_id = loan_id
      );
    FOR rec_loan_items IN SELECT id FROM loan_items WHERE loan_ref = loan_id
      LOOP
        INSERT INTO loan_items (loan_ref, ig_ref, specimen_ref, details)
          (SELECT new_loan_id, ig_ref, specimen_ref, details FROM loan_items WHERE id = rec_loan_items.id)
        RETURNING id INTO new_loan_item_id;
        INSERT INTO catalogue_people (referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
          (
            SELECT referenced_relation, new_loan_item_id, people_type, people_sub_type, order_by, people_ref
            FROM catalogue_people
            WHERE referenced_relation = 'loan_items'
                  AND record_id = rec_loan_items.id
          );
        INSERT INTO codes (
          referenced_relation,
          record_id,
          code_category,
          code_prefix,
          code_prefix_separator,
          code,
          code_suffix_separator,
          code_suffix,
          code_date,
          code_date_mask
        )
        (
          SELECT
           referenced_relation,
           new_loan_item_id,
           code_category,
           code_prefix,
           code_prefix_separator,
           code,
           code_suffix_separator,
           code_suffix,
           code_date,
           code_date_mask
          FROM codes
          WHERE referenced_relation = 'loan_items'
            AND record_id = rec_loan_items.id
        );
        INSERT INTO insurances (referenced_relation,
                                record_id,
                                insurance_value,
                                insurance_currency,
                                insurer_ref,
                                date_from_mask,
                                date_from,
                                date_to_mask,
                                date_to,
                                contact_ref)
          (SELECT
             referenced_relation,
             new_loan_item_id,
             insurance_value,
             insurance_currency,
             insurer_ref,
             date_from_mask,
             date_from,
             date_to_mask,
             date_to,
             contact_ref
           FROM insurances
           WHERE referenced_relation = 'loan_items'
                 AND record_id = rec_loan_items.id
          );
        INSERT INTO comments (referenced_relation, record_id, notion_concerned, comment)
          (SELECT referenced_relation, new_loan_item_id, notion_concerned, comment from comments where referenced_relation = 'loan_items' AND record_id = rec_loan_items.id);
        INSERT INTO properties (
          referenced_relation,
          record_id,
          property_type,
          applies_to,
          date_from_mask,
          date_from,
          date_to_mask,
          date_to,
          is_quantitative,
          property_unit,
          method,
          lower_value,
          upper_value,
          property_accuracy
        )
          (
            SELECT
              referenced_relation,
              new_loan_item_id,
              property_type,
              applies_to,
              date_from_mask,
              date_from,
              date_to_mask,
              date_to,
              is_quantitative,
              property_unit,
              method,
              lower_value,
              upper_value,
              property_accuracy
            FROM properties
            WHERE referenced_relation = 'loan_items'
                  AND record_id = rec_loan_items.id
          );
      END LOOP;
    RETURN new_loan_id;
  EXCEPTION
    WHEN OTHERS THEN
      RETURN 0;
  END;
  $$
  LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_update_import()
  RETURNS trigger AS
$BODY$
BEGIN
  if OLD.state IS DISTINCT FROM NEW.state THEN
  UPDATE imports set updated_at= now() where id=NEW.id ;
  END IF ;
  return new ;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

CREATE OR REPLACE FUNCTION fct_clean_staging_catalogue ( importRef staging_catalogue.import_ref%TYPE ) RETURNS BOOLEAN LANGUAGE plpgsql
AS
  $$
  DECLARE
    recDistinctStagingCatalogue RECORD;
  BEGIN
    FOR recDistinctStagingCatalogue IN SELECT DISTINCT ON (level_ref, fullToIndex(name), name)
                                       id, import_ref, name, level_ref
                                       FROM
                                         (
                                           SELECT
                                             id,
                                             import_ref,
                                             name,
                                             level_ref
                                           FROM staging_catalogue
                                           WHERE import_ref = importRef
                                           ORDER BY level_ref, fullToIndex(name), id
                                         ) as subqry
    LOOP
      UPDATE staging_catalogue
      SET parent_ref = recDistinctStagingCatalogue.id
      WHERE
        import_ref = importRef
        AND parent_ref IN
            (
              SELECT id
              FROM staging_catalogue
              WHERE import_ref = importRef
                AND name = recDistinctStagingCatalogue.name
                AND level_ref = recDistinctStagingCatalogue.level_ref
                AND id != recDistinctStagingCatalogue.id
            );
      DELETE FROM staging_catalogue
      WHERE import_ref = importRef
            and name = recDistinctStagingCatalogue.name
            and level_ref = recDistinctStagingCatalogue.level_ref
            and id != recDistinctStagingCatalogue.id;
    END LOOP;
    RETURN TRUE;
  EXCEPTION
    WHEN OTHERS THEN
      RAISE WARNING 'Error:%', SQLERRM;
      RETURN FALSE;
  END;
$$;

-- Statistics functions
DROP TYPE IF EXISTS stats_collections CASCADE;

create type stats_collections as (collection varchar, new_items bigint, updated_items bigint, new_types bigint, updated_types bigint, new_species bigint);

--alter type stats_collections owner to darwin2;
--alter function stats_collections_encoding (collections.id%TYPE, timestamp, timestamp) owner to darwin2;

create or replace function stats_collections_encoding_optimistics (collections.id%TYPE, timestamp, timestamp) returns setof stats_collections language sql immutable as $$
WITH users_statistics AS
(
  WITH users_stats AS
  (
      SELECT DISTINCT
        collection_ref      AS "Collection ID",
        (
          SELECT
        '/'
        ||
        array_to_string(
            array_agg(
                sc.name),
            '/')
        ||
        '/'
        ||
        collection_name
          FROM
            collections AS sc
            INNER JOIN
            (SELECT
               unnest(
                   string_to_array(
                       trim(
                           collection_path,
                           '/'),
                       '/')) :: BIGINT AS id) AS scc
              ON
                sc.id = scc.id
        )                   AS "Collection Path",
        main_ut.action      AS "Action",
        CASE WHEN
          main_s.type
          =
          'specimen'
          THEN 'non type'
        ELSE 'type' END     AS "Type",
        count(*)
        OVER (
          PARTITION BY
            collection_ref,
            action
        )                   AS "Action Count",
        count(*)
        OVER (
          PARTITION BY
            collection_ref,
            action,
            CASE WHEN
              main_s.type = 'specimen'
              THEN 'non type'
            ELSE 'type' END
        )                   AS "Type Count"
      FROM users_tracking AS main_ut
        INNER JOIN specimens AS main_s
          ON main_ut.record_id = main_s.id
             AND main_ut.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
             AND main_ut.referenced_relation = 'specimens'
      WHERE
        CASE
        WHEN 0 != $1
          THEN
            collection_ref IN (SELECT id
                               FROM collections
                               WHERE id = $1 OR path LIKE '%/' || $1 || '/%')
        ELSE
          TRUE
        END
        AND main_ut.action != 'delete'
      ORDER BY "Collection Path", "Action", "Type"
  )
  SELECT
    users_stats."Collection Path",
    users_stats."Action",
    users_stats."Type",
    users_stats."Action Count",
    users_stats."Type Count",
    new_species."New species"
  FROM users_stats
    LEFT JOIN
    (
      SELECT
        s.collection_ref,
        count(DISTINCT tax.id) AS "New species"
        FROM
        (users_tracking AS ut INNER JOIN taxonomy AS tax
            ON ut.referenced_relation = 'taxonomy'
               AND ut.action = 'insert'
               AND ut.record_id = tax.id
               AND tax.level_ref > 47
               AND ut.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
          ) INNER JOIN
        (specimens AS s INNER JOIN users_tracking AS ust
            ON ust.referenced_relation = 'specimens'
               AND ust.action = 'insert'
               AND ust.record_id = s.id
               AND ust.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
          ) ON s.taxon_ref = tax.id
      GROUP BY s.collection_ref
    ) AS new_species
      ON users_stats."Collection ID" = new_species.collection_ref
)
SELECT DISTINCT
  us."Collection Path",
  coalesce (
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
      ),
      0
  ) as "Insertion count",
  coalesce(
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
      ),
      0
  ) as "Update count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
              AND sus."Type" = 'type'
      ),
      0
  ) as "Inserted Type count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
              AND sus."Type" = 'type'
      ),
      0
  ) as "Updated Type count",
  coalesce(us."New species", 0) as "New species"
FROM users_statistics as us
ORDER BY us."Collection Path"
$$;

create or replace function stats_collections_encoding_optimistics (collections.id%TYPE, text, text) returns setof stats_collections language sql immutable as $$
  select * from stats_collections_encoding_optimistics($1, $2::timestamp, $3::timestamp);
$$;

create or replace function stats_collections_encoding (collections.id%TYPE, timestamp, timestamp) returns setof stats_collections language sql immutable as $$
WITH users_statistics AS
(
  WITH users_stats AS
  (
      SELECT DISTINCT
        collection_ref AS "Collection ID",
        (
          SELECT
        '/'
        ||
        array_to_string(
            array_agg(
                sc.name),
            '/')
        ||
        '/'
        ||
        collection_name
          FROM
            collections AS sc
            INNER JOIN
            (SELECT
               unnest(
                   string_to_array(
                       trim(
                           collection_path,
                           '/'),
                       '/')) :: BIGINT AS id) AS scc
              ON
                sc.id = scc.id
        ) AS "Collection Path",
        main_ut.action AS "Action",
        CASE WHEN
          main_s.type
          =
          'specimen'
          THEN 'non type'
        ELSE 'type' END AS "Type",
        main_s.id
      FROM users_tracking AS main_ut
        INNER JOIN specimens AS main_s
          ON main_ut.record_id = main_s.id
             AND main_ut.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
             AND main_ut.referenced_relation = 'specimens'
      WHERE
        CASE
        WHEN 0 != $1
          THEN
            collection_ref IN (SELECT id
                               FROM collections
                               WHERE id = $1 OR path LIKE '%/' || $1 || '/%')
        ELSE
          TRUE
        END
        AND main_ut.action != 'delete'
      ORDER BY "Collection Path", "Action", "Type"
  )
  SELECT DISTINCT
    users_stats."Collection Path",
    users_stats."Action",
    users_stats."Type",
    coalesce(count(*) over (partition by "Collection ID", "Action"),0) as "Action Count",
    coalesce(count(*) over (partition by "Collection ID", "Action", "Type"),0) as "Type Count",
    coalesce(new_species."New species",0) as "New species"
  FROM users_stats
    LEFT JOIN
    (
      SELECT
        s.collection_ref,
        count(DISTINCT tax.id) AS "New species"
      FROM
        (users_tracking AS ut INNER JOIN taxonomy AS tax
            ON ut.referenced_relation = 'taxonomy'
               AND ut.action = 'insert'
               AND ut.record_id = tax.id
               AND tax.level_ref > 47
               AND ut.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
          ) INNER JOIN
        (specimens AS s INNER JOIN users_tracking AS ust
            ON ust.referenced_relation = 'specimens'
               AND ust.action = 'insert'
               AND ust.record_id = s.id
               AND ust.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
          ) ON s.taxon_ref = tax.id
      GROUP BY s.collection_ref
    ) AS new_species
      ON users_stats."Collection ID" = new_species.collection_ref
)
SELECT DISTINCT
  us."Collection Path",
  coalesce (
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
      ),
      0
  ) as "Insertion count",
  coalesce(
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
      ),
      0
  ) as "Update count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
              AND sus."Type" = 'type'
      ),
      0
  ) as "Inserted Type count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
              AND sus."Type" = 'type'
      ),
      0
  ) as "Updated Type count",
  coalesce(us."New species", 0) as "New species"
FROM users_statistics as us
ORDER BY us."Collection Path"
$$;

create or replace function stats_collections_encoding(collections.id%TYPE, text, text) returns setof stats_collections language sql immutable as $$
select * from stats_collections_encoding($1, $2::timestamp, $3::timestamp);
$$;

DROP TYPE IF EXISTS encoders_stats_collections CASCADE;

create type encoders_stats_collections as (encoder TEXT, collection_path TEXT, new_items bigint, updated_items bigint, new_types bigint, updated_types bigint, new_species bigint);
--alter type encoders_stats_collections owner to darwin2;

CREATE OR REPLACE function stats_encoders_encoding_optimistics (top_collection collections.id%TYPE, users_array TEXT, from_date TIMESTAMP, to_date TIMESTAMP)
  RETURNS setof encoders_stats_collections
language SQL as
$$
WITH users_statistics AS
(
  WITH users_stats AS
  (
      SELECT DISTINCT
        users.id            AS "User ID",
        users.formated_name AS "User",
        collection_ref      AS "Collection ID",
        (
          SELECT
        '/'
        ||
        array_to_string(
            array_agg(
                sc.name),
            '/')
        ||
        '/'
        ||
        collection_name
          FROM
            collections AS sc
            INNER JOIN
            (SELECT
               unnest(
                   string_to_array(
                       trim(
                           collection_path,
                           '/'),
                       '/')) :: BIGINT AS id) AS scc
              ON
                sc.id = scc.id
        )                   AS "Collection Path",
        main_ut.action      AS "Action",
        CASE WHEN
          main_s.type
          =
          'specimen'
          THEN 'non type'
        ELSE 'type' END     AS "Type",
        count(*)
        OVER (
          PARTITION BY
            users.id,
            collection_ref,
            action
        )                   AS "Action Count",
        count(*)
        OVER (
          PARTITION BY
            users.id,
            collection_ref,
            action,
            CASE WHEN
              main_s.type = 'specimen'
              THEN 'non type'
            ELSE 'type' END
        )                   AS "Type Count"
      FROM users
        INNER JOIN users_tracking AS main_ut
          ON users.id = main_ut.user_ref
             AND main_ut.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
             AND main_ut.referenced_relation = 'specimens'
        INNER JOIN specimens AS main_s
          ON main_ut.record_id = main_s.id
      WHERE CASE
            WHEN '0' = ( select unnest(string_to_array(trim($2,'[]'), ', ')) limit 1 )
              THEN
                TRUE
            ELSE
              users.id::text IN ( select unnest(string_to_array(trim($2,'[]'), ', ')) )
            END
            AND
            CASE
            WHEN 0 != $1
              THEN
                collection_ref IN (SELECT id
                                   FROM collections
                                   WHERE id = $1 OR path LIKE '%/' || $1 || '/%')
            ELSE
              TRUE
            END
            AND main_ut.action != 'delete'
      ORDER BY "User", "Collection Path", "Action", "Type"
  )
  SELECT
    users_stats."User",
    users_stats."Collection Path",
    users_stats."Action",
    users_stats."Type",
    users_stats."Action Count",
    users_stats."Type Count",
    new_species."New species encoded by the encoder used in this collection"
  FROM users_stats
    LEFT JOIN
    (
      SELECT
        s.collection_ref,
        ut.user_ref            AS "User ID",
        count(DISTINCT tax.id) AS "New species encoded by the encoder used in this collection"
      FROM
        (users_tracking AS ut INNER JOIN taxonomy AS tax
            ON ut.referenced_relation = 'taxonomy'
               AND ut.action = 'insert'
               AND ut.record_id = tax.id
               AND tax.level_ref > 47
               AND ut.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
          ) INNER JOIN
        (specimens AS s INNER JOIN users_tracking AS ust
            ON ust.referenced_relation = 'specimens'
               AND ust.action = 'insert'
               AND ust.record_id = s.id
               AND ust.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
          ) ON s.taxon_ref = tax.id
      GROUP BY s.collection_ref, ut.user_ref
    ) AS new_species
      ON users_stats."Collection ID" = new_species.collection_ref
         AND users_stats."User ID" = new_species."User ID"
)
SELECT DISTINCT
  us."User", us."Collection Path",
  coalesce (
    (
      SELECT DISTINCT "Action Count"
      FROM users_statistics as sus
      WHERE sus."User" = us."User"
            AND sus."Collection Path" = us."Collection Path"
            AND sus."Action" = 'insert'
    ),0
  ) as "Insertion count",
  coalesce(
    (
      SELECT DISTINCT "Action Count"
      FROM users_statistics as sus
      WHERE sus."User" = us."User"
            AND sus."Collection Path" = us."Collection Path"
            AND sus."Action" = 'update'
    ),0
  ) as "Update count",
  coalesce (
    (
      SELECT DISTINCT "Type Count"
      FROM users_statistics as sus
      WHERE sus."User" = us."User"
            AND sus."Collection Path" = us."Collection Path"
            AND sus."Action" = 'insert'
            AND sus."Type" = 'type'
    ),0
  ) as "Inserted Type count",
  coalesce(
    (
      SELECT DISTINCT "Type Count"
      FROM users_statistics as sus
      WHERE sus."User" = us."User"
            AND sus."Collection Path" = us."Collection Path"
            AND sus."Action" = 'update'
            AND sus."Type" = 'type'
    ),0
  ) as "Update Type count",
  coalesce(us."New species encoded by the encoder used in this collection",0) as "New species encoded by the encoder used in this collection"
FROM users_statistics as us
ORDER BY us."User", us."Collection Path"
$$;

CREATE OR REPLACE function stats_encoders_encoding_optimistics (top_collection collections.id%TYPE, users_array TEXT, from_date TEXT, to_date TEXT)
  RETURNS setof encoders_stats_collections
language SQL as
  $$
    SELECT * FROM stats_encoders_encoding_optimistics ($1, $2, $3::timestamp, $4::timestamp);
  $$;

CREATE OR REPLACE function stats_encoders_encoding (top_collection collections.id%TYPE, users_array TEXT, from_date TIMESTAMP, to_date TIMESTAMP)
  RETURNS setof encoders_stats_collections
language SQL as
$$
WITH users_statistics AS
(
  WITH users_stats AS
  (
      SELECT DISTINCT
        users.id            AS "User ID",
        users.formated_name AS "User",
        collection_ref      AS "Collection ID",
        (
          SELECT
        '/'
        ||
        array_to_string(
            array_agg(
                sc.name),
            '/')
        ||
        '/'
        ||
        collection_name
          FROM
            collections AS sc
            INNER JOIN
            (SELECT
               unnest(
                   string_to_array(
                       trim(
                           collection_path,
                           '/'),
                       '/')) :: BIGINT AS id) AS scc
              ON
                sc.id = scc.id
        )                   AS "Collection Path",
        main_ut.action      AS "Action",
        CASE WHEN
          main_s.type
          =
          'specimen'
          THEN 'non type'
        ELSE 'type' END     AS "Type",
        main_s.id
      FROM users
        INNER JOIN users_tracking AS main_ut
          ON users.id = main_ut.user_ref
             AND main_ut.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
             AND main_ut.referenced_relation = 'specimens'
        INNER JOIN specimens AS main_s
          ON main_ut.record_id = main_s.id
      WHERE CASE
            WHEN '0' = ( select unnest(string_to_array(trim($2,'[]'), ', ')) limit 1 )
              THEN
                TRUE
            ELSE
              users.id::text IN ( select unnest(string_to_array(trim($2,'[]'), ', ')) )
            END
            AND
            CASE
            WHEN 0 != $1
              THEN
                collection_ref IN (SELECT id
                                   FROM collections
                                   WHERE id = $1 OR path LIKE '%/' || $1 || '/%')
            ELSE
              TRUE
            END
            AND main_ut.action != 'delete'
      ORDER BY "User", "Collection Path", "Action", "Type"
  )
  SELECT DISTINCT
    users_stats."User",
    users_stats."Collection Path",
    users_stats."Action",
    users_stats."Type",
    coalesce(count(*) over (partition by users_stats."User ID", "Collection ID", "Action"),0) as "Action Count",
    coalesce(count(*) over (partition by users_stats."User ID", "Collection ID", "Action", "Type"),0) as "Type Count",
    coalesce(new_species."New species encoded by the encoder used in this collection",0) as "New species encoded by the encoder used in this collection"
  FROM users_stats
    LEFT JOIN
    (
      SELECT
        s.collection_ref,
        ut.user_ref            AS "User ID",
        count(DISTINCT tax.id) AS "New species encoded by the encoder used in this collection"
      FROM
        (users_tracking AS ut INNER JOIN taxonomy AS tax
            ON ut.referenced_relation = 'taxonomy'
               AND ut.action = 'insert'
               AND ut.record_id = tax.id
               AND tax.level_ref > 47
               AND ut.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
          ) INNER JOIN
        (specimens AS s INNER JOIN users_tracking AS ust
            ON ust.referenced_relation = 'specimens'
               AND ust.action = 'insert'
               AND ust.record_id = s.id
               AND ust.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
          ) ON s.taxon_ref = tax.id
      GROUP BY s.collection_ref, ut.user_ref
    ) AS new_species
      ON users_stats."Collection ID" = new_species.collection_ref
         AND users_stats."User ID" = new_species."User ID"
)
SELECT DISTINCT
  us."User", us."Collection Path",
  coalesce (
    (
      SELECT DISTINCT "Action Count"
      FROM users_statistics as sus
      WHERE sus."User" = us."User"
            AND sus."Collection Path" = us."Collection Path"
            AND sus."Action" = 'insert'
    ),0
  ) as "Insertion count",
  coalesce (
    (
      SELECT DISTINCT "Action Count"
      FROM users_statistics as sus
      WHERE sus."User" = us."User"
            AND sus."Collection Path" = us."Collection Path"
            AND sus."Action" = 'update'
    ),0
  ) as "Update count",
  coalesce (
    (
      SELECT DISTINCT "Type Count"
      FROM users_statistics as sus
      WHERE sus."User" = us."User"
            AND sus."Collection Path" = us."Collection Path"
            AND sus."Action" = 'insert'
            AND sus."Type" = 'type'
    ),0
  ) as "Inserted Type count",
  coalesce (
    (
      SELECT DISTINCT "Type Count"
      FROM users_statistics as sus
      WHERE sus."User" = us."User"
            AND sus."Collection Path" = us."Collection Path"
            AND sus."Action" = 'update'
            AND sus."Type" = 'type'
    ),0
  ) as "Update Type count",
  coalesce(us."New species encoded by the encoder used in this collection",0) as "New species encoded by the encoder used in this collection"
FROM users_statistics as us
ORDER BY us."User", us."Collection Path"
$$;

CREATE OR REPLACE function stats_encoders_encoding (top_collection collections.id%TYPE, users_array TEXT, from_date TEXT, to_date TEXT)
  RETURNS setof encoders_stats_collections
language SQL as
$$
SELECT * FROM stats_encoders_encoding ($1, $2, $3::timestamp, $4::timestamp);
$$;

CREATE OR REPLACE FUNCTION fct_catalogue_import_keywords_update()
  RETURNS TRIGGER LANGUAGE plpgsql AS
$$
  BEGIN
    IF TG_TABLE_NAME = 'staging_catalogue' THEN
      IF TG_OP IN ('INSERT', 'UPDATE') THEN
        IF COALESCE(NEW.catalogue_ref,0) != 0 AND COALESCE(NEW.level_ref,0) != 0 THEN
          UPDATE classification_keywords as mck
            SET
              referenced_relation = (
                SELECT level_type
                FROM catalogue_levels
                WHERE id = NEW.level_ref
              ),
              record_id = NEW.catalogue_ref
          WHERE mck.referenced_relation = TG_TABLE_NAME
            AND mck.record_id = NEW.id
            AND NOT EXISTS (
              SELECT 1
              FROM classification_keywords as sck
              WHERE sck.referenced_relation = (
                  SELECT level_type
                  FROM catalogue_levels
                  WHERE id = NEW.level_ref
                )
                AND sck.record_id = NEW.catalogue_ref
                AND sck.keyword_type = mck.keyword_type
                AND sck.keyword_indexed = mck.keyword_indexed
              );
        END IF;
        RETURN NEW;
      ELSE
        DELETE FROM classification_keywords
        WHERE referenced_relation = 'staging_catalogue'
              AND record_id = OLD.id;
        RETURN NULL;
      END IF;
    ELSEIF TG_TABLE_NAME = 'staging' THEN
      IF TG_OP IN ('INSERT', 'UPDATE') THEN
        IF COALESCE(NEW.taxon_ref,0) != 0 AND COALESCE(NEW.taxon_level_ref,0) != 0 AND NEW.taxon_ref != OLD.taxon_ref THEN
          UPDATE classification_keywords as mck
          SET
            referenced_relation = 'taxonomy',
            record_id = NEW.taxon_ref
          WHERE mck.referenced_relation = TG_TABLE_NAME
                AND mck.record_id = NEW.id
                AND mck.keyword_type IN (
                                          'GenusOrMonomial',
                                          'Subgenus',
                                          'SpeciesEpithet',
                                          'FirstEpiteth',
                                          'SubspeciesEpithet',
                                          'InfraspecificEpithet',
                                          'AuthorTeamAndYear',
                                          'AuthorTeam',
                                          'AuthorTeamOriginalAndYear',
                                          'AuthorTeamParenthesisAndYear',
                                          'SubgenusAuthorAndYear',
                                          'CultivarGroupName',
                                          'CultivarName',
                                          'Breed',
                                          'CombinationAuthorTeamAndYear',
                                          'NamedIndividual'
                                        )
                AND NOT EXISTS (
                                SELECT 1
                                FROM classification_keywords as sck
                                WHERE sck.referenced_relation = 'taxonomy'
                                      AND sck.record_id = NEW.taxon_ref
                                      AND sck.keyword_type = mck.keyword_type
                                      AND sck.keyword_indexed = mck.keyword_indexed
          );
        ELSEIF COALESCE(NEW.mineral_ref,0) != 0 AND COALESCE(NEW.mineral_level_ref,0) != 0 AND NEW.mineral_ref != OLD.mineral_ref THEN
          UPDATE classification_keywords as mck
          SET
            referenced_relation = 'mineralogy',
            record_id = NEW.mineral_ref
          WHERE mck.referenced_relation = TG_TABLE_NAME
                AND mck.record_id = NEW.id
                AND mck.keyword_type IN (
                                          'AuthorTeamAndYear',
                                          'AuthorTeam',
                                          'NamedIndividual'
                                        )
                AND NOT EXISTS (
                                SELECT 1
                                FROM classification_keywords as sck
                                WHERE sck.referenced_relation = 'mineralogy'
                                      AND sck.record_id = NEW.mineral_ref
                                      AND sck.keyword_type = mck.keyword_type
                                      AND sck.keyword_indexed = mck.keyword_indexed
          );
        END IF;
        RETURN NEW;
      ELSE
        DELETE FROM classification_keywords
        WHERE referenced_relation = 'staging'
              AND record_id = OLD.id;
        RETURN NULL;
      END IF;
    END IF;
  END;
$$;
