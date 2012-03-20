<?php

/**
 * multimedia actions.
 *
 * @package    darwin
 * @subpackage multimedia
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class multimediaActions extends DarwinActions
{

  public function executeDownloadFile(sfWebRequest $request)
  {
    $this->setLayout(false);  
    $multimedia = Doctrine::getTable('Multimedia')->findOneById($request->getParameter('id')) ;
    if(!($this->getUser()->isAtLeast(Users::ADMIN) || $this->checkRights($multimedia))) $this->forwardToSecureAction();
    $this->forward404Unless(file_exists($file = $multimedia->getFullURI()),sprintf('This file does not exist') );
    // Adding the file to the Response object
    $this->getResponse()->clearHttpHeaders();
    $this->getResponse()->setHttpHeader('Pragma: private', true);
    $this->getResponse()->setHttpHeader('Content-Disposition',
                            'attachment; filename="'.
                            $multimedia->getFilename().'"');
    $this->getResponse()->setContentType("application/force-download ".$multimedia->getMimeType());    
    $this->getResponse()->sendHttpHeaders();
    $this->getResponse()->setContent(readfile($file));    
    return sfView::NONE;
  } 
  
  public function checkRights($multimedia) 
  {
    if($multimedia->getReferencedRelation() == 'loans') 
      return(Doctrine::getTable('LoanRights')->isAllowed($this->getUser()->getId(), $multimedia->getRecordId()));
    if($multimedia->getReferencedRelation() == 'loan_items') 
    {   
      $item = Doctrine::getTable('LoanItems')->findOneById($multimedia->getRecordId()) ;
      return(Doctrine::getTable('LoanRights')->isAllowed($this->getUser()->getId(), $item->getLoanRef()));    
    }
    /* Actualy multimedia is only in loans and loan items, so for the moment any otehr referenced relation
     returns false */
    return false ;
  }

  public function executePreview(sfWebRequest $request)
  {
    $this->setLayout(false);  
    $multimedia = Doctrine::getTable('Multimedia')->findOneById($request->getParameter('id')) ;
    if(!($this->getUser()->isAtLeast(Users::ADMIN) || $this->checkRights($multimedia))) $this->forwardToSecureAction();
    $this->forward404Unless(file_exists($multimedia->getFullURI()),sprintf('This file does not exist') );

    // Adding the file to the Response object
    $this->getResponse()->clearHttpHeaders();


    if($multimedia->getSize() > (1024 * 1024 * sfConfig::get('dw_preview_max_size', '10')) )
    {
      $url = sfConfig::get('sf_web_dir').'/'.sfConfig::get('sf_web_images_dir_name', 'images').'/img_placeholder.png';
      $this->getResponse()->setHttpHeader('Content-type', 'image/png');
      $this->getResponse()->sendHttpHeaders();
      $file = file_get_contents($url);
      $this->getResponse()->setContent($file);
      return sfView::NONE;
    }

    $this->getResponse()->setHttpHeader('Pragma: private', true);
    $this->getResponse()->setHttpHeader('Content-type', $multimedia->getMimeType());
    $this->getResponse()->sendHttpHeaders();
    $this->getResponse()->setContent(imagejpeg($multimedia->getPreview(100,100)));
    return sfView::NONE;
  }

  public function executeInsertFile(sfWebRequest $request)
  {
    $form = new RelatedFileForm() ;
    $form->bind(null, $request->getFiles($request->getParameter('formname')));
    $file = $form->getValue('filenames');
    if($form->isValid())
    {
      if(!Multimedia::CheckMimeType($file->getType()))
        return $this->renderText('<script type="text/javascript">parent.displayFileError(\'This type of file is not allowed\')</script>') ;
      // first save the file
      $filename = sha1($file->getOriginalName().rand());
      while(file_exists(sfConfig::get('sf_upload_dir').'/multimedia/temp/'.$filename))
        $filename = sha1($file->getOriginalName().rand());
      $extension = $file->getExtension($file->getOriginalExtension());
      $file->save(sfConfig::get('sf_upload_dir').'/multimedia/temp/'.$filename);
      if($file->isSaved()) {
        $file_info = array(
          'title' => $file->getOriginalName(),
          'filename' => $file->getOriginalName(),
          'mime_type' => $file->getType(),
          'type' => $extension,
          'uri' => $filename,
          'referenced_relation' => $request->getParameter('table'),
          'creation_date' => date('m/d/Y')
        ) ;
        $this->getUser()->setAttribute($filename, $file_info);
      }
      return $this->renderText('<script type="text/javascript">parent.getFileInfo(\''.$filename.'\')</script>') ;
    }
    return $this->renderText('<script type="text/javascript">parent.displayFileError(\''.$form->getErrorSchema()->current().'\')</script>') ;
  }

  public function executeAddRelatedFiles(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('num') && $request->hasParameter('table') && $request->hasParameter('file_id'));
    $number = intval($request->getParameter('num'));
    $form = $this->getSpecificForm($request);
    $file = $this->getUser()->getAttribute($request->getParameter('file_id')) ;
    $form->addRelatedFiles($number,$file);
    return $this->renderPartial('multimedia/multimedia',array('form' => $form['newRelatedFiles'][$number], 'row_num'=>$number));
  }

}
