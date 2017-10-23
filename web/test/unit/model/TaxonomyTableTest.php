<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(14, new lime_output_color());

$taxs = Doctrine::getTable('Taxonomy')->findOneByName('Falco Peregrinus eliticus');
$t->info('findWithParents($id)');
$taxa = Doctrine::getTable('Taxonomy')->findWithParents($taxs->getId());
$t->isnt($taxa,null, 'we got a taxa');
$t->is($taxa->count(),9, 'we got all parent of the taxa');
$t->is($taxa[7]->getId(),$taxs->getParentRef(), 'Parent is correct');

$t->is($taxa[1]->Level->__toString(),'kingdom', 'get Level');
$t->is($taxs->getNameWithFormat(),'<i>Falco Peregrinus eliticus</i>', 'get Name without extinct');

$taxs->setExtinct('true');

$t->is($taxs->getNameWithFormat(),'<i>Falco Peregrinus eliticus</i> †', 'get Name without extinct');

$t->is(DarwinTable::getFilterForTable('classification_syonymies'),"ClassificationSyonymiesFormFilter",'Filter Form name');
$t->is(DarwinTable::getFormForTable('classification_syonymies'),"ClassificationSyonymiesForm",'Form Name');
$t->is(DarwinTable::getModelForTable('classification_syonymies'),"ClassificationSyonymies",'Model Name');

$t->is(Doctrine::getTable('Taxonomy')->find(4)->toArray(),true,'We got the record with find');
$t->is(Doctrine::getTable('Taxonomy')->find(-1)->toArray(),true,'Record bellow 0 are found  with find');

$keywords = Doctrine::getTable('ClassificationKeywords')->findForTable('taxonomy', 4);
$t->is(count($keywords),0,'No KW per default');

$kw_full = ClassificationKeywords::getTags('taxonomy');
$avail_kw = array_keys($kw_full);

$kw = new ClassificationKeywords();
$kw->setReferencedRelation('taxonomy');
$kw->setRecordId(4);
$kw->setKeywordType($avail_kw[1]);
$kw->setKeyword('Falco Peregrinus');
$kw->save();


$keywords = Doctrine::getTable('ClassificationKeywords')->findForTable('taxonomy', 4);
$t->is(count($keywords),1,'The new Keyword');
$t->is($keywords[0]->getKeywordType(),$avail_kw[1],'We get the new keyword');
