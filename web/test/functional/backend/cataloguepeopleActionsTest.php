<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  get('/cataloguepeople/people?table=taxonomy&rid=3')->

  with('request')->begin()->
    isParameter('module', 'cataloguepeople')->
    isParameter('action', 'people')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('form')->
    checkElement('#catalogue_people_people_type option',2)->
    checkElement('.tab_choice li',2)->
  end()->
  
  click('Save')->
  with('form')->begin()->
    hasErrors(1)->
    isError('people_ref', 'required')->
  end();

  $people = Doctrine::getTable('People')->findOneByFamilyName('Root');

$browser->
  click('Save',array(
    'catalogue_people' => array(
      'people_type' => 'expert',
      'people_sub_type' => 'Main',
      'record_id' => '3',
      'people_ref' => $people->getId(),
      'referenced_relation' => 'taxonomy'
      )
    )
  )->
  with('form')->begin()->
    hasErrors(0)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');
  $rbins = Doctrine::getTable('People')->findOneByFamilyName('Institut Royal des Sciences Naturelles de Belgique');
  $rbins->save();

$browser->
  get('/cataloguepeople/people?table=taxonomy&rid=3')->

  click('Save',array(
    'catalogue_people' => array(
      'people_type' => 'author',
      'people_sub_type' => 'Main',
      'record_id' => '3',
      'people_ref' => $rbins->getId(),
      'referenced_relation' => 'taxonomy'
      )
    )
  )->
  with('form')->begin()->
    hasErrors(0)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');

  $browser->
  get('/widgets/reloadContent?widget=cataloguePeople&category=catalogue_taxonomy&eid=3')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('table tr:nth-child(1) .widget_sub_table tbody tr',2)->
    checkElement('table tr:nth-child(1) .widget_sub_table tbody tr:first td:nth-child(2)', '/Root/')->
    checkElement('table tr:nth-child(1) .widget_sub_table tbody tr:last td:nth-child(2)', '/Institut Royal des Sciences Naturelles de Belgique/')->
  end();

$rbins_recId = Doctrine_Query::create()
	 ->from('CataloguePeople c')
	  ->where('c.people_ref = ?',$rbins->getId())
	  ->andWhere('c.people_type=?','author')
	  ->fetchOne();

$root_recId = Doctrine_Query::create()
	 ->from('CataloguePeople c')
	  ->where('c.people_ref = ?',$people->getId())
	  ->andWhere('c.people_type=?','author')
	  ->fetchOne();
$browser->
  get('/cataloguepeople/editOrder?table=taxonomy&rid=3&people_type=author&order='.$rbins_recId->getId().','.$root_recId->getId().',')->
   with('response')->begin()->
    isStatusCode(200)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');

  $browser->
  get('/widgets/reloadContent?widget=cataloguePeople&category=catalogue_taxonomy&eid=3')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('table tr:nth-child(1) .widget_sub_table tbody tr',2)->
    checkElement('table tr:nth-child(1) .widget_sub_table tbody tr:first td:nth-child(2)', '/Institut Royal des Sciences Naturelles de Belgique/')->
    checkElement('table tr:nth-child(1) .widget_sub_table tbody tr:last td:nth-child(2)', '/Root/')->
  end();

$browser->
  get('/cataloguepeople/getSubType?type=author')->
    with('response')->begin()->
    isStatusCode(200)->
  end();
  $browser->test()->like($browser->getResponse()->getContent(),'/Secondary Author/','Content is ok');
  $browser->test()->like($browser->getResponse()->getContent(),'/Corrector/','Content is ok');

$browser->
  get('/cataloguepeople/getSubType?type=expert')->
    with('response')->begin()->
    isStatusCode(200)->
  end();
  $browser->test()->like($browser->getResponse()->getContent(),'/Main/','Content is ok');
