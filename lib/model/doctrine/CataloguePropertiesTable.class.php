<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CataloguePropertiesTable extends DarwinTable
{
  /**
  * Find a property (joined with values)
  * for a given table and record id
  * @param string $table_name db table name
  * @param int $record_id id of the record
  * @return a Doctrine_collection of results
  */
  public function findForTable($table_name, $record_id)
  {
     $q = Doctrine_Query::create()
	 ->from('CatalogueProperties p')
	 ->leftJoin('p.PropertiesValues v')
	 ->andWhere('p.referenced_relation = ?',$table_name)
         ->andWhere('p.record_id = ?',$record_id)
	 ->orderBy('p.property_type ASC');
    return $q->execute();
  }

  /**
  * Get Distincts type of properties
  * @return array an Array of types in keys
  */
  public function getDistinctType()
  {
    $results = Doctrine_Query::create()->
      select('DISTINCT(property_type) as type')->
      from('CatalogueProperties')->
      execute();
    return $results;
  }

  /**
  * Get Distincts Sub Type of properties
  * filter by type if one given
  * @param string $type a type
  * @return array an Array of sub-types in keys/values
  */
  public function getDistinctSubType($type=null)
  {
    $q = Doctrine_Query::create()->
      select('DISTINCT(property_sub_type) as sub_type')->
      from('CatalogueProperties INDEXBY sub_type');

    if(! is_null($type))
      $q->addWhere('property_type = ?',$type);
    $results = $q->fetchArray();
    if(count($results))
      $results = array_combine(array_keys($results),array_keys($results));
    return array_merge(array(''=>''), $results);
  }

  /**
  * Get Distincts Qualifier of properties
  * filter by sub type if one given
  * @param string $sub_type a type
  * @return array an Array of Qualifier in keys/values
  */
  public function getDistinctQualifier($sub_type=null)
  {
    $q = Doctrine_Query::create()->
      select('DISTINCT(property_qualifier) as qualifier')->
      from('CatalogueProperties');

    if(! is_null($sub_type))
      $q->addWhere('property_sub_type = ?',$sub_type);
    $results = $q->fetchArray();
    $rez=array(''=>''); //@TODO: don't know why but doctrine doesnt like it otherwise
    foreach($results as $item)
      $rez[$item['qualifier']]=$item['qualifier'];
    return $rez;
  }
  
  /**
  * Get Distincts units (accuracy + normal) of properties
  * filter by type if one given
  * @param string $type a type
  * @return array an Array of Qualifier in keys/values
  */
  public function getDistinctUnit($type=null)
  {
    $q = Doctrine_Query::create()->
      select('DISTINCT(property_unit) as unit')->
      from('CatalogueProperties INDEXBY unit');

    if(! is_null($type))
      $q->addWhere('property_type = ?',$type);
    $results_unit = $q->fetchArray();

    $q = Doctrine_Query::create()->
      select('DISTINCT(property_accuracy_unit) as unit')->
      from('CatalogueProperties INDEXBY unit');

    if(! is_null($type))
      $q->addWhere('property_type = ?',$type);
    $results_accuracy = $q->fetchArray();
    $results = array_merge($results_unit, $results_accuracy);
  
    if(count($results))
      $results = array_combine(array_keys($results),array_keys($results));
    return array_merge(array(''=>'unit'), $results);
  }
}