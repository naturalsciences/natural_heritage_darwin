<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(13, new lime_output_color());

$t->info('getDistinctCategories');
$cat = SpecimensTable::getDistinctCategories();
$t->is(count($cat),12,'Number of differents categories: "12"');
$t->is($cat['undefined'],'Undefined','get the first category: "Undefined"');
$t->is($cat['collect'],'Collect','get the last category: "Collect"');

/* Prepare data for the test of findConservatories method */
// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
// User creation/extraction
$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil');
$brol_user = new Users();
$brol_user->setFamilyName('Brolus');
$brol_user->setGivenName('Brolus');
$brol_user->setDbUserType(Users::REGISTERED_USER);
$brol_user->save();

$conn = Doctrine_Manager::connection();

// Collection Rights association for the "brol" user - rights on Molusca, Amphibia  and Aves collections
$conn->exec("INSERT INTO collections_rights (collection_ref, user_ref, db_user_type)
              (
                SELECT id, ? , ?
                FROM collections
                WHERE name_indexed IN ('molusca', 'amphibia', 'aves')
              )",
            array( $brol_user->getId(), $brol_user->getDbUserType() )
);

// Building and Floor creation for specimens under Molusca and Amphibia collections
$conn->exec("UPDATE specimens
              SET building = CASE WHEN id = 1 THEN 'De Vestel' ElSE 'Geology' END,
                  floor = CASE WHEN id = 1 THEN '14' ElSE '-1' END
              WHERE collection_ref IN (
                                        SELECT id
                                        FROM collections
                                        WHERE name_indexed IN ('molusca', 'amphibia')
                                      )
            ");

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

$t->info('Test of "findConservatories" - part 1 - registered user - building list searched');
$conservatories_counts = Doctrine::getTable("Specimens")->findConservatories($brol_user, 'building', array());
$t->is('', $conservatories_counts[0]['item'], '"1" specimen in "empty" building (in aves collection)');
$t->is(1, $conservatories_counts[0]['ctn'], '"1" specimen in "empty" building in (aves collection)');
$t->is('De Vestel', $conservatories_counts[1]['item'], '"1" specimen in building "De Vestel"');
$t->is(1, $conservatories_counts[1]['ctn'], '"1" specimen in building "De Vestel"');
$t->is('Geology', $conservatories_counts[2]['item'], '"2" specimen in building "Geology"');
$t->is(2, $conservatories_counts[2]['ctn'], '"2" specimen in building "De Vestel"');
$t->info('Test of "findConservatories" - part 2 - registered user - floor list searched for "De Vestel" building');
$floor_counts = Doctrine::getTable("Specimens")->findConservatories($brol_user, 'floor', array("building"=>$conservatories_counts[2]['item']));
$t->is('-1', $floor_counts[0]['item'], '"2" specimens on floor "-1" for "Geology" building');
$t->is(2, $floor_counts[0]['ctn'], '"2" specimens on floor "-1" for "Geology" building');

$conn->execute("update collections set is_public = false");

$conn->exec("DELETE FROM collections_rights
             WHERE collection_ref = (
                SELECT id
                FROM collections
                WHERE name_indexed = 'aves'
             )
               AND user_ref = ?
            ",
            array( $brol_user->getId() )
);

$t->info('Test of "findConservatories" - part 3 - registered user - building list searched after removing user from aves collection');
$t->is(2,count(Doctrine::getTable("Specimens")->findConservatories($brol_user, 'building', array())),'After having set all collections to not visible... stay well "2" counted for registered user...');
$t->is(3,count(Doctrine::getTable("Specimens")->findConservatories($userEvil, 'building', array())),'... and "3" counted for admin user...');

$conn->close();