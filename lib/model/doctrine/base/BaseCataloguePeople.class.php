<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseCataloguePeople extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('catalogue_people');
        $this->hasColumn('id', 'integer', null, array('type' => 'integer', 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('table_name', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('record_id', 'integer', null, array('type' => 'integer', 'notnull' => true));
        $this->hasColumn('people_type', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => 'authors'));
        $this->hasColumn('people_sub_type', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('order_by', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => '1'));
        $this->hasColumn('people_ref', 'integer', null, array('type' => 'integer', 'notnull' => true));
    }

    public function setUp()
    {
        $this->hasOne('People', array('local' => 'people_ref',
                                      'foreign' => 'id'));
    }
}