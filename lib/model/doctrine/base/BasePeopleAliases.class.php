<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BasePeopleAliases extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('people_aliases');
        $this->hasColumn('id', 'integer', null, array('type' => 'integer', 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('table_name', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('record_id', 'integer', null, array('type' => 'integer', 'notnull' => true));
        $this->hasColumn('person_ref', 'integer', null, array('type' => 'integer', 'notnull' => true));
        $this->hasColumn('collection_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('person_name', 'string', null, array('type' => 'string', 'notnull' => true));
    }

    public function setUp()
    {
        $this->hasOne('People', array('local' => 'person_ref',
                                      'foreign' => 'id'));

        $this->hasOne('Collections', array('local' => 'collection_ref',
                                           'foreign' => 'id'));
    }
}