<?php

/**
 * BaseExtLinks
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $referenced_relation
 * @property integer $record_id
 * @property string $url
 * @property string $comment
 * @property string $comment_indexed
 * @property string $category
 * @property string $contributor
 * @property string $disclaimer
 * @property string $license
 * @property integer $display_order
 * 
 * @method integer  getId()                  Returns the current record's "id" value
 * @method string   getReferencedRelation()  Returns the current record's "referenced_relation" value
 * @method integer  getRecordId()            Returns the current record's "record_id" value
 * @method string   getUrl()                 Returns the current record's "url" value
 * @method string   getComment()             Returns the current record's "comment" value
 * @method string   getCommentIndexed()      Returns the current record's "comment_indexed" value
 * @method string   getCategory()            Returns the current record's "category" value
 * @method string   getContributor()         Returns the current record's "contributor" value
 * @method string   getDisclaimer()          Returns the current record's "disclaimer" value
 * @method string   getLicense()             Returns the current record's "license" value
 * @method integer  getDisplayOrder()        Returns the current record's "display_order" value
 * @method ExtLinks setId()                  Sets the current record's "id" value
 * @method ExtLinks setReferencedRelation()  Sets the current record's "referenced_relation" value
 * @method ExtLinks setRecordId()            Sets the current record's "record_id" value
 * @method ExtLinks setUrl()                 Sets the current record's "url" value
 * @method ExtLinks setComment()             Sets the current record's "comment" value
 * @method ExtLinks setCommentIndexed()      Sets the current record's "comment_indexed" value
 * @method ExtLinks setCategory()            Sets the current record's "category" value
 * @method ExtLinks setContributor()         Sets the current record's "contributor" value
 * @method ExtLinks setDisclaimer()          Sets the current record's "disclaimer" value
 * @method ExtLinks setLicense()             Sets the current record's "license" value
 * @method ExtLinks setDisplayOrder()        Sets the current record's "display_order" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseExtLinks extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('ext_links');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('referenced_relation', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('record_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('url', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('comment', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('comment_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('category', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('contributor', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('disclaimer', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('license', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('display_order', 'integer', null, array(
             'type' => 'integer',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}