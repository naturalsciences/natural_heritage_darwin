<?php

/**
 * Lithostratigraphy form.
 *
 * @package    form
 * @subpackage Lithostratigraphy
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class LithostratigraphyForm extends BaseLithostratigraphyForm
{
  public function configure()
  {
    unset($this['path']);

    $this->widgetSchema['table'] = new sfWidgetFormInputHidden(array('default'=>'lithostratigraphy'));
    $this->widgetSchema['name'] = new sfWidgetFormInput();
    $this->widgetSchema['name']->setAttributes(array('class'=>'large_size'));
    $this->validatorSchema['name']->setOption('trim', true);

    $this->widgetSchema['color'] = new widgetFormColorPicker();
    $this->widgetSchema['color']->setAttributes(array('class'=>'vsmall_size'));
    $statuses = array('valid'=>$this->getI18N()->__('valid'), 'invalid'=>$this->getI18N()->__('invalid'), 'deprecated'=>$this->getI18N()->__('deprecated'));
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));
    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes', 'parameters'=>array(array('table'=>'lithostratigraphy'))),
        'add_empty' => true
      ),
      array('class'=>'catalogue_level')
      );
    $this->widgetSchema['parent_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Lithostratigraphy',
      'method' => 'getName',
      'link_url' => 'lithostratigraphy/choose',
      'box_title' => $this->getI18N()->__('Choose Parent'),
      'button_is_hidden' => true,
      'nullable' => true,
      'complete_url' => 'catalogue/completeName?table=lithostratigraphy',
      'field_level_id' => $this->widgetSchema->generateId($this->widgetSchema->generateName('level_ref')),
    ));
    $this->widgetSchema['local_naming'] = new sfWidgetFormInputCheckbox();
    $this->widgetSchema->setLabels(array(
      'level_ref' => 'Level',
      'parent_ref' => 'Parent',
      'local_naming' => 'Local unit ?',
      'color' => 'Colour',
    ));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'required' => false));
    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($statuses), 'required' => true));
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => false));

    $this->validatorSchema['color'] = new ColorPickerValidatorSchema() ;
  }

}
