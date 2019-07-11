<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(6, new lime_output_color());

$identifications = Doctrine_Query::create()
            ->from('Identifications')
            ->orderBy('referenced_relation ASC, record_id ASC,order_by asc , id asc')->execute();
$identifications[0]->setDeterminationStatus('D1C1');
$identifications[1]->setDeterminationStatus('D1C2');
$identifications->save();

$detStatus = Doctrine::getTable('Identifications')->getDistinctDeterminationStatus();

$t->is($detStatus->count(), 2, 'There are well "2" determination status');
$t->is($detStatus[0]->getDeterminationStatus(), 'D1C1', 'First determination status is "D1C1"');
$t->is($detStatus[1]->getDeterminationStatus(), 'D1C2', 'Second determination status is "D1C2"');

$specId = 3;

$identifications = Doctrine::getTable('Identifications')->getIdentificationsRelated('specimens', $specId);

$t->is($identifications->count(), 2, 'There are well "2" determination status for specimen "'.$specId.'"');
$t->is($identifications[0]->getValueDefined(), 'Falco Peregrinus Tunstall, 1771', 'The first identification is "Falco Peregrinus Tunstall, 1771"');
$t->is($identifications[1]->getValueDefined(), 'Falco Peregrinus', 'The second identification is "Falco Peregrinus"');