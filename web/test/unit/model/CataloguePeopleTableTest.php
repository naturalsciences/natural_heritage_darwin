<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(23, new lime_output_color());

$taxon = Doctrine::getTable('taxonomy')->findOneByName('Falco Peregrinus');
$person1 = Doctrine::getTable('People')->findOneByFamilyNameAndGivenName('Duchesne', 'Poilux');
$person2 = Doctrine::getTable('People')->findOneByFamilyName('Root');
$person3 = Doctrine::getTable('People')->findOneByFamilyNameAndGivenName('Duchesne', 'ML');
$person1->save();
$person3->save();
$author = new CataloguePeople;
$author->setReferencedRelation('taxonomy');
$author->setRecordId($taxon->getId());
$author->setPeopleType('author');
$author->setPeopleSubType('Main Author');
$author->setPeopleRef($person1->getId());
$author->save();

$t->info('Author associated');
$expert = new CataloguePeople;
$expert->setReferencedRelation('taxonomy');
$expert->setRecordId($taxon->getId());
$expert->setPeopleType('expert');
$expert->setPeopleRef($person2->getId());
$expert->save();

$t->info('Expert associated');
$t->info('Testing the insertions');
$catpeo = Doctrine::getTable('CataloguePeople')->findForTableByType('taxonomy', $taxon->getId());
$t->is(count($catpeo), 2, '"2" were correctly inserted !');
$t->is(count($catpeo['author']), 1, 'With author key: "1" record.');
$t->is(count($catpeo['expert']), 1, 'With expert key: "1" record.');
$t->is($catpeo['author'][0]->getPeople()->getFamilyName(), 'Duchesne', 'First type is well an author and well associated to "Duchesne"');
$t->is($catpeo['expert'][0]->getPeople()->getFamilyName(), 'Root', 'First type is well an expert and well associated to "Root"');


$t->info('Testing the sub-types');
$catpeostyp = Doctrine::getTable('CataloguePeople')->getDistinctSubType();
$t->is(count($catpeostyp), 2, 'Without giving a type, we have "2" different subtypes');
$t->is($catpeostyp[''], '', 'First sub-type is well "empty"');
$t->is($catpeostyp['Main Author'], 'Main Author', 'Second sub-type is well "Main Author"');
$catpeo = Doctrine::getTable('CataloguePeople')->findByPeopleType('expert');
$catpeo[0]->setPeopleSubType('General');
$catpeo->save();


$t->info('Testing the sub-types after modifying one');
$catpeostyp = Doctrine::getTable('CataloguePeople')->getDistinctSubType();
$t->is(count($catpeostyp), 2, 'Without giving a type, we have "2" different subtypes');
$t->is($catpeostyp['General'], 'General', 'First sub-type has been well modified in "General"');
$t->is($catpeostyp['Main Author'], 'Main Author', 'Second sub-type is still well "Main Author"');


$t->info('Testing the sub-types with a given type');
$catpeostyp = Doctrine::getTable('CataloguePeople')->getDistinctSubType('author');
$t->is(count($catpeostyp), 6, 'With "author" type, we have "6" different subtypes');
$t->is($catpeostyp['Main Author'], 'Main Author', 'Only sub-type fetched is well "Main Author"');
$catpeostyp = Doctrine::getTable('CataloguePeople')->getDistinctSubType('expert');
$t->is(count($catpeostyp), 1, 'With "expert" type, we have "1" different subtypes');
$t->is($catpeostyp['General'], 'General', 'Only sub-type fetched is well "General"');


$t->info('Associating a new Author: "ML Duchesne"');
$author = new CataloguePeople;
$author->setReferencedRelation('taxonomy');
$author->setRecordId($taxon->getId());
$author->setPeopleType('author');
$author->setPeopleSubType('Main Author');
$author->setPeopleRef($person3->getId());
$author->setOrderBy(1);
$author->save();


$t->info('ML Duchesne as Author associated');
$catpeo = Doctrine::getTable('CataloguePeople')->findForTableByType('taxonomy', $taxon->getId());
$t->is(count($catpeo['author']), 2, 'With author key: "2" record.');
$t->is($catpeo['author'][0]->getPeople()->getGivenName(), 'Poilux', 'Type author and first person well associated to "Poilux"');
$t->is($catpeo['author'][1]->getPeople()->getGivenName(), 'ML', 'Type author and second person well associated to "ML"');


$t->info('Interverting order of "Poilux Duchesne" and "ML Duchesne"');
 
$catpeo = Doctrine_Query::create()
            ->from('CataloguePeople c')
            ->orderBy('referenced_relation ASC, order_by ASC, id ASC')->execute();

Doctrine::getTable('CataloguePeople')->changeOrder($catpeo[1]->getReferencedRelation(),
                                                   $catpeo[1]->getRecordId(),
                                                   'author',
                                                   array($catpeo[3]->getId(), $catpeo[1]->getId())
                                                  );
$catpeo = Doctrine::getTable('CataloguePeople')->findForTableByType('taxonomy', $taxon->getId());
$t->is($catpeo['author'][0]->getPeople()->getGivenName(), 'ML', 'Type author and first person well associated to "ML"');
$t->is($catpeo['author'][1]->getPeople()->getGivenName(), 'Poilux', 'Type author and second person well associated to "Poilux"');


$t->info('Testing the getPeopleRelated method');
$specimen = Doctrine::getTable('Specimens')->findOneByTaxonRef($taxon->getId());
$identification = new Identifications;
$identification->setReferencedRelation('specimens');
$identification->setRecordId($specimen->getId());
$identification->setNotionConcerned('taxonomy');
$identification->setValueDefined('Joli Test');
$identification->save();

$identifier = new CataloguePeople;
$identifier->setReferencedRelation('identifications');
$identifier->setRecordId($identification->getId());
$identifier->setPeopleRef($person1->getId());
$identifier->setPeopleType('identifier');
$identifier->save();

$identifiers = Doctrine::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $identification->getId());
$t->is($identifiers->count(), 1, '"One" identifier effectively created');
$t->is($identifiers[0]->getPeopleType(), 'identifier', 'New person created in catalogue people is well "identifier"');
$t->is($identifiers[0]->getPeople()->getGivenName(), 'Poilux', '"Poilux" is well the identifier created');
