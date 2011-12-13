<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');
$browser->addCustomSpecimen('777','Collection test for individual','Taxon test for individual',1)->
	with('response')->
    isRedirected()->
    followRedirect()->
  
  with('request')->begin()->
    click('#tab_2')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('title','Add specimen individual')->
    checkElement('.board_col',2)->
    checkElement('.board_col:first .widget',3)->
    checkElement('.board_col:last .widget',6)->
    checkElement('.board_col:first .widget:first .widget_top_bar span','/Type/')->
    checkElement('.board_col:first .widget:nth-child(2) .widget_top_bar span','/Sex/')->
    checkElement('.board_col:first .widget:nth-child(3) .widget_top_bar span','/Stage/')->
    checkElement('.board_col:last .widget:first .widget_top_bar span','/Count/')->
    checkElement('.board_col:last .widget:nth-child(2) .widget_top_bar span','/Properties/')->
    checkElement('.board_col:last .widget:nth-child(3) .widget_top_bar span','/Identifications/')->
    checkElement('.board_col:last .widget:nth-child(4) .widget_top_bar span','/Comments/')->
  end();

$specimen_id = 3;
$indiv_id = $browser->addCustomIndividual($specimen_id);

$browser->
     get('individuals/overview/spec_id/'.$specimen_id)-> 
     with('response')->begin()->
     isStatusCode(200)->
     checkElement('title', 'Specimen individuals overview')->
     checkElement('table.catalogue_table tr.spec_individuals',1)->
     checkElement('table.catalogue_table tr.spec_individuals td:first','/Specimen/')->
     end();

$browser->
     get('individuals/edit/id/'.$indiv_id)-> 
     with('response')->begin()->
       checkElement('.spec_ident_identifiers_handle',1)->//debug()->
       click('Delete')->end();
$browser->
      info('check if the individual is well deleted')->
      get('individuals/edit/id/'.$indiv_id)-> 
      with('response')->begin()->
      isStatusCode(404)->
      end();

