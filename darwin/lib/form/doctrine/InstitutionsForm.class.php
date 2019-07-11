<?php

/**
 * Institution form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class InstitutionsForm extends BaseInstitutionsForm
{
  public function configure()
  {
    unset($this['formated_name_indexed']);
    $this->widgetSchema['is_physical'] = new sfWidgetFormInputHidden();
    $this->setDefault('is_physical', 'off');
    $this->widgetSchema['additional_names'] = new sfWidgetFormInput();
    $this->widgetSchema['sub_type'] = new widgetFormSelectComplete(array(
        'model' => 'Institutions',
        'table_method' => 'getDistinctSubType',
        'method' => 'getType',
        'key_method' => 'getType',
        'add_empty' => true,
	'change_label' => 'Pick a type in the list',
	'add_label' => 'Add another type',
    ));

    $this->widgetSchema['sub_type']->setLabel('Type');

    $this->widgetSchema['additional_names']->setAttributes(array('class'=>'small_size'));
  }
}
