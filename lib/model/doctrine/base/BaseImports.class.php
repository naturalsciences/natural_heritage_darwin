<?php

/**
 * BaseImports
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $filename
 * @property integer $user_ref
 * @property string $format
 * @property integer $collection_ref
 * @property string $state
 * @property string $created_at
 * @property string $updated_at
 * @property integer $initial_count
 * @property boolean $is_finished
 * @property string $errors_in_import
 * @property Collections $Collections
 * @property Users $Users
 * @property Doctrine_Collection $Staging
 * 
 * @method integer             getId()               Returns the current record's "id" value
 * @method string              getFilename()         Returns the current record's "filename" value
 * @method integer             getUserRef()          Returns the current record's "user_ref" value
 * @method string              getFormat()           Returns the current record's "format" value
 * @method integer             getCollectionRef()    Returns the current record's "collection_ref" value
 * @method string              getState()            Returns the current record's "state" value
 * @method string              getCreatedAt()        Returns the current record's "created_at" value
 * @method string              getUpdatedAt()        Returns the current record's "updated_at" value
 * @method integer             getInitialCount()     Returns the current record's "initial_count" value
 * @method boolean             getIsFinished()       Returns the current record's "is_finished" value
 * @method string              getErrorsInImport()   Returns the current record's "errors_in_import" value
 * @method Collections         getCollections()      Returns the current record's "Collections" value
 * @method Users               getUsers()            Returns the current record's "Users" value
 * @method Doctrine_Collection getStaging()          Returns the current record's "Staging" collection
 * @method Imports             setId()               Sets the current record's "id" value
 * @method Imports             setFilename()         Sets the current record's "filename" value
 * @method Imports             setUserRef()          Sets the current record's "user_ref" value
 * @method Imports             setFormat()           Sets the current record's "format" value
 * @method Imports             setCollectionRef()    Sets the current record's "collection_ref" value
 * @method Imports             setState()            Sets the current record's "state" value
 * @method Imports             setCreatedAt()        Sets the current record's "created_at" value
 * @method Imports             setUpdatedAt()        Sets the current record's "updated_at" value
 * @method Imports             setInitialCount()     Sets the current record's "initial_count" value
 * @method Imports             setIsFinished()       Sets the current record's "is_finished" value
 * @method Imports             setErrorsInImport()   Sets the current record's "errors_in_import" value
 * @method Imports             setCollections()      Sets the current record's "Collections" value
 * @method Imports             setUsers()            Sets the current record's "Users" value
 * @method Imports             setStaging()          Sets the current record's "Staging" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseImports extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('imports');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('filename', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('user_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('format', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('collection_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('state', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => 'to_be_loaded',
             ));
        $this->hasColumn('created_at', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('updated_at', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('initial_count', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('is_finished', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('errors_in_import', 'string', null, array(
             'type' => 'string',
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

        $this->hasMany('Staging', array(
             'local' => 'id',
             'foreign' => 'import_ref'));
    }
}