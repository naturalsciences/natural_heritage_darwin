<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(8, new lime_output_color());

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
$t->is(count($cat),0,'Number of loan statusses for user Evil: "0"');
$expected_statusses_array = array ();
$expect = printf_arrays("%s => %s, ", array_keys($expected_statusses_array), array_values($expected_statusses_array));
$t->is_deeply($cat, $expected_statusses_array, 'The expected statusses array has been returned. "'. $expect .'"');

$cat = Doctrine::getTable('LoanStatus')->getFromLoans(getIdsArrayFrom( $ids_collmanager ));
$t->is(count($cat),6,'Number of loan statusses for user collmanager: "6"');
$expected_statusses_array = array (  3 => 'Pending',  2 => 'Open',  9 => 'Open',  5 => 'Open',  6 => 'Open',  8 => 'Accepted');
$expect = printf_arrays("%s => %s, ", array_keys($expected_statusses_array), array_values($expected_statusses_array));
$t->is_deeply($cat, $expected_statusses_array, 'The expected statusses array has been returned. "'. $expect .'"');

$cat = Doctrine::getTable('LoanStatus')->getFromLoans(getIdsArrayFrom( $ids_reguser));
$t->is(count($cat),1,'Number of loan statusses for user reguser: "1"');
$expected_statusses_array = array (  2 => 'Open' );
$expect = printf_arrays("%s => %s, ", array_keys($expected_statusses_array), array_values($expected_statusses_array));
$t->is_deeply($cat, $expected_statusses_array, 'The expected statusses array has been returned. "'. $expect .'"');

$cat = Doctrine::getTable('LoanStatus')->getFromLoans(getIdsArrayFrom( $ids_encoder ));
$t->is(count($cat),1,'Number of loan statusses for user encoder: "1"');
$expected_statusses_array = array (  9 => 'Open' );
$expect = printf_arrays("%s => %s, ", array_keys($expected_statusses_array), array_values($expected_statusses_array));
$t->is_deeply($cat, $expected_statusses_array, 'The expected statusses array has been returned. "'. $expect .'"');

/*
  printf_arrays( string format, [array args[, array ...]] ) 
     returns a text representation "array( key => value, ... )"
*/

function printf_arrays($format) 
{
    $args = func_get_args();
    array_shift($args); // get rid of format
    $res = "array( ";
    for($i=0; $i<count($args[0]); $i++) 
    {
        $pfargs = Array();
        foreach($args as $arr) $pfargs[] = (is_array($arr) && $arr[$i]) ? $arr[$i] : '';
        $res .= vsprintf($format, $pfargs);
    }
    $res .= ")";
    return $res;
}

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

