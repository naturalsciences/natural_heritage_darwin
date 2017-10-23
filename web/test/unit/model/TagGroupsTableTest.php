<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(15, new lime_output_color());

$t->info('distinct SubGroup()');
$sgroups= Doctrine::getTable('TagGroups')->getDistinctSubGroups('administrative area');
$t->is(count($sgroups),3, 'Get all administrative sub groups');
$t->is($sgroups['country'],'country', 'Country is set');

$sgroups= Doctrine::getTable('TagGroups')->getDistinctSubGroups('brol');

$t->is(count($sgroups),1, 'Get administrative sub groups for this unused');
$t->is($sgroups[''],'', 'thre is only the empty');

$t->info('getPropositions');

$props = Doctrine::getTable('TagGroups')->getPropositions('brussels');
$t->is(count($props),4, 'We got 4 props');

$props = Doctrine::getTable('TagGroups')->getPropositions('Bruselo');
$t->is(count($props),2, 'Got 2 prop');
$t->is($props[0]['tag'],'Brussels', 'Brussels is showed');


$props = Doctrine::getTable('TagGroups')->getPropositions('brussels','administrative area', 'city');
$t->is(count($props),3, 'We got 3 props');
$t->isnt($props[0]['tag'],'Big White Mountain', 'Purpose from only 1 group');

$t->info('fetchTag');

$gtu = Doctrine::getTable('Gtu')->findOneByCode('irsnb');
$tags = Doctrine::getTable('TagGroups')->fetchTag(array($gtu->getId()));
$t->is(count($tags),1, 'We got 1 gtu');

$t->is(count($tags[$gtu->getId()]),2, 'We got 2 group');
$t->is($tags[$gtu->getId()][0]->getGroupName(), 'administrative area', 'administrative is the  group');
$t->is($tags[$gtu->getId()][0]->getSubGroupName(), 'country', 'country is the sub group');
$t->is(count($tags[$gtu->getId()][0]->Tags),4, 'We got 4 tags');

$t->is(TagGroups::getGroup('populated'), 'Populated Places', 'Get a Tag group');
