<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/gtu/index')->
  with('response')->
  begin()->
    isStatusCode(200)->
    checkElement('h1', 'Sampling location search')->
    checkElement('#gtu_filters_code', '')->
    checkElement('#gtu_filters_gtu_from_date_day > option:first_element', 'dd')->
    checkElement('#gtu_filters_gtu_from_date_month > option:first_element', 'mm')->
    checkElement('#gtu_filters_gtu_from_date_year > option:first_element', 'yyyy')->
    checkElement('#gtu_filters_gtu_to_date_day > option:first_element', 'dd')->
    checkElement('#gtu_filters_gtu_to_date_month > option:first_element', 'mm')->
    checkElement('#gtu_filters_gtu_to_date_year > option:first_element', 'yyyy')->
    checkElement('form input[type="submit"]')->
  end()->
  click('input[type="submit"]', array('gtu_filters' => array('gtu_from_date' => array('month' => 10))))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('.error_list li', '/Year missing./')->
  end()->

  get('/gtu/index')->
  click('input[type="submit"]', array('gtu_filters' => array('Tags' => array(0 => array('tag' => 'Belgique')))))->
  with('response')->
  begin()->
    checkElement('.paging_info')->
    checkElement('.results tbody tr',2)->
  end()->

  get('/gtu/index')->
  click('input[type="submit"]', array('gtu_filters' => array('Tags' => array(0 => array('tag' => 'Belgique; Antartica')))))->
  with('response')->
  begin()->
    checkElement('.paging_info')->
    checkElement('.results tbody tr',3)->
  end()->

  get('/gtu/index')->
  click('input[type="submit"]', array('gtu_filters' => array('Tags' => array(0 => array('tag' => 'Belgique; Antartica'), 1 => array('tag' => 'Belgïe')))))->
  with('response')->
  begin()->
    checkElement('.paging_info')->
    checkElement('.results tbody tr',1)->
  end()->

  info('New')->

  get('/gtu/index')->
  click('New')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'New Sampling Location')->
    checkElement('#gtu_code')->
    checkElement('.gtu_groups_add')->
  end()->
  click('Save', array('gtu' => 
    array(
      'code' => 'Brol'
    ))
  )->
 with('response')->begin()->
    isRedirected()->
 end()->

 followRedirect()->
 with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'Edit Sampling Location')->
    checkElement('input[value="Brol"]')->
    checkElement('.gtu_groups_add')->
  end()->

  click('Save', array('gtu' => 
    array(
      'code' => 'Brol2',
      'newVal' => array(2 =>
	array(
	  'group_name' => 'administrative area',
	  'sub_group_name' => 'City',
	  'tag_value' => 'Brussels; Ici',
	))
    ))
  )->
 with('response')->begin()->
    isRedirected()->
 end()->

 followRedirect()->

 with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'Edit Sampling Location')->
    checkElement('input[value="Brol2"]')->
  end()->

  info('Tags')->
  get('gtu/purposeTag?group_name=administrative%20area&sub_group_name=city&value=Brussels;Bruxelles')->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('li.tag_size_5','Bruselas')->
    checkElement('li.tag_size_3','Bruselo')->
  end()->
  
  get('gtu/purposeTag?group_name=administrative%20area&sub_group_name=city&value=Brussels;Bruxelles;Bruselas')->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('li.tag_size_5','Bruselo')->
  end()->

  get('gtu/purposeTag?value=Brussels;Bruxelles;Bruselas')->

  with('response')->begin()->
    isStatusCode(200)->
  end();
  
