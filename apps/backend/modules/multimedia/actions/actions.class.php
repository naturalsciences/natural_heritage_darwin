<?php

/**
 * multimedia actions.
 *
 * @package    darwin
 * @subpackage multimedia
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class multimediaActions extends DarwinActions
{
  public function executeDownloadFile(sfWebRequest $request)
  {
    $this->setLayout(false);  
    $multimedia = Doctrine::getTable('Multimedia')->findOneById($request->getParameter('id')) ;
    $file = sfConfig::get('sf_upload_dir').'/multimedia/'.$multimedia->getUri() ;
    // Adding the file to the Response object
    $this->getResponse()->clearHttpHeaders();
    $this->getResponse()->setHttpHeader('Pragma: public', true);    
    $this->getResponse()->setHttpHeader('Content-Disposition',
                            'attachment; filename="'.
                            $multimedia->getFilename().'"');    
    $this->getResponse()->setContentType("application/force-download ".$multimedia->getMimeType());    
    $this->getResponse()->sendHttpHeaders();
    $this->getResponse()->setContent(readfile($file));    
    return sfView::NONE;
  } 
}
