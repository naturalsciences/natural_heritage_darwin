<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(4, new lime_output_color());

$lastIg = Doctrine::getTable('taxonomy')->find('4');

$vernacular_names = new VernacularNames;

$vernacular_names->setCommunity('Français');
$vernacular_names->setReferencedRelation('taxonomy');
$vernacular_names->setRecordId($lastIg->getId());
$vernacular_names->name ='Faux con';
$vernacular_names->save();

$vernacular_names = new VernacularNames;

$vernacular_names->setCommunity('Wallon');
$vernacular_names->setReferencedRelation('taxonomy');
$vernacular_names->setRecordId($lastIg->getId());
$vernacular_names->name ='Tchat';
$vernacular_names->save();

$vernacular_names = Doctrine::getTable('VernacularNames')->findForTable('taxonomy',4);
$t->is( count($vernacular_names) , 2, 'There is vernacular names for this table / record_id');
$t->is( $vernacular_names[0]->getCommunity() , 'Français', 'Community well inserted');

$communities = Doctrine::getTable('VernacularNames')->getDistinctCommunities();
$t->is( count($communities) , 2, 'There are 2 different communities');
$t->is( $communities[1]->getCommunity() , 'Wallon', 'and are accessible through getCommunity');
