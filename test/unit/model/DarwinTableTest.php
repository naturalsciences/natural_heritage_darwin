<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(6, new lime_output_color());

$t->info('Get autocomplete results');
$entries = Doctrine::getTable('Taxonomy')->completeAsArray('','Falco peregrinus','',30,'');
$t->info('Get "Falco peregrinus" count');
$t->is(count($entries),5,'There is a good number of results');
$t->info('Get "Falco peregrinus tun" count');
$entries = Doctrine::getTable('Taxonomy')->completeAsArray('','Falco peregrinus tun','',30,'');
$t->is(count($entries),1,'There is a good number of results');
$t->info('Get "Falco" entries allowed to be connected as parent for a species count');
$entries = Doctrine::getTable('Taxonomy')->completeAsArray('','Falco','',30,48);
$t->is(count($entries),4,'There is a good number of results');
$t->info('Get "Falco" entries allowed to be connected as parent for a genus count');
$entries = Doctrine::getTable('Taxonomy')->completeAsArray('','Falco','',30,41);
$t->is(count($entries),3,'There is a good number of results');
$t->info('Get "Falco Fam" entries allowed to be connected as parent for a genus with exact option activated count');
$entries = Doctrine::getTable('Taxonomy')->completeAsArray('','Falco Fam','1',30,41);
$t->is(count($entries),1,'There is a good number of results');
$t->info('Test other method: completeWithLevelAsArray');
$entries = Doctrine::getTable('Taxonomy')->completeWithLevelAsArray('','Falco Fam','1',30,41);
$t->is(count($entries),1,'There is a good number of results');
