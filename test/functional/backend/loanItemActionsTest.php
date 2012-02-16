<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  get('/loan/edit?id=1')->
  info('Overview')->
  with('request')->begin()->
    isParameter('module', 'loan')->
    isParameter('action', 'edit')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#tab_0', ' < Edit Loan > ')->
  end()->
  click('Items overview')->
  with('request')->begin()->
    isParameter('module', 'loan')->
    isParameter('action', 'overview')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.warn_message')->
  end()->

 click('Save', array('loan_overview' => array('newLoanItems'=> array('0'=>array(
    'part_ref' => '',
    'ig_ref'           => '',
    'details'       => 'details message',
    'item_visible'   => 'true',
  )))) )->

  with('response')->
  begin()->
      isRedirected()->
      followredirect()->
  end()->
  with('request')->begin()->
    isParameter('module', 'loan')->
    isParameter('action', 'overview')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.warn_message',0)->
    checkElement('.loan_overview_form tbody tr',3)-> // 3 Row per items
    checkElement('textarea','details message')->
    checkElement('img[title="View"]')->
    checkElement('img[title="Edit"]')->
  end()->
 click('Save', array('loan_overview' => array('LoanItems'=> array('0'=>array(
    'part_ref' => '',
    'ig_ref'           => '',
    'details'       => 'details message2',
    'item_visible'   => '',
  )))) )->
 
  with('response')->
  begin()->
      isRedirected()->
      followredirect()->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.warn_message')->
  end()
;