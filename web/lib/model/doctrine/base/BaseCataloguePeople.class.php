<?php

/**
 * BaseCataloguePeople
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $referenced_relation
 * @property integer $record_id
 * @property string $people_type
 * @property string $people_sub_type
 * @property integer $order_by
 * @property integer $people_ref
 * @property People $People
 * 
 * @method integer         getId()                  Returns the current record's "id" value
 * @method string          getReferencedRelation()  Returns the current record's "referenced_relation" value
 * @method integer         getRecordId()            Returns the current record's "record_id" value
 * @method string          getPeopleType()          Returns the current record's "people_type" value
 * @method string          getPeopleSubType()       Returns the current record's "people_sub_type" value
 * @method integer         getOrderBy()             Returns the current record's "order_by" value
 * @method integer         getPeopleRef()           Returns the current record's "people_ref" value
 * @method People          getPeople()              Returns the current record's "People" value
 * @method CataloguePeople setId()                  Sets the current record's "id" value
 * @method CataloguePeople setReferencedRelation()  Sets the current record's "referenced_relation" value
 * @method CataloguePeople setRecordId()            Sets the current record's "record_id" value
 * @method CataloguePeople setPeopleType()          Sets the current record's "people_type" value
 * @method CataloguePeople setPeopleSubType()       Sets the current record's "people_sub_type" value
 * @method CataloguePeople setOrderBy()             Sets the current record's "order_by" value
 * @method CataloguePeople setPeopleRef()           Sets the current record's "people_ref" value
 * @method CataloguePeople setPeople()              Sets the current record's "People" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCataloguePeople extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('catalogue_people');
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
        $this->hasColumn('people_type', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => 'author',
             ));
        $this->hasColumn('people_sub_type', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('order_by', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 1,
             ));
        $this->hasColumn('people_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));

        $this->setSubClasses(array(
             'LoanActors' => 
             array(
              'referenced_relation' => 'loans',
             ),
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