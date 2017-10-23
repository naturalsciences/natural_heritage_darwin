<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(38, new lime_output_color());

$t->info('findGroupsIdsForRecord');
$syns = Doctrine::getTable('ClassificationSynonymies')->findGroupsIdsForRecord('taxonomy',4);
$t->is_deeply($syns, array(1,2), 'We have ids for the group of a record');
$syns = Doctrine::getTable('ClassificationSynonymies')->findGroupsIdsForRecord('taxonomy',5);
$t->is_deeply($syns, array(1), 'We have ids for the only group of another record');
$syns = Doctrine::getTable('ClassificationSynonymies')->findGroupsIdsForRecord('taxonomy',-1);
$t->is_deeply($syns, array(), 'We don\'t have id for not existing group');

$t->info('findAllForRecord');
$records = Doctrine::getTable('ClassificationSynonymies')->findAllForRecord('taxonomy', 4);
$t->is($records['homonym'][0]['id'],5,'They are the same');
$t->is(count($records['homonym']),2,'They are the same');
$t->is($records['synonym'][0]['id'],2,'They are the same');
$t->is(count($records['synonym']),2,'They are the same');

$t->is($records['homonym'][0]['id'],5,'They are the same');
$t->is(count($records['homonym']),2,'They are the same');
$t->is($records['homonym'][1]['id'],4,'They are the same');

$records = Doctrine::getTable('ClassificationSynonymies')->findAllForRecord('taxonomy', 4, array(2));

$t->is($records['homonym'][0]['id'],5,'They are the same');
$t->is(count($records['homonym']),2,'They are the same');
$t->is($records['homonym'][1]['id'],4,'They are the same');

$t->info('findGroupnames');
$groups = Doctrine::getTable('ClassificationSynonymies')->findGroupnames();

$t->is_deeply($groups, array('synonym' => 'Synonyms', 'isonym' => 'Isonyms', 'homonym' => 'Homonyms',  'rename' => 'Renaming'),'Get all groups');

$t->info('findNextGroupId');
$id = Doctrine::getTable('ClassificationSynonymies')->findNextGroupId();
$id2 = Doctrine::getTable('ClassificationSynonymies')->findNextGroupId();

$t->is($id+1, $id2,'Get Next Id');


$t->info('findGroupIdFor');

$group = Doctrine::getTable('ClassificationSynonymies')->findGroupIdFor('taxonomy', 4, 'homonym');
$t->is(2, $group,'We get the group id for homonym');

$group = Doctrine::getTable('ClassificationSynonymies')->findGroupIdFor('taxonomy', 4, 'synonym');
$t->is(1, $group,'We get the group id for synonym');

$group = Doctrine::getTable('ClassificationSynonymies')->findGroupIdFor('taxonomy', 3, 'synonym');
$t->is(0 ,$group,'We get no group id for synonym');


$t->info('saveOrder');
Doctrine::getTable('ClassificationSynonymies')->saveOrder('4,5');
$records = Doctrine::getTable('ClassificationSynonymies')->findAllForRecord('taxonomy', 3);

$t->is($records['homonym'][0]['id'],4,'They are the same');
$t->is(count($records['homonym']),2,'They are the same');
$t->is($records['homonym'][1]['id'],5,'They are the same');

Doctrine::getTable('ClassificationSynonymies')->saveOrder('2,3');

$records = Doctrine::getTable('ClassificationSynonymies')->findAllForRecord('taxonomy', 4, array(1));
$t->is($records['synonym'][0]['id'],2,'They are the same');
$t->is(count($records['synonym']),2,'They are the same');
$t->is($records['synonym'][1]['id'],3,'They are the same');



$t->info('setBasionym');
$records = Doctrine::getTable('ClassificationSynonymies')->findAllForRecord('taxonomy', 5, array(1));
Doctrine::getTable('ClassificationSynonymies')->setBasionym(1, $records['synonym'][0]['id']);
$el = Doctrine::getTable('ClassificationSynonymies')->find($records['synonym'][0]['id']);
$t->is($el->getIsBasionym(), true,'We have set the basionym');

$el = Doctrine::getTable('ClassificationSynonymies')->find(3);
$t->is($el->getIsBasionym(), false,'for others the basionym is reset');

$t->info('mergeSynonyms');

Doctrine::getTable('ClassificationSynonymies')->mergeSynonyms('taxonomy', 4, 2, 'synonym');
$syn = Doctrine::getTable('ClassificationSynonymies')->findByGroupId(1);
$t->is(count($syn), 4, 'Group 1 and 2 merge to group 1');

Doctrine::getTable('ClassificationSynonymies')->mergeSynonyms('taxonomy', 3, 2, 'homonym');
$syn = Doctrine::getTable('ClassificationSynonymies')->findByGroupId(2);
$t->is(count($syn), 3, 'record added to to group 2');

Doctrine::getTable('ClassificationSynonymies')->mergeSynonyms('taxonomy', 2, 6, 'homonym');
$syn = Doctrine::getTable('ClassificationSynonymies')->findByGroupId(2);
$t->is(count($syn), 4, 'group 2 has new record');

Doctrine::getTable('ClassificationSynonymies')->mergeSynonyms('taxonomy', 2, 6, 'isonym');
$syn = Doctrine::getTable('ClassificationSynonymies')->findByGroupId( Doctrine::getTable('ClassificationSynonymies')->findNextGroupId() - 1);
$t->is(count($syn), 2, 'two records create a new group');

Doctrine::getTable('ClassificationSynonymies')->mergeSynonyms('taxonomy', 4, 6, 'isonym');
$syn = Doctrine::getTable('ClassificationSynonymies')->findByGroupId( $syn[0]->getGroupId());
$t->is(count($syn), 3, 'the record join a group records create a new group');

Doctrine::getTable('ClassificationSynonymies')->mergeSynonyms('taxonomy', 3, 5, 'isonym');
$syn = Doctrine::getTable('ClassificationSynonymies')->findByGroupId( Doctrine::getTable('ClassificationSynonymies')->findNextGroupId() - 1);
$t->is(count($syn), 2, 'We create a new group');


$rec1 = Doctrine_Query::create()
	 ->from('ClassificationSynonymies s')
	 ->andWhere('s.group_name = ?','isonym')
	 ->andWhere('s.record_id = ?',4)
	 ->fetchOne();
$rec1->setIsBasionym(true)->save();
$t->is($rec1->getIsBasionym(), true,'We set basionymies for the first rec');

$rec2 = Doctrine_Query::create()
	 ->from('ClassificationSynonymies s')
	 ->andWhere('s.group_name = ?','isonym')
	 ->andWhere('s.record_id = ?',3)
	 ->fetchOne();

$rec2->setIsBasionym(true)->save();
$t->is($rec2->getIsBasionym(), true,'We set basionymies for the second rec');


Doctrine::getTable('ClassificationSynonymies')->mergeSynonyms('taxonomy', 3, 6, 'isonym');
$syn = Doctrine::getTable('ClassificationSynonymies')->findByGroupId($syn[0]->getGroupId());
$t->is(count($syn), 5, 'We merge all groups');

$recs1 = Doctrine::getTable('ClassificationSynonymies')->findByRecordId(4);
$recs2 = Doctrine::getTable('ClassificationSynonymies')->findByRecordId(3);
foreach($recs1 as $rec)
{
  if($rec->getGroupName()=='isonym')
    $t->is($rec->getIsBasionym(), false,'Basio is reset');
}
foreach($recs2 as $rec)
{
  if($rec->getGroupName()=='isonym')
    $t->is($rec->getIsBasionym(), false,'Basio is reset');
}



$t->info('deleteAllItemInGroup');

Doctrine::getTable('ClassificationSynonymies')->deleteAllItemInGroup(2);
$syn = Doctrine::getTable('ClassificationSynonymies')->findByGroupId(2);
$t->is(count($syn), 0, 'All record in group was deleted');
