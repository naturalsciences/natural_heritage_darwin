<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(1, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();

$t->info('getMyLoans for Evil ');
$cat = Doctrine::getTable('Loans')->getMyLoans($userEvil);
$t->is(count($cat),5,'Number of different loans for user Evil: "5"');

