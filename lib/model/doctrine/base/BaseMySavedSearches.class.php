<?php

/**
 * BaseMySavedSearches
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $user_ref
 * @property string $name
 * @property string $search_criterias
 * @property boolean $favorite
 * @property string $modification_date_time
 * @property string $visible_fields_in_result
 * @property Users $User
 * 
 * @method integer         getUserRef()                  Returns the current record's "user_ref" value
 * @method string          getName()                     Returns the current record's "name" value
 * @method string          getSearchCriterias()          Returns the current record's "search_criterias" value
 * @method boolean         getFavorite()                 Returns the current record's "favorite" value
 * @method string          getModificationDateTime()     Returns the current record's "modification_date_time" value
 * @method string          getVisibleFieldsInResult()    Returns the current record's "visible_fields_in_result" value
 * @method Users           getUser()                     Returns the current record's "User" value
 * @method MySavedSearches setUserRef()                  Sets the current record's "user_ref" value
 * @method MySavedSearches setName()                     Sets the current record's "name" value
 * @method MySavedSearches setSearchCriterias()          Sets the current record's "search_criterias" value
 * @method MySavedSearches setFavorite()                 Sets the current record's "favorite" value
 * @method MySavedSearches setModificationDateTime()     Sets the current record's "modification_date_time" value
 * @method MySavedSearches setVisibleFieldsInResult()    Sets the current record's "visible_fields_in_result" value
 * @method MySavedSearches setUser()                     Sets the current record's "User" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseMySavedSearches extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('my_saved_searches');
        $this->hasColumn('user_ref', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'primary' => true,
             ));
        $this->hasColumn('search_criterias', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('favorite', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('modification_date_time', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('visible_fields_in_result', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Users as User', array(
             'local' => 'user_ref',
             'foreign' => 'id'));
    }
}