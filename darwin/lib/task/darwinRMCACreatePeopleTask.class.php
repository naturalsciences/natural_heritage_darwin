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
	  $id_import;
	   if(!empty($options['id']) && ctype_digit($options['id']) )
	  {
		  try
		  {
                print("GO");
				$id_import=$options['id'];
				$databaseManager = new sfDatabaseManager($this->configuration);
				$environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
				$conn = $databaseManager->getDatabase($options['connection'])->getConnection();
   
				//$conn = Doctrine_Manager::connection();
				$this->setImportAsWorking($conn, $id_import, true);
				$conn->beginTransaction();
				$sql = "SELECT * FROM rmca_create_missing_people_in_staging($id_import)";
				$ctn = $conn->exec($sql);
				$conn->commit();
				$this->setImportAsWorking($conn, $id_import, false);
				$import_obj = Doctrine_Core::getTable('Imports')->find($id_import);
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
             /*$import_obj = Doctrine_Core::getTable('Imports')->find($q->getId());
             $import_obj->setErrorsInImport($e->getMessage());
             $import_obj->setState("error");
              $import_obj->setWorking(FALSE);
             $import_obj->save();
             //print("RETURN");
             return;*/
            //break;
          }
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
?>