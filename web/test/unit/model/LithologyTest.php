<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(5, new lime_output_color());

$litho = Doctrine::getTable('Lithology')->findOneByName('Rudites');
$t->info('findWithParents($id)');
$unit = Doctrine::getTable('Lithology')->findWithParents($litho->getId());
$t->isnt($unit,null, 'we got a unit');
$t->is($unit->count(),3, 'we got all parent of the unit');
$t->is($unit[1]->getId(),$litho->getParentRef(), 'Parent is correct');

$t->is($unit[1]->Level->__toString(),'group', 'get Level');
$t->is($litho->getNameWithFormat(),'Rudites', 'get Name');