<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Lithostratigraphy extends BaseLithostratigraphy
{
  public function getNameWithFormat()
  {
    return $this->_get('name');
  }

  public function getName()
  {
    if(! $this->isNew() && $this->_get('id')==0)
      return '-';
    return $this->_get('name');
  }
}