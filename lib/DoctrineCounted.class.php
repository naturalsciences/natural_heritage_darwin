<?php 
class DoctrineCounted
{
  public $count_query;
  public function count()
  {
    return $this->count_query->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }
}
