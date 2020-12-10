<?php

/**
 * CollectingMethods form base class.
 *
 * @method CollectingMethods getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCollectingMethodsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'method'         => new sfWidgetFormTextarea(),
      'method_indexed' => new sfWidgetFormTextarea(),
      'specimens_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Specimens')),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'method'         => new sfValidatorString(),
      'method_indexed' => new sfValidatorString(array('required' => false)),
      'specimens_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Specimens', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('collecting_methods[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CollectingMethods';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['specimens_list']))
    {
      $this->setDefault('specimens_list', $this->object->Specimens->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveSpecimensList($con);

    parent::doSave($con);
  }

  public function saveSpecimensList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['specimens_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Specimens->getPrimaryKeys();
    $values = $this->getValue('specimens_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Specimens', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Specimens', array_values($link));
    }
  }

}
