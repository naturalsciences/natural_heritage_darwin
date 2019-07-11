<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(4, new lime_output_color());

$tools = Doctrine::getTable('CollectingTools')->fetchTools();
$t->is(count($tools), 2, '"2" Tools defined by default');
$t->info('Insert a new value');
$newVal = new CollectingTools;
$newVal->setTool('Bourouche');
$newVal->save();
$newValIndex = $newVal->getId();
$tools = Doctrine::getTable('CollectingTools')->fetchTools();
$t->is(count($tools), 3, '"3" Tools defined now');
$t->is($tools[$newValIndex], 'Bourouche', 'The new value inserted is well "Bourouche"');
$iteration = 1;
foreach($tools as $value)
{
  if($iteration == 2)
  {
    $t->is($value, 'Bourouche', 'The new value ("Bourouche") is well the "second" one to be in the list brought by "fetchTools"');
    break;
  }
  $iteration++;
}