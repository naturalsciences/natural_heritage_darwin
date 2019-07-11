<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(5, new lime_output_color());

$lithoS = Doctrine::getTable('Lithostratigraphy')->findOneByName('Membre de Pollux');
$t->info('findWithParents($id)');
$unit = Doctrine::getTable('Lithostratigraphy')->findWithParents($lithoS->getId());
$t->isnt($unit,null, 'we got a unit');
$t->is($unit->count(),3, 'we got all parent of the unit');
$t->is($unit[1]->getId(),$lithoS->getParentRef(), 'Parent is correct');

$t->is($unit[1]->Level->__toString(),'formation', 'get Level');
$t->is($lithoS->getNameWithFormat(),'Membre de Pollux', 'get Name');