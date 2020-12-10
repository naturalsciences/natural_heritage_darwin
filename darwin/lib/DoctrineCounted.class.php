<?php 
class DoctrineCounted
{
  public $count_query;
  public $all_results;
  public function count()
  {
    //return $this->count_query->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
	$this->all_results=$this->count_query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
	$first;
	foreach($this->all_results as $val)
	{
			return $val;
    }		
	return 0;
  }
  //ftheeten 2020 02 11
  public function countAllFields()
  {
    return $this->count_query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
  }
}