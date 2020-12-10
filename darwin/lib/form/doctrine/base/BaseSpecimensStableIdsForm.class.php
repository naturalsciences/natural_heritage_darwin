<?php

/**
 * SpecimensStableIds form base class.
 *
 * @method SpecimensStableIds getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimensStableIdsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputText(),
      'specimen_ref' => new sfWidgetFormInputText(),
      'original_id'  => new sfWidgetFormInputText(),
      'uuid'         => new sfWidgetFormTextarea(),
      'doi'          => new sfWidgetFormTextarea(),
      'specimen_fk'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorInteger(),
      'specimen_ref' => new sfValidatorInteger(),
      'original_id'  => new sfValidatorInteger(),
      'uuid'         => new sfValidatorString(array('required' => false)),
      'doi'          => new sfValidatorString(array('required' => false)),
      'specimen_fk'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'))),
    ));

    $this->widgetSchema->setNameFormat('specimens_stable_ids[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimensStableIds';
  }

}
