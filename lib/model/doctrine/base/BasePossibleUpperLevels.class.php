<?php

/**
 * BasePossibleUpperLevels
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $level_ref
 * @property integer $level_upper_ref
 * @property CatalogueLevels $UpperLevel
 * @property CatalogueLevels $Level
 * 
 * @method integer             getLevelRef()        Returns the current record's "level_ref" value
 * @method integer             getLevelUpperRef()   Returns the current record's "level_upper_ref" value
 * @method CatalogueLevels     getUpperLevel()      Returns the current record's "UpperLevel" value
 * @method CatalogueLevels     getLevel()           Returns the current record's "Level" value
 * @method PossibleUpperLevels setLevelRef()        Sets the current record's "level_ref" value
 * @method PossibleUpperLevels setLevelUpperRef()   Sets the current record's "level_upper_ref" value
 * @method PossibleUpperLevels setUpperLevel()      Sets the current record's "UpperLevel" value
 * @method PossibleUpperLevels setLevel()           Sets the current record's "Level" value
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePossibleUpperLevels extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('possible_upper_levels');
        $this->hasColumn('level_ref', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('level_upper_ref', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('CatalogueLevels as UpperLevel', array(
             'local' => 'level_upper_ref',
             'foreign' => 'id'));

        $this->hasOne('CatalogueLevels as Level', array(
             'local' => 'level_ref',
             'foreign' => 'id'));
    }
}
