<?php

/**
 * BaseUsersTracking
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $referenced_relation
 * @property integer $record_id
 * @property integer $user_ref
 * @property string $action
 * @property string $modification_date_time
 * @property Users $Users
 * 
 * @method integer       getId()                     Returns the current record's "id" value
 * @method string        getReferencedRelation()     Returns the current record's "referenced_relation" value
 * @method integer       getRecordId()               Returns the current record's "record_id" value
 * @method integer       getUserRef()                Returns the current record's "user_ref" value
 * @method string        getAction()                 Returns the current record's "action" value
 * @method string        getModificationDateTime()   Returns the current record's "modification_date_time" value
 * @method Users         getUsers()                  Returns the current record's "Users" value
 * @method UsersTracking setId()                     Sets the current record's "id" value
 * @method UsersTracking setReferencedRelation()     Sets the current record's "referenced_relation" value
 * @method UsersTracking setRecordId()               Sets the current record's "record_id" value
 * @method UsersTracking setUserRef()                Sets the current record's "user_ref" value
 * @method UsersTracking setAction()                 Sets the current record's "action" value
 * @method UsersTracking setModificationDateTime()   Sets the current record's "modification_date_time" value
 * @method UsersTracking setUsers()                  Sets the current record's "Users" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseUsersTracking extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('users_tracking');
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
        $this->hasColumn('user_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('action', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => 'insert',
             ));
        $this->hasColumn('modification_date_time', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Users', array(
             'local' => 'user_ref',
             'foreign' => 'id'));
    }
}