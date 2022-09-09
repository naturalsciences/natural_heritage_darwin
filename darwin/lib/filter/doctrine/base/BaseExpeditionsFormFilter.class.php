<?php

/**
 * Expeditions filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseExpeditionsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_from_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['expedition_from_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['expedition_from_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_to_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['expedition_to_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['expedition_to_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('expeditions_filters[%s]');
  }

  public function getModelName()
  {
    return 'Expeditions';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'name' => 'Text',
      'name_indexed' => 'Text',
      'expedition_from_date_mask' => 'Number',
      'expedition_from_date' => 'Text',
      'expedition_to_date_mask' => 'Number',
      'expedition_to_date' => 'Text',
    ));
  }
}
