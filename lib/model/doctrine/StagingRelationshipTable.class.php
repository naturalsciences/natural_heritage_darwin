<?php

/**
 * StagingRelationshipTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class StagingRelationshipTable extends Doctrine_Table
{
  /**
    * Returns an instance of this class.
    *
    * @return object StagingRelationshipTable
    */
  public static function getInstance()
  {
      return Doctrine_Core::getTable('StagingRelationship');
  }
  public function UpdateInstitutionRef($institution)
  {
    $q = Doctrine_Query::create()
      ->update('StagingRelationship s')
      ->set('s.institution_ref',$institution['institution_ref'])
      ->where('s.id = ?',$institution['id']) ;
    return $q->execute() ;
  }
}
