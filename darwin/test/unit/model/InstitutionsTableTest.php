<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(4, new lime_output_color());

$t->diag('getDistinctSubType');

$p = Doctrine::getTable('Institutions')->getDistinctSubType();
$t->is($p[0]->getType(),'Federal institution','Get Institutions types');

$t->diag('Find Institution');
$people = Doctrine::getTable('People')->findByFamilyName('Duchesne'); 
$r = Doctrine::getTable('Institutions')->findInstitution($people[0]->getId());
$t->is($r,null,'we did not find the P');
$ugmmm = Doctrine::getTable('People')->findByFamilyName('UGMM'); 
$r = Doctrine::getTable('Institutions')->findInstitution( $ugmmm[0]->getId());
$t->isnt($r,null,'But ugmm is an Institutions');

$t->is($r->__toString(),'UGMM','get The toString of this institution');
