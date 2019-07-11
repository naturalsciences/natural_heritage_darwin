<?php

class darwinRMCACreatePeopleTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('id', null, sfCommandOption::PARAMETER_REQUIRED, 'Only do the job for a given import id')));
    $this->namespace        = 'darwin';
    $this->name             = 'create-people';
    $this->briefDescription = 'check staging lines status and/or import them into real tables';
    $this->detailedDescription = <<<EOF
Nothing
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
	   if(!empty($options['id']) && ctype_digit($options['id']) )
	  {
		  try
		  {
                $databaseManager = new sfDatabaseManager($this->configuration);
                $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
                $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
                $conn = Doctrine_Manager::connection();
                print("GO");
				$id_import=$options['id'];
                print($id_import);
                $import_obj = Doctrine_Core::getTable('Imports')->find($id_import);
				
   				// $import_obj->setState("aprocessing");
				$conn->beginTransaction();
				$sql = "SELECT * FROM rmca_create_missing_people_in_staging($id_import)";
				$ctn = $conn->exec($sql);
				$conn->commit();				
				$import_obj->setState("pending");
				 $import_obj->save();
		  }
		  catch(Exception $e)
          {         
            echo $e->getMessage()."\n";
            //ftheeten 2018 07 31
             if($conn->inTransaction())
             {
                $conn->rollback();
             }
             print($q->getId());

          }
	  } 
	

  }
  
}
?>