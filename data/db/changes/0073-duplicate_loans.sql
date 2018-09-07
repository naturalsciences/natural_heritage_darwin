SET search_path=darwin2,public;

BEGIN;

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

COMMIT;
