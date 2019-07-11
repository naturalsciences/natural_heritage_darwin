<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  get('/vernacularnames/vernacularnames?id=4&table=taxonomy')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->

  click('Save', array('grouped_vernacular' => array(
    'newVal' => array(
      0 => array(
      'referenced_relation' => 'taxonomy',
      'record_id'           => '4',
      'community'       => '',
      'name'       => 'Test',
    ))
  )))->

  with('response')->begin()->
    isStatusCode(200)->
  end()->
  with('form')->begin()->
    hasErrors(1)->
    isError('newVal[0][community]', 'community')->
  end()->

  click('Save', array('grouped_vernacular' => array(
   'newVal' => array(
    0 => array(
    'referenced_relation' => 'taxonomy',
    'record_id'           => '4',
    'community'       => 'Français',
    'name' => 'Faux con',
    )),
  )))->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->  

  get('/vernacularnames/addValue?num=12')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('tr',1)->
    checkElement('tr td',3)->
  end();

  $r = Doctrine::getTable('VernacularNames')->findForTable('taxonomy',4);

  $browser->
  get('/vernacularnames/addValue?num=13&id='.$r[0]->getId() )->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('tr',1)->
    checkElement('tr td',3)->
  end()
;
