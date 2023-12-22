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
  
  
}
