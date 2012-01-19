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
    unset( $this['specimen_individual_ref'] , $this['id'],$this['path'], $this['with_parts']);

    $individual = $this->getOption('individual', '');
    $this->widgetSchema['parent_ref'] = new widgetFormButtonRef(array(
      'model' => 'SpecimenParts',
      'method' => 'getName',
      'link_url' => 'parts/choose?id='.$individual,
      'box_title' => $this->getI18N()->__('Choose Parent'),
      'nullable' => true,
    ));

    $this->collection = null;
    if($this->getOption('collection', '') != '')
      $this->collection = $this->getOption('collection');

    $this->widgetSchema['specimen_part'] = new widgetFormSelectComplete(array(
      'model' => 'SpecimenParts',
      'table_method' => 'getDistinctParts',
      'method' => 'getSpecimenPart',
      'key_method' => 'getSpecimenPart',
      'add_empty' => false,
      'change_label' => 'Pick parts in the list',
      'add_label' => 'Add another part',
      ));

    $this->widgetSchema['institution_ref'] = new widgetFormButtonRef(array(
       'model' => 'Institutions',
       'link_url' => 'institution/choose?with_js=1',
       'method' => 'getFamilyName',
       'box_title' => $this->getI18N()->__('Choose Institution'),
       'nullable' => true,
     ));
    $this->widgetSchema['institution_ref']->setLabel('Institution');

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
      'method' => 'getContainerType',
      'key_method' => 'getContainerType',
      'add_empty' => true,
      'change_label' => 'Pick a container in the list',
      'add_label' => 'Add another container',
      ));

    $this->widgetSchema['sub_container_type'] = new widgetFormSelectComplete(array(
      'model' => 'SpecimenParts',
      'table_method' => 'getDistinctSubContainerTypes',
      'method' => 'getSubContainerType',
      'key_method' => 'getSubContainerType',
      'add_empty' => true,
      'change_label' => 'Pick a sub container type in the list',
      'add_label' => 'Add another sub container type',
      ));

    $this->widgetSchema['specimen_status'] = new widgetFormSelectComplete(array(
      'model' => 'SpecimenParts',
      'table_method' => 'getDistinctStatus',
      'method' => 'getSpecimenStatus',
      'key_method' => 'getSpecimenStatus',
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

    $this->widgetSchema['category'] = new sfWidgetFormChoice(array(
      'choices' => SpecimenParts::getCategories(),
    ));

    $this->validatorSchema['category'] = new sfValidatorChoice(array('choices'=>array_keys(SpecimenParts::getCategories())));

    $this->widgetSchema['accuracy'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));

    $this->setDefault('accuracy', 1);
    $this->widgetSchema['accuracy']->setLabel('Accuracy');
    $this->validatorSchema['accuracy'] = new sfValidatorPass();

    $this->widgetSchema['code'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['comment'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->widgetSchema['extlink'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->validatorSchema['extlink'] = new sfValidatorPass();

    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false, 'trim' => true));

    $this->validatorSchema['code'] = new sfValidatorPass();
    $this->validatorSchema['comment'] = new sfValidatorPass();
    $this->widgetSchema['insurance'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->validatorSchema['insurance'] = new sfValidatorPass();

    $this->widgetSchema['surnumerary']->setLabel('supernumerary');

    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkSelfAttached'))));
    $this->mergePostValidator(new sfValidatorSchemaCompare('specimen_part_count_min', '<=', 'specimen_part_count_max',
      array(),
      array('invalid' => 'The min number ("%left_field%") must be lower or equal the max number ("%right_field%")' )
    ));

  }

  public function forceContainerChoices()
  {
    $this->widgetSchema['container_storage']->setOption('forced_choices',
      Doctrine::getTable('SpecimenParts')->getDistinctContainerStorages($this->getObject()->getContainerType())
    );

    $this->widgetSchema['sub_container_storage']->setOption('forced_choices',
      Doctrine::getTable('SpecimenParts')->getDistinctSubContainerStorages($this->getObject()->getSubContainerType())
    );
  }
  public function addExtLinks($num, $obj=null)
  {
      if(! isset($this['newExtLinks'])) $this->loadEmbedLink();
      $options = array('referenced_relation' => 'specimen_parts', 'record_id' => $this->getObject()->getId());
      if(!$obj) $val = new ExtLinks();
      else $val = $obj ;      
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new ExtLinksForm($val,array('table' => 'parts'));
      $this->embeddedForms['newExtLinks']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newExtLinks', $this->embeddedForms['newExtLinks']);
  }

  public function checkSelfAttached($validator, $values)
  {
    if(!$this->getObject()->isNew() && $values['parent_ref'] == $this->getObject()->getId())
    {
      $error = new sfValidatorError($validator, "A Part can't be attached to itself");
      throw new sfValidatorErrorSchema($validator, array('parent_ref' => $error));
    }
    return $values;
  }

  public function addCodes($num, $collectionId=null, $obj=null)
  {
      if(! isset($this['newCode'])) $this->loadEmbedCode();
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
      if(!$obj) $val = new Codes();
      else $val = $obj ;
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new CodesForm($val);
      $this->embeddedForms['newCode']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newCode', $this->embeddedForms['newCode']);
  }
  
  public function addInsurances($num, $obj=null)
  {
      if(! isset($this['newInsurance'])) $this->loadEmbedInsurance();
      $options = array('referenced_relation' => 'specimen_parts');
      if(!$obj) $val = new Insurances();
      else $val = $obj ;
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new InsurancesSubForm($val);
      $this->embeddedForms['newInsurance']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newInsurance', $this->embeddedForms['newInsurance']);
  }

  public function addComments($num,$obj=null)
  {
      if(! isset($this['newComments'])) $this->loadEmbedComment();
      $options = array('referenced_relation' => 'specimen_parts', 'record_id' => $this->getObject()->getId());
      if(!$obj) $val = new Comments();
      else $val = $obj ;
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new CommentsSubForm($val,array('table' => 'parts'));
      $this->embeddedForms['newComments']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newComments', $this->embeddedForms['newComments']);
  }

  protected function getFieldsByGroup()
  {
    return array(
        'Part' => array('specimen_part'),
        'Complete' => array(
        'specimen_status',
        'complete',
      ),
      'Localisation' => array(
        'building',
        'floor',
        'room',
        'row',
        'shelf',
      ),
      'Container' => array(
        'surnumerary',
        'container',
        'container_type',
        'container_storage',
        'sub_container',
        'sub_container_type',
        'sub_container_storage',
      ),
      'Count' => array(
        'accuracy',
        'specimen_part_count_min',
        'specimen_part_count_max',
      ),
    );
  }

  public function loadEmbedInsurance()
  {
    if($this->isBound()) return;

    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('Insurances',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('Insurances')->findForTable('specimen_parts', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new InsurancesSubForm($vals,array('table' => 'parts'));
        $this->embeddedForms['Insurances']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Insurances', $this->embeddedForms['Insurances']);
    }

    $subForm = new sfForm();
    $this->embedForm('newInsurance',$subForm);
  }


  public function loadEmbedLink()
  {
    if($this->isBound()) return;
    /* extLinks sub form */
    $subForm = new sfForm();
    $this->embedForm('ExtLinks',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('ExtLinks')->findForTable('specimen_parts', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new ExtLinksForm($vals,array('table' => 'parts'));
        $this->embeddedForms['ExtLinks']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('ExtLinks', $this->embeddedForms['ExtLinks']);
    }
    $subForm = new sfForm();
    $this->embedForm('newExtLinks',$subForm);
  }

  public function loadEmbedComment()
  {
    if($this->isBound()) return;

    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('Comments',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('comments')->findForTable('specimen_parts', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new CommentsSubForm($vals,array('table' => 'parts'));
        $this->embeddedForms['Comments']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Comments', $this->embeddedForms['Comments']);
    }

    $subForm = new sfForm();
    $this->embedForm('newComments',$subForm);
  }

  public function loadEmbedCode()
  {
    if($this->isBound()) return;
    /* Codes sub form */
    $subForm = new sfForm();
    $this->embedForm('Codes',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('Codes')->getCodesRelated('specimen_parts', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new CodesForm($vals);
        $this->embeddedForms['Codes']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Codes', $this->embeddedForms['Codes']);
    }
    $subForm = new sfForm();
    $this->embedForm('newCode',$subForm);


    $this->widgetSchema['prefix_separator'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'Codes',
        'table_method' => 'getDistinctPrefixSep',
        'method' => 'getCodePrefixSeparator',
        'key_method' => 'getCodePrefixSeparator',
        'add_empty' => true,
    ));

    $this->widgetSchema['prefix_separator']->setAttributes(array('class'=>'vvsmall_size'));

    $this->widgetSchema['suffix_separator'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'Codes',
        'table_method' => 'getDistinctSuffixSep',
        'method' => 'getCodeSuffixSeparator',
        'key_method' => 'getCodeSuffixSeparator',
        'add_empty' => true,
    ));

    $this->widgetSchema['suffix_separator']->setAttributes(array('class'=>'vvsmall_size'));
    $this->validatorSchema['prefix_separator'] = new sfValidatorPass();

    $this->validatorSchema['suffix_separator'] = new sfValidatorPass();
  }


  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['accuracy']))
    {
      if($taintedValues['accuracy'] == 0 ) //exact
      {
        $taintedValues['specimen_part_count_max'] = $taintedValues['specimen_part_count_min'];
      }
    }


    if(!isset($taintedValues['code']))
    {
      $this->offsetUnset('Codes');
      unset($taintedValues['Codes']);
      $this->offsetUnset('newCode');
      unset($taintedValues['newCode']);
    }
    else
    {
      $this->loadEmbedCode();
      if(isset($taintedValues['newCode']))
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
    }


    if(!isset($taintedValues['comment']))
    {
      $this->offsetUnset('Comments');
      unset($taintedValues['Comments']);
      $this->offsetUnset('newComments');
      unset($taintedValues['newComments']);
    }
    else
    {
      $this->loadEmbedComment();
      if(isset($taintedValues['newComments']))
      {
        foreach($taintedValues['newComments'] as $key=>$newVal)
        {
          if (!isset($this['newComments'][$key]))
          {
            $this->addComments($key);
          }
          $taintedValues['newComments'][$key]['record_id'] = 0;
        }
      }
    }

    if(!isset($taintedValues['extlink']))
    {
      $this->offsetUnset('ExtLinks');
      unset($taintedValues['ExtLinks']);
      $this->offsetUnset('newExtLinks');
      unset($taintedValues['newExtLinks']);
    }
    else
    {
      $this->loadEmbedLink();
      if(isset($taintedValues['newExtLinks']))
      {
        foreach($taintedValues['newExtLinks'] as $key=>$newVal)
        {
          if (!isset($this['newExtLinks'][$key]))
          {
            $this->addExtLinks($key);
          }
          $taintedValues['newExtLinks'][$key]['record_id'] = 0;
        }
      }
    }

    if(!isset($taintedValues['insurance']))
    {
      $this->offsetUnset('Insurances');
      unset($taintedValues['Insurances']);
      $this->offsetUnset('newInsurance');
      unset($taintedValues['newInsurance']);
    }
    else
    {
      $this->loadEmbedInsurance();
      if(isset($taintedValues['newInsurance']))
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

    if (null === $forms && $this->getValue('comment'))
    {
      $value = $this->getValue('newComments');
      foreach($this->embeddedForms['newComments']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['comment'] ))
        {
          unset($this->embeddedForms['newComments'][$name]);
        }
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
        }
      }

      $value = $this->getValue('Comments');
      foreach($this->embeddedForms['Comments']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['comment'] ))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['Comments'][$name]);
        }
      }
    }

    if (null === $forms && $this->getValue('extlink'))
    {
      $value = $this->getValue('newExtLinks');
      foreach($this->embeddedForms['newExtLinks']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['url']) || $value[$name]['url'] == '')
          unset($this->embeddedForms['newExtLinks'][$name]);
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
        }
      }

      $value = $this->getValue('ExtLinks');
      foreach($this->embeddedForms['ExtLinks']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['url']) || $value[$name]['url'] == '')
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['ExtLinks'][$name]);
        }
      }
    }
    return parent::saveEmbeddedForms($con, $forms);
  }

  public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/js/jquery-datepicker-lang.js';
    return $javascripts;
  }

  public function getStylesheets()
  {
    $javascripts=parent::getStylesheets();
    $javascripts['/css/ui.datepicker.css']='all';
    return $javascripts;
  }
}
