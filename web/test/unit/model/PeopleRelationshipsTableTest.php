<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(2, new lime_output_color());

$t->diag('findAllRelated');

$u = Doctrine::getTable('People')->searchPysical('Poilux');
$relation = Doctrine::getTable('PeopleRelationships')->findAllRelated($u[0]->getId());
$t->is(count($relation),0,'We found no relation with Poilux');

$ugmmm = Doctrine::getTable('People')->findByFamilyName('UGMM');

$rel = new PeopleRelationships();
$rel->fromArray(array('person_1_ref' => $ugmmm[0]->getId(), 'person_2_ref' => $u[0]->getId() ));
$rel->save();

$relation = Doctrine::getTable('PeopleRelationships')->findAllRelated($u[0]->getId());
$t->is(count($relation),1,'We found a relation with Poilux');
