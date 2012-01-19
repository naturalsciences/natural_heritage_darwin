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
$expected_rights_array = array();
$expect = printf_arrays("%s => %s, ", array_keys($expected_rights_array), array_values($expected_rights_array));
$t->is_deeply($cat, $expected_rights_array, 'The expected rights array has been returned. "' . $expect . '"');

$cat = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($collmanager);
$t->is(count($cat),8,'Number of loan rights for user collmanager: "8"');
$expected_rights_array = array( 1 => TRUE, 2 => FALSE, 6 => FALSE, 5 => TRUE, 4 => TRUE, 3 => FALSE, 8 => TRUE, 9 => TRUE );
$expect = printf_arrays("%s => %s, ", array_keys($expected_rights_array), array_values($expected_rights_array));
$t->is_deeply($cat, $expected_rights_array, 'The expected rights array has been returned. "'. $expect .'"');

$cat = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($reguser);
$t->is(count($cat),2,'Number of loan rights for user reguser: "2"');
$expected_rights_array = array( 1 => FALSE, 2 => TRUE );
$expect = printf_arrays("%s => %s, ", array_keys($expected_rights_array), array_values($expected_rights_array));
$t->is_deeply($cat, $expected_rights_array, 'The expected rights array has been returned. "'. $expect .'"');

$cat = Doctrine::getTable('LoanRights')->getEncodingRightsForUser($encoder);
$t->is(count($cat),1,'Number of loan rights for user encoder: "1"');
$expected_rights_array = array ( 9 => true );
$expect = printf_arrays("%s => %s, ", array_keys($expected_rights_array), array_values($expected_rights_array));
$t->is_deeply($cat, $expected_rights_array, 'The expected rights array has been returned. "'. $expect .'"');

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