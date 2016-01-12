<?php

/**
 * Codes form.
 *
 * @package    form
 * @subpackage Codes
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CodesForm extends BaseCodesForm
{
  public function configure()
  {
    $this->useFields(array('code_category', 'code_prefix', 'code_prefix_separator', 'code', 'code_suffix', 'code_suffix_separator'));
    $this->widgetSchema['code_category'] = new sfWidgetFormChoice(array(
        'choices' => Codes::getCategories()
      ));
    $this->validatorSchema['code_category'] = new sfValidatorChoice(array('required' => false, 'choices'=>array_keys(Codes::getCategories())));
    $this->widgetSchema['code_prefix'] = new sfWidgetFormInput();
    $this->widgetSchema['code_prefix']->setAttributes(array('class'=>'lsmall_size'));
    $this->validatorSchema['code_prefix'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->widgetSchema['code_prefix_separator'] = new widgetFormSelectComplete(array(
        'model' => 'Codes',
        'table_method' => 'getDistinctPrefixSep',
        'method' => 'getCodePrefixSeparator',
        'key_method' => 'getCodePrefixSeparator',
        'add_empty' => true,
        'change_label' => '',
        'add_label' => '',
    ));
    $this->widgetSchema['code_prefix_separator']->setAttributes(array('class'=>'vvsmall_size'));
    $this->widgetSchema['code'] = new sfWidgetFormInput();
    $this->widgetSchema['code']->setAttributes(array('class'=>'lsmall_size'));
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->widgetSchema['code_suffix'] = new sfWidgetFormInput();
    $this->widgetSchema['code_suffix']->setAttributes(array('class'=>'lsmall_size'));
    $this->validatorSchema['code_suffix'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->widgetSchema['code_suffix_separator'] = new widgetFormSelectComplete(array(
        'model' => 'Codes',
        'table_method' => 'getDistinctSuffixSep',
        'method' => 'getCodeSuffixSeparator',
        'key_method' => 'getCodeSuffixSeparator',
        'add_empty' => true,
        'change_label' => '',
        'add_label' => '',
    ));
    $this->widgetSchema['code_suffix_separator']->setAttributes(array('class'=>'vvsmall_size'));
    $this->mergePostValidator(new CodesValidatorSchema());
  }
}
