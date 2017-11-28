<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(6, new lime_output_color());

$mineralo = Doctrine::getTable('Mineralogy')->fetchByCodeLimited('4', 10);
$t->info('fetchByCodeLimited($code, $limit)');
$t->is($mineralo->count(), 6, 'Got the right number of units with code begining with 4');
$mineralo = Doctrine::getTable('Mineralogy')->fetchByCodeLimited('4', 2);
$t->info('fetchByCodeLimited($code, 2)');
$t->is($mineralo->count(), 2, 'Got the right number of units with code begining with 4');
$t->info('Get the distinct cristal systems');
$cristalo = Doctrine::getTable('Mineralogy')->getDistinctSystems();
$t->is($cristalo->count(), 0, 'No System encoded yet...');
$mineralo = new Mineralogy;
$mineralo->setCode('6');
$mineralo->setName('Test');
$mineralo->setCristalSystem('Pollux');
$mineralo->setLevelRef(70);
$mineralo->save();
$mineralo = Doctrine::getTable('Mineralogy')->fetchByCodeLimited('6', 10);
$t->info('fetchByCodeLimited($code, $limit)');
$t->is($mineralo->count(), 1, 'New unit well stored');
$t->is($mineralo[0]->getCristalSystem(), 'Pollux', 'New system is well Pollux');
$cristalo = Doctrine::getTable('Mineralogy')->getDistinctSystems();
$t->is($cristalo[0]->getCSystem(), 'Pollux', 'No System encoded yet...');