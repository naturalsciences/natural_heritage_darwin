<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Search')->
  get('/user/index')->

  with('request')->begin()->
    isParameter('module', 'user')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.search_results_content')->
    checkElement('#users_filters_family_name')->
  end()->


  click('.search_submit', array('users_filters' => array(
    'family_name' => array('text' => 'Evil'),
      )
    )
  )->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.results_container')->
    checkElement('.results_container .results > tbody > tr',1)->
  end()->

  get('/user/choose')->
  click('Search', array('users_filters' => array(
    'family_name' => array('text' => 'sdf')
      )
    )
  )->

  with('response')->begin()->
    isStatusCode(200)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/No Matching Items/','Content is ok');

$browser->
  info('Address')->

  get('/user/profile')->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#address tbody tr',0)->
  end()->

  click('#address a.link_catalogue')->
    with('response')->begin()->
    isStatusCode(200)->
  end()->

  click('Save')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->

  with('form')->begin()->
    hasErrors(2)->
    isError('locality', 'required')->
    isError('country', 'required')->
  end()->

  click('Save', array('users_addresses' => array(
    'locality'  => 'Bruxelles',
    'country' => 'Belgium',
    'tag' => 'home,pref'
  )))->

  with('response')->begin()->
    isStatusCode(200)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');

$browser->
  get('/user/profile')->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#address tbody tr',1)->
    checkElement('#address tbody tr .tag',2)->
  end()->

  click('#address table tbody a.link_catalogue')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('input[value="Bruxelles"]')->
  end()->

  click('a.delete_button')->
   with('response')->begin()->
    isStatusCode(200)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');



$browser->
  info('Comm')->
  get('/user/profile')->


  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#comm tbody tr',0)->
  end()->

  click('#comm a.link_catalogue')->
    with('response')->begin()->
    isStatusCode(200)->
  end()->

  click('Save')->
  with('response')->begin()->
    isStatusCode(200)->
  end()->

  with('form')->begin()->
    hasErrors(1)->
    isError('entry', 'required')->
  end()->

  click('Save', array('users_comm' => array(
    'entry'  => '+32478.254415',
    'comm_type' => 'phone/fax',
    'tag' => 'home'
  )))->

  with('response')->begin()->
    isStatusCode(200)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');

$browser->
  get('/user/profile')->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#comm tbody tr',1)->
    checkElement('#comm tbody tr .tag','Home')->
  end()->

  click('#comm table tbody a.link_catalogue')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('input[value="+32478.254415"]')->
  end()->

  click('.delete_button')->
   with('response')->begin()->
    isStatusCode(200)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');

$browser->
  get('/user/profile')->

  with('response')->begin()->
    isStatusCode(200)->
  end();

$browser->addCustomUserAndLogin('ychambert','toto');
$browser->addCustomUserAndLogin('encoder','evil');
$browser->addCustomUserAndLogin('manager','evil');
$browser->get('account/logout')->
  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect()->

  with('user')->begin()->
	isAuthenticated(false)->
  end()->

  login('ychambert','toto');

$browser->
  info('Search')->
  get('/user/index')->

  with('request')->begin()->
    isParameter('module', 'user')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(403)->
  end();

$browser->
  info('Widget')->
  get('/user/widget')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('tbody[alt="board_widget"] tr',5)->
  end()->

  click('#submit', array('user_widget' => array(
    'MyWidgets' => array(
      0 => array(
      'category'  => 'board_widget',
        'group_name' => 'savedSearch',
        'user_ref' => $id,
        'widget_choice' => 'opened',
        'title_perso' => 'Search'),
        1 => array(
        'category'  => 'board_widget',
        'group_name' => 'savedSpecimens',
        'user_ref' => $id,
        'widget_choice' => 'opened',
        'title_perso' => 'Spec')))
  ))->

  with('request')->begin()->
    isParameter('module', 'user')->
    isParameter('action', 'widget')->
  end();

$browser->
  info('users ychambert is not allowed to access to this page')->
  get('user/loginInfo?user_ref='.Doctrine::getTable("Users")->findOneByFamilyName('Evil')->getId())->
  with('response')->begin()->
    isStatusCode(403)->
  end()->

  info('Prerences')->

  get('user/preferences')->
  with('response')->begin()->
    isStatusCode()->
    checkElement('h1','/My Preferences/')->
    checkElement('.user_table > thead',3)->
    checkElement('.user_table > tbody',3)->
    checkElement('.user_table > tbody > tr',6)->
    checkElement('#preferences_board_spec_rec_pp option[selected]','10')->
    checkElement('#preferences_board_search_rec_pp option[selected]','10')->
  end()->

  click('input[type="submit"]', array('preferences' => array(
    'board_spec_rec_pp' => 5,
    'board_search_rec_pp' => 5))
  )->

  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect()->

  with('response')->begin()->
    isStatusCode()->
    checkElement('h1','/My Preferences/')->
    checkElement('#preferences_board_spec_rec_pp option[selected]','5')->
    checkElement('#preferences_board_search_rec_pp option[selected]','5')->
  end();

