<?php                                                                                     
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');                               
$t = new lime_test(4, new lime_output_color());                                          

$t->diag('fetchByIgNumLimited');

$igs = Doctrine::getTable('Igs')->fetchByIgNumLimited('26', 10);

$t->is(count($igs),4,'We got 4 ig like that');

$igs = Doctrine::getTable('Igs')->fetchByIgNumLimited('26', 2);

$t->is(count($igs),2,'We limit the result to 2');

$igs = Doctrine::getTable('Igs')->fetchByIgNumLimited('21t', 10);

$t->is(count($igs),1,'only one correspond. it does the full2index');

$igs = Doctrine::getTable('Igs')->fetchByIgNumLimited('666', 10);

$t->is(count($igs),0,'pfiou... no ig is 666 \o/');