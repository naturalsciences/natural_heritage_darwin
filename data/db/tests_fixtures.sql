
insert into people (id, is_physical, formated_name, formated_name_indexed, formated_name_ts, family_name, birth_date, end_date ) VALUES
(1, false, 'Royal Belgian Institute of Natural Sciences', 'royalbelgianinstituteofnaturalsciences', to_tsvector('Royal Belgian Institute of Natural Sciences'),  'Royal Belgian Institute of Natural Sciences', DATE 'January 8, 1830', DEFAULT);
insert into people (id, is_physical, formated_name, formated_name_indexed, formated_name_ts, family_name, given_name, birth_date, end_date) VALUES
(2, true, 'Paul-André Duchesne', 'duchesnepaulandre', to_tsvector('Paul-André Duchesne'),  'Duchesne', 'Paul-André', DATE 'June 15, 1979', DEFAULT);
insert into users (id, is_physical, formated_name, formated_name_indexed, formated_name_ts, family_name, given_name, birth_date, gender) VALUES (1, true, 'Doe John', 'doejohn', to_tsvector('Doe John'), 'Doe', 'John', DATE 'June 20, 1979', 'M');
insert into users (id, is_physical, formated_name, formated_name_indexed, formated_name_ts, family_name, given_name, birth_date, gender, db_user_type) VALUES (2, true, 'Paul-André Duchesne', 'duchesnepaulandre', to_tsvector('Duchesne Paul-André'), 'Duchesne', 'Paul-André', DATE 'April 01, 1975', 'M', 8);
insert into collections (id, code, name, main_manager_ref, institution_ref, parent_ref, path) VALUES ( 1, 'vert', 'vertebrates', 1, 1, null, '');
insert into collections (id, code, name, main_manager_ref, institution_ref, parent_ref, path, code_auto_increment, code_part_code_auto_copy) VALUES ( 2, 'mam', 'mamalia', 1, 1, 1, '1', true, true);
insert into collections (id, code, name, main_manager_ref, institution_ref, parent_ref, path, code_auto_increment, code_part_code_auto_copy) VALUES ( 3, 'invert', 'invertebrate', 1, 1, null, '', false, true);

set darwin.userid= '2';
