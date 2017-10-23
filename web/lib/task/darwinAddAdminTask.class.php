<?php

class darwinAddAdminTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name','backend'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('password', 'p', sfCommandOption::PARAMETER_REQUIRED, 'password of the user'), 
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'add-admin';
    $this->briefDescription = 'Add an admin to darwin database';
    $this->detailedDescription = <<<EOF
This task adds an admin to darwin database, 
Create an internal login and add widget for the admin.
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env']; 
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();
    
    $user = new Users();
    
    $user['given_name'] = $this->ask('Given Name :');
    $user['family_name'] = $this->ask('Family Name :');
    while($user['family_name'] == "")
    {
      $this->logSection('Invalid name', "The family name can't be empty, please provide one",null, 'ERROR') ;
      $user['family_name'] = $this->ask('Family Name :');
    }

    $user['db_user_type'] = Users::ADMIN;
    $user->save();
    $this->logSection('User added', 'User is added with admin rights');

    $login = $this->ask('Login :');
    while($login== "")
    {
      $this->logSection('Invalid login', "The login can't be empty, please provide one",null, 'ERROR') ;
      $login = $this->ask('Login :');
    }

    $password = $this->ask('Password :');
    while($password== "")
    {
      $this->logSection('Invalid password', "The password can't be empty, please provide one",null, 'ERROR') ;
      $password = $this->ask('Password :');
    }

    $login_info = new UsersLoginInfos();
    $login_info->setNewPassword($password);

    $login_info->setUserName($login);
    $login_info->setLoginType('local');
    $login_info->setUserRef($user->getId());
    $login_info->save();
    
    $this->logSection('Widgets', "Start adding widgets for this user");
    $user->addUserWidgets();
    $this->logSection('Finished', "Widget added");


  }
}  
