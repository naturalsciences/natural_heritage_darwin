<?php

/**
 * CollectingTools
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class CollectingTools extends BaseCollectingTools
{
  public function getName()
  {
    if(! $this->isNew() && $this->_get('id')==0)
      return '-';
    return $this->_get('tool');
  }

}