<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseComments extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('comments');
        $this->hasColumn('id', 'integer', null, array('type' => 'integer', 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('table_name', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('record_id', 'integer', null, array('type' => 'integer', 'notnull' => true));
        $this->hasColumn('notion_concerned', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('comment', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('comment_ts', 'string', null, array('type' => 'string'));
        $this->hasColumn('comment_language_full_text', 'string', null, array('type' => 'string'));
    }

}