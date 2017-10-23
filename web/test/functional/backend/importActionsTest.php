<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');
$col = Doctrine::getTable('Collections')->findOneByName('Import collection')->getId() ;
$task = new darwinCheckImportTask($configuration->getEventDispatcher(), new sfFormatter());
$browser->
  info('Import Index')->
  get('/import/index')->
  with('request')->begin()->
    isParameter('module', 'import')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    checkElement('h1', 'Imports : ')->
  end()->
  click('input.search_submit')->
  with('response')->begin()->
    checkElement('table.results tbody tr td:nth-child(2)','Import collection')->
  end()->
  info('Staging Index')->
  get('/staging/edit/id/1')->
  with('request')->begin()->
    isParameter('module', 'staging')->
    isParameter('action', 'edit')->
  end()->
  with('response')->begin()->
    checkElement('input[id="staging_taxon_ref_name"][value="Cacaleluya"]')->
  end()->
  info('Correct the taxon error')->
  click("#submit",
        array('staging' => array('taxon_ref'  => 11)))->
  with('response')->
  begin()->
    isRedirected()->
  end()->
  followRedirect()->
  with('request')->begin()->
    isParameter('module', 'staging')->
    isParameter('action', 'index')->
  end()->
  info('Look in search if it remain just one problem between the 3 lines')->
  post('/staging/search/import/1')->
  with('response')->
  begin()->
    checkElement('table.staging_table tbody td.fld_tocomplete:first',1)->
  end()->
  info('Mark The 2 good lines as ok')->
  get('/staging/markok?import=1')->
  with('request')->begin()->
    isParameter('module', 'staging')->
    isParameter('action', 'markok')->
  end() ;

$task->run(array(), array('--application=backend','--do-import')) ;


$browser->
  info('check if just one record is imported (because the second is linked to the remaining one with error)')->
  get('/import/index')->
  click('input.search_submit')->
  with('response')->begin()->
    checkElement('table.results tbody tr td:nth-child(6)','/1 on 3/')->
  end()->  
  post('/staging/search/import/1')->
  with('response')->
  begin()->
    checkElement('table.staging_table tbody tr',1)->
  end()->
  info("let's correct the second record to import both")->
  get('/staging/edit/id/2')->
  with('request')->begin()->
    isParameter('module', 'staging')->
    isParameter('action', 'edit')->
  end()->
  with('response')->begin()->
    checkElement('input[id="staging_WrongPeople_0_people_ref_name"][value="Poilux"]')->
  end()->
  info('Correct the people error')->
  click("#submit",
        array('staging' => array('WrongPeople' => array(
        array ('people_ref'  => Doctrine::getTable('People')->findOneByGivenName('Poilux')->getId()))
        )))->
  with('response')->
  begin()->
    isRedirected()->
  end()->
  post('/staging/search/import/1')->
  with('response')->
  begin()->
    checkElement('table.staging_table tbody tr:first td:nth-child(3)','/No problems detected/')->
  end()->
  get('/staging/markok?import=1')->
  with('request')->begin()->
  end();
  
$task->run(array(), array('--application=backend','--do-import')) ;
$browser->
  info('check if all the 3 records were well imported now')->
  post('/specimensearch/search',array('specimen_search_filters' => array('collection_ref' => array(0=>$col))))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('table.spec_results tbody tr', 3)->
  end()->
  info('Ensure the import is finished (and so doesn\'t appear in search anymore)')->
get('/import/index')->
  with('request')->begin()->
    isParameter('module', 'import')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    checkElement('h1', 'Imports : ')->
  end()->
  click('input.search_submit', array('imports_filters' => array('show_finished' => true)))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr',1)->
    checkElement('table.results tbody tr td:nth-child(4)','/Finished/')->
  end();
  
  
  
