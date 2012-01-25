<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(4, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();
$collmanager = Doctrine::getTable('Users')->findOneByFamilyName('collmanager')->getId();
$reguser = Doctrine::getTable('Users')->findOneByFamilyName('reguser')->getId();
$encoder = Doctrine::getTable('Users')->findOneByFamilyName('encoder')->getId();

$ids_userEvil = Doctrine::getTable('Loans')->getMyLoans($userEvil)->execute();
$ids_collmanager = Doctrine::getTable('Loans')->getMyLoans($collmanager)->execute();
$ids_reguser = Doctrine::getTable('Loans')->getMyLoans($reguser)->execute();
$ids_encoder = Doctrine::getTable('Loans')->getMyLoans($encoder)->execute();

$t->info('getFromLoans( $loan_ids )');

$cat = Doctrine::getTable('LoanStatus')->getFromLoans(getIdsArrayFrom( $ids_userEvil ));
$t->is_deeply($cat, array (), 'The expected statusses array has been returned for evil');

$cat = Doctrine::getTable('LoanStatus')->getFromLoans(getIdsArrayFrom( $ids_collmanager ));
$expected_statusses_array = array (  3 => 'pending',  2 => 'new',  9 => 'new',  5 => 'new',  6 => 'new',  8 => 'accepted');
$t->is_deeply($cat, $expected_statusses_array, 'The expected statusses array has been returned for coll manager');

$cat = Doctrine::getTable('LoanStatus')->getFromLoans(getIdsArrayFrom( $ids_reguser));
$t->is_deeply($cat, array (  2 => 'new' ), 'The expected statusses array has been returned for reg user');

$cat = Doctrine::getTable('LoanStatus')->getFromLoans(getIdsArrayFrom( $ids_encoder ));
$t->is_deeply($cat, array (  9 => 'new' ), 'The expected statusses array has been returned for encoder');

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

