<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('Index')->
  get('/comment/comment?id=4&table=taxonomy')->
  
  with('request')->begin()->
    isParameter('module', 'comment')->
    isParameter('action', 'comment')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.delete_button', false)->
    checkElement('textarea', '')->
  end()->

  click('Save', array('comments' => array(
    'referenced_relation' => 'taxonomy',
    'record_id'           => '4',
    'notion_concerned'    => 'taxon life history',
    'comment'             => 'This is ok...
There is a thing',
  )))->
  
  with('request')->begin()->
    isParameter('module', 'comment')->
    isParameter('action', 'comment')->
  end();
 $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');

 $item = Doctrine_Query::create()->from('Comments c')->andWhere("c.referenced_relation = 'taxonomy'")
->andWhere("c.notion_concerned = 'taxon life history' ")
->andWhere('c.record_id=4')->fetchOne();

  $browser->test()->isnt($item, null,'We add an item');
  
$browser->
  info('Update')->
  get('/comment/comment?id=4&table=taxonomy&cid='.$item->getId())->
  
  with('request')->begin()->
    isParameter('module', 'comment')->
    isParameter('action', 'comment')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.delete_button')->
    checkElement('textarea', '/There is a thing/')->
  end()->
  
  click('Save', array('comments' => array(
    'referenced_relation' => 'taxonomy',
    'record_id'           => '4',
    'notion_concerned'    => 'taxon life history',
    'comment'             => 'This is not ok...',
  )))
  ->
  with('request')->begin()->
    isParameter('module', 'comment')->
    isParameter('action', 'comment')->
  end();
 $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');


$browser->
  get('/comment/comment?id=4&table=taxonomy&cid='.$item->getId())->
  
  with('request')->begin()->
    isParameter('module', 'comment')->
    isParameter('action', 'comment')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.delete_button')->
    checkElement('textarea', 'This is not ok...')->
  end()->

  info('Delete')->
  click('.delete_button')->
  with('response')->begin()->
    isStatusCode(200)->
  end();

  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');

 $item = Doctrine_Query::create()->from('Comments c')->andWhere("c.referenced_relation = 'taxonomy'")
->andWhere("c.notion_concerned = 'taxon life history' ")
->andWhere('c.record_id=4')->fetchOne();

  $browser->test()->is($item, null,'there is no more item');
