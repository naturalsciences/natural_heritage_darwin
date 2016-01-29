<?php 
include(dirname(__FILE__).'/../../bootstrap/unit.php');
$t = new lime_test(5, new lime_output_color());

$t->diag('getTagsAsArray');
$a = new PeopleComm();

$t->is($a->getTagsAsArray(),array(),"By default we doesn't have a tag");

$a->setCommType('phone/fax');
$a->setTag('pref,home,pager');
$t->is($a->getTagsAsArray(),array ('Preferred','Home','Pager'),"We have 3 tags");

$a->setTag('pref');
$t->is($a->getTagsAsArray(),array('Preferred'),"We have only 1 tag");

$a->setTag('pref,home,pager');
$a->setCommType('e-mail');

$t->is($a->getTagsAsArray(),array ('Preferred','Home'),"We have 2 tags (do not get pager)");

$a->setTag('pref,home,internet');

$t->is($a->getTagsAsArray(),array ('Preferred','Home','Internet'),"We can set mail tags");