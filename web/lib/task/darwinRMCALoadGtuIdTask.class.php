<?php

class darwinRMCALoadGtuIdTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('id', null, sfCommandOption::PARAMETER_REQUIRED, 'Only do the job for a given staging_gtu id')));
		$this->namespace        = 'darwin';
		$this->name             = 'import-staging-gtu';
		$this->briefDescription = 'Import new GTU in Darwin table';
		$this->detailedDescription = <<<EOF
Nothing
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
	  $id_import;

	   if(!empty($options['id']) && ctype_digit($options['id']) )
	  {
		  try
		  {
			    $databaseManager = new sfDatabaseManager($this->configuration);
				$environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
				$conn = $databaseManager->getDatabase($options['connection'])->getConnection();
				$id_staging_gtu=$options['id'];
				$conn->beginTransaction();
				$sql = "SELECT * FROM rmca_import_gtu_force(".$id_staging_gtu.");";
				$ctn = $conn->exec($sql);
				$conn->commit();              
		  }
		  catch(Exception $e)
          {         
            echo $e->getMessage()."\n";
            //ftheeten 2018 07 31
             if($conn->inTransaction())
             {
                $conn->rollback();
             }
             
          }
	  }
  }  
}
?>