<?php

/**
 * Mineralogy form.
 *
 * @package    form
 * @subpackage Mineralogy
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class MineralogyForm extends BaseMineralogyForm
{
  public function configure()
  {
    unset($this['path']);
    $this->widgetSchema['table'] = new sfWidgetFormInputHidden(array('default'=>'mineralogy'));
    $this->widgetSchema['code'] = new sfWidgetFormInput();
    $this->widgetSchema['name'] = new sfWidgetFormInput();
    $this->widgetSchema['formule'] = new sfWidgetFormInput();

    $this->widgetSchema['code']->setAttributes(array('class'=>'small_size'));
    $this->widgetSchema['name']->setAttributes(array('class'=>'large_size'));
    $this->widgetSchema['formule']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema['color'] = new widgetFormColorPicker();
    $this->widgetSchema['color']->setAttributes(array('class'=>'vsmall_size'));
    $statuses = array('valid'=>$this->getI18N()->__('valid'), 'invalid'=>$this->getI18N()->__('invalid'), 'deprecated'=>$this->getI18N()->__('deprecated'));
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));

    $classifications = array('strunz'=>'Strunz', 'dana'=>'Dana');
    $this->widgetSchema['classification'] = new sfWidgetFormChoice(array(
        'choices'  => $classifications,
    ));

    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes', 'parameters'=>array(array('table'=>'mineralogy'))),
        'add_empty' => true
      ),
      array('class'=>'catalogue_level')
      );

    $this->widgetSchema['parent_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Mineralogy',
      'method' => 'getName',
      'link_url' => 'mineralogy/choose',
      'box_title' => $this->getI18N()->__('Choose Parent'),
      'button_is_hidden' => true,
      'complete_url' => 'catalogue/completeName?table=mineralogy',
      'field_level_id' => $this->widgetSchema->generateId($this->widgetSchema->generateName('level_ref')),
      'nullable' => true,
     ));

    $this->widgetSchema['cristal_system'] = new widgetFormSelectComplete(array(
      'model' => 'Mineralogy',
      'table_method' => 'getDistinctSystems',
      'method' => 'getCSystem',
      'key_method' => 'getCSystem',
      'add_empty' => false,
      'change_label' => 'Pick a system in the list',
      'add_label' => 'Add another system',
    ));

    $this->widgetSchema['local_naming'] = new sfWidgetFormInputCheckbox();
    $this->widgetSchema->setLabels(array(
      'cristal_system' => 'Cristalographic system',
      'level_ref' => 'Level',
      'parent_ref' => 'Parent',
      'local_naming' => 'Local unit ?',
      'color' => 'Colour',
    ));

    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($statuses), 'required' => true));
    $this->validatorSchema['classification'] = new sfValidatorChoice(array('choices'  => array_keys($classifications), 'required' => true));
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['color'] = new ColorPickerValidatorSchema() ;
  }
}
