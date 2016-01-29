<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(12, new lime_output_color());

$t->diag('getLevel');
$r = new PeopleRelationships();
$t->is( $r->getLevel(), 0, 'By default the level is 0');
$r->setPath('/12/162/4/');
$t->is( $r->getLevel(), 3, 'We have changed the level');
$t->is($r->showPadding(),'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','We have 3 time the padding');

$r->setPath('/');
$t->is( $r->getLevel(), 0, 'We have changed the level back');


$t->diag('setActivityDate');
$r->setActivityDateFrom(new FuzzyDateTime('1975/04/24 13:12:11',48) );
$t->is($r->getActivityDateFrom(), array('year' => '1975', 'month' => '04', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a activity from date');
$t->is($r->getActivityDateFromObject()->format('y/M'), '75/Apr','We get a activity from date object');
$t->is($r->getActivityDateFromMasked(), '<em>24</em>/04/1975','We get a activity from date masked');

$r->setActivityDateTo(new FuzzyDateTime('1975/05/24 13:12:11',48) );
$t->is($r->getActivityDateTo(), array('year' => '1975', 'month' => '05', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a activity to date');
$t->is($r->getActivityDateToObject()->format('y/M'), '75/May','We get a activity to date object');
$t->is($r->getActivityDateToMasked(), '<em>24</em>/05/1975','We get a activity to date masked');

$r->setActivityDateFrom('1975/06/01');
$t->is($r->getActivityDateFromMasked(), '<em>01</em>/06/1975','We get a activity from date masked');
$r->setActivityDateTo('1985/12/12');
$t->is($r->getActivityDateToMasked(), '<em>12</em>/12/1985','We get a activity from date masked');
