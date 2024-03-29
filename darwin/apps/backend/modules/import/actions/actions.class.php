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
  public $size_staging_catalogue=1000;
  
  public function preExecute()
  {
    if(! $this->getUser()->isAtLeast(Users::ENCODER))
    {
      $this->forwardToSecureAction();
    }
  }

  private function getRight($id)
  {
    $import = Doctrine_Core::getTable('Imports')->find($id);

    $collection_ref = $import->getCollectionRef();
    if(!empty($collection_ref)) {
      if(! Doctrine_Core::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$import->getCollectionRef()))
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
    Doctrine_Core::getTable('Imports')->UpdateStatus($request->getParameter('id'));
    $this->redirect('import/index');
	
	//ftheeten 2018 06 11
	                $mails=Array();
                    $mailsTmp=Doctrine_Core::getTable('UsersComm')->getProfessionalMailsByUser($this->getUser()->getId());
               
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

    Doctrine_Core::getTable('Imports')->clearImport($this->import->getId());
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
      if($request->getParameter('format') == 'taxon')
      {
        $this->type="taxon";
      }
      elseif($request->getParameter('format') == 'locality')
      {
        $this->type="locality";        
      }
	  elseif($request->getParameter('format') == 'files')
      {
        $this->type="files";        
      }
	  elseif($request->getParameter('format') == 'links')
      {
        $this->type="links";        
      }
      elseif($request->getParameter('format') == 'lithostratigraphy')
      {
        $this->type="lithostratigraphy";        
      }                                           
	  elseif($request->getParameter('format') == 'synonymies')
      {
        $this->type="synonymies";        
      }
	  elseif($request->getParameter('format') == 'properties')
      {
        $this->type="properties";        
      }
	   elseif($request->getParameter('format') == 'codes')
      {
        $this->type="codes";        
      }
	   elseif($request->getParameter('format') == 'relationships')
      {
        $this->type="relationships";        
      }
      else
      {
        $this->type="abcd";
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
           
            if(! Doctrine_Core::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->form->getValue('collection_ref')) && $this->type != 'taxon')
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
              try 
              {
                  $file->save(sfConfig::get('sf_upload_dir').'/'.$filename.$extension);
                  $this->form->save() ;
                  if($this->type == 'taxon')
                  {
                    $this->redirect('import/indexTaxon?complete=true');
                  }
                  elseif($this->type == 'locality')
                  {
                    $this->redirect('import/indexLocalities');
                  }
                   elseif($this->type == 'lithostratigraphy')
                  {
                    $this->redirect('import/indexLithostratigraphy');
                  }
				   elseif($this->type == 'files')
                  {
                    $this->redirect('import/indexFiles');
                  }
				  elseif($this->type == 'links')
                  {
                    $this->redirect('import/indexLinks');
                  }
				   elseif($this->type == 'synonymies')
                  {
                    $this->redirect('import/indexSynonymies');
                  }
				  elseif($this->type == 'codes')
                  {
                    $this->redirect('import/indexCodes');
                  }
				   elseif($this->type == 'properties')
                  {
                    $this->redirect('import/indexProperties');
                  }
				   elseif($this->type == 'relationships')
                  {
                    $this->redirect('import/indexRelationships');
                  }
                  else
                  {          
                    $this->redirect('import/index?complete=true');
                   }
            }   
            catch(Doctrine_Exception $e)
            {
          
              $error = new sfValidatorError(new savedValidator(),$e->getMessage());
              $this->form->getErrorSchema()->addError($error, 'Darwin2 :');
            }
          }
		  else
		  {
			  print($this->type);
			  foreach ($this->form->getErrorSchema() as $key => $err) {  
				if ($key) 
				{  
					 $this->form->getErrorSchema()->addError($err, 'Darwin2 :');
				}  
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

  private function andSearch($request,$format)
  {
    $this->format = $format ;
    $this->setCommonValues('import', 'updated_at', $request);
    if( $request->getParameter('orderby', '') == '' && $request->getParameter('orderdir', '') == '')
      $this->orderDir = 'desc';
    if($this->format == 'taxon'||$this->format == 'lithostratigraphy')
    {
      $this->s_url = 'import/searchCatalogue'.'?is_choose='.$this->is_choose;
    }
    elseif($this->format == 'locality')
    {
      $this->s_url = 'import/searchLocality'.'?is_choose='.$this->is_choose;
    }
	elseif($this->format == 'files')
    {
      $this->s_url = 'import/searchFiles'.'?is_choose='.$this->is_choose;
    }
	elseif($this->format == 'links')
    {
      $this->s_url = 'import/searchLinks'.'?is_choose='.$this->is_choose;
    }
	elseif($this->format == 'properties')
    {
      $this->s_url = 'import/searchProperties'.'?is_choose='.$this->is_choose;
    }
	elseif($this->format == 'codes')
    {
      $this->s_url = 'import/searchCodes'.'?is_choose='.$this->is_choose;
    }
	elseif($this->format == 'relationships')
    {
      $this->s_url = 'import/searchRelationships'.'?is_choose='.$this->is_choose;
    }
	elseif($this->format == 'synonymies')
    {
      $this->s_url = 'import/searchSynonymies'.'?is_choose='.$this->is_choose;
    }
    elseif($this->format == 'abcd')
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
        $imp_lines = Doctrine_Core::getTable('Imports')->getNumberOfLines($ids) ;
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

  public function executeSearch(sfWebRequest $request)
  {
    $this->form = new ImportsFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'abcd') ;    
  }
  
    //ftheeten 2017 08 28 (end)
  public function executeLoadstaging(sfWebRequest $request)
  {
  
        $idImport=$request->getParameter("id");
        $importTmp=Doctrine_Core::getTable("Imports")->find($idImport);
        //if(!$this->getUser()->isAtLeast(Users::MANAGER)) $this->forwardToSecureAction();
        if(!Doctrine_Core::getTable("CollectionsRights")->hasEditRightsFor($this->getUser(), $importTmp->getCollectionRef()) && $importTmp->getFormat()!="taxon")
        {

            $this->forwardToSecureAction();
        }
        elseif(!$importTmp->getFormat()=="taxon"||$this->getUser()->isAtLeast(Users::ENCODER))
        {

            print($importTmp->getCollectionRef());
            try 
            {
               
                $mails=Array();
                    $mailsTmp=Doctrine_Core::getTable('UsersComm')->getProfessionalMailsByUser($this->getUser()->getId());
               
                    foreach($mailsTmp as $mailRecord)
                    {
                        if(filter_var($mailRecord->getEntry(), FILTER_VALIDATE_EMAIL))
                        {
                            $mails[]=$mailRecord->getEntry();
                        }
                    }
                    
                   
                     $cmd='darwin:load-import';
                    $currentDir=getcwd();

                    chdir(sfconfig::get('sf_root_dir')); 
  
                
                    exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );

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
                     elseif($importTmp->getFormat()=="lithostratigraphy")
					{
						$this->redirect('import/indexLithostratigraphy');
					}
					elseif($importTmp->getFormat()=="synonymies")
					{
						$this->redirect('import/indexSynonymies');
					}
                     elseif($importTmp->getFormat()=="codes")
					{
						$this->redirect('import/indexCodes');
					}
					 elseif($importTmp->getFormat()=="properties")
					{
						$this->redirect('import/indexProperties');
					}
					 elseif($importTmp->getFormat()=="relationships")
					{
						$this->redirect('import/indexRelationships');
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
        $importTmp=Doctrine_Core::getTable("Imports")->find($idImport);
        
        if(!Doctrine_Core::getTable("CollectionsRights")->hasEditRightsFor($this->getUser(), $importTmp->getCollectionRef()) && $importTmp->getFormat()!="taxon")
        {
            
           $this->forwardToSecureAction();
        }
        elseif(!$importTmp->getFormat()=="taxon"||$this->getUser()->isAtLeast(Users::ENCODER))
        {
            
            try 
            {
					
                    $mails=Array();
                    $mailsTmp=Doctrine_Core::getTable('UsersComm')->getProfessionalMailsByUser($this->getUser()->getId());
               
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
 //print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
                        exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
                        chdir($currentDir);                   
                        //$this->redirect('import/index');
                    }
                    //rmca 2018 02 15
					if($importTmp->getFormat()=="taxon")
					{
						$this->redirect('import/indexTaxon');
					}
                    elseif($importTmp->getFormat()=="lithostratigraphy")
					{
						$this->redirect('import/indexLithostratigraphy');
					}
					/*elseif($importTmp->getFormat()=="synonymies")
					{
						$this->redirect('import/indexSynonymies');
					}*/
                     elseif($importTmp->getFormat()=="codes")
					{
						$this->redirect('import/indexCodes');
					}
					 elseif($importTmp->getFormat()=="properties")
					{
						$this->redirect('import/indexProperties');
					}
					 elseif($importTmp->getFormat()=="relationships")
					{
						$this->redirect('import/indexRelationships');
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
    
    //ftheeten 2019 02 19
    public function executeRechecktaxonomy(sfWebRequest $request)
    {   
        $import_ref=$request->getParameter("id");
        $conn_MGR = Doctrine_Manager::connection();
		$conn = $conn_MGR->getDbh();
			
			
		$params[':import_ref'] = $import_ref;
		$sql =" SELECT * FROM fct_rmca_redo_taxonomic_import(:import_ref);";
		$statement = $conn->prepare($sql);
		$statement->execute($params);
        $this->executeCheckstaging($request);
        return sfView::NONE; 
    }
    
        //ftheeten 2019 02 19
    public function executeRechecklithostratigraphy(sfWebRequest $request)
    {   
        $import_ref=$request->getParameter("id");
        $conn_MGR = Doctrine_Manager::connection();
		$conn = $conn_MGR->getDbh();
			
			
		$params[':import_ref'] = $import_ref;
		$sql =" SELECT * FROM fct_rmca_handle_lithostratigraphy_import(:import_ref);";
		$statement = $conn->prepare($sql);
		$statement->execute($params);
		
        //$this->executeCheckstaging($request);
		
        return sfView::NONE; 
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
 
  //ftheeten 2018 08 05
    public function executeIndexLocalities(sfWebRequest $request)
  {
    $this->format = 'locality' ;
    $this->form = new ImportsLocalityFormFilter(null,array('user' =>$this->getUser()));    
    $this->setTemplate('index');
  }
    //ftheeten 2018 07 15
  public function executeSearchLocality(sfWebRequest $request)
  {
    $this->form = new ImportsLocalityFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'locality') ;
    $this->setTemplate('search');
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

 //ftheeten 2018 08 06
  public function executeLoadGtuInDB(sfWebRequest $request)
  {
	  $idImport=$request->getParameter("id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-gtu --id='.$idImport;          
      exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );

      chdir($currentDir);	 
	  $this->redirect('import/indexLocalities');
  }
  
  
  
     //ftheeten 2018 08 06
   public function executeLoadLithoInDB(sfWebRequest $request)
  {
	  $idImport=$request->getParameter("id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-litho --id='.$idImport;          
      exec('nohup php symfony '.$cmd.'  >/dev/null &' );

      chdir($currentDir);	 
	  $this->redirect('import/indexLithostratigraphy');
  }
  
  
   //ftheeten 2019 02 28
  public function executeLoadSingleGtuInDB(sfWebRequest $request)
  {
	  $idStagingGtu=$request->getParameter("staging_gtu_id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-staging-gtu --id='.$idStagingGtu;          
       exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );

      chdir($currentDir);	 
	  $this->redirect('import/indexLocalities');
  }
  
  //2019 02 28
  public function executeChangeStagingGtuCode(sfWebRequest $request)
  {
    $id_staging_gtu=$request->getParameter("staging_gtu_id");
    $sampling_code=$request->getParameter("sampling_code");
	$currentDir=getcwd();
    if(is_numeric($id_staging_gtu) && strlen(trim($sampling_code))>0)
    {
        $staging_gtu=Doctrine_Core::getTable("StagingGtu")->find($id_staging_gtu);
        if( $staging_gtu)
        {
            $staging_gtu->setSamplingCode($sampling_code);
            $staging_gtu->save();
        }
	    chdir(sfconfig::get('sf_root_dir'));   
        $cmd='darwin:import-gtu --id='.$staging_gtu->getImportRef();          
        exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
		chdir($currentDir);	 
    }
	  
     $this->redirect('import/indexLocalities');
  }
  
   public function executeViewUnimportedGtu(sfWebRequest $request)
  {
    $idImport=$request->getParameter("id");
    $this->id= $idImport;
    $this->size_catalogue= (int)$this->size_staging_catalogue;
	 
	$this->page=$request->getParameter("page",1);
	$offset=((int)$this->page-1)*$this->size_catalogue;
	$this->items = Doctrine_Core::getTable("StagingGtu")->getImportData($idImport, $offset, $this->size_catalogue);	 
	$this->stats= Doctrine_Core::getTable("StagingGtu")->countExceptionMessages($idImport, $offset, $this->size_catalogue);
    	
	 foreach($this->stats as $stat)
	 {
		$this->size_data=(int)$stat["count_all"];
		$this->max_page= ceil((int)$this->size_data/(int)$this->size_catalogue);
		 break;
	 }
	 $this->stats_all= Doctrine_Core::getTable("StagingGtu")->countExceptionMessages($idImport,0, $this->size_data);
     $this->form = new ImportsLocalityForm(null,array('items' =>$this->items));
	 
       
  } 
  
  //ftheeten 2019 02 14
  public function executeViewUnimportedTaxa(sfWebRequest $request)
  {
     $idImport=$request->getParameter("id");
     $this->id= $idImport;
	 $this->size_catalogue= (int)$this->size_staging_catalogue;
	 
	$this->page=$request->getParameter("page",1);
	$offset=((int)$this->page-1)*$this->size_catalogue;
	$this->items = Doctrine_Core::getTable("StagingCatalogue")->getByImportRef($idImport, $offset, $this->size_catalogue);	 
	$this->stats= Doctrine_Core::getTable("StagingCatalogue")->countExceptionMessages($idImport, $offset, $this->size_catalogue);
    	
	 foreach($this->stats as $stat)
	 {
		$this->size_data=(int)$stat["count_all"];
		$this->max_page= ceil((int)$this->size_data/(int)$this->size_catalogue);
		 break;
	 }
     $this->import=Doctrine_Core::getTable("Imports")->find($idImport);
     $this->metadata_ref=$this->import->getSpecimenTaxonomyRef();
	$this->stats_all= Doctrine_Core::getTable("StagingCatalogue")->countExceptionMessages($idImport,0, $this->size_data);
     
       
  } 

  //ftheeten 2019 02 14
  public function executeViewUnimportedLitho(sfWebRequest $request)
  {
     $idImport=$request->getParameter("id");
     $this->id= $idImport;
	 $this->size_catalogue= (int)$this->size_staging_catalogue;
	 
	$this->page=$request->getParameter("page",1);
	$offset=((int)$this->page-1)*$this->size_catalogue;
	$this->items = Doctrine_Core::getTable("StagingCatalogue")->getByImportRef($idImport, $offset, $this->size_catalogue);	 
	$this->stats= Doctrine_Core::getTable("StagingCatalogue")->countExceptionMessages($idImport, $offset, $this->size_catalogue);
    	
	 foreach($this->stats as $stat)
	 {
		$this->size_data=(int)$stat["count_all"];
		$this->max_page= ceil((int)$this->size_data/(int)$this->size_catalogue);
		 break;
	 }
     $this->import=Doctrine_Core::getTable("Imports")->find($idImport);
     $this->metadata_ref=$this->import->getSpecimenTaxonomyRef();
	$this->stats_all= Doctrine_Core::getTable("StagingCatalogue")->countExceptionMessages($idImport,0, $this->size_data);
     
       
  } 

 public function executeViewUnimportedSynonymies(sfWebRequest $request)
  {
     $idImport=$request->getParameter("id");
     $this->id= $idImport;
	 $this->import=Doctrine_Core::getTable("Imports")->findOneById($this->id);
	 $this->size_catalogue= (int)$this->size_staging_catalogue;
	 $this->form= new ImportsForm($this->import);
	$this->page=$request->getParameter("page",1);
	$offset=((int)$this->page-1)*$this->size_catalogue;
	$this->items = Doctrine_Core::getTable("StagingSynonymies")->getByImportRef($idImport, $offset, $this->size_catalogue);	 
	$this->stats= Doctrine_Core::getTable("StagingSynonymies")->countExceptionMessages($idImport, $offset, $this->size_catalogue);
    	
	 foreach($this->stats as $stat)
	 {
		$this->size_data=(int)$stat["count_all"];
		$this->max_page= ceil((int)$this->size_data/(int)$this->size_catalogue);
		 break;
	 }
     $this->import=Doctrine_Core::getTable("Imports")->find($idImport);
     $this->metadata_ref=$this->import->getSpecimenTaxonomyRef();
	$this->stats_all= Doctrine_Core::getTable("StagingSynonymies")->countExceptionMessages($idImport,0, $this->size_data);
     
       
  }  

	public function executeViewUnimportedCodes(sfWebRequest $request)
  {
     $idImport=$request->getParameter("id");
     $this->id= $idImport;
	 $this->size_catalogue= (int)$this->size_staging_catalogue;
	  $this->form= new ImportsForm($this->import);
	$this->page=$request->getParameter("page",1);
	$offset=((int)$this->page-1)*$this->size_catalogue;
	$this->items = Doctrine_Core::getTable("StagingCodes")->getByImportRef($idImport, $offset, $this->size_catalogue);	 
	$this->stats= Doctrine_Core::getTable("StagingCodes")->countExceptionMessages($idImport, $offset, $this->size_catalogue);
    	
	 foreach($this->stats as $stat)
	 {
		$this->size_data=(int)$stat["count_all"];
		$this->max_page= ceil((int)$this->size_data/(int)$this->size_catalogue);
		 break;
	 }
     $this->import=Doctrine_Core::getTable("Imports")->find($idImport);
     $this->metadata_ref=$this->import->getSpecimenTaxonomyRef();
	$this->stats_all= Doctrine_Core::getTable("StagingCodes")->countExceptionMessages($idImport,0, $this->size_data);
     
       
  }  

public function executeViewUnimportedProperties(sfWebRequest $request)
  {
     $idImport=$request->getParameter("id");
     $this->id= $idImport;
	 $this->size_catalogue= (int)$this->size_staging_catalogue;
	  $this->form= new ImportsForm($this->import);
	$this->page=$request->getParameter("page",1);
	$offset=((int)$this->page-1)*$this->size_catalogue;
	$this->items = Doctrine_Core::getTable("StagingProperties")->getByImportRef($idImport, $offset, $this->size_catalogue);	 
	$this->stats= Doctrine_Core::getTable("StagingProperties")->countExceptionMessages($idImport, $offset, $this->size_catalogue);
    	
	 foreach($this->stats as $stat)
	 {
		$this->size_data=(int)$stat["count_all"];
		$this->max_page= ceil((int)$this->size_data/(int)$this->size_catalogue);
		 break;
	 }
     $this->import=Doctrine_Core::getTable("Imports")->find($idImport);
     $this->metadata_ref=$this->import->getSpecimenTaxonomyRef();
	$this->stats_all= Doctrine_Core::getTable("StagingProperties")->countExceptionMessages($idImport,0, $this->size_data);
     
       
  }   
  
public function executeViewUnimportedRelationships(sfWebRequest $request)
  {
     $idImport=$request->getParameter("id");
     $this->id= $idImport;
	 $this->size_catalogue= (int)$this->size_staging_catalogue;
	  $this->form= new ImportsForm($this->import);
	$this->page=$request->getParameter("page",1);
	$offset=((int)$this->page-1)*$this->size_catalogue;
	$this->items = Doctrine_Core::getTable("StagingUpdateSpecimenRelationship")->getByImportRef($idImport, $offset, $this->size_catalogue);	 
	$this->stats= Doctrine_Core::getTable("StagingUpdateSpecimenRelationship")->countExceptionMessages($idImport, $offset, $this->size_catalogue);
    	
	 foreach($this->stats as $stat)
	 {
		$this->size_data=(int)$stat["count_all"];
		$this->max_page= ceil((int)$this->size_data/(int)$this->size_catalogue);
		 break;
	 }
     $this->import=Doctrine_Core::getTable("Imports")->find($idImport);
     $this->metadata_ref=$this->import->getSpecimenTaxonomyRef();
	$this->stats_all= Doctrine_Core::getTable("StagingUpdateSpecimenRelationship")->countExceptionMessages($idImport,0, $this->size_data);
     
       
  }   
  
  public function executeDownloadTaxonomicStaging(sfWebRequest $request)
  {
    $returned=Array();
    $headers=Array();
    $level_to_name=Array();
    $parent_indexer=Array();
    $cluster_indexer=Array();
    $unimported_only=FALSE;
    $import_ref=$request->getParameter("import_ref");
    if($request->hasParameter("unimported"))
    {
        if(strtolower($request->getParameter("unimported")=="yes")||strtolower($request->getParameter("unimported")=="on")||strtolower($request->getParameter("unimported")=="true"))
        {
                $unimported_only=TRUE;
        }
    }
    
    $conn_MGR = Doctrine_Manager::connection();
	$conn = $conn_MGR->getDbh();
			
			
	$params[':import_ref'] = $import_ref;
	$sql =" SELECT DISTINCT level_ref, level_name FROM staging_catalogue LEFT JOIN catalogue_levels ON staging_catalogue.level_ref=catalogue_levels.id WHERE import_ref=:import_ref ORDER BY level_ref;";
	$statement = $conn->prepare($sql);
	$statement->execute($params);
	$results = $statement->fetchAll(PDO::FETCH_ASSOC);
 
    foreach($results as $row)
    {
        $headers[]=$row['level_name'];
        $level_to_name[$row['level_ref']]=$row['level_name'];      
    }
    
    $headers[]="name_cluster";
    $headers[]="imported";
    $headers[]="import_exception";
    $returned[]=implode("\t",$headers);
   
    $sql2="SELECt * FROM (SELECT distinct name_cluster,
            MIN(id) as min_id,
            MAX(id) as max_id,
            min(parent_ref_internal) as min_parent,
    
            hstore(array_agg(level_ref ORDER BY level_ref)::text[] ,  array_agg(name ORDER BY level_ref)::text[]) as taxa,
            hstore(array_agg(id ORDER BY level_ref)::text[] ,  array_agg(level_ref ORDER BY level_ref)::text[]) as taxa_id,
             hstore(array_agg(level_ref ORDER BY level_ref)::text[] ,  array_agg(imported ORDER BY level_ref)::text[]) as imported,
              hstore(array_agg(level_ref ORDER BY level_ref)::text[] ,  array_agg(import_exception ORDER BY level_ref)::text[]) as messages,
                (array_agg(imported ORDER BY level_ref))[ARRAY_LENGTH(array_agg(imported ORDER BY level_ref),1)] as last_imported,
              (array_agg(import_exception ORDER BY level_ref))[ARRAY_LENGTH(array_agg(import_exception ORDER BY level_ref),1)] as last_message
              FROM staging_catalogue WHERE import_ref=:import_ref GROUP BY name_cluster) a  ";
          
    if($unimported_only)
    {
        $sql2.=" WHERE last_imported = FALSE ";
    }
    $sql2.="  ORDER BY name_cluster ;";
    $params[':import_ref'] = $import_ref;
    $statement = $conn->prepare($sql2);
	$statement->execute($params);
	$results = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($results as $row)
    {
        $min_parent=$row["min_parent"];
        $min_id=$row["min_id"];
        $max_id=$row["max_id"];
        $line= array_fill(0, count($headers), "");
        $taxa_line=$row["taxa"];
        $taxa_line=json_decode('{' . str_replace('"=>"', '":"', $taxa_line) . '}', true);
        if($min_parent!=$min_id)
        {
            $cluster_parent=$parent_indexer[$min_parent]["name_cluster"];          
            $complementary_hierarchy=$cluster_indexer[$cluster_parent];
            $min_hierarchy=min(array_keys($taxa_line));
            $newPrefix=array();
            foreach($complementary_hierarchy as $rank_complement=>$name_complement)
            {              
                if($rank_complement < $min_hierarchy)
                {   
                    $taxa_line[$rank_complement]=$name_complement;
                }
            }
            ksort($taxa_line);            
        }
        foreach($taxa_line as $rank_id=>$value)
        {
            $line[array_search($level_to_name[$rank_id], $headers)]=$value;
            $line[array_search("name_cluster", $headers)]=$row['name_cluster'];
            $line[array_search("imported", $headers)]=$row['last_imported']? "TRUE":"FALSE";
            $line[array_search("import_exception", $headers)]=$row['last_message'];
        }
        $rank_line=$row["taxa_id"];
        $rank_line=json_decode('{' . str_replace('"=>"', '":"', $rank_line) . '}', true);
        foreach($rank_line as $taxa_id=>$rank_id)
        {
            $tmp_array=array("name_cluster"=>$row['name_cluster'],"rank_id"=>$rank_id);
            $parent_indexer[$taxa_id]=$tmp_array;
        }
        $cluster_indexer[$row['name_cluster']]= $taxa_line;
        $returned[]=implode("\t",$line);
    }

    $this->getResponse()->setHttpHeader('Content-type','text/tab-separated-values');
    $this->getResponse()->setHttpHeader('Content-disposition','attachment; filename="darwin_debug_import_taxo.txt"');
    $this->getResponse()->setHttpHeader('Pragma', 'no-cache');
    $this->getResponse()->setHttpHeader('Expires', '0');
            
    $this->getResponse()->sendHttpHeaders(); //edited to add the missed sendHttpHeaders
    $this->getResponse()->sendContent();           
    //print(implode("\r\n",$returned));
    return sfView::NONE;   
        
        
  }
  
    //ftheeten 2019 03 04
    public function executeIndexLithostratigraphy(sfWebRequest $request)
  {
    $this->format = 'lithostratigraphy' ;
    $this->form = new ImportsLithostratigraphyFormFilter(null,array('user' =>$this->getUser()));    
    $this->setTemplate('index');
  }
  
      //ftheeten2019 03 04 
  public function executeSearchLithostratigraphy(sfWebRequest $request)
  {
    $this->form = new ImportsLithostratigraphyFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'lithostratigraphy') ;
    $this->setTemplate('search');
  }
      //ftheeten 2018 08 05
    public function executeIndexFiles(sfWebRequest $request)
  {
    $this->format = 'files' ;
    $this->form = new ImportsFilesFormFilter(null,array('user' =>$this->getUser()));    
    $this->setTemplate('index');
  }
  
   public function executeSearchFiles(sfWebRequest $request)
  {
    $this->form = new ImportsFilesFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'files') ;
    $this->setTemplate('search');
  }
  
  public function executeIndexSynonymies(sfWebRequest $request)
  {
		$this->format = 'synonymies' ;
		$this->form = new ImportsSynonymiesFormFilter(null,array('user' =>$this->getUser()));    
		$this->setTemplate('index');
  }
  
   public function executeIndexProperties(sfWebRequest $request)
  {
		$this->format = 'properties' ;
		$this->form = new ImportsPropertiesFormFilter(null,array('user' =>$this->getUser()));    
		$this->setTemplate('index');
  }
  
   public function executeIndexCodes(sfWebRequest $request)
  {
		$this->format = 'codes' ;
		$this->form = new ImportsPropertiesFormFilter(null,array('user' =>$this->getUser()));    
		$this->setTemplate('index');
  }

  public function executeIndexRelationships(sfWebRequest $request)
  {
		$this->format = 'relationships' ;
		$this->form = new ImportsRelationshipsFormFilter(null,array('user' =>$this->getUser()));    
		$this->setTemplate('index');
  }


   public function executeSearchSynonymies(sfWebRequest $request)
  {
    $this->form = new ImportsSynonymiesFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'synonymies') ;
    $this->setTemplate('search');
  }

  
   public function executeSearchProperties(sfWebRequest $request)
  {
    $this->form = new ImportsPropertiesFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'properties') ;
    $this->setTemplate('search');
  }
  
   public function executeSearchRelationships(sfWebRequest $request)
  {
    $this->form = new ImportsRelationshipsFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'relationships') ;
    $this->setTemplate('search');
  }
  
   public function executeSearchCodes(sfWebRequest $request)
  {
    $this->form = new ImportsCodesFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'codes') ;
    $this->setTemplate('search');
  }

  public function executeLoadfiles(sfWebRequest $request)
  {
    $idImport=$request->getParameter("id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-files --id='.$idImport;          
      exec('nohup php symfony '.$cmd.'  >/dev/null &' );

      chdir($currentDir);	 
	  $this->redirect('import/indexFiles');
  }
  
  public function executeLoadsynonyms(sfWebRequest $request)
  {
    $idImport=$request->getParameter("id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-synonyms --id='.$idImport;          
      exec('nohup php symfony '.$cmd.'  >/dev/null &' );
      chdir($currentDir);	
  
	  $this->redirect('import/indexSynonymies');
  }
  
    public function executeLoadproperties(sfWebRequest $request)
  {
    $idImport=$request->getParameter("id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-properties --id='.$idImport;          
      exec('nohup php symfony '.$cmd.'  >/dev/null &' );
      chdir($currentDir);	
  
	  $this->redirect('import/indexProperties');
  }
  
    public function executeLoadcodes(sfWebRequest $request)
  {
    $idImport=$request->getParameter("id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-codes --id='.$idImport;          
      exec('nohup php symfony '.$cmd.'  >/dev/null &' );
      chdir($currentDir);	
  
	  $this->redirect('import/indexCodes');
  }
 
   public function executeLoadrelationships(sfWebRequest $request)
  {
    $idImport=$request->getParameter("id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-relationships --id='.$idImport;          
      exec('nohup php symfony '.$cmd.'  >/dev/null &' );
      chdir($currentDir);	
  
	  $this->redirect('import/indexRelationships');
  }
 
    public function executeIndexLinks(sfWebRequest $request)
  {
    $this->format = 'links' ;
    $this->form = new ImportsLinksFormFilter(null,array('user' =>$this->getUser()));    
    $this->setTemplate('index');
  }
  
   public function executeSearchLinks(sfWebRequest $request)
  {
    $this->form = new ImportsLinksFormFilter(null,array('user' =>$this->getUser()));
    $this->andSearch($request,'links') ;
    $this->setTemplate('search');
  }
  
    public function executeLoadlinks(sfWebRequest $request)
  {
    $idImport=$request->getParameter("id");
	  $currentDir=getcwd();

      chdir(sfconfig::get('sf_root_dir')); 
  
       $cmd='darwin:import-links --id='.$idImport;          
      exec('nohup php symfony '.$cmd.'  >/dev/null &' );

      chdir($currentDir);	 
	  $this->redirect('import/indexLinks');
  }
  
  public function executeChecksynonyms(sfWebRequest $request)
  {
		
		
		if($this->getUser()->getTaxonomicManager())
		{
			$import_ref=$request->getParameter("id");
			$conn = Doctrine_Manager::connection();
            $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
            $currentDir=getcwd();
            chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:check-import-synonymy --id='.$import_ref;    
			print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
            exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
            chdir($currentDir);                   
			
			
		}
		$this->redirect('import/indexSynonymies');
		//
  }
  
    public function executeCheckproperties(sfWebRequest $request)
  {
		
		
		if($this->getUser()->isAtLeast(Users::ENCODER))
		{
			$import_ref=$request->getParameter("id");
			$conn = Doctrine_Manager::connection();
            $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
            $currentDir=getcwd();
            chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:check-import-properties --id='.$import_ref;    
			print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
            exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
            chdir($currentDir);                   
			
			
		}
		$this->redirect('import/indexProperties');
		//
  }
  
      public function executeCheckcodes(sfWebRequest $request)
  {
		
		
		if($this->getUser()->isAtLeast(Users::ENCODER))
		{
			$import_ref=$request->getParameter("id");
			$conn = Doctrine_Manager::connection();
            $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
            $currentDir=getcwd();
            chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:check-import-codes --id='.$import_ref;    
			print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
            exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
            chdir($currentDir);                   
			
			
		}
		$this->redirect('import/indexCodes');
		//
  }
  
      public function executeCheckrelationships(sfWebRequest $request)
  {
		
		
		if($this->getUser()->isAtLeast(Users::ENCODER))
		{
			$import_ref=$request->getParameter("id");
			$conn = Doctrine_Manager::connection();
            $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
            $currentDir=getcwd();
            chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:check-import-relationships --id='.$import_ref;    
			print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
            exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
            chdir($currentDir);                   
			
			
		}
		$this->redirect('import/indexRelationships');
		//
  }
  
  public function executeUpdateStagingSyn(sfWebRequest $request)
  {
	  $result=array();
	  if($this->getUser()->getTaxonomicManager())
	  {
		  $syn_row=(string)$request->getParameter("current_row_id");
		  $syn_type=(string)$request->getParameter("current_type_name");
		  $name_id=(string)$request->getParameter("name_id");
		  $name_str=(string)$request->getParameter("name");
		  $result=["syn_row"=>$syn_row, "syn_type"=>$syn_type, "name_id"=>$name_id];
		  $stagingObj=Doctrine_Core::getTable("StagingSynonymies")->find($syn_row);
		  
		  //print_r( $stagingObj);
		  $stagingObj->save();
		  if($syn_type=="valid")
		  {
			 $stagingObj->setValidNameRef($name_id);
			 $stagingObj->setValidName($name_str);
		  }
		  elseif($syn_type=="synonym")
		  {
			  $stagingObj->setSynRef($name_id);
			  $stagingObj->setSynName($name_str);
		  }
		   $stagingObj->save();
		   $result["status"]="done";
	  }
	  $this->getResponse()->setHttpHeader('Content-type','application/json');
	  return $this->renderText(json_encode($result));
	  
  }
  
    public function executeUpdateStagingProp(sfWebRequest $request)
  {
	  $result=array();
	  if($this->getUser()->getTaxonomicManager())
	  {
		  $stag_id=(string)$request->getParameter("stag_id");
		  $spec_id=(string)$request->getParameter("spec_id");
		 
		 
		  $result=["stag_id"=>$stag_id, "spec_id"=>$spec_id];
		  $stagingObj=Doctrine_Core::getTable("StagingProperties")->find($stag_id);
		  
		  //print_r( $stagingObj);
		  $stagingObj->save();
		  $stagingObj->setSpecimenRef($spec_id);
		   $stagingObj->save();
		   $result["status"]="done";
	  }
	  $this->getResponse()->setHttpHeader('Content-type','application/json');
	  return $this->renderText(json_encode($result));
	  
  }
  
  public function executeUpdateStagingCodes(sfWebRequest $request)
  {
	  $result=array();
	  if($this->getUser()->getTaxonomicManager())
	  {
		  $stag_id=(string)$request->getParameter("stag_id");
		  $spec_id=(string)$request->getParameter("spec_id");
		 
		 
		  $result=["stag_id"=>$stag_id, "spec_id"=>$spec_id];
		  $stagingObj=Doctrine_Core::getTable("StagingCodes")->find($stag_id);
		  
		  //print_r( $stagingObj);
		  $stagingObj->save();
		  $stagingObj->setSpecimenRef($spec_id);
		   $stagingObj->save();
		   $result["status"]="done";
	  }
	  $this->getResponse()->setHttpHeader('Content-type','application/json');
	  return $this->renderText(json_encode($result));
	  
  }
  
  public function executeUpdateStagingRelationship(sfWebRequest $request)
  {
	  $result=array();
	  if($this->getUser()->getTaxonomicManager())
	  {
		  $stag_id=(string)$request->getParameter("stag_id");
		  $spec_id=(string)$request->getParameter("spec_id");
		  $field_id=(string)$request->getParameter("field_id");
		 
		 
		  $result=["stag_id"=>$stag_id, "spec_id"=>$spec_id, "field_id"=>$spec_id];
		  $stagingObj=Doctrine_Core::getTable("StagingUpdateSpecimenRelationship")->find($stag_id);
		  
		  //print_r( $stagingObj);
		  $stagingObj->save();
		  if(strtolower( $field_id)=="main")
		  {
			 $stagingObj->setSpecimenRef($spec_id);
		  }
		  elseif(strtolower( $field_id)=="related")
		  {
			$stagingObj->setSpecimenRelatedRef($spec_id);
		  }
		  
		   $stagingObj->save();
		   //$result["status"]="done";
	  }
	  $this->getResponse()->setHttpHeader('Content-type','application/json');
	  return $this->renderText(json_encode($result));
	  
  }
  
  
  public function executeImportsynonyms(sfWebRequest $request)
  {
	if($this->getUser()->getTaxonomicManager())
		{
			$import_ref=$request->getParameter("id");
			$conn = Doctrine_Manager::connection();
            $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
            $currentDir=getcwd();
            chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:do-import-synonymy --id='.$import_ref;    
			print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
            exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
            chdir($currentDir);                   
			
			
		}
		$this->redirect('import/indexSynonymies');
  }
  
   public function executeImportproperties(sfWebRequest $request)
  {
	if($this->getUser()->getTaxonomicManager())
		{
			$import_ref=$request->getParameter("id");
			$conn = Doctrine_Manager::connection();
            $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
            $currentDir=getcwd();
            chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:do-import-properties --id='.$import_ref;    
			print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
            exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
            chdir($currentDir);                   
			
			
		}
		$this->redirect('import/indexProperties');
		
  }
  
     public function executeImportcodes(sfWebRequest $request)
  {
	if($this->getUser()->getTaxonomicManager())
		{
			$import_ref=$request->getParameter("id");
			$conn = Doctrine_Manager::connection();
            $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
            $currentDir=getcwd();
            chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:do-import-codes --id='.$import_ref;    
			print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
            exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
            chdir($currentDir);                   
			
			
		}
		$this->redirect('import/indexCodes');
		
  }
  
   public function executeImportrelationships(sfWebRequest $request)
  {
	if($this->getUser()->getTaxonomicManager())
		{
			$import_ref=$request->getParameter("id");
			$conn = Doctrine_Manager::connection();
            $this->setImportAsWorking($conn, array($request->getParameter('id')), true);
            $currentDir=getcwd();
            chdir(sfconfig::get('sf_root_dir'));
			$cmd='darwin:do-import-relationships --id='.$import_ref;    
			print( 'nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );                        
            exec('nohup '.sfconfig::get('dw_php_console').' symfony '.$cmd.'  >/dev/null &' );
                       
                        
            chdir($currentDir);                   
			
			
		}
		$this->redirect('import/indexRelationships');
		
		
  }
  
  
  

  
  
}
