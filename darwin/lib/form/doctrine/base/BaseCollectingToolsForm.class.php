<?php

/**
 * CollectingTools form base class.
 *
 * @method CollectingTools getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectingToolsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['tool'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tool'] = new sfValidatorString();

    $this->widgetSchema   ['tool_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tool_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimens_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Specimens'));
    $this->validatorSchema['specimens_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Specimens', 'required' => false));

    $this->widgetSchema->setNameFormat('collecting_tools[%s]');
  }

  public function getModelName()
  {
    return 'CollectingTools';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['specimens_list']))
    {
      $this->setDefault('specimens_list', $this->object->Specimens->getPrimaryKeys());
    }

  }

  protected function doUpdateObject($values)
  {
    $this->updateSpecimensList($values);

    parent::doUpdateObject($values);
  }

  public function updateSpecimensList($values)
  {
    if (!isset($this->widgetSchema['specimens_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (!array_key_exists('specimens_list', $values))
    {
      // no values for this widget
      return;
    }

    $existing = $this->object->Specimens->getPrimaryKeys();
    $values = $values['specimens_list'];
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
