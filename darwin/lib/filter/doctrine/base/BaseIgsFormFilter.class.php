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

    $this->widgetSchema   ['id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Igs', 'column' => 'id'));

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
      'id' => 'Number',
    ));
  }
}
