<?php

/**
 * Chronostratigraphy form.
 *
 * @package    form
 * @subpackage Chronostratigraphy
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class ChronostratigraphyForm extends BaseChronostratigraphyForm
{
  public function configure()
  {
    unset($this['path']);

    $this->widgetSchema['table'] = new sfWidgetFormInputHidden(array('default'=>'chronostratigraphy'));
    $this->widgetSchema['name'] = new sfWidgetFormInput();
    $this->widgetSchema['name']->setAttributes(array('class'=>'large_size'));
    $this->validatorSchema['name']->setOption('trim', true);

    $this->widgetSchema['lower_bound'] = new sfWidgetFormInput();
    $this->widgetSchema['lower_bound']->setAttributes(array('class'=>'small_size datesNum'));
    $this->widgetSchema['upper_bound'] = new sfWidgetFormInput();
    $this->widgetSchema['upper_bound']->setAttributes(array('class'=>'small_size datesNum'));
    $this->widgetSchema['color'] = new widgetFormColorPicker();
    $this->widgetSchema['color']->setAttributes(array('class'=>'vsmall_size'));
    $statuses = array('valid'=>$this->getI18N()->__('valid'), 'invalid'=>$this->getI18N()->__('invalid'), 'deprecated'=>$this->getI18N()->__('deprecated'));
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));
    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes', 'parameters'=>array(array('table'=>'chronostratigraphy'))),
        'add_empty' => true
      ),
      array('class'=>'catalogue_level')
    );

    $this->widgetSchema['parent_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Chronostratigraphy',
      'method' => 'getName',
      'link_url' => 'chronostratigraphy/choose',
      'box_title' => $this->getI18N()->__('Choose Parent'),
      'button_is_hidden' => true,
      'complete_url' => 'catalogue/completeName?table=chronostratigraphy',
      'nullable' => true,
    ));

    $this->widgetSchema['local_naming'] = new sfWidgetFormInputCheckbox();
    $this->widgetSchema->setLabels(array(
      'level_ref' => 'Level',
      'lower_bound' => 'Low. bound (My)',
      'upper_bound' => 'Up. bound (My)',
      'parent_ref' => 'Parent',
      'local_naming' => 'Local unit ?',
      'color' => 'Colour'
    ));

    $this->validatorSchema['color'] = new ColorPickerValidatorSchema() ;
    $this->validatorSchema['lower_bound'] = new sfValidatorNumber(array('required' => false, 'min' => -4600));
    $this->validatorSchema['upper_bound'] = new sfValidatorNumber(array('required' => false, 'max' => 1));
    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($statuses), 'required' => true));
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare(
      'lower_bound',
      '<=',
      'upper_bound',
      array('throw_global_error' => true),
      array('invalid'=>$this->getI18N()->__('The lower bound (%left_field%) cannot be above the upper bound (%right_field%).'))
    ));

  }

}
