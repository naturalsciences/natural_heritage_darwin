<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Mineralogy extends BaseMineralogy
{
  public function getNameWithFormat()
  {
    return $this->getName();
  }

  public function getName()
  {
    if(! $this->isNew() && $this->_get('id')==0)
      return '-';
    return $this->_get('name');
  }
}