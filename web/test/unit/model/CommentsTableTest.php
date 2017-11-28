<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(3, new lime_output_color());

$com = Doctrine::getTable('Comments')->findForTable('taxonomy',4);
$t->is(count($com), 1,"There is only 1 comment");
$com = Doctrine::getTable('Comments')->findForTable('taxonomy',3);
$t->is(count($com), 0,"There no comment for this record");


$t->is_deeply(Doctrine::getTable('Comments')->getNotionsFor('taxonomy'),
  array(
    'taxon information' => 'taxon information',
    'taxon life history' => 'taxon life history',
    'taxon sp comments' => 'taxon old sp. comments'
    ),'Notions are the same');
