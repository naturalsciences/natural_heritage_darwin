<?php

/**
 * BaseMySavedSearches
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $user_ref
 * @property string $name
 * @property string $search_criterias
 * @property boolean $favorite
 * @property boolean $is_only_id
 * @property string $modification_date_time
 * @property string $visible_fields_in_result
 * @property string $subject
 * @property string $query_where
 * @property string $query_parameters
 * @property integer $current_page
 * @property integer $page_size
 * @property integer $nb_records
 * @property boolean $download_lock
 * @property boolean $is_public
 * @property Users $User
 * 
 * @method integer         getId()                       Returns the current record's "id" value
 * @method integer         getUserRef()                  Returns the current record's "user_ref" value
 * @method string          getName()                     Returns the current record's "name" value
 * @method string          getSearchCriterias()          Returns the current record's "search_criterias" value
 * @method boolean         getFavorite()                 Returns the current record's "favorite" value
 * @method boolean         getIsOnlyId()                 Returns the current record's "is_only_id" value
 * @method string          getModificationDateTime()     Returns the current record's "modification_date_time" value
 * @method string          getVisibleFieldsInResult()    Returns the current record's "visible_fields_in_result" value
 * @method string          getSubject()                  Returns the current record's "subject" value
 * @method string          getQueryWhere()               Returns the current record's "query_where" value
 * @method string          getQueryParameters()          Returns the current record's "query_parameters" value
 * @method integer         getCurrentPage()              Returns the current record's "current_page" value
 * @method integer         getPageSize()                 Returns the current record's "page_size" value
 * @method integer         getNbRecords()                Returns the current record's "nb_records" value
 * @method boolean         getDownloadLock()             Returns the current record's "download_lock" value
 * @method boolean         getIsPublic()                 Returns the current record's "is_public" value
 * @method Users           getUser()                     Returns the current record's "User" value
 * @method MySavedSearches setId()                       Sets the current record's "id" value
 * @method MySavedSearches setUserRef()                  Sets the current record's "user_ref" value
 * @method MySavedSearches setName()                     Sets the current record's "name" value
 * @method MySavedSearches setSearchCriterias()          Sets the current record's "search_criterias" value
 * @method MySavedSearches setFavorite()                 Sets the current record's "favorite" value
 * @method MySavedSearches setIsOnlyId()                 Sets the current record's "is_only_id" value
 * @method MySavedSearches setModificationDateTime()     Sets the current record's "modification_date_time" value
 * @method MySavedSearches setVisibleFieldsInResult()    Sets the current record's "visible_fields_in_result" value
 * @method MySavedSearches setSubject()                  Sets the current record's "subject" value
 * @method MySavedSearches setQueryWhere()               Sets the current record's "query_where" value
 * @method MySavedSearches setQueryParameters()          Sets the current record's "query_parameters" value
 * @method MySavedSearches setCurrentPage()              Sets the current record's "current_page" value
 * @method MySavedSearches setPageSize()                 Sets the current record's "page_size" value
 * @method MySavedSearches setNbRecords()                Sets the current record's "nb_records" value
 * @method MySavedSearches setDownloadLock()             Sets the current record's "download_lock" value
 * @method MySavedSearches setIsPublic()                 Sets the current record's "is_public" value
 * @method MySavedSearches setUser()                     Sets the current record's "User" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseMySavedSearches extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('my_saved_searches');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('user_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
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
        $this->hasColumn('is_only_id', 'boolean', null, array(
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
        $this->hasColumn('subject', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('query_where', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('query_parameters', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('current_page', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('page_size', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 10000,
             ));
        $this->hasColumn('nb_records', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('download_lock', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('is_public', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
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