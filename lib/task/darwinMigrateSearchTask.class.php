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

      //Add 2 more widgets
      $task = new darwinAddwidgetsTask($this->dispatcher, new sfFormatter());
      $options['category'] = 'specimensearch_widget';
      $options['name'] ='properties';
      $task->run($arguments, $options);

      $task = new darwinAddwidgetsTask($this->dispatcher, new sfFormatter());
      $options['category'] = 'specimensearch_widget';
      $options['name'] ='comments';
      $task->run($arguments, $options);

      // Move Images
      $q = Doctrine_Query::create()
        ->from('Multimedia m')
        ->where('referenced_relation= ?', 'specimens')
        ;
      $mmObjects = $q->execute();
      //Remove Old Folder
      $dirsNames=array('specimens', 'specimen_individuals', 'specimen_parts');
      foreach($dirsNames as $dirn) {
        if(is_dir(sfConfig::get('sf_upload_dir')."/multimedia/".$dirn."/") &&
          ! is_dir(sfConfig::get('sf_upload_dir')."/multimedia/old".$dirn."/")) {
          $this->logSection('Move', "Move files directory to". sfConfig::get('sf_upload_dir')."/multimedia/old".$dirn."/") ;
          rename(
            sfConfig::get('sf_upload_dir')."/multimedia/".$dirn."/",
            sfConfig::get('sf_upload_dir')."/multimedia/old".$dirn."/"
          );
        }
      }

      $i=0;
      foreach($mmObjects as $obj) {
        if( file_exists(sfConfig::get('sf_upload_dir')."/multimedia/old".$obj->getUri()) ) {

          $obj->move('old'.$obj->getUri());
          $obj->save();
          $i++;
        } else {
          echo sfConfig::get('sf_upload_dir')."/multimedia/old".$obj->getUri()."\n";
          $this->logSection('Delete Error', "Unable to move File: ". $obj->getUri(),null, 'ERROR') ;
        }
        if($i%10 == 0) {
          $this->logSection('move-file', sprintf('Moved %d files',$i));
        }
      }
    }

  }
}
