<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(3, new lime_output_color());

$u = Doctrine::getTable('People')->searchPysical('Poilux');

$addresses = new PeopleAddresses();
$addresses->setPersonUserRef($u[0]->getId())
          ->setEntry('Rue Darwin2')
          ->setLocality('Paris')
          ->setCountry('France')
          ->setTag('home,pref')
          ->save();

$addresses = Doctrine::getTable('PeopleAddresses')->findByPersonUserRef($u[0]->getId());
$t->is( count($addresses) , 1, 'There s a new address inserted...');
$countries = Doctrine::getTable('PeopleAddresses')->getDistinctCountries();
$t->is( count($countries) , 1, 'There s one distinct country');
$t->is( $countries[0]->getCountries() , 'France', 'And it is well France');
