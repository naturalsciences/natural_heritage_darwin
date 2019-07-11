<?php

/**
 * TemplateClassifications filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemplateClassificationsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['level_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['local_naming'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['local_naming'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['color'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['color'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['path'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['parent_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema->setNameFormat('template_classifications_filters[%s]');
  }

  public function getModelName()
  {
    return 'TemplateClassifications';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'name' => 'Text',
      'name_indexed' => 'Text',
      'level_ref' => 'Number',
      'status' => 'Text',
      'local_naming' => 'Boolean',
      'color' => 'Text',
      'path' => 'Text',
      'parent_ref' => 'Number',
    ));
  }
}
