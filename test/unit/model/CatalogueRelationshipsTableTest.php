<?php                                                                                     
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');                               
$t = new lime_test(6, new lime_output_color());                                          

$t->diag('getRelationsForTable');

$relations = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy', 3);
$t->is(count($relations),0,'We got 0 relation');

$relations = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy', 4);
$t->is(count($relations),2,'We got 2 relation');

$t->is($relations[0]['record_id_1'], 4,'The record id is the good one');
$relations = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy', 4,'current_name');
$t->is(count($relations),1,'We got 1 relation of this type');
$t->is($relations[0]['record_id_2'], 3,'The record id is the good one');

$t->is($relations[0]['ref_item']->getName(), 'Falco Peregrinus Tunstall, 1771','The record ref is filled');