<?php

/**
 * BaseSpecimensTools
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $specimen_ref
 * @property integer $collecting_tool_ref
 * @property Specimens $Specimens
 * @property CollectingTools $CollectingTools
 * 
 * @method integer         getId()                  Returns the current record's "id" value
 * @method integer         getSpecimenRef()         Returns the current record's "specimen_ref" value
 * @method integer         getCollectingToolRef()   Returns the current record's "collecting_tool_ref" value
 * @method Specimens       getSpecimens()           Returns the current record's "Specimens" value
 * @method CollectingTools getCollectingTools()     Returns the current record's "CollectingTools" value
 * @method SpecimensTools  setId()                  Sets the current record's "id" value
 * @method SpecimensTools  setSpecimenRef()         Sets the current record's "specimen_ref" value
 * @method SpecimensTools  setCollectingToolRef()   Sets the current record's "collecting_tool_ref" value
 * @method SpecimensTools  setSpecimens()           Sets the current record's "Specimens" value
 * @method SpecimensTools  setCollectingTools()     Sets the current record's "CollectingTools" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSpecimensTools extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('specimen_collecting_tools');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('specimen_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('collecting_tool_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Specimens', array(
             'local' => 'specimen_ref',
             'foreign' => 'id'));

        $this->hasOne('CollectingTools', array(
             'local' => 'collecting_tool_ref',
             'foreign' => 'id'));
    }
}