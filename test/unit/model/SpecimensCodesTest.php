<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(5, new lime_output_color());

$taxon = Doctrine::getTable('Taxonomy')->findOneByName('Falco Peregrinus');
$specimen = Doctrine::getTable('Specimens')->findOneByTaxonRef($taxon->getId());

$code = new Codes;
$code->setReferencedRelation('specimens');
$code->setRecordId($specimen->getId());
$code->setCodeCategory('Temporary');
$code->setCodePrefix('VERT.');
$code->setCodePrefixSeparator('/');
$code->setCodeSuffix('AFTER');
$code->setCodeSuffixSeparator('/');
$code->save();

$specCodes = Doctrine::getTable('Codes')->getCodesRelated('specimens', $specimen->getId());
$specimen->SpecimensCodes = $specCodes;

$t->is(count($specimen->SpecimensCodes), 4, '"4" codes available for specimen "'.$specimen->getId().'"');
$t->is($specimen->SpecimensCodes[0]->getCodeFormated(), 'VERT. 12456', 'The Code is well "VERT. 12456"');
$t->is($specimen->SpecimensCodes[1]->getCodeFormated(), 'VERT./1548548 Abou', 'The Code is well "VERT./1548548 Abou"');
$t->is($specimen->SpecimensCodes[2]->getCodeFormated(), 'VERT./85486846', 'The Code is well "VERT./85486846"');
$t->is($specimen->SpecimensCodes[3]->getCodeFormated(), 'VERT./-/AFTER', 'The Code is well "VERT./-/AFTER"');

