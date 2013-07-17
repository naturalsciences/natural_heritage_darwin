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

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $this->import = Doctrine::getTable('Imports')->find($request->getParameter('id'));

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();

    $this->import->delete();

    if($request->isXmlHttpRequest())
    {
      return $this->renderText('ok');
    }
    return $this->redirect('import/index');
  }

  public function executeViewError(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $this->import = Doctrine::getTable('Imports')->find($request->getParameter('id'));

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();

    $this->errors = explode(';',$this->import->getErrorsInImport()) ;
    array_pop($this->errors) ; // just remove the solo ";" with cause to have a empty column at the end of the array
  }

  public function executeClear(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $this->import = Doctrine::getTable('Imports')->find($request->getParameter('id'));

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();

    Doctrine::getTable('Imports')->clearImport($this->import->getId());
    if($request->isXmlHttpRequest())
    {
      return $this->renderText('ok');
    }
    return $this->redirect('import/index');
  }
  
  public function executeExtdinfo(sfWebRequest $request)
  {   
    $this->import = new Imports() ;   
  }

  public function executeUpload(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();    
    // Initialization of the import form
    $this->form = new importsForm();    
    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if($this->form->isValid())
      {       
        if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->form->getValue('collection_ref')))
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
          $this->redirect('import/index?complete=true');
        }
        catch(Doctrine_Exception $e)
        {  
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error, 'Darwin2 :');        
        }
      }
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
    $this->form = new ImportsFormFilter(null,array('user_ref' =>$this->getUser()->getId()));
  }  
  public function executeSearch(sfWebRequest $request)
  {
    $this->form = new ImportsFormFilter(null,array('user_ref' =>$this->getUser()->getId()));
    $this->setCommonValues('import', 'updated_at', $request);
    if( $request->getParameter('orderby', '') == '' && $request->getParameter('orderdir', '') == '')
      $this->orderDir = 'desc';

    $this->s_url = 'import/search'.'?is_choose='.$this->is_choose;
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
              break 2;
            }
          }
        }
      }
    }
  }
     
}
