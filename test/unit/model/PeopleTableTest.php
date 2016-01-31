<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(6, new lime_output_color());

$t->diag('searchPysical');

$p = Doctrine::getTable('People')->searchPysical('Duchesne');
$t->is(count($p),2,'2 record for this criteria');
$p = Doctrine::getTable('People')->searchPysical('ugmm');
$t->is(count($p),0,'there is no physical UGMM');
$p = Doctrine::getTable('People')->searchPysical('Poilux');
$t->is(count($p),1,'there is only one Poilux \o/');

$t->diag('Find People');
$r = Doctrine::getTable('People')->findPeople($p[0]->getId());
$t->isnt($r,null,'we find the P');
$ugmmm = Doctrine::getTable('People')->findByFamilyName('UGMM'); 
$r = Doctrine::getTable('People')->findPeople( $ugmmm[0]->getId());
$t->is($r,null,'But ugmm is not a people');

$titles = Doctrine::getTable('People')->getDistinctTitles()->toArray();
$t->is($titles[1]['titles'], 'Mr', 'Second title is well "Mr"');