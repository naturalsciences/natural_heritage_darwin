<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(8, new lime_output_color());

$properties = Doctrine::getTable('Properties')->findForTable('taxonomy',4);
$t->is( count($properties) , 2, 'There is properties for this table / record_id');
$t->is( $properties[0]->getPropertyType() , 'physical measurement', 'ordered correcty');

$types = Doctrine::getTable('Properties')->getDistinctType();
$t->is( count($types) , 3, 'There is 3 different type with empty');
$t->is( $types['protection status'] , 'protection status', 'the and is accessible through getType');

$stype = Doctrine::getTable('Properties')->getDistinctApplies();
$t->is( count($stype) , 2, 'There is 4 different sub type');
$t->is( $stype['beak length'] , 'beak length', 'the and is accessible through getType');


$units = Doctrine::getTable('Properties')->getDistinctUnit('physical measurement');
$t->is( count($units) ,2, 'There is 2 different units');
$t->is_deeply($units, array('' =>'','cm' => 'cm'), 'There is 2 corret units');

