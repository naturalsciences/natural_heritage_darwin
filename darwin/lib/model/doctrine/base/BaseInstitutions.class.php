<?php

/**
 * BaseInstitutions
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property boolean $is_physical
 * @property string $sub_type
 * @property string $formated_name
 * @property string $formated_name_indexed
 * @property string $family_name
 * @property string $additional_names
 * @property IdentifiersPeople $IdentifiersPeople
 * @property Doctrine_Collection $PeopleRelationships
 * @property Doctrine_Collection $Specimens
 * @property Doctrine_Collection $SpecimensRelationships
 * @property Doctrine_Collection $SpecimensMaincodes
 * @property Doctrine_Collection $InstitutionSubTypes
 * 
 * @method integer             getId()                     Returns the current record's "id" value
 * @method boolean             getIsPhysical()             Returns the current record's "is_physical" value
 * @method string              getSubType()                Returns the current record's "sub_type" value
 * @method string              getFormatedName()           Returns the current record's "formated_name" value
 * @method string              getFormatedNameIndexed()    Returns the current record's "formated_name_indexed" value
 * @method string              getFamilyName()             Returns the current record's "family_name" value
 * @method string              getAdditionalNames()        Returns the current record's "additional_names" value
 * @method IdentifiersPeople   getIdentifiersPeople()      Returns the current record's "IdentifiersPeople" value
 * @method Doctrine_Collection getPeopleRelationships()    Returns the current record's "PeopleRelationships" collection
 * @method Doctrine_Collection getSpecimens()              Returns the current record's "Specimens" collection
 * @method Doctrine_Collection getSpecimensRelationships() Returns the current record's "SpecimensRelationships" collection
 * @method Doctrine_Collection getSpecimensMaincodes()     Returns the current record's "SpecimensMaincodes" collection
 * @method Doctrine_Collection getInstitutionSubTypes()    Returns the current record's "InstitutionSubTypes" collection
 * @method Institutions        setId()                     Sets the current record's "id" value
 * @method Institutions        setIsPhysical()             Sets the current record's "is_physical" value
 * @method Institutions        setSubType()                Sets the current record's "sub_type" value
 * @method Institutions        setFormatedName()           Sets the current record's "formated_name" value
 * @method Institutions        setFormatedNameIndexed()    Sets the current record's "formated_name_indexed" value
 * @method Institutions        setFamilyName()             Sets the current record's "family_name" value
 * @method Institutions        setAdditionalNames()        Sets the current record's "additional_names" value
 * @method Institutions        setIdentifiersPeople()      Sets the current record's "IdentifiersPeople" value
 * @method Institutions        setPeopleRelationships()    Sets the current record's "PeopleRelationships" collection
 * @method Institutions        setSpecimens()              Sets the current record's "Specimens" collection
 * @method Institutions        setSpecimensRelationships() Sets the current record's "SpecimensRelationships" collection
 * @method Institutions        setSpecimensMaincodes()     Sets the current record's "SpecimensMaincodes" collection
 * @method Institutions        setInstitutionSubTypes()    Sets the current record's "InstitutionSubTypes" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseInstitutions extends DarwinModel
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
             'notnull' => false,
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
        $this->hasColumn('family_name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('additional_names', 'string', null, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('IdentifiersPeople', array(
             'local' => 'id',
             'foreign' => 'record_id'));

        $this->hasMany('PeopleRelationships', array(
             'local' => 'id',
             'foreign' => 'person_1_ref'));

        $this->hasMany('Specimens', array(
             'local' => 'id',
             'foreign' => 'institution_ref'));

        $this->hasMany('SpecimensRelationships', array(
             'local' => 'id',
             'foreign' => 'institution_ref'));

        $this->hasMany('SpecimensMaincodes', array(
             'local' => 'id',
             'foreign' => 'institution_ref'));

        $this->hasMany('InstitutionSubTypes', array(
             'local' => 'id',
             'foreign' => 'people_ref'));
    }
}