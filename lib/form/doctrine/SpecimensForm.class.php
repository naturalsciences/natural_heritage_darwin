<?php

/**
 * Specimens form.
 *
 * @package    form
 * @subpackage Specimens
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class SpecimensForm extends BaseSpecimensForm
{
  public function configure()
  {
    $this->setDefaults(array(
        'gtu_ref' => 0,
        'expedition_ref' => 0,
    ));
    $this->widgetSchema->setNameFormat('specimen[%s]');
    $this->widgetSchema['collection_ref'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['acquisition_category'] = new sfWidgetFormChoice(array(
      'choices' =>  SpecimensTable::getDistinctCategories(),
    ));

    $this->widgetSchema['accuracy'] = new sfWidgetFormChoice(array(
        'choices'  => array('exact','imprecise'),
        'expanded' => true,

    ));
    
    $this->validatorSchema['acquisition_category'] = new sfValidatorChoice(array(
        'choices' => SpecimensTable::getDistinctCategories(),
        'required' => false,
        ));

    $this->validatorSchema['accuracy'] = new sfValidatorChoice(array(
        'choices' => array(0,1),
        'required' => false,
        ));
    $this->widgetSchema['collecting_tool'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'Specimens',
        'table_method' => 'getDistinctTools',
        'method' => 'getTool',
        'key_method' => 'getTool',
        'add_empty' => true,
    ));

    $this->setDefault('accuracy', 1);
//     $this->validatorSchema->setOption('allow_extra_fields', true);
  }
}