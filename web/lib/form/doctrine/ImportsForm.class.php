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
  
  	//ftheeten 2018 07 23
    $this->widgetSchema['source_database'] =  new sfWidgetFormInputText(); 
    $this->validatorSchema['source_database'] =  new sfValidatorString() ;
    if($this->options['format'] == 'taxon')
    {
      $this->useFields(array('format', 'exclude_invalid_entries', 'specimen_taxonomy_ref')) ;
      $category = array('taxon'=>$this->getI18N()->__('Taxonomy')) ;
      $this->widgetSchema['exclude_invalid_entries'] = new sfWidgetFormChoice(
        array(
          'choices'=>array(true=>$this->getI18N()->__('No'),false=>$this->getI18N()->__('Yes')),
          'multiple'=>false,
          'expanded'=>true,
        )
      );
      
      //ftheeten 2017 08 02
       //ftheeten 2018 03 06
       $this->widgetSchema['specimen_taxonomy_ref']=new sfWidgetFormChoice(array(
      'choices' =>  TaxonomyMetadataTable::getAllTaxonomicMetadata('taxonomy_name ASC', false, true)
    ));
      $this->validatorSchema['specimen_taxonomy_ref'] = new sfValidatorInteger(array('required'=>false));
      
       //ftheeeten 2018 06 06
      $this->widgetSchema['taxonomy_kingdom'] =new sfWidgetFormChoice(array(
      
      'choices' =>  TaxonomyTable::getTaxaByLevel("2", True)
        ));
      $this->validatorSchema['taxonomy_kingdom'] = new sfValidatorInteger(array('required'=>false));
    }
    //ftheeten 2019 03 04
    elseif($this->options['format'] == 'lithostratigraphy')
    {
      $this->useFields(array('format', 'exclude_invalid_entries')) ;
      $category = array('lithostratigraphy'=>$this->getI18N()->__('Lithostratigraphy')) ;

      
     
    }
	elseif($this->options['format'] == 'locality')
	{
		//ftheeten 2018 07 15
		$this->useFields(array('gtu_include_date','gtu_tags_in_merge', 'sensitive_information_withheld', 'collection_ref')) ;
		
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
	    //$this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));
		$category = array('locality'=>$this->getI18N()->__('Locality')) ;
		$this->widgetSchema['include_date'] = new sfWidgetFormInputCheckbox();
		$this->validatorSchema['include_date'] = new sfValidatorBoolean(array('required' => false));
		$this->widgetSchema['tags_in_merge'] = new sfWidgetFormInputCheckbox();
		$this->validatorSchema['tags_in_merge'] = new sfValidatorBoolean(array('required' => false));
    
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
      
      $this->widgetSchema['collection_ref_for_gtu'] = new widgetFormButtonRef(
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
      $this->validatorSchema['collection_ref_for_gtu'] = new sfValidatorInteger(array('required'=>false)); 
      
             //ftheeten 2017 08 02
      $this->widgetSchema['specimen_taxonomy_ref'] =new sfWidgetFormChoice(array(
      //'choices' => array_merge( array(''=>'All'), TaxonomyMetadataTable::getAllTaxonomicMetadata())
      'choices' =>  TaxonomyMetadataTable::getAllTaxonomicMetadata('id ASC', true)
        ));
      $this->validatorSchema['specimen_taxonomy_ref'] = new sfValidatorInteger(array('required'=>false));
      $this->widgetSchema['taxonomy_kingdom'] =new sfWidgetFormChoice(array(      
      'choices' =>  TaxonomyTable::getTaxaByLevel("2", True)
        ));
      $this->validatorSchema['taxonomy_kingdom'] = new sfValidatorInteger(array('required'=>false));
      
      $category = imports::getFormats();
      //ftheeten 2018 08 07    
     
    
    }
    
        //ftheeten 2018 12 14 collection also optional for taxon
    if($this->options['format'] == 'locality' || $this->options['format'] == 'taxon' || $this->options['format'] == 'lithostratigraphy'  )
      {
         $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>false));
      }
      else
      {
        $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));
      } 
	//ftheeten 2018 07 23810
    $this->widgetSchema['source_database'] =  new sfWidgetFormInputText(); 
    $this->validatorSchema['source_database'] =  new sfValidatorString() ;
    
    $this->widgetSchema['uploadfield'] = new sfWidgetFormInputFile(array(),array('id'=>'uploadfield'));
    //$allowed_types = array('text/xml','application/xml') ;
    //ftheeten 2017 09 07
    $allowed_types = array('text/xml','application/xml', 'text/plain', 'text/x-c++') ;
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
    print($tmpFile);
      //ftheeten 2018 02 22
    if(is_object($tmpFile))
    {
     if($tmpFile->getType()) 
     {
         $this->getObject()->setMimeType($tmpFile->getType());
     }
    }
     return $values;
  }
}
