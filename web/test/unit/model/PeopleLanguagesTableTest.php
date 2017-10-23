<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
 
$t = new lime_test(2, new lime_output_color());
$t->info('Get languages for person "root"');
$personId = Doctrine::getTable('People')->findOneByFamilyName('Root')->getId();
$personLanguage = new PeopleLanguages;
$personLanguage->setPeopleRef($personId);
$personLanguage->setLanguageCountry('fr_be');
$personLanguage->setPreferredLanguage(true);
$personLanguage->save();
$personLanguages = Doctrine::getTable('PeopleLanguages')->findByPeopleRef($personId);
$t->is($personLanguages[0]->getPreferredLanguage(), true, 'Language "'.$personLanguages[0]->getLanguageCountry().'" is well the "preferred" language');
$t->info('Reset now preferred languages');
Doctrine::getTable('PeopleLanguages')->removeOldPreferredLang($personId);
$personLanguages = Doctrine::getTable('PeopleLanguages')->findByPeopleRef($personId);
$t->is($personLanguages[0]->getPreferredLanguage(), false, 'Language "'.$personLanguages[0]->getLanguageCountry().'" is now "preferred" language "no more"');

