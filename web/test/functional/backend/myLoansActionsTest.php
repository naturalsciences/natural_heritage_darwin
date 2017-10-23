<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration);
$browser->login( 'root', 'evil' );

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
  end();

$browser->
  with('response')->begin()->
    checkElement('.board_col:last .widget:last .widget_top_bar span','/My Loans/')->
    checkElement('#myLoans .widget_content table tbody tr', 5)->
    checkElement('#myLoans .widget_content table tbody tr:first td:nth-child(2)','/Dragon of goyet/')->
    checkElement('#myLoans .widget_content table tbody tr:first td:last a[class=edit_loan]',1)->
  end();
$browser->
 with('response')->begin()->
   checkElement('tfoot .pager_separator', false)->
 checkElement('tfoot tr:eq(1) .add_link', 1)->  
 checkElement('tfoot tr:eq(1) .view_all', true)->   
   end();

	
