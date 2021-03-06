<?php

/**
 * BasePeopleSubTypes
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $people_ref
 * @property string $sub_type
 * @property People $People
 * 
 * @method integer        getId()         Returns the current record's "id" value
 * @method integer        getPeopleRef()  Returns the current record's "people_ref" value
 * @method string         getSubType()    Returns the current record's "sub_type" value
 * @method People         getPeople()     Returns the current record's "People" value
 * @method PeopleSubTypes setId()         Sets the current record's "id" value
 * @method PeopleSubTypes setPeopleRef()  Sets the current record's "people_ref" value
 * @method PeopleSubTypes setSubType()    Sets the current record's "sub_type" value
 * @method PeopleSubTypes setPeople()     Sets the current record's "People" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePeopleSubTypes extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('people_sub_types');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('people_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('sub_type', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('People', array(
             'local' => 'people_ref',
             'foreign' => 'id'));
    }
}