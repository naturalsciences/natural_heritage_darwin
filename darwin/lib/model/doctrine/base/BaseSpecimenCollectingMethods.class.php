<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('SpecimenCollectingMethods', 'doctrine');

/**
 * BaseSpecimenCollectingMethods
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $specimen_ref
 * @property integer $collecting_method_ref
 * @property CollectingMethods $CollectingMethods
 * @property Specimens $Specimens
 * 
 * @method integer                   getId()                    Returns the current record's "id" value
 * @method integer                   getSpecimenRef()           Returns the current record's "specimen_ref" value
 * @method integer                   getCollectingMethodRef()   Returns the current record's "collecting_method_ref" value
 * @method CollectingMethods         getCollectingMethods()     Returns the current record's "CollectingMethods" value
 * @method Specimens                 getSpecimens()             Returns the current record's "Specimens" value
 * @method SpecimenCollectingMethods setId()                    Sets the current record's "id" value
 * @method SpecimenCollectingMethods setSpecimenRef()           Sets the current record's "specimen_ref" value
 * @method SpecimenCollectingMethods setCollectingMethodRef()   Sets the current record's "collecting_method_ref" value
 * @method SpecimenCollectingMethods setCollectingMethods()     Sets the current record's "CollectingMethods" value
 * @method SpecimenCollectingMethods setSpecimens()             Sets the current record's "Specimens" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSpecimenCollectingMethods extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('specimen_collecting_methods');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'sequence' => 'specimen_collecting_methods_id',
             'length' => 4,
             ));
        $this->hasColumn('specimen_ref', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => true,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('collecting_method_ref', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => true,
             'primary' => false,
             'length' => 4,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('CollectingMethods', array(
             'local' => 'collecting_method_ref',
             'foreign' => 'id'));

        $this->hasOne('Specimens', array(
             'local' => 'specimen_ref',
             'foreign' => 'id'));
    }
}