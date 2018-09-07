<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(2, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();

$t->info('->addUserOrder');

$q = Doctrine::getTable('MySavedSearches')->addUserOrder(null,$userEvil);

$t->is(get_class($q),'Doctrine_Query','We got a query not null');
$results = $q->execute();
$t->is(count($results),3,'We and we got the right number or record');

