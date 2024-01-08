SET search_path=darwin2, public;

INSERT INTO possible_upper_levels (SELECT 1, NULL WHERE NOT EXISTS (SELECT 1 FROM possible_upper_levels WHERE level_ref = 1 and level_upper_ref IS NULL));
