<?php
class TagLineForm extends BaseForm
{
  public function configure()
  {
    $this->setWidget('tag',new sfWidgetFormInputText(array(),  array('class' => 'tag_line_'.$this->options['num'])));
    $this->setValidator('tag', new sfValidatorString(array('required' => false, 'trim' => false)) );
	
	$this->widgetSchema['country_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'GtuCountry',
        'table_method' => "getSortedIso3166",
		'key_method' => 'getId',
		'method' => 'getIso3166Label',
        'add_empty' => true
      ),
      array('class'=>'country_line_'.$this->options['num'])
      );
	  
	 $this->validatorSchema['country_ref']= new sfValidatorPass();
  }
}
