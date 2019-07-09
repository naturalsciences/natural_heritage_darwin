<?php

/**
 * IgsSearch filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIgsSearchFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_num'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_num_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_num_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'IgsSearch', 'column' => 'ig_ref'));

    $this->widgetSchema   ['expedition_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema->setNameFormat('igs_search_filters[%s]');
  }

  public function getModelName()
  {
    return 'IgsSearch';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'ig_num' => 'Text',
      'ig_num_indexed' => 'Text',
      'ig_date_mask' => 'Number',
      'ig_ref' => 'Number',
      'expedition_name' => 'Text',
      'expedition_name_indexed' => 'Text',
      'expedition_ref' => 'Number',
    ));
  }
}
