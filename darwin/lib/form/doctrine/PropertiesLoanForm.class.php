<?php

/**
 * Properties form.
 *
 * @package    form
 * @subpackage Properties
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class PropertiesLoanForm extends PropertiesForm
{
  public function configure()
  {
    parent::configure();
    
    
     $this->widgetSchema['lower_value'] = new widgetFormSelectComplete(array(
      'model' => 'Properties',
      'table_method' => 'getYesNo',
      'method' => 'getYesNo',
      'key_method' => 'getYesNo',
      'add_empty' => true,
      'change_label' => 'Pick a value in the list',
      'add_label' => 'Add another value',
      'default'=> 'yes'
    ));
    
        $this->validatorSchema['lower_value'] = new sfValidatorString(array('trim'=>true, 'required'=>false, 'empty_value'=>'yes'));
  }
}
