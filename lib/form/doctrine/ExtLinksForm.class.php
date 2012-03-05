<?php

/**
 * ExtLinks form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ExtLinksForm extends BaseExtLinksForm
{
  public function configure()
  {
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();
    unset($this['comment_ts']);
    unset($this['comment_language_full_text']); // @TODO : check this!
    $this->widgetSchema['url'] = new sfWidgetFormInputText();
    $this->widgetSchema['url']->setAttributes(array('class'=>'small_medium_size'));

    /* Validators */
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['url'] = new sfValidatorString(array('required'=>false));
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
  }
}
