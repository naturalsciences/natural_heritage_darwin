<?php

/**
 * StagingPropertiesTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class StagingPropertiesTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return StagingPropertiesTable The table instance
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('StagingProperties');
    }
	
	
    public function getByImportRef($import_ref, $offset="0", $limit="1000")
    {
        
		$conn_MGR = Doctrine_Manager::connection();
		$conn = $conn_MGR->getDbh();
			
			
		$params[':import_ref'] = $import_ref;
		$params[':offset'] = $offset;
	    $params[':limit'] = $limit;
		$sql =" SELECT *, status::varchar as status_str FROM staging_properties WHERE import_ref=:import_ref ORDER BY id OFFSET :offset LIMIT :limit;";
		$statement = $conn->prepare($sql);
		$statement->execute($params);
		$items = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    }
	
	public function countExceptionMessages($import_ref, $offset="0", $limit="1000")
	{
		$conn_MGR = Doctrine_Manager::connection();
		$conn = $conn_MGR->getDbh();
			
			
		$params[':import_ref'] = $import_ref;
		$params[':import_ref_2'] = $import_ref;
		$params[':offset'] = $offset;
	    $params[':limit'] = $limit;
		$sql =" SELECT count(*) as count, import_exception, (SELECT count(*) FROM staging_properties WHERE import_ref=:import_ref_2   ) as count_all FROM (SELECT *, status::varchar as import_exception  FROM staging_properties WHERE import_ref=:import_ref ORDER BY id  OFFSET :offset LIMIT :limit) a  GROUP BY import_exception ORDER BY import_exception  ;";
		$statement = $conn->prepare($sql);
		$statement->execute($params);
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $results;
	}
}