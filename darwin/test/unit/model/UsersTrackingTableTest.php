<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(14, new lime_output_color());

$user = Doctrine::getTable('Users')->findOneByGivenName('Root');
$items = Doctrine::getTable('UsersTracking')->getMyItemsForPlot($user->getId(), 'day');
$t->is(count($items),7, 'We got 7 days of changes');

$t->is($items[6][1],1, 'We got only 1 change the last day');
$t->is($items[0][1],0, 'And no the other days');

$items = Doctrine::getTable('UsersTracking')->getMyItemsForPlot($user->getId(), 'month');
$t->is(count($items),32, 'We got 31 days of changes');
$t->is($items[31][1],1, 'We got only 1 change the last day');
$t->is($items[0][1],0, 'And no the other days');

$tables = Doctrine::getTable('UsersTracking')->getDistinctTable();
$t->is(count($tables),1, 'Changes are only on 1 table');
$t->is($tables[0]->getName(),'taxonomy', 'On taxo table');


$conn = Doctrine_Manager::connection();
$conn->exec("SELECT set_config('darwin.userid', '".$user->getId()."', false);");

$ex = Doctrine::getTable('Expeditions')->findOneByName('Pollux Expedition');
$ex->setName('New Name');
$ex->save();

$tables = Doctrine::getTable('UsersTracking')->getDistinctTable();
$t->is(count($tables),2, 'Changes are now 2 diff tables');

$items = Doctrine::getTable('UsersTracking')->getMyItemsForPlot($user->getId(), 'day');
$t->is(count($items),7, 'We got 7 days of changes');
$t->is($items[6][1],2, 'We got only 2 change the last day');
$t->is($items[0][1],0, 'And no the other days');

$items = Doctrine::getTable('UsersTracking')->getMyItems($user->getId(), 5)->execute();
$t->is(count($items),2, 'We got 2 changes');
$t->is($items[1]->getLink(),'taxonomy/edit?id=4', 'Got the good link');
