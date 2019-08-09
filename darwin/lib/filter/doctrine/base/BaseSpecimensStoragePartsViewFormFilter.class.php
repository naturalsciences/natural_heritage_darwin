<?php

/**
 * SpecimensStoragePartsView filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensStoragePartsViewFormFilter extends SpecimensFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id'));

    $this->widgetSchema   ['synonymy_group_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['synonymy_group_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['synonymy_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['synonymy_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['count_by_synonymy_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['count_by_synonymy_status'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['synonymy_count_all_in_group'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['synonymy_count_all_in_group'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema->setNameFormat('specimens_storage_parts_view_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensStoragePartsView';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'specimen_ref' => 'ForeignKey',
      'synonymy_group_id' => 'Number',
      'synonymy_status' => 'Text',
      'count_by_synonymy_status' => 'Number',
      'synonymy_count_all_in_group' => 'Number',
    ));
  }
}
