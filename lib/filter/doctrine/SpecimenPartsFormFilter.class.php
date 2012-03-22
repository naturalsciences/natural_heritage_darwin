<?php

/**
 * SpecimenParts filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SpecimenPartsFormFilter extends BaseSpecimenPartsFormFilter
{
  public function configure()
  {
	$this->useFields(array('building', 'floor', 'row', 'room', 'shelf'));
	$this->widgetSchema['building'] = new sfWidgetFormDoctrineChoice(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctBuildings',
	  'method' => 'getBuildings',
	  'key_method' => 'getBuildings',
	  'add_empty' => true
    ));

	$this->widgetSchema['floor'] = new sfWidgetFormChoice(array('choices'=>array()));

	$this->widgetSchema['row'] = new sfWidgetFormChoice(array('choices'=>array()));

	$this->widgetSchema['room'] = new sfWidgetFormChoice(array('choices'=>array()));

	$this->widgetSchema['shelf'] = new sfWidgetFormChoice(array('choices'=>array()));
  
	$this->widgetSchema['parts'] = new sfWidgetFormChoice(array(
	  'choices'  => array(),
	  'multiple' => true,
	  'expanded' => true,
	));
	$this->widgetSchema['parts']->setLabel('Parts');
	$this->validatorSchema['parts'] = new sfValidatorPass();//new sfValidatorDoctrineChoice(array('multiple' => true, 'model'=> 'SpecimenParts','required'=> false));
  }
  
  public function addBuildingColumnQuery($query, $field, $val)
  {
    $alias = $query->getRootAlias();
    $query->andWhere($alias.".building = ?", $val);
    return $query;
  }
  public function addRowColumnQuery($query, $field, $val)
  {
    $alias = $query->getRootAlias();
    $query->andWhere($alias.".row = ?", $val);
    return $query;
  }
  public function addRoomColumnQuery($query, $field, $val)
  {
    $alias = $query->getRootAlias();
    $query->andWhere($alias.".room = ?", $val);
    return $query;
  }

  public function addShelfColumnQuery($query, $field, $val)
  {
    $alias = $query->getRootAlias();
    $query->andWhere($alias.".shelf = ?", $val);
    return $query;
  }


}
