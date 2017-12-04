<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$vernacular_names = new VernacularNames;

$vernacular_names->setCommunity('French');
$vernacular_names->setReferencedRelation('taxonomy');
$vernacular_names->setRecordId(4);
$vernacular_names->name ='Faux con';
$vernacular_names->save();

$personId = Doctrine::getTable('People')->findOneByFamilyNameAndGivenName('Root', 'Evil')->getId();

$catalogue_people = new CataloguePeople;
$catalogue_people->setReferencedRelation('taxonomy');
$catalogue_people->setRecordId(4);
$catalogue_people->setPeopleType('authors');
$catalogue_people->setPeopleSubType('Main authors');
$catalogue_people->setPeopleRef($personId);
$catalogue_people->save();

$browser->

    info('2 - Recombined')->
     get('/widgets/reloadContent?widget=relationRecombination&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table tbody tr',1)->
        checkElement('table tbody tr td:first a.link_catalogue','/recombinus/')->
        checkElement('img',3)->
    end();

 $items = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy', 4, 'current_name');

$browser->
    get('/catalogue/deleteRelated?table=catalogue_relationships&id='.$items[0]['id'])->
    with('response')->begin()->
      isStatusCode(200)->
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
	checkElement('table tbody tr:last td:first','/protection status/')->
    end()->

    info('5 - Vernacular Names')->
     get('/widgets/reloadContent?widget=vernacularNames&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table tbody tr',1)->
        checkElement('table tbody tr:first td:first','/French/')->
	checkElement('table tbody tr:first td::nth-child(2)','/Faux con/')->
    end()->

    info('6 - Synonymies')->
     get('/widgets/reloadContent?widget=synonym&category=catalogue_taxonomy&eid=4')->
     with('response')->begin()->
        isStatusCode(200)->
        checkElement('table.catalogue_table tbody table[alt="homonym"]',1)->
        checkElement('table.catalogue_table tbody table[alt="synonym"]',1)->
        checkElement('table.catalogue_table tbody table[alt="synonym"] tbody tr',2)->
        checkElement('table.catalogue_table tbody table[alt="synonym"] tbody td:first[class="handle"]',1)->
        checkElement('table.catalogue_table tbody table[alt="synonym"] tbody td:nth-child(2)','/Duchesnus Brulus 1912/')->
        checkElement('table.catalogue_table tbody table[alt="synonym"] tbody tr:first td:nth-child(3)[class="basio_cell"]',1)->
        checkElement('table.catalogue_table tbody table[alt="synonym"] tbody tr:first td:nth-child(4)[class="widget_row_delete"]',1)->
    end();

$items = Doctrine::getTable('ClassificationSynonymies')->findOneByReferencedRelationAndRecordId('taxonomy', 4);

$browser->
  info('8 - Synonymies after a delete')->
  get('/synonym/delete?id='.$items['id'])->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->
  get('/widgets/reloadContent?widget=synonym&category=catalogue_taxonomy&eid=4')->
    with('response')->begin()->
      isStatusCode(200)->
      checkElement('table.catalogue_table tbody table[alt="homonym"]',1)->
      checkElement('table.catalogue_table tbody table[alt="synonym"]',0)->
  end()->
  info('9 - People related')->
  get('/widgets/reloadContent?widget=cataloguePeople&category=catalogue_taxonomy&eid=4')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('table[class="widget_sub_table"] tbody td:first[class="handle"]', 1)->
    checkElement('table[class="widget_sub_table"] tbody td:nth-child(2)', '/Root Evil/')->
    checkElement('table[class="widget_sub_table"] tbody td:nth-child(3)[class="catalogue_people_sub_type"]', 1)->
    checkElement('table[class="widget_sub_table"] tbody td:last[class="widget_row_delete"]', 1)->
  end()->
  click('table[class="widget_sub_table"] tbody td:last[class="widget_row_delete"] > a')->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->
  get('/widgets/reloadContent?widget=cataloguePeople&category=catalogue_taxonomy&eid=4')->
    with('response')->begin()->
      isStatusCode(200)->
      checkElement('table[class="widget_sub_table"]', 0)->
  end();
