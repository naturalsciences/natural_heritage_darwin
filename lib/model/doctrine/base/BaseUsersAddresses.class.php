<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseUsersAddresses extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('users_addresses');
        $this->hasColumn('id', 'integer', null, array('type' => 'integer', 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('person_user_ref', 'integer', null, array('type' => 'integer', 'notnull' => true));
        $this->hasColumn('tag', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('organization_unit', 'string', null, array('type' => 'string'));
        $this->hasColumn('person_user_role', 'string', null, array('type' => 'string'));
        $this->hasColumn('activity_period', 'string', null, array('type' => 'string'));
        $this->hasColumn('po_box', 'string', null, array('type' => 'string'));
        $this->hasColumn('extended_address', 'string', null, array('type' => 'string'));
        $this->hasColumn('locality', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('region', 'string', null, array('type' => 'string'));
        $this->hasColumn('zip_code', 'string', null, array('type' => 'string'));
        $this->hasColumn('country', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('address_parts_ts', 'string', null, array('type' => 'string'));
    }

    public function setUp()
    {
        $this->hasOne('Users', array('local' => 'person_user_ref',
                                     'foreign' => 'id'));
    }
}