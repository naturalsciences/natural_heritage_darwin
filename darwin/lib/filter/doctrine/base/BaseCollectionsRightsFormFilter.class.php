<?php

/**
 * CollectionsRights filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectionsRightsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['db_user_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['db_user_type'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['widget_profile_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('WidgetProfiles'), 'add_empty' => true));
    $this->validatorSchema['widget_profile_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('WidgetProfiles'), 'column' => 'id'));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['widget_profile_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('WidgetProfiles'), 'add_empty' => true));
    $this->validatorSchema['widget_profile_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('WidgetProfiles'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('collections_rights_filters[%s]');
  }

  public function getModelName()
  {
    return 'CollectionsRights';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'collection_ref' => 'ForeignKey',
      'user_ref' => 'ForeignKey',
      'db_user_type' => 'Number',
      'widget_profile_ref' => 'ForeignKey',
      'collection_ref' => 'ForeignKey',
      'user_ref' => 'ForeignKey',
      'widget_profile_ref' => 'ForeignKey',
    ));
  }
}
