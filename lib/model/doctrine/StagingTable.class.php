<?php

/**
 * StagingTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class StagingTable extends DarwinTable
{
  /**
   * Returns an instance of this class.
   *
   * @return object StagingTable
   */
  public static function getInstance()
  {
      return Doctrine_Core::getTable('Staging');
  }
  
  /**
  * Get distinct Host Relationships
  * @return Doctrine_collection with distinct "host_relationship" as column
  */
  public function getDistinctHostRelationships()
  {
      return $this->createFlatDistinct('specimens', 'host_relationship', 'host_relationship')->execute();
  }

  /**
   * For an array of staging.id provided, get, for each of them, the count of usage in "related tables"
   * @param array $record_ids Array of staging entries ids
   * @return array An array with list of staging ids provided and the usage count in "related tables" for
   *               each of them
   */
  public function findLinked($record_ids)
  {
    if(! count($record_ids)) return array();
    $record_ids_as_string = implode(',',$record_ids);
    $conn = Doctrine_Manager::connection();
    $sql = "SELECT record_id,
                   count(*) as cnt
            FROM template_table_record_ref r
            WHERE referenced_relation='staging'
              AND record_id = ANY('{ $record_ids_as_string }'::int[])
            GROUP BY record_id";
    $result = $conn->fetchAssoc($sql);
    return $result;
  }

  public function markTaxon($import_ref)
  {
    $q = Doctrine_Query::create()
      ->update('Staging')
      ->set('create_taxon', '?','true')
      ->andwhere('import_ref = ? ',$import_ref)
      ->execute();
    $q = Doctrine_Query::create()->update('Imports');
    $q->andwhere('id = ? ',$import_ref)
      ->set('state', '?','processing')
      ->execute();
  }
}
