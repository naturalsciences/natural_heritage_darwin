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
  //not limit not used
  public function completeAsArray($user, $needle, $exact, $limit = 30, $level="")
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
  
  public function getInstitutionAsArray($id)
	{
		$sql="SELECT p.* FROM people p WHERE id=:id AND is_physical=FALSE;";
		$conn_MGR = Doctrine_Manager::connection();
        $conn = $conn_MGR->getDbh();
		$stmt=$conn->prepare($sql);
        $stmt->bindValue(":id", $id);
			
		$stmt->execute();
        $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($rs)>0)
		{
            $rs=$rs[0];			
			$sql2="SELECT i.* FROM identifiers i WHERE i.referenced_relation='people' AND i.record_id=:id";
			$stmt2=$conn->prepare($sql2);
			$stmt2->bindValue(":id", strtolower($rs["id"]));
			$stmt2->execute();
			$rs2=$stmt2->fetchAll(PDO::FETCH_ASSOC);
			$identifiers=Array();
			foreach($rs2 as $rec2)
			{
				$identifiers[]=Array("identifier_protocol"=> $rec2["protocol"], "identifier_value"=> $rec2["value"]);
			}
			$rs["people_identifiers"]=$identifiers;
		}
		return $rs;
	}
	
	public function getInstitutionAsArrayIdentifier($identifier_protocol, $identifier_value)
	{
		$sql="select i.record_id  from Identifiers i where referenced_relation = 'people' AND LOWER(i.protocol)=:protocol AND i.value=:value;";
		$conn_MGR = Doctrine_Manager::connection();
        $conn = $conn_MGR->getDbh();
		$stmt=$conn->prepare($sql);
        $stmt->bindValue(":protocol", strtolower($identifier_protocol));
		$stmt->bindValue(":value", $identifier_value);
		$stmt->execute();
        $rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
		$res_json=Array();
		
		foreach($rs as $rec)
		{
			$res_json[]=$this->getInstitutionAsArray($rec["record_id"]);
		}
		return $res_json;		
	}
}
