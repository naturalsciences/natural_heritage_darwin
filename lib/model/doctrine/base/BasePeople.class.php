<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BasePeople extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('people');
        $this->hasColumn('id', 'integer', null, array('type' => 'integer', 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('is_physical', 'boolean', null, array('type' => 'boolean', 'notnull' => true));
        $this->hasColumn('sub_type', 'string', null, array('type' => 'string'));
        $this->hasColumn('public_class', 'enum', null, array('type' => 'enum', 'values' => array(0 => 'public', 1 => 'private')));
        $this->hasColumn('formated_name', 'string', null, array('type' => 'string'));
        $this->hasColumn('formated_name_indexed', 'string', null, array('type' => 'string'));
        $this->hasColumn('formated_name_ts', 'string', null, array('type' => 'string'));
        $this->hasColumn('title', 'string', null, array('type' => 'string'));
        $this->hasColumn('family_name', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('given_name', 'string', null, array('type' => 'string'));
        $this->hasColumn('additional_names', 'string', null, array('type' => 'string'));
        $this->hasColumn('birth_date_mask', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('birth_date', 'date', null, array('type' => 'date', 'notnull' => true, 'default' => '0001-01-01'));
        $this->hasColumn('gender', 'enum', null, array('type' => 'enum', 'values' => array(0 => 'M', 1 => 'F')));
        $this->hasColumn('db_people_type', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => '1'));
        $this->hasColumn('end_date_mask', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('end_date', 'date', null, array('type' => 'date', 'notnull' => true, 'default' => '0001-01-01'));
    }

    public function setUp()
    {
        $this->hasMany('CataloguePeople', array('local' => 'id',
                                                'foreign' => 'people_ref'));

        $this->hasMany('PeopleLanguages', array('local' => 'id',
                                                'foreign' => 'people_ref'));

        $this->hasMany('PeopleRelationships', array('local' => 'id',
                                                    'foreign' => 'person_1_ref'));

        $this->hasMany('PeopleComm', array('local' => 'id',
                                           'foreign' => 'person_user_ref'));

        $this->hasMany('PeopleAddresses', array('local' => 'id',
                                                'foreign' => 'person_user_ref'));

        $this->hasMany('PeopleMultimedia', array('local' => 'id',
                                                 'foreign' => 'person_user_ref'));

        $this->hasMany('Collections', array('local' => 'id',
                                            'foreign' => 'institution_ref'));

        $this->hasMany('PeopleAliases', array('local' => 'id',
                                              'foreign' => 'person_ref'));
    }
}