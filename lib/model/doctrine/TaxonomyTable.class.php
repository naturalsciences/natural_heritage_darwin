<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class TaxonomyTable extends DarwinTable
{
  
  public function getTaxonByName($name,$level,$path)
  {
    $q = Doctrine_Query::create()
      ->from('Taxonomy t')
      ->where('t.name = ?', $name)
      ->andWhere('t.level_ref = ?', $level)
      ->andWhere('t.path = ?', $path);

    return $q->fetchOne();
  }

  public function getRealTaxon()
  {
    $q = Doctrine_Query::create()
      ->from('Taxonomy t')
      ->where('t.id > 0') ;  
      return $q->execute() ;  
  }

  public function ifTaxonExist($level,$name)
  {
    $q = Doctrine_Query::create()
      ->from('Taxonomy t')
      ->innerjoin('t.Level l')
      ->where('t.name_indexed = fulltoindex(?)', $name)
      ->andWhere('l.level_sys_name = ?', $level);
    return $q->fetchOne();

  }
}
