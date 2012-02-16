<?php

/**
 * SpecimensFlat filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SpecimensFlatFormFilter extends BaseSpecimensFlatFormFilter
{
  const SC_SPEC = 'specimen';
  const SC_IND = 'individual';
  const SC_PART = 'part';

  public function configure()
  {
    $this->useFields(array('gtu_code','gtu_from_date','gtu_to_date', 'taxon_level_ref', 'litho_name', 'litho_level_ref', 'litho_level_name', 'chrono_name', 'chrono_level_ref',
        'chrono_level_name', 'lithology_name', 'lithology_level_ref', 'lithology_level_name', 'mineral_name', 'mineral_level_ref',
        'mineral_level_name','ig_num','acquisition_category','acquisition_date'));

    $this->addPagerItems();

    $this->widgetSchema['gtu_code'] = new sfWidgetFormInputText();
    $this->widgetSchema['expedition_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));

    $this->widgetSchema['taxon_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['taxon_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'taxonomy'))),
        'add_empty' => $this->getI18N()->__('All')
      ));
    $rel = array('child'=>'Is a Child Of','direct_child'=>'Is a Direct Child','synonym'=> 'Is a Synonym Of', 'equal' => 'Is strictly equal to');
    
    $this->widgetSchema['taxon_relation'] = new sfWidgetFormChoice(array('choices'=> $rel));
    $this->widgetSchema['taxon_item_ref'] = new widgetFormButtonRef(array(
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

    $this->validatorSchema['taxon_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['taxon_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));


    $this->widgetSchema['lithology_relation'] = new sfWidgetFormChoice(array('choices'=> $rel));
    $this->widgetSchema['lithology_item_ref'] = new widgetFormButtonRef(array(
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

    $this->validatorSchema['lithology_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['lithology_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));


    $this->widgetSchema['lithology_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['lithology_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'lithology'))),
        'add_empty' => $this->getI18N()->__('All')
      ));

    $this->widgetSchema['litho_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['litho_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'lithostratigraphy'))),
        'add_empty' => $this->getI18N()->__('All')
      ));

    $this->widgetSchema['litho_relation'] = new sfWidgetFormChoice(array('choices'=> $rel));
    $this->widgetSchema['litho_item_ref'] = new widgetFormButtonRef(array(
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

    $this->validatorSchema['litho_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['litho_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));

    $this->widgetSchema['chrono_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['chrono_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'chronostratigraphy'))),
        'add_empty' => $this->getI18N()->__('All')
      ));

    $this->widgetSchema['chrono_relation'] = new sfWidgetFormChoice(array('choices'=> $rel));
    $this->widgetSchema['chrono_item_ref'] = new widgetFormButtonRef(array(
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

    $this->validatorSchema['chrono_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['chrono_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));


    $this->widgetSchema['mineral_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['mineral_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'mineralogy'))),
        'add_empty' => $this->getI18N()->__('All')
      ));

    $this->widgetSchema['mineral_item_ref'] = new widgetFormButtonRef(array(
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
    $this->widgetSchema['mineral_relation'] = new sfWidgetFormChoice(array('choices'=> $rel));

    $this->validatorSchema['mineral_item_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['mineral_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($rel)));


    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();
    $this->widgetSchema['ig_from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                     array('class' => 'from_date')
                                                                    );
    $this->widgetSchema['ig_to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                   array('class' => 'to_date')
                                                                  );
    $this->widgetSchema->setLabels(array('ig_from_date' => 'Between',
                                         'ig_to_date' => 'and',
                                        )
                                  );
    $this->widgetSchema['ig_num']->setAttributes(array('class'=>'small_size'));
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['ig_from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                       'from_date' => true,
                                                                       'min' => $minDate,
                                                                       'max' => $maxDate, 
                                                                       'empty_value' => $dateLowerBound,
                                                                      ),
                                                                 array('invalid' => 'Date provided is not valid',)
                                                                );
    $this->validatorSchema['ig_to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                     'from_date' => false,
                                                                     'min' => $minDate,
                                                                     'max' => $maxDate,
                                                                     'empty_value' => $dateUpperBound,
                                                                    ),
                                                               array('invalid' => 'Date provided is not valid',)
                                                              );
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('ig_from_date', 
                                                                          '<=', 
                                                                          'ig_to_date', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>'The "begin" date cannot be above the "end" date.')
                                                                         )
                                            );


    $this->widgetSchema['col_fields'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['collection_ref'] = new sfWidgetCollectionList(array('choices' => array()));
    $this->widgetSchema['collection_ref']->addOption('public_only',false);
    $this->validatorSchema['collection_ref'] = new sfValidatorPass(); //Avoid duplicate the query
    $this->widgetSchema['spec_ids'] = new sfWidgetFormTextarea(array('label'=>'#ID list'));

    $this->validatorSchema['spec_ids'] = new sfValidatorString( array('required' => false,'trim' => true));
    $this->validatorSchema['col_fields'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                ));

    $this->validatorSchema['gtu_code'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                )
                                                          );

    $this->validatorSchema['expedition_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['taxon_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['chrono_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['chrono_level_ref'] = new sfValidatorInteger(array('required' => false));    
    $this->validatorSchema['litho_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['litho_level_ref'] = new sfValidatorInteger(array('required' => false));  
    $this->validatorSchema['lithology_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['lithology_level_ref'] = new sfValidatorInteger(array('required' => false));  
    $this->validatorSchema['mineral_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['mineral_level_ref'] = new sfValidatorInteger(array('required' => false));


    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['tags'] = new sfWidgetFormInputText();
    $this->widgetSchema['gtu_from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                                array('class' => 'from_date')
                                                                               );
    $this->widgetSchema['gtu_to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                              array('class' => 'to_date')
                                                                             );
    $this->widgetSchema->setLabels(array('gtu_from_date' => 'Between',
                                         'gtu_to_date' => 'and',
                                        )
                                  );
    $this->validatorSchema['tags'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['gtu_from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                  'from_date' => true,
                                                                                  'min' => $minDate,
                                                                                  'max' => $maxDate, 
                                                                                  'empty_value' => $dateLowerBound,
                                                                                 ),
                                                                            array('invalid' => 'Date provided is not valid',)
                                                                           );
    $this->validatorSchema['gtu_to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                'from_date' => false,
                                                                                'min' => $minDate,
                                                                                'max' => $maxDate,
                                                                                'empty_value' => $dateUpperBound,
                                                                               ),
                                                                          array('invalid' => 'Date provided is not valid',)
                                                                         );

    $subForm = new sfForm();
    $this->embedForm('Tags',$subForm);

    $this->widgetSchema['tools'] = new widgetFormSelectDoubleListFilterable(
      array(
            'choices' => new sfCallable(array(Doctrine::getTable('CollectingTools'),'fetchTools')),
            'label_associated'=>$this->getI18N()->__('Selected'),
            'label_unassociated'=>$this->getI18N()->__('Available')
           ));
    $this->widgetSchema['methods'] = new widgetFormSelectDoubleListFilterable(
      array(
            'choices' => new sfCallable(array(Doctrine::getTable('CollectingMethods'),'fetchMethods')),
            'label_associated'=>$this->getI18N()->__('Selected'),
            'label_unassociated'=>$this->getI18N()->__('Available')
           ));
    $this->validatorSchema['methods'] = new sfValidatorPass();
    $this->validatorSchema['tools'] = new sfValidatorPass();


    /* Define list of options available for different type of searches to provide */
    $what_searched = array(self::SC_SPEC=>$this->getI18N()->__('Specimens'), 
                           self::SC_IND=>$this->getI18N()->__('Individuals'), 
                           self::SC_PART=>$this->getI18N()->__('Parts'));
    $this->widgetSchema['what_searched'] = new sfWidgetFormChoice(array(
        'choices' => $what_searched,
    ));

    $this->validatorSchema['what_searched'] = new sfValidatorChoice(array('choices'=>array_keys($what_searched), 'required'=>false,'empty_value'=>self::SC_SPEC));

    //people widget
    $this->widgetSchema['people_ref'] = new widgetFormButtonRef(array(
       'model' => 'People',
       'link_url' => 'people/searchBoth',
       'box_title' => $this->getI18N()->__('Choose people role'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );    
    $fields_to_search = array('spec_coll_ids' => $this->getI18N()->__('Collector'),
                              'spec_don_sel_ids' => $this->getI18N()->__('Donator or seller'),
                              'ident_ids' => $this->getI18N()->__('Identifier')) ;

    $this->widgetSchema['role_ref'] = new sfWidgetFormChoice(
      array('choices'=> $fields_to_search,
            'multiple' => true,
            'expanded' => true,
      ));
    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required' => false)) ;
    $this->validatorSchema['role_ref'] = new sfValidatorChoice(array('choices'=>array_keys($fields_to_search), 'required'=>false)) ;
    $this->validatorSchema['role_ref'] = new sfValidatorPass() ;
    /* Labels */
    $this->widgetSchema->setLabels(array('gtu_code' => 'Sampling Location code',
                                         'taxon_name' => 'Taxon text search',
                                         'litho_name' => 'Litho text search',
                                         'lithology_name' => 'Lithology text search',
                                         'chrono_name' => 'Chrono text search',
                                         'mineral_name' => 'Mineralo text search',
                                         'taxon_level_ref' => 'Level',
                                         'what_searched' => 'What would you like to search ?',
                                         'code_ref_relation' => 'Code of',
                                         'people_ref' => 'Whom are you looking for',
                                         'role_ref' => 'Which role'
                                        )
                                  );
    /* Acquisition categories */
    $this->widgetSchema['acquisition_category'] = new sfWidgetFormChoice(array(
      'choices' =>  array_merge(array('' => ''),SpecimensTable::getDistinctCategories()),
    ));

    $this->widgetSchema['acquisition_from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                                array('class' => 'from_date')
                                                                               );
    $this->widgetSchema['acquisition_to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                              array('class' => 'to_date')
                                                                             );
    $this->widgetSchema->setLabels(array('acquisition_from_date' => 'Between',
                                         'acquisition_to_date' => 'and',
                                        )
                                  );
    $this->validatorSchema['acquisition_from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                  'from_date' => true,
                                                                                  'min' => $minDate,
                                                                                  'max' => $maxDate, 
                                                                                  'empty_value' => $dateLowerBound,
                                                                                 ),
                                                                            array('invalid' => 'Date provided is not valid',)
                                                                           );
    $this->validatorSchema['acquisition_to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                'from_date' => false,
                                                                                'min' => $minDate,
                                                                                'max' => $maxDate,
                                                                                'empty_value' => $dateUpperBound,
                                                                               ),
                                                                          array('invalid' => 'Date provided is not valid',)
                                                                         );
  /**
  * Individuals Fields
  */
    $this->widgetSchema['type'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctTypeGroups',
        'method' => 'getTypeGroupFormated',
        'key_method' => 'getTypeGroup',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['type'] = new sfValidatorPass();

    $this->widgetSchema['sex'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctSexes',
        'method' => 'getSexSearchFormated',
        'key_method' => 'getSex',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['sex'] = new sfValidatorPass();

    $this->widgetSchema['stage'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctStages',
        'method' => 'getStageSearchFormated',
        'key_method' => 'getStage',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['stage'] = new sfValidatorPass();

    $this->widgetSchema['status'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctStates',
        'method' => 'getStateSearchFormated',
        'key_method' => 'getState',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['status'] = new sfValidatorPass();

    $this->widgetSchema['social'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctSocialStatuses',
        'method' => 'getSocialStatusSearchFormated',
        'key_method' => 'getSocialStatus',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['social'] = new sfValidatorPass();

    $this->widgetSchema['rockform'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctRockForms',
        'method' => 'getRockFormSearchFormated',
        'key_method' => 'getRockForm',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['rockform'] = new sfValidatorPass();

    $this->widgetSchema['container'] = new sfWidgetFormInput();
    $this->validatorSchema['container'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['sub_container'] = new sfWidgetFormInput();
    $this->validatorSchema['sub_container'] = new sfValidatorString(array('required' => false));



    $this->widgetSchema['institution_ref'] = new widgetFormButtonRef(array(
       'model' => 'Institutions',
       'link_url' => 'institution/choose?with_js=1',
       'method' => 'getFamilyName',
       'box_title' => $this->getI18N()->__('Choose Institution'),
       'nullable' => true,
     ));
    $this->widgetSchema['institution_ref']->setLabel('Institution');

    $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema['building'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'SpecimenParts',
      'table_method' => 'getDistinctBuildings',
      'method' => 'getBuildings',
      'key_method' => 'getBuildings',
      'add_empty' => true,
    ));

    $this->validatorSchema['building'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['floor'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'SpecimenParts',
      'table_method' => 'getDistinctFloors',
      'method' => 'getFloors',
      'key_method' => 'getFloors',
      'add_empty' => true,
    ));
    $this->validatorSchema['floor'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['row'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'SpecimenParts',
      'table_method' => 'getDistinctRows',
      'method' => 'getRows',
      'key_method' => 'getRows',
      'add_empty' => true,
    ));
    $this->validatorSchema['row'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['room'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'SpecimenParts',
      'table_method' => 'getDistinctRooms',
      'method' => 'getRooms',
      'key_method' => 'getRooms',
      'add_empty' => true,
    ));
    $this->validatorSchema['room'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['shelf'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'SpecimenParts',
      'table_method' => 'getDistinctShelfs',
      'method' => 'getShelfs',
      'key_method' => 'getShelfs',
      'add_empty' => true,
    ));
    $this->validatorSchema['shelf'] = new sfValidatorString(array('required' => false));

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

    $this->validatorSchema['lat_from'] = new sfValidatorNumber(array('required'=>false,'min' => '-90', 'max'=>'90'));
    $this->validatorSchema['lon_from'] = new sfValidatorNumber(array('required'=>false,'min' => '-180', 'max'=>'180'));
    $this->validatorSchema['lat_to'] = new sfValidatorNumber(array('required'=>false,'min' => '-90', 'max'=>'90'));
    $this->validatorSchema['lon_to'] = new sfValidatorNumber(array('required'=>false,'min' => '-180', 'max'=>'180'));

    sfWidgetFormSchema::setDefaultFormFormatterName('list');
    $this->widgetSchema->setNameFormat('specimen_search_filters[%s]');

  }

  public function addGtuTagValue($num)
  {
      $form = new TagLineForm(null,array('num'=>$num));
      $this->embeddedForms['Tags']->embedForm($num, $form);
      $this->embedForm('Tags', $this->embeddedForms['Tags']);
  }
  

  public function addCodeValue($num)
  {
      $form = new CodeLineForm();
      $this->embeddedForms['Codes']->embedForm($num, $form);
      $this->embedForm('Codes', $this->embeddedForms['Codes']);
  }

  public function addLatLonColumnQuery($query, $values)
  {
    if( $values['lat_from'] != '' && $values['lon_from'] != '' && $values['lon_to'] != ''  && $values['lat_to'] != '' )
    {
      $query->andWhere('
        ( station_visible = true AND gtu_location && ST_SetSRID(ST_MakeBox2D(ST_Point('.$values['lon_from'].', '.$values['lat_from'].'),
        ST_Point('.$values['lon_to'].', '.$values['lat_to'].')),4326) )
       OR 
        ( station_visible = false AND collection_ref in ('.implode(',',$this->encoding_collection).') 
        AND gtu_location && ST_SetSRID(ST_MakeBox2D(ST_Point('.$values['lon_from'].', '.$values['lat_from'].'),
        ST_Point('.$values['lon_to'].', '.$values['lat_to'].')),4326) )
      ');
      $query->whereParenWrap();
    }
    return $query;
  }

  public function addToolsColumnQuery($query, $field, $val)
  {
    if($val != '' && is_array($val) && !empty($val))
    {
      $query->andWhere('s.specimen_ref in (select fct_search_tools (?))',implode(',', $val));
    }
    return $query ;
  }

  public function addMethodsColumnQuery($query, $field, $val)
  {
    if($val != '' && is_array($val) && !empty($val))
    {
      $query->andWhere('s.specimen_ref in (select fct_search_methods (?))',implode(',', $val));
    }
    return $query ;
  }


  public function addIgNumColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $conn_MGR = Doctrine_Manager::connection();
       $query->andWhere("ig_num_indexed like concat(fullToIndex(".$conn_MGR->quote($values, 'string')."), '%') ");
     }
     return $query;
  } 

  public function addSexColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope == self::SC_SPEC)
      $this->exists_qry_ind[] = ' i1.sex in ('.implode(',',$val).') ';
    else
      $query->andWhere('i.sex in ('.implode(',',$val).')');
    return $query ;
  }

  public function addTypeColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope == self::SC_SPEC)
      $this->exists_qry_ind[] = ' i1.type_search in ('.implode(',',$val).') ';
    else
      $query->andWhere('i.type_search in ('.implode(',',$val).')');
    return $query ;
  }

  public function addStageColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope == self::SC_SPEC)
      $this->exists_qry_ind[] = ' i1.stage in ('.implode(',',$val).') ';
    else
      $query->andWhere('i.stage in ('.implode(',',$val).')');

    return $query ;
  }

  public function addStatusColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope == self::SC_SPEC)
      $this->exists_qry_ind[] = ' i1.state in ('.implode(',',$val).') ';
    else
      $query->andWhere('i.state in ('.implode(',',$val).')');
    return $query ;
  }

  public function addSocialColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope == self::SC_SPEC)
      $this->exists_qry_ind[] = ' i1.social_status in ('.implode(',',$val).') ';
    else
      $query->andWhere('i.social_status in ('.implode(',',$val).')');
    return $query ;
  }

  public function addRockformColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope == self::SC_SPEC)
      $this->exists_qry_ind[] = ' i1.rock_form in ('.implode(',',$val).') ';
    else
      $query->andWhere('i.rock_form in ('.implode(',',$val).')');

    return $query ;
  }

  public function addInstitutionRefColumnQuery($query, $field, $val)
  {
    if($val == '' &&  ! ctype_digit($val)) return ;
    $conn_MGR = Doctrine_Manager::connection();
    $val = $conn_MGR->quote($val, 'integer');

    if($this->scope != self::SC_PART)
      $this->exists_qry_part[] = ' p1.institution_ref =  '. $val;
    else
      $query->andWhere(' p1.institution_ref =  '.$val);

    return $query ;
  }

  public function addContainerColumnQuery($query, $field, $val)
  {

    if(trim($val) != '')
    {
      $values = explode(' ',$val);
      $query_value = array();
      foreach($values as $value)
      {
        if(trim($value) != '')
          $query_value[] = '%'.strtolower($value).'%';
      }
   

      if($this->scope != self::SC_PART)
      {
        if(! empty($query_value))
        {
          $conn_MGR = Doctrine_Manager::connection();
          $exist_qry ='';
          foreach($query_value as $k=>$param)
          {
            if($k != 0)
              $exist_qry .= ' or ';
            $exist_qry .= 'lower(p1.container) like '. $conn_MGR->quote($param, 'string') ;
          }
          $this->exists_qry_part[] = $exist_qry;
        }
      }
      else
      {
        $query_array = array_fill(0,count($query_value),'lower(p.container) like ?');
        $query->andWhere( implode(' or ',$query_array) ,$query_value);
      }
    }
    return $query ;
  }
  public function addSubContainerColumnQuery($query, $field, $val)
  {
    if(trim($val) != '')
    {
      $values = explode(' ',$val);
      $query_value = array();
      foreach($values as $value)
      {
        if(trim($value) != '')
          $query_value[] = '%'.strtolower($value).'%';
      }
   

      if($this->scope != self::SC_PART)
      {
        if(! empty($query_value))
        {
          $conn_MGR = Doctrine_Manager::connection();
          $exist_qry ='';
          foreach($query_value as $k=>$param)
          {
            if($k != 0)
              $exist_qry .= ' or ';
            $exist_qry .= 'lower(p1.sub_container) like '. $conn_MGR->quote($param, 'string') ;
          }
          $this->exists_qry_part[] = $exist_qry;
        }
      }
      else
      {
        $query_array = array_fill(0,count($query_value),'lower(p.sub_container) like ?');
        $query->andWhere( implode(' or ',$query_array) ,$query_value);
      }
    }
    return $query ;
  }

  public function addBuildingColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope != self::SC_PART)
      $this->exists_qry_part[] = ' p1.building in ('.implode(',',$val).') ';
    else
      $query->andWhere('p.building in ('.implode(',',$val).')');

    return $query ;
  }

  public function addFloorColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope != self::SC_PART)
      $this->exists_qry_part[] = ' p1.floor in ('.implode(',',$val).') ';
    else
      $query->andWhere('p.floor in ('.implode(',',$val).')');

    return $query ;
  }

  public function addRoomColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope != self::SC_PART)
      $this->exists_qry_part[] = ' p1.room in ('.implode(',',$val).') ';
    else
      $query->andWhere('p.room in ('.implode(',',$val).')');

    return $query ;
  }

  public function addRowColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope != self::SC_PART)
      $this->exists_qry_part[] = ' p1.row in ('.implode(',',$val).') ';
    else
      $query->andWhere('p.row in ('.implode(',',$val).')');

    return $query ;
  }

  public function addShelfColumnQuery($query, $field, $val)
  {
    if($val == '') return ;
    if(! is_array($val)) $val = array($val);
    $conn_MGR = Doctrine_Manager::connection();
    foreach($val as $k => $v)
      $val[$k] = $conn_MGR->quote($v, 'string');

    if($this->scope != self::SC_PART)
      $this->exists_qry_part[] = ' p1.shelf in ('.implode(',',$val).') ';
    else
      $query->andWhere('p.shelf in ('.implode(',',$val).')');

    return $query ;
  }

  public function addTagsColumnQuery($query, $field, $val)
  {
    $alias = $query->getRootAlias();
    $conn_MGR = Doctrine_Manager::connection();
    $tagList = '';

    foreach($val as $line)
    {
      $line_val = $line['tag'];
      if( $line_val != '')
      {
        $tagList = $conn_MGR->quote($line_val, 'string');
        $query->andWhere("
              (station_visible = true AND  gtu_tag_values_indexed && getTagsIndexedAsArray($tagList)) 
               OR
              (station_visible = false
               AND (
                    (
                      collection_ref in (".implode(',',$this->encoding_collection).")
                      AND gtu_tag_values_indexed && getTagsIndexedAsArray($tagList)
                    )
                    OR
                    (gtu_country_tag_indexed && getTagsIndexedAsArray($tagList))
                  )
              )");
        $query->whereParenWrap();
      }
    }
    return $query ;
  }

  public function addCodesColumnQuery($query, $field, $val)
  {   

    $str_params = '';
    $str_params_part = '' ;
    $params = array();
    $params_part = array() ;
    foreach($val as $i => $code)
    {
      if(empty($code)) continue;
      if($code['referenced_relation'] == 'specimens')
      {
        if($str_params != '')
          $str_params .= ',';
        $str_params .= '?,?,?,?,?';
        $params[] = $code['category'];
        $params[] = $code['code_part'];
        $params[] = $code['code_from'];
        $params[] = $code['code_to'];
        $params[] = $code['referenced_relation'];
      }
      else
      {
        if($str_params_part != '')
          $str_params_part .= ',';
        $str_params_part .= '?,?,?,?,?';
        $params_part[] = $code['category'];
        $params_part[] = $code['code_part'];
        $params_part[] = $code['code_from'];
        $params_part[] = $code['code_to'];
        $params_part[] = $code['referenced_relation'];
      }
      
    }
    if(! empty($params)) 
    {
      $query->addWhere("s.specimen_ref in (select fct_searchCodes($str_params) )", $params);
    }
    if(! empty($params_part)) 
    {
      if($this->scope == self::SC_SPEC)
        $query->addWhere("exists (select 1 from specimen_individuals i inner join specimen_parts p on p.specimen_individual_ref = i.id 
          inner join fct_searchCodes($str_params_part) c1 on c1 = p.id where i.specimen_ref=s.specimen_ref  )", $params_part);
      elseif($this->scope == self::SC_IND)
        $query->addWhere("exists (select 1 from specimen_parts p on p.specimen_individual_ref = i.id 
          inner join fct_searchCodes($str_params_part) c1 on c1 = p.id where i.specimen_ref=s.specimen_ref  )", $params_part);
      elseif($this->scope == self::SC_PART)
        $query->addWhere("p.id in (select fct_searchCodes($str_params_part) )", $params_part);

    }
    return $query ;
  }

  public function addGtuCodeColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $query->andWhere("
        (station_visible = true AND  LOWER(gtu_code) like ? )
        OR
        (station_visible = false AND collection_ref in (".implode(',',$this->encoding_collection).")
          AND LOWER(gtu_code) like ? )", array(strtolower('%'.$val.'%'),strtolower('%'.$val.'%')));
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
      if(ctype_digit($id))
        $clean_ids[] = $id;
    }

    if(! empty($clean_ids))
    {
      if($this->scope == self::SC_SPEC)
        $query->andWhereIn("s.specimen_ref", $clean_ids);
      elseif($this->scope == self::SC_IND)
        $query->andWhereIn("i.id", $clean_ids);
      else
        $query->andWhereIn("p.id", $clean_ids);
    }
    return $query ;
  }

  public function addPeopleSearchColumnQuery(Doctrine_Query $query, $people_id, $field_to_use)
  {
    $build_query = ''; 
    if(count($field_to_use) < 1)
      $field_to_use = array('ident_ids','spec_coll_ids','spec_don_sel_ids') ;

    foreach($field_to_use as $field)
    {
      if($field == 'ident_ids')
      {
        $build_query .= "(s.spec_ident_ids @> ARRAY[$people_id]::int[] OR ";
        if($this->scope == self::SC_SPEC)
        {
          $build_query .= " exists ( select 1 from specimen_individuals i1
          INNER JOIN specimen_parts p1 on i1.id = p1.specimen_individual_ref
          where i1.specimen_ref= s.specimen_ref AND  i1.ind_ident_ids @> ARRAY[$people_id]::int[] ) ";
        }
        else
        {
          $build_query .= " i.ind_ident_ids @> ARRAY[$people_id]::int[] ";
        }
        $build_query .= ") OR " ;
      }
      elseif($field == 'spec_coll_ids')
      {
        $build_query .= "s.spec_coll_ids @> ARRAY[$people_id]::int[] OR " ;
      }
      else
      {
        $build_query .= "s.spec_don_sel_ids @> ARRAY[$people_id]::int[] OR " ;    
      }
    }
    // I remove the last 'OR ' at the end of the string
    $build_query = substr($build_query,0,strlen($build_query) -3) ;
    $query->andWhere($build_query) ;
    return $query ;
  }

  public function addCollectionRefColumnQuery($query, $field, $val)
  {
    //Do Nothing here, the job is done in the doBuildQuery with check collection rights
    return $query;
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['Codes'])&& is_array($taintedValues['Codes']))
    {

      foreach($taintedValues['Codes'] as $key=>$newVal)
      {
        if (!isset($this['Codes'][$key]))
        {
          $this->addCodeValue($key);
        }
      }
    }
    else
    {
      $this->offsetUnset('Codes') ;
      $subForm = new sfForm();
      $this->embedForm('Codes',$subForm);
      $taintedValues['Codes'] = array();
    }

    if(isset($taintedValues['Tags'])&& is_array($taintedValues['Tags']))
    {
      foreach($taintedValues['Tags'] as $key=>$newVal)
      {
        if (!isset($this['Tags'][$key]))
        {
          $this->addGtuTagValue($key);
        }
      }
    }
    else
    {
      $this->offsetUnset('Tags') ;
      $subForm = new sfForm();
      $this->embedForm('Tags',$subForm);
      $taintedValues['Tags'] = array();
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function doBuildQuery(array $values)
  {
    $this->exists_qry_ind = array();
    $this->exists_qry_part = array();
    $this->scope = $values['what_searched'];

    if($this->scope == self::SC_SPEC)
    {
      $query = DQ::create()
        ->select('s.*')
        ->from('SpecimensFlat s');
    }
    elseif($this->scope == self::SC_IND)
    {
      $query = DQ::create()
        ->select('s.*, i.*, p.*')
        ->from('SpecimenIndividuals i')
        ->innerJoin('i.SpecimensFlat s');
    }
    elseif($this->scope == self::SC_PART)
    {
      $query = DQ::create()
        ->select('s.*, i.*, p.*')
        ->from('SpecimenParts p')
        ->innerJoin('p.Individual i')
        ->innerJoin('i.SpecimensFlat s');
    }

    $this->options['query'] = $query;


    $this->encoding_collection = $this->getCollectionWithRights($this->options['user'],true);

    $query = parent::doBuildQuery($values);

    /** ADD exists to the query **/
    $where_exist = '';
    if($this->scope == self::SC_SPEC && ! empty($this->exists_qry_part))
    {
       $query->where('exists ( select 1 from specimen_individuals i1
          INNER JOIN specimen_parts p1 on i1.id = p1.specimen_individual_ref
          where i1.specimen_ref= s.specimen_ref AND '. implode(' AND ', array_merge($this->exists_qry_ind,$this->exists_qry_part)). ')');
    }
    elseif(! empty($this->exists_qry_ind)) // When scope = spec and look on ind only
    {
        $query->where('exists ( select 1 from specimen_individuals i1
          where i1.specimen_ref= s.specimen_ref AND '. implode(' AND ', $this->exists_qry_ind). ')');
    }
    elseif(! empty($this->exists_qry_part)) // When scope = ind and look on part
    {
        $alias = $query->getRootAlias();
        $query->where('exists ( select 1 from specimen_parts p1
          where p1.specimen_individual_ref= s.specimen_ref AND '. implode(' AND ', $this->exists_qry_part). ')');
    }

    $query->addSelect('(collection_ref in ('.implode(',',$this->encoding_collection).')) as has_encoding_rights');

    $this->cols = $this->getCollectionWithRights($this->options['user']);
    if(!empty($values['collection_ref']))
    {
      $this->cols = array_intersect($values['collection_ref'], $this->cols);
    }
    $query->andwhere('collection_ref in ( '.implode(',',$this->cols). ') ');

    if ($values['people_ref'] != '') $this->addPeopleSearchColumnQuery($query, $values['people_ref'], $values['role_ref']);
    if ($values['acquisition_category'] != '' ) $query->andWhere('acquisition_category = ?',$values['acquisition_category']);
    if ($values['taxon_level_ref'] != '') $query->andWhere('taxon_level_ref = ?', intval($values['taxon_level_ref']));
    if ($values['chrono_level_ref'] != '') $query->andWhere('chrono_level_ref = ?', intval($values['chrono_level_ref']));
    if ($values['litho_level_ref'] != '') $query->andWhere('litho_level_ref = ?', intval($values['litho_level_ref']));    
    if ($values['lithology_level_ref'] != '') $query->andWhere('lithology_level_ref = ?', intval($values['lithology_level_ref']));
    if ($values['mineral_level_ref'] != '') $query->andWhere('mineral_level_ref = ?', intval($values['mineral_level_ref']));
    $this->addLatLonColumnQuery($query, $values);
    $this->addNamingColumnQuery($query, 'expeditions', 's.name_ts', $values['expedition_name'],'s','expedition_name_ts');

    $this->addNamingColumnQuery($query, 'taxonomy', 'name_indexed', $values['taxon_name'],'s','taxon_name_indexed');
    $this->addNamingColumnQuery($query, 'chronostratigraphy', 'name_indexed', $values['chrono_name'],'s','chrono_name_indexed');
    $this->addNamingColumnQuery($query, 'lithostratigraphy', 'name_indexed', $values['litho_name'],'s','litho_name_indexed');
    $this->addNamingColumnQuery($query, 'lithology', 'name_indexed', $values['lithology_name'],'s','lithology_name_indexed');
    $this->addNamingColumnQuery($query, 'mineralogy', 'name_indexed', $values['mineral_name'],'s','mineral_name_indexed');

    $fields = array('gtu_from_date', 'gtu_to_date');
    $this->addDateFromToColumnQuery($query, $fields, $values['gtu_from_date'], $values['gtu_to_date']);
    $this->addDateFromToColumnQuery($query, array('ig_date'), $values['ig_from_date'], $values['ig_to_date']);    
    $this->addDateFromToColumnQuery($query, array('acquisition_date'), $values['acquisition_from_date'], $values['acquisition_to_date']);

    $this->addCatalogueRelationColumnQuery($query, $values['taxon_item_ref'], $values['taxon_relation'],'taxonomy','taxon');
    $this->addCatalogueRelationColumnQuery($query, $values['chrono_item_ref'], $values['chrono_relation'],'chronostratigraphy','chrono');
    $this->addCatalogueRelationColumnQuery($query, $values['litho_item_ref'], $values['litho_relation'],'lithostratigraphy','litho');
    $this->addCatalogueRelationColumnQuery($query, $values['lithology_item_ref'], $values['lithology_relation'],'lithology','lithology');
    $this->addCatalogueRelationColumnQuery($query, $values['mineral_item_ref'], $values['mineral_relation'],'mineralogy','mineral');

    $query->limit($this->getCatalogueRecLimits());

    return $query;
  }

  public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/js/OpenLayers.js';
    $javascripts[]='/js/map.js';
    return $javascripts;
  }


}
