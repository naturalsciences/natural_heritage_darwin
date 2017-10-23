<?php

/**
 * CollectingMethods form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CollectingMethodsForm extends BaseCollectingMethodsForm
{
  public function configure()
  {
    $this->useFields(array('id', 'method'));
    $this->widgetSchema['method'] = new sfWidgetFormInputText();
    $this->widgetSchema['method']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['method'] = new sfValidatorString(array('required' => true, 'trim' => true));
  }
}
