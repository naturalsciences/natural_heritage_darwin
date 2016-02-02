<?php

/**
 * PeopleComm form.
 *
 * @package    form
 * @subpackage PeopleComm
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class PeopleCommForm extends BasePeopleCommForm
{
  public function configure()
  {
    $comm_means = PeopleComm::getCommTypes();
    unset($this['id']);
    $this->widgetSchema['person_user_ref'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['entry'] = new sfWidgetFormInput();
    $this->widgetSchema['comm_type'] = new sfWidgetFormChoice(array('choices' => $comm_means));
    $this->widgetSchema['tag'] = new widgetFormTagEntry(array('choices' => Doctrine::getTable('PeopleComm')->getTags($this->getObject()->getCommType()) ));
    $this->validatorSchema['entry'] = new sfValidatorString(array('required'=> true));
    $this->validatorSchema['comm_type'] = new sfValidatorChoice(array('required'=> true, 'choices'=>array_keys($comm_means)));
    $this->validatorSchema['tag'] = new sfValidatorString(array('required'=> false));
  }
}
