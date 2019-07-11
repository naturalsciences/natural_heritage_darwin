SET search_path = darwin2, public;

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

\i reports/ticketing/create_labeling_view.sql

