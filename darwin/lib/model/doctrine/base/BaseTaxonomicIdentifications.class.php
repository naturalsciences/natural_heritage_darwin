<?php

/**
 * BaseTaxonomicIdentifications
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property Specimens $Specimens
 * 
 * @method Specimens                getSpecimens() Returns the current record's "Specimens" value
 * @method TaxonomicIdentifications setSpecimens() Sets the current record's "Specimens" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTaxonomicIdentifications extends Identifications
{
    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Specimens', array(
             'local' => 'record_id',
             'foreign' => 'id'));
    }
}