<?php

/**
 * ImportsTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ImportsTable extends Doctrine_Table
{
  /**
    * Returns an instance of this class.
    *
    * @return object ImportsTable
    */
  public static function getInstance()
  {
      return Doctrine_Core::getTable('Imports');
  }

  public function markOk($id)
  {
    $q = Doctrine_Query::create()->update('staging s');
    $q->andwhere('import_ref = ? ',$id)
      ->andWhere("status = ?",'')
      ->set('to_import', '?',true)
      ->execute();
    $q = Doctrine_Query::create()->update('Imports');
    $q->andwhere('id = ? ',$id)
      ->set('state', '?','waiting for import')
      ->execute();
  }
}