<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CatalogueRelationshipsTable extends DarwinTable
{
  public function getRelationsForTable($table, $id, $type=null)
  {
    $model = DarwinTable::getModelForTable($table);
    $q = Doctrine_Query::create()
      ->select('r.id, r.referenced_relation, r.record_id_1, r.record_id_2, r.relationship_type , t.id, t.name' . ($table == 'taxonomy' ? ', t.extinct' : '') )
      ->from('CatalogueRelationships r, '.$model. ' t')
      ->andwhere('r.referenced_relation = ?', $table)
      ->andWhere('r.record_id_1=?', $id)
      ->andWhere('t.id=r.record_id_2')
      ->setHydrationMode(Doctrine_Core::HYDRATE_NONE);

    if($type !== null)
      $q->andWhere('r.relationship_type = ?',$type);

    $items = $q->execute();

    $results = array();
    foreach($items as $item)
    {
      $cRecord = new $model();
      $cRecord->setName($item[6]);
      $cRecord->setId($item[5]);
      if($table=='taxonomy')
        $cRecord->setExtinct($item[7]);

      $results[] = array(
        'id' => $item[0],
        'referenced_relation' => $item[1],
        'record_id_1' => $item[2],
        'record_id_2' => $item[3],
        'relationship_type' => $item[4],
      'ref_item' => $cRecord,
      );
    }
    return $results;
  }
}
