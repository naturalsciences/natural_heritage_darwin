<?php

/**
 * SpecimenIndividuals form base class.
 *
 * @method SpecimenIndividuals getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimenIndividualsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                             => new sfWidgetFormInputHidden(),
      'specimen_ref'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false)),
      'type'                           => new sfWidgetFormTextarea(),
      'type_group'                     => new sfWidgetFormTextarea(),
      'type_search'                    => new sfWidgetFormTextarea(),
      'sex'                            => new sfWidgetFormTextarea(),
      'stage'                          => new sfWidgetFormTextarea(),
      'state'                          => new sfWidgetFormTextarea(),
      'social_status'                  => new sfWidgetFormTextarea(),
      'rock_form'                      => new sfWidgetFormTextarea(),
      'specimen_individuals_count_min' => new sfWidgetFormInputText(),
      'specimen_individuals_count_max' => new sfWidgetFormInputText(),
      'with_parts'                     => new sfWidgetFormInputCheckbox(),
      'ind_ident_ids'                  => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'specimen_ref'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'))),
      'type'                           => new sfValidatorString(array('required' => false)),
      'type_group'                     => new sfValidatorString(array('required' => false)),
      'type_search'                    => new sfValidatorString(array('required' => false)),
      'sex'                            => new sfValidatorString(array('required' => false)),
      'stage'                          => new sfValidatorString(array('required' => false)),
      'state'                          => new sfValidatorString(array('required' => false)),
      'social_status'                  => new sfValidatorString(array('required' => false)),
      'rock_form'                      => new sfValidatorString(array('required' => false)),
      'specimen_individuals_count_min' => new sfValidatorInteger(array('required' => false)),
      'specimen_individuals_count_max' => new sfValidatorInteger(array('required' => false)),
      'with_parts'                     => new sfValidatorBoolean(array('required' => false)),
      'ind_ident_ids'                  => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('specimen_individuals[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimenIndividuals';
  }

}
