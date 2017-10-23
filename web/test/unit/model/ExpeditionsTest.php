<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(13, new lime_output_color());

$fromDate = new FuzzyDateTime('1975/01/01', 32);
$toDate = new FuzzyDateTime('2009/12/31', 32);
$expedition = Doctrine::getTable('Expeditions')->findOneByName('Antarctica 1988');
$t->is( $expedition->getExpeditionFromDateMasked() , '<em>01</em>/04/1988', 'Correct from date masked for "Antarctica 1988"');
$t->is( $expedition->getExpeditionToDateMasked() , '<em>31</em>/07/1988', 'Correct to date masked for "Antarctica 1988"');
$t->is( $expedition->getExpeditionFromDate() , array('year'=>1988, 'month'=>04, 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct from date masked as array for "Antarctica 1988"');
$t->is( $expedition->getExpeditionToDate() , array('year'=>1988, 'month'=>07, 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct to date masked as array for "Antarctica 1988"');
$expedition->setExpeditionFromDate($fromDate);
$expedition->setExpeditionToDate($toDate);
$t->is( $expedition->getExpeditionFromDateMasked() , '<em>01/01</em>/1975', 'Correct from date masked for "Antarctica 1988"');
$t->is( $expedition->getExpeditionToDateMasked() , '<em>31/12</em>/2009', 'Correct to date masked for "Antarctica 1988"');
$t->is( $expedition->getExpeditionFromDate() , array('year'=>1975, 'month'=>'', 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct from date masked as array for "Antarctica 1988"');
$t->is( $expedition->getExpeditionToDate() , array('year'=>2009, 'month'=>'', 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct to date masked as array for "Antarctica 1988"');
$expedition->setExpeditionFromDate('1975/01/01');
$expedition->setExpeditionToDate('2009/12/31');
$t->is( $expedition->getExpeditionFromDateMasked() , '01/01/1975', 'Correct from date masked for "Antarctica 1988"');
$t->is( $expedition->getExpeditionToDateMasked() , '31/12/2009', 'Correct to date masked for "Antarctica 1988"');
$t->is( $expedition->getExpeditionFromDate() , array('year'=>1975, 'month'=>01, 'day'=>01, 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct from date masked as array for "Antarctica 1988"');
$t->is( $expedition->getExpeditionToDate() , array('year'=>2009, 'month'=>12, 'day'=>31, 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct to date masked as array for "Antarctica 1988"');
$t->is( $expedition->getName(), 'Antarctica 1988', 'Correct return of getName() method');