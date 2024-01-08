<?php

/**
 * Igs filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIgsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['ig_num'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_num_indexed'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['ig_num_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['ig_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['ig_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['nagoya_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['nagoya_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['complete'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['complete'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema->setNameFormat('igs_filters[%s]');
  }

  public function getModelName()
  {
    return 'Igs';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'ig_num' => 'Text',
      'ig_num_indexed' => 'Text',
      'ig_date_mask' => 'Number',
      'ig_date' => 'Text',
      'nagoya_status' => 'Text',
      'ig_type' => 'Text',
      'complete' => 'Boolean',
    ));
  }
}
