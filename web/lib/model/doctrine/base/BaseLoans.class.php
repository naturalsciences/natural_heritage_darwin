<?php

/**
 * BaseLoans
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $search_indexed
 * @property string $from_date
 * @property string $to_date
 * @property string $extended_to_date
 * @property integer $collection_ref
 * @property string $address_receiver
 * @property string $institution_receiver
 * @property string $country_receiver
 * @property string $city_receiver
 * @property string $zip_receiver
 * @property Doctrine_Collection $CataloguePeople
 * @property Collections $Collections
 * @property Doctrine_Collection $LoanItems
 * @property Doctrine_Collection $LoanRights
 * @property Doctrine_Collection $LoanStatus
 * 
 * @method integer             getId()                   Returns the current record's "id" value
 * @method string              getName()                 Returns the current record's "name" value
 * @method string              getDescription()          Returns the current record's "description" value
 * @method string              getSearchIndexed()        Returns the current record's "search_indexed" value
 * @method string              getFromDate()             Returns the current record's "from_date" value
 * @method string              getToDate()               Returns the current record's "to_date" value
 * @method string              getExtendedToDate()       Returns the current record's "extended_to_date" value
 * @method integer             getCollectionRef()        Returns the current record's "collection_ref" value
 * @method string              getAddressReceiver()      Returns the current record's "address_receiver" value
 * @method string              getInstitutionReceiver()  Returns the current record's "institution_receiver" value
 * @method string              getCountryReceiver()      Returns the current record's "country_receiver" value
 * @method string              getCityReceiver()         Returns the current record's "city_receiver" value
 * @method string              getZipReceiver()          Returns the current record's "zip_receiver" value
 * @method Doctrine_Collection getCataloguePeople()      Returns the current record's "CataloguePeople" collection
 * @method Collections         getCollections()          Returns the current record's "Collections" value
 * @method Doctrine_Collection getLoanItems()            Returns the current record's "LoanItems" collection
 * @method Doctrine_Collection getLoanRights()           Returns the current record's "LoanRights" collection
 * @method Doctrine_Collection getLoanStatus()           Returns the current record's "LoanStatus" collection
 * @method Loans               setId()                   Sets the current record's "id" value
 * @method Loans               setName()                 Sets the current record's "name" value
 * @method Loans               setDescription()          Sets the current record's "description" value
 * @method Loans               setSearchIndexed()        Sets the current record's "search_indexed" value
 * @method Loans               setFromDate()             Sets the current record's "from_date" value
 * @method Loans               setToDate()               Sets the current record's "to_date" value
 * @method Loans               setExtendedToDate()       Sets the current record's "extended_to_date" value
 * @method Loans               setCollectionRef()        Sets the current record's "collection_ref" value
 * @method Loans               setAddressReceiver()      Sets the current record's "address_receiver" value
 * @method Loans               setInstitutionReceiver()  Sets the current record's "institution_receiver" value
 * @method Loans               setCountryReceiver()      Sets the current record's "country_receiver" value
 * @method Loans               setCityReceiver()         Sets the current record's "city_receiver" value
 * @method Loans               setZipReceiver()          Sets the current record's "zip_receiver" value
 * @method Loans               setCataloguePeople()      Sets the current record's "CataloguePeople" collection
 * @method Loans               setCollections()          Sets the current record's "Collections" value
 * @method Loans               setLoanItems()            Sets the current record's "LoanItems" collection
 * @method Loans               setLoanRights()           Sets the current record's "LoanRights" collection
 * @method Loans               setLoanStatus()           Sets the current record's "LoanStatus" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLoans extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('loans');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '',
             ));
        $this->hasColumn('search_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('from_date', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('to_date', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('extended_to_date', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collection_ref', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('address_receiver', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('institution_receiver', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('country_receiver', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('city_receiver', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('zip_receiver', 'string', null, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('LoanActors as CataloguePeople', array(
             'local' => 'id',
             'foreign' => 'record_id'));

        $this->hasOne('Collections', array(
             'local' => 'collection_ref',
             'foreign' => 'id'));

        $this->hasMany('LoanItems', array(
             'local' => 'id',
             'foreign' => 'loan_ref'));

        $this->hasMany('LoanRights', array(
             'local' => 'id',
             'foreign' => 'loan_ref'));

        $this->hasMany('LoanStatus', array(
             'local' => 'id',
             'foreign' => 'loan_ref'));
    }
}