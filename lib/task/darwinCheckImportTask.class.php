<?php

class darwinCheckImportTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('do-import', null, sfCommandOption::PARAMETER_NONE, 'if some lines are marked as "to be imported", try to import after the check'),
      new sfCommandOption('full-check', null, sfCommandOption::PARAMETER_NONE, 'if this option is specified, even this import file on pending state are checked'),
      new sfCommandOption('id', null, sfCommandOption::PARAMETER_REQUIRED, 'Only do the job for a given import id'),
      new sfCommandOption('no-delete', null, sfCommandOption::PARAMETER_NONE, 'Do not try to delete old imported lines'),
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'check-import';
    $this->briefDescription = 'check staging lines status and/or import them into real tables';
    $this->detailedDescription = <<<EOF
Nothing
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {    
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();
    $randnum = rand(1,10000) ;
    $this->log("Start Check $randnum : ".date('G:i:s'));
    if(empty($options['no-delete'])) {
      $this->logSection('Delete', sprintf('Check %d : Removing some deleted import lines',$randnum)) ;
      $batch_nbr = 2000;
      $sql = "delete from staging where ctid = ANY (select s.ctid from staging s inner join imports i on s.import_ref = i.id and i.state='deleted' limit $batch_nbr);";
      $ctn = $conn->getDbh()->exec($sql);

      $sql = "delete from imports i WHERE i.state='deleted' AND NOT EXISTS (select 1 from staging where import_ref = i.id)";
      $conn->getDbh()->exec($sql);
      $this->logSection('Delete', sprintf('Check %d : Removed %d lines',$randnum, $ctn)) ;
    }

    if(!empty($options['id']) && ! ctype_digit($options['id']) )
    {
      $this->logSection('id not int', sprintf('the Id parameter must be an integer (id of import)'),null, 'ERROR') ;
    }

     // initialize the database connection
    // First Check :)
    if (empty($options['full-check']))
      $state_to_check = array('loaded','processing') ;
    else
      $state_to_check = array('loaded','processing','pending') ;
    // let's 'lock' all imports checkable to avoid an other check from the next check task
    $catalogues = Doctrine::getTable('Imports')->tagProcessing('taxon'); 
    $imports = Doctrine::getTable('Imports')->tagProcessing($state_to_check); 
    $conn->getDbh()->exec('BEGIN TRANSACTION;');
    // let's begin with import catalogue
    foreach($catalogues as $catalogue)
    {
      $date_start = date('G:i:s') ;
      $sql = 'select fct_importer_catalogue('.$catalogue.',\'taxonomy\')';
      $conn->getDbh()->exec($sql);

      $this->logSection('Processing', sprintf('Check %d : Start processing Catalogue import %d (start: %s - end: %s)',$randnum, $catalogue,$date_start,date('G:i:s')));
    }
    
    // now let's check all checkable staging
    $sql = "select fct_imp_checker_manager(s.*) from staging s, imports i WHERE s.import_ref=i.id" ;
    if(!empty($options['id']))
      $sql.= " AND i.id = ".$options['id'];
    elseif(count($imports)>0)
      $sql .= " AND i.id in (".implode(',', $imports).")";
    else
    {
      // nothing to check, nothing to do
      $conn->getDbh()->exec('COMMIT;');
      $this->log("End Check $randnum : ".date('G:i:s'));      
      return ;
    }
    $this->logSection('checking', sprintf('Check %d : (%s) Start checking staging',$randnum,date('G:i:s')));
    $conn->getDbh()->exec($sql);
    $this->logSection('checking', sprintf('Check %d : (%s) Checking ended',$randnum,date('G:i:s')));
    // Check done, all loaded import won' t be imported again. So we can put then into pending state
    Doctrine_Query::create()
            ->update('imports p')
            ->set('p.state','?','pending')
            ->andWhereIn('p.state',array('aloaded','apending'))
            ->execute();
    if(empty($options['do-import']))
    {
      $sql = "update imports p set state = 'pending' where (state = 'aprocessing' OR state = 'apending' OR state = 'aloaded') and
        exists( select 1 from staging where import_ref = p.id and status != ''::hstore)";
      $conn->getDbh()->exec($sql);
    }
    else
    {
      //Then if option is set, do Import
      $this->logSection('fetch', sprintf('Check %d : (%s) Load Imports file in processing state',$randnum,date('G:i:s')));
      
      $imports  = Doctrine::getTable('Imports')->getWithImports($options['id']); 

      foreach($imports as $import)
      {
        $date_start = date('G:i:s') ;
        $sql = 'select fct_importer_abcd('.$import->getId().')';
        $conn->getDbh()->exec($sql);
        $this->logSection('Processing', sprintf('Check %d : Processing import %d (start: %s - end: %s) done',$randnum,$import->getId(),$date_start,date('G:i:s')));
      }
      // Ok import line asked but 0 ok lines....so it can remain some line in processing not processed....
      Doctrine_Query::create()
              ->update('imports p')
              ->set('p.state','?','pending')
              ->andWhere('p.state = ?','aprocessing')
              ->execute();
    }     
    $conn->getDbh()->exec('COMMIT;');
    $this->log("End Check $randnum : ".date('G:i:s'));
  }
}
