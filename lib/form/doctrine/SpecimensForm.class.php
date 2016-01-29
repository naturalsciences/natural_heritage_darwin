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
      'acquisition_category',
      'acquisition_date',
      'station_visible',
      'ig_ref',

      'type', 'sex', 'state','stage','social_status','rock_form',
      'specimen_part', 'complete', 'institution_ref', 'building', 'floor', 'room',
      'row', 'col', 'shelf', 'container', 'sub_container', 'container_type', 'sub_container_type',
      'container_storage', 'sub_container_storage', 'surnumerary', 'specimen_status',
      'specimen_count_min', 'specimen_count_max','object_name'
    ));

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

    $this->widgetSchema['category'] = new sfWidgetFormChoice(array(
      'choices' => Specimens::getCategories(),
    ));

    $this->validatorSchema['category'] = new sfValidatorChoice(array('choices'=>array_keys(Specimens::getCategories())));

    /* Collection Reference */
    $this->widgetSchema['collection_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Collections',
      'link_url' => 'collection/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Collection'),
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=collections',
    ));

    /* Expedition Reference */
    $this->widgetSchema['expedition_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Expeditions',
      'link_url' => 'expedition/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Expedition'),
      'nullable' => true,
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=expeditions',
    ));

    /* Taxonomy Reference */
    $this->widgetSchema['taxon_ref'] = new widgetFormCompleteButtonRef(array(
       'model' => 'Taxonomy',
       'link_url' => 'taxonomy/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Taxon'),
       'nullable' => true,
       'button_class'=>'',
       'complete_url' => 'catalogue/completeName?table=taxonomy',
    ));

    /* Chronostratigraphy Reference */
    $this->widgetSchema['chrono_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Chronostratigraphy',
      'link_url' => 'chronostratigraphy/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Chronostratigraphic unit'),
      'nullable' => true,
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=chronostratigraphy',
     ));

    /* Lithostratigraphy Reference */
    $this->widgetSchema['litho_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Lithostratigraphy',
      'link_url' => 'lithostratigraphy/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Lithostratigraphic unit'),
      'nullable' => true,
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=lithostratigraphy',
     ));

    /* Lithology Reference */
    $this->widgetSchema['lithology_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Lithology',
      'link_url' => 'lithology/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Lithologic unit'),
      'nullable' => true,
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=lithology',
     ));

    /* Mineralogy Reference */
    $this->widgetSchema['mineral_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Mineralogy',
      'link_url' => 'mineralogy/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Mineralogic unit'),
      'nullable' => true,
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=mineralogy',
     ));

    /* IG number Reference */
    $this->widgetSchema['ig_ref'] = new widgetFormInputChecked(array(
      'model' => 'Igs',
      'method' => 'getIgNum',
      'nullable' => true,
      'link_url' => 'igs/searchFor',
      'notExistingAddTitle' => $this->getI18N()->__('This I.G. number does not exist. Would you like to automatically insert it ?'),
      'notExistingAddValues' => array(
          $this->getI18N()->__('No'),
          $this->getI18N()->__('Yes')
        ),
    ));

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

    $this->widgetSchema['coll_methods'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['coll_tools'] = new sfWidgetFormInputHidden(array('default'=>1));

    /* Acquisition categories */
    $this->widgetSchema['acquisition_category'] = new sfWidgetFormChoice(array(
      'choices' =>  SpecimensTable::getDistinctCategories(),
    ));

    $this->widgetSchema['acquisition_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );

    $this->widgetSchema['relationship'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->widgetSchema['ident'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['extlink'] = new sfWidgetFormInputHidden(array('default'=>1));

    /*Input file for related files*/
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));


    $this->widgetSchema['type'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctTypes',
      'method' => 'getType',
      'key_method' => 'getType',
      'add_empty' => false,
      'change_label' => 'Pick a type in the list',
      'add_label' => 'Add an other type',
    ));
    $this->widgetSchema['sex'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSexes',
      'add_empty' => false,
      'change_label' => 'Pick a sex in the list',
      'add_label' => 'Add an other sex',
    ));
    $this->widgetSchema['state'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctStates',
      'method' => 'getState',
      'key_method' => 'getState',
      'add_empty' => false,
      'change_label' => 'Pick a "sexual" state in the list',
      'add_label' => 'Add an other "sexual" state',
    ));
    $this->widgetSchema['stage'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctStages',
      'method' => 'getStage',
      'key_method' => 'getStage',
      'add_empty' => false,
      'change_label' => 'Pick a stage in the list',
      'add_label' => 'Add an other stage',
    ));
    $this->widgetSchema['social_status'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSocialStatuses',
      'method' => 'getSocialStatus',
      'key_method' => 'getSocialStatus',
      'add_empty' => false,
      'change_label' => 'Pick a social status in the list',
      'add_label' => 'Add an other social status',
    ));
    $this->widgetSchema['rock_form'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctRockForms',
      'method' => 'getRockForm',
      'key_method' => 'getRockForm',
      'add_empty' => false,
      'change_label' => 'Pick a rock form in the list',
      'add_label' => 'Add another rock form',
    ));


    $this->widgetSchema['specimen_part'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctParts',
      'method' => 'getSpecimenPart',
      'key_method' => 'getSpecimenPart',
      'add_empty' => false,
      'change_label' => 'Pick parts in the list',
      'add_label' => 'Add another part',
    ));

    $this->widgetSchema['institution_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Institutions',
      'link_url' => 'institution/choose?with_js=1',
      'method' => 'getFamilyName',
      'box_title' => $this->getI18N()->__('Choose Institution'),
      'complete_url' => 'catalogue/completeName?table=institutions',
      'nullable' => true,
    ));
    $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required'=>false));
    if(sfConfig::get('dw_defaultInstitutionRef')) {
      $this->setDefault('institution_ref', sfConfig::get('dw_defaultInstitutionRef'));
    }

    $this->widgetSchema['building'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctBuildings',
      'method' => 'getBuildings',
      'key_method' => 'getBuildings',
      'add_empty' => true,
      'change_label' => 'Pick a building in the list',
      'add_label' => 'Add another building',
    ));

    $this->widgetSchema['floor'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctFloors',
      'method' => 'getFloors',
      'key_method' => 'getFloors',
      'add_empty' => true,
      'change_label' => 'Pick a floor in the list',
      'add_label' => 'Add another floor',
    ));

    $this->widgetSchema['row'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctRows',
      'method' => 'getRows',
      'key_method' => 'getRows',
      'add_empty' => true,
      'change_label' => 'Pick a row in the list',
      'add_label' => 'Add another row',
    ));


    $this->widgetSchema['col'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctCols',
      'method' => 'getCols',
      'key_method' => 'getCols',
      'add_empty' => true,
      'change_label' => 'Pick a col in the list',
      'add_label' => 'Add another col',
    ));

    $this->widgetSchema['room'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctRooms',
      'method' => 'getRooms',
      'key_method' => 'getRooms',
      'add_empty' => true,
      'change_label' => 'Pick a room in the list',
      'add_label' => 'Add another room',
    ));

    $this->widgetSchema['shelf'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctShelfs',
      'method' => 'getShelfs',
      'key_method' => 'getShelfs',
      'add_empty' => true,
      'change_label' => 'Pick a shelf in the list',
      'add_label' => 'Add another shelf',
    ));

    $this->widgetSchema['container_type'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctContainerTypes',
      'method' => 'getContainerType',
      'key_method' => 'getContainerType',
      'add_empty' => true,
      'change_label' => 'Pick a container in the list',
      'add_label' => 'Add another container',
      ));

    $this->widgetSchema['sub_container_type'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSubContainerTypes',
      'method' => 'getSubContainerType',
      'key_method' => 'getSubContainerType',
      'add_empty' => true,
      'change_label' => 'Pick a sub container type in the list',
      'add_label' => 'Add another sub container type',
    ));

    $this->widgetSchema['specimen_status'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctStatus',
      'method' => 'getSpecimenStatus',
      'key_method' => 'getSpecimenStatus',
      'add_empty' => true,
      'change_label' => 'Pick a status in the list',
      'add_label' => 'Add another status',
    ));

    $this->widgetSchema['container'] = new sfWidgetFormInput();
    $this->widgetSchema['sub_container'] = new sfWidgetFormInput();
    $this->widgetSchema['object_name'] = new sfWidgetFormInput();

    $this->widgetSchema['container_storage'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'change_label' => 'Pick a container storage in the list',
      'add_label' => 'Add another container storage',
    ));

    $this->widgetSchema['sub_container_storage'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'change_label' => 'Pick a sub container storage in the list',
      'add_label' => 'Add another sub container storage',
    ));

    $this->widgetSchema['accuracy'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));

    $this->setDefault('accuracy', 1);
    $this->validatorSchema['accuracy'] = new sfValidatorPass();

    /* Labels */
    $this->widgetSchema->setLabels(array(
      'gtu_ref' => 'Sampling location Tags',
      'station_visible' => 'Public sampling location ?',
      'filenames' => 'Add File',
      'institution_ref' => 'Institution',
      'accuracy' => 'Accuracy',
      'surnumerary' => 'supernumerary',
      'col' => 'Column',
    ));

    /* Validators */
    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['object_name'] = new sfValidatorString(array('required' => false, 'trim' => true));

    $this->validatorSchema['extlink'] = new sfValidatorPass();

    $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));

    $this->validatorSchema['expedition_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['taxon_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['chrono_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['litho_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['lithology_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['mineral_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['gtu_ref'] = new sfValidatorInteger(array('required'=>false));

    $this->validatorSchema['type'] = new sfValidatorString(array('trim'=>true, 'required'=>false, 'empty_value'=>$this->getDefault('type')));
    $this->validatorSchema['sex'] = new sfValidatorString(array('trim'=>true, 'required'=>false, 'empty_value'=>$this->getDefault('sex')));
    $this->validatorSchema['stage'] = new sfValidatorString(array('trim'=>true, 'required'=>false, 'empty_value'=>$this->getDefault('stage')));
    $this->validatorSchema['state'] = new sfValidatorString(array('trim'=>true, 'required'=>false, 'empty_value'=>$this->getDefault('state')));
    $this->validatorSchema['social_status'] = new sfValidatorString(array('trim'=>true, 'required'=>false, 'empty_value'=>$this->getDefault('social_status')));
    $this->validatorSchema['rock_form'] = new sfValidatorString(array('trim'=>true, 'required'=>false, 'empty_value'=>$this->getDefault('rock_form')));


    $this->validatorSchema['acquisition_category'] = new sfValidatorChoice(array(
      'choices' => array_keys(SpecimensTable::getDistinctCategories()),
      'required' => false,
    ));

    $this->validatorSchema['acquisition_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',
    ));


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

    $this->validatorSchema['relationships'] = new sfValidatorPass();

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

    $this->validatorSchema['SpecimensRelationships_holder'] = new sfValidatorPass();
    $this->widgetSchema['SpecimensRelationships_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['Insurances_holder'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->validatorSchema['Insurances_holder'] = new sfValidatorPass();

    $this->mergePostValidator(new sfValidatorSchemaCompare('specimen_count_min', '<=', 'specimen_count_max',
      array(),
      array('invalid' => 'The min number ("%left_field%") must be lower or equal the max number ("%right_field%")' )
    ));
  }

  public function forceContainerChoices()
  {
    $this->widgetSchema['container_storage']->setOption('forced_choices',
      Doctrine::getTable('Specimens')->getDistinctContainerStorages($this->getObject()->getContainerType())
    );

    $this->widgetSchema['sub_container_storage']->setOption('forced_choices',
      Doctrine::getTable('Specimens')->getDistinctSubContainerStorages($this->getObject()->getSubContainerType())
    );
  }

  public function addIdentifications($num, $order_by=0, $obj=null)
  {
    if(! isset($this['newIdentification']))
      $this->loadEmbedIndentifications();

    $options = array('referenced_relation' => 'specimens', 'order_by' => $order_by);
    if (!$obj)
      $val = new Identifications();
    else
      $val = $obj ;

    $val->fromArray($options);
    $val->setRecordId($this->getObject()->getId());
    $form = new IdentificationsForm($val);
    $this->embeddedForms['newIdentification']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('newIdentification', $this->embeddedForms['newIdentification']);
  }

  public function addInsurances($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimens', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('Insurances', new InsurancesSubForm(DarwinTable::newObjectFromArray('Insurances',$options)), $num);
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

      'Ig' => array(
        'ig_ref',
      ),
      'Gtu' => array(
        'gtu_ref',
        'station_visible',
      ),
      'Tool' => array('collecting_tools_list'),
      'Method' => array('collecting_methods_list'),

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
        'col',
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
        'specimen_count_min',
        'specimen_count_max',
      ),
      'Type' => array('type'),
      'Sex' => array('sex', 'state'),
      'Stage' => array('stage'),
      'Social' => array('social_status'),
      'Rock' => array('rock_form'),
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
    $this->bindEmbed('SpecimensRelationships', 'addSpecimensRelationships' , $taintedValues);
    $this->bindEmbed('Insurances', 'addInsurances' , $taintedValues);

    // Unset not used widgets
    $fields_groups = $this->getFieldsByGroup();
    foreach($fields_groups as $group) {
      $cnt_unset = 0;
      foreach($group as $field) {
        if(!isset($taintedValues[$field])) {
          $cnt_unset++;
        }
        if($cnt_unset == count($group)) {
          foreach($group as $ufield) {
            $this->offsetUnset($ufield);
          }
        }
      }
    }

    //Little hack to make default value work if widget is not there
    if(!isset($taintedValues['institution_ref']) && $this->object->isNew()) {
      if(sfConfig::get('dw_defaultInstitutionRef')) {
        $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required'=>true));
        $taintedValues['institution_ref'] = sfConfig::get('dw_defaultInstitutionRef');
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }


  public function addSpecimensRelationships($num, $values, $order_by=0)
  {
    $options = array('unit' => '%', 'specimen_ref' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('SpecimensRelationships', new SpecimensRelationshipsForm(DarwinTable::newObjectFromArray('SpecimensRelationships',$options)), $num);
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

    if($col != '') {
      $collections = Doctrine::getTable('Collections');
      $collection = $collections->findOneById($col);
      if($collection)
      {
        $options['code_prefix'] = $collection->getCodePrefix();
        $options['code_prefix_separator'] = $collection->getCodePrefixSeparator();
        if($collection->getCodeAutoIncrement() && (empty($values['code']) || $values['code'] == ''))
          $options['code'] = $collections->getAndUpdateLastCode($collection->getId());
        elseif (!empty($values['code']) && $values['code'] != '')
          $options['code'] = $values['code'];
        $options['code_suffix'] = $collection->getCodeSuffix();
        $options['code_suffix_separator'] = $collection->getCodeSuffixSeparator();
      }
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
    if( $emFieldName =='SpecimensRelationships' )
      return Doctrine::getTable('SpecimensRelationships')->findBySpecimenRef($record_id);
    if( $emFieldName =='Insurances' )
      return Doctrine::getTable('Insurances')->findForTable('specimens', $record_id);
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
    if( $emFieldName =='SpecimensRelationships' )
      return new SpecimensRelationshipsForm($values);
    if( $emFieldName =='Insurances' )
      return new InsurancesSubForm($values);
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

    //Assume the collection_ref is set
    $collection  = Doctrine::getTable('Collections')->find($this->getObject()->getCollectionRef());
    if( $collection->getCodeSpecimenDuplicate())
    {
      $Codes = Doctrine::getTable('Codes')->getCodesRelatedArray('specimens',$id) ;
      foreach ($Codes as $key=> $code)
      {
        $newCode = new Codes();
        $newCode->fromArray($code->toArray());
        $form = new CodesForm($newCode);
        $this->attachEmbedRecord('Codes', $form, $key);
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

    // reembed duplicated specimen Relationships
    $spec_a = Doctrine::getTable('SpecimensRelationships')->findBySpecimen($id) ;
    foreach ($spec_a as $key=>$val)
    {
      $spec = new SpecimensRelationships() ;
      $spec->fromArray($val->toArray());
      $form = new SpecimensRelationshipsForm($spec);
      $this->attachEmbedRecord('SpecimensRelationships', $form, $spec) ;
    }

    // reembed duplicated insurances
    $Insurances = Doctrine::getTable('Insurances')->findForTable('specimens',$id) ;
    foreach ($Insurances as $key=>$val)
    {
      $insurance = new Insurances() ;
      $insurance->fromArray($val->toArray());
      $form = new InsurancesSubForm($insurance);
      $this->attachEmbedRecord('Insurances', $form, $key);
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
    $this->saveEmbed('SpecimensRelationships', 'unit_type' ,$forms, array('specimen_ref' => $this->getObject()->getId()));
    $this->saveEmbed('Insurances', 'insurance_value' ,$forms, array('referenced_relation'=>'specimens', 'record_id' => $this->getObject()->getId()));

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
    $javascripts[]='/js/ui.complete.js';
	  $javascripts[]='/js/jquery.inputmask.js';
	  $javascripts[]='/js/inputmask.js';
    return $javascripts;
  }

  public function getStylesheets()
  {
    $javascripts=parent::getStylesheets();
    $javascripts['/css/ui.datepicker.css']='all';
    return $javascripts;
  }
}
