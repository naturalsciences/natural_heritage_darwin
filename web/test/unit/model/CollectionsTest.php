<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(4, new lime_output_color());

$t->info('getLevel');
$col1 = new Collections();
$col1->setPath('/12/13/');

$t->is($col1->getLevel(),3,'Get the level of the collection with parents');
$col1->setPath('/');
$t->is($col1->getLevel(),1,'Get the level of the collection at root');

$t->info('__toString');
$col1->setName("Malaco");
$t->is("".$col1,'Malaco',"To string of collection get name for root levels");

$col1->setPath('/12/13/');
$t->is("".$col1,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Malaco',"To string of collection add spaces for childrens levels");
