<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new DarwinTestFunctional(new sfBrowser());
$browser->loadData($configuration)->login('root','evil');
$browser->setTester('doctrine', 'sfTesterDoctrine');
$taxon = Doctrine::getTable('Taxonomy')->findOneByName('Falco Peregrinus Tunstall, 1771');
$taxonId = $taxon->getId();
$secondTaxon = Doctrine::getTable('Taxonomy')->findOneByName('Eucaryota');
$secondTaxonId = $secondTaxon->getId();
$collection = Doctrine::getTable('Collections')->findOneByName('Aves');
$collectionId = $collection->getId();
$collection->setCodePrefix('AVES');
$collection->setCodePrefixSeparator('/');
$collection->setCodeSuffix('LOULOU');
$collection->setCodeSuffixSeparator('-');
$collection->save();

$browser->
  info('1 - New Specimen screen')->
  get('/specimen/new')->

  with('request')->begin()->
    isParameter('module', 'specimen')->
    isParameter('action', 'new')->
  end()->

  info('1.1 - is everything ok on screen')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('title','Add specimens')->
    checkElement('.board_col',2)->
    checkElement('.board_col:first .widget',11)->
    checkElement('.board_col:first .widget:first .widget_top_bar span','/Collection/')->
    checkElement('.board_col:first .widget:nth-child(2) .widget_top_bar span','/Codes/')->
    checkElement('.board_col:first .widget:nth-child(2) .widget_content thead tr',4)->
    checkElement('.board_col:first .widget:nth-child(2) .widget_content thead tr:first th',1)->
    checkElement('.board_col:first .widget:nth-child(2) .widget_content thead tr:first th','/Mask:/')->
    checkElement('.board_col:first .widget:nth-child(2) .widget_content thead tr:nth-child(2) th',1)->
    checkElement('.board_col:first .widget:nth-child(2) .widget_content thead tr:nth-child(3) th',7)->
    checkElement('.board_col:first .widget:nth-child(2) .widget_content thead tr:nth-child(4) th',5)->
    checkElement('.board_col:last .widget:first .widget_top_bar span','/Acquisition/')->
    checkElement('.board_col:last .widget:nth-child(2) .widget_top_bar span','/Expedition/')->
    checkElement('.board_col:last .widget:nth-child(3) .widget_top_bar span','/I.G. number/')->
    checkElement('.board_col:last .widget:nth-child(4) .widget_top_bar span','/Sampling location/')->
    checkElement('.board_col:last .widget:nth-child(5) .widget_top_bar span','/Collectors/')->    
    checkElement('.board_col:last .widget:nth-child(6) .widget_top_bar span','/Properties/')->    
    checkElement('.board_col:last .widget:nth-child(7) .widget_top_bar span','/Comments/')->    
  end();

$browser->
  info('2 - Specimen search')->
  get('/specimen/index')->

  with('request')->begin()->
    isParameter('module', 'specimen')->
    isParameter('action', 'index')->
  end()->

  info('2.1 - is everything ok on screen')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('table.search tbody:first tr td:nth-child(2) input#searchSpecimen_taxon_name',1)->
    checkElement('table.search tbody:last tr td:last input#searchSpecimen_caller_id',1)->
    checkElement('table.search tbody:first tr td:last select#searchSpecimen_taxon_level_ref',1)->
    checkElement('table.search tbody:first tr td:last select#searchSpecimen_taxon_level_ref option',55)->
  end()->
  info('2.2 - Post waiting for the full results table and the pager')->
  post('/specimen/search', array('is_choose'=>0, 'searchSpecimen'=>array()))->
  with('response')->
  begin()->
    isStatusCode()->
    checkElement('ul.pager_nav li', 10)->
    checkElement('ul.pager_nav li.page_selected', '[1]')->
    checkElement('.pager li a span.nav_arrow', 0)->
    checkElement('div.paging_info table td:nth-child(2)', 1)->
    checkElement('div.paging_info table td:last-child select[id="searchSpecimen_rec_per_page"]', 1)->
    checkElement('table.results tbody tr', 4)->
    checkElement('table.results tbody tr td:nth-child(1)', 'Amphibia')->
    checkElement('table.results thead th:nth-child(1) a.sort span.order_sign_down')->
  end()->  
  info('2.3 - Click to sort on name descending...')->
  post('/specimen/search', array('orderby'=>'taxon_name', 'orderdir'=>'desc', 'page'=>1, 'is_choose'=>0, 'searchSpecimen'=>array()))->
  with('response')->
  begin()->
    checkElement('table.results thead th:nth-child(3) a.sort span.order_sign_up')->
  end()->

  info('2.4 - Select only species level...')->
  post('/specimen/search', array('orderby'=>'taxon_name', 'orderdir'=>'asc', 'page'=>1, 'is_choose'=>0, 'searchSpecimen'=>array('taxon_level_ref'=>48)))->
  with('response')->
  begin()->
    checkElement('table.results tbody tr', 1)->
    checkElement('table.results tbody tr td:nth-child(3)', 'Falco Peregrinus')->
  end()->
  info('2.5 - edition...');
  $record = Doctrine::getTable('Specimens')->findOneByTaxonRef('2');//Falco Peregrinus');
  
$browser->
    get('specimen/edit?id='.$record->getId())->
    with('response')->begin()->
    isStatusCode(200)->
    checkElement('title','Edit Specimen')->
    checkElement('.board_col',2)->
  end()->
  info('3 - Edit specimen - Change Taxon')->
  click('Save', 
        array('specimen' => array('taxon_ref'  => $taxonId,
                                  'collection_ref' => $collectionId,
                                 )
             )
       )->
  with('response')->begin()->
    isRedirected()->
  end()->

  followRedirect()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('.widget_content #specimen_collection_ref_name')->
    checkElement('.widget_content #specimen_taxon_ref_name')->
  end()->

  info('4 - Check sameTaxon action call')->
  info('4.1 - ...without arguments')->  
  get('/specimen/sameTaxon')->
  with('response')->begin()->
    matches('/ok/')->
  end();

$specimens =  Doctrine_Query::create()
            ->from('Specimens')
            ->orderBy('collection_ref ASC, taxon_ref ASC, id ASC')->execute();
$specId = $specimens[0]->getId();
$people_ref = Doctrine::getTable('People')->getPeopleByName('Root')->getId();
$browser->
  info('4.2 - ...with a specimen id and a taxon id different from one associated to specimen called')->  
  get('specimen/sameTaxon', array('specId'=>$specId, 'taxonId'=>'-1'))->
  with('response')->begin()->
    matches('/not ok/')->
  end()->
  info('4.3 - ...with a specimen id and a corresponding taxon id')->  
  get('specimen/sameTaxon', array('specId'=>$specId, 'taxonId'=>$specimens[0]->Taxonomy->getId()))->
  with('response')->begin()->
    matches('/ok/')->
  end()->
  info('4.4 - Check getTaxon action call without arguments')->  
  get('specimen/getTaxon')->
  with('response')->begin()->
    isStatusCode(404)->
  end();

$num = 5;

$browser->
  info('5. - Check AddCode method call')->
  info('5.1 - First add...')->
  info('informations given are'.print_r(array('id'=>$specId, 'num'=>$num, 'collectionId'=>$collectionId)))->
  get('specimen/addCode', array('id'=>$specId, 'num'=>$num, 'collectionId'=>$collectionId))->
  with('response')->begin()->
    isStatusCode()->
    checkElement('tr td:first select#specimen_newCodes_'.$num.'_code_category',1)->
    checkElement('tr td:nth-child(2) input#specimen_newCodes_'.$num.'_code_prefix',1)->
    checkElement('tr td:nth-child(3) div#specimen_newCodes_'.$num.'_code_prefix_separator_parent',1)->
    checkElement('tr td:nth-child(4) input#specimen_newCodes_'.$num.'_code',1)->
    checkElement('tr td:nth-child(4) input[value="124"]',1)->
    checkElement('tr td:nth-child(5) div#specimen_newCodes_'.$num.'_code_suffix_separator_parent',1)->
    checkElement('tr td:nth-child(6) input#specimen_newCodes_'.$num.'_code_suffix',1)->
  end();

$num +=1;

$browser->
  info('5.2 - Check AddCode auto-incrementation method call')->
  get('specimen/addCode', array('id'=>$specId, 'num'=>$num, 'collectionId'=>$collectionId))->
  with('response')->begin()->
    isStatusCode()->
    checkElement('tr td:first select#specimen_newCodes_'.$num.'_code_category',1)->
    checkElement('tr td:nth-child(2) input#specimen_newCodes_'.$num.'_code_prefix',1)->
    checkElement('tr td:nth-child(3) div#specimen_newCodes_'.$num.'_code_prefix_separator_parent',1)->
    checkElement('tr td:nth-child(4) input#specimen_newCodes_'.$num.'_code',1)->
    checkElement('tr td:nth-child(4) input[value="125"]',1)->
    checkElement('tr td:nth-child(5) div#specimen_newCodes_'.$num.'_code_suffix_separator_parent',1)->
    checkElement('tr td:nth-child(6) input#specimen_newCodes_'.$num.'_code_suffix',1)->
  end();

$num = 1;
$identifier_num = 1;

$browser->
  info('6 - Check AddIdentification method call')->
  get('specimen/addIdentification', array('spec_id'=>$specId, 'num'=>$num))->
  with('response')->begin()->
    isStatusCode()->
    checkElement('tr:first td:first[class="spec_ident_handle"]',1)->
    checkElement('tr:first td:nth-child(3) select#specimen_newIdentification_'.$num.'_notion_concerned option',11)->
    checkElement('tr:first td:nth-child(3) select#specimen_newIdentification_'.$num.'_notion_concerned option:first','All')->
    checkElement('tr:first td:last input:last[id="specimen_newIdentification_'.$num.'_order_by"][value="0"]',1)->
    checkElement('tr:nth-child(2)[class="spec_ident_identifiers"]',1)->
    checkElement('tr:nth-child(2)[class="spec_ident_identifiers"] td:nth-child(2) table#spec_ident_identifiers_'.$num,1)->
    checkElement('tr:nth-child(2)[class="spec_ident_identifiers"] td:nth-child(2) table#spec_ident_identifiers_'.$num.' tfoot div a[href="/index.php/specimen/addIdentifier/spec_id/'.$specId.'/num/'.$num.'/identifier_num/"]',1)->
  end();

$browser->
  info('7 - Check AddIdentifier method call')->
  get('specimen/addIdentifier', array('spec_id'=>$specId,'people_ref' => $people_ref, 'num'=>$num, 'identifier_num'=>$identifier_num))->
  with('response')->begin()->
    isStatusCode()->
    checkElement('.spec_ident_identifiers_handle',1)->
 end();
$browser->
  info('8 - add a property')->
  get('property/add/table/specimens/id/'.$specId)->
  with('response')->begin()->
    isStatusCode(200)->
    click('#submit',
          array('properties' => array(
            'property_type' => 'length',
            'applies_to' => 'ear size',
            'lower_value'=>  '120',
            'property_unit'=> 'cm',
          )
        )
       )->end()->
with('doctrine')->begin()
  ->check('Properties', array(
    'property_type' => 'length',
    'applies_to' => 'ear size'))
   ->end();

$browser->addCustomSpecimen();
$browser->with('response')->begin()->
      isRedirected()->
      followredirect()->
  end()->
  with('response')->begin()->
  isStatusCode()->
  checkElement('.board_col:first .widget:nth-child(2) tbody',2)->
  checkElement('.board_col:first .widget:nth-child(5) .spec_ident_identifiers_handle',2)->
  checkElement('table.collectors tr.spec_ident_collector_data',2)->
  checkElement('#specimen_Comments_0_comment','Test comment for a collector')-> 
  click('Delete')->
  end() ;

$browser->  
  info('9 - check if specimen is well deleted')->
  get('specimens/edit/id/'.$specId)->
  with('response')->begin()->
    isStatusCode(404)->
    end();

