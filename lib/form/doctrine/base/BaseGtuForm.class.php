<?php

/**
 * Gtu form base class.
 *
 * @method Gtu getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseGtuForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'code'               => new sfWidgetFormTextarea(),
      'parent_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'gtu_from_date_mask' => new sfWidgetFormInputText(),
      'gtu_from_date'      => new sfWidgetFormTextarea(),
      'gtu_to_date_mask'   => new sfWidgetFormInputText(),
      'gtu_to_date'        => new sfWidgetFormTextarea(),
      'latitude'           => new sfWidgetFormInputText(),
      'longitude'          => new sfWidgetFormInputText(),
      'location'           => new sfWidgetFormTextarea(),
      'lat_long_accuracy'  => new sfWidgetFormInputText(),
      'elevation'          => new sfWidgetFormInputText(),
      'elevation_accuracy' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'code'               => new sfValidatorString(),
      'parent_ref'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'required' => false)),
      'gtu_from_date_mask' => new sfValidatorInteger(array('required' => false)),
      'gtu_from_date'      => new sfValidatorString(array('required' => false)),
      'gtu_to_date_mask'   => new sfValidatorInteger(array('required' => false)),
      'gtu_to_date'        => new sfValidatorString(array('required' => false)),
      'latitude'           => new sfValidatorNumber(array('required' => false)),
      'longitude'          => new sfValidatorNumber(array('required' => false)),
      'location'           => new sfValidatorString(array('required' => false)),
      'lat_long_accuracy'  => new sfValidatorNumber(array('required' => false)),
      'elevation'          => new sfValidatorNumber(array('required' => false)),
      'elevation_accuracy' => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('gtu[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Gtu';
  }

}
