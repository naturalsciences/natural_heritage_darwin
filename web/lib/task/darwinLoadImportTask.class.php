<?php

class darwinLoadImportTask extends sfBaseTask
{

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      //ftheeten 2017 08 28
      new sfCommandOption('mailsfornotification', null, sfCommandOption::PARAMETER_REQUIRED, 'The Users for the mail notification'),
      //ftheeten 2018 06 11
      new sfCommandOption('direct-check', null, sfCommandOption::PARAMETER_REQUIRED, 'Load the quality check just after load')
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
	  //print("LOAD");
	 //$myfile = fopen("/home/sysadmin/debug/sysadmin_debug_gtu.txt", "a");
     //fwrite($myfile, "create");
     // initialize the database connection
    $result = null ;
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();

    $idTmp=$this->returnImportId($conn);	
    $this->setImportAsWorking($conn, $idTmp, true);
    //ftheeten 2018 06 11
     $this->realignImportSeq($conn);
    $conn->beginTransaction();
	// print("LOAD 2");
   
    while($id = $conn->fetchOne('SELECT get_import_row()'))
    {
        $q = Doctrine_Query::create()
          ->from('imports p')
          ->where('p.id=?',$id)
          ->fetchOne() ;
		  //print("LOAD 3");
        //ftheeten 2017 03 13
        $file = sfConfig::get('sf_upload_dir').'/uploaded_'.sha1($q->getFilename().$q->getCreatedAt()).".".(explode(".",$q->getFilename())[1]) ;
        //$file = sfConfig::get('sf_upload_dir').'/uploaded_'.sha1($q->getFilename().$q->getCreatedAt()).'.xml' ;
        if(file_exists($file))
        {
          try{
            switch ($q->getFormat())
            {
              case 'taxon':
			  //print("GO TAXON");
                $import = new importCatalogueXml('taxonomy') ;
                $count_line = "(select count(*) from staging_catalogue where parent_ref IS NULL AND import_ref = $id )" ;
                break;   
               //ftheeten 2018 07 15 				
              case 'locality':
			  
				 //fwrite($myfile, "\n!!!!!!!!!!!!!!!!!GTU detected!!!!!!!!!!!!!!!!!!");
				$import = new ImportGtuCSV() ;
                $count_line = "(select count(*) from staging where import_ref = $id )" ;
                break;
              case 'abcd':             
                $import = new importABCDXml() ;
                $count_line = "(select count(*) from staging where import_ref = $id )" ;
                break;
            } 
print("IN TASK");
            $result = $import->parseFile($file,$id) ;
            
           
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
            //ftheeten 2018 07 31
             if($conn->getDbh()->inTransaction())
             {
                $conn->rollback();
             }
             print($q->getId());
             $import_obj = Doctrine::getTable('Imports')->find($q->getId());
             $import_obj->setErrorsInImport($e->getMessage());
             $import_obj->setState("error");
              $import_obj->setWorking(FALSE);
             $import_obj->save();
             //print("RETURN");
             return;
            //break;
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
    //ftheeten 2017 08 29	
     $this->setImportAsWorking($conn, $idTmp, false);
    //ftheeten 2017 08 28
    
    //ftheeten 2018 06 11 (call quality check there)
    if(!empty($options['direct-check']))
    {
        //print("try to check");
        $cmd='darwin:check-import --id='.$options['direct-check'].' --no-delete';
        $currentDir=getcwd();
        chdir(sfconfig::get('sf_root_dir')); 
        exec('nohup php symfony '.$cmd.'  >/dev/null &' );                       
        chdir($currentDir);   
    }
    else
    {
        //print("no check");
    }
    if(array_key_exists("mailsfornotification", $options))
    {
        foreach(explode(";",$options["mailsfornotification"]) as $mail)
        {
                 $this->sendMail($mail, "Darwin XML import loaded in staging", "The pending imports of Darwin have been uploaded into the staging part and are ready for quality check.");
        }
    }
  }
  
  //ftheeten 2017 08 28
  // attention keep identation
 //ftheeten 2017 08 28
  // attention keep identation
  public function sendMail($recipient, $title, $messageContent)
 {


    if(filter_var($recipient, FILTER_VALIDATE_EMAIL))
    {
    
      // mail( $recipient ,  $title,
//<<<EOF
//{$messageContent}
//EOF
  //     );
    }
 }
 
 
 //ftheeten 2018 06 11
   public function realignImportSeq($p_conn)
  {
     $p_conn->beginTransaction();
	 $count=$p_conn->fetchOne("
            SELECT setval('staging_id_seq', (SELECT MAX(id)+1 FROM staging ), false);          
          "); 
	$p_conn->commit();
    
     $p_conn->beginTransaction();
	 $count=$p_conn->fetchOne("
            SELECT setval('staging_catalogue_id_seq', (SELECT MAX(id)+1 FROM staging_catalogue ), false);          
          "); 
	$p_conn->commit();
   
   }
 
 
  //ftheeten 2017 08 29 
  public function returnImportId($p_conn)
  {
       
     $returned=-1;
     $p_conn->beginTransaction();
	 $count=$p_conn->fetchOne("
            SELECT  count(id) FROM imports i1 WHERE i1.state = 'to_be_loaded' OFFSET 0 
          "); 
	$p_conn->commit();
	if($count>=1)
	{
		$p_conn->beginTransaction();
		$id = $p_conn->fetchOne("
            SELECT  id FROM imports i1 WHERE i1.state = 'to_be_loaded' ORDER BY i1.created_at asc, id asc OFFSET 0 
          "); 
		$p_conn->commit();
        $returned=$id; 
	}
	 
	 
     //print_r($returned);
     return $returned;
  }
  
  //ftheeten 2017 08 28
  public function setImportAsWorking( $p_conn, $p_id, $p_working)
  {
	 //print("test working");
	 //print("PID=".$p_id);
    if($p_id>=0)
    {
		if(is_bool($p_working))
		{
			//print(1);
			
			$p_conn->beginTransaction();
         Doctrine_Query::create()
            ->update('imports p')
            ->set('p.working','?',$p_working)
            ->where('p.id = ?', $p_id)
            ->execute();
         $p_conn->commit();
		}
		else
		{
			//print(2);
         $p_conn->beginTransaction();
         Doctrine_Query::create()
            ->update('imports p')
            ->set('p.working','?',((int)$p_working==0)?'f':'t')
            ->where('p.id = ?', $p_id)
            ->execute();
         $p_conn->commit();
		}
	}
	
  }  
}