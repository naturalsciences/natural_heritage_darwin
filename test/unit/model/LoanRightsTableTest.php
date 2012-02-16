<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(8, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();
$collmanager = Doctrine::getTable('Users')->findOneByFamilyName('collmanager')->getId();
$reguser = Doctrine::getTable('Users')->findOneByFamilyName('reguser')->getId();
$encoder = Doctrine::getTable('Users')->findOneByFamilyName('encoder')->getId();

$t->info('getEncodingRightsForUser( $user )');

$cat = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($userEvil);
$t->is(count($cat),0,'Number of loan rights for user Evil: "0"');

$cat = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($collmanager);
$t->is(count($cat),8,'Number of loan rights for user collmanager: "8"');

$cat = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($reguser);
$t->is(count($cat),2,'Number of loan rights for user reguser: "2"');

$cat = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($encoder);
$t->is(count($cat),1,'Number of loan rights for user encoder: "1"');

