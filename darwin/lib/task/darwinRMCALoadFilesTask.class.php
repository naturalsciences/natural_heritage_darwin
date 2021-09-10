<?php
require_once(__DIR__."/../file_models/Encoding.php");
use \ForceUTF8\Encoding;

class darwinRMCALoadFilesTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('id', null, sfCommandOption::PARAMETER_REQUIRED, 'Only do the job for a given staging_gtu id')));
		$this->namespace        = 'darwin';
		$this->name             = 'import-files';
		$this->briefDescription = 'Associate files to Darwin specimens';
		$this->detailedDescription = <<<EOF
Nothing
EOF;
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
  
  
  protected function execute($arguments = array(), $options = array())
  {
	  $id_import;
	  $import_obj;

	   if(!empty($options['id']) && ctype_digit($options['id']) )
	  {
		  $databaseManager = new sfDatabaseManager($this->configuration);
		  $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
		  $conn = $databaseManager->getDatabase($options['connection'])->getConnection();
		  $id_import=$options['id'];
		  try
		  {
			print("debug");
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
				$file_to_unzip=sfConfig::get('sf_upload_dir')."/uploaded_".$sha1.".".$extension;
				print($file_to_unzip);
				$zip = new ZipArchive();
				if ($zip->open($file_to_unzip) === TRUE) {
					$zip->extractTo('php://temp');
					for ($i = 0; $i < $zip->numFiles; $i++) 
					{
						$filename = $zip->getNameIndex($i);
						print("\r\n");
						print($filename);
					}
					
					$meta=$zip->getFromName('meta.txt');
					if($meta)
					{
						print("\r\n");
						print($meta);
						$tabParser = new RMCATabImportFiles(
						$this->configuration,
						$id_import, 
						$this->getCollectionOfImport($id_import),
						$zip						
						);
						$tabParser->configure();
						//string as filestream
						$fiveMBs = 5 * 1024 * 1024;
						$fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
						fputs($fp, $meta);
						rewind($fp);
						//
						$tabParser->identifyHeader($fp);
						
						try
						{
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
							$import_obj->setIsFinished(true);
							$import_obj->setState("finished");
							$import_obj->save();
						 }
						 catch(Doctrine_Exception $ne)
						{
							/*print("ERROR 1");
							$conn->rollback();
							$import_obj = Doctrine_Core::getTable('Imports')->find($q->getId());
							$import_obj->setErrorsInImport($ne->getMessage());
							$import_obj->setState("error");
							$import_obj->setWorking(FALSE);
							$import_obj->save();*/
							throw $ne;
						}
						catch(Exception $e)
						{
							/*print("ERROR 2");
							$conn->rollback();
							$import_obj = Doctrine_Core::getTable('Imports')->find($q->getId());
							$import_obj->setErrorsInImport($ne->getMessage());
							$import_obj->setState("error");
							$import_obj->setWorking(FALSE);
							$import_obj->save();*/
							throw $e;
						}
						
						fclose($fp);
					}
					else
					{
						print("meta not found");						
					}
					$zip->close();
					echo 'ok';
				} else {
					echo 'failure with zip';
				}
				
			}
				
				
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
  

  
  private function getCollectionOfImport($import_id)
  {
  	 $collection_ref=Doctrine_Core::getTable('Imports')->find($import_id)->getCollectionRef();
	 return Doctrine_Core::getTable('Collections')->find($collection_ref);
  }  
}
?>