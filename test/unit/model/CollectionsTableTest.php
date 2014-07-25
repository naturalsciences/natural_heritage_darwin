<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(12, new lime_output_color());

$t->info('fetchByInstitutionList');

$user = Doctrine::getTable('Users')->getUserByPassword('root','evil') ;

$list = Doctrine::getTable('Collections')->fetchByInstitutionList($user);
$t->is($list[0]->getFormatedName(),'Institut Royal des Sciences Naturelles de Belgique','Thre list give institutions');
$collections = $list[0]->Collections;

$t->is(count($collections),5,'Get all collections');
$t->is($collections[0]->getPath(),'/','The first item has path /');
$t->is($collections[3]->getName(),'Fossile Aves','The childrens item are also fetched');

$t->is($list[1]->getFormatedName(),'UGMM','Thre list give institutions');

$collections = Doctrine::getTable('Collections')->getAllCollections();

$t->is($collections[0]->getName(),'Import collection','The last item is Import collection (correctly order)');

$t->info('getCollectionByName');

$col = Doctrine::getTable('Collections')->getCollectionByName('Import collection');
$t->is($col->getId(), $collections[0]->getId(),'Got the good collection');

$t->info('getDistinctCollectionByInstitution');

$list2 = Doctrine::getTable('Collections')->getDistinctCollectionByInstitution($list[0]->getId());
$t->is(count($list2), 6, 'We have 6 collections in this institution ("" + 4)');

$list2 = Doctrine::getTable('Collections')->getDistinctCollectionByInstitution($list[1]->getId());
$t->is(count($list2), 2, 'We have 2 collections in this institution ("" + 1)');

$t->info('findCollectionsByUser');


$collections = $list[0]->Collections;
$list3 = Doctrine::getTable('Collections')->fetchByCollectionParent($user, $user->getId(), $collections[0]->getId()); 
$t->is(count($list3), 3, 'Vertebrates have 3 children collections ');

$collection = Doctrine::getTable('Collections')->findOneByName('Vertebrates');
$collectionId = $collection->getId();
$value = Doctrine::getTable('Collections')->getAndUpdateLastCode($collectionId);
$t->is(is_numeric($value), true, 'Updated value of "code_last_value" for collection "Vertebrates" is well a number...');
$t->is(($value>0), true, '... and a number above 0');

$t->info('afterSaveAddCode');

print(Doctrine::getTable('Collections')->afterSaveAddCode(134, 1));
print('Collection id is: '.$collectionId);
