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
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new MultimediaFormFilter();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('multimedia', 'referenced_relation', $request);
    $this->form = new MultimediaFormFilter(array(),array('user' => $this->getUser()));

    if($request->getParameter('multimedia_filters','') !== '')
    {
      $this->form->bind($request->getParameter('multimedia_filters'));

      if ($this->form->isValid())
      {
        $query = $this->form->getQuery()->orderBy($this->orderBy .' '.$this->orderDir);
        $this->pagerLayout = new PagerLayoutWithArrows(
          new DarwinPager(
            $query,
            $this->currentPage,
            $this->form->getValue('rec_per_page')
          ),
          new Doctrine_Pager_Range_Sliding(
            array('chunk' => $this->pagerSlidingSize)
            ),
          $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
        );

        // Sets the Pager Layout templates
        $this->setDefaultPaggingLayout($this->pagerLayout);
        // If pager not yet executed, this means the query has to be executed for data loading
        if (! $this->pagerLayout->getPager()->getExecuted())
           $this->items = $this->pagerLayout->execute();
      }
    }
  }

  public function executeDownloadFile(sfWebRequest $request)
  {
    $this->setLayout(false);
    $multimedia = Doctrine::getTable('Multimedia')->findOneById($request->getParameter('id')) ;
    if(!($this->getUser()->isAtLeast(Users::ADMIN) || $this->checkRights($multimedia))) $this->forwardToSecureAction();
    $this->forward404If(!($this->getUser()->isAtLeast(Users::ENCODER)) && !($multimedia->getVisible()));
    $this->forward404Unless(file_exists($file = $multimedia->getFullURI()),sprintf('This file does not exist') );
    // Adding the file to the Response object

    $this->getResponse()->clearHttpHeaders();
    $this->getResponse()->setHttpHeader('Pragma: private', true);
    $this->getResponse()->setHttpHeader('Content-Disposition',
                            'attachment; filename="'.
                            $multimedia->getFilename().'"');
    $this->getResponse()->setHttpHeader('content-type', 'application/octet-stream', true);
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
    return true;
  }

  public function executePreview(sfWebRequest $request)
  {
    $this->setLayout(false);
    $multimedia = Doctrine::getTable('Multimedia')->findOneById($request->getParameter('id')) ;
    if(!($this->getUser()->isAtLeast(Users::ADMIN) || $this->checkRights($multimedia))) $this->forwardToSecureAction();
    $this->forward404If(!($this->getUser()->isAtLeast(Users::ENCODER)) && !($multimedia->getVisible()));
    $this->forward404Unless($multimedia,sprintf('This file does not exist') );
    $this->forward404Unless(file_exists($multimedia->getFullURI()),sprintf('This file does not exist') );

    // Adding the file to the Response object
    $this->getResponse()->clearHttpHeaders();


    // If image is too large , display placeholder
    if($multimedia->getSize() > (1024 * 1024 * sfConfig::get('dw_preview_max_size', 10)) )
    {
      $url = sfConfig::get('sf_web_dir').'/'.sfConfig::get('sf_web_images_dir_name', 'images').'/img_placeholder.png';
      $this->getResponse()->setHttpHeader('Content-type', 'image/png');
      $this->getResponse()->sendHttpHeaders();
      $file = file_get_contents($url);
      $this->getResponse()->setContent($file);
      return sfView::NONE;
    }

    $preview = $multimedia->getPreview(100,100);
    $this->getResponse()->setHttpHeader('Pragma: private', true);
    $this->getResponse()->setHttpHeader('Content-type', 'image/png');
    $this->getResponse()->sendHttpHeaders();
    $this->getResponse()->setContent($preview);
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
        return $this->renderText('<script type="text/javascript">parent.displayFileError(\'This type of file is not allowed('.$file->getType().')\')</script>') ;
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
          'creation_date' => date('Y-m-d')
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
    return $this->renderPartial('multimedia/multimedia',array('form' => $form['newRelatedFiles'][$number], 'row_num'=>$number, 'table' => $request->getParameter('table')));
  }

  public function executeAdd(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $file_record = null;

    if($request->hasParameter('rid'))
    {
      $file_record = Doctrine::getTable('Multimedia')->find($request->getParameter('rid'));
    }

    if(! $file_record)
    {
      $this->forward404Unless($request->hasParameter('id') && $request->hasParameter('table') && $request->hasParameter('file_id'));
      $file = $this->getUser()->getAttribute($request->getParameter('file_id')) ;
      $file_record = new Multimedia();
      $file_record->fromArray($file);
      $file_record->setReferencedRelation($request->getParameter('table'));
      $file_record->setRecordId($request->getParameter('id'));
    }

    $this->form = new MultimediaForm($file_record);

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('multimedia'));
      if($this->form->isValid())
      {
        try{
          if($this->form->getObject()->isNew())
            $this->form->setRecordRef($request->getParameter('table'), $request->getParameter('id'));
          $this->form->save();
          $this->form->getObject()->refreshRelated();
          $this->form = new MultimediaForm($this->form->getObject()); //Ugly refresh
          return $this->renderText('ok');
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }
  }

}
