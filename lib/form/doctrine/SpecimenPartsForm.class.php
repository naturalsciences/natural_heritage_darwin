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
    $this->useFields(array('parent_ref', 'specimen_part', 'complete', 'institution_ref', 'building', 'floor', 'room',
      'row', 'shelf', 'container', 'sub_container', 'container_type', 'sub_container_type',
      'container_storage', 'sub_container_storage', 'surnumerary', 'specimen_status', 'specimen_part_count_min', 'specimen_part_count_max',));

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


    $this->setDefault('accuracy', 1);
    $this->widgetSchema['accuracy']->setLabel('Accuracy');
    $this->validatorSchema['accuracy'] = new sfValidatorPass();

    $this->widgetSchema['extlink'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->validatorSchema['extlink'] = new sfValidatorPass();

    /*Input file for related files*/
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));
    $this->widgetSchema['filenames']->setLabel('Add File');

    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false, 'trim' => true));

    $this->widgetSchema['surnumerary']->setLabel('supernumerary');

    //Loan form is submited to upload file, when called like that we don't want some fields to be required
    $this->validatorSchema['filenames'] = new sfValidatorPass();

    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkSelfAttached'))));
    $this->mergePostValidator(new sfValidatorSchemaCompare('specimen_part_count_min', '<=', 'specimen_part_count_max',
      array(),
      array('invalid' => 'The min number ("%left_field%") must be lower or equal the max number ("%right_field%")' )
    ));

   $this->widgetSchema['Biblio_holder'] = new sfWidgetFormInputHidden(array('default'=>1));
   $this->validatorSchema['Biblio_holder'] = new sfValidatorPass();

    $this->validatorSchema['Codes_holder'] = new sfValidatorPass();
    $this->widgetSchema['Codes_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['Comments_holder'] = new sfValidatorPass();
    $this->widgetSchema['Comments_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['ExtLinks_holder'] = new sfValidatorPass();
    $this->widgetSchema['ExtLinks_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['RelatedFiles_holder'] = new sfValidatorPass();
    $this->widgetSchema['RelatedFiles_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['Insurances_holder'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->validatorSchema['Insurances_holder'] = new sfValidatorPass();
  }
  public function getEmbedRecords($emFieldName, $record_id = false)
  {
    if($record_id === false)
      $record_id = $this->getObject()->getId();
    if( $emFieldName =='Biblio' )
      return Doctrine::getTable('CatalogueBibliography')->findForTable('specimen_parts', $record_id);
    if( $emFieldName =='Codes' )
      return Doctrine::getTable('Codes')->getCodesRelated('specimen_parts', $record_id);
    if( $emFieldName =='Comments' )
      return Doctrine::getTable('Comments')->findForTable('specimen_parts', $record_id);
    if( $emFieldName =='ExtLinks' )
      return Doctrine::getTable('ExtLinks')->findForTable('specimen_parts', $record_id);
    if( $emFieldName =='RelatedFiles' )
      return Doctrine::getTable('Multimedia')->findForTable('specimen_parts', $record_id);
    if( $emFieldName =='Insurances' )
      return Doctrine::getTable('Insurances')->findForTable('specimen_parts', $record_id);
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

  public function addBiblio($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimen_parts', 'bibliography_ref' => $values['bibliography_ref'], 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Biblio', new BiblioAssociationsForm(DarwinTable::newObjectFromArray('CatalogueBibliography',$options)), $num);
  }

  public function addExtLinks($num, $obj=null)
  {
    $options = array('referenced_relation' => 'specimen_parts', 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('ExtLinks', new ExtLinksForm(DarwinTable::newObjectFromArray('ExtLinks',$options)), $num);
  }

  public function addRelatedFiles($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimen_parts', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('RelatedFiles', new MultimediaForm(DarwinTable::newObjectFromArray('Multimedia',$options)), $num);
  }

  public function addInsurances($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimen_parts', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('Insurances', new InsurancesSubForm(DarwinTable::newObjectFromArray('Insurances',$options)), $num);
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

  public function addCodes($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimen_parts', 'record_id' => $this->getObject()->getId());
    if (! $this->getObject()->isNew())
    {
      $collection = $this->getObject()->Individual->Specimen->Collection();
      if($collection)
      {
        $options['code_prefix'] = $collection->getCodePrefix();
        $options['code_prefix_separator'] = $collection->getCodePrefixSeparator();
        $options['code_suffix'] = $collection->getCodeSuffix();
        $options['code_suffix_separator'] = $collection->getCodeSuffixSeparator();
      }
    }
    $this->attachEmbedRecord('Codes', new CodesForm(DarwinTable::newObjectFromArray('Codes',$options)), $num);
  }

  public function addComments($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'specimen_parts', 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Comments', new CommentsSubForm(DarwinTable::newObjectFromArray('Comments',$options)), $num);
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

    $this->bindEmbed('Biblio', 'addBiblio' , $taintedValues);
    $this->bindEmbed('Codes', 'addCodes' , $taintedValues);
    $this->bindEmbed('Comments', 'addComments' , $taintedValues);
    $this->bindEmbed('ExtLinks', 'addExtLinks' , $taintedValues);
    $this->bindEmbed('RelatedFiles', 'addRelatedFiles' , $taintedValues);
    $this->bindEmbed('Insurances', 'addInsurances' , $taintedValues);
    parent::bind($taintedValues, $taintedFiles);
  }


  public function saveEmbeddedForms($con = null, $forms = null)
  {
    $this->saveEmbed('Biblio', 'bibliography_ref' ,$forms,array('referenced_relation'=>'specimen_parts', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Codes', 'code' ,$forms, array('referenced_relation'=>'specimen_parts', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Comments', 'comment' ,$forms, array('referenced_relation'=>'specimen_parts', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('ExtLinks', 'url' ,$forms, array('referenced_relation'=>'specimen_parts', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('RelatedFiles', 'mime_type' ,$forms, array('referenced_relation'=>'specimen_parts', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Insurances', 'insurance_value' ,$forms, array('referenced_relation'=>'specimen_parts', 'record_id' => $this->getObject()->getId()));

    return parent::saveEmbeddedForms($con, $forms);
  }


  public function duplicate($id)
  {
    //reembed biblio
    $bib =  $this->getEmbedRecords('Biblio', $id);
    foreach($bib as $key=>$vals) {
      $this->addBiblio($key, array('bibliography_ref' => $vals->getBibliographyRef()) );
    }

    $Codes = Doctrine::getTable('Codes')->getCodesRelatedArray('specimen_parts',$id) ;
    foreach ($Codes as $key=> $code)
    {
      $newCode = new Codes();
      $newCode->fromArray($code->toArray());
      $form = new CodesForm($newCode);
      $this->attachEmbedRecord('Codes', $form, $key);
    }

    // reembed duplicated comment
    $Comments = Doctrine::getTable('Comments')->findForTable('specimen_parts',$id) ;
    foreach ($Comments as $key=>$val)
    {
      $comment = new Comments();
      $comment->fromArray($val->toArray());
      $form = new CommentsSubForm($comment);
      $this->attachEmbedRecord('Comments', $form, $key);
    }
    // reembed duplicated external url
    $ExtLinks = Doctrine::getTable('ExtLinks')->findForTable('specimen_parts', $id) ;
    foreach ($ExtLinks as $key=>$val)
    {
      $links = new ExtLinks() ;
      $links->fromArray($val->toArray());
      $form = new ExtLinksForm($links);
      $this->attachEmbedRecord('ExtLinks', $form, $key);
    }

    // reembed duplicated insurances
    $Insurances = Doctrine::getTable('Insurances')->findForTable('specimen_parts',$id) ;
    foreach ($Insurances as $key=>$val)
    {
      $insurance = new Insurances() ;
      $insurance->fromArray($val->toArray());
      $form = new InsurancesSubForm($insurance);
      $this->attachEmbedRecord('Insurances', $form, $key);
    }
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

  public function getEmbedRelationForm($emFieldName, $values)
  {
    if( $emFieldName =='Biblio' )
      return new BiblioAssociationsForm($values);
    if( $emFieldName =='Codes' )
      return new CodesForm($values);
    if( $emFieldName =='Comments' )
      return new CommentsSubForm($values);
    if( $emFieldName =='ExtLinks' )
      return new ExtLinksForm($values);
    if( $emFieldName =='RelatedFiles' )
      return new MultimediaForm($values);
    if( $emFieldName =='Insurances' )
      return new InsurancesSubForm($values);
  }
}
