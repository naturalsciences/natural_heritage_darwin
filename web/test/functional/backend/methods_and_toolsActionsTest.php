<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
    info('1 - Add a collecting method with a post method but not as a XMLHTTP Request')->
    get('/methods_and_tools/addMethod?value=Test')->
    with('response')->begin()->
        isStatusCode(404)->
    end()->
    info('2 - Add a collecting method with a post method as a XMLHTTP Request')->
    setHttpHeader("X-Requested-With", "XMLHttpRequest")->
    post('/methods_and_tools/addMethod?value=Test')->
    with('response')->begin()->
        isStatusCode(200)->
        matches('/[0-9]+/')->
    end()->
    info('3 - Try to reAdd the same collecting method.. and check we get an error')->
    setHttpHeader("X-Requested-With", "XMLHttpRequest")->
    post('/methods_and_tools/addMethod?value=Test')->
    with('response')->begin()->
        isStatusCode(200)->
        matches('/This method already exist/')->
    end()->
    info('4 - Add a collecting tool with a post method but not as a XMLHTTP Request')->
    get('/methods_and_tools/addTool?value=Test')->
    with('response')->begin()->
        isStatusCode(404)->
    end()->
    info('5 - Add a collecting tool with a post method as a XMLHTTP Request')->
    setHttpHeader("X-Requested-With", "XMLHttpRequest")->
    post('/methods_and_tools/addTool?value=Test')->
    with('response')->begin()->
        isStatusCode(200)->
        matches('/[0-9]+/')->
    end()->
    info('6 - Try to reAdd the same collecting tool.. and check we get an error')->
    setHttpHeader("X-Requested-With", "XMLHttpRequest")->
    post('/methods_and_tools/addTool?value=Test')->
    with('response')->begin()->
        isStatusCode(200)->
        matches('/This tool already exist/')->
    end()
;
