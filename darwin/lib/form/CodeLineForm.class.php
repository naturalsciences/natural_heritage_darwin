<?php
class CodeLineForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['category'] = new sfWidgetFormChoice(array(
      'choices' => Codes::getCategories()
    ));

    $this->validatorSchema['category'] = new sfValidatorChoice(array(
      'required' => false,
      'choices' => array_keys(Codes::getCategories())
    ));
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormChoice(array(
        'choices' => array('specimens'=>'Specimen Code','specimen_parts'=>'Parts Code'),
    ));   
    $this->validatorSchema['referenced_relation'] = new sfValidatorChoice(array(
      'required' => false,
      'choices' => array('specimens'=>'specimens','specimen_parts'=>'specimen_parts'),
    ));
    $this->widgetSchema['code_part'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
    $this->widgetSchema['code_prefix'] = new sfWidgetFormInput(array(),array('class'=> 'lsmall_size '));
    $this->widgetSchema['code_from'] = new sfWidgetFormInput(array(),array('class'=> 'lsmall_size'));
    $this->widgetSchema['code_to'] = new sfWidgetFormInput(array(),array('class'=> 'lsmall_size'));

    $this->validatorSchema['code_prefix'] = new sfValidatorString(array('required'=>false,'trim'=>true));
    $this->validatorSchema['code_part'] = new sfValidatorString(array('required'=>false,'trim'=>true));
    //ftheeten 2015 06 04
    $this->widgetSchema['code_part']->setAttributes(array('class'=>'autocomplete_for_code'));
    $this->validatorSchema['code_from'] = new sfValidatorString(array('required'=>false,'trim'=>true));
    $this->validatorSchema['code_to'] = new sfValidatorString(array('required'=>false,'trim'=>true));
   
    
       
	$this->widgetSchema['exclude_prefix_in_searches'] = new sfWidgetFormInputCheckbox();
    $this->widgetSchema['exclude_prefix_in_searches']->setLabel("Exclude collection prefixes in searches");
    $pref_keys = array('exclude_prefix_in_searches');
    $db_keys = Doctrine_Core::getTable('Preferences')->getAllPreferences( sfContext::getInstance()->getUser()->getId(), $pref_keys);
    if((boolean)$db_keys['exclude_prefix_in_searches']==true)
    {
        $this->widgetSchema['exclude_prefix_in_searches']->setAttribute('checked', 'checked');;
	}
    $this->validatorSchema['exclude_prefix_in_searches'] = new  sfValidatorboolean() ;
    
     $this->mergePostValidator(new CodesLineValidatorSchema());

  }
}