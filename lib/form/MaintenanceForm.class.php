<?php

/**
 * Maintenance form.
 *
 * @package    form
 * @subpackage Maintenance
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class MaintenanceForm extends BaseCollectionMaintenanceForm
{
  public function configure()
  {
	$this->useFields(array('id','people_ref', 'category', 'action_observation', 'description','modification_date_time'));

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));

	$this->widgetSchema['modification_date_time'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
		'image'=>'/images/calendar.gif', 
		'format' => '%day%/%month%/%year%', 
		'years' => $years,
		'with_time' => true
	  ),
	  array('class' => 'from_date')
	  );


    $this->validatorSchema['modification_date_time'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      'with_time' => true
	  ),
	  array('invalid' => 'Invalid date "from"')
    );

	$this->widgetSchema['people_ref'] = new widgetFormButtonRef(array(
	  'model' => 'People',
	  'method' => 'getFormatedName',
	  'link_url' => 'people/choose',
	  'nullable' => false,
	  'box_title' => $this->getI18N()->__('Choose Yourself'),
	));

	$this->widgetSchema['parts_ids'] = new sfWidgetFormInputHidden();
	$this->validatorSchema['parts_ids'] = new sfValidatorString(array('required' => false, 'empty_value' => ''));

	$this->widgetSchema['category'] = new sfWidgetFormChoice(array('choices' => array('action' => 'Action', 'observation'=>'Observation')));
	$this->widgetSchema['category']->setLabel('Type');
	$this->widgetSchema['action_observation']->setLabel('Maintenance Achieved');
	$this->widgetSchema['action_observation'] = new widgetFormSelectComplete(array(
	  'model' => 'CollectionMaintenance',
	  'table_method' => 'getDistinctActions',
	  'method' => 'getAction',
	  'key_method' => 'getAction',
	  'add_empty' => false,
	  'change_label' => 'Pick an action in the list',
	  'add_label' => 'Add another action',
    ));
    
    $this->widgetSchema->setNameFormat('collection_maintenance[%s]');    
    /* input file for related files */
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();
    $this->widgetSchema['filenames']->setLabel("Add File") ;
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));        
    $this->validatorSchema['filenames'] = new sfValidatorPass() ;    
    
    $this->widgetSchema['comment'] = new sfWidgetFormInputHidden(array('default'=>1));    
    $this->widgetSchema['relatedfile'] = new sfWidgetFormInputHidden(array('default'=>1));    
    $this->widgetSchema['extlink'] = new sfWidgetFormInputHidden(array('default'=>1));            
    $this->validatorSchema['comment'] = new sfValidatorPass();
    $this->validatorSchema['extlink'] = new sfValidatorPass();
    $this->validatorSchema['relatedfile'] = new sfValidatorPass();    
  }
  
  public function addRelatedFiles($num,$file=null)
  {
    if(! isset($this['newRelatedFiles'])) $this->loadEmbedRelatedFiles();
    $options = array('referenced_relation' => 'collection_maintenance');
    if($file) $options = $file ;
    $val = new Multimedia();
    $val->fromArray($options);
    $val->setRecordId($this->getObject()->getId());
    $form = new MultimediaForm($val);
    $this->embeddedForms['newRelatedFiles']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('newRelatedFiles', $this->embeddedForms['newRelatedFiles']);
  }
  
  public function loadEmbedRelatedFiles()
  {
    if($this->isBound()) return;

    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('RelatedFiles',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('Multimedia')->findForTable('collection_maintenance', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new MultimediaForm($vals);
        $this->embeddedForms['RelatedFiles']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('RelatedFiles', $this->embeddedForms['RelatedFiles']);
    }

    $subForm = new sfForm();
    $this->embedForm('newRelatedFiles',$subForm);
  }
    
  public function addComments($num, $obj=null)
  {
      if(! isset($this['newComments'])) $this->loadEmbedComments();
      $options = array('referenced_relation' => 'collection_maintenance', 'record_id' => $this->getObject()->getId());
      if (!$obj) $val = new Comments();
      else $val = $obj ; 
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new CommentsSubForm($val,array('table' => 'collection_maintenance'));
      $this->embeddedForms['newComments']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newComments', $this->embeddedForms['newComments']);
  }  
  
  public function loadEmbedComments()
  {
    if($this->isBound()) return;
    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('Comments',$subForm);    
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('comments')->findForTable('collection_maintenance', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new CommentsSubForm($vals,array('table' => 'collection_maintenance'));
        $this->embeddedForms['Comments']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Comments', $this->embeddedForms['Comments']);
    }

    $subForm = new sfForm();
    $this->embedForm('newComments',$subForm);
  }
  
  public function addExtLinks($num, $obj=null)
  {
      if(! isset($this['newExtLinks'])) $this->loadEmbedLink();
      $options = array('referenced_relation' => 'collection_maintenance', 'record_id' => $this->getObject()->getId());
      if(!$obj) $val = new ExtLinks();
      else $val = $obj ;      
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new ExtLinksForm($val,array('table' => 'collection_maintenance'));
      $this->embeddedForms['newExtLinks']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newExtLinks', $this->embeddedForms['newExtLinks']);
  }
  
  public function loadEmbedLink()
  {
    if($this->isBound()) return;
    /* extLinks sub form */
    $subForm = new sfForm();
    $this->embedForm('ExtLinks',$subForm);
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('ExtLinks')->findForTable('collection_maintenance', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new ExtLinksForm($vals,array('table' => 'collection_maintenance'));
        $this->embeddedForms['ExtLinks']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('ExtLinks', $this->embeddedForms['ExtLinks']);
    }
    $subForm = new sfForm();
    $this->embedForm('newExtLinks',$subForm);
  }  
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    /* For each embedded informations 
     * test if the widget is on screen by testing a flag field present on the concerned widget
     * If widget is not on screen, remove the field from list of fields to be bound, and than potentially saved
    */
    if(!isset($taintedValues['comment']))
    {
      $this->offsetUnset('Comments');
      unset($taintedValues['Comments']);
      $this->offsetUnset('newComments');
      unset($taintedValues['newComments']);
    }
    else
    {
      $this->loadEmbedComments();
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
    
    if(!isset($taintedValues['relatedfile']))
    {
      $this->offsetUnset('RelatedFiles');
      unset($taintedValues['RelatedFiles']);
      $this->offsetUnset('newRelatedFiles');
      unset($taintedValues['newRelatedFiles']);
    }
    else
    {
      $this->loadEmbedRelatedFiles();
      if(isset($taintedValues['newRelatedFiles']))
      {
        foreach($taintedValues['newRelatedFiles'] as $key=>$newVal)
        {
          if (!isset($this['newRelatedFiles'][$key]))
          {
            $this->addRelatedFiles($key);
          }
          $taintedValues['newRelatedFiles'][$key]['record_id'] = 0;
        }
      }
    }     
    parent::bind($taintedValues, $taintedFiles);   
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
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
    if (null === $forms && $this->getValue('relatedfile'))
    {  
      $value = $this->getValue('newRelatedFiles');
      foreach($this->embeddedForms['newRelatedFiles']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['referenced_relation']))
          unset($this->embeddedForms['newRelatedFiles'][$name]);
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
          $form->getObject()->changeUri() ;
        }
      }

      $value = $this->getValue('RelatedFiles');
      foreach($this->embeddedForms['RelatedFiles']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['referenced_relation']))
        {
          $form->getObject()->deleteObjectAndFile();
          unset($this->embeddedForms['RelatedFiles'][$name]);          
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
