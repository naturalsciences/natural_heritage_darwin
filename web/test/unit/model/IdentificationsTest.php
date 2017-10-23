<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(6, new lime_output_color());

$fromDate = new FuzzyDateTime('2009/12/31', 32);
$identification = Doctrine::getTable('Identifications')->findOneById(1);
$t->is( $identification->getNotionDateMasked() , '<em>01/01/0001</em>', 'Correct date masked for identification "'.$identification->getId().'" for table "'.$identification->getReferencedRelation().'" and record id "'.$identification->getRecordId().'"');
$t->is( $identification->getNotionDate() , array('year'=>'', 'month'=>'', 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct date masked as array for identification "'.$identification->getId().'" for table "'.$identification->getReferencedRelation().'" and record id "'.$identification->getRecordId().'"');
$identification->setNotionDate($fromDate);
$t->is( $identification->getNotionDateMasked() , '<em>31/12</em>/2009', 'Correct date masked for identification "'.$identification->getId().'" for table "'.$identification->getReferencedRelation().'" and record id "'.$identification->getRecordId().'"');
$t->is( $identification->getNotionDate() , array('year'=>2009, 'month'=>'', 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct date masked as array for identification "'.$identification->getId().'" for table "'.$identification->getReferencedRelation().'" and record id "'.$identification->getRecordId().'"');
$identification->setNotionDate('1975/01/01');
$t->is( $identification->getNotionDateMasked() , '01/01/1975', 'Correct date masked for identification "'.$identification->getId().'" for table "'.$identification->getReferencedRelation().'" and record id "'.$identification->getRecordId().'"');
$t->is( $identification->getNotionDate() , array('year'=>1975, 'month'=>01, 'day'=>01, 'hour'=>'', 'minute'=>'', 'second'=>''), 'Correct date masked as array for identification "'.$identification->getId().'" for table "'.$identification->getReferencedRelation().'" and record id "'.$identification->getRecordId().'"');
