<?php

/**
 * Mineralogy filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseMineralogyFormFilter extends DarwinModelFormFilter
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

    $this->widgetSchema   ['local_naming'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['local_naming'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['color'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['color'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['code'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['classification'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['classification'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formule'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['formule'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formule_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['formule_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['cristal_system'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['cristal_system'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Level'), 'add_empty' => true));
    $this->validatorSchema['level_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Level'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('mineralogy_filters[%s]');
  }

  public function getModelName()
  {
    return 'Mineralogy';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'name' => 'Text',
      'name_indexed' => 'Text',
      'level_ref' => 'ForeignKey',
      'status' => 'Text',
      'local_naming' => 'Boolean',
      'color' => 'Text',
      'path' => 'Text',
      'parent_ref' => 'ForeignKey',
      'code' => 'Text',
      'classification' => 'Text',
      'formule' => 'Text',
      'formule_indexed' => 'Text',
      'cristal_system' => 'Text',
      'parent_ref' => 'ForeignKey',
      'level_ref' => 'ForeignKey',
    ));
  }
}
