<?php

/**
 * BasePeople
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property boolean $is_physical
 * @property string $sub_type
 * @property string $formated_name
 * @property string $formated_name_indexed
 * @property string $title
 * @property string $family_name
 * @property string $given_name
 * @property string $additional_names
 * @property integer $birth_date_mask
 * @property string $birth_date
 * @property enum $gender
 * @property integer $end_date_mask
 * @property string $end_date
 * @property string $activity_date_from
 * @property integer $activity_date_from_mask
 * @property string $activity_date_to
 * @property integer $activity_date_to_mask
 * @property Doctrine_Collection $CataloguePeople
 * @property Doctrine_Collection $Users
 * @property Doctrine_Collection $PeopleLanguages
 * @property Doctrine_Collection $PeopleRelationships
 * @property Doctrine_Collection $PeopleComm
 * @property Doctrine_Collection $PeopleAddresses
 * @property Doctrine_Collection $Collections
 * @property Doctrine_Collection $CollectionMaintenance
 * @property Doctrine_Collection $Insurances
 * @property Doctrine_Collection $StagingPeople
 * 
 * @method integer             getId()                      Returns the current record's "id" value
 * @method boolean             getIsPhysical()              Returns the current record's "is_physical" value
 * @method string              getSubType()                 Returns the current record's "sub_type" value
 * @method string              getFormatedName()            Returns the current record's "formated_name" value
 * @method string              getFormatedNameIndexed()     Returns the current record's "formated_name_indexed" value
 * @method string              getTitle()                   Returns the current record's "title" value
 * @method string              getFamilyName()              Returns the current record's "family_name" value
 * @method string              getGivenName()               Returns the current record's "given_name" value
 * @method string              getAdditionalNames()         Returns the current record's "additional_names" value
 * @method integer             getBirthDateMask()           Returns the current record's "birth_date_mask" value
 * @method string              getBirthDate()               Returns the current record's "birth_date" value
 * @method enum                getGender()                  Returns the current record's "gender" value
 * @method integer             getEndDateMask()             Returns the current record's "end_date_mask" value
 * @method string              getEndDate()                 Returns the current record's "end_date" value
 * @method string              getActivityDateFrom()        Returns the current record's "activity_date_from" value
 * @method integer             getActivityDateFromMask()    Returns the current record's "activity_date_from_mask" value
 * @method string              getActivityDateTo()          Returns the current record's "activity_date_to" value
 * @method integer             getActivityDateToMask()      Returns the current record's "activity_date_to_mask" value
 * @method Doctrine_Collection getCataloguePeople()         Returns the current record's "CataloguePeople" collection
 * @method Doctrine_Collection getUsers()                   Returns the current record's "Users" collection
 * @method Doctrine_Collection getPeopleLanguages()         Returns the current record's "PeopleLanguages" collection
 * @method Doctrine_Collection getPeopleRelationships()     Returns the current record's "PeopleRelationships" collection
 * @method Doctrine_Collection getPeopleComm()              Returns the current record's "PeopleComm" collection
 * @method Doctrine_Collection getPeopleAddresses()         Returns the current record's "PeopleAddresses" collection
 * @method Doctrine_Collection getCollections()             Returns the current record's "Collections" collection
 * @method Doctrine_Collection getCollectionMaintenance()   Returns the current record's "CollectionMaintenance" collection
 * @method Doctrine_Collection getInsurances()              Returns the current record's "Insurances" collection
 * @method Doctrine_Collection getStagingPeople()           Returns the current record's "StagingPeople" collection
 * @method People              setId()                      Sets the current record's "id" value
 * @method People              setIsPhysical()              Sets the current record's "is_physical" value
 * @method People              setSubType()                 Sets the current record's "sub_type" value
 * @method People              setFormatedName()            Sets the current record's "formated_name" value
 * @method People              setFormatedNameIndexed()     Sets the current record's "formated_name_indexed" value
 * @method People              setTitle()                   Sets the current record's "title" value
 * @method People              setFamilyName()              Sets the current record's "family_name" value
 * @method People              setGivenName()               Sets the current record's "given_name" value
 * @method People              setAdditionalNames()         Sets the current record's "additional_names" value
 * @method People              setBirthDateMask()           Sets the current record's "birth_date_mask" value
 * @method People              setBirthDate()               Sets the current record's "birth_date" value
 * @method People              setGender()                  Sets the current record's "gender" value
 * @method People              setEndDateMask()             Sets the current record's "end_date_mask" value
 * @method People              setEndDate()                 Sets the current record's "end_date" value
 * @method People              setActivityDateFrom()        Sets the current record's "activity_date_from" value
 * @method People              setActivityDateFromMask()    Sets the current record's "activity_date_from_mask" value
 * @method People              setActivityDateTo()          Sets the current record's "activity_date_to" value
 * @method People              setActivityDateToMask()      Sets the current record's "activity_date_to_mask" value
 * @method People              setCataloguePeople()         Sets the current record's "CataloguePeople" collection
 * @method People              setUsers()                   Sets the current record's "Users" collection
 * @method People              setPeopleLanguages()         Sets the current record's "PeopleLanguages" collection
 * @method People              setPeopleRelationships()     Sets the current record's "PeopleRelationships" collection
 * @method People              setPeopleComm()              Sets the current record's "PeopleComm" collection
 * @method People              setPeopleAddresses()         Sets the current record's "PeopleAddresses" collection
 * @method People              setCollections()             Sets the current record's "Collections" collection
 * @method People              setCollectionMaintenance()   Sets the current record's "CollectionMaintenance" collection
 * @method People              setInsurances()              Sets the current record's "Insurances" collection
 * @method People              setStagingPeople()           Sets the current record's "StagingPeople" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePeople extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('people');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('is_physical', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('sub_type', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('formated_name', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('formated_name_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('title', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('family_name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('given_name', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('additional_names', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('birth_date_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('birth_date', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '0001-01-01',
             ));
        $this->hasColumn('gender', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'M',
              1 => 'F',
             ),
             ));
        $this->hasColumn('end_date_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('end_date', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '2038-12-31',
             ));
        $this->hasColumn('activity_date_from', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '0001-01-01',
             ));
        $this->hasColumn('activity_date_from_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('activity_date_to', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '2038-12-31',
             ));
        $this->hasColumn('activity_date_to_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('CataloguePeople', array(
             'local' => 'id',
             'foreign' => 'people_ref'));

        $this->hasMany('Users', array(
             'local' => 'id',
             'foreign' => 'people_id'));

        $this->hasMany('PeopleLanguages', array(
             'local' => 'id',
             'foreign' => 'people_ref'));

        $this->hasMany('PeopleRelationships', array(
             'local' => 'id',
             'foreign' => 'person_2_ref'));

        $this->hasMany('PeopleComm', array(
             'local' => 'id',
             'foreign' => 'person_user_ref'));

        $this->hasMany('PeopleAddresses', array(
             'local' => 'id',
             'foreign' => 'person_user_ref'));

        $this->hasMany('Collections', array(
             'local' => 'id',
             'foreign' => 'institution_ref'));

        $this->hasMany('CollectionMaintenance', array(
             'local' => 'id',
             'foreign' => 'people_ref'));

        $this->hasMany('Insurances', array(
             'local' => 'id',
             'foreign' => 'insurer_ref'));

        $this->hasMany('StagingPeople', array(
             'local' => 'id',
             'foreign' => 'people_ref'));
    }
}
