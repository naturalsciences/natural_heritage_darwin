<?php

/**
 * BaseTagGroupsDistinct
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $sub_group_name_indexed
 * @property string $group_name_indexed
 * @property string $tag_value
 * 
 * @method integer           getId()                     Returns the current record's "id" value
 * @method string            getSubGroupNameIndexed()    Returns the current record's "sub_group_name_indexed" value
 * @method string            getGroupNameIndexed()       Returns the current record's "group_name_indexed" value
 * @method string            getTagValue()               Returns the current record's "tag_value" value
 * @method TagGroupsDistinct setId()                     Sets the current record's "id" value
 * @method TagGroupsDistinct setSubGroupNameIndexed()    Sets the current record's "sub_group_name_indexed" value
 * @method TagGroupsDistinct setGroupNameIndexed()       Sets the current record's "group_name_indexed" value
 * @method TagGroupsDistinct setTagValue()               Sets the current record's "tag_value" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTagGroupsDistinct extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('tag_groups_distinct');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('sub_group_name_indexed', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('group_name_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('tag_value', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}