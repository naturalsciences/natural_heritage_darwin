<?php

/**
 * default actions.
 *
 * @package    darwin
 * @subpackage dna
 * @categorie  action
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class importActions extends DarwinActions
{
  public function preExecute()
  {
    if(! $this->getUser()->isAtLeast(Users::ENCODER))
    {
      $this->forwardToSecureAction();
    }
  }

  private function getRight($id)
  {
    $import = Doctrine::getTable('Imports')->find($id);

    $collection_ref = $import->getCollectionRef();
    if(!empty($collection_ref)) {
      if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$import->getCollectionRef()))
         $this->forwardToSecureAction();
    }
    elseif (! $this->getUser()->isAtLeast(Users::ENCODER)) {
      $this->forwardToSecureAction();
    }
    return $import ;
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $this->import = $this->getRight($request->getParameter('id')) ;
    if($this->import->getFormat() == 'taxon' && ($this->import->getUserRef() == $this->getUser()->getId() || $this->getUser()->isAtLeast(Users::ADMIN))) 
    {
      $this->import->delete() ;
      if($request->isXmlHttpRequest())
      {
        return $this->renderText('ok');
      }
      return $this->redirect('import/indexTaxon');
    }
    else
    {      
      $this->import->setState('deleted');
      $this->import->save();

      if($request->isXmlHttpRequest())
      {
        return $this->renderText('ok');
      }
      return $this->redirect('import/index');
    }

  }

  public function executeMaj(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $this->import = $this->getRight($request->getParameter('id')) ;
    Doctrine::getTable('Imports')->UpdateStatus($request->getParameter('id'));
    $this->redirect('import/index');
	
	//ftheeten 2018 06 11
	                $mails=Array();
                    $mailsTmp=Doctrine::getTable('UsersComm')->getProfessionalMailsByUser($this->getUser()->getId());
               
                    foreach($mailsTmp as $mailRecord)
                    {
                        if(filter_var($mailRecord->getEntry(), FILTER_VALIDATE_EMAIL))
                        {
                            $mails[]=$mailRecord->getEntry();
                        }
                    }
                    
                    if(count($mails)>0)
                    {
                        $cmd='darwin:check-import  --mailsfornotification='.implode(";",$mails);
                    }
                    else
                    {
                        $cmd='darwin:check-import';
                    }
                    $currentDir=getcwd();

                    chdir(sfconfig::get('sf_root_dir')); 
  
                
                    exec('nohup php symfony '.$cmd.'  >/dev/null &' );

                    chdir($currentDir);
					
					//rmca 2018 02 15
					if($importTmp->getFormat()=="taxon")
					{
						$this->redirect('import/indexTaxon');
					}
					else
					{
						$this->redirect('import/index');
					}
  }

  public function executeViewError(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $this->id = $request->getParameter('id');
    $this->import = $this->getRight($this->id);
    $this->errors = explode(';',$this->import->getErrorsInImport()) ;    
  }

  public function executeClear(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $this->import = $this->getRight($request->getParameter('id')) ;

    Doctrine::getTable('Imports')->clearImport($this->import->getId());
    if($request->isXmlHttpRequest())
    {
      return $this->renderText('ok');
    }
    if($this->import->getFormat() == 'taxon') return $this->redirect('import/indexTaxon');
    return $this->redirect('import/index');
  }

  public function executeExtdinfo(sfWebRequest $request)
  {
    $this->import = new Imports() ;
  }

  public function executeUploadTaxon(sfWebRequest $request)
  {
  }

  public function executeUpload(sfWebRequest $request)
  {

    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();
    // Initialization of the import form
    if($request->isMethod('post')) {
      $this->type = $request->getParameter('imports')[ 'format' ];
    }
    else {
      //$this->type = $request->getParameter('format') == 'taxon' ? 'taxon' : 'abcd';
	   //ftheeten 2018 07 15
	   if($request->getParameter('format') == 'taxon')
	   {
		   $this->type = 'taxon';
	   }
	   elseif($request->getParameter('format') == 'abcd')
	   {
		   $this->type = 'abcd';
	   }
	   elseif($request->getParameter('format') == 'locality')
	   {
		   $this->type = 'locality';
	   }
    }
    $this->form = new ImportsForm(null,array('format' => $this->type));
    if($request->isMethod('post'))
    {
      
      try
      {
          $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
          if($this->form->isValid())
          {
           
            if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->form->getValue('collection_ref')) && $this->type != 'taxon')
            {
             
              $error = new sfValidatorError(new sfValidatorPass(),'You don\'t have right on this collection');
              $this->form->getErrorSchema()->addError($error, 'Darwin2 :');
              return ;
            }
           
            $file = $this->form->getValue('uploadfield');
            $date = date('Y-m-d H:i:s') ;
            $filename = 'uploaded_'.sha1($file->getOriginalName().$date);
            $extension = $file->getExtension($file->getOriginalExtension());
            // we can have the temporary file here : $file->getTempName()) ;
            // usefull if we choose not to save the file in fact
            $this->form->getObject()->setUserRef($this->getUser()->getId()) ;
            $this->form->getObject()->setFilename($file->getOriginalName()) ;
            $this->form->getObject()->setCreatedAt($date) ;
            try {
           
              $file->save(sfConfig::get('sf_upload_dir').'/'.$filename.$extension);
              $this->form->save() ;
               //ftheeten 2017 08 28 (begin)
              $mails=Doctrine::getTable('UsersComm')->getProfessionalMailsByUser($this->getUser()->getId());
              //$mails=$mailsTmp->fetchAll();
              foreach($mails as $mail)
              {
                $this->sendMail($mail->getEntry(), "Darwin XML import file loaded", "The file ".$file->getOriginalName()." has been uploaded into Darwin and is ready to be loaded in staging.");
              }
               //ftheeten 2017 08 28 (end)
              if($this->type != 'abcd')
                $this->redirect('import/indexTaxon?complete=true');
              $this->redirect('import/index?complete=true');
             
            }
            catch(Doctrine_Exception $e)
            {
          
              $error = new sfValidatorError(new savedValidator(),$e->getMessage());
              $this->form->getErrorSchema()->addError($error, 'Darwin2 :');
            }
          }
      }
      catch(Doctrine_Exception $e)
     {
          
       $error = new sfValidatorError(new savedValidator(),$e->getMessage());
       $this->form->getErrorSchema()->addError($error, 'Darwin2 :');
      }
      /*else
      {
         $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error, 'Darwin2 :');
      }*/
      $this->setTemplate('upload');
    }
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->format = 'abcd' ;
    $this->form = new ImportsFormFilter(null,array('user' =>$this->getUser()));
  }

  public function executeIndexTaxon(sfWebRequest $request)
  {
    $this->format = 'taxon' ;
    $this->form = new ImportsTaxonFormFilter(null,array('user' =>$this->getUser()));    
    $this->setTemplate('index');
  }
  
  public function executeIndexLocalities(sfWebRequest $request)
  {
    $this->format = 'locality' ;
    $this->form = new ImportsLocalityFormFilter(null,array('user' =>$this->getUser()));    
    $this->setTemplate('index');
  }

  private function andSearch($request,$format)
  {
    $this->format = $format ;
    $this->setCommonValues('import', 'updated_at', $request);
    if( $request->getParameter('orderby', '') == '' && $request->getParameter('orderdir', '') == '')
      $this->orderDir = 'desc';
    if($this->format == 'taxon')
	{
      $this->s_url = 'import/searchCatalogue'.'?is_choose='.$this->is_choose;
    }
	elseif($this->format == 'locality')//ftheeten 2018 07 16
	{
		 $this->s_url = 'import/searchLocality'.'?is_choose='.$this->is_choose;
	}
	else
	{
      $this->s_url = 'import/search'.'?is_choose='.$this->is_choose;
    }
	$this->o_url = '&orderby='.$this->orderBy.'&orderdir='.$this->orderDir;

    if($request->getParameter('imports_filters','') !== '')
    {
      $this->form->bind($request->getParameter('imports_filters'));
      if ($this->form->isValid())
      {
        $query = $this->form->getQuery()
          ->orderBy($this->orderBy .' '.$this->orderDir);
        $this->pagerLayout = new PagerLayoutWithArrows(
          new DarwinPager(
            $query,
            $this->currentPage,
            $this->form->getValue('rec_per_page')
          ),
          new Doctrine_Pager_Range_Sliding(
            array('chunk' => $this->pagerSlidingSize)
          ),
          $this->getController()->genUrl($this->s_url.$this->o_url) . '/page/{%page_number}'
        );

        $this->setDefaultPaggingLayout($this->pagerLayout);

        if (! $this->pagerLayout->getPager()->getExecuted())
          $this->imports = $this->pagerLayout->execute();

        $ids = array();
        foreach($this->imports as $k=>$v)
        {
          $ids[] = $v->getId();
        }
        $imp_lines = Doctrine::getTable('Imports')->getNumberOfLines($ids) ;
        foreach($imp_lines as $k=>$v)
        {
          foreach($this->imports as $import)
          {
            if($v['id'] == $import->getId())
            {
              $import->setCurrentLineNum($v['cnt']);
              break 1;
            }
          }
        }
      }
    }
  }

  public function executeSearchCatalogue(sfWebRequest $request)
  {
    $this->form = new ImportsTaxonFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'taxon') ;
    $this->setTemplate('search');
  }
  
  //ftheeten 2018 07 15
  public function executeSearchLocality(sfWebRequest $request)
  {
    $this->form = new ImportsLocalityFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'locality') ;
    $this->setTemplate('search');
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->form = new ImportsFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'abcd') ;    
  }
  
    //ftheeten 2017 08 28 (end)
    public function executeLoadstaging(sfWebRequest $request)
    {
  
        $idImport=$request->getParameter("id");
        $importTmp=Doctrine::getTable("Imports")->find($idImport);
        //if(!$this->getUser()->isAtLeast(Users::MANAGER)) $this->forwardToSecureAction();
        if(!Doctrine::getTable("CollectionsRights")->hasEditRightsFor($this->getUser(), $importTmp->getCollectionRef()))
        {

            $this->forwardToSecureAction();
        }
        elseif(!$importTmp->getFormat()=="taxon"||$this->getUser()->isAtLeast(Users::ENCODER))
        {

            print($importTmp->getCollectionRef());
            try 
            {
               
                $mails=Array();
                    $mailsTmp=Doctrine::getTable('UsersComm')->getProfessionalMailsByUser($this->getUser()->getId());
               
                    foreach($mailsTmp as $mailRecord)
                    {
                        if(filter_var($mailRecord->getEntry(), FILTER_VALIDATE_EMAIL))
                        {
                            $mails[]=$mailRecord->getEntry();
                        }
                    }
                    
                    if(count($mails)>0)
                    {
                        $cmd='darwin:load-import --direct-check='.$idImport.'  --mailsfornotification='.implode(";",$mails);
                    }
                    else
                    {
                        $cmd='darwin:load-import --direct-check='.$idImport;
                    }
                    $currentDir=getcwd();

                    chdir(sfconfig::get('sf_root_dir')); 
  
                
                    exec('nohup php symfony '.$cmd.'  >/dev/null &' );

                    chdir($currentDir);
					
					//rmca 2018 02 15
					if($importTmp->getFormat()=="taxon")
					{
						$this->redirect('import/indexTaxon');
					}
					elseif($importTmp->getFormat()=="locality")
					{
						$this->redirect('import/indexLocalities');
					}
					else
					{
						$this->redirect('import/index');
					}
			}
            catch(Doctrine_Exception $e)
            {

              $error = new sfValidatorError(new savedValidator(),$e->getMessage());
              $this->form->getErrorSchema()->addError($error, 'Darwin2 :');
            }
        }
    }
    
    //ftheeten 2017 08 29 
    public function executeCheckstaging(sfWebRequest $request)
    {
        $idImport=$request->getParameter("id");
        $importTmp=Doctrine::getTable("Imports")->find($idImport);
        
        if(!Doctrine::getTable("CollectionsRights")->hasEditRightsFor($this->getUser(), $importTmp->getCollectionRef()))
        {
            
           $this->forwardToSecureAction();
        }
        elseif(!$importTmp->getFormat()=="taxon"||$this->getUser()->isAtLeast(Users::ENCODER))
        {
            
            try 
            {
                 
                    $mails=Array();
                    $mailsTmp=Doctrine::getTable('UsersComm')->getProfessionalMailsByUser($this->getUser()->getId());
               
                    foreach($mailsTmp as $mailRecord)
                    {
                        if(filter_var($mailRecord->getEntry(), FILTER_VALIDATE_EMAIL))
                        {
                            $mails[]=$mailRecord->getEntry();
                        }
                    }
                    if(filter_var($request->getParameter('id'), FILTER_VALIDATE_INT))
                    {
                        if(count($mails)>0)
                        {
                            // $cmd='darwin:check-import --id='.$request->getParameter('id').' --mailsfornotification='.implode(";",$mails).' --no-delete';
                             $cmd='darwin:check-import --id='.$request->getParameter('id').' --no-delete';
                        }
                        else
                        {
                             $cmd='darwin:check-import --id='.$request->getParameter('id').' --no-delete';
                        }
                        $conn = Doctrine_Manager::connection();
                        $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
                        $currentDir=getcwd();
                        chdir(sfconfig::get('sf_root_dir'));
 //print( 'nohup php symfony '.$cmd.'  >/dev/null &' );                        
                        exec('nohup php symfony '.$cmd.'  >/dev/null &' );
                       
                        
                        chdir($currentDir);                   
                        //$this->redirect('import/index');
                    }
                    //rmca 2018 02 15
					if($importTmp->getFormat()=="taxon")
					{
						$this->redirect('import/indexTaxon');
					}
					else
					{
						$this->redirect('import/index');
					}
            }
            catch(Doctrine_Exception $e)
            {
          
              $error = new sfValidatorError(new savedValidator(),$e->getMessage());
              $this->form->getErrorSchema()->addError($error, 'Darwin2 :');
            }
        }
    }
    

  
  //ftheeten 2017 08 28
  // attention keep identation
  public function sendMail($recipient, $title, $message)
 {
     if(filter_var($recipient, FILTER_VALIDATE_EMAIL))
    {
        // send an email to the affiliate
        /*$message = $this->getMailer()->compose(
          array('darwin@africamuseum.be' => 'Franck Theeten'),
          $recipient,//$affiliate->getEmail(),
          $title,
<<<EOF
{$message}
EOF
           );
 
        $this->getMailer()->send($message);*/
       mail ( $recipient ,  $title,
<<<EOF
{$message}
EOF
       );
        
    }
 }

   //ftheeten 2017 08 28
  public function setImportAsWorking( $p_conn, $p_ids, $p_working)
  {
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
