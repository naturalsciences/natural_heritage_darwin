<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PeopleTable extends DarwinTable
{
  /**
  * Find item for autocompletion
  * @param $user The User object for right management
  * @param $needle the string entered by the user for search
  * @param $exact bool are we searching the exact term or more or less fuzzy
  * @return Array of results
  */
  public function completeAsArray($user, $needle, $exact, $limit = 30)
  {
    $conn_MGR = Doctrine_Manager::connection();
    $q = Doctrine_Query::create()
      ->from('Institutions')
      ->andWhere('is_physical = ?', true)
      ->orderBy('formated_name ASC')
      ->limit($limit);
    if($exact)
      $q->andWhere("formated_name = ?",$needle);
    else
      $q->andWhere("formated_name_indexed like concat('%',fulltoindex(".$conn_MGR->quote($needle, 'string')."),'%') ");
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
  public function getDistinctTitles()
  {
    return $this->createFlatDistinct('people', 'title', 'titles')->execute();

  }

  /**
  * Search all physical people by name
  * @param string $name a part of the formated name to look for (with ts)
  * @return Doctrine_Collection Collection of People
  */
  public function searchPysical($name)
  {
    $q = Doctrine_Query::create()
      ->from('People p')
      ->andWhere('p.is_physical = ?', true)
      ->andWhere('p.id != 0')
      ->andWhere('p.formated_name_indexed like concat(\'%\', fulltoindex(?), \'%\' )',$name);
    return $q->execute();
  }


  /**
  * Find Only people not institution
  * @param int the id of the people
  * @return Doctrine_Record 
  */
  public function findPeople($id)
  {
    $q = Doctrine_Query::create()
	 ->from('People p')
	 ->where('p.id = ?', $id)
   ->andWhere('p.id != 0')
	 ->andWhere('p.is_physical = ?', true);

    return $q->fetchOne(); 
  }
  
  /**
  * Find Only people with specified family name
  * @param string the name of the people
  * @return Doctrine_Record 
  */
    public function getPeopleByName($name)
    {
      $q = Doctrine_Query::create()
      ->from('people p')
      ->where('p.family_name = ?', $name);

      return $q->fetchOne(); 
    }

}