<?php

/**
 * MultimediaTodelete
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class MultimediaTodelete extends BaseMultimediaTodelete
{
  public function getFullURI()
  {
    return sfConfig::get('sf_upload_dir').'/multimedia/'.$this->getUri();
  }

  public function deleteFile()
  {
    $url = $this->getFullURI();
    if(! is_writable($url))
      throw new Exception('Folder is not writable');

    unlink($url);
    if(file_exists($url))
      throw new Exception('Unable to remove file');
  }
}
