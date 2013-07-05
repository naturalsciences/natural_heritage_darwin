<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  get('/property/add?id=4&table=taxonomy')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#catalogue_properties_property_type option',3)->
    checkElement('#catalogue_properties_property_sub_type option',4)->
    checkElement('#catalogue_properties_property_accuracy_unit option',3)->
    checkElement('#catalogue_properties_property_unit option',3)->
  end()->
  
  click('Save', array('catalogue_properties' => array(
    'referenced_relation' => 'taxonomy',
    'record_id'           => '4',
    'property_type'       => '',
    'property_sub_type'   => '',
    'property_qualifier'  => '',
    'date_from'           => '',
    'date_to'             => '',
    'property_method'     => '',
    'property_tool'       => '',
    'property_accuracy_unit' => '',
    'property_unit'       => ''
  )))->

  with('form')->begin()->
    hasErrors(1)->
    isError('property_type', 'required')->
  end()->

  click('Save', array('catalogue_properties' => array(
    'referenced_relation' => 'taxonomy',
    'record_id'           => '4',
    'property_type'       => 'physical measurement',
    'property_sub_type'   => '',
    'property_qualifier'  => '',
    'date_from'           => '',
    'date_to'             => '',
    'property_method'     => '',
    'property_tool'       => '',
    'property_accuracy_unit' => 'cm²',
    'property_unit'       => 'm²',
    'newVal' => array('0' => array(
	'property_value' => '12',
	'property_accuracy' => '10',
	'id' => '',
      )),
  )))->

  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->
  

  get('/property/getUnit?type=physical%20measurement')->
  
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('option',5)->
  end()->

  get('/property/getSubtype?type=physical%20measurement')->
  
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('option',2)->
    checkElement('option:last','length')->
  end()->

  get('/property/getQualifier?subtype=length')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('option',2)->
    checkElement('option:last','beak length')->
  end()->

  get('/property/addValue?num=12')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('tr',1)->
    checkElement('tr td',3)->
  end();

  $r = Doctrine::getTable('CatalogueProperties')->findForTable('taxonomy',4);

  $browser->
  get('/property/addValue?num=13&id='.$r[2]->getId() )->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('tr',1)->
    checkElement('tr td',3)->
  end()
;
