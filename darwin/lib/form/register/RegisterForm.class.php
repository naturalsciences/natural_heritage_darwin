<?php

/**
 * Register form.
 *
 * @package    form
 * @subpackage Register
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class RegisterForm extends BaseUsersForm
{
  public function configure()
  {
    $this->useFields(array('id','is_physical','sub_type','title','family_name','given_name','additional_names','gender')) ;
    $choices = array($this->getI18N()->__('Moral person'), $this->getI18N()->__('Physical person'));
    $this->widgetSchema['is_physical'] = new sfWidgetFormChoice(array ('choices' => $choices));
    $this->setDefault('is_physical', 1);
    $this->widgetSchema['is_physical']->setAttributes(array('class'=>'required_field'));
    $this->widgetSchema['sub_type'] = new sfWidgetFormDoctrineChoice(array('model' => 'Users',
                                                                           'table_method' => 'getDistinctSubType',
                                                                           'method' => 'getSubType',
                                                                           'key_method' => 'getSubType',
                                                                           'add_empty'=>true,
                                                                          )
                                                                    );
    $this->widgetSchema['title'] = new sfWidgetFormDoctrineChoice(array('model' => 'Users',
                                                                        'table_method' => 'getDistinctTitle',
                                                                        'method' => 'getTitle',
                                                                        'key_method' => 'getTitle',
                                                                        'add_empty'=>true,
                                                                       ));
    $this->widgetSchema['gender'] = new sfWidgetFormChoice(array('choices' => array('M' => 'M', 'F' => 'F'))) ;
    $this->validatorSchema['gender'] = new sfValidatorChoice(array('choices' => array('M' => 'M', 'F' => 'F'), 'required' => false));
    $this->widgetSchema['given_name'] = new sfWidgetFormInput();
    $this->widgetSchema['given_name']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema['family_name'] = new sfWidgetFormInput();
    $this->widgetSchema['family_name']->setAttributes(array('class'=>'medium_size required_field'));

    $this->widgetSchema['terms_of_use'] = new sfWidgetFormInputCheckbox();
    $this->widgetSchema['terms_of_use']->setAttributes(array('class'=>'required_field'));
    $this->setDefault('terms_of_use',false);

    $langs = array('en'=>'English','nl'=>'Nederlands','fr'=>'Français');
    $this->widgetSchema['selected_lang'] = new sfWidgetFormChoice(array('choices'=>$langs,'expanded'=>false));
    $this->validatorSchema['selected_lang'] = new sfValidatorChoice(array('choices'=>array_keys($langs) ));
    $this->widgetSchema['selected_lang']->setLabel('Application Language');

    /* Captcha */
    
    $this->widgetSchema['captcha'] = new sfWidgetFormReCaptcha(array('public_key' => sfConfig::get('dw_recaptcha_public_key')));
 
    /* Validators */

    $this->validatorSchema['terms_of_use'] = new sfValidatorBoolean(array('required'=>true),
                                                                    array('required'=> 'You cannot register without accepting DaRWIN 2 terms of use',
                                                                          'invalid' => 'The value provided for term of use is invalid'
                                                                         )
                                                                   );
    $this->validatorSchema['is_physical'] = new sfValidatorBoolean(array('required'=>true),
                                                                   array('required'=> 'Status is required',
                                                                         'invalid' => 'The value provided for status is invalid'
                                                                        )
                                                                  );
    $this->validatorSchema['family_name'] = new sfValidatorString(array('trim'=>true,
                                                                        'required'=>true
                                                                       ),
                                                                   array('required'=> 'Name is required',
                                                                         'invalid' => 'The value provided for name is invalid'
                                                                        )
                                                                 );
    $this->validatorSchema['captcha'] = new sfValidatorReCaptcha(array('private_key' => sfConfig::get('dw_recaptcha_private_key'),
                                                                       'proxy_host' => sfConfig::get('dw_recaptcha_proxy_host'),
                                                                       'proxy_port' => sfConfig::get('dw_recaptcha_proxy_port'),
                                                                      ));

    /* Labels */
    $this->widgetSchema->setLabels(array('family_name'=>$this->getI18N()->__('Name'),
                                         'given_name'=>$this->getI18N()->__('First name'),
                                         'is_physical'=>$this->getI18N()->__('Status'),
                                         'sub_type'=>$this->getI18N()->__('Type')
                                        )
                                  );

    /* Login infos as embedded form */

    $regLoginInfoSubForm = new sfForm();
    $this->embedForm('RegisterLoginInfosForm',$regLoginInfoSubForm);


    /* Comm means as embedded form */
    $regCommSubForm = new sfForm();
    $this->embedForm('RegisterCommForm',$regCommSubForm);

  }

  public function addLoginInfos($num)
  {
    $val = new UsersLoginInfos();
    $val->User = $this->getObject();
    $form = new RegisterLoginInfosForm($val);
    $this->embeddedForms['RegisterLoginInfosForm']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('RegisterLoginInfosForm', $this->embeddedForms['RegisterLoginInfosForm']);
  }

  public function addComm($num)
  {
    $val = new UsersComm();
    $val->Users = $this->getObject();
    $form = new RegisterCommForm($val);
    $this->embeddedForms['RegisterCommForm']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('RegisterCommForm', $this->embeddedForms['RegisterCommForm']);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['RegisterLoginInfosForm']))
    {
      foreach($taintedValues['RegisterLoginInfosForm'] as $key=>$newVal)
      {
        if (!isset($this['RegisterLoginInfosForm'][$key]))
        {
          $this->addLoginInfos($key);
        }
      }
    }
    if(isset($taintedValues['RegisterCommForm']))
    {
      foreach($taintedValues['RegisterCommForm'] as $key=>$newVal)
      {
        if (!isset($this['RegisterCommForm'][$key]))
        {
          $this->addComm($key);
        }
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function saveObjectEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms)
    {
      $value = $this->getValue('RegisterLoginInfosForm');
      foreach($this->embeddedForms['RegisterLoginInfosForm']->getEmbeddedForms() as $name => $form)
      {
        $form->getObject()->setUserRef($this->getObject()->getId());
        $form->getObject()->save();
      }
      $value = $this->getValue('RegisterCommForm');
      foreach($this->embeddedForms['RegisterCommForm']->getEmbeddedForms() as $name => $form)
      {
        $form->getObject()->setPersonUserRef($this->getObject()->getId());
        $form->getObject()->save();
      }
    }
    return parent::saveObjectEmbeddedForms($con, $forms);
  }
}
