<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(4, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();
$collmanager = Doctrine::getTable('Users')->findOneByFamilyName('collmanager')->getId();
$reguser = Doctrine::getTable('Users')->findOneByFamilyName('reguser')->getId();
$encoder = Doctrine::getTable('Users')->findOneByFamilyName('encoder')->getId();

$t->info('getMyLoans($user_id, $max_items = FALSE) ');
$cat = Doctrine::getTable('Loans')->getMyLoans($userEvil);
$t->is(count($cat),0,'Number of different loans for user Evil: "0"');
$cat = Doctrine::getTable('Loans')->getMyLoans($collmanager);
$t->is(count($cat),6,'Number of different loans for user collmanager: "6"');
$cat = Doctrine::getTable('Loans')->getMyLoans($reguser);
$t->is(count($cat),1,'Number of different loans for user reguser: "1"');
$cat = Doctrine::getTable('Loans')->getMyLoans($encoder);
$t->is(count($cat),1,'Number of different loans for user encoder: "1"');
