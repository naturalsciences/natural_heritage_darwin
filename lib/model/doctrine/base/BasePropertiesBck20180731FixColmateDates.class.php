<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('PropertiesBck20180731FixColmateDates', 'doctrine');

/**
 * BasePropertiesBck20180731FixColmateDates
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $referenced_relation
 * @property integer $record_id
 * @property string $property_type
 * @property string $applies_to
 * @property string $applies_to_indexed
 * @property integer $date_from_mask
 * @property timestamp $date_from
 * @property integer $date_to_mask
 * @property timestamp $date_to
 * @property boolean $is_quantitative
 * @property string $property_unit
 * @property string $method
 * @property string $method_indexed
 * @property string $lower_value
 * @property float $lower_value_unified
 * @property string $upper_value
 * @property float $upper_value_unified
 * @property string $property_accuracy
 * 
 * @method integer                              getId()                  Returns the current record's "id" value
 * @method string                               getReferencedRelation()  Returns the current record's "referenced_relation" value
 * @method integer                              getRecordId()            Returns the current record's "record_id" value
 * @method string                               getPropertyType()        Returns the current record's "property_type" value
 * @method string                               getAppliesTo()           Returns the current record's "applies_to" value
 * @method string                               getAppliesToIndexed()    Returns the current record's "applies_to_indexed" value
 * @method integer                              getDateFromMask()        Returns the current record's "date_from_mask" value
 * @method timestamp                            getDateFrom()            Returns the current record's "date_from" value
 * @method integer                              getDateToMask()          Returns the current record's "date_to_mask" value
 * @method timestamp                            getDateTo()              Returns the current record's "date_to" value
 * @method boolean                              getIsQuantitative()      Returns the current record's "is_quantitative" value
 * @method string                               getPropertyUnit()        Returns the current record's "property_unit" value
 * @method string                               getMethod()              Returns the current record's "method" value
 * @method string                               getMethodIndexed()       Returns the current record's "method_indexed" value
 * @method string                               getLowerValue()          Returns the current record's "lower_value" value
 * @method float                                getLowerValueUnified()   Returns the current record's "lower_value_unified" value
 * @method string                               getUpperValue()          Returns the current record's "upper_value" value
 * @method float                                getUpperValueUnified()   Returns the current record's "upper_value_unified" value
 * @method string                               getPropertyAccuracy()    Returns the current record's "property_accuracy" value
 * @method PropertiesBck20180731FixColmateDates setId()                  Sets the current record's "id" value
 * @method PropertiesBck20180731FixColmateDates setReferencedRelation()  Sets the current record's "referenced_relation" value
 * @method PropertiesBck20180731FixColmateDates setRecordId()            Sets the current record's "record_id" value
 * @method PropertiesBck20180731FixColmateDates setPropertyType()        Sets the current record's "property_type" value
 * @method PropertiesBck20180731FixColmateDates setAppliesTo()           Sets the current record's "applies_to" value
 * @method PropertiesBck20180731FixColmateDates setAppliesToIndexed()    Sets the current record's "applies_to_indexed" value
 * @method PropertiesBck20180731FixColmateDates setDateFromMask()        Sets the current record's "date_from_mask" value
 * @method PropertiesBck20180731FixColmateDates setDateFrom()            Sets the current record's "date_from" value
 * @method PropertiesBck20180731FixColmateDates setDateToMask()          Sets the current record's "date_to_mask" value
 * @method PropertiesBck20180731FixColmateDates setDateTo()              Sets the current record's "date_to" value
 * @method PropertiesBck20180731FixColmateDates setIsQuantitative()      Sets the current record's "is_quantitative" value
 * @method PropertiesBck20180731FixColmateDates setPropertyUnit()        Sets the current record's "property_unit" value
 * @method PropertiesBck20180731FixColmateDates setMethod()              Sets the current record's "method" value
 * @method PropertiesBck20180731FixColmateDates setMethodIndexed()       Sets the current record's "method_indexed" value
 * @method PropertiesBck20180731FixColmateDates setLowerValue()          Sets the current record's "lower_value" value
 * @method PropertiesBck20180731FixColmateDates setLowerValueUnified()   Sets the current record's "lower_value_unified" value
 * @method PropertiesBck20180731FixColmateDates setUpperValue()          Sets the current record's "upper_value" value
 * @method PropertiesBck20180731FixColmateDates setUpperValueUnified()   Sets the current record's "upper_value_unified" value
 * @method PropertiesBck20180731FixColmateDates setPropertyAccuracy()    Sets the current record's "property_accuracy" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePropertiesBck20180731FixColmateDates extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('properties_bck20180731_fix_colmate_dates');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('referenced_relation', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('record_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('property_type', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('applies_to', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('applies_to_indexed', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('date_from_mask', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('date_from', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 25,
             ));
        $this->hasColumn('date_to_mask', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('date_to', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 25,
             ));
        $this->hasColumn('is_quantitative', 'boolean', 1, array(
             'type' => 'boolean',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 1,
             ));
        $this->hasColumn('property_unit', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('method', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('method_indexed', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('lower_value', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('lower_value_unified', 'float', null, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('upper_value', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('upper_value_unified', 'float', null, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('property_accuracy', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}