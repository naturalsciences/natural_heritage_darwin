<?php

/**
 * PeopleSubTypes form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class PeopleSubTypesForm extends BasePeopleSubTypesForm
{
  /**
   * @see DarwinModelForm
   */
  public function configure()
  {
    $this->useFields(array( 'sub_type', 'people_ref'));

   
    $this->widgetSchema['sub_type'] =  new  widgetFormSelectComplete(array(
      'model' => 'PeopleSubTypes',
      'table_method' => 'getDistinctSubTypes',
      'method' => 'getSubType',
      'key_method' => 'getSubType',
      'add_empty' => true,
      'change_label' => 'Pick a category in the list',
      'add_label' => 'Add another category'
    ));
	$this->widgetSchema['value'] = new sfWidgetFormInput();
	
   
    $this->validatorSchema['sub_type'] = new sfValidatorPass();

    $this->widgetSchema['people_ref'] = new sfWidgetFormInputHidden();

    $this->validatorSchema['people_ref'] = new sfValidatorInteger();

  }
}
