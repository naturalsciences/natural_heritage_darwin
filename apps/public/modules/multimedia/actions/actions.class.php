<?php

/**
 * search actions.
 *
 * @package    darwin
 * @subpackage search
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class multimediaActions extends DarwinActions
{
  public function executeDownloadFile(sfWebRequest $request)
  {
    $this->setLayout(false);
    $multimedia = Doctrine::getTable('Multimedia')->findOneById($request->getParameter('id')) ;
    if($multimedia->getReferencedRelation() == 'loans' || $multimedia->getReferencedRelation() == 'loan_items') $this->forwardToSecureAction();
    $this->forward404If(!($multimedia->getVisible()));
    $this->forward404Unless(file_exists($file = $multimedia->getFullURI()),sprintf('This file does not exist') );
    // Adding the file to the Response object

    $this->getResponse()->clearHttpHeaders();
    $this->getResponse()->setHttpHeader('Pragma: private', true);
    $this->getResponse()->setHttpHeader('Content-Disposition',
                            'attachment; filename="'.
                            $multimedia->getFilename().'"');
    //$this->getResponse()->setContentType("application/force-download ".$multimedia->getMimeType());
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
    if($multimedia->getReferencedRelation() == 'loans' || $multimedia->getReferencedRelation() == 'loan_items') $this->forwardToSecureAction();
    $this->forward404If(!($multimedia->getVisible()));
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

}
