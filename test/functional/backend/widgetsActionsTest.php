<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('1 - Add hidden widget')->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.board_col:last .widget',4)->
  end()->

  info('1.1 - no widget when it is the bad category')->
  get('/widgets/changeStatus?category=specimen&widget=addTaxon&status=visible')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.board_col:last .widget',4)->
  end()->


  info('1.2 - the other category is still not changed')->
  get('/specimen/new')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',11)->
    checkElement('.board_col:last .widget',15)->
  end()->
  
  info('1.3 - add widget when the category is ok')->
  get('/widgets/changeStatus?category=board&widget=addTaxon&status=visible')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.board_col:last .widget',4)->
  end()->

//-------------------- 
  info('2 - hide a visible widget')->
  get('/widgets/changeStatus?category=board&widget=addTaxon&status=hidden')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.board_col:last .widget',3)->
  end()->
  info('2.1 - hide a hidden widget')->
  get('/widgets/changeStatus?category=board&widget=addTaxon&status=hidden')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.board_col:last .widget',3)->
  end()->

//-------------------- 
  info('3 - expand a widget')->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.widget_content.hidden',2)->
  end()->

  get('/widgets/changeStatus?category=board&widget=savedSearch&status=close')->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.widget_content.hidden',2)->
  end()->

  get('/widgets/changeStatus?category=board&widget=savedSearch&status=open')->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.widget_content.hidden',1)->
  end()->
  
//-----------------------
  info('4 - ChangeOrder')->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.board_col:first .widget:first .widget_top_bar span','/My saved specimens/')-> //First widget
    checkElement('.board_col:first .widget:nth-child(2) .widget_top_bar span','/My saved searches/')->//Second widget
  end()->
  get('/widgets/changeOrder?category=board&col1=savedSearch,savedSpecimens&col2=')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',2)->
    checkElement('.board_col:first .widget:first .widget_top_bar span','/My saved searches/')-> //First widget
    checkElement('.board_col:first .widget:nth-child(2) .widget_top_bar span','/My saved specimens/')->//Second widget
  end()->
  
  
  info('4.1 - change everybody to the 2th col')->
  get('/widgets/changeOrder?category=board&col2=savedSpecimens,savedSearch&col1=')-> // RE set to the previous position in col2
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',0)->
    checkElement('.board_col:last .widget',5)->
    checkElement('.board_col:last .widget:first .widget_top_bar span','/Add specimens/')-> //First widget
    checkElement('.board_col:last .widget:nth-child(2) .widget_top_bar span','/My saved specimens/')->//Second widget
  end()->
  
  
  info('4.2 - set 1 widget in each col')->
  get('/widgets/changeOrder?category=board&col1=savedSearch&col2=savedSpecimens')-> // set only one in each col
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',1)->
    checkElement('.board_col:last .widget',4)->
    checkElement('.board_col:first .widget:first .widget_top_bar span','/My saved searches/')-> 
    checkElement('.board_col:last .widget:first .widget_top_bar span','/Add specimens/')->
  end()->


  info('4.3 - no change when this is another category')->
  get('/widgets/changeOrder?category=specimen&col1=savedSearch&col2=savedSpecimens')-> // set only one in each col
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',1)->
    checkElement('.board_col:last .widget',4)->
    checkElement('.board_col:first .widget:first .widget_top_bar span','/My saved searches/')-> 
    checkElement('.board_col:last .widget:first .widget_top_bar span','/Add specimens/')->
  end()->


  info('4.4 - not represented is just not changed')->
  get('/widgets/changeOrder?category=board&col1=savedSearch&col2=')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  get('/board/index')->
  with('response')->begin()->
    checkElement('.board_col:first .widget',1)->
    checkElement('.board_col:last .widget',4)->
    checkElement('.board_col:first .widget:first .widget_top_bar span','/My saved searches/')->
    checkElement('.board_col:last .widget:first .widget_top_bar span','/Add specimens/')->
  end()
;
