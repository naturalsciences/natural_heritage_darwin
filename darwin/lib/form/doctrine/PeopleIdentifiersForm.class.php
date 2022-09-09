<?php

/**
 * Comments form.
 *
 * @package    form
 * @subpackage Comments
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class PeopleIdentifiersForm extends BaseIdentifiersForm
{
  public function configure()
  {
    $this->useFields(array( 'protocol', 'value', 'referenced_relation', 'record_id'));

   
    $this->widgetSchema['protocol'] =  new  widgetFormSelectComplete(array(
      'model' => 'Identifiers',
      'table_method' => 'getDistinctProtocol',
      'method' => 'getProtocol',
      'key_method' => 'getProtocol',
      'add_empty' => true,
      'change_label' => 'Pick a protocol in the list',
      'add_label' => 'Add another protocol'
    ));
	$this->widgetSchema['value'] = new sfWidgetFormInput();
	
   
    $this->validatorSchema['protocol'] = new sfValidatorPass();
    $this->validatorSchema['value'] = new sfValidatorPass();
	
	$this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();

    $this->validatorSchema['record_id'] = new sfValidatorInteger();


  }
  

    
  
}