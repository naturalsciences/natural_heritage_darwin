<?php

/**
 * Imports form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ImportsForm extends BaseImportsForm
{
  public function configure()
  {    
    if($this->options['format'] == 'taxon')
    {
      $this->useFields(array('format', 'exclude_invalid_entries')) ;
      $category = array('taxon'=>$this->getI18N()->__('Taxonomy')) ;
      $this->widgetSchema['exclude_invalid_entries'] = new sfWidgetFormChoice(
        array(
          'choices'=>array(true=>$this->getI18N()->__('No'),false=>$this->getI18N()->__('Yes')),
          'multiple'=>false,
          'expanded'=>true,
        )
      );
      
          //ftheeten 2017 07 06
        $this->widgetSchema['taxonomy_name'] = new sfWidgetFormInput();
        $this->widgetSchema['taxonomy_name']->setAttributes(array('class'=>'large_size'));
       $this->validatorSchema['taxonomy_name'] = new sfValidatorString(array('required' => false));
        
        $this->widgetSchema['is_reference_taxonomy'] = new sfWidgetFormInputCheckbox();
        $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorBoolean(array('required' => false));
        
        $this->widgetSchema['source_taxonomy'] = new sfWidgetFormTextarea();
        $this->validatorSchema['source_taxonomy'] = new sfValidatorString(array('required' => true));
        
        $this->widgetSchema['definition_taxonomy'] = new sfWidgetFormTextarea();
        $this->validatorSchema['definition_taxonomy'] = new sfValidatorString(array('required' => false));

        
        $this->widgetSchema['url_website_taxonomy'] = new sfWidgetFormInput();
        $this->widgetSchema['url_website_taxonomy']->setAttributes(array('class'=>'medium_size'));
        $this->validatorSchema['url_website_taxonomy'] = new sfValidatorString(array('required' => false)); 
        
        $this->widgetSchema['url_webservice_taxonomy'] = new sfWidgetFormInput();
        $this->widgetSchema['url_webservice_taxonomy']->setAttributes(array('class'=>'medium_size'));
        $this->validatorSchema['url_webservice_taxonomy'] = new sfValidatorString(array('required' => false)); 
        
            $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
        $years = array_combine($yearsKeyVal, $yearsKeyVal);
        $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
        $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
        $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
        $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));

        $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);
    
      
    $this->widgetSchema['creation_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );
    $this->validatorSchema['creation_date'] = new fuzzyDateValidator(array(
      'required' => true,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',
        ));
    }
    else
    {
      $this->useFields(array('collection_ref', 'format')) ;
      /* Collection Reference */
      $this->widgetSchema['collection_ref'] = new widgetFormButtonRef(
        array(
          'model' => 'Collections',
          'link_url' => 'collection/choose',
          'method' => 'getName',
          'box_title' => $this->getI18N()->__('Choose'),
          'button_class'=>'',
        ),
        array(
          'class'=>'inline',
        )
      );
      $category = imports::getFormats();
      $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));
      
      //ftheeeten 2017 08 02
      $this->widgetSchema['specimen_taxonomy_ref'] =new sfWidgetFormChoice(array(
      //'choices' => array_merge( array(''=>'All'), TaxonomyMetadataTable::getAllTaxonomicMetadata())
      'choices' =>  TaxonomyMetadataTable::getAllTaxonomicMetadata('id ASC', true)
        ));
      $this->validatorSchema['specimen_taxonomy_ref'] = new sfValidatorInteger(array('required'=>false));
    
    }
    $this->widgetSchema['uploadfield'] = new sfWidgetFormInputFile(array(),array('id'=>'uploadfield'));
    //$allowed_types = array('text/xml','application/xml') ;
    //ftheeten 2017 09 07
    $allowed_types = array('text/xml','application/xml', 'text/plain') ;
    $this->widgetSchema['format'] = new sfWidgetFormChoice(
      array(
        'choices' => $category
      )
    );

    /* Labels */
    $this->widgetSchema->setLabels(array(
      'collection_ref' => 'Collection',
      'uploadfield' => 'File',
      'format' => 'Format',
      'exclude_invalid_entries' => 'Match invalid Units',
    ));

    $this->validatorSchema['format'] = new sfValidatorChoice(
      array('choices'=> array_keys($category)
    ));    
    $this->validatorSchema['uploadfield'] = new xmlFileValidator(
      array(
        'xml_path_file'=>$this->options['format'] == 'taxon'?'/xsd/taxonomy.xsd':'/xsd/ABCD_2.06_EFGDNA.XSD',
        'required' => true,
        'mime_types' => $allowed_types,
        'validated_file_class' => 'myValidatedFile',
    ));
    
    //ftheeten 2017 09 13
     $this->mergePostValidator(new sfValidatorCallback(
			array('callback' => array($this, 'setValidatorSetMimeType'))));

    
  }
  
  //ftheeten 2017 09 13
  public function setValidatorSetMimeType($validator, $values, $arguments)
  {
    $tmpFile=$values["uploadfield"];
     if($tmpFile->getType()) 
     {
         $this->getObject()->setMimeType($tmpFile->getType());
     }
     return $values;
  }
		
}
