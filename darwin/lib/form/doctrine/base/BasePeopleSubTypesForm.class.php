<?php

/**
 * PeopleSubTypes form base class.
 *
 * @method PeopleSubTypes getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleSubTypesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_type'] = new sfValidatorString();

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_sub_types[%s]');
  }

  public function getModelName()
  {
    return 'PeopleSubTypes';
  }

}
