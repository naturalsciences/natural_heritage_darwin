<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('TemplatePeopleUsersCommCommon', 'doctrine');

/**
 * BaseTemplatePeopleUsersCommCommon
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $person_user_ref
 * @property string $entry
 * 
 * @method integer                       getId()              Returns the current record's "id" value
 * @method integer                       getPersonUserRef()   Returns the current record's "person_user_ref" value
 * @method string                        getEntry()           Returns the current record's "entry" value
 * @method TemplatePeopleUsersCommCommon setId()              Sets the current record's "id" value
 * @method TemplatePeopleUsersCommCommon setPersonUserRef()   Sets the current record's "person_user_ref" value
 * @method TemplatePeopleUsersCommCommon setEntry()           Sets the current record's "entry" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTemplatePeopleUsersCommCommon extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('template_people_users_comm_common');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'autoincrement' => true,
             'primary' => true,
             'length' => 8,
             ));
        $this->hasColumn('person_user_ref', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => true,
             'primary' => false,
             'length' => 4,
             ));
        $this->hasColumn('entry', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => true,
             'primary' => false,
             'length' => '',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}