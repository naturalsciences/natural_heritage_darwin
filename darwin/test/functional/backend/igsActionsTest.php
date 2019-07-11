<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/igs/index')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'General Inventory Numbers Search')->
    checkElement('#searchIg_ig_num', '')->
    checkElement('#searchIg_from_date_day > option:first_element', 'dd')->
    checkElement('#searchIg_from_date_month > option:first_element', 'mm')->
    checkElement('#searchIg_from_date_year > option:first_element', 'yyyy')->
    checkElement('#searchIg_to_date_day > option:first_element', 'dd')->
    checkElement('#searchIg_to_date_month > option:first_element', 'mm')->
    checkElement('#searchIg_to_date_year > option:first_element', 'yyyy')->
    checkElement('form input[type="submit"]', 1)->
    setField('#searchIg_from_date_month', '10')->
  end()->
  info('Post waiting for a "Year missing" error')->
  post('/igs/search', array('searchIg'=>array('from_date'=>array('month'=>10))))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('.error_list li', 'Year missing.')->
  end()->  
  info('Post waiting for a "Month missing" error')->
  post('/igs/search', array('searchIg'=>array('from_date'=>array('day'=>2, 'year'=>1975))))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('.error_list li', 'Month missing or remove the day and time.')->
  end()->  
  info('Post waiting for the full results table and the pager')->
  post('/igs/search', array('is_choose'=>0, 'searchIg'=>array('ig_num'=>'', 'from_date'=>'', 'to_date'=>'')))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('ul.pager_nav li', 10)->
    checkElement('ul.pager_nav li.page_selected', '[1]')->
    checkElement('.pager li a span.nav_arrow', 0)->
    checkElement('div.paging_info table td:nth-child(2)', 1)->
    checkElement('div.paging_info table td:last-child select[id="searchIg_rec_per_page"]', 1)->
    checkElement('table.results tbody tr', 7)->
    checkElement('table.results thead th:first_element a.sort span.order_sign_down')->
  end()->  
  info('Click to sort on name descending...')->
  post('/igs/search', array('orderby'=>'ig_num', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchIg'=>array('ig_num'=>'', 'from_date'=>'', 'to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results thead th:first_element a.sort span.order_sign_up')->
  end()->
  info('Select a number of 5 records per pages and test existence of links in pager')->
  post('/igs/search', array('orderby'=>'ig_num', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchIg'=>array('rec_per_page'=>5, 'ig_num'=>'', 'from_date'=>'', 'to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('.pager_nav .pager_separator')->
    checkElement('.pager_nav li a span.nav_arrow', 4)->
    checkElement('table.results tbody tr', 5)->
  end()->
  info('Select page 2')->
  post('/igs/search', array('orderby'=>'ig_num', 'orderdir'=>'desc', 'page'=>2, 'is_choose'=>0, 'searchIg'=>array('rec_per_page'=>5, 'ig_num'=>'', 'from_date'=>'', 'to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('.pager .pager_separator')->
    checkElement('ul.pager_nav li.page_selected', '[2]')->
    checkElement('.pager_nav li a span.nav_arrow', 4)->
    checkElement('table.results tbody tr', 2)->
  end()->
  info('Search on IG num "26" like')->
  post('/igs/search', array('orderby'=>'ig_num', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchIg'=>array('ig_num'=>'26', 'from_date'=>'', 'to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 4)->
  end()->
  info('Search on IG from 1900')->
  post('/igs/search', array('orderby'=>'ig_num', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchIg'=>array('ig_num'=>'', 'from_date'=>array('day'=>'', 'month'=>'', 'year'=>'1900'), 'to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 1)->
  end()->
  info('Search on IG from 01/01/1877')->
  post('/igs/search', array('orderby'=>'ig_num', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchIg'=>array('ig_num'=>'', 'from_date'=>array('day'=>'01', 'month'=>'01', 'year'=>'1877'), 'to_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 2)->
  end()->
  info('Search on IG num up to 01/01/1878')->
  post('/igs/search', array('orderby'=>'ig_num', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchIg'=>array('ig_num'=>'', 'to_date'=>array('day'=>'01', 'month'=>'01', 'year'=>'1878'), 'from_date'=>'')))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 3)->
  end()->
  info('Get new record')->
  get('/igs/index')->
  click('New')->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('h1', 'New I.G. number')->
    checkElement('h1[class="edit_mode"]', true)->
    checkElement('form[class="edition"]', true)->
    checkElement('#igs_ig_date_day > option:first_element', 'dd')->
    checkElement('#igs_ig_date_month > option:first_element', 'mm')->
    checkElement('#igs_ig_date_year > option:first_element', 'yyyy')->
    checkElement('form a', 'Cancel')->
    checkElement('form input[type="submit"][value="Save"]', 1)->
  end()->
  info('Try to save without data')->
  click('Save', 
        array('igss' => array('ig_num'  => '',
                              'ig_date' => ''
                             )
             )
       )->
  with('form')->
  begin()->
    hasErrors(1)->
    isError('ig_num', 'required')->
  end()->
  info('Try to save with only "Year missing" error')->
  click('Save', 
        array('igs' => array('ig_num'  => 'Pollux',
                              'ig_date' => array('day'=>'','month'=>'05','year'=>''),
                             )
             )
       )->
  with('form')->
  begin()->
    hasErrors(1)->
    isError('ig_date', 'year_missing')->
  end()->
  info('Try to save with only "Month missing" error')->
  click('Save', 
        array('igs' => array('ig_num' => 'Pollux',
                              'ig_date' => array('day'=>'05','month'=>'','year'=>'2000'),
                             )
             )
       )->
  with('form')->
  begin()->
    hasErrors(1)->
    isError('ig_date', 'month_missing')->
  end()->
  info('Save with correct data and check everything was saved correctly')->
  click('Save', 
        array('igs' => array('ig_num' => 'Pollux',
                              'ig_date' => array('day'=>'05','month'=>'12','year'=>'2000'),
                             )
             )
       )->
  with('response')->
  begin()->
    isRedirected()->
  end()->
  followRedirect()->
  with('request')->begin()->
    isParameter('module', 'igs')->
    isParameter('action', 'edit')->
  end()->
  with('response')->
  begin()->
    checkElement('form input[name="igs[ig_num]"][value="Pollux"]', 1)->
    checkElement('form table tfoot a:first', 'New I.G.')->
    checkElement('form table tfoot a:nth-child(4)', 'Cancel')->
    checkElement('form table tfoot a:nth-child(5)', 'Delete')->
    checkElement('li[class="widget"][id="comment"]', 1)->
    checkElement('li[class="widget"][id="comment"] div[class="widget_content hidden"]', 1)->
    checkElement('li[class="widget"][id="insurances"]', 1)->
    checkElement('li[class="widget"][id="insurances"] div[class="widget_content hidden"]', 1)->
  end();

  $items = Doctrine::getTable('Igs')->findByIgNum('Pollux');

  $browser->
  info('Check new record has been saved in DB')->
  test()->is($items[0]->getIgNum(),'Pollux', 'We have the new encoded IG');
  $browser->
  test()->is($items[0]->getIgDate(),array('year'=>2000, 'month'=>12, 'day'=>5, 'hour'=>'', 'minute'=>'', 'second'=>''), 'We have the new encoded IG');

  $browser->
  info('Test the delete action...')->
  get('/igs/edit/id/'. $items[0]->getId())->
  click('Delete')->
  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'igs')->
    isParameter('action', 'index')->
  end();

  $items = Doctrine::getTable('Igs')->findByIgNum('Pollux');

  $browser->
  test()->is($items->count(),0, 'IG well deleted');
