<?php

class darwinMigrateSearchTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace        = 'darwin';
    $this->name             = 'migrate-search';
    $this->briefDescription = 'migrate saved searches from php to json';
    $this->detailedDescription = <<<EOF
migrate-search
EOF;

  $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $searches = Doctrine::getTable('MySavedSearches')->findAll();
    $i= 0;
    foreach($searches as $search) {
      $req = unserialize($search->getSearchCriterias());
      $search->setUnserialRequest($req);
      $search->save();
      $i++;
    }
    $this->logSection('Done', sprintf('Job done for %d searches',$i));
  }
}
