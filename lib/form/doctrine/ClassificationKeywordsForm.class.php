<?php

/**
 * ClassificationKeywords form.
 *
 * @package    form
 * @subpackage ClassificationKeywords
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class ClassificationKeywordsForm extends BaseClassificationKeywordsForm
{
  public function configure()
  {
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();


    $this->widgetSchema['keyword_type'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['keyword'] = new sfWidgetFormInput();

    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorString(array('required'=>false));
    $this->validatorSchema['keyword'] = new sfValidatorString(array('required'=>false));// Go for the keys
    $this->validatorSchema['keyword_type'] = new sfValidatorString(array('required'=>false));

    $this->mergePostValidator(new KeywordsValidatorSchema());
  }
}
