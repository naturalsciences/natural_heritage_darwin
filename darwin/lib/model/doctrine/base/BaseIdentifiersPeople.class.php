<?php

/**
 * BaseIdentifiersPeople
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property Doctrine_Collection $Institutions
 * @property Doctrine_Collection $People
 * 
 * @method Doctrine_Collection getInstitutions() Returns the current record's "Institutions" collection
 * @method Doctrine_Collection getPeople()       Returns the current record's "People" collection
 * @method IdentifiersPeople   setInstitutions() Sets the current record's "Institutions" collection
 * @method IdentifiersPeople   setPeople()       Sets the current record's "People" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseIdentifiersPeople extends Identifiers
{
    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Institutions', array(
             'local' => 'record_id',
             'foreign' => 'id'));

        $this->hasMany('People', array(
             'local' => 'record_id',
             'foreign' => 'id'));
    }
}