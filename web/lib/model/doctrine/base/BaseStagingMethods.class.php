<?php

/**
 * BaseStagingMethods
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $staging_ref
 * @property integer $collecting_method_ref
 * @property Staging $Staging
 * @property CollectingMethods $CollectingMethods
 * 
 * @method integer           getId()                    Returns the current record's "id" value
 * @method integer           getStagingRef()            Returns the current record's "staging_ref" value
 * @method integer           getCollectingMethodRef()   Returns the current record's "collecting_method_ref" value
 * @method Staging           getStaging()               Returns the current record's "Staging" value
 * @method CollectingMethods getCollectingMethods()     Returns the current record's "CollectingMethods" value
 * @method StagingMethods    setId()                    Sets the current record's "id" value
 * @method StagingMethods    setStagingRef()            Sets the current record's "staging_ref" value
 * @method StagingMethods    setCollectingMethodRef()   Sets the current record's "collecting_method_ref" value
 * @method StagingMethods    setStaging()               Sets the current record's "Staging" value
 * @method StagingMethods    setCollectingMethods()     Sets the current record's "CollectingMethods" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseStagingMethods extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('staging_collecting_methods');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('staging_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('collecting_method_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Staging', array(
             'local' => 'staging_ref',
             'foreign' => 'id'));

        $this->hasOne('CollectingMethods', array(
             'local' => 'collecting_method_ref',
             'foreign' => 'id'));
    }
}