<?php

/**
 * Institutions filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseInstitutionsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_physical'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_physical'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['formated_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formated_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['formated_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['family_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['family_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['additional_names'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['additional_names'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Institutions', 'column' => 'id'));

    $this->widgetSchema->setNameFormat('institutions_filters[%s]');
  }

  public function getModelName()
  {
    return 'Institutions';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'is_physical' => 'Boolean',
      'sub_type' => 'Text',
      'formated_name' => 'Text',
      'formated_name_indexed' => 'Text',
      'family_name' => 'Text',
      'additional_names' => 'Text',
      'id' => 'Number',
    ));
  }
}
