<?php

/**
 * BaseLithostratigraphy
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
 * @property Lithostratigraphy $Parent
 * @property CatalogueLevels $Level
 * @property Doctrine_Collection $Lithostratigraphy
 * @property Doctrine_Collection $Specimens
 * @property Doctrine_Collection $SpecimensFlat
 * 
 * @method integer             getId()                Returns the current record's "id" value
 * @method string              getName()              Returns the current record's "name" value
 * @method string              getNameIndexed()       Returns the current record's "name_indexed" value
 * @method integer             getLevelRef()          Returns the current record's "level_ref" value
 * @method string              getStatus()            Returns the current record's "status" value
 * @method boolean             getLocalNaming()       Returns the current record's "local_naming" value
 * @method string              getColor()             Returns the current record's "color" value
 * @method string              getPath()              Returns the current record's "path" value
 * @method integer             getParentRef()         Returns the current record's "parent_ref" value
 * @method Lithostratigraphy   getParent()            Returns the current record's "Parent" value
 * @method CatalogueLevels     getLevel()             Returns the current record's "Level" value
 * @method Doctrine_Collection getLithostratigraphy() Returns the current record's "Lithostratigraphy" collection
 * @method Doctrine_Collection getSpecimens()         Returns the current record's "Specimens" collection
 * @method Doctrine_Collection getSpecimensFlat()     Returns the current record's "SpecimensFlat" collection
 * @method Lithostratigraphy   setId()                Sets the current record's "id" value
 * @method Lithostratigraphy   setName()              Sets the current record's "name" value
 * @method Lithostratigraphy   setNameIndexed()       Sets the current record's "name_indexed" value
 * @method Lithostratigraphy   setLevelRef()          Sets the current record's "level_ref" value
 * @method Lithostratigraphy   setStatus()            Sets the current record's "status" value
 * @method Lithostratigraphy   setLocalNaming()       Sets the current record's "local_naming" value
 * @method Lithostratigraphy   setColor()             Sets the current record's "color" value
 * @method Lithostratigraphy   setPath()              Sets the current record's "path" value
 * @method Lithostratigraphy   setParentRef()         Sets the current record's "parent_ref" value
 * @method Lithostratigraphy   setParent()            Sets the current record's "Parent" value
 * @method Lithostratigraphy   setLevel()             Sets the current record's "Level" value
 * @method Lithostratigraphy   setLithostratigraphy() Sets the current record's "Lithostratigraphy" collection
 * @method Lithostratigraphy   setSpecimens()         Sets the current record's "Specimens" collection
 * @method Lithostratigraphy   setSpecimensFlat()     Sets the current record's "SpecimensFlat" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLithostratigraphy extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('lithostratigraphy');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('name_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('level_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('status', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => 'valid',
             ));
        $this->hasColumn('local_naming', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('color', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('path', 'string', null, array(
             'type' => 'string',
             'notnull' => false,
             'default' => '/',
             ));
        $this->hasColumn('parent_ref', 'integer', null, array(
             'type' => 'integer',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Lithostratigraphy as Parent', array(
             'local' => 'parent_ref',
             'foreign' => 'id'));

        $this->hasOne('CatalogueLevels as Level', array(
             'local' => 'level_ref',
             'foreign' => 'id'));

        $this->hasMany('Lithostratigraphy', array(
             'local' => 'id',
             'foreign' => 'parent_ref'));

        $this->hasMany('Specimens', array(
             'local' => 'id',
             'foreign' => 'litho_ref'));

        $this->hasMany('SpecimensFlat', array(
             'local' => 'id',
             'foreign' => 'litho_ref'));
    }
}