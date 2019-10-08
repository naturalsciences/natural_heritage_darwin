<?php

class MaAddPropertyForm extends sfForm
{
  public function configure()
  {
    //parent::configure();
    /*$this->useFields(array(
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
    ));*/

    $yearsKeyVal = range(1400, intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_reverse(array_combine($yearsKeyVal, $yearsKeyVal),true);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['date_from'] = new widgetFormJQueryFuzzyDate(array(
     
      'image'=> '/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'with_time' => true,
      ),
      array('class' => 'from_date')
    );
    $this->widgetSchema['date_to'] = new widgetFormJQueryFuzzyDate(array(
     
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
    /*$this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->setDefault('referenced_relation', 'specimens');
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();
    $this->setDefault('record_id',0);
    */
    
    $this->widgetSchema['property_type'] = new widgetFormSelectComplete(array(
      'model' => 'Properties',
      'table_method' => array('method' => 'getDistinctType', 'parameters' => array($this->options['ref_relation'])),
      'method' => 'getType',
      'key_method' => 'getType',
      'add_empty' => true,
      'change_label' => 'Pick a type in the list',
      'add_label' => 'Add another type',
    ));
    $this->validatorSchema['property_type'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['applies_to'] = new widgetFormSelectComplete(array(
      'model' => 'Properties',
      'change_label' => 'Pick a sub-type in the list',
      'add_label' => 'Add another sub-type',
    ));
     $this->validatorSchema['applies_to'] = new sfValidatorString(array('required' => false));


    $this->widgetSchema['applies_to']->setOption('forced_choices',array(''=>''));

    $this->widgetSchema['property_unit'] = new widgetFormSelectComplete(array(
      'model' => 'Properties',
      'change_label' => 'Pick a unit in the list',
      'add_label' => 'Add another unit',
    ));


    $this->widgetSchema['property_unit']->setOption('forced_choices',array(''=>''));
 
    $this->validatorSchema['property_unit'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['method'] = new sfWidgetFormInput();
    $this->widgetSchema['method']->setAttributes(array('class'=>'medium_size'));
   $this->validatorSchema['method'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['lower_value'] = new sfWidgetFormInput();
    $this->validatorSchema['lower_value'] = new sfValidatorString(array('required' => true));
    $this->widgetSchema['upper_value'] = new sfWidgetFormInput();
    $this->validatorSchema['upper_value'] = new sfValidatorString(array('required' => false));
    
    $this->widgetSchema['property_accuracy'] = new sfWidgetFormInput();
     $this->validatorSchema['property_accuracy'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['is_quantitative'] = new sfWidgetFormInputCheckBox();
    $this->validatorSchema['is_quantitative'] = new sfValidatorBoolean(array('required' => false));
    $this->widgetSchema['property_unit']->setLabel("Unit");
    $this->widgetSchema['property_accuracy']->setLabel('Accuracy');
  }

 

  public function doMassAction($user_id, $items, $values)
  {

    $query = Doctrine_Query::create()->select('id')->from('Specimens s');
    $query->andWhere('s.id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$items),'spec_ref', $user_id));
    $results = $query->execute();

    foreach($results as $result)
    {
      $property = new Properties();
      $property->fromArray($values);
      $property->setRecordId($result->getId());
      $property->setReferencedRelation("specimens");
      $property->save();
    }
  }

}
