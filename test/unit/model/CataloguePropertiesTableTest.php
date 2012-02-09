<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(12, new lime_output_color());

$properties = Doctrine::getTable('CatalogueProperties')->findForTable('taxonomy',4);
$t->is( count($properties) , 2, 'There is properties for this table / record_id');
$t->is( $properties[0]->getPropertyType() , 'physical measurement', 'ordered correcty');
$t->is( count($properties[0]->PropertiesValues) , 2, 'There is also 2 properties values');

$types =
Doctrine::getTable('CatalogueProperties')->getDistinctType('taxonomy');
$t->is( count($types) , 3, 'There is 2 different type');
$t->is( $types["protection status"] , 'protection status', 'the ');

$stype =
Doctrine::getTable('CatalogueProperties')->getDistinctSubType('protection status');
$t->is( count($stype) , 3, 'There is different sub type');
$t->is( $stype['top'] , 'top', 'the and is accessible');

$stype = Doctrine::getTable('CatalogueProperties')->getDistinctSubType('physical measurement');
$t->is( count($stype) ,2, 'There is 2 different sub type with this type');
$t->is( $stype['length'], 'length', 'There is 1 different sub type with this type');


$qual = Doctrine::getTable('CatalogueProperties')->getDistinctQualifier('length');
$t->is( count($stype) ,2, 'There is 2 different qualifier with this sub type');

$units = Doctrine::getTable('CatalogueProperties')->getDistinctUnit('physical measurement');
$t->is( count($units) ,3, 'There is 3 different units');
$t->is_deeply($units, array('' =>'unit','cm' => 'cm', 'mm' => 'mm'), 'There is 3 corret units');

