<?php

/**
 * Users form.
 *
 * @package    form
 * @subpackage Users
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class UsersForm extends BaseUsersForm
{
  public function configure()
  {
    if ($this->options['mode'] == 'new') 
    {
      $this->useFields(array('is_physical','sub_type','title','family_name','given_name','additional_names','gender','user_ip')) ;
      $this->widgetSchema['sub_type'] = new widgetFormSelectComplete(array('model' => 'Users',
                                                                   'table_method' => 'getDistinctSubType',
                                                                   'method' => 'getSubType',
                                                                   'key_method' => 'getSubType',
                                                                   'add_empty' => true,
                                                                   'change_label' => 'Pick a sub type in the list',
                                                                   'add_label' => 'Add another sub type',
                                                                          )
                                                                    );  
      $this->widgetSchema['title'] = new widgetFormSelectComplete(array('model' => 'Users',
                                                                        'table_method' => 'getDistinctTitle',
                                                                        'method' => 'getTitle',
                                                                        'key_method' => 'getTitle',
                                                                        'add_empty' => true,
                                                                        'change_label' => 'Pick a title in the list',
                                                                        'add_label' => 'Add another title',
                                                                         )
                                                                   );  
      $this->widgetSchema['is_physical'] = new sfWidgetFormInputCheckbox(array ('default' => 'true')); 
      $this->widgetSchema['gender'] = new sfWidgetFormChoice(array('choices' => array('M' => 'M', 'F' => 'F'))) ;
      $this->validatorSchema['gender'] = new sfValidatorChoice(array('choices' => array('M' => 'M', 'F' => 'F'), 'required' => false)); 
      $this->widgetSchema['title']->setAttributes(array('class'=>'small_size')) ;     
      $this->validatorSchema['title'] =  new sfValidatorString(array('required' => false));
      $this->validatorSchema['sub_type'] =  new sfValidatorString(array('required' => false));        
    }
    elseif($this->options['is_physical'])
    {
      $this->useFields(array('title','family_name','given_name','additional_names','gender','people_id')) ;      
      $this->widgetSchema['people_id'] = new widgetFormButtonRef(array('model' => 'People',
                                                                'method' => 'getFormatedName',
                                                                'link_url' => 'people/choose?with_js=1',
                                                                'nullable' => true,
                                                                'box_title' => $this->getI18N()->__('Choose Yourself'),
                                                                      )
                                                                );   
      $this->widgetSchema['title'] = new widgetFormSelectComplete(array('model' => 'Users',
                                                                        'table_method' => 'getDistinctTitle',
                                                                        'method' => 'getTitle',
                                                                        'key_method' => 'getTitle',
                                                                        'add_empty' => true,
                                                                        'change_label' => 'Pick a title in the list',
                                                                        'add_label' => 'Add another title',
                                                                         )
                                                                   );       
      $this->widgetSchema['title']->setAttributes(array('class'=>'small_size')) ;     
      $this->validatorSchema['title'] =  new sfValidatorString(array('required' => false)); 
      $this->validatorSchema['people_id'] = new sfValidatorInteger(array('required' => false)) ;                                                                      
    }
    else
    {
      $this->useFields(array('sub_type','family_name','given_name','additional_names','people_id')) ;
      $this->widgetSchema['sub_type'] = new widgetFormSelectComplete(array('model' => 'Users',
                                                                   'table_method' => 'getDistinctSubType',
                                                                   'method' => 'getSubType',
                                                                   'key_method' => 'getSubType',
                                                                   'add_empty' => true,
                                                                   'change_label' => 'Pick a sub type in the list',
                                                                   'add_label' => 'Add another sub type',
                                                                          )
                                                                    );
      $this->widgetSchema['people_id'] = new widgetFormButtonRef(array('model' => 'People',
                                                                'method' => 'getFormatedName',
                                                                'link_url' => 'institution/choose',
                                                                'nullable' => true,
                                                                'box_title' => $this->getI18N()->__('Choose Yourself'),
                                                                      )
                                                                );   
      $this->validatorSchema['sub_type'] =  new sfValidatorString(array('required' => false));
      $this->validatorSchema['people_id'] = new sfValidatorInteger(array('required' => false)) ;     
    }
    $this->widgetSchema->setHelp('people_id','With this field, you can associate this user to a people recorded in the database (because user and people are not the same in DaRWIN2), the real interest is it will improve the synchronisation between the two record associated');                                             
    
    $langs = array('en'=>'English','nl'=>'Nederlands','fr'=>'Français');
    $this->widgetSchema['selected_lang'] = new sfWidgetFormChoice(array('choices'=>$langs,'expanded'=>true));
    $this->validatorSchema['selected_lang'] = new sfValidatorChoice(array('choices'=>array_keys($langs) ));
    $this->widgetSchema['selected_lang']->setLabel('Application Language');

    $this->widgetSchema['given_name'] = new sfWidgetFormInput();
    $this->widgetSchema['given_name']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema['family_name'] = new sfWidgetFormInput();
    $this->widgetSchema['family_name']->setAttributes(array('class'=>'medium_size'));
		//JMHerpers 2018 05 14
	$this->widgetSchema['user_ip'] = new sfWidgetFormInput();
    $this->widgetSchema['user_ip']->setAttributes(array('class'=>'medium_size'));
	$this->validatorSchema['user_ip'] =  new sfValidatorString(array('required' => false));    

    $this->widgetSchema['additional_names'] = new sfWidgetFormInput();
    $this->widgetSchema['additional_names']->setAttributes(array('class'=>'medium_size'));
     
 					//JMHerpers 2018 02 15 Inversion of max and Min to have most recent dates on top
	$yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')),intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(date('Y').'/12/31');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['birth_date'] = new widgetFormJQueryFuzzyDate(
      array('culture'=> $this->getCurrentCulture(), 
            'image'=> '/images/calendar.gif',       
            'format' => '%day%/%month%/%year%',    
            'years' => $years,                     
            'empty_values' => $dateText,           
      ),                                      
      array('class' => 'from_date')                
    );

    $this->validatorSchema['birth_date'] = new fuzzyDateValidator(
      array(
        'required' => false,                       
        'from_date' => true,                       
        'min' => $minDate,                         
        'max' => $maxDate,
        'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );
  }
}
