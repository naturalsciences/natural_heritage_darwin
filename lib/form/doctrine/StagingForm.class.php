<?php

/**
 * Staging form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class StagingForm extends BaseStagingForm
{
  public function configure()
  {
    $array_of_field = $this->options['fields'] ;
    if (in_array('identifiers',$array_of_field)) unset($array_of_field[array_search('identifiers', $array_of_field)]);
    if (in_array('people',$array_of_field)) unset($array_of_field[array_search('people', $array_of_field)]);    
    $this->useFields($array_of_field) ;
    if (in_array('spec_ref',$array_of_field))
    {
      $this->widgetSchema['spec_ref'] = new widgetFormButtonRef(array(
         'model' => 'Staging',
         'link_url' => 'specimen/choose',
         'method' => 'getName',
         'box_title' => $this->getI18N()->__('Choose Specimen'),
         'nullable' => true,
         'button_class'=>'',
       ),
        array('class'=>'inline',
             )
      );    
      $this->validatorSchema['spec_ref'] = new sfValidatorInteger(array('required'=>false, 'empty_value'=>0));
    }      
    /* Taxonomy Reference */
    if(in_array('taxon_ref',$this->options['fields']))
    {  
      $this->widgetSchema['taxon_ref'] = new widgetFormButtonRef(array(
         'model' => 'Staging',
         'link_url' => 'taxonomy/choose?name='.$this->getObject()->getTaxonName(),
         'method' => 'getTaxon',
         'default_name' => $this->getObject()->getTaxonName(),
         'box_title' => $this->getI18N()->__('Choose Taxon'),
         'nullable' => true,
         'button_class'=>'',
       ),
        array('class'=>'inline',
             )
      );
      $this->validatorSchema['taxon_ref'] = new sfValidatorInteger(array('required'=>false));
    }      
    /* Chronostratigraphy Reference */
    if(in_array('chrono_ref',$this->options['fields']))
    {
      $this->widgetSchema['chrono_ref'] = new widgetFormButtonRef(array(
         'model' => 'Staging',
         'link_url' => 'chronostratigraphy/choose?name='.$this->getObject()->getChronoName(),
         'method' => 'getChrono',
         'default_name' => $this->getObject()->getChronoName(),       
         'box_title' => $this->getI18N()->__('Choose Chronostratigraphic unit'),
         'nullable' => true,
         'button_class'=>'',
       ),
        array('class'=>'inline',
             )
      );
      $this->validatorSchema['chrono_ref'] = new sfValidatorInteger(array('required'=>false));      
    }

    /* Lithostratigraphy Reference */
    if(in_array('litho_ref',$this->options['fields']) )
    {   
      $this->widgetSchema['litho_ref'] = new widgetFormButtonRef(array(
         'model' => 'Staging',
         'link_url' => 'lithostratigraphy/choose?name='.$this->getObject()->getLithoName(),
         'method' => 'getLitho',
         'default_name' => $this->getObject()->getLithoName(),       
         'box_title' => $this->getI18N()->__('Choose Lithostratigraphic unit'),
         'nullable' => true,
         'button_class'=>'',
       ),
        array('class'=>'inline',
             )
      );
      $this->validatorSchema['litho_ref'] = new sfValidatorInteger(array('required'=>false));
    }

    /* Lithology Reference */
    if(in_array('lithology_ref',$this->options['fields']))    
    {
      $this->widgetSchema['lithology_ref'] = new widgetFormButtonRef(array(
         'model' => 'Staging',
         'link_url' => 'lithology/choose?name='.$this->getObject()->getLithologyName(),
         'method' => 'getLithology',
         'default_name' => $this->getObject()->getLithologyName(),       
         'box_title' => $this->getI18N()->__('Choose Lithologic unit'),
         'nullable' => true,
         'button_class'=>'',
       ),
        array('class'=>'inline',
             )
      );
      $this->validatorSchema['lithology_ref'] = new sfValidatorInteger(array('required'=>false));
    }

    /* Mineralogy Reference */
    if(in_array('mineral_ref',$this->options['fields']))
    {    
      $this->widgetSchema['mineral_ref'] = new widgetFormButtonRef(array(
         'model' => 'Staging',
         'link_url' => 'mineralogy/choose?name='.$this->getObject()->getMineralName(),
         'method' => 'getMineral',
         'default_name' => $this->getObject()->getMineralName(),
         'box_title' => $this->getI18N()->__('Choose Mineralogic unit'),
         'nullable' => true,
         'button_class'=>'',
       ),
        array('class'=>'inline',
             )
      );
      $this->validatorSchema['mineral_ref'] = new sfValidatorInteger(array('required'=>false));
    }

    /* IG number Reference */
    if(in_array('ig_ref',$this->options['fields']))    
      $this->widgetSchema['ig_ref'] = new widgetFormInputChecked(
        array(
          'model' => 'Igs',
          'method' => 'getIgNum',
          'nullable' => true,
          'link_url' => 'igs/searchFor',
        )
      );
        
    /* Expedition Reference */
    if(in_array('expedition_ref',$this->options['fields']) ) 
    {  
      $this->widgetSchema['expedition_ref'] = new widgetFormButtonRef(array(
         'model' => 'Expeditions',
         'link_url' => 'expedition/choose?name='.$this->getObject()->getExpeditionName(),
         'method' => 'getExpedition',
         'default_name' => $this->getObject()->getExpeditionName(),         
         'box_title' => $this->getI18N()->__('Choose Expedition'),
         'nullable' => true,
         'button_class'=>'',
       ),
        array('class'=>'inline',
             )
      );
      $this->validatorSchema['expedition_ref'] = new sfValidatorInteger(array('required'=>false));
    }
    
    /* Gtu Reference */
    if(in_array('gtu_ref',$this->options['fields']) )  
    { 
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
      $this->validatorSchema['gtu_ref'] = new sfValidatorInteger(array('required'=>false));
    }
    
    /* Lithology Reference */
    if(in_array('institution_ref',$this->options['fields']))    
    {
      $this->widgetSchema['institution_ref'] = new widgetFormButtonRef(array(
         'model' => 'Staging',
         'link_url' => 'institution/choose?name='.$this->getObject()->getInstitutionName(),
         'method' => 'getInstitution',
         'default_name' => $this->getObject()->getInstitutionName(),       
         'box_title' => $this->getI18N()->__('Choose an Institution'),
         'nullable' => true,
         'button_class'=>'',
       ),
        array('class'=>'inline',
             )
      );
      $this->validatorSchema['institution_ref'] = new sfValidatorInteger(array('required'=>false));
    }      
    /* Host Reference *//*
    if(in_array('host_ref',$this->options['fields']) )  
    {    
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
    }
      
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
      
      $this->widgetSchema['accuracy'] = new sfWidgetFormChoice(array(
          'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
          'expanded' => true,
      ));*/
    if(in_array('people',$this->options['fields']) )        
    {    
      $subForm = new sfForm();
      $this->embedForm('WrongPeople',$subForm);      
      foreach(Doctrine::getTable("stagingPeople")->getPeopleInError($this->getObject()->getId()) as $key=>$people)
      {       
        $form = new PeopleInErrorForm($people);
        $this->embeddedForms['WrongPeople']->embedForm($key, $form);
      } 
      $this->embedForm('WrongPeople', $this->embeddedForms['WrongPeople']); 
    }  
           
/*    if(in_array('donators',$this->options['fields']) )        
    {    
      $subForm = new sfForm();
      $this->embedForm('WrongDonators',$subForm);      
      foreach($this->getObject()->getPeopleInError('donator',$this->getObject()->getDonators()) as $key=>$vals)
      {       
        $val = new CataloguePeople();
        $val->fromArray(array('people_type' => 'donator','referenced_relation' => 'staging', 'order_by' => $key, 'record_id' => $this->getObject()->getId()));
        $form = new PeopleInErrorForm($val, array('default_name'=> $vals, 'only_role' => 0, 'donator'=> true));
        $this->embeddedForms['WrongDonators']->embedForm($key, $form);      
      } 
      $this->embedForm('WrongDonators', $this->embeddedForms['WrongDonators']); 
    } */
      
    if(in_array('identifiers',$this->options['fields']) )        
    {    
      // $identifications containts all indentification id of this staging id
      $identifications = Doctrine::getTable('identifications')->getStagingIds($this->getObject()->getId()) ;
      $subForm = new sfForm();
      $this->embedForm('WrongIdentifiers',$subForm);      
      foreach(Doctrine::getTable("stagingPeople")->getPeopleInError($identifications) as $key=>$people)
      {      
        $form = new PeopleInErrorForm($people);
        $this->embeddedForms['WrongIdentifiers']->embedForm($key, $form);      
      } 
      $this->embedForm('WrongIdentifiers', $this->embeddedForms['WrongIdentifiers']); 
    }           
  }
  
  public function loadEmbedPeople($people)
  {
    if($this->isBound()) return;
    $subForm = new sfForm();
    $this->embedForm('WrongPeople',$subForm);
    foreach($people as $key=>$vals)
    {       
      $val = new StagingPeople();
      $val->fromArray($vals);
      $form = new PeopleInErrorForm($val);
      $this->embeddedForms['WrongPeople']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('WrongPeople', $this->embeddedForms['WrongPeople']);
  } 
  
 /* public function loadEmbedDonators($donator)
  {
    if($this->isBound()) return;
    $subForm = new sfForm();
    $this->embedForm('WrongDonators',$subForm);
    foreach($donator as $key=>$vals)
    {       
      $val = new CataloguePeople();
      $val->fromArray($vals);
      $form = new PeopleInErrorForm($val,array('only_role' => 0, 'donator'=> true));
      $this->embeddedForms['WrongDonators']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('WrongDonators', $this->embeddedForms['WrongDonators']);
  }*/
   
  public function loadEmbedIdentifiers($identifier)
  {
    if($this->isBound()) return;
    $subForm = new sfForm();
    $this->embedForm('WrongIdentifiers',$subForm);
    foreach($identifier as $key=>$vals)
    {       
      $val = new StagingPeople();
      $val->fromArray($vals);
      $form = new PeopleInErrorForm($val);
      $this->embeddedForms['WrongIdentifiers']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('WrongIdentifiers', $this->embeddedForms['WrongIdentifiers']);
  }   
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['WrongPeople'])) $this->loadEmbedPeople($taintedValues['WrongPeople']); 
 //   if(isset($taintedValues['WrongDonators'])) $this->loadEmbedDonators($taintedValues['WrongDonators']); 
    if(isset($taintedValues['WrongIdentifiers'])) $this->loadEmbedIdentifiers($taintedValues['WrongIdentifiers']);         
    parent::bind($taintedValues, $taintedFiles);    
  }
  
  public function save($con = null, $forms = null) 
  {
    $status = $this->getObject()->getFields(true) ;
    if(is_numeric($this->getValue('taxon_ref'))) $status['taxon'] = 'done' ; 
    else unset($this['taxon_ref']) ;
    if(is_numeric($this->getValue('chrono_ref'))) $status['chrono'] = 'done' ;    
    else unset($this['chrono_ref']) ;
    if(is_numeric($this->getValue('mineral_ref'))) $status['mineral'] = 'done' ;    
    else unset($this['mineral_ref']) ;    
    if(is_numeric($this->getValue('litho_ref'))) $status['litho'] = 'done' ;    
    else unset($this['litho_ref']) ;    
    if(is_numeric($this->getValue('lithology_ref'))) $status['lithology'] = 'done' ;    
    else unset($this['lithology_ref']) ;    
    if(is_numeric($this->getValue('igs_ref'))) $status['igs'] = 'done' ;        
    else unset($this['igs_ref']) ;   
    if($this->getValue('spec_ref') != 0) $status['duplicate'] = 'done' ;        
    else unset($this['spec_ref']) ;
    if(is_numeric($this->getValue('institution_ref'))) $status['institution'] = 'done' ;        
    else unset($this['institution_ref']) ;        
    if($value = $this->getValue('WrongPeople')) 
    {
      unset($this['people']) ; 
      foreach($this->embeddedForms['WrongPeople']->getEmbeddedForms() as $name => $form)
      {
        if (isset($value[$name]['people_ref'])) Doctrine::getTable('StagingPeople')->UpdatePeopleRef($value[$name]) ;
        unset($this->embeddedForms['WrongPeople'][$name]);
      }
    }
    if($value = $this->getValue('WrongIdentifiers')) 
    {
      unset($this['identifiers']) ;     
      foreach($this->embeddedForms['WrongIdentifiers']->getEmbeddedForms() as $name => $form)
      {
        if (isset($value[$name]['people_ref']))  Doctrine::getTable('StagingPeople')->UpdatePeopleRef($value[$name]) ;
        unset($this->embeddedForms['WrongIdentifiers'][$name]);
      }
    }
    $this->getObject()->setStatus($status) ;      
    return parent::save($con, $forms);
  }
}
