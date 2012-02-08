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
    $multimedia = Doctrine::getTable('Multimedia')->findOneById($request->getParameter('id')) ;
    $file = sfConfig::get('sf_upload_dir').'/multimedia/'.$multimedia->getUri() ;
    header("Content-disposition: attachment; filename=".$multimedia->getFilename()); 
    header("Content-Type: application/force-download"); 
    header("Content-Transfer-Encoding: ".$multimedia->getMimeType()."\n");
    header("Content-Length: ".filesize($file)); 
    header("Pragma: no-cache"); 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public"); 
    header("Expires: 0"); 
    readfile($file); 
    return(0) ;
  } 
}
