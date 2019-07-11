<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/mineralogy/index')->
  
  with('request')->begin()->
    isParameter('module', 'mineralogy')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('form#catalogue_filter table#search tbody td:first ul li input#searchCatalogue_code', 1)->
    checkElement('form#catalogue_filter table#search tbody td:nth-child(2) input#searchCatalogue_name', 1)->
    checkElement('form#catalogue_filter table#search tbody td:nth-child(2) input#searchCatalogue_table', 1)->
    checkElement('form#catalogue_filter table#search tbody td:nth-child(3) select#searchCatalogue_classification', 1)->
    checkElement('form#catalogue_filter table#search tbody td:nth-child(4) select#searchCatalogue_level_ref', 1)->
    checkElement('div.new_link', 1)->
  end()->
  info('Search with Dana classification -> no records encoded')->
  post('/catalogue/search/', array('searchCatalogue'=>array('table'=>'mineralogy', 'classification'=>'dana')))->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/no matching items/i')->
  end()->
  info('Search without criterias')->
  get('/mineralogy/index')->
  with('response')->begin()->
    isStatusCode('200')->
  end()->
  click('.search_submit')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div[class="pager paging_info"] table tr td:nth-child(2)', '/11/')->
    checkElement('table.results tbody tr:first td:nth-child(4)', '/Invalid/')->
  end()->
  info('Edit invalid record')->
  click('table.results tbody tr:first td.edit a:nth-child(3)')->
  with('response')->begin()->
    isStatusCode(200)->
    setField('mineralogy[code]', '')->
    setField('mineralogy[name]', '')->
    setField('mineralogy[level_ref]', '')->
    setField('mineralogy[classification]', '')->
  end()->
  click('Save')->
  with('form')->begin()->
    hasErrors(5)->
    isError('code', 'required')->
    isError('name', 'required')->
    isError('level_ref', 'required')->
    isError('classification', 'required')->
    isError('color', 'required')->
    setField('mineralogy[code]', '5.AA')->
    setField('mineralogy[name]', 'Proutprout')->
    setField('mineralogy[level_ref]', '72')->
    setField('mineralogy[classification]', 'Bouc')->
    setField('mineralogy[color]', '#555555')->
  end()->
  click('Save')->
  with('form')->begin()->
    hasErrors(1)->
    isError('classification', 'invalid')->
    setField('mineralogy[classification]', 'strunz')->
  end()->
  click('Save')->
  info('Test the record has been saved in DB');

$unit = Doctrine::getTable('mineralogy')->findOneByName('Proutprout');
$browser->
  test()->is($unit->getName(),'Proutprout', 'We have the modified unit');
$browser->
  info('Create a new record and save it then test its existence in DB')->
  get('mineralogy/new')->
  with('response')->begin()->
    isStatusCode('200')->
  end()->
  click('Save', array('mineralogy'=>array('code'=>'5.AAH','color'=>'#554554', 'name'=>'Paxien', 'level_ref'=>'73', 'parent_ref'=>$unit->getId())));
$unit = Doctrine::getTable('mineralogy')->findOneByName('Paxien');
$browser->
  test()->is($unit->getName(),'Paxien', 'We have the new encoded unit');
