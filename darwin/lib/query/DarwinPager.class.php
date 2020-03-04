<?php

class DarwinPager extends Doctrine_Pager
{
      /**
     * execute
     *
     * Executes the query, populates the collection and then return it
     * Inherit for Doctrine_Pager but avoid the query when the cound is 0
     *
     * @param $params               Optional parameters to Doctrine_Query::execute
     * @param $hydrationMode        Hydration Mode of Doctrine_Query::execute returned ResultSet.
     * @return Doctrine_Collection  The root collection
     */
	public $additional_count;
    public function execute($params = array(), $hydrationMode = null)
    {
        if ( !$this->getExecuted()) {
            $this->_initialize($params);
        }
        if( $this->getNumResults() == 0 )
        {
          $this->getQuery()->getSqlQuery();
          return new  Doctrine_Collection($this->getQuery()->getRoot()->getComponentName());
        }
        else
		{
          $tmp= $this->getQuery()->execute($params, $hydrationMode);
		  return $tmp;
		}
	}
	
	public function setAdditionalCount($val)
	{
		$this->additionalCount=$val;
	}
	
	public function getAdditionalCount()
	{
		$this->additionalCount;
	}
}
