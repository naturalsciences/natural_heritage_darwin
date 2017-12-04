<?php 
include(dirname(__FILE__).'/../../bootstrap/unit.php');
$t = new lime_test(3, new lime_output_color());

$t->diag('getTagsAsArray');
$a = new PeopleAddresses();

$t->is($a->getTagsAsArray(),array(),"By default we doesn't have a tag");

$a->setTag('pref,home,work');
$t->is($a->getTagsAsArray(),array ('Preferred','Home','Work'),"We have 3 tags");

$a->setTag('pref');
$t->is($a->getTagsAsArray(),array('Preferred'),"We have only 1 tag");