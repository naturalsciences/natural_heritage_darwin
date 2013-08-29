<?php

class darwinMigrateSearchTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace        = 'darwin';
    $this->name             = 'migrate';
    $this->briefDescription = 'migrate from one version to another';
    $this->detailedDescription = <<<EOF
migrate from one version to another
EOF;
  $this->addArguments(array(
      new sfCommandArgument('to_version', sfCommandArgument::REQUIRED, 'The version where we want to migrate'),
  ));

  $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    if($arguments['to_version'] == '44') {
      // Reset Widgets
      $arguments = array(
        'user_id' => 'all',
      );
      $options['category'] = 'specimen_widget' ;
      $task = new darwinAddwidgetsTask($this->dispatcher, new sfFormatter());
      $task->run($arguments, $options);

      // Move Images
    }
/*
    $searches = Doctrine::getTable('MySavedSearches')->findAll();
    $i= 0;
    foreach($searches as $search) {
      $req = unserialize($search->getSearchCriterias());
      $search->setUnserialRequest($req);
      $search->save();
      $i++;
    }
    $this->logSection('Done', sprintf('Job done for %d searches',$i));*/

  }
}
