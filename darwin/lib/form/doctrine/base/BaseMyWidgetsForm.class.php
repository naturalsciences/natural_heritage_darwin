<?php

/**
 * MyWidgets form base class.
 *
 * @method MyWidgets getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMyWidgetsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'user_ref'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'category'     => new sfWidgetFormTextarea(),
      'group_name'   => new sfWidgetFormTextarea(),
      'order_by'     => new sfWidgetFormInputText(),
      'col_num'      => new sfWidgetFormInputText(),
      'mandatory'    => new sfWidgetFormInputCheckbox(),
      'visible'      => new sfWidgetFormInputCheckbox(),
      'is_available' => new sfWidgetFormInputCheckbox(),
      'opened'       => new sfWidgetFormInputCheckbox(),
      'color'        => new sfWidgetFormTextarea(),
      'icon_ref'     => new sfWidgetFormInputText(),
      'title_perso'  => new sfWidgetFormTextarea(),
      'collections'  => new sfWidgetFormTextarea(),
      'all_public'   => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_ref'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'category'     => new sfValidatorString(array('required' => false)),
      'group_name'   => new sfValidatorString(),
      'order_by'     => new sfValidatorInteger(array('required' => false)),
      'col_num'      => new sfValidatorInteger(array('required' => false)),
      'mandatory'    => new sfValidatorBoolean(array('required' => false)),
      'visible'      => new sfValidatorBoolean(array('required' => false)),
      'is_available' => new sfValidatorBoolean(array('required' => false)),
      'opened'       => new sfValidatorBoolean(array('required' => false)),
      'color'        => new sfValidatorString(array('required' => false)),
      'icon_ref'     => new sfValidatorInteger(array('required' => false)),
      'title_perso'  => new sfValidatorString(array('required' => false)),
      'collections'  => new sfValidatorString(array('required' => false)),
      'all_public'   => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('my_widgets[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MyWidgets';
  }

}
