<?php

/**
 * StagingCollectingMethods form base class.
 *
 * @method StagingCollectingMethods getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStagingCollectingMethodsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'staging_ref'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => false)),
      'collecting_method_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'staging_ref'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'))),
      'collecting_method_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'))),
    ));

    $this->widgetSchema->setNameFormat('staging_collecting_methods[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingCollectingMethods';
  }

}
