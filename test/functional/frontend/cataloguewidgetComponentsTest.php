<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$vernacular_names = new ClassVernacularNames;

$vernacular_names->setCommunity('French');
$vernacular_names->setReferencedRelation('taxonomy');
$vernacular_names->setRecordId(4);
$vernacular_names->VernacularNames[]->name ='Faux con';
$vernacular_names->VernacularNames[]->name ='Con de pélerin';
$vernacular_names->save();

$browser->
    info('1 - Rename')->
     get('/widgets/reloadContent?widget=relationRename&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table tr',1)->
        checkElement('table tr td:first a.link_catalogue','/Falco Peregrinus Tunstall/')->
	checkElement('img', 2)->
    end()->

    info('2 - Recombined')->
     get('/widgets/reloadContent?widget=relationRecombination&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table tr',1)->
        checkElement('table tr td:first a.link_catalogue','/recombinus/')->
	checkElement('img',2)->
    end();

 $items = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy', 4, 'current_name');

$browser->
    get('/catalogue/deleteRelated?table=catalogue_relationships&id='.$items[0]['id'])->
    with('response')->begin()->
      isStatusCode(200)->
    end()->

     get('/widgets/reloadContent?widget=relationRename&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table tr',0)->
	checkElement('img',1)->
    end();

$browser->
    info('3 - Comment')->
     get('/widgets/reloadContent?widget=comment&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table tr',2)->
        checkElement('table tr:last a.link_catalogue','/taxon information/')->
	checkElement('table tr:last td:nth-child(2)','/This is bullshit/')->
    end()->

    info('4 - Properties')->
     get('/widgets/reloadContent?widget=properties&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table tbody tr',2)->
        checkElement('table tbody tr:first td:first','/physical measurement/')->
	checkElement('table tbody tr:first td::nth-child(6)','/Show 2 Values/')->
	checkElement('table tbody tr:last td:first','/protection status/')->
	checkElement('table tbody tr:last td::nth-child(6)','/Show 1 Value/')->
    end()->

    info('5 - Vernacular Names')->
     get('/widgets/reloadContent?widget=vernacularNames&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table tbody tr',1)->
        checkElement('table tbody tr:first td:first','/French/')->
	checkElement('table tbody tr:first td::nth-child(2)','/Show 2 Names/')->
    end();