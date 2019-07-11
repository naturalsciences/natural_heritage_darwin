<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

Doctrine_Query::create()->delete('informativeWorkflow')->execute();

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

/*$workflow = new informativeWorkflow() ;
$workflow->setStatus('suggestion') ;
$workflow->setReferencedRelation('specimens') ;
$workflow->setRecordId(1) ;
$workflow->setComment('change taxonomy for this specimen, use Falco peregrinus instead') ;
$workflow->save() ;*/
$browser->setHttpHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
$browser->info('1 Add a workflow in a specimen with Ajax')->
  get('informativeWorkflow/add?table=specimens&id=1&status=suggestion&comment=change taxonomy for this specimen, use Falco peregrinus instead');
  
$browser->
  info('check if an informative workflow is present in taxonomy')->
  get('/taxonomy/edit?id=8')->
  with('response')->
  begin()->
    checkElement('#informativeWorkflow table.catalogue_table tbody td:nth-child(2)','To check')->  
    checkElement('#informativeWorkflow table.catalogue_table tbody td:nth-child(3)','test de workflow pour les tests')->      
  end();
$browser->
  info('check the workflow added with Ajax is present on board')->
  get('informativeWorkflow/search?status=all')->  
  with('response')->  
  begin()->
    checkElement('table.catalogue_table_view tbody td:nth-child(2)','specimens')->
    checkElement('table.catalogue_table_view tbody td:nth-child(3)','Suggestion')->            
    checkElement('table.catalogue_table_view tbody td:nth-child(4)','change taxonomy for this sp...')->        
  end();

?>
