<?php 
include(dirname(__FILE__).'/../../bootstrap/unit.php');
$t = new lime_test(4, new lime_output_color());
$t->info('Get the tags for "Telephone" type');
$tags = Doctrine::getTable('PeopleComm')->getTags('phone/fax');
$t->is(count($tags), 8, 'There are well "8" tags');
$t->is($tags['cell'], 'Cell', 'Tags "Cell" is well defined');
$tags = Doctrine::getTable('PeopleComm')->getTags('e-mail');
$t->is(count($tags), 4, 'There are well "4" tags');
$t->is($tags['cell'], '', 'Tags "Cell" is not defined... normal :)');
