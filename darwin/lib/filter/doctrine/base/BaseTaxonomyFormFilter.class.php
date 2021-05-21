<?php

/**
 * Taxonomy filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTaxonomyFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'add_empty' => true));
    $this->validatorSchema['level_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Level'), 'column' => 'id'));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['extinct'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['extinct'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['is_reference_taxonomy'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['metadata_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxonomyMetadata'), 'add_empty' => true));
    $this->validatorSchema['metadata_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TaxonomyMetadata'), 'column' => 'id'));

    $this->widgetSchema   ['sensitive_info_withheld'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['sensitive_info_withheld'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['cites'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['cites'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'add_empty' => true));
    $this->validatorSchema['level_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Level'), 'column' => 'id'));

    $this->widgetSchema   ['metadata_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TaxonomyMetadata'), 'add_empty' => true));
    $this->validatorSchema['metadata_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TaxonomyMetadata'), 'column' => 'id'));

    $this->widgetSchema   ['id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Taxonomy', 'column' => 'id'));

    $this->widgetSchema->setNameFormat('taxonomy_filters[%s]');
  }

  public function getModelName()
  {
    return 'Taxonomy';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'name' => 'Text',
      'name_indexed' => 'Text',
      'level_ref' => 'ForeignKey',
      'status' => 'Text',
      'path' => 'Text',
      'parent_ref' => 'ForeignKey',
      'extinct' => 'Boolean',
      'is_reference_taxonomy' => 'Boolean',
      'metadata_ref' => 'ForeignKey',
      'sensitive_info_withheld' => 'Boolean',
      'cites' => 'Boolean',
      'parent_ref' => 'ForeignKey',
      'level_ref' => 'ForeignKey',
      'metadata_ref' => 'ForeignKey',
      'id' => 'Number',
    ));
  }
}
