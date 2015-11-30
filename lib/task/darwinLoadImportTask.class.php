<?php

class darwinLoadImportTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
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
    $result = null ;
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();
    $conn->beginTransaction();
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
            switch ($q->getFormat())
            {
              case 'taxon':
                $import = new importCatalogueXml('taxonomy') ;
                $count_line = "(select count(*) from staging_catalogue where parent_ref IS NULL AND import_ref = $id )" ;
                break;              
              case 'abcd':
              default:
                $import = new importABCDXml() ;
                $count_line = "(select count(*) from staging where import_ref = $id )" ;
                break;
            } 
            $result = $import->parseFile($file,$id) ;
            if ( $result == '' && $q->getFormat() == 'taxon') {
              $conn->execute("select fct_clean_staging_catalogue(?)",
                             array(
                               $id
                             )
              );
            }
          }
          catch(Exception $e)
          {
            echo $e->getMessage()."\n";
            $conn->rollback();
            break;
          }
          Doctrine_Query::create()
            ->update('imports p')
            ->set('p.state','?',$result!=''?'error':'loaded')
            ->set('p.initial_count',$count_line)
            ->set('p.errors_in_import','?',$result ? $result : '')
            ->where('p.id = ?', $id)
            ->execute();
        }
    }
    $conn->commit();
  }
}
