<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(22, new lime_output_color());

$taxon = Doctrine::getTable('Taxonomy')->findOneByName('Falco Peregrinus');
$specimen = Doctrine::getTable('Specimens')->findOneByTaxonRef($taxon->getId());
$code = new Codes;
$code->setReferencedRelation('specimens');
$code->setRecordId($specimen->getId());
$code->setCodeCategory('Temporary');
$code->setCodePrefix('VERT.');
$code->setCodePrefixSeparator('/');
$code->setCodeSuffix('AFTER');
$code->setCodeSuffixSeparator('*');
$code->save();

$codes = Doctrine::getTable('Codes')->getCodesRelated('specimens', $specimen->getId());

$t->is(count($codes), 4, '"4" codes available for specimen "'.$specimen->getId().'"');
$t->is($codes[0]->getCodeFormated(), 'VERT. 12456', 'The Code is well "VERT. 12456"');
$t->is($codes[1]->getCodeFormated(), 'VERT./1548548 Abou', 'The Code is well "VERT./1548548 Abou"');
$t->is($codes[2]->getCodeFormated(), 'VERT./85486846', 'The Code is well "VERT./85486846"');
$t->is($codes[3]->getCodeFormated(), 'VERT./-*AFTER', 'The Code is well "VERT./-*AFTER"');

$specimens = Doctrine_Query::create()
            ->from('Specimens')
            ->orderBy('collection_ref ASC, id ASC')->execute();
$specIds = array();
foreach ($specimens as $key=>$value)
{
  $specIds[$value->getId()]=$value->getId();
}

$codes = Doctrine::getTable('Codes')->getCodesRelatedArray('specimens', $specIds);

$t->is(count($codes), 7, '"4" codes available');
$t->is($codes[0]->getCodeFormated(), 'INV.-123-Neo', 'The Code is well "INV.-123-Neo"');
$t->is($codes[1]->getCodeFormated(), 'VERT. 12456', 'The Code is well "VERT. 12456"');
$t->is($codes[2]->getCodeFormated(), 'VERT./1548548 Abou', 'The Code is well "VERT./1548548 Abou"');
$t->is($codes[3]->getCodeFormated(), 'VERT./85486846', 'The Code is well "VERT./85486846"');
$t->is($codes[4]->getCodeFormated(), 'VERT./-*AFTER', 'The Code is well "VERT./-*AFTER"');
$t->is($codes[5]->getCodeFormated(), 'TEST-12345-AFTER', 'The Code is well "TEST-12345-AFTER"');
$t->is($codes[6]->getCodeFormated(), 'TEST-54321-AFTER', 'The Code is well "TEST-54321-AFTER"');

$codesPrefix = Doctrine::getTable('Codes')->getDistinctPrefixSep();

$t->is($codesPrefix->count(), 3, '"3" different prefix separators exists');
$t->is($codesPrefix[0]->getCodePrefixSeparator(), ' ', '"First" code prefix separator is " "');
$t->is($codesPrefix[1]->getCodePrefixSeparator(), '-', '"Second" code prefix separator is "-"');
$t->is($codesPrefix[2]->getCodePrefixSeparator(), '/', '"Third" code prefix separator is "/"');

$codesSuffix = Doctrine::getTable('Codes')->getDistinctSuffixSep();

$t->is($codesSuffix->count(), 4, '"4" different suffix separators exists');
$t->is($codesSuffix[0]->getCodeSuffixSeparator(), ' ', '"First" code suffix separator is " "');
$t->is($codesSuffix[1]->getCodeSuffixSeparator(), '-', '"Second" code suffix separator is "-"');
$t->is($codesSuffix[2]->getCodeSuffixSeparator(), '/', '"Third" code suffix separator is "/"');
$t->is($codesSuffix[3]->getCodeSuffixSeparator(), '*', '"Third" code suffix separator is "*"');