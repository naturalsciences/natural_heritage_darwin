<?php

/**
 * CollectingTools form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CollectingToolsForm extends BaseCollectingToolsForm
{
  public function configure()
  {
    $this->useFields(array('id', 'tool'));
    $this->widgetSchema['tool'] = new sfWidgetFormInputText();
    $this->widgetSchema['tool']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['tool'] = new sfValidatorString(array('required' => true, 'trim' => true));
  }
}
