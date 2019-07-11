<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Save Search Page')->
  get('/savesearch/index')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'My saved searches')->
    checkElement('table.saved_searches')->
    checkElement('table.saved_searches tbody tr',2)->
    checkElement('table.saved_searches tbody tr div.search_name',2)->
    checkElement('table.saved_searches tbody tr div.date',2)->
    checkElement('table.saved_searches tbody tr:first div.search_name','/All specimens encoded by me/')->
    checkElement('table.saved_searches tbody tr:first .favorite_off.hidden')->
  end()->

  info('Save Specimen Page')->
  get('/savesearch/index?specimen=1')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'My saved specimens')->
    checkElement('table.saved_searches')->
    checkElement('table.saved_searches tbody tr',1)->
    checkElement('table.saved_searches tbody tr div.search_name',1)->
    checkElement('table.saved_searches tbody tr div.date',1)->
    checkElement('table.saved_searches tbody tr:first div.search_name','/Specimen 4/')->
    checkElement('table.saved_searches tbody tr:first span.saved_count','/1 Item/')->
    checkElement('table.saved_searches tbody tr:first .favorite_off.hidden')->
  end()->

  info('Pin / Unpin')->

  get('/savesearch/pin?id=1&status=1&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    matches('/ok/')->
  end()->

  get('/savesearch/pin?id=4&status=1&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    matches('/ok/')->
  end()->

  get('specimensearch/search?pinned=true&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('table tbody tr',2)->
  end()->

  get('/savesearch/pin?id=4&status=0&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    matches('/ok/')->
  end()->

  get('specimensearch/search?pinned=true&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('table tbody tr',1)->
  end()->

  info('Save pin to new spec search')->
  
  get('savesearch/saveSearch?type=pin&cols=gtu|count&list_nr=create&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('table label','/Title/')->
    checkElement('h2','/Visibility of fields /')->
  end()->
  click('#save')->
  with('response')->
  begin()->
    isStatusCode()->
    matches('/ok/')->
  end()->

  get('specimensearch/search?pinned=true&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('.search_results_content','/No Specimen Matching/')->
  end()->

  get('/savesearch/index?specimen=1')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'My saved specimens')->
    checkElement('table.saved_searches')->
    checkElement('table.saved_searches tbody tr',2)->
    checkElement('table.saved_searches tbody tr:first span.saved_count','/1 Item/')->
    checkElement('table.saved_searches tbody tr:last span.saved_count','/1 Item/')->
  end();

$searches = Doctrine_Query::create()
            ->from('MySavedSearches')
            ->andwhere('is_only_id = true')
            ->orderBy('favorite DESC, id ASC')->execute();
$old_search = $searches[0];
$new_search = $searches[1];
  $browser->
  info('Save pin to existing spec search')->

  get('/savesearch/pin?id=1&status=1&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    matches('/ok/')->
  end()->

  get('savesearch/saveSearch?type=pin&source=specimen&cols=gtu|count&list_nr='.$old_search->getId())->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('table label','/Title/')->
    checkElement('h2','/Visibility of fields /')->
  end()->
  click('#save')->
  with('response')->
  begin()->
    isStatusCode()->
    matches('/ok/')->
  end()->

  get('specimensearch/search?pinned=true&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('.search_results_content','/No Specimen Matching/')->
  end()->

  get('/savesearch/index?specimen=1&source=specimen')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'My saved specimens')->
    checkElement('table.saved_searches')->
    checkElement('table.saved_searches tbody tr.r_id_'.$old_search->getId())->
    checkElement('table.saved_searches tbody tr',2)->
    checkElement('table.saved_searches tbody tr:first span.saved_count','/2 Items/')->
    checkElement('table.saved_searches tbody tr:last span.saved_count','/1 Item/')->
  end()->

  info('Fav / unfav a search')-> 

  get('/savesearch/favorite?status=0&id='.$old_search->getId())->
  with('response')->
  begin()->
    isStatusCode()->
    matches('/ok/')->
  end()->

  get('/savesearch/favorite?status=1&id='.$new_search->getId())->
  with('response')->
  begin()->
    isStatusCode()->
    matches('/ok/')->
  end()->


  info('Delete a search')->

  get('/savesearch/index?specimen=1')->

  click('table.saved_searches tbody tr:first .del_butt')->
  with('response')->
  begin()->
    isStatusCode(302)->
  end()->

  get('/savesearch/index?specimen=1')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'My saved specimens')->
    checkElement('table.saved_searches')->
    checkElement('table.saved_searches tbody tr.r_id_'.$old_search->getId())->
    checkElement('table.saved_searches tbody tr',1)->
    checkElement('table.saved_searches tbody tr:first span.saved_count','/2 Items/')->
  end()
;

  
