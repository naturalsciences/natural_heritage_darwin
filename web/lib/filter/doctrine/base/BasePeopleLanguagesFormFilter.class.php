<?php

/**
 * PeopleLanguages filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePeopleLanguagesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'people_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true)),
      'language_country'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mother'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'preferred_language' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'people_ref'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id')),
      'language_country'   => new sfValidatorPass(array('required' => false)),
      'mother'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'preferred_language' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('people_languages_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PeopleLanguages';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'people_ref'         => 'ForeignKey',
      'language_country'   => 'Text',
      'mother'             => 'Boolean',
      'preferred_language' => 'Boolean',
    );
  }
}
