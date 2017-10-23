<?php
class TagLineForm extends BaseForm
{
  public function configure()
  {
    $this->setWidget('tag',new sfWidgetFormInputText(array(),  array('class' => 'tag_line_'.$this->options['num'])));
    $this->setValidator('tag', new sfValidatorString(array('required' => false, 'trim' => false)) );
	//ftheeten 2016 01 08
	$this->widgetSchema['fuzzy_matching_tag']= new sfWidgetFormInputCheckbox(array('default' => TRUE),array('checked' => TRUE));
	$this->validatorSchema['fuzzy_matching_tag'] = new sfValidatorPass();
	 $this->widgetSchema->setLabels(array("fuzzy_matching_tag" => "Fuzzy"));
  }
}
