<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Identifications extends BaseIdentifications
{
  public function getNotionDateMasked ()
  {
    $dateTime = new FuzzyDateTime($this->_get('notion_date'), $this->_get('notion_date_mask'));
    return $dateTime->getDateMasked();
  }
  
  public function getNotionDate()
  {
    $from_date = new FuzzyDateTime($this->_get('notion_date'), $this->_get('notion_date_mask'));
    return $from_date->getDateTimeMaskedAsArray();
  }

  public function setNotionDate($fd)
  {
    if ($fd instanceof FuzzyDateTime)
    {
      if ($this->getNotionDate() != $fd->getDateTimeMaskedAsArray())
      {
        $this->_set('notion_date', $fd->format('Y/m/d'));
        $this->_set('notion_date_mask', $fd->getMask());
      }
    }
    else
    {
      $dateTime = new FuzzyDateTime($fd, 56, true); 
      if ($this->getNotionDate() != $dateTime->getDateTimeMaskedAsArray())
      {
        $this->_set('notion_date', $dateTime->format('Y/m/d'));
        $this->_set('notion_date_mask', $dateTime->getMask());
      }
    }
  }


}