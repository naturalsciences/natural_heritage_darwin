<?php

/**
 * Properties form.
 *
 * @package    form
 * @subpackage Properties
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class PropertiesForm extends BasePropertiesForm
{
  public function configure()
  {
    $this->useFields(array(
      'date_from',
      'date_to',
      'referenced_relation',
      'record_id',
      'property_type',
      'property_unit',
      'property_accuracy',
      'applies_to',
      'method',
      'lower_value',
      'upper_value',
      'is_quantitative',
    ));

    $yearsKeyVal = range(1400, intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_reverse(array_combine($yearsKeyVal, $yearsKeyVal),true);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['date_from'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>  $this->getCurrentCulture(),
      'image'=> '/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'with_time' => true,
      ),
      array('class' => 'from_date')
    );
    $this->widgetSchema['date_to'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=> $this->getCurrentCulture(),
      'image'=> '/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'with_time' => true,
      ),
      array('class' => 'to_date')
    );

    $this->validatorSchema['date_from'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      'with_time' => true
      ),
      array('invalid' => 'Invalid date "from"')
    );

    $this->validatorSchema['date_to'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      'with_time' => true
      ),
      array('invalid' => 'Invalid date "to"')
    );

     $this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare(
        'date_from',
        '<=',
        'date_to',
        array('throw_global_error' => true),
        array('invalid' => 'The "from" date cannot be above the "to" date.')
      )
    );

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();

    $this->validatorSchema['record_id'] = new sfValidatorInteger();
    $this->widgetSchema['property_type'] = new widgetFormSelectComplete(array(
      'model' => 'Properties',
      'table_method' => array('method' => 'getDistinctType', 'parameters' => array($this->options['ref_relation'])),
      'method' => 'getType',
      'key_method' => 'getType',
      'add_empty' => true,
      'change_label' => 'Pick a type in the list',
      'add_label' => 'Add another type',
    ));

    $this->widgetSchema['applies_to'] = new widgetFormSelectComplete(array(
      'model' => 'Properties',
      'change_label' => 'Pick a sub-type in the list',
      'add_label' => 'Add another sub-type',
    ));

    if(! $this->getObject()->isNew() || isset($this->options['hasmodel']))
      $this->widgetSchema['applies_to']->setOption('forced_choices', Doctrine::getTable('Properties')->getDistinctApplies($this->getObject()->getPropertyType()) );
    else
      $this->widgetSchema['applies_to']->setOption('forced_choices',array(''=>''));

    $this->widgetSchema['property_unit'] = new widgetFormSelectComplete(array(
      'model' => 'Properties',
      'change_label' => 'Pick a unit in the list',
      'add_label' => 'Add another unit',
    ));

    if(! $this->getObject()->isNew() || isset($this->options['hasmodel']))
      $this->widgetSchema['property_unit']->setOption('forced_choices', Doctrine::getTable('Properties')->getDistinctUnit($this->getObject()->getPropertyType()) );
    else
      $this->widgetSchema['property_unit']->setOption('forced_choices',array(''=>''));

    $this->widgetSchema['method'] = new sfWidgetFormInput();
    $this->widgetSchema['method']->setAttributes(array('class'=>'medium_size'));

    $this->widgetSchema['lower_value'] = new sfWidgetFormInput();
    $this->widgetSchema['upper_value'] = new sfWidgetFormInput();
    $this->widgetSchema['property_accuracy'] = new sfWidgetFormInput();
    $this->widgetSchema['property_unit']->setLabel("Unit");
    $this->widgetSchema['property_accuracy']->setLabel('Accuracy');
  }
}
