<?php

/**
 * SpecimenParts form.
 *
 * @package    form
 * @subpackage SpecimenParts
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class SpecimenPartsForm extends BaseSpecimenPartsForm
{
  public function configure()
  {
	unset( $this['specimen_individual_ref'] , $this['id']);

	$this->collection = null;
	if(isset($this->options['collection']))
	  $this->collection = $this->options['collection'];

	$this->widgetSchema['specimen_part'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctParts',
	  'method' => 'getParts',
	  'key_method' => 'getParts',
	  'add_empty' => false,
	  'change_label' => 'Pick parts in the list',
	  'add_label' => 'Add another parts',
    ));

	$this->widgetSchema['building'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctBuildings',
	  'method' => 'getBuildings',
	  'key_method' => 'getBuildings',
	  'add_empty' => true,
	  'change_label' => 'Pick a building in the list',
	  'add_label' => 'Add another building',
    ));

	$this->widgetSchema['floor'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctFloors',
	  'method' => 'getFloors',
	  'key_method' => 'getFloors',
	  'add_empty' => true,
	  'change_label' => 'Pick a floor in the list',
	  'add_label' => 'Add another floor',
    ));

	$this->widgetSchema['row'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctRows',
	  'method' => 'getRows',
	  'key_method' => 'getRows',
	  'add_empty' => true,
	  'change_label' => 'Pick a row in the list',
	  'add_label' => 'Add another row',
    ));

	$this->widgetSchema['room'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctRooms',
	  'method' => 'getRooms',
	  'key_method' => 'getRooms',
	  'add_empty' => true,
	  'change_label' => 'Pick a room in the list',
	  'add_label' => 'Add another room',
    ));

	$this->widgetSchema['shelf'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctShelfs',
	  'method' => 'getShelfs',
	  'key_method' => 'getShelfs',
	  'add_empty' => true,
	  'change_label' => 'Pick a shelf in the list',
	  'add_label' => 'Add another shelf',
    ));

	$this->widgetSchema['container_type'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctContainerTypes',
	  'method' => 'getContainerTypes',
	  'key_method' => 'getContainerTypes',
	  'add_empty' => true,
	  'change_label' => 'Pick a container in the list',
	  'add_label' => 'Add another container',
    ));

	$this->widgetSchema['sub_container_type'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctSubContainerTypes',
	  'method' => 'getSubContainerTypes',
	  'key_method' => 'getSubContainerTypes',
	  'add_empty' => true,
	  'change_label' => 'Pick a sub container type in the list',
	  'add_label' => 'Add another sub container type',
    ));

	$this->widgetSchema['specimen_status'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'table_method' => 'getDistinctStatus',
	  'method' => 'getStatus',
	  'key_method' => 'getStatus',
	  'add_empty' => true,
	  'change_label' => 'Pick a status in the list',
	  'add_label' => 'Add another status',
    ));

	$this->widgetSchema['container'] = new sfWidgetFormInput();
	$this->widgetSchema['sub_container'] = new sfWidgetFormInput();


	$this->widgetSchema['container_storage'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'change_label' => 'Pick a container storage in the list',
	  'add_label' => 'Add another container storage',
    ));

	$this->widgetSchema['sub_container_storage'] = new widgetFormSelectComplete(array(
	  'model' => 'SpecimenParts',
	  'change_label' => 'Pick a sub container storage in the list',
	  'add_label' => 'Add another sub container storage',
    ));


    $this->widgetSchema['container_storage']->setOption('forced_choices',
	  Doctrine::getTable('SpecimenParts')->getDistinctContainerStorages($this->getObject()->getContainerType())
	);

    $this->widgetSchema['sub_container_storage']->setOption('forced_choices',
	  Doctrine::getTable('SpecimenParts')->getDistinctSubContainerStorages($this->getObject()->getSubContainerType())
	);

	$this->widgetSchema['category'] = new sfWidgetFormChoice(array(
	  'choices' => SpecimenParts::getCategories(),
	));
    $this->validatorSchema['category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['accuracy'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));

    $this->setDefault('accuracy', 1);
    $this->widgetSchema['accuracy']->setLabel('Accuracy');
    $this->validatorSchema['accuracy'] = new sfValidatorPass();

    /* Codes sub form */
    $prefixes = Doctrine::getTable('Codes')->getDistinctSepVals();
    $suffixes = Doctrine::getTable('Codes')->getDistinctSepVals(false);

    $subForm = new sfForm();
    $this->embedForm('Codes',$subForm);   
    foreach(Doctrine::getTable('Codes')->getCodesRelated('specimen_parts', $this->getObject()->getId()) as $key=>$vals)
    {
      $form = new CodesForm($vals);
      $this->embeddedForms['Codes']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('Codes', $this->embeddedForms['Codes']);

    $subForm = new sfForm();
    $this->embedForm('newCode',$subForm);

    $this->widgetSchema['prefix_separator'] = new sfWidgetFormChoice(array(
        'choices' => $prefixes
    ));
    $this->widgetSchema['prefix_separator']->setAttributes(array('class'=>'vvsmall_size'));

    $this->widgetSchema['suffix_separator'] = new sfWidgetFormChoice(array(
        'choices' => $suffixes
    ));
    $this->widgetSchema['suffix_separator']->setAttributes(array('class'=>'vvsmall_size'));

    $this->widgetSchema['code'] = new sfWidgetFormInputHidden(array('default'=>1));



    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false, 'trim' => true));

    $this->validatorSchema['prefix_separator'] = new sfValidatorChoice(array('choices' => array_keys($prefixes), 'required' => false));
    $this->validatorSchema['suffix_separator'] = new sfValidatorChoice(array('choices' => array_keys($suffixes), 'required' => false));
    $this->validatorSchema['code'] = new sfValidatorPass();

    /* Insurances sub form */

    $subForm = new sfForm();
    $this->embedForm('Insurances',$subForm);   
    foreach(Doctrine::getTable('Insurances')->getInsurancesRelated('specimen_parts', $this->getObject()->getId()) as $key=>$vals)
    {
      $form = new InsurancesSubForm($vals);
      $this->embeddedForms['Insurances']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('Insurances', $this->embeddedForms['Insurances']);

    $subForm = new sfForm();
    $this->embedForm('newInsurance',$subForm);

    $this->widgetSchema['insurance'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->validatorSchema['insurance'] = new sfValidatorPass();


    $this->validatorSchema->setPostValidator(
        new sfValidatorSchemaCompare('specimen_part_count_min', '<=', 'specimen_part_count_max',
            array(),
            array('invalid' => 'The min number ("%left_field%") must be lower or equal the max number ("%right_field%")' )
            )
        );
	$this->setEmptyToObjectValue();
  }

  public function addCodes($num, $collectionId=null)
  {
      $options = array('referenced_relation' => 'specimen_parts');
      $form_options = array();
      if ($collectionId)
      {
        $collection = Doctrine::getTable('Collections')->findOneById($collectionId);
        if($collection)
        {
          $options['code_prefix'] = $collection->getCodePrefix();
          $options['code_prefix_separator'] = $collection->getCodePrefixSeparator();
          $options['code_suffix'] = $collection->getCodeSuffix();
          $options['code_suffix_separator'] = $collection->getCodeSuffixSeparator();
        }
      }
      $val = new Codes();
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new CodesForm($val);
      $this->embeddedForms['newCode']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newCode', $this->embeddedForms['newCode']);
  }
  
  public function addInsurances($num)
  {
      $options = array('referenced_relation' => 'specimen_parts');
      $val = new Insurances();
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new InsurancesSubForm($val);
      $this->embeddedForms['newInsurance']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newInsurance', $this->embeddedForms['newInsurance']);
  }
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
	if(isset($taintedValues['newCode']) && isset($taintedValues['code']))
	{
	  foreach($taintedValues['newCode'] as $key=>$newVal)
	  {
		if (!isset($this['newCode'][$key]))
		{
		  $this->addCodes($key);
		}
		$taintedValues['newCode'][$key]['record_id'] = 0;
	  }
	}
	if(isset($taintedValues['newInsurance']) && isset($taintedValues['insurance']))
	{
	  foreach($taintedValues['newInsurance'] as $key=>$newVal)
	  {
		if (!isset($this['newInsurance'][$key]))
		{
		  $this->addInsurances($key);
		}
		$taintedValues['newInsurance'][$key]['record_id'] = 0;
	  }
	}

	if(!isset($taintedValues['code']))
	{
	  $this->offsetUnset('Codes');
	  unset($taintedValues['Codes']);
	}
	if(!isset($taintedValues['insurance']))
	{
	  $this->offsetUnset('Insurances');
	  unset($taintedValues['Insurances']);
	}
	parent::bind($taintedValues, $taintedFiles);
  }


  public function saveEmbeddedForms($con = null, $forms = null)
  {
	if (null === $forms && $this->getValue('code'))
	{
	  $value = $this->getValue('newCode');
	  $collection = Doctrine::getTable('Collections')->findOneById($this->collection);
	  foreach($this->embeddedForms['newCode']->getEmbeddedForms() as $name => $form)
	  {
		if(!isset($value[$name]['code']))
		{
		  unset($this->embeddedForms['newCode'][$name]);
		}
		elseif($value[$name]['code']=='' && $value[$name]['code_prefix']=='' && $value[$name]['code_suffix']=='' && $collection)
		{
		  if($collection->getCodeAutoIncrement())
		  {
			$form->getObject()->setCode(Doctrine::getTable('Collections')->getAndUpdateLastCode($this->collection));
			$form->getObject()->setRecordId($this->getObject()->getId());
		  }
		  else
		  {
			unset($this->embeddedForms['newCode'][$name]);
		  }
		}
		else
		{
		  if($value[$name]['code']=='' && $collection)
		  {
			if($collection->getCodeAutoIncrement())
			{
			  $form->getObject()->setCode(Doctrine::getTable('Collections')->getAndUpdateLastCode($this->collection));
			}
		  }
		  $form->getObject()->setRecordId($this->getObject()->getId());
		}
	  }
	  $value = $this->getValue('Codes');
	  foreach($this->embeddedForms['Codes']->getEmbeddedForms() as $name => $form)
	  {
		if (!isset($value[$name]['code']) || ($value[$name]['code_prefix']=='' && $value[$name]['code']=='' && $value[$name]['code_suffix']==''))
		{
		  $form->getObject()->delete();
		  unset($this->embeddedForms['Codes'][$name]);
		}
	  }
	}
	if (null === $forms && $this->getValue('insurance'))
	{
	    $value = $this->getValue('newInsurance');
	    foreach($this->embeddedForms['newInsurance']->getEmbeddedForms() as $name => $form)
	    {
	      if(!isset($value[$name]['insurance_value']))
		unset($this->embeddedForms['newInsurance'][$name]);
	      else
		$form->getObject()->setRecordId($this->getObject()->getId());
	    }
	    $value = $this->getValue('Insurances');
	    foreach($this->embeddedForms['Insurances']->getEmbeddedForms() as $name => $form)
	    {
	      if (!isset($value[$name]['insurance_value']))
	      {
		$form->getObject()->delete();
		unset($this->embeddedForms['Insurances'][$name]);
	      }
	    }
	}    

	return parent::saveEmbeddedForms($con, $forms);
  }
}