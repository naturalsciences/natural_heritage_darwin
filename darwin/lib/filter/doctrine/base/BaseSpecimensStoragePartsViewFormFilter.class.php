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

    $this->widgetSchema   ['category'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_part'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['specimen_part'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['complete'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['complete'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['institution_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Institution'), 'add_empty' => true));
    $this->validatorSchema['institution_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Institution'), 'column' => 'id'));

    $this->widgetSchema   ['building'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['building'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['floor'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['floor'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['room'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['room'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['row'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['row'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['col'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['col'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['shelf'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['shelf'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['container'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['container'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_container'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['container_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['container_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_container_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['container_storage'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['container_storage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_container_storage'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sub_container_storage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['surnumerary'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['surnumerary'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['object_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['object_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['object_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['object_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_status'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['specimen_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['synonymy_group_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['synonymy_group_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['synonymy_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['synonymy_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['count_by_synonymy_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['count_by_synonymy_status'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['synonymy_count_all_in_group'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['synonymy_count_all_in_group'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['cites'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['cites'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['determination_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['determination_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('specimens_storage_parts_view_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensStoragePartsView';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'category' => 'Text',
      'specimen_ref' => 'ForeignKey',
      'specimen_part' => 'Text',
      'complete' => 'Boolean',
      'institution_ref' => 'ForeignKey',
      'building' => 'Text',
      'floor' => 'Text',
      'room' => 'Text',
      'row' => 'Text',
      'col' => 'Text',
      'shelf' => 'Text',
      'container' => 'Text',
      'sub_container' => 'Text',
      'container_type' => 'Text',
      'sub_container_type' => 'Text',
      'container_storage' => 'Text',
      'sub_container_storage' => 'Text',
      'surnumerary' => 'Boolean',
      'object_name' => 'Text',
      'object_name_indexed' => 'Text',
      'specimen_status' => 'Text',
      'synonymy_group_id' => 'Number',
      'synonymy_status' => 'Text',
      'count_by_synonymy_status' => 'Number',
      'synonymy_count_all_in_group' => 'Number',
      'cites' => 'Boolean',
      'determination_status' => 'Text',
      'collection_ref' => 'ForeignKey',
    ));
  }
}
