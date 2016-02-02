<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/report/index')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'Reports')->
    checkElement('#report_list > option:first_element', '')->
  end();
