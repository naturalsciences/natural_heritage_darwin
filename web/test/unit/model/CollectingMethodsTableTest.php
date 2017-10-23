<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(4, new lime_output_color());

$methods = Doctrine::getTable('CollectingMethods')->fetchMethods();
$t->is(count($methods), 2, '"2" Methods defined by default');
$t->info('Insert a new value');
$newVal = new CollectingMethods;
$newVal->setMethod('Intuition');
$newVal->save();
$newValIndex = $newVal->getId();
$methods = Doctrine::getTable('CollectingMethods')->fetchMethods();
$t->is(count($methods), 3, '"3" Methods defined now');
$t->is($methods[$newValIndex], 'Intuition', 'The new value inserted is well "Intuition"');
foreach($methods as $value)
{
  $t->is($value, 'Intuition', 'The new value ("Intuition") is well the "first" one to be in the list brought by "fetchMethods"');
  break;
}