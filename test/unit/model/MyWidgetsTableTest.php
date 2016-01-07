<?php 
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(22, new lime_output_color());

$userEvil = Doctrine::getTable('Users')->findOneByFamilyName('Evil')->getId();

try
{
  Doctrine::getTable('MyWidgets')->getWidgets('board_widget');
  $t->fail('Must throw an exception for not giving user id');
}
catch (Exception $e)
{
  $t->pass('exception caught successfully');
}

$t->info('->getWidgets()');
$t->isnt(Doctrine::getTable('MyWidgets')->setUserRef($userEvil)->addCategoryUser(null,'board_widget'), null,'It\'s not null');

$t->is(count(Doctrine::getTable('MyWidgets')
        ->setUserRef($userEvil)
        ->getWidgets('board_widget')),6,'Get all board widget');
$t->is(count(Doctrine::getTable('MyWidgets')
        ->setUserRef($userEvil)
        ->getWidgets('specimen_widget')),35,'Get all specimen widget');

$t->comment('->changeWidgetStatus()');

$widget = Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->createQuery()
    ->andWhere("group_name = ?","savedSpecimens")
    ->fetchOne();

$t->is($widget->getOpened(),false, "Status is Open");

Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->changeWidgetStatus("board_widget","savedSpecimens","close");

$widget = Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->createQuery()
    ->andWhere("group_name = ?","savedSpecimens")
    ->fetchOne();

$t->is($widget->getOpened(),false, "Status Open/Close changing");

Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->changeWidgetStatus("board_widget","savedSpecimens","hidden");

$widget = Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->createQuery()
    ->andWhere("group_name = ?","savedSpecimens")
    ->fetchOne();

$t->is($widget->getVisible(),false, "Set status hidden");

Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->changeWidgetStatus("board_widget","savedSpecimens","visible");

$widget = Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->createQuery()
    ->andWhere("group_name = ?","savedSpecimens")
    ->fetchOne();

$t->is($widget->getVisible(),true, "Is status changed to visible");
$t->is($widget->getOpened(),true, "Is status changed to opened");
$t->is($widget->getColNum(),1, "Is colnum changed to 1");


$t->comment('->updateWidgetsOrder()');

try {
Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->updateWidgetsOrder("savedSpecimens,savedSearch", 2, "board_widget");
    $t->fail('updateWidgetsOrder must throw a exception for bad arguments');
}
catch(Exception $e)
{
    $t->pass('exception for bad arguments well throws');
}


Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->updateWidgetsOrder(array("savedSpecimens","savedSearch"), 2, "board_widget");

$widgets = Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->createQuery()
    ->andWhereIn("group_name",array("savedSpecimens","savedSearch"))
    ->orderBy('group_name ASC')
    ->execute();

$t->is($widgets[1]->getOrderBy(),1, "Is order set correctly");
$t->is($widgets[0]->getOrderBy(),2, "Is order set for everyone");
$t->is($widgets[0]->getColNum(),2, "Is col_num set correctly");

$t->comment('->changeOrder()');
Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->changeOrder('board_widget',array("savedSpecimens"), array("savedSearch"));

$widgets = Doctrine::getTable('MyWidgets')
    ->setUserRef($userEvil)
    ->createQuery()
    ->andWhereIn("group_name",array("savedSpecimens","savedSearch"))
    ->orderBy('group_name ASC')
    ->execute();

$t->is($widgets[0]->getOrderBy(),1, "Is order set correctly");
$t->is($widgets[1]->getOrderBy(),1, "Is order set for everyone");
$t->is($widgets[0]->getColNum(),2, "Is col_num set correctly");
$t->is($widgets[1]->getColNum(),1, "Is col_num for everyone");

$q = Doctrine_Query::create()
    ->delete('MyWidgets p')
    ->Where('p.user_ref = ?', $userEvil)
    ->execute();
    
$t->is(count(Doctrine::getTable('MyWidgets')
        ->setUserRef($userEvil)
        ->getWidgets('board_widget')),0,'All widget would have been deleted');

$t->comment('->addWidgets()');

$brol_user = new Users();
$brol_user->setFamilyName('Brolus');
$brol_user->setGivenName('Brolus');
$brol_user->setDbUserType(Users::REGISTERED_USER);
$brol_user->save();

$brol_user->addUserWidgets();

$t->comment('->updateWigetsAvailabilityForRole()');
$t->is(count(Doctrine::getTable('MyWidgets')
        ->setUserRef($brol_user->getId())
        ->setDbUserType(Users::REGISTERED_USER)
        ->getWidgets('board_widget')),5,'6 board widgets but only 5 visible for a registered user');

$widget_workflowsSummary = Doctrine_Query::create()
                          ->from("MyWidgets p")
                          ->where("p.user_ref = ?", array($brol_user->getId()))
                          ->andWhere("p.category = ?", array("board_widget"))
                          ->andWhere("p.group_name = ?", array("workflowsSummary"))
                          ->fetchArray();

$collection_mollusca =  Doctrine_Query::create()
                          ->select("c.id")
                          ->from("Collections c")
                          ->where("c.name = ?", array("Molusca"))
                          ->fetchArray();

$t->info('Testing the "doUpdateWidgetRight" method');
Doctrine::getTable('MyWidgets')
  ->setUserRef($brol_user->getId())
  ->setDbUserType(Users::REGISTERED_USER)
  ->doUpdateWidgetRight($collection_mollusca[0]['id'],array($widget_workflowsSummary[0]['id']),'insert');

$widget_workflowsSummary = Doctrine_Query::create()
                                         ->from("MyWidgets p")
                                         ->where("p.user_ref = ?", array($brol_user->getId()))
                                         ->andWhere("p.category = ?", array("board_widget"))
                                         ->andWhere("p.group_name = ?", array("workflowsSummary"))
                                         ->fetchArray();

$t->is(count(Doctrine::getTable('MyWidgets')
                     ->setUserRef($brol_user->getId())
                     ->setDbUserType(Users::REGISTERED_USER)
                     ->getWidgets('board_widget', $collection_mollusca[0]['id'])),6,'6 board widgets if Molusca collection given');

Doctrine::getTable('MyWidgets')->setUserRef($brol_user->getId())->updateWigetsAvailabilityForRole(Users::REGISTERED_USER, false) ;

$t->is(count(Doctrine::getTable('MyWidgets')
        ->setUserRef($brol_user->getId())
        ->getWidgets('board_widget')),1,'Removing \'Registered user\' right : 1 board widgets visible now (stats)');     
