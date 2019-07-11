set search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION fct_cpy_unified_values()
  RETURNS trigger AS
$BODY$
DECLARE
  property_line properties%ROWTYPE;
BEGIN
  NEW.lower_value_unified = convert_to_unified(NEW.lower_value, NEW.property_unit);
  NEW.upper_value_unified = convert_to_unified(CASE WHEN NEW.upper_value = '' THEN NEW.lower_value ELSE NEW.upper_value END, NEW.property_unit);
  RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;
ALTER FUNCTION fct_cpy_unified_values()
OWNER TO darwin2;

DELETE FROM properties
WHERE referenced_relation = 'staging'
      AND  NOT EXISTS (SELECT 1 FROM staging WHERE id = record_id);

UPDATE properties
SET lower_value_unified = convert_to_unified(lower_value, property_unit)
WHERE lower_value != ''
      AND property_unit != ''
      AND convert_to_unified(lower_value, property_unit) IS NOT NULL
      AND lower_value_unified != convert_to_unified(lower_value, property_unit);

UPDATE properties
SET upper_value_unified = convert_to_unified(upper_value, property_unit)
WHERE upper_value != ''
      AND property_unit != ''
      AND convert_to_unified(upper_value, property_unit) IS NOT NULL
      AND upper_value_unified != convert_to_unified(upper_value, property_unit);

COMMIT;