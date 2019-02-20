<?php

class darwinAddwidgetsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('user_id', sfCommandArgument::REQUIRED, 'The user Id'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('reset', null, sfCommandOption::PARAMETER_NONE, 'delete current data in the database for this user'),
      new sfCommandOption('name', null, sfCommandOption::PARAMETER_REQUIRED, 'add a specific name widget in the database for this user'),
      new sfCommandOption('category', null, sfCommandOption::PARAMETER_REQUIRED, 'add a specific category widget in the database for this user'),
      // add your own options here
    ));

    $this->namespace        = 'darwin';
    $this->name             = 'add-widgets';
    $this->briefDescription = 'Add default widgets for a user';
    $this->detailedDescription = <<<EOF
The [darwin:add-widgets|INFO] task insert all widgets for a user.

Example:
  [php symfony darwin:add-widgets 1|INFO]

If you want the task to remove existing data in the database for this user,
use the [--reset|COMMENT] option:

  [./symfony darwin:add-widgets --reset|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    if($options['name'] && !$options['category'])
    {
      $this->logSection('Incomplete command', sprintf('If you want to add specific widget, you have to give de --name option AND the --category option'),null, 'ERROR') ;
      throw new Exception('Incomplete command');
    }
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    if($arguments['user_id'] == 'all')
    {
      $userIds = Doctrine_Query::create()->select('id')->from('Users')->fetchArray();
    }
    else
      $userIds = explode(",",$arguments['user_id']);

    foreach($userIds as $key=>$value)
    {
      $user = Doctrine::getTable('Users')->find($value);
      if(! $user) {
        $this->logSection('Not Found', sprintf('User %d not found, are you in the right env?',$value));
        continue;
      }
      if($options['reset'])
      {
        Doctrine_Query::create()
          ->delete('MyWidgets p')
          ->where('p.user_ref = ?', $user->getId())
          ->execute();
        $this->logSection('add-widgets', 'Remove old widgets successfully!');
      }
      if($options['category'])
        $cnt = $user->addUserWidgets(array('category' => $options['category'],'name' => $options['name']));
      else
        $cnt = $user->addUserWidgets();
      $this->logSection('add-widgets', sprintf('Added %d widgets successfully!',$cnt));
    }
  }
}
