<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/institution/index')->
  
  with('request')->begin()->
    isParameter('module', 'institution')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.search_results_content')->
    checkElement('#institutions_filters_family_name')->
  end()->


  click('.search_submit', array('institutions_filters' => array(
    'family_name' => 'royal',
    'is_physical' => 0,)
    )
  )->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.results_container')->
    checkElement('.results_container tbody tr',1)->
  end()->

  get('/institution/index')->
  click('.search_submit', array('institutions_filters' => array(
    'family_name' => 'ntol',
    'is_physical' => 0,)
    )
  )->

  with('response')->begin()->
    isStatusCode(200)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/No Matching Items/','Content is ok');

  $browser->
  get('/institution/index')->
  click('New')->

  with('response')->begin()->
    isStatusCode(200)->
  end()->

  click('Save', array('institutions' => array(
    'family_name'  => '',
    'additional_names' => '',
    'sub_type' => '',
  )))->

  with('form')->begin()->
    hasErrors(1)->
    isError('family_name', 'required')->
  end()->

  click('Save', array('institutions' => array(
    'family_name'  => 'Banque Bruxelles Lambert',
    'additional_names' => 'BBL',
    'sub_type' => 'inc',
  )))->

  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect();

$nitems = Doctrine::getTable('Institutions')->findByAdditionalNames('BBL');

  $browser->
  test()->is($nitems[0]->getFamilyName(),'Banque Bruxelles Lambert', 'We have the new encoded institution');

  $browser->
  with('request')->begin()->
    isParameter('module', 'institution')->
    isParameter('action', 'edit')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
  end()->

  click('Save', array('institutions' => array(
    'family_name'  => 'Ing',
    'additional_names' => 'Ing',
    'sub_type' => 'inc',
  )))->

  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'institution')->
    isParameter('action', 'edit')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('input[value="Ing"]')->

  end()->

  click('Delete')->
  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'institution')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end();

$nitems = Doctrine::getTable('Institutions')->findByAdditionalNames('Ing');

  $browser->
  test()->is($nitems->count(),0, 'We have no matching institutions');
