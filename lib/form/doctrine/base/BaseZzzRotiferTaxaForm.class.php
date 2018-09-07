<?php

/**
 * ZzzRotiferTaxa form base class.
 *
 * @method ZzzRotiferTaxa getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseZzzRotiferTaxaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputText(),
      'name'         => new sfWidgetFormTextarea(),
      'name_indexed' => new sfWidgetFormTextarea(),
      'level_ref'    => new sfWidgetFormInputText(),
      'status'       => new sfWidgetFormTextarea(),
      'local_naming' => new sfWidgetFormInputCheckbox(),
      'color'        => new sfWidgetFormTextarea(),
      'path'         => new sfWidgetFormTextarea(),
      'parent_ref'   => new sfWidgetFormInputText(),
      'extinct'      => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorInteger(array('required' => false)),
      'name'         => new sfValidatorString(array('required' => false)),
      'name_indexed' => new sfValidatorString(array('required' => false)),
      'level_ref'    => new sfValidatorInteger(array('required' => false)),
      'status'       => new sfValidatorString(array('required' => false)),
      'local_naming' => new sfValidatorBoolean(array('required' => false)),
      'color'        => new sfValidatorString(array('required' => false)),
      'path'         => new sfValidatorString(array('required' => false)),
      'parent_ref'   => new sfValidatorInteger(array('required' => false)),
      'extinct'      => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('zzz_rotifer_taxa[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ZzzRotiferTaxa';
  }

}
