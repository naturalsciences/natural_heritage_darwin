<?php

/**
 * Multimedia form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MultimediaForm extends BaseMultimediaForm
{
  public function configure()
  {
    unset(
      $this['id'],
      $this['creation_date'],
      $this['creation_date_mask']
      );

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString(array('required'=>false));
          
    $this->widgetSchema['title'] = new sfWidgetFormInput();       
    $this->widgetSchema['title']->setAttributes(array('class'=>'medium_small_size'));  
    $this->validatorSchema['title'] = new sfValidatorString(); 
    
    $this->widgetSchema['description'] = new sfWidgetFormInput();       
    $this->widgetSchema['description']->setAttributes(array('class'=>'medium_size'));  
    $this->validatorSchema['description'] = new sfValidatorString(array('required'=>false)); 

    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema['filename'] = new sfWidgetFormInputHidden();        
    $this->validatorSchema['filename'] = new sfValidatorPass();
    $this->widgetSchema['filename']->setLabel($this->options['file']) ;
    $this->setDefault('filename',$this->options['file']);


    $this->mergePostValidator(new relatedFileValidatorSchema());    
  }
}
