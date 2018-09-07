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
    if(!$multimedia || $multimedia->getReferencedRelation() == 'loans' || $multimedia->getReferencedRelation() == 'loan_items')
      $this->forward404('Multimedia not found or not authorized');
    $this->forward404If(!($multimedia->getVisible()));
    $this->forward404Unless(file_exists($file = $multimedia->getFullURI()),sprintf('This file does not exist') );
    // Adding the file to the Response object

    $this->getResponse()->clearHttpHeaders();
    $this->getResponse()->setHttpHeader('Pragma: private', true);
    $this->getResponse()->setHttpHeader(
      'Content-Disposition',
      'attachment; filename="'.
      $multimedia->getFilename().'"'
    );

    $this->getResponse()->setHttpHeader('content-type', 'application/octet-stream', true);
    $this->getResponse()->sendHttpHeaders();
    $this->getResponse()->setContent(readfile($file));
    return sfView::NONE;
  }

  public function executePreview(sfWebRequest $request)
  {
    $this->setLayout(false);
    $multimedia = Doctrine::getTable('Multimedia')->findOneById($request->getParameter('id')) ;
    if(! $multimedia || $multimedia->getReferencedRelation() == 'loans' || $multimedia->getReferencedRelation() == 'loan_items')
      $this->forwardToSecureAction('Multimedia not found or not authorized');
    $this->forward404If(!($multimedia->getVisible()));
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
}
