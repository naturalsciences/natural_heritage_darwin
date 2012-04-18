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

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
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
    $this->widgetSchema['ident'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['extlink'] = new sfWidgetFormInputHidden(array('default'=>1));

    /*Input file for related files*/
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));

    /* Labels */
    $this->widgetSchema->setLabels(array('host_specimen_ref' => 'Host specimen',
                                         'host_relationship' => 'Relationship',
                                         'host_taxon_ref' => 'Host Taxon',
                                         'gtu_ref' => 'Sampling location Tags',
                                         'station_visible' => 'Public sampling location ?',
                                         'filenames' => 'Add File',
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


    $this->validatorSchema['ident'] = new sfValidatorPass();

    $this->validatorSchema['accompanying'] = new sfValidatorPass();

    $this->validatorSchema['coll_tools'] = new sfValidatorPass();

    $this->validatorSchema['coll_methods'] = new sfValidatorPass();
    //Loan form is submited to upload file, when called like that we don't want some fields to be required
    $this->validatorSchema['filenames'] = new sfValidatorPass();

    $this->widgetSchema['Biblio_holder'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->validatorSchema['Biblio_holder'] = new sfValidatorPass();
    
    $this->validatorSchema['Collectors_holder'] = new sfValidatorPass();
    $this->widgetSchema['Collectors_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['Donators_holder'] = new sfValidatorPass();
    $this->widgetSchema['Donators_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['Codes_holder'] = new sfValidatorPass();
    $this->widgetSchema['Codes_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['Comments_holder'] = new sfValidatorPass();
    $this->widgetSchema['Comments_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['ExtLinks_holder'] = new sfValidatorPass();
    $this->widgetSchema['ExtLinks_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['RelatedFiles_holder'] = new sfValidatorPass();
    $this->widgetSchema['RelatedFiles_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['SpecimensAccompanying_holder'] = new sfValidatorPass();
    $this->widgetSchema['SpecimensAccompanying_holder'] = new sfWidgetFormInputHidden(array('default'=>1));
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

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    /* For each embedded informations or many-to-many data such as collecting tools and methods
     * test if the widget is on screen by testing a flag field present on the concerned widget
     * If widget is not on screen, remove the field from list of fields to be bound, and than potentially saved
    */
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

    $this->bindEmbed('Biblio', 'addBiblio' , $taintedValues);
    $this->bindEmbed('Collectors', 'addCollectors' , $taintedValues);
    $this->bindEmbed('Donators', 'addDonators' , $taintedValues);
    $this->bindEmbed('Codes', 'addCodes' , $taintedValues);
    $this->bindEmbed('Comments', 'addComments' , $taintedValues);
    $this->bindEmbed('ExtLinks', 'addExtLinks' , $taintedValues);
    $this->bindEmbed('RelatedFiles', 'addRelatedFiles' , $taintedValues);
    $this->bindEmbed('SpecimensAccompanying', 'addSpecimensAccompanying' , $taintedValues);
    parent::bind($taintedValues, $taintedFiles);
  }


  public function addSpecimensAccompanying($num, $values, $order_by=0)
  {
    $options = array('unit' => '%', 'specimen_ref' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('SpecimensAccompanying', new SpecimensAccompanyingForm(DarwinTable::newObjectFromArray('SpecimensAccompanying',$options)), $num);
  }

  public function addRelatedFiles($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimens', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('RelatedFiles', new MultimediaForm(DarwinTable::newObjectFromArray('Multimedia',$options)), $num);
  }

  public function addComments($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimens', 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Comments', new CommentsSubForm(DarwinTable::newObjectFromArray('Comments',$options)), $num);
  }

  public function addBiblio($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimens', 'bibliography_ref' => $values['bibliography_ref'], 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Biblio', new BiblioAssociationsForm(DarwinTable::newObjectFromArray('CatalogueBibliography',$options)), $num);
  }

  public function addCollectors($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimens', 'people_type' => 'collector', 'people_ref' => $values['people_ref'], 'order_by' => $order_by,
      'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Collectors', new PeopleAssociationsForm(DarwinTable::newObjectFromArray('CataloguePeople',$options)), $num);
  }

  public function addDonators($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimens', 'people_type' => 'donator', 'people_ref' => $values['people_ref'], 'order_by' => $order_by,
      'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Donators', new PeopleAssociationsForm(DarwinTable::newObjectFromArray('CataloguePeople',$options)), $num);
  }

  public function addCodes($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimens', 'record_id' => $this->getObject()->getId());
    if(isset($values['collection_ref']))
      $col = $values['collection_ref'];
    else
      $col = $this->getObject()->getCollectionRef();

    $collection = Doctrine::getTable('Collections')->find($col);
    if($collection)
    {
      $options['code_prefix'] = $collection->getCodePrefix();
      $options['code_prefix_separator'] = $collection->getCodePrefixSeparator();
      $options['code_suffix'] = $collection->getCodeSuffix();
      $options['code_suffix_separator'] = $collection->getCodeSuffixSeparator();
    }
    $this->attachEmbedRecord('Codes', new CodesForm(DarwinTable::newObjectFromArray('Codes',$options)), $num);
  }

  public function addExtLinks($num, $obj=null)
  {
    $options = array('referenced_relation' => 'specimens', 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('ExtLinks', new ExtLinksForm(DarwinTable::newObjectFromArray('ExtLinks',$options)), $num);
  }

  public function getEmbedRecords($emFieldName, $record_id = false)
  {
    if($record_id === false)
      $record_id = $this->getObject()->getId();
    if( $emFieldName =='Biblio' )
      return Doctrine::getTable('CatalogueBibliography')->findForTable('specimens', $record_id);
    if( $emFieldName =='Collectors' )
      return Doctrine::getTable('CataloguePeople')->getPeopleRelated('specimens','collector', $record_id);
    if( $emFieldName =='Donators' )
      return Doctrine::getTable('CataloguePeople')->getPeopleRelated('specimens','donator', $record_id);
    if( $emFieldName =='Codes' )
      return Doctrine::getTable('Codes')->getCodesRelated('specimens', $record_id);
    if( $emFieldName =='Comments' )
      return Doctrine::getTable('Comments')->findForTable('specimens', $record_id);
    if( $emFieldName =='ExtLinks' )
      return Doctrine::getTable('ExtLinks')->findForTable('specimens', $record_id);
    if( $emFieldName =='RelatedFiles' )
      return Doctrine::getTable('Multimedia')->findForTable('specimens', $record_id);
    if( $emFieldName =='SpecimensAccompanying' )
      return Doctrine::getTable('SpecimensAccompanying')->findBySpecimenRef($record_id);
  }

  public function getEmbedRelationForm($emFieldName, $values)
  {
    if( $emFieldName =='Biblio' )
      return new BiblioAssociationsForm($values);
    if( $emFieldName =='Collectors' || $emFieldName =='Donators' )
      return new PeopleAssociationsForm($values);
    if( $emFieldName =='Codes' )
      return new CodesForm($values);
    if( $emFieldName =='Comments' )
      return new CommentsSubForm($values);
    if( $emFieldName =='ExtLinks' )
      return new ExtLinksForm($values);
    if( $emFieldName =='RelatedFiles' )
      return new MultimediaForm($values);
    if( $emFieldName =='SpecimensAccompanying' )
      return new SpecimensAccompanyingForm($values);
  }

  public function duplicate($id)
  {
    // reembed duplicated collector
    $Catalogue = Doctrine::getTable('CataloguePeople')->findForTableByType('specimens',$id) ;

    if(isset($Catalogue['collector'])) {
      foreach ($Catalogue['collector'] as $key=>$val) {
        $this->addCollectors($key, array('people_ref' => $val->getPeopleRef()),$val->getOrderBy());
      }
    }
    if(isset($Catalogue['donator'])) {
      foreach ($Catalogue['donator'] as $key=>$val) {
        $this->addDonators($key, array('people_ref' => $val->getPeopleRef()),$val->getOrderBy());
      }
    }

    //reembed biblio
    $bib =  $this->getEmbedRecords('Biblio', $id);
    foreach($bib as $key=>$vals) {
      $this->addBiblio($key, array('bibliography_ref' => $vals->getBibliographyRef()) );
    }

    // reembed duplicated comment
    $Comments = Doctrine::getTable('Comments')->findForTable('specimens', $id) ;
    foreach ($Comments as $key=>$val)
    {
      $comment = new Comments();
      $comment->fromArray($val->toArray());
      $form = new CommentsSubForm($comment);
      $this->attachEmbedRecord('Comments', $form, $key);
    }

    // reembed duplicated external url
    $ExtLinks = Doctrine::getTable('ExtLinks')->findForTable('specimens', $id) ;
    foreach ($ExtLinks as $key=>$val)
    {
      $links = new ExtLinks() ;
      $links->fromArray($val->toArray());
      $form = new ExtLinksForm($links);
      $this->attachEmbedRecord('ExtLinks', $form, $key);
    } 

    // reembed duplicated specimen Accompanying
    $spec_a = Doctrine::getTable('SpecimensAccompanying')->findBySpecimen($id) ;
    foreach ($spec_a as $key=>$val)
    {
      $spec = new SpecimensAccompanying() ;
      $spec->fromArray($val->toArray());
      $form = new SpecimensAccompanyingForm($spec);
      $this->attachEmbedRecord('SpecimensAccompanying', $key, $spec) ;
    }

  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    $this->saveEmbed('Biblio', 'bibliography_ref', $forms, array('referenced_relation'=>'specimens', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Collectors', 'people_ref', $forms, array('referenced_relation'=>'specimens', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Donators', 'people_ref', $forms, array('referenced_relation'=>'specimens', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Codes', 'code' ,$forms, array('referenced_relation'=>'specimens', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Comments', 'comment' ,$forms, array('referenced_relation'=>'specimens', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('ExtLinks', 'url' ,$forms, array('referenced_relation'=>'specimens', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('RelatedFiles', 'mime_type' ,$forms, array('referenced_relation'=>'specimens', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('SpecimensAccompanying', 'taxon_ref' ,$forms, array('specimen_ref' => $this->getObject()->getId()));

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
