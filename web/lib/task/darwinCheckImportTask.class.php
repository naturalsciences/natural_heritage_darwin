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
    // Initialize the connection to DB and get the environment (prod, dev,...) this task is runing on
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();
    // Generate a random number that will be used as an identifier for the task (and the logging of task)
    $randnum = rand(1,10000) ;
    $this->log("Start Check $randnum : ".date('G:i:s'));
    // If no no-delete option is given, then remove the lines from staging with the state set to 'deleted'
    if(empty($options['no-delete'])) {
      $this->logSection('Delete', sprintf('Check %d : Removing some deleted import lines',$randnum)) ;
      // No more than 2000 lines deleted at once per task, to avoid latencty ;)
      $batch_nbr = 2000;
      $sql = "delete from staging where ctid = ANY (select s.ctid 
                                                    from staging s 
                                                    inner join imports i 
                                                       on s.import_ref = i.id 
                                                       and i.state='deleted' 
                                                    limit $batch_nbr
                                                   );";
      $ctn = $conn->getDbh()->exec($sql);

      // Remove the import reference from imports table if no more lines in staging for this import
      $sql = "delete from imports i WHERE i.state='deleted' AND NOT EXISTS (select 1 from staging where import_ref = i.id)";
      $conn->getDbh()->exec($sql);
      $this->logSection('Delete', sprintf('Check %d : Removed %d lines',$randnum, $ctn)) ;
    }

    // Log the fact that if an id provided is not a digit, send an error and stop the execution
    if(!empty($options['id']) && ! ctype_digit($options['id']) )
    {
      $this->logSection('id not int', sprintf('the Id parameter must be an integer (id of import)'),null, 'ERROR') ;
      return;
    }

    // Define what's checkable (and therefore importable if do-import option is defined)
    if (empty($options['full-check']))
      $state_to_check = array('loaded','processing');
    else
      $state_to_check = array('loaded','pending','processing');

    // let's 'lock' all imports checkable to avoid an other check from the next check task
    $catalogues = Doctrine::getTable('Imports')->tagProcessing('taxon', $options['id']);
    // Get back here the list of imports id that could be treated
    $imports = Doctrine::getTable('Imports')->tagProcessing($state_to_check, $options['id']);
    $imports_ids = $imports->toKeyValueArray("id", "id");
    // let's begin with import catalogue
    foreach($catalogues as $catalogue)
    {
      $date_start = date('G:i:s') ;
      $this->logSection('Processing', sprintf('Check %d : Start processing Catalogue import %d (start: %s)',$randnum, $catalogue->getId(),$date_start));
      // Begin here the transactional process
      $conn->beginTransaction();
      try {
        $conn->execute("select fct_importer_catalogue(?,'taxonomy',?)",
                       array(
                         $catalogue->getId(),
                         (integer) $catalogue->getExcludeInvalidEntries()
                       )
        );
        $conn->commit();
        $sql_prepared = $conn->prepare("UPDATE imports set state='finished', is_finished = TRUE WHERE id = ?");
        $sql_prepared->execute(array($catalogue->getId()));
      }
      catch (\Exception $e) {
        $conn->rollback();
        $sql_prepared = $conn->prepare("UPDATE imports set errors_in_import = ?, state='error' WHERE id = ?");
        $sql_prepared->execute(array(ltrim($conn->errorInfo()[2], 'ERROR: '), $catalogue->getId()));
      }
      $this->logSection('Processing', sprintf('Check %d : End processing Catalogue import %d (start: %s - end: %s)',$randnum, $catalogue->getId(),$date_start,date('G:i:s')));
    }
    // Check we've got at least one import concerned - if not, no check, no do-import :)
    if(count($imports_ids)) {
      $imports_ids_string = implode(',', $imports_ids);
      // Begin here the transactional process for the check-import
      $conn->beginTransaction();
      $this->logSection('checking', sprintf('Check %d : (%s) Start checking staging',$randnum,date('G:i:s')));
      // now let's check all checkable staging - the checkability is coming from list of id in imports array
      $conn->exec("SELECT fct_imp_checker_manager(s.*)
                    FROM staging s, imports i
                    WHERE s.import_ref=i.id
                      AND i.id = ANY('{ $imports_ids_string }'::int[])
                      AND i.state != 'aprocessing'"
      );
      $this->logSection('checking', sprintf('Check %d : (%s) Checking ended',$randnum,date('G:i:s')));
      // Close here the transactional process responsible of either taxonomic import or 
      // of checking
      $conn->commit();
      // Check done, all loaded import won' t be imported again. So we can put then into pending state
      Doctrine_Query::create()
              ->update('imports p')
              ->set('p.state','?','pending')
              ->andWhereIn('p.state',array('aloaded','apending'))
              ->andWhereIn('p.id', $imports_ids)
              ->execute();

      // if followed by process of do-import...
      if(!empty($options['do-import']))
      {
        // Initialize the variable that will hold all the imports id to be processed for
        // changing the state from aprocessing to pending
        $processed_ids = array();
        // We need to begin an other transaction for the importing of lines in aprocessing
        $conn->beginTransaction();

          $this->logSection('fetch', sprintf('Check %d : (%s) Load Imports file in processing state',$randnum,date('G:i:s')));
          
          $imports  = Doctrine::getTable('Imports')->getWithImports($options['id']); 

          foreach($imports as $import)
          {
            $processed_ids[] = $import->getId();
            $date_start = date('G:i:s') ;
            $sql_prepared = $conn->prepare("select fct_importer_abcd(?)");
            $sql_prepared->execute(array($import->getId()));
            $this->logSection('Processing', sprintf('Check %d : Processing import %d (start: %s - end: %s) done',$randnum,$import->getId(),$date_start,date('G:i:s')));
          }
        // Work done, we need to release hand by a commit
        $conn->commit();
        // Ok import line asked but 0 ok lines... so it can remain some line in processing not processed...
        // or simply work done... We then need to set the state back to pending for the current imports
        Doctrine_Query::create()
                ->update('imports p')
                ->set('p.state','?','pending')
                ->andWhere('p.state = ?','aprocessing')
                ->andWhereIn('p.id', $processed_ids)
                ->execute();
      }
    }
    $this->log("End Check $randnum : ".date('G:i:s'));
  }
}
