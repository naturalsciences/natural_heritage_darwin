<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(3, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();
$ids_userEvil = Doctrine::getTable('Loans')->getMyLoans($userEvil)->execute();

$t->info('getFromLoans()');
$cat = Doctrine::getTable('LoanStatus')->getFromLoans(getIdsArrayFrom($ids_userEvil));
$t->is(count($cat), 5, '5 last loans status are returnes for evil');

$status = Doctrine::getTable('LoanStatus')->getDistinctStatus();
$t->is(count($status), 6, '6 status for loans are possible');

$allstatus = Doctrine::getTable('LoanStatus')->getallLoanStatus(1) ;
$t->is(count($allstatus), 3, '3 status exist for loans Dog of Goyet');

/*
   little helper function to construct an array of ids
*/

function getIdsArrayFrom( $ids )
{
  $de_ids = array();
  foreach ( $ids as $id )
    $de_ids[] = $id->getId();
  return $de_ids;
}

