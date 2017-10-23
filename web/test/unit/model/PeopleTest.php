<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(20, new lime_output_color());
$p = new People();
$p->setFormatedName('Mr Poilux Duchesne');
$t->info('Test "__toString" method to get formated name of given person');
$t->is($p->__toString(),'Mr Poilux Duchesne','to string get "FormatedName": "Mr Poilux Duchesne"');

$t->info('Get static list of people types');
$types = People::getTypes();
$t->is($types['identifier'], 'Identifier','We have "Identifier" as Type');
$t->is($types['collector'], 'Collector','We have "Collector" as Type');

$t->info('DB people types tests');

$p->setBirthDate('05/12/1908');
$t->is($p->getBirthDate(), array('year' => '', 'month' => '', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a null birth date');

$p->setBirthDate(new FuzzyDateTime('1975/02/24 13:12:11',48) );
$t->is($p->getBirthDate(), array('year' => '1975', 'month' => '02', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a birth date');
$t->is($p->getBirthDateObject()->format('y/M'), '75/Feb','We get a birth date object');
$t->is($p->getBirthDateMasked(), '<em>24</em>/02/1975','We get a birth date masked');
$p->setBirthDate('1900/05/2');
$t->is($p->getBirthDate(), array('year' => '1900', 'month' => '05', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a End date');

$p->setEndDate(new FuzzyDateTime('1975/03/24 13:12:11',48) );
$t->is($p->getEndDate(), array('year' => '1975', 'month' => '03', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a End date');
$t->is($p->getEndDateObject()->format('y/M'), '75/Mar','We get a End date object');
$t->is($p->getEndDateMasked(), '<em>24</em>/03/1975','We get a End date masked');

$p->setEndDate('1975/05/2');
$t->is($p->getEndDate(), array('year' => '1975', 'month' => '05', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a End date');



$p->setActivityDateFrom(new FuzzyDateTime('1975/04/24 13:12:11',48) );
$t->is($p->getActivityDateFrom(), array('year' => '1975', 'month' => '04', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a activity from date');
$t->is($p->getActivityDateFromObject()->format('y/M'), '75/Apr','We get a activity from date object');
$t->is($p->getActivityDateFromMasked(), '<em>24</em>/04/1975','We get a activity from date masked');
$p->setActivityDateFrom('1900/05/2');
$t->is($p->getActivityDateFrom(), array('year' => '1900', 'month' => '05', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a End date');


$p->setActivityDateTo(new FuzzyDateTime('1975/05/24 13:12:11',48) );
$t->is($p->getActivityDateTo(), array('year' => '1975', 'month' => '05', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a activity to date');
$t->is($p->getActivityDateToObject()->format('y/M'), '75/May','We get a activity to date object');
$t->is($p->getActivityDateToMasked(), '<em>24</em>/05/1975','We get a activity to date masked');

$p->setActivityDateTo('1900/01/2');
$t->is($p->getActivityDateTo(), array('year' => '1900', 'month' => '01', 'day' => '', 'hour' => '', 'minute' => '','second' => ''),'We have set a End date');
