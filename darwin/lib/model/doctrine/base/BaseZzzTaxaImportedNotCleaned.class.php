<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ZzzTaxaImportedNotCleaned', 'doctrine');

/**
 * BaseZzzTaxaImportedNotCleaned
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $taxon_ref
 * @property string $reason
 * 
 * @method integer                   getId()        Returns the current record's "id" value
 * @method integer                   getTaxonRef()  Returns the current record's "taxon_ref" value
 * @method string                    getReason()    Returns the current record's "reason" value
 * @method ZzzTaxaImportedNotCleaned setId()        Sets the current record's "id" value
 * @method ZzzTaxaImportedNotCleaned setTaxonRef()  Sets the current record's "taxon_ref" value
 * @method ZzzTaxaImportedNotCleaned setReason()    Sets the current record's "reason" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseZzzTaxaImportedNotCleaned extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('zzz_taxa_imported_not_cleaned');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'autoincrement' => true,
             'primary' => true,
             'length' => 8,
             ));
        $this->hasColumn('taxon_ref', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('reason', 'string', null, array(
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