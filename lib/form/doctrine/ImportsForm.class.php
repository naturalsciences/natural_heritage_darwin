<?php

/**
 * Imports form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ImportsForm extends BaseImportsForm
{
  public function configure()
  {   
    $this->useFields(array('collection_ref', 'format')) ;
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
    
    $category = imports::getFormats();
    $allowed_types = array('text/xml','application/xml') ;
    $this->widgetSchema['format'] = new sfWidgetFormChoice(
      array(
        'choices' => $category
      )
    );

    /* Labels */
    $this->widgetSchema->setLabels(array('collection_ref' => 'Collection',
                                         'uploadfield' => 'File',
                                         'format' => 'Format',
                                        )
                                  );
    $this->validatorSchema['format'] = new sfValidatorChoice(
      array('choices'=> array_keys($category) ));
    $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));    
    $this->validatorSchema['uploadfield'] = new xmlFileValidator(
      array(
          'required' => true,
          'mime_types' => $allowed_types,
          'validated_file_class' => 'myValidatedFile'
      ));
  }
}
