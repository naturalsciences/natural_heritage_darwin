<?php

class InstitutionsTable extends DarwinTable
{
  /**
  * Find item for autocompletion
  * @param $user The User object for right management
  * @param $needle the string entered by the user for search
  * @param $exact bool are we searching the exact term or more or less fuzzy
  * @return Array of results
  */
   //ftheeten added default to level 2016 11 04
  public function completeAsArray($user, $needle, $exact, $limit = 30, $level='')
  {
    $conn_MGR = Doctrine_Manager::connection();
    $q = Doctrine_Query::create()
      ->from('Institutions')
      ->andWhere('is_physical = ?', false)
      ->orderBy('formated_name ASC')
      ->limit($limit);
    if($exact)
      $q->andWhere("formated_name = ?",$needle);
    else
      $q->andWhere("formated_name_indexed like concat(fulltoindex(".$conn_MGR->quote($needle, 'string')."),'%') ");
    $q_results = $q->execute();
    $result = array();
    foreach($q_results as $item) {
      $result[] = array('label' => $item->getFormatedName(), 'value'=> $item->getId() );
    }
    return $result;
  }

  /**
  * Find all distinct tyoe of institutions
  * @return Doctrine_Collection with only the key 'type'
  */
  public function getDistinctSubType()
  {
    return $this->createFlatDistinct('people', 'sub_type', 'type')->execute();
  }

  /**
  * Find Only institution not people
  * @param int the id of the people
  * @return Doctrine_Record 
  */
  public function findInstitution($id)
  {
    $q = Doctrine_Query::create()
      ->from('Institutions p')
      ->where('p.id = ?', $id)
      ->andWhere('p.is_physical = ?', false);

    return $q->fetchOne(); 
  }
  
  public function getInstitutionByName($name)
  {
    $q = Doctrine_Query::create()
      ->from('Institutions p')
      ->where('p.family_name = ?', $name)
      ->andWhere('p.is_physical = ?', false);

    return $q->fetchOne();  	
  } 
}
