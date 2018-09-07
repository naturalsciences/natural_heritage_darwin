<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(3, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();


$t->info('getEncodingRightsForUser( Evil )');

$cat = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($userEvil);
$t->is(count($cat),5,'Number of loan rights for user Evil: "5"');
$t->is(Doctrine::getTable('LoanRights')->isAllowed($userEvil,3),FALSE, 'Evil don\'t have right on loan "3"');
$t->is(Doctrine::getTable('LoanRights')->isAllowed($userEvil,6),'view', 'Evil only have read right on loan "6"');
