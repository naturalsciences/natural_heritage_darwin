<?php
class UploadFileForm extends BaseForm
{
  public function configure()
  {
    /* Define name format */
    $this->widgetSchema->setNameFormat('uploadform[%s]');
    
    $this->widgetSchema['uploadfield'] = new sfWidgetFormInputFile(array(),array('id'=>'uploadfield'));

    /* Collection Reference */
    $this->widgetSchema['collection_ref'] = new widgetFormButtonRef(
      array('model' => 'Collections',
            'link_url' => 'collection/choose',
            'method' => 'getName',
            'box_title' => $this->getI18N()->__('Choose'),
            'button_class'=>'',
           ),
      array('class'=>'inline',
           )
    );
    
    $category = array('dna'=>'DNA');
    $allowed_types = array('text/xml','image/jpeg','application/zip') ;
    $this->widgetSchema['format_ref'] = new sfWidgetFormChoice(
      array(
        'choices' => $category
      )
    );

    /* Labels */
    $this->widgetSchema->setLabels(array('collection_ref' => 'Collection',
                                         'uploadfield' => 'File',
                                         'format_ref' => 'Format',
                                        )
                                  );

    $this->validatorSchema['format_ref'] = new sfValidatorChoice(
      array('choices'=> array_keys($category) ));
    $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));    
    $this->validatorSchema['uploadfield'] = new sfValidatorFilePHP8(
      array(
          'required' => true,
          'mime_types' => $allowed_types,
          'validated_file_class' => 'myValidatedFile'
      ));
	  
	  	//ftheeten 2023 04 11 PHP8
	 $this->validatorSchema->addOption('allow_extra_fields', true);
  }
}
