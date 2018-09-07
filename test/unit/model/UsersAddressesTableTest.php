<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(3, new lime_output_color());

$u = Doctrine::getTable('Users')->getUserByPassword("root","evil");

$addresses = new UsersAddresses();
$addresses->setPersonUserRef($u->getId())
          ->setEntry('Rue Darwin2')
          ->setLocality('Paris')
          ->setCountry('France')
          ->setTag('home,pref')
          ->save();

$addresses = Doctrine::getTable('UsersAddresses')->findByPersonUserRef($u->getId());
$t->is( count($addresses) , 1, 'There is "1" new address inserted...');
$countries = Doctrine::getTable('UsersAddresses')->getDistinctCountries();
$t->is( count($countries) , 1, 'There is "1" distinct country');
$t->is( $countries[0]->getCountries() , 'France', 'And it is well "France"');
