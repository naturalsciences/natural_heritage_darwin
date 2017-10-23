<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');
$browser->setTester('doctrine', 'sfTesterDoctrine');
$people = Doctrine::getTable('People')->findOneByFamilyName('Chambert') ;
$reguser = $browser->addCustomUserAndLogin('reguser','evil') ;
$encoder = $browser->addCustomUserAndLogin('encoder','evil') ;

$browser->
  info('1 - New Loan screen')->
  get('/loan/new')->
  with('request')->begin()->
    isParameter('module', 'loan')->
    isParameter('action', 'new')->
  end()->
  info('1.1 - is everything ok on screen')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('title','Add loan')->
    checkElement('.board_col',1)->
    checkElement('.board_col .widget:first .widget_top_bar span','/Loan/')->
    checkElement('.board_col .widget:nth-child(2) .widget_top_bar span','/People involved/')->
    checkElement('.board_col .widget:nth-child(3) .widget_top_bar span','/Loan status/')->
    checkElement('.board_col .widget:nth-child(4) .widget_top_bar span','/Properties/')->
    checkElement('.board_col .widget:nth-child(5) .widget_top_bar span','/Comments/')->
    checkElement('.board_col .widget:nth-child(6) .widget_top_bar span','/Insurances/')->
    checkElement('.board_col .widget:nth-child(7) .widget_top_bar span','/Related Files/')->
    checkElement('.board_col .widget:nth-child(8) .widget_top_bar span','/Darwin Users/')->
    end()->
    click('#submit_loan',
          array('loans'=> array(
              'name' => 'loan for test',
              'newActorsSender' => array(
                                    0 => array(
                                      'people_ref' => $people->getId(),
                                      'people_type' => 'sender',
                                      'people_sub_type' => array(2),
                                      'order_by' => 1
                                              )
                                      ),
             'newActorsReceiver' => array(
                                    0 => array(
                                      'people_ref' => $people->getId(),
                                      'people_type' => 'receiver',
                                      'people_sub_type' => array(2,4,8),
                                      'order_by' => 1
                                              )
                                      ),
             'newUsers' => array(
                              0 => array(
                                'user_ref' => $reguser,
                                        ),
                              1 => array(
                                'user_ref' => $encoder,
                                'has_encoding_right' => 'on'
                                        ),
                                )
            	)
        )
       )->
    with('response')->begin()->
    isRedirected()->
    end()->
    followRedirect()->
    with('response')->begin()->
    isStatusCode(200)->
    info('2 - Edition screen')->
    checkElement('title','Edit loan')->
    checkElement('.board_col .widget:nth-child(2) .widget_top_bar tbody#sender_body tr:first td:nth-child(4)','/Chambert Yann/')->
    checkElement('.board_col .widget:nth-child(2) .widget_top_bar tbody#receiver_body tr:first td:nth-child(4)','/Chambert Yann/')->
    #checkElement('#loanStatus table.catalogue_table tbody tr:first td:nth-child(4)','/Evil Root/')->
    end()->
  info('3 - Look for our new loan in search')->
  post('/loan/search',array('orderby'=>'name', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0,'loans_filters'=> array('name' => 'loan for test')))->
  with('response')->begin()->
    checkElement('table.results tbody tr td:first','/loan for test/')->
  end();
