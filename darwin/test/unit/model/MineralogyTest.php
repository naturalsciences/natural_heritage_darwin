<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(6, new lime_output_color());

$mineralo = Doctrine::getTable('Mineralogy')->findOneByName('Alkali carbonates');
$t->info('findWithParents($id)');
$unit = Doctrine::getTable('Mineralogy')->findWithParents($mineralo->getId());
$t->isnt($unit,null, 'we got a unit');
$t->is($unit->count(),3, 'we got all parent of the unit');
$t->is($unit[1]->getId(),$mineralo->getParentRef(), 'Parent is correct');

$t->is($unit[1]->Level->__toString(),'sub_class', 'get Level');
$t->is($mineralo->getNameWithFormat(),'Alkali carbonates', 'get Name');
$t->is($mineralo->getCode(), '5.AA', 'Corect code');