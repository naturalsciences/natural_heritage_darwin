-- Testing that only one preferred language is provided for people/users
\unset ECHO
\i unit_launch.sql
SELECT plan(14);

SELECT lives_ok('INSERT INTO people_languages (id, people_ref) VALUES (10,1)', 'People: First data and not a preferred language -> should return ok');
SELECT lives_ok('INSERT INTO users_languages (id, users_ref) VALUES (11,1)', 'Users: First data and not a preferred language -> should return ok');
SELECT lives_ok('INSERT INTO people_languages (id, people_ref, language_country, preferred_language) VALUES (13,1, ''fr'', true)', 'People: First preferred language inserted -> should be ok');
SELECT lives_ok('INSERT INTO users_languages (id, users_ref, language_country, preferred_language) VALUES (14,1, ''fr'', true)', 'Users: First preferred language inserted -> should be ok');

SELECT throws_ok('INSERT INTO people_languages (people_ref, language_country, preferred_language) VALUES (1, ''nl'', true)');
SELECT throws_ok('INSERT INTO users_languages (users_ref, language_country, preferred_language) VALUES (1, ''fr'', true)');

SELECT throws_ok('UPDATE people_languages set preferred_language = true where language_country = ''en'' ');
SELECT throws_ok('UPDATE users_languages set preferred_language = true where language_country = ''en'' ');

SELECT lives_ok('UPDATE people_languages set preferred_language = false', 'People: Set all languages to false -> should be ok');
SELECT lives_ok('UPDATE users_languages set preferred_language = false', 'Users: Set all languages to false -> should be ok');
SELECT lives_ok('UPDATE people_languages set preferred_language = true where people_ref = 1 and language_country = ''en'' ', 'People: Set english to true for user 1 -> should be ok');
SELECT lives_ok('UPDATE users_languages set preferred_language = false where users_ref = 1 and language_country = ''en'' ', 'Users: Set english to true for user 1 -> should be ok');

SELECT lives_ok('INSERT INTO people_languages (people_ref, language_country, preferred_language) VALUES (2, ''fr'', true)', 'People: New person inserted with first preferred language inserted -> should be ok');
SELECT lives_ok('INSERT INTO users_languages (users_ref, language_country, preferred_language) VALUES (2, ''fr'', true)', 'Users: New user inserted with first preferred language inserted -> should be ok');

-- Finish the tests and clean up.
SELECT * FROM finish();
ROLLBACK;
