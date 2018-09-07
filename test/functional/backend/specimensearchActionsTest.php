<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  get('/specimensearch/index')->

  with('request')->begin()->
    isParameter('module', 'specimensearch')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '!/This is a temporary page/')->
    checkElement('ul#taxon_child_syn_included input[name="specimen_search_filters[taxon_child_syn_included]"][type="checkbox"]')->
    checkElement('ul#taxon_child_syn_included input[name="specimen_search_filters[taxon_child_syn_included]"][type="checkbox"][checked="checked"]', '')->
  end();
