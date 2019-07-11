<?php

class darwinRemoveFilesTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'remove-files';
    $this->briefDescription = 'remove files from deleted multimedia lines';
    $this->detailedDescription = <<<EOF
Look into the multimedia-todelete table and remove old , unused files.
Then, delete the row in multimedia-todelete.
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
     // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();
    $i=0;
    while($media = Doctrine_Query::create()->from('MultimediaTodelete')->fetchOne()) {
      $file = $media->getUri();
      try{
        $media->deleteFile();
        $this->logSection('File Deleted', "The file $file was deleted") ;
        $media->delete();
        $i++;
      }
      Catch(Exception $e)
      {
        $this->logSection('Delete Error', $e->getMessage() . ". File: $file",null, 'ERROR') ;
        break;
      }
    }
    $this->logSection('Finished', "All files removed ($i)");
  }
}
