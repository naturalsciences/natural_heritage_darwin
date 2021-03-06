<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('TvReportingCountAllSpecimensTypeByCollectionRefYearIg', 'doctrine');

/**
 * BaseTvReportingCountAllSpecimensTypeByCollectionRefYearIg
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $collection_path
 * @property string $collection_name
 * @property integer $collection_ref
 * @property integer $ig_ref
 * @property string $ig_num
 * @property float $year
 * @property timestamp $specimen_creation_date
 * @property string $type
 * @property integer $nb_records
 * @property integer $specimen_count_min
 * @property integer $specimen_count_max
 * 
 * @method integer                                               getId()                     Returns the current record's "id" value
 * @method string                                                getCollectionPath()         Returns the current record's "collection_path" value
 * @method string                                                getCollectionName()         Returns the current record's "collection_name" value
 * @method integer                                               getCollectionRef()          Returns the current record's "collection_ref" value
 * @method integer                                               getIgRef()                  Returns the current record's "ig_ref" value
 * @method string                                                getIgNum()                  Returns the current record's "ig_num" value
 * @method float                                                 getYear()                   Returns the current record's "year" value
 * @method timestamp                                             getSpecimenCreationDate()   Returns the current record's "specimen_creation_date" value
 * @method string                                                getType()                   Returns the current record's "type" value
 * @method integer                                               getNbRecords()              Returns the current record's "nb_records" value
 * @method integer                                               getSpecimenCountMin()       Returns the current record's "specimen_count_min" value
 * @method integer                                               getSpecimenCountMax()       Returns the current record's "specimen_count_max" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setId()                     Sets the current record's "id" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setCollectionPath()         Sets the current record's "collection_path" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setCollectionName()         Sets the current record's "collection_name" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setCollectionRef()          Sets the current record's "collection_ref" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setIgRef()                  Sets the current record's "ig_ref" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setIgNum()                  Sets the current record's "ig_num" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setYear()                   Sets the current record's "year" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setSpecimenCreationDate()   Sets the current record's "specimen_creation_date" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setType()                   Sets the current record's "type" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setNbRecords()              Sets the current record's "nb_records" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setSpecimenCountMin()       Sets the current record's "specimen_count_min" value
 * @method TvReportingCountAllSpecimensTypeByCollectionRefYearIg setSpecimenCountMax()       Sets the current record's "specimen_count_max" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTvReportingCountAllSpecimensTypeByCollectionRefYearIg extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('tv_reporting_count_all_specimens_type_by_collection_ref_year_ig');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'autoincrement' => true,
             'primary' => true,
             'length' => 8,
             ));
        $this->hasColumn('collection_path', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('collection_name', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('collection_ref', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('ig_ref', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('ig_num', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('year', 'float', null, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('specimen_creation_date', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 25,
             ));
        $this->hasColumn('type', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('nb_records', 'integer', 8, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 8,
             ));
        $this->hasColumn('specimen_count_min', 'integer', 8, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 8,
             ));
        $this->hasColumn('specimen_count_max', 'integer', 8, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 8,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}