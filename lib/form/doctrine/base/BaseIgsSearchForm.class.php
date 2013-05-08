<?php

/**
 * IgsSearch form base class.
 *
 * @method IgsSearch getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseIgsSearchForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ig_num'                  => new sfWidgetFormTextarea(),
      'ig_num_indexed'          => new sfWidgetFormTextarea(),
      'ig_date_mask'            => new sfWidgetFormInputText(),
      'ig_ref'                  => new sfWidgetFormInputHidden(),
      'expedition_name'         => new sfWidgetFormTextarea(),
      'expedition_name_indexed' => new sfWidgetFormTextarea(),
      'expedition_ref'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ig_num'                  => new sfValidatorString(array('required' => false)),
      'ig_num_indexed'          => new sfValidatorString(array('required' => false)),
      'ig_date_mask'            => new sfValidatorInteger(array('required' => false)),
      'ig_ref'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('ig_ref')), 'empty_value' => $this->getObject()->get('ig_ref'), 'required' => false)),
      'expedition_name'         => new sfValidatorString(array('required' => false)),
      'expedition_name_indexed' => new sfValidatorString(array('required' => false)),
      'expedition_ref'          => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('igs_search[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'IgsSearch';
  }

}
