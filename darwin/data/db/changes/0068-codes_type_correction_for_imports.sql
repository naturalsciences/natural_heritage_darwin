set search_path=darwin2,public;

BEGIN;

DROP VIEW IF EXISTS public.labeling;

alter table codes alter column code_num TYPE BIGINT;
alter table collections alter column code_last_value TYPE BIGINT;

CREATE OR REPLACE FUNCTION check_auto_increment_code_in_spec() RETURNS trigger
AS $$
DECLARE
  col collections%ROWTYPE;
  number BIGINT ;
BEGIN
  IF TG_OP != 'DELETE' THEN
    IF NEW.referenced_relation = 'specimens' THEN
      SELECT c.* INTO col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=NEW.record_id;
      IF FOUND THEN
        IF NEW.code_category = 'main' THEN
          IF isnumeric(NEW.code) THEN
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
            IF isnumeric(OLD.code) THEN
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
      IF FOUND AND isnumeric(OLD.code) THEN
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
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_cpy_fulltoindex()
  RETURNS trigger AS
  $BODY$
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
$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;
ALTER FUNCTION fct_cpy_fulltoindex()
OWNER TO darwin2;

DROP FUNCTION if exists lineToTagRowsFormatConserved(text) CASCADE;
drop function if exists labeling_country_for_indexation(gtu.id%TYPE) CASCADE;
drop function if exists labeling_country_for_indexation_array(gtu.id%TYPE) CASCADE;
drop function if exists labeling_province_for_indexation(gtu.id%TYPE) CASCADE;
drop function if exists labeling_province_for_indexation_array(gtu.id%TYPE) CASCADE;
drop function if exists labeling_other_gtu_for_indexation(gtu.id%TYPE) CASCADE;
drop function if exists labeling_other_gtu_for_indexation_array(gtu.id%TYPE) CASCADE;
DROP FUNCTION IF EXISTS labeling_code_num_for_indexation(specimens.id%TYPE) CASCADE;
drop function if exists labeling_sex_for_indexation(specimens.sex%TYPE) CASCADE;
drop function if exists labeling_stage_for_indexation(specimens.stage%TYPE) CASCADE;

DROP INDEX IF EXISTS idx_labeling_country;
DROP INDEX IF EXISTS idx_labeling_province;
DROP INDEX IF EXISTS idx_labeling_other_gtu;
DROP INDEX IF EXISTS idx_labeling_code;
DROP INDEX IF EXISTS idx_labeling_code_varchar;
DROP INDEX IF EXISTS idx_labeling_code_numeric;
DROP INDEX IF EXISTS idx_labeling_type;
DROP INDEX IF EXISTS idx_labeling_sex;
DROP INDEX IF EXISTS idx_labeling_stage;
DROP INDEX IF EXISTS idx_labeling_part;
DROP INDEX IF EXISTS idx_labeling_ig_num_numeric;
DROP INDEX IF EXISTS idx_labeling_ig_num_coalesced;

CREATE INDEX idx_labeling_province ON specimens USING gin (gtu_province_tag_indexed);

CREATE INDEX idx_labeling_other_gtu ON specimens USING gin (gtu_others_tag_indexed);

create or replace function labeling_code_for_indexation(in part_ref specimens.id%TYPE) returns varchar[] language SQL IMMUTABLE as
  $$
select array_agg(coding)
from (select trim(coalesce(code_prefix, '') || coalesce(code_prefix_separator, '') || coalesce(code, '') || coalesce(code_suffix_separator, '') || coalesce(code_suffix, ''))::varchar as coding
      from codes
      where referenced_relation = 'specimens'
        and record_id = $1
        and code_category = 'main'
        and coalesce(upper(code_prefix),'') != 'RBINS'
     ) as x;
$$;

GRANT EXECUTE ON FUNCTION labeling_code_for_indexation(specimens.id%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_code_for_indexation(specimens.id%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_code_for_indexation(specimens.id%TYPE) OWNER TO darwin2;

CREATE INDEX idx_labeling_code ON specimens USING gin (labeling_code_for_indexation(id));

create or replace function labeling_type_for_indexation(in individual_type specimens.type%TYPE) returns varchar[] language SQL IMMUTABLE as
  $$
SELECT array[coalesce(fullToIndex($1),'-')];
$$;

create or replace function labeling_part_for_indexation(in part specimens.specimen_part%TYPE) returns varchar[] language SQL IMMUTABLE as
  $$
SELECT array[coalesce(fullToIndex($1),'-')];
$$;

GRANT EXECUTE ON FUNCTION labeling_type_for_indexation(specimens.type%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_type_for_indexation(specimens.type%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_type_for_indexation(specimens.type%TYPE) OWNER TO darwin2;
GRANT EXECUTE ON FUNCTION labeling_part_for_indexation(specimens.specimen_part%TYPE) TO d2viewer;
GRANT ALL ON FUNCTION labeling_part_for_indexation(specimens.specimen_part%TYPE) TO cebmpad, darwin2;
ALTER FUNCTION labeling_part_for_indexation(specimens.specimen_part%TYPE) OWNER TO darwin2;

CREATE INDEX idx_labeling_type ON specimens using gin (labeling_type_for_indexation("type"));
CREATE INDEX idx_labeling_part ON specimens using gin (labeling_part_for_indexation(specimen_part));

CREATE INDEX idx_labeling_ig_num_numeric ON specimens(convert_to_integer(coalesce(ig_num, '-')));

CREATE OR REPLACE VIEW public.labeling AS
  SELECT df.id AS unique_id, df.collection_ref AS collection, df.collection_name, df.collection_path,
    CASE
    WHEN COALESCE(df.specimen_part, ''::character varying)::text = ANY (ARRAY['specimen'::character varying::text, 'animal'::character varying::text, 'undefined'::character varying::text, 'unknown'::character varying::text, ''::character varying::text]) THEN ''::character varying
    ELSE df.specimen_part
    END AS part_item,
    CASE
    WHEN df.sex::text = ANY (ARRAY['undefined'::character varying::text, 'unknown'::character varying::text, 'not stated'::character varying::text, 'non applicable'::character varying::text]) THEN ''::text
    ELSE df.sex::text ||
         CASE
         WHEN df.state::text = 'not applicable'::text THEN ''::text
         ELSE ' '::text || df.state::text
         END
    END AS part_sex_state,
    CASE
    WHEN df.type::text = 'specimen'::text THEN ''::character varying
    ELSE df.type
    END AS part_type,
    CASE
    WHEN df.stage::text = ANY (ARRAY['undefined'::character varying::text, 'unknown'::character varying::text, 'not stated'::character varying::text]) THEN ''::character varying
    ELSE df.stage
    END AS part_stage,
    CASE
    WHEN df.sub_container IS NULL THEN
      CASE
      WHEN COALESCE(df.container_storage, ''::character varying)::text = ANY (ARRAY['unknown'::character varying::text, '/'::character varying::text, ''::character varying::text]) THEN ''::character varying
      ELSE df.container_storage
      END
    ELSE ''::character varying
    END AS part_container_storage,
    CASE
    WHEN COALESCE(df.sub_container_storage, ''::character varying)::text = ANY (ARRAY['unknown'::character varying::text, '/'::character varying::text, ''::character varying::text]) THEN ''::character varying
    ELSE COALESCE(df.sub_container_storage, ''::character varying)
    END AS part_sub_container_storage, ARRAY[fulltoindex(df.specimen_part)] AS part, ARRAY[fulltoindex(df.type)] AS type, df.sex, df.stage, array_to_string(labeling_code_for_indexation(df.id), ';'::text)::character varying AS code, ( SELECT codes.code_num
                                                                                                                                                                                                                                          FROM codes
                                                                                                                                                                                                                                          WHERE codes.referenced_relation::text = 'specimens'::text AND codes.record_id = df.id AND codes.code_category::text = 'main'::text AND COALESCE(upper(codes.code_prefix::text), ''::text) <> 'RBINS'::text AND codes.code_num IS NOT NULL
                                                                                                                                                                                                                                          LIMIT 1) AS code_num, labeling_code_for_indexation(df.id) AS code_array, df.taxon_ref, df.taxon_name, df.taxon_name_indexed, df.taxon_path, ( SELECT phyl.name
                                                                                                                                                                                                                                                                                                                                                                                        FROM ( SELECT x.id::integer AS id
                                                                                                                                                                                                                                                                                                                                                                                               FROM ( SELECT regexp_split_to_table(taxphyls.path::text, '/'::text) AS id
                                                                                                                                                                                                                                                                                                                                                                                                      FROM taxonomy taxphyls
                                                                                                                                                                                                                                                                                                                                                                                                      WHERE taxphyls.id = df.taxon_ref) x
                                                                                                                                                                                                                                                                                                                                                                                               WHERE x.id <> ''::text) y
                                                                                                                                                                                                                                                                                                                                                                                          JOIN taxonomy phyl ON y.id = phyl.id AND phyl.level_ref = 4) AS phyl, ( SELECT clas.name
                                                                                                                                                                                                                                                                                                                                                                                                                                                                  FROM ( SELECT x.id::integer AS id
                                                                                                                                                                                                                                                                                                                                                                                                                                                                         FROM ( SELECT regexp_split_to_table(taxclass.path::text, '/'::text) AS id
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                FROM taxonomy taxclass
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                WHERE taxclass.id = df.taxon_ref) x
                                                                                                                                                                                                                                                                                                                                                                                                                                                                         WHERE x.id <> ''::text) y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    JOIN taxonomy clas ON y.id = clas.id AND clas.level_ref = 12) AS clas, ( SELECT ordo.name
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             FROM ( SELECT x.id::integer AS id
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    FROM ( SELECT regexp_split_to_table(taxord.path::text, '/'::text) AS id
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           FROM taxonomy taxord
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           WHERE taxord.id = df.taxon_ref) x
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    WHERE x.id <> ''::text) y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               JOIN taxonomy ordo ON y.id = ordo.id AND ordo.level_ref = 28) AS ordo, ( SELECT fam.name
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        FROM ( SELECT x.id::integer AS id
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               FROM ( SELECT regexp_split_to_table(taxfam.path::text, '/'::text) AS id
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      FROM taxonomy taxfam
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      WHERE taxfam.id = df.taxon_ref) x
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               WHERE x.id <> ''::text) y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          JOIN taxonomy fam ON y.id = fam.id AND fam.level_ref = 34) AS family, (( SELECT array_to_string(array_agg(ct.name), ' - '::text) AS array_to_string
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   FROM taxonomy ct
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     JOIN classification_synonymies cs ON cs.referenced_relation::text = 'taxonomy'::text AND cs.record_id = ct.id AND cs.is_basionym = true
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   WHERE cs.group_id = (( SELECT classification_synonymies.group_id
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          FROM classification_synonymies
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          WHERE classification_synonymies.referenced_relation::text = 'taxonomy'::text AND classification_synonymies.record_id = df.taxon_ref AND classification_synonymies.group_name::text = 'rename'::text))))::character varying AS current_name,
    CASE
    WHEN df.acquisition_category IS NOT NULL AND btrim(df.acquisition_category::text) <> ''::text THEN df.acquisition_category
    ELSE ''::character varying
    END AS acquisition_category, df.gtu_ref, df.gtu_country_tag_value AS countries, df.gtu_country_tag_indexed AS countries_array, df.gtu_province_tag_value AS provinces, df.gtu_province_tag_indexed AS provinces_array, df.gtu_others_tag_value AS location, df.gtu_others_tag_indexed AS location_array,
    CASE
    WHEN btrim(df.gtu_code::text) = ANY (ARRAY[''::text, '/'::text, '0'::text, '0/'::text]) THEN ''::text
    ELSE btrim(df.gtu_code::text)
    END AS location_code,
    CASE
    WHEN df.gtu_from_date_mask >= 32 THEN to_char(df.gtu_from_date, 'DD/MM/YYYY'::text)
    ELSE ''::text
    END ||
    CASE
    WHEN df.gtu_to_date_mask >= 32 THEN ' - '::text || to_char(df.gtu_to_date, 'DD/MM/YYYY'::text)
    ELSE ''::text
    END AS gtu_date,
    CASE
    WHEN df.gtu_location IS NOT NULL THEN (trunc(df.gtu_location[0]::numeric, 6) || '/'::text) || trunc(df.gtu_location[1]::numeric, 6)
    ELSE ''::text
    END AS lat_long,
    CASE
    WHEN df.gtu_elevation IS NOT NULL THEN (trunc(df.gtu_elevation::numeric, 2) || ' m'::text) ||
                                           CASE
                                           WHEN df.gtu_elevation_accuracy IS NOT NULL THEN (' +- '::text || trunc(df.gtu_elevation_accuracy::numeric, 2)) || ' m'::text
                                           ELSE ''::text
                                           END
    ELSE ''::text
    END AS elevation, (( SELECT
                           CASE
                           WHEN length(regexp_replace(y.coll, '[^,]+'::text, ''::text, 'g'::text)) > 2 THEN substr(y.coll, 1, strpos(y.coll, ','::text) - 1) || ' & al.'::text
                           ELSE y.coll
                           END AS coll
                         FROM ( SELECT array_to_string(array_agg(x.people_list), ', '::text) AS coll
                                FROM ( SELECT DISTINCT btrim(peo.family_name::text) AS people_list, cp.order_by
                                       FROM catalogue_people cp
                                         JOIN people peo ON cp.people_ref = peo.id
                                       WHERE cp.people_type::text = 'collector'::text AND cp.referenced_relation::text = 'specimens'::text AND cp.record_id = df.id AND (peo.family_name::text <> ALL (ARRAY['Unknown'::character varying::text, '/'::character varying::text]))
                                       ORDER BY cp.order_by) x) y))::character varying AS collectors, (( SELECT
                                                                                                           CASE
                                                                                                           WHEN length(regexp_replace(y.identi, '[^,]+'::text, ''::text, 'g'::text)) > 2 THEN substr(y.identi, 1, strpos(y.identi, ','::text) - 1) || ' & al.'::text
                                                                                                           ELSE y.identi
                                                                                                           END || y.identi_year
                                                                                                         FROM ( SELECT array_to_string(array_agg(x.people_list), ', '::text) AS identi,
                                                                                                                       CASE
                                                                                                                       WHEN max(x.ident_date) IS NOT NULL THEN ', '::text || max(x.ident_date)
                                                                                                                       ELSE ''::text
                                                                                                                       END AS identi_year
                                                                                                                FROM ( SELECT DISTINCT btrim(peo.family_name::text) AS people_list, cp.order_by,
                                                                                                                                       CASE
                                                                                                                                       WHEN ident.notion_date_mask <> 0 THEN date_part('year'::text, ident.notion_date)
                                                                                                                                       ELSE NULL::double precision
                                                                                                                                       END AS ident_date
                                                                                                                       FROM catalogue_people cp
                                                                                                                         JOIN people peo ON cp.people_ref = peo.id
                                                                                                                         JOIN identifications ident ON cp.record_id = ident.id AND cp.referenced_relation::text = 'identifications'::text AND cp.people_type::text = 'identifier'::text
                                                                                                                       WHERE ident.referenced_relation::text = 'specimens'::text AND ident.record_id = df.id AND (peo.family_name::text <> ALL (ARRAY['Unknown'::character varying::text, '/'::character varying::text])) AND ident.notion_date = (( SELECT max(idt.notion_date) AS max
                                                                                                                                                                                                                                                                                                                                                     FROM identifications idt
                                                                                                                                                                                                                                                                                                                                                     WHERE idt.referenced_relation::text = ident.referenced_relation::text AND idt.record_id = ident.record_id))
                                                                                                                       ORDER BY cp.order_by) x) y))::character varying AS identifiers, ''::character varying AS part_identifiers, (( SELECT
                                                                                                                                                                                                                                       CASE
                                                                                                                                                                                                                                       WHEN length(regexp_replace(y.coll, '[^,]+'::text, ''::text, 'g'::text)) > 2 THEN substr(y.coll, 1, strpos(y.coll, ','::text) - 1) || ' & al.'::text
                                                                                                                                                                                                                                       ELSE y.coll
                                                                                                                                                                                                                                       END AS coll
                                                                                                                                                                                                                                     FROM ( SELECT array_to_string(array_agg(x.people_list), ', '::text) AS coll
                                                                                                                                                                                                                                            FROM ( SELECT DISTINCT btrim(peo.family_name::text) AS people_list, cp.order_by
                                                                                                                                                                                                                                                   FROM catalogue_people cp
                                                                                                                                                                                                                                                     JOIN people peo ON cp.people_ref = peo.id
                                                                                                                                                                                                                                                   WHERE cp.people_type::text = 'donator'::text AND cp.referenced_relation::text = 'specimens'::text AND cp.record_id = df.id AND (peo.family_name::text <> ALL (ARRAY['Unknown'::character varying::text, '/'::character varying::text]))
                                                                                                                                                                                                                                                   ORDER BY cp.order_by) x) y))::character varying AS donators, COALESCE(df.ig_num, '-'::character varying) AS ig_num, df.ig_num_indexed, convert_to_integer(COALESCE(df.ig_num, '-'::character varying)) AS ig_numeric,
    CASE
    WHEN df.specimen_count_min <> df.specimen_count_max AND df.specimen_count_min IS NOT NULL AND df.specimen_count_max IS NOT NULL THEN (df.specimen_count_min || ' - '::text) || df.specimen_count_max
    ELSE
      CASE
      WHEN df.specimen_count_min IS NOT NULL THEN df.specimen_count_min::text
      ELSE ''::text
      END
    END AS specimen_number, df.specimen_count_max AS specimen_number_max, df.room AS part_room, df."row" AS part_row, df.shelf AS part_shelf, df.container AS part_container, df.sub_container AS part_sub_container,
    CASE
    WHEN (EXISTS ( SELECT 1
                   FROM comments
                   WHERE comments.referenced_relation::text = 'specimens'::text AND comments.record_id = df.id)) THEN 'Y'::text
    ELSE 'N'::text
    END AS comments
  FROM specimens df;

ALTER TABLE public.labeling
OWNER TO darwin2;
GRANT ALL ON TABLE public.labeling TO darwin2;
GRANT SELECT ON TABLE public.labeling TO d2viewer;

commit;
