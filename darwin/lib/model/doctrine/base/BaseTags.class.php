<?php

/**
 * BaseTags
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $gtu_ref
 * @property integer $group_ref
 * @property string $tag
 * @property string $group_type
 * @property string $sub_group_type
 * @property string $tag_indexed
 * @property TagGroups $TagGroups
 * @property Gtu $Gtu
 * @property Doctrine_Collection $Specimens
 * 
 * @method integer             getGtuRef()         Returns the current record's "gtu_ref" value
 * @method integer             getGroupRef()       Returns the current record's "group_ref" value
 * @method string              getTag()            Returns the current record's "tag" value
 * @method string              getGroupType()      Returns the current record's "group_type" value
 * @method string              getSubGroupType()   Returns the current record's "sub_group_type" value
 * @method string              getTagIndexed()     Returns the current record's "tag_indexed" value
 * @method TagGroups           getTagGroups()      Returns the current record's "TagGroups" value
 * @method Gtu                 getGtu()            Returns the current record's "Gtu" value
 * @method Doctrine_Collection getSpecimens()      Returns the current record's "Specimens" collection
 * @method Tags                setGtuRef()         Sets the current record's "gtu_ref" value
 * @method Tags                setGroupRef()       Sets the current record's "group_ref" value
 * @method Tags                setTag()            Sets the current record's "tag" value
 * @method Tags                setGroupType()      Sets the current record's "group_type" value
 * @method Tags                setSubGroupType()   Sets the current record's "sub_group_type" value
 * @method Tags                setTagIndexed()     Sets the current record's "tag_indexed" value
 * @method Tags                setTagGroups()      Sets the current record's "TagGroups" value
 * @method Tags                setGtu()            Sets the current record's "Gtu" value
 * @method Tags                setSpecimens()      Sets the current record's "Specimens" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTags extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('tags');
        $this->hasColumn('gtu_ref', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('group_ref', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('tag', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('group_type', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('sub_group_type', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('tag_indexed', 'string', null, array(
             'type' => 'string',
             'primary' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('TagGroups', array(
             'local' => 'group_ref',
             'foreign' => 'id'));

        $this->hasOne('Gtu', array(
             'local' => 'gtu_ref',
             'foreign' => 'id'));

        $this->hasMany('Specimens', array(
             'local' => 'gtu_ref',
             'foreign' => 'gtu_ref'));
    }
}