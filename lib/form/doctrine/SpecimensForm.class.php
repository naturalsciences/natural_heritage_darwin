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
    $this->useFields(array('category','collection_ref',
        'expedition_ref',
        'gtu_ref',
        'taxon_ref',
        'litho_ref',
        'chrono_ref',
        'lithology_ref',
        'mineral_ref',
        'host_taxon_ref',
        'host_specimen_ref',
        'host_relationship',
        'acquisition_category',
        'acquisition_date_mask',
        'acquisition_date',
        'station_visible',
        'ig_ref'));

    $yearsKeyVal = range(intval(sfConfig::get('app_yearRangeMin')), intval(sfConfig::get('app_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('app_dateLowerBound'));
    $maxDate->setStart(false);

    /* Define name format */
    $this->widgetSchema->setNameFormat('specimen[%s]');
    /* Fields */

    $categoy_values = array('observation'=>'Observation','physical'=>'Physical');
    $this->widgetSchema['category'] = new sfWidgetFormChoice(
      array(
        'choices' => $categoy_values
      )
    );

    /* Collection Reference */
    $this->widgetSchema['collection_ref'] = new widgetFormButtonRef(
      array('model' => 'Collections',
            'link_url' => 'collection/choose',
            'method' => 'getName',
            'box_title' => $this->getI18N()->__('Choose Collection'),
            'button_class'=>'',
           ),
      array('class'=>'inline',
           )
     );

    /* Expedition Reference */
    $this->widgetSchema['expedition_ref'] = new widgetFormButtonRef(array(
       'model' => 'Expeditions',
       'link_url' => 'expedition/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Expedition'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    /* Taxonomy Reference */
    $this->widgetSchema['taxon_ref'] = new widgetFormButtonRef(array(
       'model' => 'Taxonomy',
       'link_url' => 'taxonomy/choose',
       'method' => 'getNameWithFormat',
       'box_title' => $this->getI18N()->__('Choose Taxon'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    /* Chronostratigraphy Reference */
    $this->widgetSchema['chrono_ref'] = new widgetFormButtonRef(array(
       'model' => 'Chronostratigraphy',
       'link_url' => 'chronostratigraphy/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Chronostratigraphic unit'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    /* Lithostratigraphy Reference */
    $this->widgetSchema['litho_ref'] = new widgetFormButtonRef(array(
       'model' => 'Lithostratigraphy',
       'link_url' => 'lithostratigraphy/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Lithostratigraphic unit'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    /* Lithology Reference */
    $this->widgetSchema['lithology_ref'] = new widgetFormButtonRef(array(
       'model' => 'Lithology',
       'link_url' => 'lithology/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Lithologic unit'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    /* Mineralogy Reference */
    $this->widgetSchema['mineral_ref'] = new widgetFormButtonRef(array(
       'model' => 'Mineralogy',
       'link_url' => 'mineralogy/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Mineralogic unit'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    /* IG number Reference */
    $this->widgetSchema['ig_ref'] = new widgetFormInputChecked(
      array(
        'model' => 'Igs',
        'method' => 'getIgNum',
        'nullable' => true,
        'link_url' => 'igs/searchFor',
        'notExistingAddTitle' => $this->getI18N()->__('This I.G. number does not exist. Would you like to automatically insert it ?'),
        'notExistingAddValues' => array(
          $this->getI18N()->__('No'),
          $this->getI18N()->__('Yes')
        ),
      )
    );

    /* Gtu Reference */
    $this->widgetSchema['gtu_ref'] = new widgetFormButtonRef(array(
       'model' => 'Gtu',
       'link_url' => 'gtu/choose?with_js=1',
       'method' => 'getTagsWithCode',
       'box_title' => $this->getI18N()->__('Choose Sampling Location'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline')
    );

    /* Host Reference */
    $this->widgetSchema['host_specimen_ref'] = new widgetFormButtonRef(array(
       'model' => 'Specimens',
       'link_url' => 'specimen/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Host specimen'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    $this->widgetSchema['host_taxon_ref'] = new widgetFormButtonRef(array(
       'model' => 'Taxonomy',
       'link_url' => 'taxonomy/choose',
       'method' => 'getNameWithFormat',
       'box_title' => $this->getI18N()->__('Choose Host taxon'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    $this->widgetSchema['host_relationship'] = new widgetFormSelectComplete(array(
        'model' => 'Specimens',
        'table_method' => 'getDistinctHostRelationships',
        'method' => 'getHostRelationship',
        'key_method' => 'getHostRelationship',
        'add_empty' => true,
        'change_label' => 'Pick a relationship in the list',
        'add_label' => 'Add another relationship',
    ));

    $this->widgetSchema['coll_methods'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['coll_tools'] = new sfWidgetFormInputHidden(array('default'=>1));

    /* Acquisition categories */
    $this->widgetSchema['acquisition_category'] = new sfWidgetFormChoice(array(
      'choices' =>  SpecimensTable::getDistinctCategories(),
    ));

    $this->widgetSchema['acquisition_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(),
                                                                                  'image'=>'/images/calendar.gif',
                                                                                  'format' => '%day%/%month%/%year%',
                                                                                  'years' => $years,
                                                                                  'empty_values' => $dateText,
                                                                                 ),
                                                                            array('class' => 'to_date')
                                                                           );

    $this->widgetSchema['accompanying'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->widgetSchema['collector'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->widgetSchema['code'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['collector'] = new sfWidgetFormInputHidden(array('default'=>1));


    $this->widgetSchema['donator'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['ident'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['comment'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->widgetSchema['extlink'] = new sfWidgetFormInputHidden(array('default'=>1));

    /* Labels */
    $this->widgetSchema->setLabels(array('host_specimen_ref' => 'Host specimen',
                                         'host_relationship' => 'Relationship',
                                         'host_taxon_ref' => 'Host Taxon',
                                         'gtu_ref' => 'Sampling location Tags',
                                         'station_visible' => 'Public sampling location ?'
                                        )
                                  );

    /* Validators */
    $this->validatorSchema['extlink'] = new sfValidatorPass();

    $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));

    $this->validatorSchema['expedition_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['taxon_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['chrono_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['litho_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['lithology_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['mineral_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['gtu_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['host_specimen_ref'] = new sfValidatorInteger(array('required'=>false, 'empty_value'=>null));

    $this->validatorSchema['host_taxon_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['acquisition_category'] = new sfValidatorChoice(array(
        'choices' => array_keys(SpecimensTable::getDistinctCategories()),
        'required' => false,
        ));
    $this->validatorSchema['category'] = new sfValidatorChoice(
      array('choices'=> array_keys($categoy_values) ));

    $this->validatorSchema['acquisition_date'] = new fuzzyDateValidator(array('required' => false,
                                                                              'from_date' => true,
                                                                              'min' => $minDate,
                                                                              'max' => $maxDate,
                                                                              'empty_value' => $dateLowerBound,
                                                                             ),
                                                                        array('invalid' => 'Date provided is not valid',
                                                                             )
                                                                       );

    $this->validatorSchema['collector'] = new sfValidatorPass();

    $this->validatorSchema['donator'] = new sfValidatorPass();

    $this->validatorSchema['comment'] = new sfValidatorPass();

    $this->validatorSchema['code'] = new sfValidatorPass();

    $this->validatorSchema['ident'] = new sfValidatorPass();

    $this->validatorSchema['accompanying'] = new sfValidatorPass();

    $this->validatorSchema['coll_tools'] = new sfValidatorPass();

    $this->validatorSchema['coll_methods'] = new sfValidatorPass();

  }

  public function addExtLinks($num, $obj=null)
  {
      if(! isset($this['newExtLinks'])) $this->loadEmbedLink();
      $options = array('referenced_relation' => 'specimens', 'record_id' => $this->getObject()->getId());
      if(!$obj) $val = new ExtLinks();
      else $val = $obj ;      
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new ExtLinksForm($val,array('table' => 'specimens'));
      $this->embeddedForms['newExtLinks']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newExtLinks', $this->embeddedForms['newExtLinks']);
  }
  

  public function addCodes($num, $collectionId=null, $code=null)
  {
      if(! isset($this['newCode'])) $this->loadEmbedCode();
      $options = array('referenced_relation' => 'specimens');
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
      if(!$code) $val = new Codes();
      else $val = $code ;
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new CodesForm($val);
      $this->embeddedForms['newCode']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newCode', $this->embeddedForms['newCode']);
  }


  public function addCollectors($num, $people_ref, $order_by=0)
  {
      if(! isset($this['newCollectors']))  $this->loadEmbedCollectors();
      $options = array('referenced_relation' => 'specimens', 'people_type' => 'collector', 'people_ref' => $people_ref, 'order_by' => $order_by);
      $val = new CataloguePeople();
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new PeopleAssociationsForm($val);
      $this->embeddedForms['newCollectors']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newCollectors', $this->embeddedForms['newCollectors']);
  }

  public function addDonators($num, $people_ref, $order_by=0)
  {
      if(! isset($this['newDonators']))  $this->loadEmbedDonators();
      $options = array('referenced_relation' => 'specimens', 'people_type' => 'donator', 'people_ref' => $people_ref, 'order_by' => $order_by);
      $val = new CataloguePeople();
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new PeopleAssociationsForm($val);
      $this->embeddedForms['newDonators']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newDonators', $this->embeddedForms['newDonators']);

  }

  public function addSpecimensAccompanying($num, $obj=null)
  {
      if(! isset($this['newSpecimensAccompanying'])) $this->loadEmbedAccompanying();
      $options = array('unit' => '%', 'specimen_ref' => $this->getObject()->getId());
      if (!$obj) $val = new SpecimensAccompanying();
      else $val = $obj ;
      $val->fromArray($options);
      $val->Specimens = $this->getObject();
//      $val->setSpecimenRef($this->getObject()->getId());
      $form = new SpecimensAccompanyingForm($val);
      $this->embeddedForms['newSpecimensAccompanying']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newSpecimensAccompanying', $this->embeddedForms['newSpecimensAccompanying']);
  }

  public function addComments($num, $obj=null)
  {
      if(! isset($this['newComments'])) $this->loadEmbedComment();
      $options = array('referenced_relation' => 'specimens', 'record_id' => $this->getObject()->getId());
      if (!$obj) $val = new Comments();
      else $val = $obj ;      
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new CommentsSubForm($val,array('table' => 'specimens'));
      $this->embeddedForms['newComments']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newComments', $this->embeddedForms['newComments']);
  }

  public function addIdentifications($num, $order_by=0, $obj=null)
  {
      if(! isset($this['newIdentification'])) $this->loadEmbedIndentifications();
      $options = array('referenced_relation' => 'specimens', 'order_by' => $order_by);
      if (!$obj) $val = new Identifications();
      else $val = $obj ;
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new IdentificationsForm($val);
      $this->embeddedForms['newIdentification']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newIdentification', $this->embeddedForms['newIdentification']);
  }

  public function reembedIdentifications ($identification, $identification_number)
  {
      $this->getEmbeddedForm('Identifications')->embedForm($identification_number, $identification);
      $this->embedForm('Identifications', $this->embeddedForms['Identifications']);
  }

  public function reembedNewIdentification ($identification, $identification_number)
  {
      $this->getEmbeddedForm('newIdentification')->embedForm($identification_number, $identification);
      $this->embedForm('newIdentification', $this->embeddedForms['newIdentification']);
  }

  protected function getFieldsByGroup()
  {
    return array(
      'Acquisition' => array(
        'acquisition_category',
        'acquisition_date',
      ),
      'Expedition' => array(
        'expedition_ref',
      ),
      'Taxonomy' => array('taxon_ref'),
      'Chrono' => array('chrono_ref'),
      'Lithology' => array('lithology_ref'),
      'Lithostratigraphy' => array('litho_ref'),
      'Mineralogy' => array('mineral_ref'),

      'Host' => array(
        'host_relationship',
        'host_specimen_ref',
        'host_taxon_ref',
      ),
      'Ig' => array(
        'ig_ref',
      ),
      'Gtu' => array(
        'gtu_ref',
        'station_visible',
      ),
      'Tool' => array('collecting_tools_list'),
      'Method' => array('collecting_methods_list'),
    );
  }
  
  public function loadEmbedTools()
  {
    /* Collecting tools */
    $this->widgetSchema['collecting_tools_list'] = new widgetFormSelectDoubleListFilterable(
      array(
            'choices' => new sfCallable(array(Doctrine::getTable('CollectingTools'),'fetchTools')),
            'label_associated'=>$this->getI18N()->__('Selected'),
            'label_unassociated'=>$this->getI18N()->__('Available'),
            'add_active'=>true,
            'add_url'=>'methods_and_tools/addTool'
           )
    );
    $this->validatorSchema['collecting_tools_list'] = new sfValidatorDoctrineChoice(array('model' => 'CollectingTools','column' => 'id', 'required' => false, 'multiple' => true));
    $this->setDefault('collecting_tools_list', $this->object->CollectingTools->getPrimaryKeys());
  }

  public function loadEmbedMethods()
  {
    /* Collecting methods */
    $this->widgetSchema['collecting_methods_list'] = new widgetFormSelectDoubleListFilterable(
      array(
            'choices' => new sfCallable(array(Doctrine::getTable('CollectingMethods'), 'fetchMethods')),
            'label_associated'=>$this->getI18N()->__('Selected'),
            'label_unassociated'=>$this->getI18N()->__('Available'),
            'add_active'=>true,
            'add_url'=>'methods_and_tools/addMethod'
           )
    );
    $this->validatorSchema['collecting_methods_list'] = new sfValidatorDoctrineChoice(array('model' => 'CollectingMethods','column' => 'id', 'required' => false, 'multiple' => true));
    $this->setDefault('collecting_methods_list', $this->object->CollectingMethods->getPrimaryKeys());
  }
  

  public function loadEmbedCollectors()
  {
    if($this->isBound()) return;
    $subForm = new sfForm();
    $this->embedForm('Collectors',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('CataloguePeople')->getPeopleRelated('specimens','collector', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new PeopleAssociationsForm($vals);
        $this->embeddedForms['Collectors']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Collectors', $this->embeddedForms['Collectors']);
    }
    $subForm = new sfForm();
    $this->embedForm('newCollectors',$subForm);
  }

  public function loadEmbedDonators()
  {
    if($this->isBound()) return;
    $subForm2 = new sfForm();
    $this->embedForm('Donators',$subForm2);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('CataloguePeople')->getPeopleRelated('specimens','donator', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new PeopleAssociationsForm($vals);
        $this->embeddedForms['Donators']->embedForm($key, $form);
      }
      $this->embedForm('Donators', $this->embeddedForms['Donators']);
    }

    $subForm = new sfForm();
    $this->embedForm('newDonators',$subForm);
  }

  public function loadEmbedAccompanying()
  {
    /* Accompanying elements sub form */
    if($this->isBound()) return;
    $this->embedRelation('SpecimensAccompanying');
    $subForm = new sfForm();
    $this->embedForm('newSpecimensAccompanying',$subForm);
  }

  public function loadEmbedIndentifications()
  {
    /* Identifications sub form */
    if($this->isBound()) return;
    $subForm = new sfForm();
    $this->embedForm('Identifications',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('Identifications')->getIdentificationsRelated('specimens', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new IdentificationsForm($vals);
        $this->embeddedForms['Identifications']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Identifications', $this->embeddedForms['Identifications']);
    }
    $subForm = new sfForm();
    $this->embedForm('newIdentification',$subForm);
  }

  public function loadEmbedComment()
  {
    if($this->isBound()) return;
    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('Comments',$subForm);    
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('comments')->findForTable('specimens', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new CommentsSubForm($vals,array('table' => 'specimens'));
        $this->embeddedForms['Comments']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Comments', $this->embeddedForms['Comments']);
    }

    $subForm = new sfForm();
    $this->embedForm('newComments',$subForm);
  }

  public function loadEmbedLink()
  {
    if($this->isBound()) return;
    /* extLinks sub form */
    $subForm = new sfForm();
    $this->embedForm('ExtLinks',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('ExtLinks')->findForTable('specimens', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new ExtLinksForm($vals,array('table' => 'specimens'));
        $this->embeddedForms['ExtLinks']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('ExtLinks', $this->embeddedForms['ExtLinks']);
    }
    $subForm = new sfForm();
    $this->embedForm('newExtLinks',$subForm);
  }

  public function loadEmbedCode()
  {
    if($this->isBound()) return;
    /* Codes sub form */
    $subForm = new sfForm();
    $this->embedForm('Codes',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('Codes')->getCodesRelated('specimens', $this->getObject()->getId()) as $key=>$vals)
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
    /* For each embedded informations or many-to-many data such as collecting tools and methods
     * test if the widget is on screen by testing a flag field present on the concerned widget
     * If widget is not on screen, remove the field from list of fields to be bound, and than potentially saved
    */
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

    if(!isset($taintedValues['collector']))
    {
      $this->offsetUnset('Collectors');
      unset($taintedValues['Collectors']);
      $this->offsetUnset('newCollectors');
      unset($taintedValues['newCollectors']);
    }
    else
    {
      $this->loadEmbedCollectors();
      if(isset($taintedValues['newCollectors']))
      {
        foreach($taintedValues['newCollectors'] as $key=>$newVal)
        {
          if (!isset($this['newCollectors'][$key]))
          {
            $this->addCollectors($key,$newVal['people_ref'],$newVal['order_by']);
          }
          $taintedValues['newCollectors'][$key]['record_id'] = 0;
        }
      }
    }


    if(!isset($taintedValues['donator']))
    {
      $this->offsetUnset('Donators');
      unset($taintedValues['Donators']);
      $this->offsetUnset('newDonators');
      unset($taintedValues['newDonators']);
    }
    else
    {
      $this->loadEmbedDonators();
      if(isset($taintedValues['newDonators']))
      {
        foreach($taintedValues['newDonators'] as $key=>$newVal)
        {
          if (!isset($this['newDonators'][$key]))
          {
            $this->addDonators($key,$newVal['people_ref'],$newVal['order_by']);
          }
          $taintedValues['newDonators'][$key]['record_id'] = 0;
        }
      }
    }

    if(!isset($taintedValues['accompanying']))
    {
      $this->offsetUnset('SpecimensAccompanying');
      unset($taintedValues['SpecimensAccompanying']);
      $this->offsetUnset('newSpecimensAccompanying');
      unset($taintedValues['newSpecimensAccompanying']);
    }
    else
    {
      $this->loadEmbedAccompanying();
      if(isset($taintedValues['newSpecimensAccompanying']))
      {
        foreach($taintedValues['newSpecimensAccompanying'] as $key=>$newVal)
        {
          if (!isset($this['newSpecimensAccompanying'][$key]))
          {
            $this->addSpecimensAccompanying($key);
          }
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

    if(!isset($taintedValues['ident']))
    {
      $this->offsetUnset('Identifications');
      unset($taintedValues['Identifications']);
      $this->offsetUnset('newIdentification');
      unset($taintedValues['newIdentification']);
    }
    else
    {
      $this->loadEmbedIndentifications();
      if(isset($taintedValues['newIdentification']))
      {
        foreach($taintedValues['newIdentification'] as $key=>$newVal)
        {
          if (!isset($this['newIdentification'][$key]))
          {
            $this->addIdentifications($key);
            if(isset($taintedValues['newIdentification'][$key]['newIdentifier']))
            {
              foreach($taintedValues['newIdentification'][$key]['newIdentifier'] as $ikey=>$ival)
              {
                if(!isset($this['newIdentification'][$key]['newIdentifier'][$ikey]))
                {
                  $identification = $this->getEmbeddedForm('newIdentification')->getEmbeddedForm($key);
                  $identification->addIdentifiers($ikey,$ival['people_ref'], $ival['order_by']);
                  $this->reembedNewIdentification($identification, $key);
                }
                $taintedValues['newIdentification'][$key]['newIdentifier'][$ikey]['record_id'] = 0;
              }
            }
          }
          elseif(isset($taintedValues['newIdentification'][$key]['newIdentifier']))
          {
            foreach($taintedValues['newIdentification'][$key]['newIdentifier'] as $ikey=>$ival)
            {
              if(!isset($this['newIdentification'][$key]['newIdentifier'][$ikey]))
              {
                $identification = $this->getEmbeddedForm('newIdentification')->getEmbeddedForm($key);
                $identification->addIdentifiers($ikey,$ival['people_ref'], $ival['order_by']);
                $this->reembedNewIdentification($identification, $key);
              }
              $taintedValues['newIdentification'][$key]['newIdentifier'][$ikey]['record_id'] = 0;
            }
          }
          $taintedValues['newIdentification'][$key]['record_id'] = 0;
        }
      }

      if(isset($taintedValues['Identifications']))
      {
        foreach($taintedValues['Identifications'] as $key=>$newval)
        {
          if(isset($newval['newIdentifier']))
          {
            foreach($taintedValues['Identifications'][$key]['newIdentifier'] as $ikey=>$ival)
            {
              if(!isset($this['Identifications'][$key]['newIdentifier'][$ikey]))
              {
                $identification = $this->getEmbeddedForm('Identifications')->getEmbeddedForm($key);
                $identification->addIdentifiers($ikey,$ival['people_ref'], $ival['order_by']);
                $this->reembedIdentifications($identification, $key);
              }
              $taintedValues['Identifications'][$key]['newIdentifier'][$ikey]['record_id'] = 0;
            }
          }
        }
      }
    }

    if(!isset($taintedValues['coll_tools']))
    {
      $this->offsetUnset('collecting_tools_list');
      unset($taintedValues['collecting_tools_list']);
    }
    else
      $this->loadEmbedTools();

    if(!isset($taintedValues['coll_methods']))
    {
      $this->offsetUnset('collecting_methods_list');
      unset($taintedValues['collecting_methods_list']);
    }
    else
      $this->loadEmbedMethods();

    parent::bind($taintedValues, $taintedFiles);
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms && $this->getValue('ident'))
    {
      $value = $this->getValue('newIdentification');
      foreach($this->embeddedForms['newIdentification']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['value_defined']))
        {
          unset($this->embeddedForms['newIdentification'][$name]);
        }
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
          $form->getObject()->save();
          $subvalue = $value[$name]['newIdentifier'];
          foreach($form->embeddedForms['newIdentifier']->getEmbeddedForms() as $subname => $subform)
          {
            if (!isset($subvalue[$subname]['people_ref']))
            {
              unset($form->embeddedForms['newIdentifier'][$subname]);
            }
            else
            {
              $subform->getObject()->setRecordId($form->getObject()->getId());
            }
          }
        }
      }
      $value = $this->getValue('Identifications');
      foreach($this->embeddedForms['Identifications']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['value_defined']))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['Identifications'][$name]);
        }
        else
        {
          $subvalue = $value[$name]['newIdentifier'];
          foreach($form->embeddedForms['newIdentifier']->getEmbeddedForms() as $subname => $subform)
          {
            if (!isset($subvalue[$subname]['people_ref']))
            {
              unset($form->embeddedForms['newIdentifier'][$subname]);
            }
            else
            {
              $subform->getObject()->setRecordId($form->getObject()->getId());
            }
          }
          $subvalue = $value[$name]['Identifiers'];
          foreach($form->embeddedForms['Identifiers']->getEmbeddedForms() as $subname => $subform)
          {
            if (!isset($subvalue[$subname]['people_ref']))
            {
              $subform->getObject()->delete();
              unset($form->embeddedForms['Identifiers'][$subname]);
            }
          }
        }
      }
    }
    if (null === $forms && $this->getValue('code'))
    {
      $value = $this->getValue('newCode');
      $collectionId = $this->getValue('collection_ref');
            $collection = Doctrine::getTable('Collections')->findOneById($collectionId);
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
              $form->getObject()->setCode(Doctrine::getTable('Collections')->getAndUpdateLastCode($collectionId));
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
              $form->getObject()->setCode(Doctrine::getTable('Collections')->getAndUpdateLastCode($collectionId));
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

    if (null === $forms && $this->getValue('collector'))
    {
      $value = $this->getValue('newCollectors');
      foreach($this->embeddedForms['newCollectors']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['people_ref']))
          unset($this->embeddedForms['newCollectors'][$name]);
        else
          $form->getObject()->setRecordId($this->getObject()->getId());
      }
      $value = $this->getValue('Collectors');
      foreach($this->embeddedForms['Collectors']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['people_ref']))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['Collectors'][$name]);
        }
      }
    }


    if (null === $forms && $this->getValue('donator'))
    {
      $value = $this->getValue('newDonators');
      foreach($this->embeddedForms['newDonators']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['people_ref']))
          unset($this->embeddedForms['newDonators'][$name]);
        else
          $form->getObject()->setRecordId($this->getObject()->getId());
      }
      $value = $this->getValue('Donators');
      foreach($this->embeddedForms['Donators']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['people_ref']))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['Donators'][$name]);
        }
      }
    }

    if (null === $forms && $this->getValue('accompanying'))
    {
      $value = $this->getValue('newSpecimensAccompanying');
      foreach($this->embeddedForms['newSpecimensAccompanying']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['taxon_ref']) && !isset($value[$name]['mineral_ref']))
          unset($this->embeddedForms['newSpecimensAccompanying'][$name]);
      }
      $value = $this->getValue('SpecimensAccompanying');
      foreach($this->embeddedForms['SpecimensAccompanying']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['taxon_ref']) && !isset($value[$name]['mineral_ref']))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['SpecimensAccompanying'][$name]);
        }
      }
    }
    if (null === $forms && $this->getValue('comment'))
    {
      $value = $this->getValue('newComments');
      foreach($this->embeddedForms['newComments']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['comment'] ))
          unset($this->embeddedForms['newComments'][$name]);
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
	      if (!isset($value[$name]['url']) || $value[$name]['url'] == '')
	      {
	        $form->getObject()->delete();
	        unset($this->embeddedForms['ExtLinks'][$name]);
	      }
	    }
    }       
    return parent::saveEmbeddedForms($con, $forms);
  }
}
