<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/lithology/index')->
  
  with('request')->begin()->
    isParameter('module', 'lithology')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('form#catalogue_filter table#search tbody td:first input#searchCatalogue_name', 1)->
    checkElement('form#catalogue_filter table#search tbody td:first input#searchCatalogue_table', 1)->
    checkElement('form#catalogue_filter table#search tbody td:nth-child(2) select#searchCatalogue_level_ref', 1)->
    checkElement('div.new_link', 1)->
  end()->
  info('Search without criterias')->
  click('.search_submit')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div[class="pager paging_info"] table tr td:nth-child(2)', '/10/')->
    checkElement('table.results tbody tr:nth-child(6) td:nth-child(3)', '/Invalid/')->
  end()->
  info('Edit this record')->
  click('table.results tbody tr:nth-child(6) td.edit a:nth-child(3)')->
  with('response')->begin()->
    isStatusCode(200)->
    setField('lithology[name]', '')->
    setField('lithology[level_ref]', '')->
  end()->
  click('Save')->
  with('form')->begin()->
    hasErrors(3)->
    isError('name', 'required')->
    isError('level_ref', 'required')->
    isError('color', 'required')->
    setField('lithology[name]', 'Proutprout')->
    setField('lithology[level_ref]', '77')->
    setField('lithology[color]', '#000000')->
  end()->
  click('Save')->
  info('Test the record has been saved in DB');

$unit = Doctrine::getTable('lithology')->findOneByName('Proutprout');
$browser->
  test()->is($unit->getName(),'Proutprout', 'We have the new encoded unit');
$browser->
  info('Create a new record and save it then test its existence in DB')->
  get('lithology/new')->
  with('response')->begin()->
    isStatusCode('200')->
  end()->
  click('Save', array('lithology'=>array('name'=>'Paxien','color'=>'#000000','level_ref'=>'78', 'parent_ref'=>$unit->getId())));
$unit = Doctrine::getTable('lithology')->findOneByName('Paxien');
$browser->
  test()->is($unit->getName(),'Paxien', 'We have the new encoded unit');
