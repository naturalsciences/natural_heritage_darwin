<?php

/**
 * BaseIdentifications
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $referenced_relation
 * @property integer $record_id
 * @property string $notion_concerned
 * @property string $notion_date
 * @property integer $notion_date_mask
 * @property string $value_defined
 * @property string $value_defined_indexed
 * @property string $determination_status
 * @property integer $order_by
 * 
 * @method integer         getId()                    Returns the current record's "id" value
 * @method string          getReferencedRelation()    Returns the current record's "referenced_relation" value
 * @method integer         getRecordId()              Returns the current record's "record_id" value
 * @method string          getNotionConcerned()       Returns the current record's "notion_concerned" value
 * @method string          getNotionDate()            Returns the current record's "notion_date" value
 * @method integer         getNotionDateMask()        Returns the current record's "notion_date_mask" value
 * @method string          getValueDefined()          Returns the current record's "value_defined" value
 * @method string          getValueDefinedIndexed()   Returns the current record's "value_defined_indexed" value
 * @method string          getDeterminationStatus()   Returns the current record's "determination_status" value
 * @method integer         getOrderBy()               Returns the current record's "order_by" value
 * @method Identifications setId()                    Sets the current record's "id" value
 * @method Identifications setReferencedRelation()    Sets the current record's "referenced_relation" value
 * @method Identifications setRecordId()              Sets the current record's "record_id" value
 * @method Identifications setNotionConcerned()       Sets the current record's "notion_concerned" value
 * @method Identifications setNotionDate()            Sets the current record's "notion_date" value
 * @method Identifications setNotionDateMask()        Sets the current record's "notion_date_mask" value
 * @method Identifications setValueDefined()          Sets the current record's "value_defined" value
 * @method Identifications setValueDefinedIndexed()   Sets the current record's "value_defined_indexed" value
 * @method Identifications setDeterminationStatus()   Sets the current record's "determination_status" value
 * @method Identifications setOrderBy()               Sets the current record's "order_by" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseIdentifications extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('identifications');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('referenced_relation', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('record_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('notion_concerned', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => 'taxonomy',
             ));
        $this->hasColumn('notion_date', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '0001-01-01',
             ));
        $this->hasColumn('notion_date_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('value_defined', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('value_defined_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('determination_status', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('order_by', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 1,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}