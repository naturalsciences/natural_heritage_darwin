<?php

/**
 * CataloguePeopleBibliographyTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CataloguePeopleBibliographyTable extends CataloguePeopleTable
{
    /**
     * Returns an instance of this class.
     *
     * @return CataloguePeopleBibliographyTable The table instance
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('CataloguePeopleBibliography');
    }
}