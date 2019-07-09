<?php

/**
 * PeopleLanguages form base class.
 *
 * @method PeopleLanguages getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleLanguagesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['language_country'] = new sfWidgetFormTextarea();
    $this->validatorSchema['language_country'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mother'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['mother'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['preferred_language'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['preferred_language'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_languages[%s]');
  }

  public function getModelName()
  {
    return 'PeopleLanguages';
  }

}
