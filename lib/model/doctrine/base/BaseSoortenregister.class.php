<?php

/**
 * BaseSoortenregister
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $taxa_ref
 * @property integer $gtu_ref
 * @property integer $habitat_ref
 * @property string $date_from
 * @property string $date_to
 * @property Taxonomy $Taxonomy
 * @property Gtu $Gtu
 * @property Habitats $Habitats
 * 
 * @method integer         getTaxaRef()     Returns the current record's "taxa_ref" value
 * @method integer         getGtuRef()      Returns the current record's "gtu_ref" value
 * @method integer         getHabitatRef()  Returns the current record's "habitat_ref" value
 * @method string          getDateFrom()    Returns the current record's "date_from" value
 * @method string          getDateTo()      Returns the current record's "date_to" value
 * @method Taxonomy        getTaxonomy()    Returns the current record's "Taxonomy" value
 * @method Gtu             getGtu()         Returns the current record's "Gtu" value
 * @method Habitats        getHabitats()    Returns the current record's "Habitats" value
 * @method Soortenregister setTaxaRef()     Sets the current record's "taxa_ref" value
 * @method Soortenregister setGtuRef()      Sets the current record's "gtu_ref" value
 * @method Soortenregister setHabitatRef()  Sets the current record's "habitat_ref" value
 * @method Soortenregister setDateFrom()    Sets the current record's "date_from" value
 * @method Soortenregister setDateTo()      Sets the current record's "date_to" value
 * @method Soortenregister setTaxonomy()    Sets the current record's "Taxonomy" value
 * @method Soortenregister setGtu()         Sets the current record's "Gtu" value
 * @method Soortenregister setHabitats()    Sets the current record's "Habitats" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSoortenregister extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('soortenregister');
        $this->hasColumn('taxa_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('gtu_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('habitat_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('date_from', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('date_to', 'string', null, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Taxonomy', array(
             'local' => 'taxa_ref',
             'foreign' => 'id'));

        $this->hasOne('Gtu', array(
             'local' => 'gtu_ref',
             'foreign' => 'id'));

        $this->hasOne('Habitats', array(
             'local' => 'habitat_ref',
             'foreign' => 'id'));
    }
}