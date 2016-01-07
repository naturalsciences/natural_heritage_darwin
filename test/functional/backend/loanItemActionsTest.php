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
    checkElement('#tab_0', ' < Edit loan > ')->
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
    'specimen_ref' => '',
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
    checkElement('.loan_overview_form tbody tr',3)-> // 3 Row per items
    checkElement('textarea','details message')->
    checkElement('img[title="View"]')->
    checkElement('img[title="Edit"]')->
    checkElement('.maint_butt')->
  end()->

  info('Add Maintenance')->
  get('/loanitem/maintenances?ids='.Doctrine::getTable('LoanItems')->findOneByLoanRef(1)->getId())->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1','/Add Maintenance/')->
  end()->
  click('Add Maintenance', array('collection_maintenance' => array(
    'category' => 'observation',
    'action_observation'           => 'check',
    'people_ref'       => Doctrine::getTable('People')->findOneByGivenName('Poilux')->getId(),
    'description'   => 'Ok The package is checked!',
  )) )->

  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->

  get('/loanitem/showmaintenances?id='.Doctrine::getTable('LoanItems')->findOneByLoanRef(1)->getId())->
   with('response')->begin()->
    isStatusCode(200)->
    checkElement('.catalogue_table_view')->
    checkElement('.catalogue_table_view tbody tr',1)->
    checkElement('.catalogue_table_view .delete_maint_button',1)->
  end()->
  click('.delete_maint_button')->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->

  get('/loanitem/showmaintenances?id='.Doctrine::getTable('LoanItems')->findOneByLoanRef(1)->getId())->
   with('response')->begin()->
    isStatusCode(200)->
    checkElement('.catalogue_table_view')->
    checkElement('.catalogue_table_view tbody tr',0)->
    checkElement('.catalogue_table_view .delete_maint_button',0)->
  end()->

 info('Edit Loan Item Screen')->
  get('/loanitem/edit?id='.Doctrine::getTable('LoanItems')->findOneByLoanRef(1)->getId())->
   with('response')->begin()->
    isStatusCode(200)->
    checkElement('#mainInfo')->
    checkElement('.loanitem_form')->
  end()->

  click('#submit_loan_item',
          array('loan_items'=> array(
              'receiver' => '1',
              'sender' => '1',
              'newActorsSender' => array(
                                    0 => array(
                                      'people_ref' => Doctrine::getTable('People')->findOneByGivenName('Poilux')->getId(),
                                      'people_type' => 'sender',
                                      'people_sub_type' => array(2),
                                      'order_by' => 1
                                              )
                                      ),
             'newActorsReceiver' => array(
                                    0 => array(
                                      'people_ref' => Doctrine::getTable('People')->findOneByGivenName('Poilux')->getId(),
                                      'people_type' => 'receiver',
                                      'people_sub_type' => array(2,4,8),
                                      'order_by' => 1
                                              )
                                      )))
  )->
  with('response')->begin()->
    isRedirected()->
  end()->
  followRedirect()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#sender_body')->
    checkElement('#sender_body tr',1)->
  end()->

 info('Delete Loan Item')->
 get('/loan/overview?id=1')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.loan_overview_form tbody tr',3)-> // 3 Row per items
  end()->
 click('Save', array('loan_overview' => array('LoanItems'=> array('0'=>array(
    'specimen_ref' => '',
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
