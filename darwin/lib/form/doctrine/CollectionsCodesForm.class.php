<?php

/**
 * Collections codes form.
 *
 * @package    form
 * @subpackage Collections
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CollectionsCodesForm extends BaseCollectionsForm
{
  public function configure()
  {
    $this->useFields(array('id', 'code_specimen_duplicate', 'code_auto_increment','code_prefix','code_prefix_separator','code_suffix','code_suffix_separator'
    //ftheeten 2018 04 26
    ,'code_last_value','code_ai_inherit'
    
    ));
    
    $this->widgetSchema['code_prefix'] = new sfWidgetFormInputText();
    $this->widgetSchema['code_prefix_separator'] = new sfWidgetFormInputText();
    $this->widgetSchema['code_suffix'] = new sfWidgetFormInputText();
    $this->widgetSchema['code_suffix_separator'] = new sfWidgetFormInputText();

    $this->widgetSchema['code_auto_increment']->setLabel('Auto incrementation of specimen code');
    $this->widgetSchema['code_prefix']->setLabel('Default specimen code prefix used');
    $this->widgetSchema['code_prefix_separator']->setLabel('Default separator after prefix');
    $this->widgetSchema['code_suffix']->setLabel('Default specimen code suffix used');
    $this->widgetSchema['code_suffix_separator']->setLabel('Default separator before suffix');

    $this->widgetSchema['code_prefix']->setAttributes(array('class'=>'lsmall_size'));
    $this->widgetSchema['code_prefix_separator']->setAttributes(array('class'=>'vvsmall_size'));
    $this->widgetSchema['code_suffix']->setAttributes(array('class'=>'lsmall_size'));
    $this->widgetSchema['code_suffix_separator']->setAttributes(array('class'=>'vvsmall_size'));

    $this->validatorSchema['code_prefix'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->validatorSchema['code_prefix_separator'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->validatorSchema['code_suffix'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->validatorSchema['code_suffix_separator'] = new sfValidatorString(array('required' => false, 'trim'=>true));
    $this->widgetSchema['code_specimen_duplicate']->setLabel('Duplicate specimen codes');
	/*f theeten 15/10/2015 mask for code  */
    $this->widgetSchema['code_mask']    = new sfWidgetFormInputText();
    $this->validatorSchema['code_mask'] = new sfValidatorString(array('required' => false, 'trim'=>true));
     //ftheeten 2018 04 26
    $this->widgetSchema['code_last_value']->setAttributes(array('class'=>'lsmall_size'));
    //$this->widgetSchema['code_last_value']->setAttributes(array('class'=>'vvsmall_size'));

  }
}