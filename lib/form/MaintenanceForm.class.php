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

    $this->widgetSchema['modification_date_time'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'with_time' => true
      ),
      array('class' => 'from_date')
    );

    $this->widgetSchema['modification_date_time']->setLabel('Last update date') ;
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

    $this->widgetSchema['people_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'People',
      'method' => 'getFormatedName',
      'link_url' => 'people/choose',
      'nullable' => false,
      'box_title' => $this->getI18N()->__('Choose Yourself'),
      'complete_url' => 'catalogue/completeName?table=people',
    ));
    $this->widgetSchema['people_ref']->setLabel('Person');

    $this->widgetSchema['parts_ids'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['parts_ids'] = new sfValidatorString(array('required' => false, 'empty_value' => ''));

    $this->widgetSchema['category'] = new sfWidgetFormChoice(array('choices' => array('action' => 'Action', 'observation'=>'Observation')));
    $this->widgetSchema['category']->setLabel('Type');

    $forced_choices = false;
    $default = null;
    if(
        isset($this->options['forced_action_observation_options'])
        && is_array($this->options['forced_action_observation_options'])
        && count($this->options['forced_action_observation_options']) > 0
    ) {
      $forced_choices = $this->options['forced_action_observation_options'];
      $default = current(array_keys($forced_choices));
    }
    $this->widgetSchema['action_observation'] = new widgetFormSelectComplete(array(
      'model' => 'CollectionMaintenance',
      'table_method' => 'getDistinctActions',
      'method' => 'getAction',
      'key_method' => 'getAction',
      'add_empty' => false,
      'change_label' => 'Pick an action in the list',
      'add_label' => 'Add another action',
      'forced_choices'=>$forced_choices,
      'default'=>$default,
    ));

    $this->widgetSchema['action_observation']->setLabel('Action / Observation');
    /* input file for related files */
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();
    $this->widgetSchema['filenames']->setLabel("Add File") ;
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));
    $this->validatorSchema['filenames'] = new sfValidatorPass() ;

    $this->validatorSchema['Comments_holder'] = new sfValidatorPass();
    $this->widgetSchema['Comments_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['ExtLinks_holder'] = new sfValidatorPass();
    $this->widgetSchema['ExtLinks_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['RelatedFiles_holder'] = new sfValidatorPass();
    $this->widgetSchema['RelatedFiles_holder'] = new sfWidgetFormInputHidden(array('default'=>1));
  }

  public function getEmbedRecords($emFieldName, $record_id = false)
  {
    if($record_id === false)
      $record_id = $this->getObject()->getId();
    if( $emFieldName =='Comments' )
      return Doctrine::getTable('Comments')->findForTable('collection_maintenance', $record_id);
    if( $emFieldName =='ExtLinks' )
      return Doctrine::getTable('ExtLinks')->findForTable('collection_maintenance', $record_id);
    if( $emFieldName =='RelatedFiles' )
      return Doctrine::getTable('Multimedia')->findForTable('collection_maintenance', $record_id);
  }

  public function addExtLinks($num, $obj=null)
  {
    $options = array('referenced_relation' => 'collection_maintenance', 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('ExtLinks', new ExtLinksForm(DarwinTable::newObjectFromArray('ExtLinks',$options)), $num);
  }

  public function addComments($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'collection_maintenance', 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Comments', new CommentsSubForm(DarwinTable::newObjectFromArray('Comments',$options)), $num);
  }

  public function addRelatedFiles($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'collection_maintenance', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('RelatedFiles', new MultimediaForm(DarwinTable::newObjectFromArray('Multimedia',$options)), $num);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    /* For each embedded informations If widget is not on screen, remove the field from list of fields to be bound, and than potentially saved */
    $this->bindEmbed('Comments', 'addComments' , $taintedValues);
    $this->bindEmbed('ExtLinks', 'addExtLinks' , $taintedValues);
    $this->bindEmbed('RelatedFiles', 'addRelatedFiles' , $taintedValues);
    parent::bind($taintedValues, $taintedFiles);
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    $this->saveEmbed('Comments', 'comment' ,$forms, array('referenced_relation'=>'collection_maintenance', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('ExtLinks', 'url' ,$forms, array('referenced_relation'=>'collection_maintenance', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('RelatedFiles', 'mime_type' ,$forms, array('referenced_relation'=>'collection_maintenance', 'record_id' => $this->getObject()->getId()));

    return parent::saveEmbeddedForms($con, $forms);
  }

  public function getEmbedRelationForm($emFieldName, $values)
  {
    if( $emFieldName =='Comments' )
      return new CommentsSubForm($values);
    if( $emFieldName =='ExtLinks' )
      return new ExtLinksForm($values);
    if( $emFieldName =='RelatedFiles' )
      return new MultimediaForm($values);
  }
}
