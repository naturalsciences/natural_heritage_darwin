<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CodesTable extends DarwinTable
{
  /**
  * Get Distincts prefix separators
  * @return array an Array of types in keys
  */
  public function getDistinctPrefixSep()
  {
    return $this->createFlatDistinct('codes', 'code_prefix_separator', 'code_prefix_separator')->execute();
  }

  /**
  * Get Distincts suffix separators
  * @return array an Array of types in keys
  */
  public function getDistinctSuffixSep()
  {
    return $this->createFlatDistinct('codes', 'code_suffix_separator', 'code_suffix_separator')->execute();
  }

  //change ftheeten added possibility to modify order by
  public function getCodesRelated($table='specimens', $specId = null, $p_order_by='referenced_relation, record_id, code_category ASC, code_date DESC, full_code_indexed ASC')
  {
	return $this->getCodesRelatedArray($table, $specId, $p_order_by);
  }
  /**
  * Get all codes related to an Array of id
  * @param string $table Name of the table referenced
  * @param array $specIds Array of id of related record
  * @return Doctrine_Collection Collection of codes
  */
  public function getCodesRelatedArray($table='specimens', $specIds = array())
  {
    if(!is_array($specIds))
      $specIds = array($specIds);
	if(empty($specIds)) return array();
    $q = Doctrine_Query::create()->
         from('Codes')->
         where('referenced_relation = ?', $table)->
         andWhereIn('record_id', $specIds)->
         orderBy('referenced_relation, record_id, code_category ASC, code_date DESC, full_code_indexed ASC');
    return $q->execute();
  }

/**
   * Get all codes related to an Array of id
   * @param string $table Name of the table referenced
   * @param array $specIds Array of id of related record
   * @return Doctrine_Collection Collection of codes
   */
  public function getMainCodesRelatedArray($table='specimens', $specIds = array())
  {
    if(!is_array($specIds))
      $specIds = array($specIds);
    if(empty($specIds)) return array();
    $q = Doctrine_Query::create()->
          from('Codes')->
          where('referenced_relation = ?', $table)->
          andWhereIn('record_id', $specIds)->
          andWhere('code_category = ?', 'main')->
          orderBy('referenced_relation, record_id, code_category ASC, code_date DESC, full_code_indexed ASC');
    return $q->execute();
  }
  
  /**
  * Get all codes related to an Array of id
  * @param string $table Name of the table referenced
  * @param array $specIds Array of id of related record
  * @return Doctrine_Collection Collection of codes
  */
  public function getCodesRelatedMultiple($table='specimens', $itemIds = array())
  {
    if(!is_array($itemIds))
      $specIds = array($itemIds);
        if(empty($itemIds)) return array();
    $q = Doctrine_Query::create()->
      select("record_id, code_category, concat( concat(COALESCE(code_prefix,''), COALESCE(code_prefix_separator,''),  COALESCE(code,'') ), 
        COALESCE(code_suffix_separator,''), COALESCE(code_suffix,'')) as full_code")->
      from('Codes')->
      where('referenced_relation = ?', $table)->
      andWhere('record_id = any(ARRAY['.implode(',',$itemIds).'])')->
      orderBy('code_category ASC,  full_code_indexed ASC');
    return $q->execute();
  }
}