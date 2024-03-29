<?php

/**
 * TemporalInformationTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
 
   //ftheeten PHP8
const ESC_RAW='esc_raw';

class TemporalInformationTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object TemporalInformationTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('TemporalInformation');
    }
    
     //2019 03 07
  public function getSortedTemporalInformation($gtu_id)
  {
    $q = Doctrine_Query::create()
      ->from('TemporalInformation t')
      ->where('t.gtu_ref = ?', $gtu_id)
      ->orderBy('t.id') ;
    return $q->execute();

}   

  public function getTemporalInformationNoSpecimen($gtu_id)
  {
    $q = Doctrine_Query::create()
      ->from('TemporalInformation t')
      ->where('t.gtu_ref = ?', $gtu_id)
	  ->andWhere('t.specimen_ref IS NULL ')
      ->orderBy('t.id') ;
    return $q->execute();
  }

  public function getTemporalInformationNoSpecimenArray($gtu_id)
  {
	$date_array=Array();
	$date_test=$this->getTemporalInformationNoSpecimen($gtu_id);
	$i=0;
	if($date_test !==null)
	{
		foreach($date_test as $date_item)
		{
			$from_date=	$date_item->getFromDateMasked(ESC_RAW);
			$to_date=	$date_item->getToDateMasked(ESC_RAW);

			$date_array[$i]["from_date"]=$from_date;
			$date_array[$i]["to_date"]=$to_date;
			$i++;
		}
	}
	
	return $date_array;
  }
  
    
    //2019 03 07
  public function getDistinctTemporalInformation($gtu_id)
  {
    
    $conn_MGR = Doctrine_Manager::connection();
    $conn = $conn_MGR->getDbh();
    $statement = $conn->prepare("SELECT min(t.id) as id, gtu_ref, min(specimen_ref) as specimen_ref,
from_date, from_date_mask, to_date, to_date_mask FROM temporal_information t WHERE t.gtu_ref=? GROUP BY gtu_ref, from_date, from_date_mask, to_date, to_date_mask ORDER BY from_date");
    $statement->execute(array($gtu_id));
    $res = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $res;
  }
  
  public function deleteTemporalInformation($id)
  {
    $conn_MGR = Doctrine_Manager::connection();
    $conn = $conn_MGR->getDbh();
    $statement = $conn->prepare("DELETE FROM temporal_information WHERE 
        gtu_ref IN (SELECT t2.gtu_ref FROM temporal_information t2 WHERE t2.id= ? ) 
        AND to_date IN (SELECT t2.to_date FROM temporal_information t2 WHERE t2.id= ? )
        AND to_date_mask IN (SELECT t2.to_date_mask FROM temporal_information t2 WHERE t2.id= ? )
        AND from_date IN (SELECT t2.from_date FROM temporal_information t2 WHERE t2.id= ? )
        AND from_date_mask IN (SELECT t2.from_date_mask FROM temporal_information t2 WHERE t2.id= ? )  
        AND specimen_Ref IS NULL        ");
    $statement->execute(array($id,$id,$id,$id,$id ));
  }
  
  public function countTemporalInformationBoundtoSpecimen($id)
	{	
		try
        {

            
            $conn_MGR = Doctrine_Manager::connection();
            $conn = $conn_MGR->getDbh();
            $statement = $conn->prepare("SELECT COUNT(*) FROM temporal_information WHERE 
            (gtu_ref IN (SELECT t2.gtu_ref FROM temporal_information t2 WHERE t2.id= ?) 
            AND to_date IN (SELECT t3.to_date FROM temporal_information t3 WHERE t3.id= ? )
            AND to_date_mask IN (SELECT t4.to_date_mask FROM temporal_information t4 WHERE t4.id= ? )
            AND from_date IN (SELECT t5.from_date FROM temporal_information t5 WHERE t5.id= ? )
            AND from_date_mask IN (SELECT t6.from_date_mask FROM temporal_information t6 WHERE t6.id= ? )  
            AND specimen_ref IS NOT NULL) ;       ");		
          
            $res= $statement->execute(array($id,$id,$id,$id,$id ));
            
            $returned= $statement->fetchColumn();
            return $returned;
         }
         catch(Exception  $e)
         {
            print("except");
            print($e->getMessage());
         }
	}
}