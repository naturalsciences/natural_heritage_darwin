<?php

/**
 * Reports form base class.
 *
 * @method Reports getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseReportsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['uri'] = new sfWidgetFormTextarea();
    $this->validatorSchema['uri'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang'] = new sfWidgetFormInputText();
    $this->validatorSchema['lang'] = new sfValidatorString(array('max_length' => 2));

    $this->widgetSchema   ['format'] = new sfWidgetFormTextarea();
    $this->validatorSchema['format'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['parameters'] = new sfWidgetFormTextarea();
    $this->validatorSchema['parameters'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['comment'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comment'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('reports[%s]');
  }

  public function getModelName()
  {
    return 'Reports';
  }

}
