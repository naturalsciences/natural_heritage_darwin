<?php

/**
 * MySavedSearches form.
 *
 * @package    form
 * @subpackage MySavedSearches
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class MySavedSearchesForm extends BaseMySavedSearchesForm
{
  public function configure()
  {
    $this->widgetSchema['subject'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['search_criterias'] = new sfWidgetFormInputHidden() ;
    $this->widgetSchema['user_ref'] = new sfWidgetFormInputHidden() ;
    $this->widgetSchema['name'] = new sfWidgetFormInputText() ;
    $this->widgetSchema['name']->setAttribute('class','medium_size');
    $this->widgetSchema['name']->setLabel('Title');
    $this->widgetSchema['favorite']->setAttribute('class','hidden');    
    $this->widgetSchema['modification_date_time'] = new sfWidgetFormInputText() ;
    $this->widgetSchema['modification_date_time']->setLabel('Last modification');
    $default_name = sprintf($this->getI18N()->__("My search on
%s"),date('d/m/Y H:i:s'));
    if(isset($this->options['type']) && $this->options['type']=='pin')
      $default_name = sprintf($this->getI18N()->__("My specimens on
%s"),date('d/m/Y H:i:s'));

    if($this->getObject()->getName() == "")
      $this->widgetSchema['name']->setDefault($default_name) ;

    $choices = Doctrine::getTable('MySavedSearches')->getAllFields($this->object->getSubject(),$this->options['is_reg_user']) ;
    $choices = $this->translateValues($choices);
    $this->widgetSchema['visible_fields_in_result'] = new sfWidgetFormChoice(array(
      'choices' => $choices, 
      'expanded' => true,
      'multiple' => true,
      'renderer_options' => array('formatter' => array($this, 'formatter'))
    ));

    $this->validatorSchema['visible_fields_in_result'] = new sfValidatorChoice(array('choices' => $choices,'multiple' => true));

    $this->validatorSchema['subject'] = new sfValidatorChoice(array('choices' => array('specimen','individual','part'), 'required'=>true,'empty_value'=>'specimen'));

    $this->validatorSchema['name'] = new sfValidatorString() ;
    $this->validatorSchema['modification_date_time'] = new sfValidatorString(array('required' => false)) ;
    $this->validatorSchema['user_ref'] = new sfValidatorString(array('required' => false)) ;
  }
  
  public function formatter($widget, $inputs)
  {
    $rows = array();
    foreach ($inputs as $i => $input)
    {
      $rows[] = $widget->renderContentTag(
            'tr',
      	    '<td>'.$input['label'].'</td><td>'.$input['input'].'</td>'
           );
    }
 
    return $widget->renderContentTag('tbody', implode($widget->getOption('separator'), $rows));
  }
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['subject']) && in_array($taintedValues['subject'], array('specimen','individual','part')))
    {
      $choices = Doctrine::getTable('MySavedSearches')->getAllFields($taintedValues['subject']) ;
      $choices = array_keys($choices);
      $this->validatorSchema['visible_fields_in_result'] = new sfValidatorChoice(array('choices' => $choices,'multiple' => true));
    }
    parent::bind($taintedValues,$taintedFiles);
  }

  public function save($con = null)
  {
    $values = $this->getValues();
    $this->getObject()->fromArray($values) ;
    $string_fields = implode('|',$values['visible_fields_in_result']) ;
    $this->getObject()->setModificationDateTime(date('d/m/Y H:i:s'));
    $this->getObject()->setVisibleFieldsInResult($string_fields) ;
    $this->getObject()->save();
  }
  
  
}
