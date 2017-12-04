<?php

/**
 * Suggestion form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SuggestionForm extends BaseInformativeWorkflowForm
{
  public function configure()
  {
    unset(
      $this['modification_date_time'],
      $this['record_id'],
      $this['referenced_relation'],
      $this['user_ref'],
      $this['status']);     
    $this->widgetSchema->setNameFormat('suggestion[%s]');       
    $this->widgetSchema['id'] = new sfWidgetFormInputHidden() ; 
    $this->setDefaults(array('id' => $this->options['ref_id'])) ;
    $this->widgetSchema['formated_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['formated_name']->setLabel("Your Name") ;
    $this->widgetSchema['formated_name']->setAttributes(array('class'=>'small_medium_size'));    
    $this->widgetSchema['email'] = new sfWidgetFormInputText();
    $this->widgetSchema['email']->setLabel("e-Mail") ;
    $this->widgetSchema['email']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>true));    
    $this->validatorSchema['formated_name'] = new sfValidatorPass() ;
    $this->validatorSchema['email'] = new sfValidatorEmail(array('required'=> false),
                                                           array('invalid' => 'E-mail is not of a valid form')); 
    $this->validatorSchema['id'] = new sfValidatorPass() ;
    
    /* Captcha */
    $this->widgetSchema['captcha'] = new sfWidgetFormReCaptcha(array('public_key' => sfConfig::get('dw_recaptcha_public_key'),'ajax' => $this->options['ajax']));    
    $this->validatorSchema['captcha'] = new sfValidatorReCaptcha(array('private_key' => sfConfig::get('dw_recaptcha_private_key'),
                                                                       'proxy_host' => sfConfig::get('dw_recaptcha_proxy_host'),
                                                                       'proxy_port' => sfConfig::get('dw_recaptcha_proxy_port'),
                                                                      ));    
  }
}
