<?php

/**
 * SpecimensStableIds filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensStableIdsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['original_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['original_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['uuid'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['uuid'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['doi'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['doi'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_fk'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_fk'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_fk'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_fk'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('specimens_stable_ids_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensStableIds';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'specimen_ref' => 'Number',
      'original_id' => 'Number',
      'uuid' => 'Text',
      'doi' => 'Text',
      'specimen_fk' => 'ForeignKey',
      'specimen_fk' => 'ForeignKey',
    ));
  }
}
