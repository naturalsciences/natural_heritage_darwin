<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(5, new lime_output_color());

$chrono = Doctrine::getTable('Chronostratigraphy')->findOneByName('Jurassic');
$t->info('findWithParents($id)');
$unit = Doctrine::getTable('Chronostratigraphy')->findWithParents($chrono->getId());
$t->isnt($unit,null, 'we got a unit');
$t->is($unit->count(),3, 'we got all parent of the unit');
$t->is($unit[1]->getId(),$chrono->getParentRef(), 'Parent is correct');

$t->is($unit[1]->Level->__toString(),'era', 'get Level');
$t->is($chrono->getNameWithFormat(),'Jurassic', 'get Name');