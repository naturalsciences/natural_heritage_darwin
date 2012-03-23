<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');
$individual = Doctrine::getTable('SpecimenIndividuals')->findOneBySpecimenRef(4);
$indivId = $individual->getId();
$browser->
  get('/parts/edit/indid/'.$indivId)->

  with('request')->begin()->
    isParameter('module', 'parts')->
    isParameter('action', 'edit')->
  end()->
  info('1.1 - is everything ok on screen')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('title','Add Part')->
    checkElement('.board_col',2)->
  end()
;

$browser->addCustomPart($indivId);
	
$browser->
  get('parts/overview/id/'.$indivId)-> 
  with('response')->begin()->
     isStatusCode(200)->
     checkElement('title', 'Parts overview')->
     checkElement('table.catalogue_table > tbody > tr',1)->
     checkElement('table.catalogue_table > tbody > tr td:nth-child(3)','specimen')->
     checkElement('table.catalogue_table > tbody > tr td:nth-child(7)','Test for parts')->
  end()->
  
  info('Add a new part')->
  click('#tab_4')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('title','Add Part')->
  end()->

  click('Save',array(
    'specimen_parts' => array(
      'building' => 'Vestel',
      'floor' => '12a',
      'room' => '14',
      'row' => '50',
      'shelf' => 'xx15',
      'specimen_status' => 'good state',
      'complete' => '1',
    )
  ))->

  followRedirect()->
  
  get('parts/overview/id/'.$indivId)-> 
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('title', 'Parts overview')->
    checkElement('table.catalogue_table > tbody > tr',2)->
    checkElement('table.catalogue_table > tbody > tr:last td:nth-child(3)','specimen')->
    checkElement('table.catalogue_table > tbody > tr:last td:nth-child(7)','')->
    checkElement('table.catalogue_table > tbody > tr:last td:nth-child(4)','14')->
  end()->

  click('table.catalogue_table > tbody > tr:last td:nth-child(10) a')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('title','Edit part')->
    checkElement('#specimen_parts_building option',2)->
    checkElement('#specimen_parts_building option[selected]','Vestel')->
  end()->

  click('Delete')->

  with('response')->begin()->
    isRedirected()->
    followRedirect()->
  end();

$browser->
  get('parts/overview/id/'.$indivId)-> 
  with('response')->begin()->
     isStatusCode(200)->
     checkElement('title', 'Parts overview')->
     checkElement('table.catalogue_table > tbody > tr',1)->
     checkElement('table.catalogue_table > tbody > tr td:nth-child(3)','specimen')->
     checkElement('table.catalogue_table > tbody > tr td:nth-child(7)','Test for parts')->
  end()

;
  
  
