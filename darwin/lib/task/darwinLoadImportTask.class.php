<?php

class darwinLoadImportTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      ));
    $this->namespace        = 'darwin';
    $this->name             = 'load-import';
    $this->briefDescription = 'Import uploaded file to potgresql staging table';
    $this->detailedDescription = <<<EOF
Nothing
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {

     // initialize the database connection
    $result = null ;
	$import=null;
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();
    $conn->beginTransaction();    
    while($id= $conn->fetchOne('SELECT get_import_row()'))
    {
        $q = Doctrine_Query::create()
          ->from('imports p')
          ->where('p.id=?',$id)
          ->fetchOne() ;
        //$file = sfConfig::get('sf_upload_dir').'/uploaded_'.sha1($q->getFilename().$q->getCreatedAt()).'.xml' ;
		//ftheeten 2018 08 05
		   $filename=$q->getFilename();
		   $date=$q->getCreatedAt();
		   $upload_dir=sfConfig::get('sf_upload_dir');
		  $file = $upload_dir.'/uploaded_'.sha1($filename.$date).".".end(explode(".",$filename)) ;

        if(file_exists($file))
        {
        //print("GO");
          try{
            switch ($q->getFormat())
            {
              case 'taxon':
              //print("TAXON");
                $import = new importCatalogueXml('taxonomy') ;
				$result = $import->parseFile($file,$id) ;
				//ftheeten 2019 02 07
                $sql = "select count(distinct name_cluster)  from staging_catalogue WHERE import_ref = :id " ;
				$q2 = $conn->prepare($sql);
				$q2->execute(array(':id'=> $id ));
				$res = $q2->fetchAll(PDO::FETCH_BOTH);
				print_r($res);
				$count_line = $res[0][0];
                break;
				//ftheeten 2018 07 15 				
              case 'locality':
				$import = new ImportGtuCSV() ;
				$result = $import->parseFile($file,$id) ;
                $count_line = "(select count(*) from staging_gtu where import_ref = $id )" ;
                break;
              //2019 03 05
              case 'lithostratigraphy': 
                $import = new RMCATabLithostratigraphy($id) ;
				$result = $import->parseFile($file,$id) ;
                $count_line = "(select count(*) from staging_catalogue where import_ref = $id )" ;
                break;              
              case 'abcd':
              default:
                    /*$import_obj = Doctrine_Core::getTable('Imports')->find($id);
                    $import_obj->setWorking(TRUE);
                    $import_obj->save();*/
                    $import = new importABCDXml($this->configuration ) ;
                    $result = $import->parseFile($file,$id) ;
                    $count_line = "(select count(*) from staging where import_ref = $id )" ;
                    /*$import_obj->setWorking(FALSE);
                    $import_obj->save();*/
                    break;
            } 
            //$result = $import->parseFile($file,$id) ;
            if ( $result == '' && $q->getFormat() == 'taxon') {
              $conn->execute("select fct_clean_staging_catalogue(?)",
                             array(
                               $id
                             )
              );
            }
          }
          catch(Exception $e)
          {
            echo $e->getMessage()."\n";
            $conn->rollback();
           // print_r($res);
            //ftheeten 2018 08 06
            $import_obj = Doctrine_Core::getTable('Imports')->find($q->getId());
			/*if(property_exists($import,'imported_table'))
			{
				$current_table = $import->imported_table.".";
			}
			else
			{
				$current_table = "";
			}*/
             $import_obj->setErrorsInImport($e->getMessage(). " ". $e->getFile(). " ". (string)$e->getLine(). " ".$e->getTraceAsString());
             $import_obj->setState("error");
              $import_obj->setWorking(FALSE);
             $import_obj->save();
            
            break;
            
            
          }
          Doctrine_Query::create()
            ->update('imports p')
            ->set('p.state','?',$result!=''?'error':'loaded')
            ->set('p.initial_count',$count_line)
            ->set('p.errors_in_import','?',$result ? $result : '')
            ->where('p.id = ?', $id)
            ->execute();
        }
    }
    $conn->commit();
  }
}
