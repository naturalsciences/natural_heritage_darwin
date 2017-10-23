<?php

/**
 * CollectionsRights filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCollectionsRightsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'collection_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true)),
      'user_ref'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true)),
      'db_user_type'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'collection_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id')),
      'user_ref'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id')),
      'db_user_type'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('collections_rights_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CollectionsRights';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'collection_ref' => 'ForeignKey',
      'user_ref'       => 'ForeignKey',
      'db_user_type'   => 'Number',
    );
  }
}
