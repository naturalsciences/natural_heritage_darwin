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
      new sfCommandOption('mailsfornotification', null, sfCommandOption::PARAMETER_REQUIRED, 'The Users for the mail notification', 'backend'),
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
    $databaseManager = new sfDatabaseManager($this->configuration);
    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : $options['env'];
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $conn = Doctrine_Manager::connection();

    $idTmp=$this->returnImportId($conn);	
    $this->setImportAsWorking($conn, $idTmp, true);
    $conn->beginTransaction();
    while($id = $conn->fetchOne('SELECT get_import_row()'))
    {
        $q = Doctrine_Query::create()
          ->from('imports p')
          ->where('p.id=?',$id)
          ->fetchOne() ;
        //ftheeten 2017 03 13
        $file = sfConfig::get('sf_upload_dir').'/uploaded_'.sha1($q->getFilename().$q->getCreatedAt()).".".(explode(".",$q->getFilename())[1]) ;
        //$file = sfConfig::get('sf_upload_dir').'/uploaded_'.sha1($q->getFilename().$q->getCreatedAt()).'.xml' ;
        if(file_exists($file))
        {
          try{
            switch ($q->getFormat())
            {
              case 'taxon':
                $import = new importCatalogueXml('taxonomy') ;
                $count_line = "(select count(*) from staging_catalogue where parent_ref IS NULL AND import_ref = $id )" ;
                break;              
              case 'abcd':
              default:             
                $import = new importABCDXml() ;
                $count_line = "(select count(*) from staging where import_ref = $id )" ;
                break;
            } 

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
    //ftheeten 2017 08 29	
     $this->setImportAsWorking($conn, $idTmp, false);
    //ftheeten 2017 08 28
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
  public function sendMail($recipient, $title, $message)
 {
    if(filter_var($recipient, FILTER_VALIDATE_EMAIL))
    {
    sfContext::getInstance()->set("sf_charset", "utf-8");
        // send an email to the affiliate
        $message = sfContext::getInstance()->getMailer()->compose(
          array('franck.theeten@africamuseum.be' => 'Franck Theeten'),
          $recipient,//$affiliate->getEmail(),
          $title,
<<<EOF
{$message}
EOF
           );
 
        sfContext::getInstance()->getMailer()->send($message);
        //$headers = "From: franck.theeten@africamuseum.be" . "\r\n" .
"CC: theetenfrk@yahoo.fr";
        // mail($recipient, $title, $message, $headers);
         
    }
 }
 
  //ftheeten 2047 08 29 
  public function returnImportId($p_conn)
  {
       
     $returned=-1;
     $p_conn->beginTransaction();
     $id = $p_conn->fetchOne("
            SELECT  id FROM imports i1 WHERE i1.state = 'to_be_loaded' ORDER BY i1.created_at asc, id asc OFFSET 0 
          "); 
     
        $returned=$id; 
     $p_conn->commit();
     //print_r($returned);
     return $returned;
  }
  
  //ftheeten 2017 08 28
  public function setImportAsWorking( $p_conn, $p_id, $p_working)
  {
	 
    if($p_id>=0)
    {
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
