<?php

/**
 * BaseCollections
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property enum $collection_type
 * @property string $code
 * @property string $name
 * @property string $name_indexed
 * @property integer $institution_ref
 * @property integer $main_manager_ref
 * @property integer $staff_ref
 * @property integer $parent_ref
 * @property string $path
 * @property boolean $code_auto_increment
 * @property boolean $code_auto_increment_for_insert_only
 * @property integer $code_last_value
 * @property string $code_prefix
 * @property string $code_prefix_separator
 * @property string $code_suffix
 * @property string $code_suffix_separator
 * @property boolean $code_specimen_duplicate
 * @property boolean $is_public
 * @property string $code_mask
 * @property boolean $allow_duplicates
 * @property boolean $code_ai_inherit
 * @property boolean $nagoya
 * @property integer $preferred_taxonomy
 * @property People $Institution
 * @property Users $Manager
 * @property Users $Staff
 * @property Collections $Parent
 * @property Doctrine_Collection $Collections
 * @property Doctrine_Collection $CollectionsRights
 * @property Doctrine_Collection $Specimens
 * @property Doctrine_Collection $Imports
 * @property Doctrine_Collection $SpecimensMaincodes
 * 
 * @method integer             getId()                                  Returns the current record's "id" value
 * @method enum                getCollectionType()                      Returns the current record's "collection_type" value
 * @method string              getCode()                                Returns the current record's "code" value
 * @method string              getName()                                Returns the current record's "name" value
 * @method string              getNameIndexed()                         Returns the current record's "name_indexed" value
 * @method integer             getInstitutionRef()                      Returns the current record's "institution_ref" value
 * @method integer             getMainManagerRef()                      Returns the current record's "main_manager_ref" value
 * @method integer             getStaffRef()                            Returns the current record's "staff_ref" value
 * @method integer             getParentRef()                           Returns the current record's "parent_ref" value
 * @method string              getPath()                                Returns the current record's "path" value
 * @method boolean             getCodeAutoIncrement()                   Returns the current record's "code_auto_increment" value
 * @method boolean             getCodeAutoIncrementForInsertOnly()      Returns the current record's "code_auto_increment_for_insert_only" value
 * @method integer             getCodeLastValue()                       Returns the current record's "code_last_value" value
 * @method string              getCodePrefix()                          Returns the current record's "code_prefix" value
 * @method string              getCodePrefixSeparator()                 Returns the current record's "code_prefix_separator" value
 * @method string              getCodeSuffix()                          Returns the current record's "code_suffix" value
 * @method string              getCodeSuffixSeparator()                 Returns the current record's "code_suffix_separator" value
 * @method boolean             getCodeSpecimenDuplicate()               Returns the current record's "code_specimen_duplicate" value
 * @method boolean             getIsPublic()                            Returns the current record's "is_public" value
 * @method string              getCodeMask()                            Returns the current record's "code_mask" value
 * @method boolean             getAllowDuplicates()                     Returns the current record's "allow_duplicates" value
 * @method boolean             getCodeAiInherit()                       Returns the current record's "code_ai_inherit" value
 * @method boolean             getNagoya()                              Returns the current record's "nagoya" value
 * @method integer             getPreferredTaxonomy()                   Returns the current record's "preferred_taxonomy" value
 * @method People              getInstitution()                         Returns the current record's "Institution" value
 * @method Users               getManager()                             Returns the current record's "Manager" value
 * @method Users               getStaff()                               Returns the current record's "Staff" value
 * @method Collections         getParent()                              Returns the current record's "Parent" value
 * @method Doctrine_Collection getCollections()                         Returns the current record's "Collections" collection
 * @method Doctrine_Collection getCollectionsRights()                   Returns the current record's "CollectionsRights" collection
 * @method Doctrine_Collection getSpecimens()                           Returns the current record's "Specimens" collection
 * @method Doctrine_Collection getImports()                             Returns the current record's "Imports" collection
 * @method Doctrine_Collection getSpecimensMaincodes()                  Returns the current record's "SpecimensMaincodes" collection
 * @method Collections         setId()                                  Sets the current record's "id" value
 * @method Collections         setCollectionType()                      Sets the current record's "collection_type" value
 * @method Collections         setCode()                                Sets the current record's "code" value
 * @method Collections         setName()                                Sets the current record's "name" value
 * @method Collections         setNameIndexed()                         Sets the current record's "name_indexed" value
 * @method Collections         setInstitutionRef()                      Sets the current record's "institution_ref" value
 * @method Collections         setMainManagerRef()                      Sets the current record's "main_manager_ref" value
 * @method Collections         setStaffRef()                            Sets the current record's "staff_ref" value
 * @method Collections         setParentRef()                           Sets the current record's "parent_ref" value
 * @method Collections         setPath()                                Sets the current record's "path" value
 * @method Collections         setCodeAutoIncrement()                   Sets the current record's "code_auto_increment" value
 * @method Collections         setCodeAutoIncrementForInsertOnly()      Sets the current record's "code_auto_increment_for_insert_only" value
 * @method Collections         setCodeLastValue()                       Sets the current record's "code_last_value" value
 * @method Collections         setCodePrefix()                          Sets the current record's "code_prefix" value
 * @method Collections         setCodePrefixSeparator()                 Sets the current record's "code_prefix_separator" value
 * @method Collections         setCodeSuffix()                          Sets the current record's "code_suffix" value
 * @method Collections         setCodeSuffixSeparator()                 Sets the current record's "code_suffix_separator" value
 * @method Collections         setCodeSpecimenDuplicate()               Sets the current record's "code_specimen_duplicate" value
 * @method Collections         setIsPublic()                            Sets the current record's "is_public" value
 * @method Collections         setCodeMask()                            Sets the current record's "code_mask" value
 * @method Collections         setAllowDuplicates()                     Sets the current record's "allow_duplicates" value
 * @method Collections         setCodeAiInherit()                       Sets the current record's "code_ai_inherit" value
 * @method Collections         setNagoya()                              Sets the current record's "nagoya" value
 * @method Collections         setPreferredTaxonomy()                   Sets the current record's "preferred_taxonomy" value
 * @method Collections         setInstitution()                         Sets the current record's "Institution" value
 * @method Collections         setManager()                             Sets the current record's "Manager" value
 * @method Collections         setStaff()                               Sets the current record's "Staff" value
 * @method Collections         setParent()                              Sets the current record's "Parent" value
 * @method Collections         setCollections()                         Sets the current record's "Collections" collection
 * @method Collections         setCollectionsRights()                   Sets the current record's "CollectionsRights" collection
 * @method Collections         setSpecimens()                           Sets the current record's "Specimens" collection
 * @method Collections         setImports()                             Sets the current record's "Imports" collection
 * @method Collections         setSpecimensMaincodes()                  Sets the current record's "SpecimensMaincodes" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCollections extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('collections');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('collection_type', 'enum', null, array(
             'type' => 'enum',
             'notnull' => true,
             'default' => 'mix',
             'values' => 
             array(
              0 => 'mix',
              1 => 'observation',
              2 => 'physical',
             ),
             ));
        $this->hasColumn('code', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('name_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('institution_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('main_manager_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('staff_ref', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('parent_ref', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('path', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '/',
             ));
        $this->hasColumn('code_auto_increment', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('code_auto_increment_for_insert_only', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => true,
             ));
        $this->hasColumn('code_last_value', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('code_prefix', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('code_prefix_separator', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('code_suffix', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('code_suffix_separator', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('code_specimen_duplicate', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('is_public', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => true,
             ));
        $this->hasColumn('code_mask', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('allow_duplicates', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('code_ai_inherit', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('nagoya', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => false,
             ));
        $this->hasColumn('preferred_taxonomy', 'integer', null, array(
             'type' => 'integer',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('People as Institution', array(
             'local' => 'institution_ref',
             'foreign' => 'id'));

        $this->hasOne('Users as Manager', array(
             'local' => 'main_manager_ref',
             'foreign' => 'id'));

        $this->hasOne('Users as Staff', array(
             'local' => 'staff_ref',
             'foreign' => 'id'));

        $this->hasOne('Collections as Parent', array(
             'local' => 'parent_ref',
             'foreign' => 'id'));

        $this->hasMany('Collections', array(
             'local' => 'id',
             'foreign' => 'parent_ref'));

        $this->hasMany('CollectionsRights', array(
             'local' => 'id',
             'foreign' => 'collection_ref'));

        $this->hasMany('Specimens', array(
             'local' => 'id',
             'foreign' => 'collection_ref'));

        $this->hasMany('Imports', array(
             'local' => 'id',
             'foreign' => 'collection_ref'));

        $this->hasMany('SpecimensMaincodes', array(
             'local' => 'id',
             'foreign' => 'collection_ref'));
    }
}