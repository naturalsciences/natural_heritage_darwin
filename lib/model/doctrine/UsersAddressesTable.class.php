<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class UsersAddressesTable extends DarwinTable
{
  /**
  * Get Distincts Countries
  * @return array an Array of countries in keys
  */
  public function getDistinctCountries()
  {
    return $this->createFlatDistinct('users_addresses', 'country', 'countries')->execute();
  }

  public function fetchByUser($id)
  {
    $q = Doctrine_Query::create()
	  ->from('UsersAddresses r')
	  ->where('r.person_user_ref = ?',$id)
	  ->orderBy('r.country ASC,r.locality ASC, r.id ASC');
    return $q->execute();
  }
}
