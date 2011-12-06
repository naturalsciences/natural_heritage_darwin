<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(1, new lime_output_color());

$lastIg = Doctrine::getTable('igs')->findOneByIgNum('21Ter');
$insurances = new Insurances();
$insurances->setInsuranceValue(750);
$insurances->setInsuranceCurrency('€');
$insurances->setReferencedRelation('igs');
$insurances->setRecordId($lastIg->getId());
$insurances->save();

$insurances = Doctrine::getTable('insurances')->findForTable('igs', $lastIg->getId());
$t->is( $insurances[0]->getFormatedInsuranceValue() , '750.00 €', 'Insurance value is well "750.00 €"');
