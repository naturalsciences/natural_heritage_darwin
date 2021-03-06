<?php

/**
 * BaseSpecimensCode
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $code_main
 * 
 * @method integer       getId()        Returns the current record's "id" value
 * @method string        getCodeMain()  Returns the current record's "code_main" value
 * @method SpecimensCode setId()        Sets the current record's "id" value
 * @method SpecimensCode setCodeMain()  Sets the current record's "code_main" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSpecimensCode extends Specimens
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('specimens_code');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('code_main', 'string', null, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}