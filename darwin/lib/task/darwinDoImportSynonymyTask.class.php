<?php

class darwinDoImportSynonymyTask extends sfBaseTask
{
	protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      
      new sfCommandOption('id', null, sfCommandOption::PARAMETER_REQUIRED, 'Only do the job for a given import id'),
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'do-import-synonymy';
    $this->briefDescription = 'Store checked synonyms into the database ';
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
	if(empty($options['id']))
	{
		$this->log("ID parameter missing");
	}
	elseif(!empty($options['id']) && ! ctype_digit($options['id']) )
    {
      $this->logSection('id not int', sprintf('the Id parameter must be an integer (id of import)'),null, 'ERROR') ;
      return;
    }
	else
	{	
		$import_id=$options['id'];
		$import = Doctrine_Core::getTable('Imports')->findOneById($import_id);
		if($import->getFormat()=="synonymies")
		{
			$this->log("Going for ID ".$import_id);
			$conn->beginTransaction();
			try 
			{
				$conn->execute("SELECT darwin2.fct_rmca_taxonomy_create_synonym_import(staging_synonymies.*)
					FROM staging_synonymies where import_ref=?;",[$import_id]);
				
				$sql_prepared = $conn->prepare("UPDATE imports set state='finished', is_finished = TRUE WHERE id = ?");
				$sql_prepared->execute([$import_id]);
				$conn->commit();
				
				$conn->beginTransaction();
				$conn->execute("select fct_rmca_imp_checker_manage_synonyms_launch(?)",[$import_id]);				
				$sql_prepared = $conn->prepare("UPDATE imports set state='finished', is_finished = TRUE WHERE id = ?");
				$sql_prepared->execute([$import_id]);
				$conn->commit();
			}			
		   catch (\Exception $e) 
		   {
			$conn->rollback();
			$this->log("Exception ".$e->getMessage());
			$this->log("Exception ".$e->getTraceAsString());
		  }
		}
		
	}
    $this->log("End Check Syno : ".date('G:i:s'));
  }
}