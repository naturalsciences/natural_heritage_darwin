<?php

/**
 * ExtLinks
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ExtLinks extends BaseExtLinks{
	 private static $link_types = array(
     /*RBINS values values*/
	'no'=>'',
    'ext' => 'External',
    'vc' => 'Virtual Collection',
	'nagoya'=> 'Nagoya',
	'ltp' => 'LTP',
	/*RMCA values*/
	'dna' => 'DNA link',
	'document'=>'document',
	'image_link'=>'image link', 
	'html_3d_snippet'=>'html 3d snippet' , 
	'thumbnail'=>'thumbnail',
	'cites'=>'CITES',
	'acquisition'=>'Acquisition documents'
    ) ;

  static public function getLinkTypes()
  {
    return self::$link_types ;
  }

  public function setUrl($url) 
  {
    if(substr(strtolower($url),0,4) != 'http')
      $url = 'http://'.$url ;
    $this->_set('url',$url) ;
  }

}
		
 