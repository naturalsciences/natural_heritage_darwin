<?php

/**
 * VernacularNames form.
 *
 * @package    form
 * @subpackage VernacularNames
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class VernacularNamesForm extends BaseVernacularNamesForm
{
  public function configure()
  {
    $this->useFields(array('id', 'name','community'));
    $this->widgetSchema['name'] = new sfWidgetFormInput();
    $this->widgetSchema['name']->setAttributes(array('class'=>'xlarge_size'));
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false));
    
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorString(array('required'=>false));

    $this->widgetSchema['community'] = new widgetFormSelectComplete(array(
        'model' => 'VernacularNames',
        'table_method' => 'getDistinctCommunities',
        'method' => 'getCommunity',
        'key_method' => 'getCommunity',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['community']  = new sfValidatorString(array('required'=>false));
    $this->mergePostValidator(new VernacularnamesValidatorSchema());

  }
}
