<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('DbVersion', 'doctrine');

/**
 * BaseDbVersion
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property timestamp $update_at
 * 
 * @method integer   getId()        Returns the current record's "id" value
 * @method timestamp getUpdateAt()  Returns the current record's "update_at" value
 * @method DbVersion setId()        Sets the current record's "id" value
 * @method DbVersion setUpdateAt()  Sets the current record's "update_at" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseDbVersion extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('db_version');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => true,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('update_at', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'default' => 'now()',
             'primary' => false,
             'length' => 25,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}