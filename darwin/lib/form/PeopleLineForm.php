<?php
class PeopleLineForm extends BaseForm
{
  public function configure()
  {
  
	//people widget
    $this->widgetSchema['people_ref'] = new widgetFormButtonRef(array(
      'model' => 'People',
      'link_url' => 'people/searchBoth',
      'box_title' => $this->getI18N()->__('Choose people'),
	  'label' => $this->getI18N()->__('Choose people'),
      'nullable' => true,
      'button_class'=>'people_ref people_ref_'.$this->options['num'],
      ),
      array('class'=>'inline',)
    );

    $fields_to_search = array(
      'spec_coll_ids' => $this->getI18N()->__('Collector'),
      'spec_don_sel_ids' => $this->getI18N()->__('Donator or seller'),
      'ident_ids' => $this->getI18N()->__('Identifier')
    );

    $this->widgetSchema['role_ref'] = new sfWidgetFormChoice(
      array('choices'=> $fields_to_search,
            'multiple' => true,
            'expanded' => true,
			 'label' => $this->getI18N()->__('Choose people role'),
      ),
	  array('class'=> 'role_ref_'.$this->options['num']));
    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required' => false)) ;
    $this->validatorSchema['role_ref'] = new sfValidatorChoice(array('choices'=>array_keys($fields_to_search), 'required'=>false)) ;
    $this->validatorSchema['role_ref'] = new sfValidatorPass() ;
	//ftheeten 2016/01/07
		$this->widgetSchema['people_fuzzy'] = new sfWidgetFormInputText();
	$this->widgetSchema['people_fuzzy']->setAttributes(array("class"=> 'class_fuzzy_people_'.$this->options['num']));
	$this->validatorSchema['people_fuzzy'] = new sfValidatorString(array('required' => false)) ;
	$this->validatorSchema['people_fuzzy'] = new sfValidatorPass() ;

	
  }
}