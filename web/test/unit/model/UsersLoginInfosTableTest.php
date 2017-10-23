<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
 
$t = new lime_test(5, new lime_output_color());
$t->info('Get informations for User "root"');
$users = Doctrine::getTable('Users')->getUserByPassword("root","evil");
$userInfo = Doctrine::getTable('UsersLoginInfos')->getInfoForUser($users->getId());
$t->is($userInfo->count(), 1, '"1" Login info');
$t->is($userInfo[0]->getLoginType(), 'local', 'The login type is well "local"');
$t->is($userInfo[0]->getLoginSystem(), '', 'There are "no" login system encoded');
$userInfo[0]->setLoginSystem('local');
$userInfo[0]->save();
$t->is($userInfo[0]->getLoginSystem(), 'local', 'The login system is well "local" now');
$t->info('Test to get users with system brol :)');
$userInfo = Doctrine::getTable('UsersLoginInfos')->getInfoForUser($users->getId(), 'brol');
$t->is($userInfo->count(), 0, '"No" user with system Brol... and it is normal ! :)');