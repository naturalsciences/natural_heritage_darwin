<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ZzzFranckAssociateFileToRecordMarch2018', 'doctrine');

/**
 * BaseZzzFranckAssociateFileToRecordMarch2018
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $filename
 * @property string $date_modified
 * @property string $scientific_name
 * @property string $unitid
 * @property string $kindofunit
 * 
 * @method integer                                 getId()              Returns the current record's "id" value
 * @method string                                  getFilename()        Returns the current record's "filename" value
 * @method string                                  getDateModified()    Returns the current record's "date_modified" value
 * @method string                                  getScientificName()  Returns the current record's "scientific_name" value
 * @method string                                  getUnitid()          Returns the current record's "unitid" value
 * @method string                                  getKindofunit()      Returns the current record's "kindofunit" value
 * @method ZzzFranckAssociateFileToRecordMarch2018 setId()              Sets the current record's "id" value
 * @method ZzzFranckAssociateFileToRecordMarch2018 setFilename()        Sets the current record's "filename" value
 * @method ZzzFranckAssociateFileToRecordMarch2018 setDateModified()    Sets the current record's "date_modified" value
 * @method ZzzFranckAssociateFileToRecordMarch2018 setScientificName()  Sets the current record's "scientific_name" value
 * @method ZzzFranckAssociateFileToRecordMarch2018 setUnitid()          Sets the current record's "unitid" value
 * @method ZzzFranckAssociateFileToRecordMarch2018 setKindofunit()      Sets the current record's "kindofunit" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseZzzFranckAssociateFileToRecordMarch2018 extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('zzz_franck_associate_file_to_record_march2018');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'autoincrement' => true,
             'primary' => true,
             'length' => 8,
             ));
        $this->hasColumn('filename', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('date_modified', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('scientific_name', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('unitid', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
        $this->hasColumn('kindofunit', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => '',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}