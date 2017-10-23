<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  get('/property/add?id=4&table=taxonomy')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#properties_property_type option',3)->
    checkElement('#properties_applies_to option',2)->
    checkElement('#properties_property_unit option',2)->
  end()->
  
  click('Save', array('properties' => array(
    'referenced_relation' => 'taxonomy',
    'record_id'           => '4',
    'property_type'       => '',
    'applies_to'   => '',
    'date_from'           => '',
    'date_to'             => '',
    'method'     => '',
    'property_accuracy' => '',
    'property_unit'       => ''
  )))->

  with('form')->begin()->
    hasErrors(1)->
    isError('property_type', 'required')->
  end()->

  click('Save', array('properties' => array(
    'referenced_relation' => 'taxonomy',
    'record_id'           => '4',
    'property_type'       => 'size',
    'date_from'           => '',
    'date_to'             => '',
    'method'     => '',
    'property_unit'       => 'm²',
    'lower_value'       => '12',
    'property_accuracy' => '10',
      ))
  )->

  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->

  get('/property/getUnit?type=size')->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('option',2)->
  end()
;
