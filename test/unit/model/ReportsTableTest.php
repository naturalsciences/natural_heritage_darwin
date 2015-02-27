<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(4, new lime_output_color());

$u = Doctrine::getTable('Users')->getUserByPassword("root","evil");

$reports = Doctrine::getTable('Reports')->getTaskReports() ;
$t->is(count($reports), 0, '"0" Tasks report available');

$report = new Reports() ;
$report->setParameters(array('name'=>'annual_stat_collection','collection_ref'=>1,'date_from'=>'01-01-2000','date_to'=>'31-12-2012')) ;
$report->setUserRef($u->getId()) ;
$report->setName('annual_stat_collection') ;
$report->setLang('en') ;
$report->setFormat('pdf') ;
$report->save() ;

$reports = Doctrine::getTable('Reports')->getTaskReports() ;
$t->is(count($reports), 1, '"1" Task report available');

$reports = Doctrine::getTable('Reports')->getUserReport($u->getId()) ;
$t->is(count($reports), 1, '"1" report available for user "'.$u->getGivenName().'"');
foreach ($reports as $report) {
  $t->is($report->getParameters()->count(), 3, '"3" parameters (collection_ref,date_from,date_to) set for "'.$report->getName().'"') ;
}


