<?php

/**
 * TaxonomyMetadata
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class TaxonomyMetadata extends BaseTaxonomyMetadata
{
  public function getCreationDateMasked ()
  {
    $dateTime = new FuzzyDateTime($this->_get('creation_date'), $this->_get('creation_date_mask'));
    return $dateTime->getDateMasked();
  }

  public function getCreationDate()
  {
    $from_date = new FuzzyDateTime($this->_get('creation_date'), $this->_get('creation_date_mask'));
    return $from_date->getDateTimeMaskedAsArray();
  }
  
  
  public function setCreationDate($fd)
  {
    if ($fd instanceof FuzzyDateTime)
    {
      if ($this->getCreationDate() != $fd->getDateTimeMaskedAsArray()) {
        $this->_set('creation_date', $fd->format('Y/m/d'));
        $this->_set('creation_date_mask', $fd->getMask());
      }
    }
    else
    {
      $dateTime = new FuzzyDateTime($fd, 56, true); 
      if ($this->getCreationDate() != $dateTime->getDateTimeMaskedAsArray()) {
        $this->_set('creation_date', $dateTime->format('Y/m/d'));
        $this->_set('creation_date_mask', $dateTime->getMask());
      }
    }
  }
}
