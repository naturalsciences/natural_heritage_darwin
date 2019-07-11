<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('1 - GetBoard')->
  get('/board/index')->

  with('request')->begin()->
    isParameter('module', 'board')->
    isParameter('action', 'index')->
  end()->

  info('1.1 - is everything ok on the board')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('title','Dashboard')->
    checkElement('.board_col',2)->
    checkElement('.board_col:first .widget', 2)->
    checkElement('.board_col:last .widget', 4)->
    checkElement('.board_col:first .widget:first .widget_top_bar span','/My saved specimens/')->
  end()
;
