<?php

/**
 * BaseCollectionsRights
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $collection_ref
 * @property integer $user_ref
 * @property integer $db_user_type
 * @property Collections $Collections
 * @property Users $Users
 * @property Doctrine_Collection $SpecimensStoragePartsView
 * 
 * @method integer             getId()                        Returns the current record's "id" value
 * @method integer             getCollectionRef()             Returns the current record's "collection_ref" value
 * @method integer             getUserRef()                   Returns the current record's "user_ref" value
 * @method integer             getDbUserType()                Returns the current record's "db_user_type" value
 * @method Collections         getCollections()               Returns the current record's "Collections" value
 * @method Users               getUsers()                     Returns the current record's "Users" value
 * @method Doctrine_Collection getSpecimensStoragePartsView() Returns the current record's "SpecimensStoragePartsView" collection
 * @method CollectionsRights   setId()                        Sets the current record's "id" value
 * @method CollectionsRights   setCollectionRef()             Sets the current record's "collection_ref" value
 * @method CollectionsRights   setUserRef()                   Sets the current record's "user_ref" value
 * @method CollectionsRights   setDbUserType()                Sets the current record's "db_user_type" value
 * @method CollectionsRights   setCollections()               Sets the current record's "Collections" value
 * @method CollectionsRights   setUsers()                     Sets the current record's "Users" value
 * @method CollectionsRights   setSpecimensStoragePartsView() Sets the current record's "SpecimensStoragePartsView" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCollectionsRights extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('collections_rights');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('collection_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('user_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('db_user_type', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 1,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Collections', array(
             'local' => 'collection_ref',
             'foreign' => 'id'));

        $this->hasOne('Users', array(
             'local' => 'user_ref',
             'foreign' => 'id'));

        $this->hasMany('SpecimensStoragePartsView', array(
             'local' => 'collection_ref',
             'foreign' => 'collection_ref'));
    }
}