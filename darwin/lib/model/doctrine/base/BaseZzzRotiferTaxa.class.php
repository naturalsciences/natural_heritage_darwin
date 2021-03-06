<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ZzzRotiferTaxa', 'doctrine');

/**
 * BaseZzzRotiferTaxa
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $name_indexed
 * @property integer $level_ref
 * @property string $status
 * @property boolean $local_naming
 * @property string $color
 * @property string $path
 * @property integer $parent_ref
 * @property boolean $extinct
 * 
 * @method integer        getId()           Returns the current record's "id" value
 * @method string         getName()         Returns the current record's "name" value
 * @method string         getNameIndexed()  Returns the current record's "name_indexed" value
 * @method integer        getLevelRef()     Returns the current record's "level_ref" value
 * @method string         getStatus()       Returns the current record's "status" value
 * @method boolean        getLocalNaming()  Returns the current record's "local_naming" value
 * @method string         getColor()        Returns the current record's "color" value
 * @method string         getPath()         Returns the current record's "path" value
 * @method integer        getParentRef()    Returns the current record's "parent_ref" value
 * @method boolean        getExtinct()      Returns the current record's "extinct" value
 * @method ZzzRotiferTaxa setId()           Sets the current record's "id" value
 * @method ZzzRotiferTaxa setName()         Sets the current record's "name" value
 * @method ZzzRotiferTaxa setNameIndexed()  Sets the current record's "name_indexed" value
 * @method ZzzRotiferTaxa setLevelRef()     Sets the current record's "level_ref" value
 * @method ZzzRotiferTaxa setStatus()       Sets the current record's "status" value
 * @method ZzzRotiferTaxa setLocalNaming()  Sets the current record's "local_naming" value
 * @method ZzzRotiferTaxa setColor()        Sets the current record's "color" value
 * @method ZzzRotiferTaxa setPath()         Sets the current record's "path" value
 * @method ZzzRotiferTaxa setParentRef()    Sets the current record's "parent_ref" value
 * @method ZzzRotiferTaxa setExtinct()      Sets the current record's "extinct" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseZzzRotiferTaxa extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('zzz_rotifer_taxa');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('name_indexed', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('level_ref', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('status', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('local_naming', 'boolean', 1, array(
             'type' => 'boolean',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 1,
             ));
        $this->hasColumn('color', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('path', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('parent_ref', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('extinct', 'boolean', 1, array(
             'type' => 'boolean',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 1,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}