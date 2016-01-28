<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration);

$browser->
  info('1 - Not Logged Request')->
  get('/board/index')->

  with('request')->begin()->
    isParameter('module', 'board')->
    isParameter('action', 'index')->
    isForwardedTo('account','login')-> //When not logged in, forwarded to login page
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  with('user')->begin()->
    isAuthenticated(false)->
  end()->
  
  info('2 - Test bad login and password')->
  
  click('Log in',array('login' => array(
    'username' => 'brol',
    'password' => 'brol'
    )))->
  with('form')->begin()->
    hasErrors(true)->
  end()->
  
  info('3 - Test good login but bad password')->
  
  click('Log in',array('login' => array(
    'username' => 'root',
    'password' => 'brol'
    )))->
  with('form')->begin()->
    hasErrors(true)->
  end()->

  info('4 - Test good login and password')->

  click('Log in',array('login' => array(
    'username' => 'root',
    'password' => 'evil'
    )))->
  with('form')->begin()->
    hasErrors(false)->
  end()->

  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect()->
  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'board')->
    isParameter('action', 'index')->
  end()->
  with('user')->begin()->
    isAuthenticated(true)->
  end()->

  info('5 - Logout')->
  
  click('Exit')->
  with('response')->begin()->
    isRedirected()->
  end()->
  followRedirect()->

  with('user')->begin()->
    isAuthenticated(false)->
  end();
