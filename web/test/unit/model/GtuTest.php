<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(16, new lime_output_color());

$fromDate = new FuzzyDateTime('1975/01/01', 32);
$toDate = new FuzzyDateTime('2009/12/31', 32);
$gtu = Doctrine::getTable('Gtu')->findOneByCode('Antarctica');
$t->is( $gtu-> getGtuFromDateMasked() , '<em>01</em>/04/1988 <em>00:00:00</em>', 'Correct from date masked for this gtu');
$t->is( $gtu-> getGtuToDateMasked() , '<em>31</em>/07/1988 <em>00:00:00</em>', 'Correct to date masked for this gtu');
$t->is( $gtu-> getGtuFromDate() , array('year'=>1988, 'month'=>04, 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct from date masked as array this gtu');
$t->is( $gtu-> getGtuToDate() , array('year'=>1988, 'month'=>07, 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct to date masked as array this gtu');
$gtu->setGtuFromDate($fromDate);
$gtu->setGtuToDate($toDate);
$t->is( $gtu-> getGtuFromDateMasked() , '<em>01/01</em>/1975 <em>00:00:00</em>', 'Correct from date masked this gtu');
$t->is( $gtu-> getGtuToDateMasked() , '<em>31/12</em>/2009 <em>00:00:00</em>', 'Correct to date masked this gtu');
$t->is( $gtu-> getGtuFromDate() , array('year'=>1975, 'month'=>'', 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct from date masked as array this gtu');
$t->is( $gtu-> getGtuToDate() , array('year'=>2009, 'month'=>'', 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct to date masked as array this gtu');
$gtu->setGtuFromDate('1975/01/01');
$gtu->setGtuToDate('2009/12/31');
$t->is( $gtu-> getGtuFromDateMasked() , '01/01/1975 <em>00:00:00</em>', 'Correct from date masked this gtu');
$t->is( $gtu-> getGtuToDateMasked() , '31/12/2009 <em>00:00:00</em>', 'Correct to date masked this gtu');
$t->is( $gtu-> getGtuFromDate() , array('year'=>1975, 'month'=>01, 'day'=>01, 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct from date masked as array this gtu');
$t->is( $gtu-> getGtuToDate() , array('year'=>2009, 'month'=>12, 'day'=>31, 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct to date masked as array this gtu');
$t->is( $gtu->getCode(), 'Antarctica', 'Correct return of getName() method');
$t->is( ($gtu->hasCountries()), true,  'Antarctica gtu has well country');
$gtu = Doctrine::getTable('Gtu')->findOneByCode('3615Danger');
$t->is( $gtu->getCode(), '3615Danger', 'Correct return of getName() method');
$t->is( ($gtu->hasCountries()), false,  '3615Danger gtu has well no country');
