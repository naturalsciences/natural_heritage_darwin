<?php

/**
 * InformativeWorkflowTable
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class InformativeWorkflowTable extends DarwinTable
{
  /**
   * Returns an instance of this class.
   *
   * @return object InformativeWorkflowTable
   */
  public static function getInstance()
  {
      return Doctrine_Core::getTable('InformativeWorkflow');
  }

  /**
  * Find all associated workflow (joined with values)
  * for a given table and record id
  * @param string $table_name db table name
  * @param int $record_id id of the record
  * @return a Doctrine_collection of results
  */
  public function prepareQuery($table_name, $record_id)
  {
     $q = Doctrine_Query::create()
	 ->from('InformativeWorkflow iw')
	 ->orderBy('iw.modification_date_time DESC');
     $q = $this->addCatalogueReferences($q, $table_name, $record_id, 'iw', true);
     return $q;
  }

  public function findForTable($table_name, $record_id)
  {
    $q = self::prepareQuery($table_name, $record_id);
    $q->limit(5) ;
    return $q->execute() ;
  }

  public function findAllForTable($table_name, $record_id)
  {
    $q = self::prepareQuery($table_name, $record_id);
    return $q->execute() ;
  }

  public function deleteRow($id, $user)
  {
  print($id);
    $q = Doctrine_Query::create()
    ->delete('InformativeWorkflow i')
    ->where('i.id = ?',$id);
    $this->addRightsCheck($user, $q, true);
    $q->execute();
  }

  public function getAllLatestWorkflow($user,$status)
  {
    $q = Doctrine_Query::create()
      ->from('InformativeWorkflow i')
      ->where('is_last=?',true) ;
    if($status != 'all') $q->addWhere('status=?',$status) ;
    $this->addRightsCheck($user, $q);
    return $q ;
  }

  protected function addRightsCheck($user, $q, $with_calatogues = false)
  {
    if($user->isA(Users::ADMIN) && ! $with_calatogues)
    {
      $q->AndWhere('referenced_relation = ?', 'specimens')   ;
      return $q ;
    }

    $where_str = "( referenced_relation = 'specimens' AND exists ( select fct_filter_encodable_row(record_id::varchar , 'spec_ref',".$user->getId().")) )";

    if($with_calatogues) {
      $where_str .= " OR referenced_relation != 'specimens' ";
    }

    $q->andWhere($where_str);
    return $q;
  }
}
