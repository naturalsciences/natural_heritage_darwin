<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/chronostratigraphy/index')->
  
  with('request')->begin()->
    isParameter('module', 'chronostratigraphy')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('form#catalogue_filter table#search tbody td:first input#searchCatalogue_name', 1)->
    checkElement('form#catalogue_filter table#search tbody td:first input#searchCatalogue_table', 1)->
    checkElement('form#catalogue_filter table#search tbody td:nth-child(2) select#searchCatalogue_level_ref', 1)->
    checkElement('form#catalogue_filter table#search tbody td:nth-child(3)[class="dateNum"] input#searchCatalogue_lower_bound', 1)->
    checkElement('form#catalogue_filter table#search tbody td:nth-child(4)[class="dateNum"] input#searchCatalogue_upper_bound', 1)->
    checkElement('div.new_link', 1)->
  end()->
  info('Search without criterias')->
  click('.search_submit')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div[class="pager paging_info"] table tr td:nth-child(2)', '/12/')->
    checkElement('table.results tbody tr:nth-child(4) td:nth-child(3)', '/Invalid/')->
  end()->
  info('Search with a wrong lower bound criteria')->
  post('/catalogue/search/', array('searchCatalogue'=>array('table'=>'chronostratigraphy', 'lower_bound'=>'-500000')))->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/must be at least -4600/')->
  end()->
  info('Search with a lower bound above upper bound criteria')->
  post('/catalogue/search/', array('searchCatalogue'=>array('table'=>'chronostratigraphy', 'lower_bound'=>'-300', 'upper_bound'=>'-400')))->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/cannot be above the upper bound/')->
  end()->
  info('Search with lower bound and upper bound criteria for a result of 1 record')->
  post('/catalogue/search/', array('searchCatalogue'=>array('table'=>'chronostratigraphy', 'lower_bound'=>'-400', 'upper_bound'=>'-200')))->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div[class="pager paging_info"] table tr td:nth-child(2)', 1)->
  end()->
  info('Edit this record')->
  click('td.edit a:nth-child(3)')->
  with('response')->begin()->
    isStatusCode(200)->
    setField('chronostratigraphy[name]', '')->
    setField('chronostratigraphy[level_ref]', '')->
    setField('chronostratigraphy[upper_bound]', '3')->
  end()->
  click('Save')->
  with('form')->begin()->
    hasErrors(5)->
    isError('name', 'required')->
    isError('level_ref', 'required')->
    isError('color', 'required')->
    isError('upper_bound', 'max')->
    hasGlobalError('invalid')->
    setField('chronostratigraphy[name]', 'Proutprout')->
    setField('chronostratigraphy[level_ref]', '58')->
    setField('chronostratigraphy[upper_bound]', '-1')->
    setField('chronostratigraphy[lower_bound]', '-4800')->
    setField('chronostratigraphy[color]', '#555666')->
  end()->
  click('Save')->
  with('form')->begin()->
    hasErrors(1)->
    isError('lower_bound', 'min')->
    setField('chronostratigraphy[lower_bound]', '-20')->
  end()->
  click('Save')->
  info('Test the record has been saved in DB');

$unit = Doctrine::getTable('chronostratigraphy')->findOneByName('Proutprout');
$browser->
  test()->is($unit->getName(),'Proutprout', 'We have the new encoded unit');
$browser->
  info('Create a new record and save it then test its existence in DB')->
  get('chronostratigraphy/new')->
  with('response')->begin()->
    isStatusCode('200')->
  end()->
  click('Save', array('chronostratigraphy'=>array('name'=>'Paxien', 'color'=>'#565656','level_ref'=>'59', 'parent_ref'=>$unit->getId())));
$unit = Doctrine::getTable('chronostratigraphy')->findOneByName('Paxien');
$browser->
  test()->is($unit->getName(),'Paxien', 'We have the new encoded unit');
