<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class InsurancesTable extends DarwinTable
{
  /**
  * Find an insurance value
  * for a given table and record id
  * @param string $table_name db table name
  * @param int $record_id id of the record
  * @return a Doctrine_collection of results
  */
  public function findForTable($table_name, $record_id)
  {
    $q = Doctrine_Query::create()
	 ->from('Insurances i')
         ->leftJoin('i.People p')
	 ->orderBy('i.date_from DESC');
    $q = $this->addCatalogueReferences($q, $table_name, $record_id, 'i', true);
    return $q->execute();
  }

  /**
  * Get Distincts Currencies
  * @return array an Array of currencies in keys
  */
  public function getDistinctCurrencies()
  {
    return $this->createFlatDistinct('insurances', 'insurance_currency', 'currencies')->execute();
  }

  public function getInsurancesRelated($table='specimen_parts', $partId = null)
  {
	return $this->getInsurancesRelatedArray($table, $partId);
  }

  /**
  * Get all insurances related to an Array of id
  * @param string $table Name of the table referenced
  * @param array $partIds Array of id of related record
  * @return Doctrine_Collection Collection of insurances
  */
  public function getInsurancesRelatedArray($table='specimen_parts', $partIds = array())
  {
    if(!is_array($partIds))
      $partIds = array($partIds);
	if(empty($partIds)) return array();
    $q = Doctrine_Query::create()->
         from('Insurances')->
         where('referenced_relation = ?', $table)->
         andWhereIn('record_id', $partIds)->
         orderBy('record_id, date_from ASC');
    return $q->execute();
  }

}