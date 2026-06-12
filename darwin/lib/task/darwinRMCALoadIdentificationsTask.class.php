<?php
require_once(__DIR__."/../file_models/Encoding.php");
use \ForceUTF8\Encoding;

class darwinRMCALoadIdentificationsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('id', null, sfCommandOption::PARAMETER_REQUIRED, 'Only do the job for a given import id')));
		$this->namespace        = 'darwin';
		
		$this->name             = 'import-identifications';
		$this->briefDescription = 'Attach identifications to specimens';
		$this->detailedDescription = <<<EOF
Nothing
EOF;
  }
  
   protected function execute($arguments = array(), $options = array())
  {
	  $id_import;
	  $import_obj;

	   if(!empty($options['id']) && ctype_digit($options['id']) )
	  {
		 // $databaseManager = new sfDatabaseManager($this->configuration);
		  //$environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
		  //$conn = $databaseManager->getDatabase($options['connection'])->getConnection();
		  $databaseManager = new sfDatabaseManager($this->configuration);
		  $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
          $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
		  $conn = Doctrine_Manager::connection();
		  $conn->beginTransaction();    
		  $id_import=$options['id'];
		  try
		  {
				$import_obj=Doctrine_Core::getTable('Imports')->find($id_import);
				if($import_obj!==null)
				{
					$filename=$import_obj->getFileName();
					$tmp_array=explode(".", $filename);
					$extension=array_pop($tmp_array);
					$file_radical=implode(".", $tmp_array);
					$sha1=sha1($filename.$import_obj->getCreatedAt());
					print($filename);
					print($sha1);
					$file_to_load=sfConfig::get('sf_upload_dir')."/uploaded_".$sha1.".".$extension;
					
					if ($fp=fopen($file_to_load,'r')) 
					{
						$tabParser = new RMCATabImportIdentifications( $id_import, $conn);
						$tabParser->configure($options);
						$tabParser->identifyHeader($fp);
						//try
						//{
							while (($row = fgetcsv($fp, 0, "\t")) !== FALSE)
							{
								if($this->csvLineIsNotEmpty($row))
								{
									if (array(null) !== $row) 
									{ // ignore blank lines
											//ftheeten 2018 02 28
										 $row=  Encoding::toUTF8($row);
										 print_r($row);
										 $tabParser->parseLineAndSaveToDB($row);
									}
								 }
							}
							print("file read");
							$import_obj->setIsFinished(true);
							$import_obj->setState("finished");
							$import_obj->save();
						 //}
						 //catch(Doctrine_Exception $ne)
						//{
							
						//	throw $ne;
						//}
						//catch(Exception $e)
						//{
							
						//	throw $e;
						//}
						
						fclose($fp);
					
					
						echo 'ok';
					}
					else 
					{
						echo 'failure with file';
					}
				}
				if($conn->getTransactionLevel()>0)
                {
					$conn->commit();
				}
		  }
		  catch(Exception $e)
          {         
           print("exception");
			echo $e->getMessage()."\n";
            //ftheeten 2018 07 31
             //if($conn->inTransaction())
             //{
				 if($conn->getTransactionLevel()>0)
                {
					$conn->rollBack();
				}
				 print($e->getMessage(). " ". $e->getFile(). " ". (string)$e->getLine(). " ".$e->getTraceAsString());
                //$conn->rollback();
				//$import_obj->setState("pending");
				
				 $import_obj->setErrorsInImport($e->getMessage(). " ". $e->getFile(). " ". (string)$e->getLine(). " ".$e->getTraceAsString());
				$import_obj->setState("error");
				$import_obj->setWorking(FALSE);
				
				$import_obj->save();	
				
				exit();
				
             //}
             
          }
		  catch(Doctrine_Exception $e)
          {         
           print("Doctrine exception");
			echo $e->getMessage()."\n";
            //ftheeten 2018 07 31
             //if($conn->inTransaction())
             //{
				 if($conn->inTransaction())
                {
					$conn->rollBack();
				}
				 print($e->getMessage(). " ". $e->getFile(). " ". (string)$e->getLine(). " ".$e->getTraceAsString());
                //$conn->rollback();
				//$import_obj->setState("pending");
				
				 $import_obj->setErrorsInImport($e->getMessage(). " ". $e->getFile(). " ". (string)$e->getLine(). " ".$e->getTraceAsString());
				$import_obj->setState("error");
				$import_obj->setWorking(FALSE);
				
				$import_obj->save();	
				
				exit();
				
             //}
             
          }
		  
	  }
  }
  
    protected function csvLineIsNotEmpty($p_row)
  {
    $returned=FALSE;
    foreach($p_row as $field=>$value)
    {
        if(strlen(trim((string)$value))>0)
        {
            return TRUE;
        }
    }
    return $returned;
  }
  
  

}