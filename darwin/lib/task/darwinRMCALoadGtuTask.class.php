<?php

class darwinRMCALoadGtuTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('id', null, sfCommandOption::PARAMETER_REQUIRED, 'Only do the job for a given import id')));
		$this->namespace        = 'darwin';
		$this->name             = 'import-gtu';
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
				$id_import=$options['id'];
				$import_obj = Doctrine_Core::getTable('Imports')->find($id_import);
				$import_obj->setState("aloaded");
				$import_obj->save();
				
   
				$sql_count = "UPDATE imports SET initial_count=(SELECT COUNT(*) FROM staging_gtu WHERE import_ref=".$id_import.") WHERE id=".$id_import.";";
				$ctn_init = $conn->exec($sql_count);
				$conn->beginTransaction();
				$sql = "SELECT * FROM rmca_import_gtu_create_missing_people_in_staging(".$id_import.");SELECT * FROM rmca_import_gtu_in_darwin_fast(".$id_import.");";
				$ctn = $conn->exec($sql);
				$conn->commit();
				
                $staging_gtu_sql="SELECT count(*) FROM staging_gtu WHERe import_ref=:id_import AND imported=false;";
                $q = $conn->prepare($staging_gtu_sql);
                $q->execute(array(':id_import' => $id_import));
                $staging_gtu_count=$q->fetchAll();
                $staging_gtu_count=$staging_gtu_count[0][0];
                print("test");
                print($staging_gtu_count);
                if($staging_gtu_count==0)
                {
                    $import_obj->setState("finished");
				}
                else
                {
                    $import_obj->setState("pending");
                }
                $import_obj->save();
		  }
		  catch(Exception $e)
          {         
            echo $e->getMessage()."\n";
            //ftheeten 2018 07 31
             if($conn->inTransaction())
             {
                $conn->rollback();
				$import_obj->setState("pending");
				$import_obj->save();
             }
             
          }
	  }
  }  
}
?>