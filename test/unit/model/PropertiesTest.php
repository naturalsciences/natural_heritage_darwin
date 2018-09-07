<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(6, new lime_output_color());
$c = new Properties();
$c->setDateFrom('1830/10/05 00:00:00');
$c->setDateFromMask(56);
$t->is($c->getFromDateMasked(),'05/10/1830 <em>00:00:00</em>', 'The date from Is construct and Masked');
$c->setDateTo('1830/10/05 12:36:00');
$c->setDateToMask(62);
$t->is($c->getToDateMasked(),'05/10/1830 12:36:<em>00</em>', 'The date to Is construct and Masked');
$t->is_deeply($c->getDateTo(),array (  'year' => 1830,  'month' => 10,  'day' => 5,  'hour' => 12,  'minute' => 36,  'second' => ''),'The date to is masked as array');
$t->is_deeply($c->getDateFrom(),array (  'year' => 1830,  'month' => 10,  'day' => 5,  'hour' => '',  'minute' => '',  'second' => ''),'The date from is masked as array');

$d = new FuzzyDateTime('1830/10/05 00:00:00',56);
$c->setDateTo($d);
$t->is($c->getToDateMasked(),'05/10/1830 <em>00:00:00</em>', 'The date to Is construct and Masked');

$d = new FuzzyDateTime('1830/10/05 12:36:00',62);
$c->setDateFrom($d);
$t->is($c->getFromDateMasked(),'05/10/1830 12:36:<em>00</em>', 'The date to Is construct and Masked');
