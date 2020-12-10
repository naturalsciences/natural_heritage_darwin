<?php

/**
 * StorageParts form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class StoragePartsSubForm extends StoragePartsForm
{
  public function configure()
  {
  

        $this->useFields(array( 'category', 'specimen_part', 'complete', 'institution_ref', 'building', 
       'floor', 'room', 'row', 'col', 'shelf', 'container', 'sub_container', 'container_type', 
       'sub_container_type', 'container_storage', 'sub_container_storage', 
       'surnumerary', 'object_name', 'object_name_indexed', 'specimen_status'));
       
    /*$this->widgetSchema['specimen_status'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctSpecimenStatuses',
      'method' => 'getSpecimenStatus',
      'key_method' => 'getSpecimenStatus',
      'add_empty' => true,
      'change_label' => 'Pick a status in the list',
      'add_label' => 'Add another status',
    ));*/
    
     $this->widgetSchema['specimen_status'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['specimen_status']->setAttributes(array('class'=>'autocomplete_for_status'));

     $this->widgetSchema['category'] = new sfWidgetFormChoice(array(
      'choices' => StorageParts::getCategories(),
    ));

    $this->validatorSchema['category'] = new sfValidatorChoice(array('choices'=>array_keys(StorageParts::getCategories())));
    
    /*$this->widgetSchema['specimen_part'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctParts',
      'method' => 'getSpecimenPart',
      'key_method' => 'getSpecimenPart',
      'add_empty' => false,
      'change_label' => 'Pick parts in the list',
      'add_label' => 'Add another part',
    ));*/
    
     $this->widgetSchema['specimen_part'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['specimen_part']->setAttributes(array('class'=>'autocomplete_for_parts'));
    
    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false, 'trim' => true));
       
       
     $this->widgetSchema['institution_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Institutions',
      'link_url' => 'institution/choose?with_js=1',
      'method' => 'getFamilyName',
      'box_title' => $this->getI18N()->__('Choose Institution'),
      'complete_url' => 'catalogue/completeName?table=institutions',
      'nullable' => true,
    ));
    $this->widgetSchema['institution_ref']->setLabel("Institution");
    $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required'=>true));
    if(array_key_exists('institution_ref_session',$_COOKIE ))
    {
        $this->setDefault('institution_ref', $_COOKIE['institution_ref_session']);
    }
    elseif(sfConfig::get('dw_defaultInstitutionRef')) {
      $this->setDefault('institution_ref', sfConfig::get('dw_defaultInstitutionRef'));
    }
    
     $this->widgetSchema['object_name'] = new sfWidgetFormInput();
     $this->validatorSchema['object_name'] = new sfValidatorString(array('required' => false, 'trim' => true));
     
     
    /* $this->widgetSchema['building'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctBuildings',
      'method' => 'getBuildings',
      'key_method' => 'getBuildings',
      'add_empty' => true,
      'change_label' => 'Pick a building in the list',
      'add_label' => 'Add another building',
    ));*/
    
    $this->widgetSchema['building'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['building']->setAttributes(array('class'=>'autocomplete_for_building'));
 

   /* $this->widgetSchema['floor'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctFloors',
      'method' => 'getFloors',
      'key_method' => 'getFloors',
      'add_empty' => true,
      'change_label' => 'Pick a floor in the list',
      'add_label' => 'Add another floor',
    ));*/
    
    $this->widgetSchema['floor'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['floor']->setAttributes(array('class'=>'autocomplete_for_floor'));

    /*$this->widgetSchema['row'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctRows',
      'method' => 'getRows',
      'key_method' => 'getRows',
      'add_empty' => true,
      'change_label' => 'Pick a row in the list',
      'add_label' => 'Add another row',
    ));*/
    
     $this->widgetSchema['row'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['row']->setAttributes(array('class'=>'autocomplete_for_row'));


    /*$this->widgetSchema['col'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctCols',
      'method' => 'getCols',
      'key_method' => 'getCols',
      'add_empty' => true,
      'change_label' => 'Pick a col in the list',
      'add_label' => 'Add another col',
    ));*/
    
        $this->widgetSchema['col'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['col']->setAttributes(array('class'=>'autocomplete_for_col'));
    
    /*$this->widgetSchema['room'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctRooms',
      'method' => 'getRooms',
      'key_method' => 'getRooms',
      'add_empty' => true,
      'change_label' => 'Pick a room in the list',
      'add_label' => 'Add another room',
    ));*/
    
    $this->widgetSchema['room'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['room']->setAttributes(array('class'=>'autocomplete_for_room'));

    /*$this->widgetSchema['shelf'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctShelfs',
      'method' => 'getShelfs',
      'key_method' => 'getShelfs',
      'add_empty' => true,
      'change_label' => 'Pick a shelf in the list',
      'add_label' => 'Add another shelf',
    ));*/
    
    $this->widgetSchema['shelf'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['shelf']->setAttributes(array('class'=>'autocomplete_for_shelf'));
     
      $this->widgetSchema['container_type'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctContainerTypes',
      'method' => 'getContainerType',
      'key_method' => 'getContainerType',
      'add_empty' => true,
      'change_label' => 'Pick a container in the list',
      'add_label' => 'Add another container',
      ));

    $this->widgetSchema['sub_container_type'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'table_method' => 'getDistinctSubContainerTypes',
      'method' => 'getSubContainerType',
      'key_method' => 'getSubContainerType',
      'add_empty' => true,
      'change_label' => 'Pick a sub container type in the list',
      'add_label' => 'Add another sub container type',
    ));
    
    $this->widgetSchema['container'] = new sfWidgetFormInput();
    $this->widgetSchema['sub_container'] = new sfWidgetFormInput();


    $this->widgetSchema['container_storage'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'change_label' => 'Pick a container storage in the list',
      'add_label' => 'Add another container storage',
      'add_empty' => true,
    ));
     $this->validatorSchema['container_storage'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['sub_container_storage'] = new widgetFormSelectComplete(array(
      'model' => 'StorageParts',
      'change_label' => 'Pick a sub container storage in the list',
      'add_label' => 'Add another sub container storage',
    ));
     
     
     // needed to unset the whole form
    
      $this->widgetSchema['check']= new sfWidgetFormInputHidden();
          $this->validatorSchema['check'] = new sfValidatorPass();
          
                 $this->widgetSchema->setLabels(array(
          
          'surnumerary' => 'supernumerary',
          'col' => 'Column',
        ));
        
        $this->forceContainerChoices();
  }
  
    public function forceContainerChoices()
      {
        $this->widgetSchema['container_storage']->setOption('forced_choices',
          Doctrine::getTable('StorageParts')->getDistinctContainerStorages($this->getObject()->getContainerType())
        );

        $this->widgetSchema['sub_container_storage']->setOption('forced_choices',
          Doctrine::getTable('StorageParts')->getDistinctSubContainerStorages($this->getObject()->getSubContainerType())
        );
      }
}
