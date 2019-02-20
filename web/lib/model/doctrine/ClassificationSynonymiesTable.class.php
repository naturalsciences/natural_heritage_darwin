<?php
/**
 * Synonyms or Homonyms for catalogues
 */
class ClassificationSynonymiesTable extends DarwinTable
{
  /**
   * deleteAllItemInGroup
   * Delete All synonyms in a given group
   * @param int $id Id of the group
   */
  public function deleteAllItemInGroup($id)
  {
    $q = Doctrine_Query::create()
      ->delete('ClassificationSynonymies s')
      ->where('s.group_id = ?',$id)
      ->execute();
  }

  /**
   * findGroupsIdsForRecord
   * find group ids in an array of ids for a given table_name and record_id
   * @param string $table_name the name of the referenced relation
   * @param int $record_id record id of the referenced item_id
   * @return  array Array of ids of the groups (or empty array if there is no existing groups)
  */
  public function findGroupsIdsForRecord($table_name, $record_id)
  {
    $q = Doctrine_Query::create()
	 ->select('DISTINCT(group_id) as group')
	 ->from('ClassificationSynonymies s INDEXBY group');
    $q = $this->addCatalogueReferences($q,$table_name, $record_id,'s');
    $q->orderBy('group');
    $results = $q->fetchArray();
    if(!count($results))
      return array();
    $groups = array();
    foreach($results as $result)
    {
      $groups[] = $result['group'];
    }
    return $groups;
  }

  public function findSynonymsIds($table_name, $record_id)
  {
    $groups = $this->findGroupsIdsForRecord($table_name, $record_id);
    if(empty($groups))
      return array();
    $q = Doctrine_Query::create()
      ->select('distinct(s.record_id) as record_id')
      ->from('ClassificationSynonymies s')
      ->andwhereIn('s.group_id', $groups);
    $results = $q->execute();
    $res_array = array();
    foreach($results as $val)
    {
      $res_array[] = $val->getRecordId();
    }
    return $res_array;
  }

  /**
   * findAllForRecord
   * Find all synoyms (including self) of a given table_name and record_id (and group if specified).
   * Also takes the 'name' column of the referenced record
   * @param string $table_name the name of the referenced relation
   * @param int $record_id record id of the referenced item_id
   * @param array $groups array of group ids to filter the research
   * @return array of array of result. each item has keys  id, record_id, group_id, is_basionym, order_by, item_id, name (of the referenced record)
  */
  public function findAllForRecord($table_name, $record_id, $groups = null)
  {
    if($groups === null)
      $groups = $this->findGroupsIdsForRecord($table_name, $record_id);

    if(empty($groups))
      return array();

    $q = Doctrine_Query::create()
      ->select('s.group_name, s.id, s.record_id, s.group_id, s.is_basionym, s.order_by, t.name, t.id ' .
        ($table_name=='taxonomy' ? ', t.extinct' : '') )
      ->from('ClassificationSynonymies s, '.DarwinTable::getModelForTable($table_name). ' t')
      ->where('s.referenced_relation = ?',$table_name) //Not really necessay but....
      ->andWhere('s.record_id=t.id')
      ->andwhereIn('s.group_id', $groups)
      ->orderBy('s.group_name ASC, s.order_by ASC')
      ->setHydrationMode(Doctrine::HYDRATE_NONE);
    $items = $q->execute();

    $results = array();
    foreach($items as $item)
    {
      $catalogue = DarwinTable::getModelForTable($table_name);
      $cRecord = new $catalogue();
      $cRecord->setName($item[6]);
      $cRecord->setId($item[7]);
      if($table_name=='taxonomy')
        $cRecord->setExtinct($item[8]);

      //group_name 
      if(! isset($results[$item[0]]) )
        $results[$item[0]]=array();
      $results[$item[0]][] = array(
        'id' => $item[1],
        'record_id' => $item[2],
        'group_id' => $item[3],
        'is_basionym' => $item[4],
        'order_by' => $item[5],
        'ref_item' => $cRecord,
      );
    }
    return $results;
  }
  
  /**
   * findGroupnames
   * Give all the possible groups of synonymies
   * @return array an array of key/value (value is localised)
 */
  public function findGroupnames()
  {
    return array(
      'synonym' => $this->getI18N()->__('Synonyms'),
      'isonym' => $this->getI18N()->__('Isonyms'),
      'homonym' => $this->getI18N()->__('Homonyms'),
      'rename' => $this->getI18N()->__('Renaming'),
    );
  }

  /**
   * findNextGroupId
   * Give the next unused id for a group
   * @return int next unused id of group
  */
  public function findNextGroupId()
  {
    $conn = Doctrine_Manager::connection()->getDbh();
    $statement = $conn->prepare("SELECT nextval('classification_synonymies_group_id_seq')");
    $statement->execute();
    $resultset = $statement->fetchAll(PDO::FETCH_NUM);
    return $resultset[0][0];
  }
  
  /**
   * findGroupIdFor
   * Give the id of the group for a given group type and referenced recordµ
   * @param string $table_name the name of the referenced relation
   * @param int $record_id record id of the referenced item_id
   * @param string $type string of the group name
   * @return int the id of the group or '0' if it doesn't exist yet
  */
  public function findGroupIdFor($table_name, $record_id, $type)
  {
    $q = Doctrine_Query::create()
	 ->from('ClassificationSynonymies s')
	 ->andWhere('s.group_name = ?',$type);

    $q = $this->addCatalogueReferences($q,$table_name, $record_id,'s');

    $result = $q->fetchOne();
    if($result)
      return $result->getGroupId();
    else
      return 0;
  }

  /**
   * saveOrder
   * Save the order of the synonyms in a group
   * @param string $ids string of Ids separated by ',' of ClassificationSynonymies records id ordered
  */
  public function saveOrder($ids)
  {
    $id_list = explode(',',$ids);
     $q = Doctrine_Query::create()
      ->update('ClassificationSynonymies')
      ->set('order_by',"fct_array_find(?, id::text) ",implode(",",$id_list))
      ->whereIn('id', $id_list);

    $q->execute();
  }
  
  /**
   * Set Basionym
   * @param int $group_id The id of the synonym group
   * @param int $basionym_id The id of the new basionym record
  */
  public function setBasionym($group_id, $basionym_id)
  {
    $this->resetBasionym($group_id);
    $element = $this->find($basionym_id);
    $element->setIsBasionym(true);
    $element->save();
  }
  
  /**
  * Reset the basionym for a given groupId
  * @param int Group Id
  */
  public function resetBasionym($group_id)
  {
    $q = Doctrine_Query::create()
      ->update('ClassificationSynonymies')
      ->set('is_basionym', '?', false)
      ->where('group_id = ?',$group_id)
      ->execute();
  }

  /**
   * mergeSynonyms
   * Merge two groups of synonyms for 2 items (or create groups if they doesn't exists)
   * I the group name is rename, and the group does not exists, set the record id 2 as basionym (current name)
   * @param string $table the name of the referenced relation
   * @param int $record_id_1 id of the first referenced record
   * @param int $record_id_2 id of the second referenced record
   * @param string $group_name the type of the group (synonym, homonym,...)
  */
  public function mergeSynonyms($table, $record_id_1, $record_id_2, $group_name)
  {
    //Get id For the element to be linked
    $ref_group_id_1 = $this->findGroupIdFor($table, $record_id_1, $group_name);

    //Get id For this element 
    $ref_group_id_2 = $this->findGroupIdFor($table, $record_id_2, $group_name);

    if($ref_group_id_1 == 0 || $ref_group_id_2 == 0)
    {
      $c1 = new ClassificationSynonymies();
      $c1->setReferencedRelation($table);
      $c1->setGroupName($group_name);
      $c1->setRecordId($record_id_2);

      if($ref_group_id_1 == 0 && $ref_group_id_2 == 0) //If there is no group
      {
        $c1->setGroupId( $this->findNextGroupId());

        $c2 = clone $c1;
        $c2->setRecordId($record_id_1);
        if($group_name =='rename')
        {
          $c2->setIsBasionym(true);
        }
        $c2->save();
      }
      elseif($ref_group_id_1 == 0)
      {
        $c1->setRecordId($record_id_1);
        $c1->setGroupId($ref_group_id_2);
      }
      else
      {
        $c1->setGroupId( $ref_group_id_1 );
      }
      $c1->save();
    }
    else // There is 2 existing groups... let's merge them
    {
      $this->mergeGroup($ref_group_id_1, $ref_group_id_2);
    }
  }

  /** 
   * mergeGroup
   * Merge groups of two synonyms
   * @param int $group1 id of the first group 
   * @param int $group2 id of the second group
  */
  protected function mergeGroup($group1, $group2)
  {
    $q = Doctrine_Query::create()
      ->update('ClassificationSynonymies s')
      ->set('s.group_id', '?', $group1)
      ->where('s.group_id = ?', $group2);
    $q->execute();
    //Check if 2 basionym
    $q = Doctrine_Query::create()
      ->select("COUNT(s.id) num_ids")
      ->from('ClassificationSynonymies s')
      ->andWhere('s.group_id = ?', $group1)
      ->andWhere('s.is_basionym = ?',true);

    $res = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);

    // Set No Basionym If more than 1
    if($res > 1)
    {
      $this->resetBasionym($group1);
    }
  }

  public function countRecordInGroup($group_id)
  {
    $q = Doctrine_Query::create()
      ->from('ClassificationSynonymies s')
      ->andWhere('s.group_id = ?', $group_id);
    return $q->count();
  }

  public function findBasionymIdForGroupId($id)
  {
    $q = Doctrine_Query::create()
      ->from('ClassificationSynonymies s')
      ->andWhere('s.group_id = ?', $id)
      ->andWhere('s.is_basionym = ?',true);
    $classif = $q->fetchOne();
    if($classif)
      return $classif->getRecordId();
    else return 0;
  }
}
