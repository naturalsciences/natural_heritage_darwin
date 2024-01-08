<?php

/**
 * WidgetProfiles form base class.
 *
 * @method WidgetProfiles getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseWidgetProfilesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['creator_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['creator_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['creation_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['creation_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['creator_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['creator_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('widget_profiles[%s]');
  }

  public function getModelName()
  {
    return 'WidgetProfiles';
  }

}
