<?php

class darwinLoadImportTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'load-import';
    $this->briefDescription = 'Import uploaded file to potgresql staging table';
    $this->detailedDescription = <<<EOF
Nothing
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
     // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env']; 
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();
    $conn->getDbh()->exec('BEGIN TRANSACTION;');
    $staging_id = $conn->fetchOne('SELECT last_value from staging_id_seq;') ;
    while($id = $conn->fetchOne('SELECT get_import_row()'))
    {
        $q = Doctrine_Query::create()
          ->from('imports p')
          ->where('p.id=?',$id)
          ->fetchOne() ;
        $file = sfConfig::get('sf_upload_dir').'/uploaded_'.sha1($q->getFilename().$q->getCreatedAt()).'.xml' ;
        if(file_exists($file))
        {
          try{
            if($q->getFormat() == 'abcd') $import = new importABCDXml() ;
            else $import = new importDnaXml() ;
            $import->parseFile($file,$id, $staging_id) ;
          }
          catch(Exception $e)
          {
            echo $e->getMessage()."\n";break;
          }
          Doctrine_Query::create()
            ->update('imports p')
            ->set('p.state','?','loaded')
            ->set('p.initial_count','(select count(*) from staging where import_ref = ? )',$id)
            ->where('p.id = ?', $id)
            ->execute();
        }
    }
    $conn->getDbh()->exec('COMMIT;');

  }
}
