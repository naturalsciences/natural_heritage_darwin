<?php

/**
 * DbVersionTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class DbVersionTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object DbVersionTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('DbVersion');
    }
}