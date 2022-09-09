<?php

/**
 * StagingInfo filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingInfoFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => true));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => true));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_info_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingInfo';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'staging_ref' => 'ForeignKey',
      'referenced_relation' => 'Text',
      'staging_ref' => 'ForeignKey',
    ));
  }
}
