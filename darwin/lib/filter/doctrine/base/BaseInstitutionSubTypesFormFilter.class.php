<?php

/**
 * InstitutionSubTypes filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseInstitutionSubTypesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'add_empty' => true));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institutions'), 'column' => 'id'));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institutions'), 'add_empty' => true));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institutions'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('institution_sub_types_filters[%s]');
  }

  public function getModelName()
  {
    return 'InstitutionSubTypes';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'people_ref' => 'ForeignKey',
      'sub_type' => 'Text',
      'people_ref' => 'ForeignKey',
    ));
  }
}
