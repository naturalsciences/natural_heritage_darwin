<?php


class ExtLinksTable extends DarwinTable
{
    
  public static function getInstance()
  {
      return Doctrine_Core::getTable('ExtLinks');
  }
  /**
  * Find all external links for a table name and a recordId
  * @param string $table_name the table to look for
  * @param int record_id the record to be commented out.
  * @return Doctrine_Collection Collection of Doctrine records
  */
  public function findForTable($table_name, $record_id)
  {
     $q = Doctrine_Query::create()
	 ->from('ExtLinks e');
     $q = $this->addCatalogueReferences($q, $table_name, $record_id, 'e', true);
    return $q->execute();
  }    
  
  
    public function getRelatedLinks_as_array($table_name, $record_ids)
  {
     if(empty($record_ids)) 
	 {
		 return array() ;
     }
	 else
	 {

		$conn = Doctrine_Manager::connection();
	    $sql = "select referenced_relation, record_id, ext_links.id, url, comment, ext_links.type, access_rights, uuid  FROM ext_links INNER JOIN specimens ON record_id=specimens.id  WHERE referenced_relation=:ref AND  record_id = ANY (:ids) ;";
	    $q = $conn->prepare($sql);
		$imploded="{".implode(",",$record_ids)."}" ;
		$q->execute(array(':ref'=> $table_name, ":ids"=> $imploded ));
	    //$response = $q->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
		$response=Array();
		$tmp=$q->fetchAll( PDO::FETCH_ASSOC);
		foreach($tmp as $item)
		{
	
			//if(!array_key_exists($item["record_id"], $response))
			$response[$item["record_id"]][$item['id']]=$item;			
			
		}
		 return $response;
	 }
  }
}
