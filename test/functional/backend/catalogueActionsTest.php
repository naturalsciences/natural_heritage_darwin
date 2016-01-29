<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');

$browser->
  info('executeSearch')->
  get('/catalogue/search?searchCatalogue[name]=falco&searchCatalogue[table]=taxonomy')->
  with('request')->begin()->
    isParameter('module', 'catalogue')->
    isParameter('action', 'search')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('table tbody tr',9)->
    checkElement('tbody tr:first span','/Falco Class/')->
  end()->
  info('executeTree');

$items = Doctrine::getTable('Taxonomy')->findByName('Falco Peregrinus (Duchesnus Brulus 1912)');

$browser->
  get('/catalogue/tree?table=taxonomy&id='.$items[0]->getId())->
  
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body > div.wrapper > ul > li',8)->
    checkElement('body > div.wrapper > ul li:first','/Eucaryota/')->
    checkElement('body > div.wrapper > ul li:last','/Duchesnus/')->
  end()->

  info('Relation');

$items = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy', 4, 'current_name');

$browser->  
  get('/catalogue/relation?type=rename&table=taxonomy&rid=4&id='.$items[0]['id'])->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('label[for="catalogue_relationships_record_id_2"]','Renamed in')->
    checkElement('form')->
    checkElement('input[id="catalogue_relationships_record_id_2_name"][value="Falco Peregrinus Tunstall, 1771"]')->
  end();

$items = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy', 4, 'recombined from');

$browser->  
  get('/catalogue/relation?type=recombined&table=taxonomy&rid=4&id='.$items[0]['id'])->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('label[for="catalogue_relationships_record_id_2"]','Recombined from')->
    checkElement('form')->
    checkElement('input[id="catalogue_relationships_record_id_2_name"][value="Falco Peregrinus recombinus"]')->
  end()->

  info('SaveRelation')->

  get('/catalogue/relation?type=recombined&table=taxonomy&rid=4')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('label[for="catalogue_relationships_record_id_2"]','Recombined from')->
    checkElement('form')->
    checkElement('input[id="catalogue_relationships_record_id_2_name"][value=""]')->
  end()->
  
  click('Save', 
    array('catalogue_relationships' => array(
	'record_id_2'  => '2',
        'referenced_relation' => 'taxonomy',
	'relationship_type' => 'recombined from',
	'record_id_1' =>'4',
      )
    )
  );
  $browser->test()->like($browser->getResponse()->getContent(),'/ok/','Content is ok');

  $items = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy', 4, 'recombined from');
  $tax =  Doctrine::getTable('Taxonomy')->find($items[1]['record_id_2']);
  $browser->
  get('/catalogue/relation?type=recombined&table=taxonomy&rid=4&id='.$items[1]['id'])->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('label[for="catalogue_relationships_record_id_2"]','Recombined from')->
    checkElement('form')->
    checkElement('input[id="catalogue_relationships_record_id_2_name"]')->
    checkElement('input[id="catalogue_relationships_record_id_2_name"][value="'.$tax->getName().'"]')->
  end()->

  info('DeleteRelated')->

  get('/catalogue/deleteRelated?table=catalogue_relationships&id='.$items[1]['id'])->
  with('response')->begin()->
    isStatusCode(200)->
  end()->

  get('/catalogue/relation?type=recombined&table=taxonomy&rid=4&id='.$items[1]['id'])->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('label[for="catalogue_relationships_record_id_2"]','Recombined from')->
    checkElement('form')->
    checkElement('#relation_catalogue_name','')->
  end();

  $items = Doctrine::getTable('Taxonomy')->findByName('Falco Peregrinus (Duchesnus Brulus 1912)');
  $parent_item = Doctrine::getTable('Taxonomy')->findOneByLevelRef(48);
  $items[0]->setParentRef($parent_item->getId());
  $items[0]->save();

$browser->
  info('Test the possible upper levels check function')->
  get('/catalogue/searchPUL/table/taxonomy/level_id/'.$items[0]->getLevelRef().'/parent_id/'.$parent_item->getId())->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->
  get('/catalogue/searchPUL/table/taxonomy/level_id/20/parent_id/'.$parent_item->getId())->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/not ok/')->
  end()->
  get('/catalogue/searchPUL/table/taxonomy/level_id/50/parent_id/'.$parent_item->getId())->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/ok/')->
  end()->
  get('/catalogue/searchPUL/table/taxonomy/level_id/1/parent_id/'.$parent_item->getId())->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/top/')->
  end();
  