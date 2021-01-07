<?php

/**
 * TemporalInformation filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemporalInformationFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['from_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['from_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['to_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('temporal_information_filters[%s]');
  }

  public function getModelName()
  {
    return 'TemporalInformation';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'gtu_ref' => 'Number',
      'specimen_ref' => 'ForeignKey',
      'from_date_mask' => 'Number',
      'from_date' => 'Text',
      'to_date_mask' => 'Number',
      'to_date' => 'Text',
    ));
  }
}
