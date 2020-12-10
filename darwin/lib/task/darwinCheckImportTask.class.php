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
       //ftheeten 2017 08 28
      new sfCommandOption('mailsfornotification', null, sfCommandOption::PARAMETER_REQUIRED, 'The Users for the mail notification'),
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
  //print("EXECUTE");
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
      //ftheeten  2018 02 28
	  if(!empty($options['id']) && ctype_digit($options['id']) )
	  {
		$this->setImportAsWorking($conn, $options['id'], true);
	  }
	  $ctn = $conn->getDbh()->exec($sql);
	  //ftheeten  2018 02 28
	  if(!empty($options['id']) && ctype_digit($options['id']) )
	  {
		$this->setImportAsWorking($conn, $options['id'], false);
	  }

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
    {
       print("full : loaded, processing");
      $state_to_check = array('loaded','processing');
    }
    else
    {
print("full : loaded, pending, processing");
      $state_to_check = array('loaded','pending','processing');
    }
    // let's 'lock' all imports checkable to avoid an other check from the next check task
    $catalogues = Doctrine::getTable('Imports')->tagProcessing('taxon', $options['id']);
    //debug
   
    
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
        //$sql_prepared = $conn->prepare("UPDATE imports set state='finished', is_finished = TRUE WHERE id = ?");
        //ftheeten 2018 06 11 
        $sql_prepared = $conn->prepare("UPDATE imports set state='finished', is_finished = TRUE WHERE id = ? and state !='error'");
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
    print("BEFORE_TEST");
    if(count($imports_ids)>0) 
    {
      //ftheeten 2017 08 29
            print("AFTER");
            $this->setImportAsWorking($conn, $imports_ids, true);

          $imports_ids_string = implode(',', $imports_ids);
		  print($imports_ids_string);
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
		 //print("TRY");
				print("DO IMPORT");
				// Initialize the variable that will hold all the imports id to be processed for
				// changing the state from aprocessing to pending
				$processed_ids = array();
				// We need to begin an other transaction for the importing of lines in aprocessing
				$conn->beginTransaction();

				$this->logSection('fetch', sprintf('Check %d : (%s) Load Imports file in processing state',$randnum,date('G:i:s')));
					  
					  //DBUG
				foreach($imports_ids as $tmp_id)
				{
						
					 $importsTmp = Doctrine::getTable('Imports')->find($tmp_id);
					
					
				}
				//DBUG
				//ftheeten 2020 01 13
					if(!empty($options['id']) &&  ctype_digit($options['id']) )
					{
											
						$imports  = Doctrine::getTable('Imports')->getWithImports($options['id'], "OR"); 
					}
					else
					{
					  $imports  = Doctrine::getTable('Imports')->getWithImports($options['id']); 
                    }
					  foreach($imports as $import)
					  {
						print("!!!!!!!!!!!!!! IMPORT ".$import->getId());
						$processed_ids[] = $import->getId();
						$date_start = date('G:i:s') ;
						//ftheeten 2017 09 15 'try' block added
						try
						{
							print("TRY");
							$sql_prepared = $conn->prepare("select fct_importer_abcd(?)");
							$sql_prepared->execute(array($import->getId()));
							//ftheeten 2017 09 18 handle host/parasite at taxonlevel
							//$sql_prepared = $conn->prepare("SELECT * FROM rmca_move_host_from_specimens_to_taxa(?)");
							//$sql_prepared->execute(array($import->getCollectionRef()));
					  
						}
						catch(\Exception $e)
						{
							 $this->logSection('DATABASE ERROR', $e->getCode());
							 $this->logSection('DATABASE ERROR', $e->getTrace());
							 $this->logSection('DATABASE ERROR', $e->getMessage());
						}
						$this->logSection('Processing', sprintf('Check %d : Processing import %d (start: %s - end: %s) done',$randnum,$import->getId(),$date_start,date('G:i:s')));

					  }
					// Work done, we need to release hand by a commit
					$conn->commit();
					// Ok import line asked but 0 ok lines... so it can remain some line in processing not processed...
					// or simply work done... We then need to set the state back to pending for the current imports
					//print("COMMIT");
					Doctrine_Query::create()
							->update('imports p')
							->set('p.state','?','pending')
							->andWhere('p.state = ?','aprocessing')
							->andWhereIn('p.id', $processed_ids)
							->execute();

          }
          else
          {
            print("NO_DO_IMPORT");
          }
          //ftheeten 2017 08 29
          Doctrine_Query::create()
                  ->update('imports p')
                  ->set('p.state','?','pending')
                  ->andWhereIn('p.state',array('aloaded','apending'))
                  ->andWhereIn('p.id', $imports_ids)
                  ->execute();
                  
         //ftheeten 2018 09 27
        if (!empty($options['full-check']))
        {
            Doctrine_Query::create()
							->update('imports p')
							->set('p.state','?','pending')
							->andWhere('p.state = ?','aprocessing')
							->andWhereIn('p.id', $imports_ids)
							->execute();
        }        
        $this->setImportAsWorking($conn, $imports_ids, false);
    }
    //print("EXIT");
     //ftheeten 2017 08 28
    if(array_key_exists("mailsfornotification", $options))
    {
        foreach(explode(";",$options["mailsfornotification"]) as $mail)
        {
                 //$this->sendMail($mail, "Darwin XML import quality-checked in staging", "End of quality-check in staging.");
       }
        //$this->sendMail("franck.theeten@africamuseum.be", "Darwin XML import quality-checked in staging", "End of quality-check in staging.");
    }
    $this->log("End Check $randnum : ".date('G:i:s'));
    
    
  }
 //ftheeten 2017 08 28
  // attention keep identation
  public function sendMail($recipient, $title, $messageContent)
 {


    if(filter_var($recipient, FILTER_VALIDATE_EMAIL))
    {
    
      /* mail ( $recipient ,  $title,
<<<EOF
{$messageContent}
EOF
       );*/
    }
 }
 

  
  //ftheeten 2017 08 28
  public function setImportAsWorking( $p_conn, $p_ids, $p_working)
  {
    if(is_array($p_ids)===false)
    {
        $tmp=$p_ids;
        $p_ids=Array();
        $p_ids[]=$tmp;
    }
    if(count($p_ids)>0)
    {
        $p_conn->beginTransaction();
        foreach($p_ids as $id)
        {
         Doctrine_Query::create()
            ->update('imports p')
            ->set('p.working','?',((int)$p_working==0)?'f':'t')
            ->where('p.id = ?', $id)
            ->execute();
        }
        $p_conn->commit();
    }
  }    
}
