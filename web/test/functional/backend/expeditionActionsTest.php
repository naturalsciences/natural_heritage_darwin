<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/expedition/index')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'Expedition Search')->
    checkElement('#searchExpedition_name', '')->
    checkElement('#searchExpedition_expedition_from_date_day > option:first_element', 'dd')->
    checkElement('#searchExpedition_expedition_from_date_month > option:first_element', 'mm')->
    checkElement('#searchExpedition_expedition_from_date_year > option:first_element', 'yyyy')->
    checkElement('#searchExpedition_expedition_to_date_day > option:first_element', 'dd')->
    checkElement('#searchExpedition_expedition_to_date_month > option:first_element', 'mm')->
    checkElement('#searchExpedition_expedition_to_date_year > option:first_element', 'yyyy')->
    checkElement('form input[type="submit"]', 1)->
    setField('#searchExpedition_expedition_from_date_month', '10')->
  end()->
  info('Post waiting for a "Year missing" error')->
  post('/expedition/search', array('searchExpedition'=>array('expedition_from_date'=>array('month'=>10))))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('.error_list li', 'Year missing.')->
  end()->  
  info('Post waiting for a "Month missing" error')->
  post('/expedition/search', array('searchExpedition'=>array('expedition_from_date'=>array('day'=>2, 'year'=>1975))))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('.error_list li', 'Month missing or remove the day and time.')->
  end()->  
  info('Post waiting for the full results table and the pager')->
  post('/expedition/search', array('is_choose'=>0, 'searchExpedition'=>array('name'=>'', 'expedition_from_date'=>'', 'expedition_to_date'=>'')))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('ul.pager_nav li', 10)->
    checkElement('ul.pager_nav li.page_selected', '[1]')->
    checkElement('.pager li a span.nav_arrow', 0)->
    checkElement('div.paging_info table td:nth-child(2)', 1)->
    checkElement('div.paging_info table td:last-child select[id="searchExpedition_rec_per_page"]', 1)->
    checkElement('table.results tbody tr', 8)->
    checkElement('table.results thead th:first_element a.sort span.order_sign_down')->
  end()->  
  info('Click to sort on name descending...')->
  post('/expedition/search', array('orderby'=>'name', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchExpedition'=>array('name'=>'', 'expedition_from_date'=>'', 'expedition_to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results thead th:first_element a.sort span.order_sign_up')->
  end()->
  info('Select a number of 5 records per pages and test existence of links in pager')->
  post('/expedition/search', array('orderby'=>'name', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchExpedition'=>array('rec_per_page'=>5, 'name'=>'', 'expedition_from_date'=>'', 'expedition_to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('.pager_nav .pager_separator')->
    checkElement('.pager_nav li a span.nav_arrow', 4)->
    checkElement('table.results tbody tr', 5)->
  end()->
  info('Select page 2')->
  post('/expedition/search', array('orderby'=>'name', 'orderdir'=>'desc', 'page'=>2, 'is_choose'=>0, 'searchExpedition'=>array('rec_per_page'=>5, 'name'=>'', 'expedition_from_date'=>'', 'expedition_to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('.pager .pager_separator')->
    checkElement('ul.pager_nav li.page_selected', '[2]')->
    checkElement('.pager_nav li a span.nav_arrow', 4)->
    checkElement('table.results tbody tr', 3)->
  end()->
  info('Search on Expedition name "Antar" like')->
  post('/expedition/search', array('orderby'=>'name', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchExpedition'=>array('name'=>'Antar', 'expedition_from_date'=>'', 'expedition_to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 1)->
  end()->
  info('Search on Expedition from 2004')->
  post('/expedition/search', array('orderby'=>'name', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchExpedition'=>array('name'=>'', 'expedition_from_date'=>array('day'=>'', 'month'=>'', 'year'=>'2004'), 'expedition_to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 3)->
  end()->
  info('Search on Expedition from 24/12/2002')->
  post('/expedition/search', array('orderby'=>'name', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchExpedition'=>array('name'=>'', 'expedition_from_date'=>array('day'=>'24', 'month'=>'12', 'year'=>'2002'), 'expedition_to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 4)->
  end()->
  info('Search on Expedition from 25/12/2002')->
  post('/expedition/search', array('orderby'=>'name', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchExpedition'=>array('name'=>'', 'expedition_from_date'=>array('day'=>'25', 'month'=>'12', 'year'=>'2002'), 'expedition_to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 3)->
  end()->
  info('Get new record')->
  get('/expedition/index')->
  click('New')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'New Expedition')->
    checkElement('h1[class="edit_mode"]', true)->
    checkElement('form[class="edition"]', true)->
    checkElement('#expeditions_expedition_from_date_day > option:first_element', 'dd')->
    checkElement('#expeditions_expedition_from_date_month > option:first_element', 'mm')->
    checkElement('#expeditions_expedition_from_date_year > option:first_element', 'yyyy')->
    checkElement('#expeditions_expedition_to_date_day > option:first_element', 'dd')->
    checkElement('#expeditions_expedition_to_date_month > option:first_element', 'mm')->
    checkElement('#expeditions_expedition_to_date_year > option:first_element', 'yyyy')->
    checkElement('form tfoot a', 'Cancel')->
    checkElement('form input[type="submit"][value="Save"]', 1)->
  end()->
  info('Try to save without data')->
  click('Save', 
        array('expeditions' => array('name'  => '',
                                     'expedition_from_date' => '',
                                     'expedition_to_date' => ''
                                    )
             )
       )->
  with('form')->
  begin()->
    hasErrors(1)->
    isError('name', 'required')->
  end()->
  info('Try to save with only "Year missing" error')->
  click('Save', 
        array('expeditions' => array('name'  => 'Antarctica 2000',
                                     'expedition_from_date' => array('day'=>'','month'=>'05','year'=>''),
                                     'expedition_to_date' => array('day'=>'','month'=>'','year'=>'')
                                    )
             )
       )->
  with('form')->
  begin()->
    hasErrors(1)->
    isError('expedition_from_date', 'year_missing')->
  end()->
  info('Try to save with only "Month missing" error')->
  click('Save', 
        array('expeditions' => array('name'  => 'Antarctica 2000',
                                     'expedition_from_date' => array('day'=>'05','month'=>'','year'=>'2000'),
                                     'expedition_to_date' => array('day'=>'','month'=>'','year'=>'')
                                    )
             )
       )->
  with('form')->
  begin()->
    hasErrors(1)->
    isError('expedition_from_date', 'month_missing')->
  end()->
  info('Try to save with date to bellow date from global error')->
  click('Save', 
        array('expeditions' => array('name'  => 'Antarctica 2000',
                                     'expedition_from_date' => array('day'=>'05','month'=>'12','year'=>'2000'),
                                     'expedition_to_date' => array('day'=>'05','month'=>'12','year'=>'1999')
                                    )
             )
       )->
  with('form')->
  begin()->
    hasGlobalError("invalid")->
  end()->
  info('Save with correct data and check everything was saved correctly')->
  click('Save', 
        array('expeditions' => array('name'  => 'Antarctica 2000',
                                     'expedition_from_date' => array('day'=>'05','month'=>'12','year'=>'1999'),
                                     'expedition_to_date' => array('day'=>'05','month'=>'12','year'=>'2000')
                                    )
             )
       )->
  with('response')->
  begin()->
    isRedirected()->
  end()->
  followRedirect()->
  with('request')->begin()->
    isParameter('module', 'expedition')->
    isParameter('action', 'edit')->
  end()->
  with('response')->
  begin()->
    checkElement('form input[name="expeditions[name]"][value="Antarctica 2000"]', 1)->
    checkElement('form table tfoot a:first', 'New Expedition')->
    checkElement('form table tfoot a:nth-child(4)', 'Cancel')->
    checkElement('form table tfoot a:nth-child(5)', 'Delete')->
    checkElement('li[class="widget"][id="comment"]', 1)->
    checkElement('li[class="widget"][id="comment"] div[class="widget_content hidden"]', 1)->
  end();

  $items = Doctrine::getTable('Expeditions')->findByName('Antarctica 2000');

  $browser->
  info('Check new record has been saved in DB')->
  test()->is($items[0]->getName(),'Antarctica 2000', 'We have the new encoded expedition');
  $browser->
  test()->is($items[0]->getExpeditionFromDate(),array('year'=>1999, 'month'=>12, 'day'=>5, 'hour'=>'', 'minute'=>'', 'second'=>''), 'We have the new encoded expedition');
  $browser->
  test()->is($items[0]->getExpeditionToDate(),array('year'=>2000, 'month'=>12, 'day'=>5, 'hour'=>'', 'minute'=>'', 'second'=>''), 'We have the new encoded expedition');

  $browser->
  info('Test the delete action...')->
  get('/expedition/edit/id/'. $items[0]->getId())->
  click('Delete')->
  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'expedition')->
    isParameter('action', 'index')->
  end();

  $items = Doctrine::getTable('Expeditions')->findByName('Antarctica 2000');

  $browser->
  test()->is($items->count(),0, 'Expedition well deleted');
