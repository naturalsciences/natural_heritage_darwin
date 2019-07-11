alter table codes drop constraint unq_codes;
alter table codes add constraint unq_codes unique (referenced_relation, record_id, full_code_order_by,code_category);
