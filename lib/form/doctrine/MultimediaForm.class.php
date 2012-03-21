<?php

/**
 * Multimedia form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MultimediaForm extends BaseMultimediaForm
{
  public function configure()
  {
    unset(
      $this['creation_date_mask']
      );
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString(array('required'=>false));    
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
          
    $this->widgetSchema['title'] = new sfWidgetFormInput();
    $this->widgetSchema['title']->setAttributes(array('class'=>'medium_small_size'));
    $this->validatorSchema['title'] = new sfValidatorString();
    
    if($this->getObject()->isNew())
    {
      $this->widgetSchema['uri'] = new sfWidgetFormInputHidden();
      $this->validatorSchema['uri'] = new sfValidatorString();
    }
    else unset($this['uri']) ;
      
    $this->widgetSchema['description'] = new sfWidgetFormInput();    
    $this->widgetSchema['description']->setAttributes(array('class'=>'medium_small_size'));
    $this->validatorSchema['description'] = new sfValidatorString(array('required'=>false)); 

    $this->widgetSchema['filename'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['filename'] = new sfValidatorPass();  
    
    $this->widgetSchema['mime_type'] = new sfWidgetFormInputHidden(); 
    $this->validatorSchema['mime_type'] = new sfValidatorString();     
    
    $this->widgetSchema['type'] = new sfWidgetFormInputHidden(); 
    $this->validatorSchema['type'] = new sfValidatorString(array('required'=>false));         
    
    $this->widgetSchema['creation_date'] = new sfWidgetFormInputHidden(); 
    $this->validatorSchema['creation_date'] = new sfValidatorPass();  
    
    $this->mergePostValidator(new MultimediaFileValidatorSchema());    
  }  
  
  public function doSave($con = null)
  {
    $this->offsetUnset('id');  
  }
}
