<?php

/**
 * CollectionsRights form base class.
 *
 * @method CollectionsRights getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectionsRightsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => false));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['db_user_type'] = new sfWidgetFormInputText();
    $this->validatorSchema['db_user_type'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => false));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('collections_rights[%s]');
  }

  public function getModelName()
  {
    return 'CollectionsRights';
  }

}
