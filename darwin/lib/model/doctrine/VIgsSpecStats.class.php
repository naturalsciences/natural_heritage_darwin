<?php

/**
 * VIgsSpecStats
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class VIgsSpecStats extends BaseVIgsSpecStats
{
 public function getIgDateMasked ()
  {
    $dateTime = new FuzzyDateTime($this->_get('ig_date'), $this->_get('ig_date_mask'));
    return $dateTime->getDateMasked();
  }
  
  public function getIgDate()
  {
    $from_date = new FuzzyDateTime($this->_get('ig_date'), $this->_get('ig_date_mask'));
    return $from_date->getDateTimeMaskedAsArray();
  }

  public function setIgDate($fd)
  {
    if ($fd instanceof FuzzyDateTime)
    {
      $this->_set('ig_date', $fd->format('Y/m/d'));
      $this->_set('ig_date_mask', $fd->getMask());
    }
    else
    {
      $dateTime = new FuzzyDateTime($fd, 56, true); 
      $this->_set('ig_date', $dateTime->format('Y/m/d'));
      $this->_set('ig_date_mask', $dateTime->getMask());
    }
  }
  
    //ftheeten 2018 04 10
  public function countSpecimens()
  {
    $q = Doctrine_Query::create()
      ->select("count(*) as count ")
      ->from('Specimens s')
      ->where('s.ig_ref = ?', $this->getId());
    $row= $q->fetchOne();
    return $row["count"];
  }
}
