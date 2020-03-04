<?php

/**
 * SpecimensFlat filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SpecimensFormFilter extends BaseSpecimensFormFilter
{
  public function configure()
  {
    $this->with_group = false;
    $this->useFields(array('gtu_ref','gtu_code','gtu_from_date','gtu_to_date', 'taxon_level_ref', 'litho_name', 'litho_level_ref', 'litho_level_name', 'chrono_name', 'chrono_level_ref',
        'chrono_level_name', 'lithology_name', 'lithology_level_ref', 'lithology_level_name', 'mineral_name', 'mineral_level_ref',
        'mineral_level_name','ig_num','acquisition_category','acquisition_date',
        'import_ref','ig_ref',
		//JMherpers 2019 04 25
		'nagoya'));

    $this->addPagerItems();

    //ftheeten 2019 01 24
    $this->widgetSchema['gtu_ref'] = new sfWidgetFormInputText();
    
    $this->widgetSchema['gtu_code'] = new sfWidgetFormInputText();
    $this->widgetSchema['expedition_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    //ftheeten 2018 08 09
     $this->widgetSchema['expedition_name']->setAttributes(array('class'=>'autocomplete_for_expeditions'));
    $this->widgetSchema['taxon_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size taxon_name'));
    $this->widgetSchema['taxon_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(
      array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'taxonomy'))),
        'add_empty' => $this->getI18N()->__('All')
      ),
      array(
        'class'=>'taxon_name'
      )
    );
    $rel = array('child'=>'Is a Child Of','direct_child'=>'Is a Direct Child','synonym'=> 'Is a Synonym Of', 'equal' => 'Is strictly equal to');

   
    //ftheeten 2016 03 24
    $this->widgetSchema['taxon_relation'] = new sfWidgetFormChoice(array('choices'=> $rel,'expanded'=> true, 'multiple'=>true));

    $this->widgetSchema['taxon_relation']->setDefault('child');
    $this->widgetSchema['taxon_item_ref'] = new widgetFormCompleteButtonRef(
      array(
        'model' => 'Taxonomy',
        'method' => 'getName',
        'link_url' => 'taxonomy/choose',
        'box_title' => $this->getI18N()->__('Choose Taxon'),
        'button_is_hidden' => true,
        'complete_url' => 'catalogue/completeName?table=taxonomy&level=1',
        'nullable' => true,
        'field_to_clean_class' => 'taxon_name'
      ),
      array('class'=>'taxon_autocomplete')
    );
    $this->widgetSchema['taxon_child_syn_included'] = new WidgetFormInputCheckboxDarwin();
    $this->widgetSchema['taxon_child_syn_included']->setOption('label','Syn. included ?');

    $this->validatorSchema['taxon_item_ref'] = new sfValidatorInteger(array('required'=>false));
    //ftheeten 2016 03 24
    $this->validatorSchema['taxon_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel), 'multiple'=>true));

    $this->validatorSchema['taxon_child_syn_included'] = new sfValidatorBoolean();

    $this->widgetSchema['lithology_relation'] = new sfWidgetFormChoice(array('choices'=> $rel,'expanded'=> true));
    $this->widgetSchema['lithology_relation']->setDefault('child');
    $this->widgetSchema['lithology_item_ref'] = new widgetFormCompleteButtonRef(
      array(
        'model' => 'Lithology',
        'link_url' => 'lithology/choose',
        'method' => 'getName',
        'box_title' => $this->getI18N()->__('Choose Lithologic unit'),
        'button_is_hidden' => true,
        'complete_url' => 'catalogue/completeName?table=lithology',
        'nullable' => true,
        'field_to_clean_class' => 'lithology_name'
        ),
      array('class'=>'lithology_autocomplete')
    );
    $this->widgetSchema['lithology_child_syn_included'] = new WidgetFormInputCheckboxDarwin();
    $this->widgetSchema['lithology_child_syn_included']->setOption('label','Syn. included ?');

    $this->validatorSchema['lithology_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['lithology_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));
    $this->validatorSchema['lithology_child_syn_included'] = new sfValidatorBoolean();


    $this->widgetSchema['lithology_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size lithology_name'));
    $this->widgetSchema['lithology_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(
      array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'lithology'))),
        'add_empty' => $this->getI18N()->__('All')
      ),
      array(
        'class'=>'lithology_name'
      )
    );

    $this->widgetSchema['litho_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size litho_name'));
    $this->widgetSchema['litho_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(
      array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'lithostratigraphy'))),
        'add_empty' => $this->getI18N()->__('All')
      ),
      array(
        'class'=>'litho_name'
      )
    );

    $this->widgetSchema['litho_relation'] = new sfWidgetFormChoice(array('choices'=> $rel,'expanded'=> true, 'multiple'=> true));
    $this->widgetSchema['litho_relation']->setDefault('child');
    $this->widgetSchema['litho_item_ref'] = new widgetFormCompleteButtonRef(
      array(
        'model' => 'Lithostratigraphy',
        'link_url' => 'lithostratigraphy/choose',
        'method' => 'getName',
        'box_title' => $this->getI18N()->__('Choose Lithostratigraphic unit'),
        'button_is_hidden' => true,
        'complete_url' => 'catalogue/completeName?table=lithostratigraphy',
        'nullable' => true,
        'field_to_clean_class' => 'litho_name'
        ),
      array('class'=>'litho_autocomplete')
    );
    $this->widgetSchema['litho_child_syn_included'] = new WidgetFormInputCheckboxDarwin();
    $this->widgetSchema['litho_child_syn_included']->setOption('label','Syn. included ?');

    $this->validatorSchema['litho_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['litho_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel), 'multiple'=> true));
    $this->validatorSchema['litho_child_syn_included'] = new sfValidatorBoolean();

    $this->widgetSchema['chrono_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size chrono_name'));
    $this->widgetSchema['chrono_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(
      array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'chronostratigraphy'))),
        'add_empty' => $this->getI18N()->__('All')
      ),
      array(
        'class'=>'chrono_name'
      )
    );

    $this->widgetSchema['chrono_relation'] = new sfWidgetFormChoice(array('choices'=> $rel,'expanded'=> true, 'multiple'=> true));
    $this->widgetSchema['chrono_relation']->setDefault('child');

    $this->widgetSchema['chrono_item_ref'] = new widgetFormCompleteButtonRef(
      array(
        'model' => 'Chronostratigraphy',
        'link_url' => 'chronostratigraphy/choose',
        'method' => 'getName',
        'box_title' => $this->getI18N()->__('Choose Chronostratigraphic unit'),
        'nullable' => true,
        'button_is_hidden' => true,
        'complete_url' => 'catalogue/completeName?table=chronostratigraphy',
        'button_class'=>'',
        'field_to_clean_class' => 'chrono_name'
      ),
      array('class'=>'chrono_autocomplete')
    );
    $this->widgetSchema['chrono_child_syn_included'] = new WidgetFormInputCheckboxDarwin();
    $this->widgetSchema['chrono_child_syn_included']->setOption('label','Syn. included ?');

    $this->validatorSchema['chrono_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['chrono_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel), 'multiple'=>true));
    $this->validatorSchema['chrono_child_syn_included'] = new sfValidatorBoolean();

    $this->widgetSchema['mineral_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size mineral_name'));
    $this->widgetSchema['mineral_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(
      array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'mineralogy'))),
        'add_empty' => $this->getI18N()->__('All')
      ),
      array(
        'class'=>'mineral_name'
      )
    );

    $this->widgetSchema['mineral_item_ref'] = new widgetFormCompleteButtonRef(
      array(
        'model' => 'Mineralogy',
        'link_url' => 'mineralogy/choose',
        'method' => 'getName',
        'box_title' => $this->getI18N()->__('Choose Mineralogic unit'),
        'nullable' => true,
        'button_is_hidden' => true,
        'complete_url' => 'catalogue/completeName?table=mineralogy',
        'button_class'=>'',
        'field_to_clean_class' => 'mineral_name'
        ),
      array('class'=>'mineral_autocomplete')
    );
    $this->widgetSchema['mineral_child_syn_included'] = new WidgetFormInputCheckboxDarwin();
    $this->widgetSchema['mineral_child_syn_included']->setOption('label','Syn. included ?');

    $this->widgetSchema['mineral_relation'] = new sfWidgetFormChoice(array('choices'=> $rel,'expanded'=> true));
    $this->widgetSchema['mineral_relation']->setDefault('child');

    $this->validatorSchema['mineral_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['mineral_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));
    $this->validatorSchema['mineral_child_syn_included'] = new sfValidatorBoolean();

    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();
    $this->widgetSchema['ig_from_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );

    $this->widgetSchema['ig_to_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'to_date')
    );

    $this->widgetSchema['ig_num']->setAttributes(array('class'=>'small_size'));
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['ig_from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );
	
	    //ftheeten 2018 05 29
	$this->widgetSchema['ig_num_contains'] = new sfWidgetFormInputCheckbox();
  	////ftheeten 2018 05 29
	$this->validatorSchema['ig_num_contains'] = new sfValidatorPass();
	

    $this->validatorSchema['ig_to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare(
      'ig_from_date',
      '<=',
      'ig_to_date',
      array('throw_global_error' => true),
      array('invalid'=>'The "begin" date cannot be above the "end" date.')
    ));


    $this->widgetSchema['col_fields'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['collection_ref'] = new sfWidgetCollectionList(array('choices' => array()));
    $this->widgetSchema['collection_ref']->addOption('public_only',false);
    $this->validatorSchema['collection_ref'] = new sfValidatorPass(); //Avoid duplicate the query
    $this->widgetSchema['spec_ids'] = new sfWidgetFormTextarea(array('label'=>"#ID list separated by ',' "));

    $this->validatorSchema['spec_ids'] = new sfValidatorString( array(
      'required' => false,
      'trim' => true
    ));
    
    //ftheeten 2018 05 29
	$this->widgetSchema['include_sub_collections'] = new sfWidgetFormInputCheckbox();
  	////ftheeten 2018 05 29
	$this->validatorSchema['include_sub_collections'] = new sfValidatorPass();
	

    $this->validatorSchema['col_fields'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));

    $this->validatorSchema['gtu_code'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));
	
	//madam 2019 01 28
	$this->validatorSchema['gtu_ref'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));

    $this->validatorSchema['expedition_name'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));
    $this->validatorSchema['taxon_name'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));

    $this->validatorSchema['taxon_level_ref'] = new sfValidatorInteger(array(
      'required' => false,
    ));

    $this->validatorSchema['chrono_name'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));

    $this->validatorSchema['chrono_level_ref'] = new sfValidatorInteger(array(
      'required' => false,
    ));

    $this->validatorSchema['litho_name'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));

    $this->validatorSchema['litho_level_ref'] = new sfValidatorInteger(array(
      'required' => false,
    ));

    $this->validatorSchema['lithology_name'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));

    $this->validatorSchema['lithology_level_ref'] = new sfValidatorInteger(array(
      'required' => false,
    ));

    $this->validatorSchema['mineral_name'] = new sfValidatorString(array(
      'required' => false,
      'trim' => true
    ));

    $this->validatorSchema['mineral_level_ref'] = new sfValidatorInteger(array(
      'required' => false,
    ));

    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['tags'] = new sfWidgetFormInputText();
    $this->widgetSchema['gtu_from_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );

    $this->widgetSchema['gtu_to_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'to_date')
    );

    $this->widgetSchema['gtu_from_precise'] = new sfWidgetFormInputCheckbox();//array('default' => FALSE));
	$this->widgetSchema['gtu_from_precise']->setAttributes(Array("class"=>"precise_gtu_date"));
    $this->validatorSchema['gtu_from_precise'] = new sfValidatorPass();
    //ftheeten 2020 02 11 no trim
    $this->validatorSchema['tags'] = new sfValidatorString(array('required' => false, 'trim'=>false));
    $this->validatorSchema['gtu_from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $this->validatorSchema['gtu_to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $subForm = new sfForm();
    $this->embedForm('Tags',$subForm);

    $this->widgetSchema['tools'] = new widgetFormSelectDoubleListFilterable(array(
      'choices' => new sfCallable(array(Doctrine_Core::getTable('CollectingTools'),'fetchTools')),
      'label_associated'=>$this->getI18N()->__('Selected'),
      'label_unassociated'=>$this->getI18N()->__('Available')
    ));

    $this->widgetSchema['methods'] = new widgetFormSelectDoubleListFilterable(array(
      'choices' => new sfCallable(array(Doctrine_Core::getTable('CollectingMethods'),'fetchMethods')),
      'label_associated'=>$this->getI18N()->__('Selected'),
      'label_unassociated'=>$this->getI18N()->__('Available')
    ));

    $this->validatorSchema['methods'] = new sfValidatorPass();
    $this->validatorSchema['tools'] = new sfValidatorPass();



    $this->widgetSchema['with_multimedia'] = new sfWidgetFormInputCheckbox();

    $this->validatorSchema['with_multimedia'] = new sfValidatorPass();
        //ftheeten 2018 11 22



    /* Acquisition categories */
    $this->widgetSchema['acquisition_category'] = new sfWidgetFormChoice(array(
      'choices' =>  array_merge(array('' => ''), SpecimensTable::getDistinctCategories()),
    ));

    $this->widgetSchema['acquisition_from_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );

    $this->widgetSchema['acquisition_to_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'to_date')
    );

    $this->validatorSchema['acquisition_from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $this->validatorSchema['acquisition_to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'min' => $minDate,
      'from_date' => false,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );
  /**
  * Individuals Fields
  */

    $this->widgetSchema['type'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctTypeGroups',
      'multiple' => true,
      'expanded' => true,
      'add_empty' => false,
    ),
    //ftheeten 2018 09 27
    array('class' => 'search_type_class'));
    $this->validatorSchema['type'] = new sfValidatorPass();

    $this->widgetSchema['sex'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSexes',
      'multiple' => true,
      'expanded' => true,
      'add_empty' => false,
    ));
    $this->validatorSchema['sex'] = new sfValidatorPass();


    $this->widgetSchema['stage'] = new widgetFormSelectDoubleListFilterable(array(
      'choices' => new sfCallable(array(Doctrine_Core::getTable('Specimens'),'getDistinctStages')),
      'label_associated'=>$this->getI18N()->__('Selected'),
      'label_unassociated'=>$this->getI18N()->__('Available')
    ));

    $this->validatorSchema['stage'] = new sfValidatorPass();

    $this->widgetSchema['status'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctStates',
      'multiple' => true,
      'expanded' => true,
      'add_empty' => false,
    ));
    $this->validatorSchema['status'] = new sfValidatorPass();

    $this->widgetSchema['specimen_status'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSpecimenStatus',
      'multiple' => false,
      'expanded' => false,
      'add_empty' => true,
    ));
    $this->validatorSchema['specimen_status'] = new sfValidatorPass();

    $this->widgetSchema['social'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSocialStatuses',
      'multiple' => true,
      'expanded' => true,
      'add_empty' => false,
    ));
    $this->validatorSchema['social'] = new sfValidatorPass();

    $this->widgetSchema['rockform'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctRockForms',
      'multiple' => true,
      'expanded' => true,
      'add_empty' => false,
    ));
    $this->validatorSchema['rockform'] = new sfValidatorPass();
	
	/*2019 06 22*/
	 $this->widgetSchema['mineralogical_identification'] = new sfWidgetForminput();
	 $this->widgetSchema['mineralogical_identification']->setAttributes(array('class'=>'autocomplete_mineralogy'));
	 $this->validatorSchema['mineralogical_identification'] = new sfValidatorPass();
	 $identification_choices = array('all'=>'All', 'taxonomy'=> 'Taxon.', 'mineralogy' => 'Miner.', 'chronostratigraphy' => 'Chron.',
      'lithostratigraphy' => 'Litho.', 'lithology' => 'Lithology', 'type'=> 'Type', 
      'sex' => 'Sex', 'stage' => 'Stage', 'social_status' => 'Social', 'rock_form' => 'Rock') ;
	   $this->widgetSchema['identification_notion_concerned'] = new sfWidgetFormChoice(array(
        'choices' => $identification_choices
      ));
    $this->validatorSchema['identification_notion_concerned'] = new sfValidatorChoice(array('required' => false, 'choices'=>array_keys($identification_choices)));
	 $this->widgetSchema['identification_value_defined'] = new sfWidgetFormInput();
    //ftheeten 2018 09 18 new class identification_subject
    $this->widgetSchema['identification_value_defined']->setAttributes(array('class'=>'xlsmall_size identification_subject, autocomplete_identification_value'));
    $this->validatorSchema['identification_value_defined'] = new sfValidatorString(array('required' => false, 'trim'=>true));

/*ftheeten 2016 06 22*/
 $this->widgetSchema['specimen_count_min'] = new sfWidgetForminput();
 $this->widgetSchema['specimen_count_min']->setAttributes(array('class'=>'vvsmall_size'));
 $this->widgetSchema['specimen_count_min']->setLabel('Count (min)');
 $this->validatorSchema['specimen_count_min'] = new sfValidatorNumber(array('required'=>false,'min' => '0'));
 
  $this->widgetSchema['specimen_count_males_min'] = new sfWidgetForminput();
   $this->widgetSchema['specimen_count_males_min']->setAttributes(array('class'=>'vvsmall_size'));
 $this->widgetSchema['specimen_count_males_min']->setLabel('Count males (min)');
 $this->validatorSchema['specimen_count_males_min'] = new sfValidatorNumber(array('required'=>false,'min' => '0'));
 
   $this->widgetSchema['specimen_count_females_min'] = new sfWidgetForminput();
      $this->widgetSchema['specimen_count_females_min']->setAttributes(array('class'=>'vvsmall_size'));
 $this->widgetSchema['specimen_count_females_min']->setLabel('Count females (min)');
 $this->validatorSchema['specimen_count_females_min'] = new sfValidatorNumber(array('required'=>false,'min' => '0'));

   $this->widgetSchema['specimen_count_juveniles_min'] = new sfWidgetForminput();
      $this->widgetSchema['specimen_count_juveniles_min']->setAttributes(array('class'=>'vvsmall_size'));
 $this->widgetSchema['specimen_count_juveniles_min']->setLabel('Count juveniles (min)');
 $this->validatorSchema['specimen_count_juveniles_min'] = new sfValidatorNumber(array('required'=>false,'min' => '0'));
 
  $this->widgetSchema['specimen_count_max'] = new sfWidgetForminput();
 $this->widgetSchema['specimen_count_max']->setAttributes(array('class'=>'vvsmall_size'));
 $this->widgetSchema['specimen_count_max']->setLabel('Count (max)');
 $this->validatorSchema['specimen_count_max'] = new sfValidatorNumber(array('required'=>false,'min' => '0'));
 
  $this->widgetSchema['specimen_count_males_max'] = new sfWidgetForminput();
   $this->widgetSchema['specimen_count_males_max']->setAttributes(array('class'=>'vvsmall_size'));
 $this->widgetSchema['specimen_count_males_max']->setLabel('Count males (max)');
 $this->validatorSchema['specimen_count_males_max'] = new sfValidatorNumber(array('required'=>false,'min' => '0'));
 
   $this->widgetSchema['specimen_count_females_max'] = new sfWidgetForminput();
      $this->widgetSchema['specimen_count_females_max']->setAttributes(array('class'=>'vvsmall_size'));
 $this->widgetSchema['specimen_count_females_max']->setLabel('Count females (max)');
 $this->validatorSchema['specimen_count_females_max'] = new sfValidatorNumber(array('required'=>false,'min' => '0'));
 
    $this->widgetSchema['specimen_count_juveniles_max'] = new sfWidgetForminput();
      $this->widgetSchema['specimen_count_juveniles_max']->setAttributes(array('class'=>'vvsmall_size'));
 $this->widgetSchema['specimen_count_juveniles_max']->setLabel('Count juveniles (max)');
 $this->validatorSchema['specimen_count_juveniles_max'] = new sfValidatorNumber(array('required'=>false,'min' => '0'));
 //end group count

    /*$operators = array(''=>'','e'=>'=','l'=>'<=','g'=>'>=') ;
    $this->widgetSchema['count_operator'] = new sfWidgetFormChoice(array('choices'=> $operators));
    $this->validatorSchema['count_operator'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($operators)));*/
    
        //ftheeten 2016 06 08 t disable warning when saving saved search in the DB
    if(isset($values['count_operator']))
    {
        if ($values['count_operator'] != '' && $values['count'] != '')
        {
          if($values['count_operator'] == 'e') $query->andwhere('specimen_count_max = ?',$values['count']) ;
          if($values['count_operator'] == 'l') $query->andwhere('specimen_count_max <= ?',$values['count']) ;
          if($values['count_operator'] == 'g') $query->andwhere('specimen_count_min >= ?',$values['count']) ;
        }
    }
    
    $this->widgetSchema['container'] = new sfWidgetFormInput();
    $this->validatorSchema['container'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['sub_container'] = new sfWidgetFormInput();
    $this->validatorSchema['sub_container'] = new sfValidatorString(array('required' => false));

    $storage_tmp=array_unique(array_map('strtolower',array_change_key_case(Doctrine_Core::getTable("Specimens")->getDistinctContainerStorages())));
    $this->widgetSchema['container_storage'] = new sfWidgetFormChoice(array(
       "choices"=> $storage_tmp,
       'multiple' => true,
    ), array("size"=>10));

    $this->validatorSchema['container_storage'] =  new sfValidatorChoice(
        array(
         "choices"=> $storage_tmp,
         'multiple' => true,
         "required"=>false
         )
    );

    $sub_storage_tmp=array_unique(array_map('strtolower',array_change_key_case(Doctrine_Core::getTable("Specimens")->getDistinctSubContainerStorages())));
    $this->widgetSchema['sub_container_storage'] = new sfWidgetFormChoice(array(
       "choices"=> $sub_storage_tmp,
       'multiple' => true,
    ), array("size"=>10));

    $this->validatorSchema['sub_container_storage'] =  new sfValidatorChoice(
         array("choices"=> $sub_storage_tmp,
         'multiple' => true, 
         "required"=>false
         )
    );

    $part_tmp=array_unique(array_map('strtolower',array_change_key_case(Doctrine_Core::getTable("Specimens")->getDistinctParts())));
    $this->widgetSchema['part'] = new sfWidgetFormChoice(array(
       "choices"=> $part_tmp,
       'multiple' => true,
    ), array("size"=>10));

    $this->validatorSchema['part'] =  new sfValidatorChoice(
         array("choices"=> $part_tmp,
         'multiple' => true, 
         "required"=>false
         )
    );
    $this->widgetSchema['object_name'] = new sfWidgetFormInput();
    $this->validatorSchema['object_name'] = new sfValidatorString(array('required' => false));

    $this->validatorSchema['floor'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['institution_ref'] = new widgetFormButtonRef(array(
      'model' => 'Institutions',
      'link_url' => 'institution/choose?with_js=1',
      'method' => 'getFamilyName',
      'box_title' => $this->getI18N()->__('Choose Institution'),
      'nullable' => true,
     ));
    $this->widgetSchema['institution_ref']->setLabel('Institution');

    $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema['building'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctBuildings',
      'add_empty' => true,
    ));

    $this->validatorSchema['building'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['floor'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctFloors',
      'add_empty' => true,
    ));
    $this->validatorSchema['floor'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['row'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctRows',
      'add_empty' => true,
    ));
    $this->validatorSchema['row'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['col'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctCols',
      'add_empty' => true,
    ));
    $this->validatorSchema['col'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['room'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctRooms',
      'add_empty' => true,
    ));
    $this->validatorSchema['room'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['shelf'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctShelfs',
      'add_empty' => true,
    ));
    $this->validatorSchema['shelf'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['property_type'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Properties',
      'table_method' => array('method'=>'getDistinctType', 'parameters'=> array('specimens') ),
      'add_empty' => $this->getI18N()->__('All')
    ));
    $this->validatorSchema['property_type'] = new sfValidatorString(array('required' => false));


    $this->widgetSchema['property_applies_to'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Properties',
      'table_method' => array('method'=>'getDistinctApplies', 'parameters'=> array() ),
      'add_empty' => $this->getI18N()->__('All')
    ));
    $this->validatorSchema['property_applies_to'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['property_value_from'] = new sfWidgetFormInput();
    $this->validatorSchema['property_value_from'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['property_value_to'] = new sfWidgetFormInput();
    $this->validatorSchema['property_value_to'] = new sfValidatorString(array('required' => false));
    
    
    //2019 03 29
	$this->widgetSchema['property_fuzzy'] = new sfWidgetFormInputCheckbox();//array('default' => FALSE));
  	////ftheeten 2015 09 09
	$this->validatorSchema['property_fuzzy'] = new sfValidatorPass();



    $this->widgetSchema['property_units'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Properties',
      'table_method' => array('method' => 'getDistinctUnit', 'parameters' => array(/*$this->options['ref_relation']*/)),
      'add_empty' => true,
    ));
    $this->validatorSchema['property_units'] = new sfValidatorString(array('required' => false));


    $this->widgetSchema['comment'] = new sfWidgetFormInput();
    $this->validatorSchema['comment'] = new sfValidatorString(array('required' => false));

    $comment_choices = array(''=>'');
    $comment_choices = $comment_choices + CommentsTable::getNotionsFor('specimens');
    $this->widgetSchema['comment_notion_concerned'] = new sfWidgetFormChoice(array('choices'=> $comment_choices));
    $this->validatorSchema['comment_notion_concerned'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($comment_choices)));


      //2019 02 25
    $this->widgetSchema['creation_from_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );

    $this->widgetSchema['creation_to_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'to_date')
    );
    
     $this->validatorSchema['creation_from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );
    
    $this->validatorSchema['creation_to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );
	

    
    $subForm = new sfForm();
    $this->embedForm('Codes',$subForm);

     // LAT LON
    $this->widgetSchema['lat_from'] = new sfWidgetForminput();
    $this->widgetSchema['lat_from']->setLabel('Latitude');
    $this->widgetSchema['lat_from']->setAttributes(array('class'=>'medium_small_size'));
    $this->widgetSchema['lat_to'] = new sfWidgetForminput();
    $this->widgetSchema['lat_to']->setAttributes(array('class'=>'medium_small_size'));
    $this->widgetSchema['lon_from'] = new sfWidgetForminput();
    $this->widgetSchema['lon_from']->setLabel('Longitude');
    $this->widgetSchema['lon_from']->setAttributes(array('class'=>'medium_small_size'));
    $this->widgetSchema['lon_to'] = new sfWidgetForminput();
    $this->widgetSchema['lon_to']->setAttributes(array('class'=>'medium_small_size'));

    $this->validatorSchema['lat_from'] = new sfValidatorNumber(array('required'=>false,'min' => '-180', 'max'=>'180'));
    $this->validatorSchema['lon_from'] = new sfValidatorNumber(array('required'=>false,'min' => '-360', 'max'=>'360'));
    $this->validatorSchema['lat_to'] = new sfValidatorNumber(array('required'=>false,'min' => '-180', 'max'=>'180'));
    $this->validatorSchema['lon_to'] = new sfValidatorNumber(array('required'=>false,'min' => '-360', 'max'=>'360'));
	
    //ftheeten 2018 10 05
    $this->widgetSchema['wkt_search'] = new sfWidgetFormInputText();
    $this->widgetSchema['wkt_search']->setAttributes(array('class'=>'wkt_search'));
    $this->validatorSchema['wkt_search'] = new sfValidatorString(array('required' => false, 'trim' => true));
	
	$this->widgetSchema['wfs_search'] = new sfWidgetFormInputText();
    $this->widgetSchema['wfs_search']->setAttributes(array('class'=>'wfs_search'));
    $this->validatorSchema['wfs_search'] = new sfValidatorString(array('required' => false, 'trim' => true));
    
	//ftheeten 2018 06 20
	$this->widgetSchema['code_main'] = new sfWidgetFormInput();
    $this->validatorSchema['code_main'] = new sfValidatorString(array('required' => false));

    sfWidgetFormSchema::setDefaultFormFormatterName('list');
    $this->widgetSchema->setNameFormat('specimen_search_filters[%s]');
    /* Labels */
    $this->widgetSchema->setLabels(array(
      'rockform' => 'Rock form',
      'gtu_code' => 'Sampling Location code',
      'taxon_name' => 'Taxon text search',
      'litho_name' => 'Litho text search',
      'lithology_name' => 'Lithology text search',
      'chrono_name' => 'Chrono text search',
      'mineral_name' => 'Mineralo text search',
      'taxon_level_ref' => 'Level',
      'code_ref_relation' => 'Code of',
      'people_ref' => 'Whom are you looking for',
      'role_ref' => 'Which role',
      'with_multimedia' => 'Search Only objects with multimedia files',
      'gtu_from_date' => 'Between',
      'gtu_to_date' => 'and',
      'acquisition_from_date' => 'Between',
      'acquisition_to_date' => 'and',
      'ig_from_date' => 'Between',
      'ig_to_date' => 'and',
      'ig_num' => 'I.G. unit',
      'property_type' => 'Type',
      'property_applies_to' => 'Applies to',
      'property_value_from' => 'From',
      'property_value_to' => 'To',
      'property_units' => 'Unit',
      'comment_notion_concerned' => 'Notion concerned',
    ));

    // For compat only with old saved search
    // might be removed with a migration
    $this->validatorSchema['what_searched'] = new sfValidatorPass();
    
   
    
	 //2018 09 19chnage sort order on name
     
	$this->widgetSchema['taxonomy_metadata_ref'] = new sfWidgetFormChoice(array(
      'choices' => TaxonomyMetadataTable::getAllTaxonomicMetadata( 'id ASC',true)  //array_merge( array(''=>'All'),TaxonomyMetadataTable::getAllTaxonomicMetadata("id ASC"))
    ));
	 $this->widgetSchema['taxonomy_metadata_ref']->setAttributes(array('class'=>'col_check_metadata_ref col_check_metadata_callback'));
	$this->validatorSchema['taxonomy_metadata_ref'] = new sfValidatorInteger(array('required'=>false));
    
     //2018 11 22
	$this->widgetSchema['people_boolean'] = new sfWidgetFormChoice(array('choices' => array('OR' => 'OR', 'AND' => 'AND')));
	$this->validatorSchema['people_boolean'] = new sfValidatorPass();
	$subForm = new sfForm();
    $this->embedForm('Peoples',$subForm);
    
    //ftheeten 2018 11 22
	//$this->widgetSchema['codes_list'] = new sfWidgetFormInputHidden();//array('choices'=>array()));
	$this->widgetSchema['codes_list'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['codes_list']->setAttributes(Array("class"=>"select2_code_values"));
    $this->validatorSchema['codes_list'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['exact_codes_list']=new sfWidgetFormInputCheckbox();   
    $this->widgetSchema['exact_codes_list']->setLabel("Fuzzy matching");
    $this->validatorSchema['exact_codes_list'] = new sfValidatorPass();
    $this->is_fuzzy_codes_list=false;
    $this->codeListCalled=false;
    
    //ftheeten 2018 11 26
	$this->widgetSchema['taxa_list'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['taxa_list']->setAttributes(Array("class"=>"select2_taxa_values"));
    $this->validatorSchema['taxa_list'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['taxa_list_placeholder'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['taxa_list_placeholder']->setAttributes(Array("class"=>"select2_taxa_list_placeholder"));
    $this->validatorSchema['taxa_list_placeholder'] = new sfValidatorString(array('required' => false));
    
	//jmherpers 2019 04 26
	$this->widgetSchema['taxonomy_cites'] = new sfWidgetFormChoice(
		array(
			'expanded' => true,
			'choices'  => array(True => 'yes', False => 'no', NULL=>'yes or no')
		),
		array( 'style' => "display: inline-block;text-align:center")
	);

    $this->validatorSchema['taxonomy_cites'] = new sfValidatorString(array('required' => false));
	
	//jmherpers 2019 04 26
    $this->widgetSchema['nagoya'] = new sfWidgetFormChoice(array(
        'expanded' => true,
        'choices'  => array(True => 'yes', False => 'no', NULL=>'yes or no'),
       
        ), array( 'style' => "display: inline-block;text-align:center"));
    $this->validatorSchema['nagoya'] = new sfValidatorString(array('required' => false));
	
	//ftheeten 2019 06 02
	 $this->widgetSchema['import_ref'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
	 $this->validatorSchema['import_ref'] = new sfValidatorString(array('required' => false));
     
    //2018 11 22
	$this->widgetSchema['tag_boolean'] = new sfWidgetFormChoice(array('choices' => array('OR' => 'OR', 'AND' => 'AND')));
	$this->validatorSchema['tag_boolean'] = new sfValidatorPass();
    
    
	//ftheeten 2015 01 08
	$this->widgetSchema['code_boolean'] = new sfWidgetFormChoice(array('choices' => array('OR' => 'OR', 'AND' => 'AND')));
  	////ftheeten 2015 01 08
	$this->validatorSchema['code_boolean'] = new sfValidatorPass();
  
  }

  public function addGtuTagValue($num)
  {
      $form = new TagLineForm(null,array('num'=>$num));
      $this->embeddedForms['Tags']->embedForm($num, $form);
      $this->embedForm('Tags', $this->embeddedForms['Tags']);
  }

  //ftheeten 2018 11 22
  public function addPeopleValue($num)
  {
	 
      $form = new PeopleLineForm(null,array('num'=>$num));
      $this->embeddedForms['Peoples']->embedForm($num, $form);
      $this->embedForm('Peoples', $this->embeddedForms['Peoples']);
  }
  
  public function addCodeValue($num)
  {
      $form = new CodeLineForm();
      $this->embeddedForms['Codes']->embedForm($num, $form);
      $this->embedForm('Codes', $this->embeddedForms['Codes']);
  }
  public function addCommentsQuery($query, $notion, $comment)
  {
    if($notion != '' || $comment != '') {
      $query->innerJoin('s.SubComments c');

      $query->groupBy("s.id");

      if($notion != '')
        $query->andWhere('notion_concerned = ?', $notion ) ;
      if($comment != '')
        $query->andWhere('comment_indexed like concat(\'%\', fulltoindex(?), \'%\' )', $comment);
      $this->with_group = true;
    }
    return $query ;
  }

 //ftheeten 2018 11 22
  public function addCodesListColumnQuery($query, $fields, $val)
  {
        
                   
            if(strlen(trim($val))>0)
            {
                $sql_params=Array();
                $sql_parts=Array();
                $tmpCodes= preg_split( "/(;|\|)/", $val );
              
                foreach($tmpCodes as $to_search)
                {
                    if($this->is_fuzzy_codes_list)
                    {
                        $to_search="%".BaseSpecimensFormFilter::fulltoindex_sql($to_search)."%";
                        $sql_parts[]= "EXISTS(select 1 from codes where referenced_relation='specimens' and code_category='main' and record_id = s.id AND full_code_indexed LIKE ? ) ";
                    }
                    else
                    {
                        $sql_parts[]= "EXISTS(select 1 from codes where referenced_relation='specimens' and code_category='main' and record_id = s.id AND full_code_indexed like (SELECT  fulltoindex(?) )) ";
                    }
                    $sql_params[] =trim($to_search);
                }
                $sql=implode(" OR ", $sql_parts);
                $query->andWhere("(" .$sql.")", $sql_params);
            }
        
        return $query ;
  }
  
  public function addLatLonColumnQuery($query, $values)
  {
    if( $values['lat_from'] != '' && $values['lon_from'] != '' && $values['lon_to'] != ''  && $values['lat_to'] != '' )
    {
      $horizontal_box = "((".(float)$values['lat_from'].",-180),(".(float)$values['lat_to'].",180))";
      $vert_box = "((".(float)$values['lat_from'].",".(float)$values['lon_from']."),(".(float)$values['lat_to'].",".(float)$values['lon_to']."))";

      // Look for a wrapped box (ie. between RUSSIA and USA)
      if( (float)$values['lon_to'] < (float) $values['lon_from']) {

        $query->andWhere("
          ( station_visible = true
            AND box('$horizontal_box') @> gtu_location
            AND NOT box('$vert_box') @> gtu_location
          )
          OR
          ( station_visible = false
            AND collection_ref IN (".implode(',',$this->encoding_collection).")
            AND box('$horizontal_box') @> gtu_location
            AND NOT box('$vert_box') @> gtu_location
          )"
        );
        $query->whereParenWrap();

      } else {
        $query->andWhere("
          ( station_visible = true
            AND box('$horizontal_box') @> gtu_location
            AND box('$vert_box') @> gtu_location
          )
          OR
          ( station_visible = false
            AND collection_ref IN (".implode(',',$this->encoding_collection).")
            AND box('$horizontal_box') @> gtu_location
            AND box('$vert_box') @> gtu_location
          )"
        );
        $query->whereParenWrap();
      }
      $query->andWhere('gtu_location is not null');
    }
    
     //2018 10 05
    if( isset($values['wkt_search']))
    {
        if(strlen(trim($values['wkt_search'])))
        {
            $query->andWhere("ST_INTERSECTS(ST_SETSRID(ST_Point(gtu_location[1], gtu_location[0]),4326), ST_GEOMFROMTEXT('".$values['wkt_search']."',4326))");
        }
    }
	
	if( isset($values['wfs_search']))
    {
        if(strlen(trim($values['wfs_search'])))
        {
           $tmp_array=json_decode($values['wfs_search'], TRUE);
		   //print_r($tmp_array);
		   $sql_block=Array();
		   /*foreach($tmp_array as $key=>$val)
		   {
			  
			   $searched=$val["value"];
			   $layer=$val["layer"];
			   
			   $sql_block[]="  EXISTS(SELECT id FROM rmca_get_wfs_geom('wfs.".$layer."', ".$searched.") wfs WHERE wfs.id=s.id)  ";
		   }
		   $wfs_sql = implode(" OR ", $sql_block );
		   $query->andWhere($wfs_sql);*/
           $secondArray=Array();
           foreach($tmp_array as $key=>$val)
		   {
            $searched=$val["value"];
			$layer=$val["layer"];
            if(!array_key_exists($layer,$secondArray))
            {
                 $secondArray[$layer]=Array();
            }
            $secondArray[$layer][]=$searched;
           }
           foreach($secondArray as $key=>$val)
           {
                $layer=$key;
                $values="'{".implode(",", $val)."}'::integer[]";
                $sql_block[]="  ST_INTERSECTS(ST_SETSRID(ST_Point(gtu_location[1], gtu_location[0]),4326),(SELECT rmca_get_wfs_geom('wfs.".$layer."', ".$values.")))"; 
           }
           $wfs_sql = implode(" OR ", $sql_block );
		   $query->andWhere($wfs_sql);
        }
    }
    return $query;
  }

  public function addToolsColumnQuery($query, $field, $val)
  {
    if($val != '' && is_array($val) && !empty($val)) {
      $query->andWhere('s.id in (select fct_search_tools (?))',implode(',', $val));
    }
    return $query ;
  }

  public function addMethodsColumnQuery($query, $field, $val)
  {
    if($val != '' && is_array($val) && !empty($val)) {
      $query->andWhere('s.id in (select fct_search_methods (?))',implode(',', $val));
    }
    return $query ;
  }


  public function addIgNumQuery(Doctrine_Query $query, $field, $values, $fuzzy_mode)
  {
    if ($values != "") {
      $conn_MGR = Doctrine_Manager::connection();
	  if($fuzzy_mode=="on")
	  {
		  $query->andWhere("ig_num_indexed like concat(fullToIndex(".$conn_MGR->quote($values, 'string')."), '%') ");
	  }
	  else
	  {
		 $query->andWhere("ig_num_indexed like fullToIndex(".$conn_MGR->quote($values, 'string').") ");
	  }
	}
    return $query;
  }

  public function checksToQuotedValues($val) {
    if($val == '') return ;
    if(! is_array($val))
      $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');
    return $val;
  }

  public function addSexColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.sex in ('.implode(',',$val).')');
    return $query ;
  }

  public function addTypeColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.type_group in ('.implode(',',$val).')');
    return $query ;
  }

  public function addStageColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.stage in ('.implode(',',$val).')');
    return $query ;
  }

  public function addStatusColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.state in ('.implode(',',$val).')');
    return $query ;
  }

  public function addSocialColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.social_status in ('.implode(',',$val).')');
    return $query ;
  }

  public function addRockformColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.rock_form in ('.implode(',',$val).')');
    return $query ;
  }

  public function addInstitutionRefColumnQuery($query, $field, $val)
  {
    if($val == '' &&  ! ctype_digit($val)) return ;
    $query->andWhere('s.institution_ref =  ?', $val);
    return $query ;
  }

  public function addContainerColumnQuery($query, $field, $val)
  {
    if(trim($val) != '') {
      $values = explode(' ',$val);
      $query_value = array();
      foreach($values as $value) {
        if(trim($value) != '')
          $query_value[] = '%'.$value.'%';
      }

      $query_array = array_fill(0,count($query_value),'s.container ilike ?');
      $query->andWhere( implode(' or ',$query_array) ,$query_value);
    }
    return $query ;
  }

  public function addSubContainerColumnQuery($query, $field, $val)
  {
    if(trim($val) != '') {
      $values = explode(' ',$val);
      $query_value = array();
      foreach($values as $value) {
        if(trim($value) != '')
          $query_value[] = '%'.strtolower($value).'%';
      }

      $query_array = array_fill(0,count($query_value),'s.sub_container ilike ?');
      $query->andWhere( implode(' or ',$query_array) ,$query_value);
    }
    return $query ;
  }
  
  public function addContainerStorageColumnQuery($query, $field, $val)
  { 
    $containers=Array();
    foreach($val as $tmp)
    {
        $containers[]='"'.str_replace("\"","\\\"", str_replace("\\","\\\\", $tmp )).'"';
    }
    $query->andWhere("LOWER(s.container_storage) = ANY ('{".implode(",", $containers)."}')");
    return $query ;
  }
  
   
   public function addSubContainerStorageColumnQuery($query, $field, $val)
  {
    $sub_containers=Array();
    foreach($val as $tmp)
    {
        $sub_containers[]='"'.str_replace("\"","\\\"", str_replace("\\","\\\\", $tmp )).'"';
    }
    $query->andWhere("LOWER(s.sub_container_storage) = ANY ('{".implode(",", $sub_containers)."}')");
    return $query ;
  }

  public function addBuildingColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.building in ('.implode(',',$val).')');
    return $query ;
  }

  public function addFloorColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.floor in ('.implode(',',$val).')');
    return $query ;
  }

  public function addRoomColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.room in ('.implode(',',$val).')');
    return $query ;
  }

  public function addRowColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.row in ('.implode(',',$val).')');
    return $query ;
  }

  public function addColColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.col in ('.implode(',',$val).')');
    return $query ;
  }

  public function addShelfColumnQuery($query, $field, $val)
  {
    $val = $this->checksToQuotedValues($val);
    $query->andWhere('s.shelf in ('.implode(',',$val).')');
    return $query ;
  }

  public function addPartColumnQuery($query, $field, $val)
  { 
    $containers=Array();
    foreach($val as $tmp)
    {
        $containers[]='"'.str_replace("\"","\\\"", str_replace("\\","\\\\", $tmp )).'"';
    }
    $query->andWhere("LOWER(s.specimen_part) = ANY ('{".implode(",", $containers)."}')");
    return $query ;
  }
  

  public function addTagsColumn($query, $field, $val)
  {
    $conn_MGR = Doctrine_Manager::connection();
    $tagList = '';
    $whereArray=array();
    $goWhere=false;
     $tmpStr=Array();
    foreach($val as $line)
    {
      $line_val = $line['tag'];
      if( $line_val != '')
      {
        //$tagList = $conn_MGR->quote($line_val, 'string');
        //$tagList=trim($line_val);
		//ftheeten 2020 02 11
		$tagList=$line_val;
        $tagList=trim($tagList, ";");
       
        foreach(explode(";", $tagList  ) as $tagvalue)
        {
            if(strlen($tagvalue)>0)
            {
            
                $tagvalue=str_replace('*', '.*', $tagvalue);
				$tagPrefix="''";
				$tagSuffix="''";
				if(substr($tagvalue, 0,2)!=".")
				{					
					$tagPrefix= "'(^|\s+)'";
				}
				if(substr($tagvalue, strlen($tagvalue)-2,2)!=".*")
				{					
					$tagSuffix= "'($|\s+)'";
				}
				$tagvalue=trim($tagvalue);
                $tagvalue = $conn_MGR->quote($tagvalue, 'string');
                $tmpStr[]="(
                        
                        EXISTS(SELECT id FROM Tags t where t.tag_indexed ~ fulltoindex_add_prefix_suffix(fulltoindex($tagvalue, TRUE, TRUE),$tagPrefix, $tagSuffix) and t.gtu_ref=s.gtu_ref) 
                      )";
         
            }
            $goWhere=true;
        } 
          
      }
    }
    if(count($tmpStr)>0)
    {
        $query->andWhere("(".implode(" ".$this->tag_boolean." ",$tmpStr).") AND (s.station_visible = true 
												   OR (s.station_visible = false AND s.collection_ref in (".implode(',',$this->encoding_collection).")))");      
    }
    
    return $query ;
  }
  

  
 
  //ftheeten 2018 11 22
    public function addPeoplesColumnQuery($query, $field, $val)
  {

    
	$queriesPeople=Array();
	$array_peoples=array();
    foreach($val as $i=>$people)
    {
		$query_people="";
	  if(empty($people)) continue;
	   if ($people['people_ref'] != '')
		{

			
			$query_people=$this->addPeopleSearchColumnQuerySQL( $people['people_ref'], $people['role_ref'], $i);
			$queriesPeople[]=$query_people;
			
		}
		if ($people['people_fuzzy'] != '') 
		{

			$iParams=0;
			$query_people=$this->addPeopleSearchColumnQueryFuzzySQL( $people['people_fuzzy'], $people['role_ref'], $i, $iParams);
			$queriesPeople[]=$query_people;
			for($iP=0; $iP<$iParams;$iP++)
			{
				$array_peoples[]=$people['people_fuzzy'];
			}
		}
		
		
    }
	if(count($queriesPeople)>0)
	{
		$query->andWhere("(".implode(" ".$this->people_boolean. " ", $queriesPeople).")",$array_peoples);
    }
    return $query ;
  }
  
  
  public function addCodesColumnQuery($query, $field, $val)
 {
	$sqlElems = Array();
    $sqlParams = Array() ;
	 foreach($val as $i => $code)
    {   
        $sql="";
        if(array_key_exists('code_from', $code)&&array_key_exists('code_to', $code))
        {
            if(ctype_digit($code['code_from']) && ctype_digit($code['code_to'])) 
            {
              $sql = "EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND  code_num BETWEEN ? AND ?";
              $sqlParams[]=$code['code_from'];
              $sqlParams[]=$code['code_to'];
              if($code['category']  != '' && strtolower($code['category'])  != 'all') 
                {
                    
                        $sql .= " AND code_category = ?";
                         $sqlParams[]=$code['category'];
                }
                
                 if($code['code_prefix']  != '')
                {
                     $sql .= " AND full_code_indexed LIKE (SELECT fulltoindex(?)||'%')";
                     $sqlParams[]=$code['code_prefix'];
                }
                
                 $sql .= ")";
                 
             
            }
		}
        if(array_key_exists('code_part', $code))
        {           
            if($code['code_part']  != '')
            {
                $sql ="EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND full_code_indexed like (SELECT fulltoindex(?))";
                $sqlParams[]=$code['code_part'];
                if($code['category']  != '' && strtolower($code['category'])  != 'all') 
                {
                    
                        $sql .= " AND code_category = ?";
                        $sqlParams[]=$code['category'];
                }
                 $sql .= ")";
            }
            if(strlen($sql)>0)
            {
             $sqlElems[]= $sql ;
            }
        }
    }
	if(count($sqlElems)>0)
	{
		if($this->code_boolean=='OR')
		{
			$query->andWhere("(".implode(" OR ", $sqlElems).")",$sqlParams);
		}
		else
		{			
			$query->andWhere("(".implode(" AND ", $sqlElems).")",$sqlParams);
		}
		
	}
	return $query;
	 
 }
  //ftheeten 2019 24 01
  public function addGtuRefColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $query->andWhere("
        (station_visible = true AND  gtu_ref = ? )
        OR
        (station_visible = false AND collection_ref in (".implode(',',$this->encoding_collection).")
          AND gtu_ref= ? )", array($val,$val));
      $query->whereParenWrap();
    }
    return $query ;
  }

  public function addGtuCodeColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $query->andWhere("
        (station_visible = true AND  gtu_code ilike ? )
        OR
        (station_visible = false AND collection_ref in (".implode(',',$this->encoding_collection).")
          AND gtu_code ilike ? )", array('%'.$val.'%','%'.$val.'%'));
      $query->whereParenWrap();
    }
    return $query ;
  }

  public function addSpecIdsColumnQuery($query, $field, $val)
  {
    $ids = explode(',', $val);
    $clean_ids =array();
    foreach($ids as $id)
    {
      $id=trim($id);
      if(ctype_digit($id))
        $clean_ids[] = $id;
      elseif(ctype_digit(substr($id,1, strlen($id))))
        $clean_ids[] = substr($id,1, strlen($id));
    }

    if(! empty($clean_ids)) {
      $query->andWhereIn("s.id", $clean_ids);
    }
    return $query ;
  }
  
 
  
   public function addPeopleSearchColumnQuery(Doctrine_Query $query, $people_id, $field_to_use, $alias_id=NULL, $boolean="AND")
  {
	$alias1="cp";

	if($alias_id)
	{
		$alias1=$alias1.$alias_id;

	}
    $build_query = '';
    if(! is_array($field_to_use) || count($field_to_use) < 1)
      $field_to_use = array('ident_ids','spec_coll_ids','spec_don_sel_ids') ;

	$nb2=0;  
    foreach($field_to_use as $field)
    {
       $alias1=$alias1.$nb2;

	  if($field == 'ident_ids')
      {
		$build_query .= "s.spec_ident_ids @> ARRAY[$people_id]::int[] OR " ;
      }
      elseif($field == 'spec_coll_ids')
      {
         $build_query .= "(s.spec_coll_ids @> ARRAY[$people_id]::int[] OR (s.expedition_ref IN (SELECT $alias1.record_id FROM CataloguePeople $alias1 WHERE $alias1.referenced_relation= 'expeditions' AND $alias1.people_ref= $people_id) )) OR " ;

      }
      else
      {
        $build_query .= "s.spec_don_sel_ids @> ARRAY[$people_id]::int[] OR " ;
      }
	  $nb2++;
    }
    // I remove the last 'OR ' at the end of the string
    $build_query = substr($build_query,0,strlen($build_query) -3) ;
	if($boolean=="AND")
	{
		$query->andWhere($build_query) ;
	}
	elseif($boolean=="OR")
	{
		if($alias_id>1)
		{
		 $query->orWhere($build_query) ;
		}
		else
		{
		 $query->andWhere($build_query) ;
		}
	}
	

    return $query ;
  }
  
    public function addPeopleSearchColumnQuerySQL($people_id, $field_to_use, $alias_id=NULL)
  {
	$alias1="cp";


	if($alias_id)
	{
		$alias1=$alias1.$alias_id;

	}
    $build_query = '';
    if(! is_array($field_to_use) || count($field_to_use) < 1)
      $field_to_use = array('ident_ids','spec_coll_ids','spec_don_sel_ids') ;

	$nb2=0;  
    foreach($field_to_use as $field)
    {
       $alias1=$alias1.$nb2;

	  if($field == 'ident_ids')
      {
		$build_query .= "s.spec_ident_ids @> ARRAY[$people_id]::int[] OR " ;
      }
      elseif($field == 'spec_coll_ids')
      {
         $build_query .= "(s.spec_coll_ids @> ARRAY[$people_id]::int[] OR (s.expedition_ref IN (SELECT $alias1.record_id FROM CataloguePeople $alias1 WHERE $alias1.referenced_relation= 'expeditions' AND $alias1.people_ref= $people_id) )) OR " ;

      }
      else
      {
        $build_query .= "s.spec_don_sel_ids @> ARRAY[$people_id]::int[] OR " ;
      }
  
	  $nb2++;
    }
    // I remove the last 'OR ' at the end of the string
    $build_query = substr($build_query,0,strlen($build_query) -3) ;
	return $build_query;
  }

  
   public function addPeopleSearchColumnQueryFuzzy(Doctrine_Query $query, $people_name, $field_to_use, $alias_id=NULL, $boolean="AND")
  {
    $alias1="ppa";
	$alias2="ppb";
	$alias3="cp";
	$alias4="ppc";
	$alias5="ppd";
	$idxAlias1=1;
	if($alias_id)
	{
			$idxAlias1=	$idxAlias1+$alias_id;
		
	}
	$alias1=$alias1.$idxAlias1;
	$idxAlias1++;
	$alias2=$alias2.$idxAlias1;
	$idxAlias1++;
	$alias3=$alias3.$idxAlias1;
	$idxAlias1++;
	$alias4=$alias4.$idxAlias1;
	$idxAlias1++;
	$alias5=$alias5.$idxAlias1;
    $build_query = '';
    if(! is_array($field_to_use) || count($field_to_use) < 1)
      $field_to_use = array('ident_ids','spec_coll_ids','spec_don_sel_ids') ;
	 $sql_params = array();
    foreach($field_to_use as $field)
    {
      if($field == 'ident_ids')
      {
        $build_query .= "s.spec_ident_ids && (SELECT array_agg($alias1.id) FROM people $alias1 WHERE fulltoindex(formated_name_indexed) ILIKE  '%'||fulltoindex(?)||'%' ) OR " ;
		$sql_params[]=$people_name;
      }
      elseif($field == 'spec_coll_ids')
      {
        $build_query .= "(s.spec_coll_ids && (SELECT array_agg($alias2.id) FROM people $alias2 WHERE fulltoindex(formated_name_indexed)ILIKE '%'||fulltoindex(?)||'%' ) OR s.expedition_ref IN (SELECT $alias3.record_id FROM CataloguePeople $alias3 WHERE $alias3.referenced_relation= 'expeditions' AND $alias3.people_ref IN (SELECT $alias4.id FROM people $alias4 WHERE fulltoindex(formated_name_indexed) ILIKE '%'||fulltoindex(?)||'%')) ) OR " ;
		$sql_params[]=$people_name;
		$sql_params[]=$people_name;
      }
      else
      {
        $build_query .= "s.spec_don_sel_ids && (SELECT array_agg($alias5.id) FROM people $alias5 WHERE fulltoindex(formated_name_indexed) ILIKE '%'||fulltoindex(?)||'%' ) OR " ;
		$sql_params[]=$people_name;
      }
    }
    // I remove the last 'OR ' at the end of the string
    $build_query = substr($build_query,0,strlen($build_query) -3) ;
    if($boolean=="AND")
	{
		$query->andWhere($build_query, $sql_params) ;
	}
	elseif($boolean=="OR")
	{
		if($alias_id>1)
		{
		 $query->orWhere($build_query, $sql_params) ;
		}
		else
		{
		 $query->andWhere($build_query, $sql_params) ;
		}
	}
    return $query ;
  }
  
  
  public function addPeopleSearchColumnQueryFuzzySQL( $people_name, $field_to_use, $alias_id=NULL, &$count_names)
  {
    $alias1="ppa";
	$alias2="ppb";
	$alias3="cp";
	$alias4="ppc";
	$alias5="ppd";
	$idxAlias1=1;
	$count_names=0;
	if($alias_id)
	{
			$idxAlias1=	$idxAlias1+$alias_id;
		
	}
	$alias1=$alias1.$idxAlias1;
	$idxAlias1++;
	$alias2=$alias2.$idxAlias1;
	$idxAlias1++;
	$alias3=$alias3.$idxAlias1;
	$idxAlias1++;
	$alias4=$alias4.$idxAlias1;
	$idxAlias1++;
	$alias5=$alias5.$idxAlias1;
    $build_query = '';
    if(! is_array($field_to_use) || count($field_to_use) < 1)
      $field_to_use = array('ident_ids','spec_coll_ids','spec_don_sel_ids') ;
	 //$sql_params = array();
    foreach($field_to_use as $field)
    {
      if($field == 'ident_ids')
      {
        $build_query .= "s.spec_ident_ids && (SELECT array_agg($alias1.id) FROM people $alias1 WHERE fulltoindex(formated_name_indexed) ILIKE  '%'||fulltoindex(?)||'%' ) OR " ;
		$count_names++;
      }
      elseif($field == 'spec_coll_ids')
      {
        $build_query .= "(s.spec_coll_ids && (SELECT array_agg($alias2.id) FROM people $alias2 WHERE fulltoindex(formated_name_indexed)ILIKE '%'||fulltoindex(?)||'%' ) OR s.expedition_ref IN (SELECT $alias3.record_id FROM CataloguePeople $alias3 WHERE $alias3.referenced_relation= 'expeditions' AND $alias3.people_ref IN (SELECT $alias4.id FROM people $alias4 WHERE fulltoindex(formated_name_indexed) ILIKE '%'||fulltoindex(?)||'%')) ) OR " ;

		$count_names++;
		$count_names++;
      }
      else
      {
        $build_query .= "s.spec_don_sel_ids && (SELECT array_agg($alias5.id) FROM people $alias5 WHERE fulltoindex(formated_name_indexed) ILIKE '%'||fulltoindex(?)||'%' ) OR " ;
		$count_names++;
      }
    }
    // I remove the last 'OR ' at the end of the string
    $build_query = substr($build_query,0,strlen($build_query) -3) ;
    
    return $build_query ;
  }

  public function addObjectNameColumnQuery($query, $field, $val) {
    $val = $this->checksToQuotedValues($val);
    $query_array = array_fill(0,count($val)," s.object_name_indexed like '%' || fulltoindex(?) || '%'");
    $query->andWhere( implode(' AND ',$query_array) ,$val);
    return $query ;
  }

  public function addCollectionRefColumnQuery($query, $field, $val)
  {
    //Do Nothing here, the job is done in the doBuildQuery with check collection rights
    return $query;
  }
  
  //ftheeten 2018 09 19
   public function addTaxonomicMetadataRef($query, $val) 
   {
    if(is_numeric($val))
    {
         $query->andWhere("EXISTS (select t.id from taxonomy t where t.metadata_ref = $val AND t.id = s.taxon_ref)") ;
    }
    return $query ;
  }
  
   public function addImportRefColumnQuery($query, $field, $val) 
   {
		if(strlen($val)>0)
		{
			if(is_numeric($val))
			{
				$query->andWhere( " import_ref = ? " ,$val);
			}
		}
    return $query ;
  }
  
   public function addMineralogicalIdentificationQuery($query, $field, $val) 
   {
		if(strlen($val)>0)
		{
			$query->andWhere( " EXISTS (SELECT i.id FROM identifications i WHERE i.referenced_relation = 'specimens' AND i.notion_concerned='mineralogy' AND i.value_defined_indexed=fulltoindex(?) AND i.record_id= s.id)" ,$val);
		}
    return $query ;
  }

  
     public function addIdentificationQuery($query, $field, $val, $notion) 
   {
		if(strlen($val)>0)
		{
			if($notion=="all")
			{
				$query->andWhere( " EXISTS (SELECT i.id FROM identifications i WHERE i.referenced_relation = 'specimens'  AND i.value_defined_indexed=fulltoindex(?) AND i.record_id= s.id)" ,$val);
			}
			else
			{
				$query->andWhere( " EXISTS (SELECT i.id FROM identifications i WHERE i.referenced_relation = 'specimens' AND i.notion_concerned=? AND i.value_defined_indexed=fulltoindex(?) AND i.record_id= s.id)" ,array($notion,$val));
			}
		}
    return $query ;
  }



    //JMHerpers 2019 04 29
	public function addCites($query, $val) {
		if(is_numeric($val)) {
			if($val == 0){
				//$query->andWhere("cites = FALSE") ;
				$query->andWhere("EXISTS (select t.id from taxonomy t where t.cites = FALSE AND t.id = s.taxon_ref)") ;
			}
			if($val == 1){
				//$query->andWhere("cites = TRUE") ;
				$query->andWhere("EXISTS (select t.id from taxonomy t where t.cites = TRUE AND t.id = s.taxon_ref)") ;
			}
		}
		return $query ;
	}
	
   public function addPropertiesQuery($query, $type , $applies_to, $value_from, $value_to, $unit, $taintedValues=Array()) 
  {
  
    $property_fuzzy=FALSE;
    $str_like = " ILIKE ? ";
    if(isset($taintedValues['property_fuzzy'])) 
	{
		if($taintedValues['property_fuzzy']==TRUE)
		{
			$this->property_fuzzy=TRUE;
            $str_like = "  ILIKE '%'||?||'%' ";
		}
	}
    
    $sql_part = array();
    $sql_params = array();
    if(trim($type) != '') {
      $sql_part[] = ' property_type = ? ';
      $sql_params[] = $type;
    }
    if(trim($applies_to) != '') {
      $sql_part[] = ' applies_to = ? ';
      $sql_params[] = $applies_to;
    }
    $value_from = trim($value_from);
    $value_to = trim($value_to);
    $unit = trim($unit);
    if($value_from == '' && $value_to != '') {
      $value_from = $value_to;
      $value_to = '';
    }

    // We have only 1 Value
    if($value_from != '' && $value_to == '') {
      if($unit == '') {
        $sql_part[] = '  ( p.lower_value '.$str_like.' OR  p.upper_value '.$str_like.') ';
        $sql_params[] = $value_from;
        $sql_params[] = $value_from;
      //We don't know the filed unit
      } elseif(Properties::searchRecognizedUnitsGroups($unit) === false) {
        $sql_part[] = '  ( p.lower_value '.$str_like.' OR  p.upper_value ILIKE '.$str_like.') AND property_unit = ? ';
        $sql_params[] = $value_from;
        $sql_params[] = $value_from;
        $sql_params[] = $unit;

      } else { // Recognized unit
        $sql_params[] = $value_from;
        $sql_params[] = $unit;
        $sql_params[] = $unit;

        $unitGroupStr =  implode(',',array_fill(0,count($unitGroup),'?'));
        $sql_part[] = ' ( convert_to_unified ( ?,  ? ) BETWEEN p.lower_value_unified AND  p.upper_value_unified) AND is_property_unit_in_group(property_unit, ?)  ';
      }
    }
    // We have 2 Values
    elseif($value_from != '' && $value_to != '') {
      if($unit == '') {
        $sql_part[] = ' ( ( p.lower_value '.$str_like.' OR  p.upper_value ILIKE ?) OR ( p.lower_value ILIKE ? OR  p.upper_value ILIKE ?) )';
        $sql_params[] = $value_from;
        $sql_params[] = $value_from;
        $sql_params[] = $value_to;
        $sql_params[] = $value_to;
      //We don't know the filed unit
      } elseif(Properties::searchRecognizedUnitsGroups($unit) === false) {
        $sql_part[] = ' ( ( p.lower_value '.$str_like.' OR  p.upper_value = ?) OR ( p.lower_value '.$str_like.' OR  p.upper_value '.$str_like.') )  AND property_unit = ? ';
        $sql_params[] = $value_from;
        $sql_params[] = $value_from;
        $sql_params[] = $value_to;
        $sql_params[] = $value_to;
        $sql_params[] = $unit;

      } else { // Recognized unit
        $conn_MGR = Doctrine_Manager::connection();
        $lv = $conn_MGR->quote($value_from, 'string');
        $uv = $conn_MGR->quote($value_to, 'string');
        $unit = $conn_MGR->quote($unit, 'string');
        $sql_part[] = "
            (
              ( p.lower_value_unified BETWEEN convert_to_unified($lv,$unit) AND convert_to_unified($uv,$unit))
              OR
              ( p.upper_value_unified BETWEEN convert_to_unified($lv,$unit) AND convert_to_unified($uv,$unit))
            )
            OR
            (
              p.lower_value_unified BETWEEN 0 AND convert_to_unified($lv,$unit)
              AND
              p.upper_value_unified BETWEEN convert_to_unified($uv,$unit) AND 'Infinity'
        )";
        $query->andWhere("is_property_unit_in_group(property_unit,$unit)") ;
        //OR ( convert_to_unified ( ?::text,  ?::text ) < p.lower_value_unified AND convert_to_unified ( ?::text,  ?::text ) > p.upper_value_unified)
      }

    }
    elseif($unit != '') {
      $sql_part[] = ' property_unit = ? ';
      $sql_params[] = $unit;
    }

    if(!empty($sql_part) ) {
      $query->innerJoin('s.SubProperties p');
      $query->andWhere("p.referenced_relation = ? ",'specimens');
      //$query->groupBy("s.id");

      $query->andWhere(implode(' AND ', $sql_part), $sql_params ) ;
      $this->with_group = true;
    }
    return $query;
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
  
  
  	$this->code_boolean='AND';
	 if(isset($taintedValues['Codes'])&& is_array($taintedValues['Codes']) && isset($taintedValues['code_boolean'])) 
	 {
		if($taintedValues['code_boolean']=='OR')
		{
			$this->code_boolean='OR';
		}
	}
    $this->tag_boolean='AND';
	 if(isset($taintedValues['tag_boolean'])) 
	 {
		if(strtolower($taintedValues['tag_boolean'])=='or')
		{
			$this->tag_boolean='OR';
		}
	}
    // the line below is used to avoid with_multimedia checkbox to remains checked when we click to back to criteria
    if(!isset($taintedValues['with_multimedia'])) $taintedValues['with_multimedia'] = false ;

    if(isset($taintedValues['Codes'])&& is_array($taintedValues['Codes'])) {
      foreach($taintedValues['Codes'] as $key=>$newVal) {
        if (!isset($this['Codes'][$key])) {
          $this->addCodeValue($key);
        }
      }
    } else {
      $this->offsetUnset('Codes') ;
      $subForm = new sfForm();
      $this->embedForm('Codes',$subForm);
      $taintedValues['Codes'] = array();
    }

    if(isset($taintedValues['Tags'])&& is_array($taintedValues['Tags'])) {
      foreach($taintedValues['Tags'] as $key=>$newVal) {
        if (!isset($this['Tags'][$key])) {
          $this->addGtuTagValue($key);
        }
      }
    } else {
      $this->offsetUnset('Tags') ;
      $subForm = new sfForm();
      $this->embedForm('Tags',$subForm);
      $taintedValues['Tags'] = array();
    }
    
    	//ftheeten 2015 10 22
	$this->people_boolean='AND';
	 if(isset($taintedValues['Peoples'])&& is_array($taintedValues['Peoples']) && isset($taintedValues['people_boolean'])) 
	 {
		if($taintedValues['people_boolean']=='OR')
		{
			$this->people_boolean='OR';
		}
	}
	
    //ftheeten 2018 11 22
    if(isset($taintedValues['exact_codes_list']))
    {
        if($taintedValues['exact_codes_list'])
        {
            $this->is_fuzzy_codes_list=true;
        }
    }

	
	if(isset($taintedValues['Peoples'])&& is_array($taintedValues['Peoples']))
	{

		 foreach($taintedValues['Peoples'] as $key=>$newVal) 
		 {

			if (!isset($this['Peoples'][$key]))
			{

				$this->addPeopleValue($key);
			}
		 }
	}
	else 
	{
      $this->offsetUnset('Peoples') ;
      $subForm = new sfForm();
      $this->embedForm('Peoples',$subForm);
      $taintedValues['Peoples'] = array();
    }

	//ftheeten 2018 11 22 handle several peoples in filter
	$this->people_boolean='AND';
	 if(isset($taintedValues['Peoples'])&& is_array($taintedValues['Peoples']) && isset($taintedValues['people_boolean'])) 
	 {
		if($taintedValues['people_boolean']=='OR')
		{
			$this->people_boolean='OR';
		}
	}
	
	if(isset($taintedValues['Peoples'])&& is_array($taintedValues['Peoples']))
	{

		 foreach($taintedValues['Peoples'] as $key=>$newVal) 
		 {

			if (!isset($this['Peoples'][$key]))
			{

				$this->addPeopleValue($key);
			}
		 }
	}
	else 
	{
      $this->offsetUnset('Peoples') ;
      $subForm = new sfForm();
      $this->embedForm('Peoples',$subForm);
      $taintedValues['Peoples'] = array();
    }
	//see sfValidatorSchemafilter in vendor
	/*foreach($taintedValues as $key=>$val)
    {
        if(!is_array($val))
        {
            unset($taintedValues[$key]);
        }
    }*/
    parent::bind($taintedValues, $taintedFiles);
  }

  public function doBuildQuery(array $values)
  {
    $this->encoding_collection = $this->getCollectionWithRights($this->options['user'],true);

    /*$query = DQ::create()
      ->select('s.*,

        gtu_location[0] as latitude,
        gtu_location[1] as longitude,
        (collection_ref in ('.implode(',',$this->encoding_collection).')) as has_encoding_rights, code_category, code_prefix as codeprefix, 
       sc.code_prefix_separator as codeprefixseparator, sc.code as codecore, sc.code_suffix_separator as codesuffixseparator, sc.code_suffix as codesuffix'
      )
      ->from('Specimens s')
	  ->leftJoin("s.SpecimensCodes sc on s.id=sc.record_id")->where("sc.code_category='main'");
*/

$query = DQ::create()
      ->select('s.*,

        gtu_location[0] as latitude,
        gtu_location[1] as longitude,
        (collection_ref in ('.implode(',',$this->encoding_collection).')) as has_encoding_rights
        '
      )
      ->from('Specimens s');
      
     
    $this->addTagsColumn($query, $values['Tags'], $values["Tags"]);
    if($values['with_multimedia'])
      $query->where("EXISTS (select m.id from multimedia m where m.referenced_relation = 'specimens' AND m.record_id = s.id)") ;

    $this->options['query'] = $query;

    $query = parent::doBuildQuery($values);

    $this->cols = $this->getCollectionWithRights($this->options['user']);

    if(!empty($values['collection_ref'])) {
    
      //ftheeten 2018 05 29
      if((boolean)$values['include_sub_collections']===true)
      {

          foreach($values['collection_ref'] as $tmp_id)
          {           
            $sub_cols = Doctrine_Core::getTable("Collections")->fetchByCollectionParent($this->options['user'] , $this->options['user']->getId(), $tmp_id);
            foreach($sub_cols as $sub_col)
            {
           
                if(!in_array($values['collection_ref'], $this->cols))
                {
                    $values['collection_ref'][]=$sub_col->getId();
                }
            }
          }
       }
      $this->cols = array_intersect($values['collection_ref'], $this->cols);
    }
    $query->andwhereIn('collection_ref ', $this->cols);
    if(!empty($values['specimen_status'])) $query->andwhere('specimen_status = ?',$values['specimen_status']) ; 

    
    //if ($values['people_ref'] != '') $this->addPeopleSearchColumnQuery($query, $values['people_ref'], $values['role_ref']);
    if ($values['acquisition_category'] != '' ) $query->andWhere('acquisition_category = ?',$values['acquisition_category']);
    if ($values['taxon_level_ref'] != '') $query->andWhere('taxon_level_ref = ?', intval($values['taxon_level_ref']));
    if ($values['chrono_level_ref'] != '') $query->andWhere('chrono_level_ref = ?', intval($values['chrono_level_ref']));
    if ($values['litho_level_ref'] != '') $query->andWhere('litho_level_ref = ?', intval($values['litho_level_ref']));
    if ($values['lithology_level_ref'] != '') $query->andWhere('lithology_level_ref = ?', intval($values['lithology_level_ref']));
    if ($values['mineral_level_ref'] != '') $query->andWhere('mineral_level_ref = ?', intval($values['mineral_level_ref']));
    $this->addLatLonColumnQuery($query, $values);
    $this->addNamingColumnQuery($query, 'expeditions', 'expedition_name_indexed', $values['expedition_name'],'s','expedition_name_indexed');

    $this->addNamingColumnQuery($query, 'taxonomy', 'taxon_name_indexed', $values['taxon_name'],'s','taxon_name_indexed');
    $this->addNamingColumnQuery($query, 'chronostratigraphy', 'chrono_name_indexed', $values['chrono_name'],'s','chrono_name_indexed');
    $this->addNamingColumnQuery($query, 'lithostratigraphy', 'litho_name_indexed', $values['litho_name'],'s','litho_name_indexed');
    $this->addNamingColumnQuery($query, 'lithology', 'lithology_name_indexed', $values['lithology_name'],'s','lithology_name_indexed');
    $this->addNamingColumnQuery($query, 'mineralogy', 'mineral_name_indexed', $values['mineral_name'],'s','mineral_name_indexed');

    //ftheeten 2018 09 19
    $this->addTaxonomicMetadataRef($query, $values["taxonomy_metadata_ref"]);
    
	//JMHerpers 2019 04 29
    $this->addCites($query, $values["taxonomy_cites"]);
	$this->addImportRefColumnQuery($query, $values["import_ref"],  $values["import_ref"]);
	$this->addMineralogicalIdentificationQuery($query, $values["mineralogical_identification"],  $values["mineralogical_identification"] );
	$this->addIdentificationQuery($query, $values["identification_choices"],  $values["identification_value_defined"], $values["identification_notion_concerned"] );
	$this->addIgNumQuery($query, $values["ig_num"],  $values["ig_num"], $values["ig_num_contains"] );
	
    $this->addPropertiesQuery($query, $values['property_type'] , $values['property_applies_to'], $values['property_value_from'], $values['property_value_to'], $values['property_units'], $values);

    $this->addCommentsQuery($query, $values['comment_notion_concerned'] , $values['comment']);

    $fields = array('gtu_from_date', 'gtu_to_date');
    $this->addDateFromToColumnQuery($query, $fields, $values['gtu_from_date'], $values['gtu_to_date']);
     
    
    
    $this->addDateFromToColumnQuery($query, array('ig_date'), $values['ig_from_date'], $values['ig_to_date']);
    $this->addDateFromToColumnQuery($query, array('acquisition_date'), $values['acquisition_from_date'], $values['acquisition_to_date']);
    //2019 02 25
    $this->addCreationDateFromToColumnQuery($query, array('modification_date_time'), $values['creation_from_date'], $values['creation_to_date']);
    
    
    
    //ftheeten 2016 03 24
    $this->addCatalogueRelationColumnQueryArrayRelations($query, $values['taxa_list'], $values['taxon_relation'],'taxonomy','taxon');
    $this->addCatalogueRelationColumnQueryArrayRelations($query, $values['chrono_item_ref'], $values['chrono_relation'],'chronostratigraphy','chrono');
    $this->addCatalogueRelationColumnQueryArrayRelations($query, $values['litho_item_ref'], $values['litho_relation'],'lithostratigraphy','litho');
    
    $this->addCatalogueRelationColumnQuery($query, $values['lithology_item_ref'], $values['lithology_relation'],'lithology','lithology', $values['lithology_child_syn_included']);
    $this->addCatalogueRelationColumnQuery($query, $values['mineral_item_ref'], $values['mineral_relation'],'mineralogy','mineral', $values['mineral_child_syn_included']);

   
    $query->limit($this->getCatalogueRecLimits());

    return $query;
  }
  
     //ftheeten 2019 02 25
   public function addCreationDateFromToColumnQuery(Doctrine_Query $query, array $dateFields, $val_from, $val_to)
  {
    if (count($dateFields) > 0 && ($val_from->getMask() > 0 || $val_to->getMask() > 0 ))
    {
      $query->innerJoin('s.UsersTrackingSpecimens ut');
      $query->andWhere("ut.referenced_relation = ? ",'specimens');
       $query->andWhere("ut.action = ? ",'insert');
      if($val_from->getMask() > 0 && $val_to->getMask() > 0)
      {
        if (count($dateFields) == 1)
        {
          $query->andWhere($dateFields[0] . " Between ? and ? ",
                           array($val_from->format('d/m/Y'),
                                 $val_to->format('d/m/Y')
                                )
                          );
        }
        else
        {
          $query->andWhere(" " . $dateFields[0] . " >= ? ", $val_from->format('d/m/Y'))
                ->andWhere(" " . $dateFields[1] . " <= ? ", $val_to->format('d/m/Y'));
        }
      }
      elseif ($val_from->getMask() > 0)
      {
        $sql = " (" . $dateFields[0] . " >= ? ) ";
        for ($i = 1; $i <= count($dateFields); $i++)
        {
          $vals[] = $val_from->format('d/m/Y');
        }
        if (count($dateFields) > 1) $sql .= " OR (" . $dateFields[1] . " >= ?) ";
        $query->andWhere($sql,
                         $vals
                        );
      }
      elseif ($val_to->getMask() > 0)
      {
        $sql = " (" . $dateFields[0] . " <= ? AND " . $dateFields[0] . ") ";
        for ($i = 1; $i <= count($dateFields); $i++)
        {
          $vals[] = $val_to->format('d/m/Y');
        }
        if (count($dateFields) > 1) $sql .= " OR (" . $dateFields[1] . " <= ? AND " . $dateFields[1] . ") ";
        $query->andWhere($sql,
                         $vals
                        );
      }
    }
    return $query;
  }
  
   //ftheeten 2016 06 22
 public function addSpecimenCountMinColumnQuery($query, $field, $val)
 {
    if( $val != '' ) {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere('s.specimen_count_min  >= '.$val);
    }
    return $query ;
  }
  
   //ftheeten 2016 06 22
 public function addSpecimenCountMaxColumnQuery($query, $field, $val)
 {
    if( $val != '' ) {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere('s.specimen_count_min  <= '.$val);
    }
    return $query ;
  }
  
  //ftheeten 2016 06 22
 public function addSpecimenCountMalesMinColumnQuery($query, $field, $val)
 {
    if( $val != '' ) {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere('s.specimen_count_males_min  >= '.$val);
    }
    return $query ;
  }
  
   //ftheeten 2016 06 22
 public function addSpecimenCountMalesMaxColumnQuery($query, $field, $val)
 {
    if( $val != '' ) {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere('s.specimen_count_males_max  <= '.$val);
    }
    return $query ;
  }
  
    //ftheeten 2016 06 22
 public function addSpecimenCountFemalesMinColumnQuery($query, $field, $val)
 {
    if( $val != '' ) {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere('s.specimen_count_females_min  >= '.$val);
    }
    return $query ;
  }
  
   //ftheeten 2016 06 22
 public function addSpecimenCountFemalesMaxColumnQuery($query, $field, $val)
 {
    if( $val != '' ) {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere('s.specimen_count_females_max  <= '.$val);
    }
    return $query ;
  }
  
      //ftheeten 2016 06 22
 public function addSpecimenCountJuvenilesMinColumnQuery($query, $field, $val)
 {
    if( $val != '' ) {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere('s.specimen_count_juveniles_min  >= '.$val);
    }
    return $query ;
  }
  
   //ftheeten 2016 06 22
 public function addSpecimenCountJuvenilesMaxColumnQuery($query, $field, $val)
 {
    if( $val != '' ) {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere('s.specimen_count_juveniles_max  <= '.$val);
    }
    return $query ;
  }
  
       //ftheeten 2018 04 10
   public function addIgRefColumnQuery($query, $field, $values)
  {
    if ($values != "") {
      $conn_MGR = Doctrine_Manager::connection();
      $query->andWhere("s.ig_ref= ?" , $values);
    }
    return $query;
  }

  public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/leaflet/leaflet.js';
    $javascripts[]='/js/map.js';
    $javascripts[]='/leaflet/leaflet.markercluster-src.js';
  	$javascripts[]= '/Leaflet.draw/dist/leaflet.draw.js';
      //ftheeten 2018 11 22
    $javascripts[]= "/select2-4.0.5/dist/js/select2.full.min.js";
	
    return $javascripts;
  }

  public function getStylesheets() {
    $items=parent::getStylesheets();
    $items['/leaflet/leaflet.css']='all';
    $items['/leaflet/MarkerCluster.css']='all';
	$items['/Leaflet.draw/dist/leaflet.draw.css']='all';
    //ftheeten 2018 11 22
    $items["/select2-4.0.5/dist/css/select2.min.css"]=  'all';
    return $items;
  }
}
