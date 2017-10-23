<?php

/**
 * MyWidgets form.
 *
 * @package    form
 * @subpackage MyWidgets
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class MyWidgetsForm extends BaseMyWidgetsForm
{
  public function configure()
  {
    $this->useFields(array('title_perso','user_ref','group_name', 'category'));
    $w = $this->getObject() ;
    if ($this->options['level'] >= Users::MANAGER)
	    $choices = array('unused'=> '', 'is_available' => '', 'visible' => '', 'opened' => '') ;
    else
	    $choices = array('is_available' => '', 'visible' => '', 'opened' => '') ;
    $this->widgetSchema['widget_choice'] = new sfWidgetFormChoice(array(
	  'choices' => $choices, 
	  'expanded' => true,
	  'renderer_options' => array('formatter' => array($this, 'formatter'))     
    ));
    $this->widgetSchema['title_perso'] = new sfWidgetFormInputText() ;
    $this->widgetSchema['title_perso']->setAttributes(array('class' => 'medium_size')) ;
    $this->widgetSchema['user_ref'] = new sfWidgetFormInputHidden() ;
    $this->widgetSchema['group_name'] = new sfWidgetFormInputHidden() ;
    $this->widgetSchema['category'] = new sfWidgetFormInputHidden() ;
    $this->validatorSchema['user_ref'] = new sfValidatorInteger();
    $this->validatorSchema['widget_choice'] = new sfValidatorChoice(array('choices' => array_keys($choices),'required' => false));
    $this->setDefault('widget_choice',$w->getWidgetField());
    $this->widgetSchema['widget_choice']->setLabel($w->getGroupName()) ;
  }
  

  public function formatter($widget, $inputs)
  {
    $rows = array();
    foreach ($inputs as $i => $input)
    {
      $rows[] = $widget->renderContentTag(
   	    'td', 
        $input['input'].$widget->getOption('label_separator').$input['label'],
		    array('class' => 'widget_selection')
      );
    }
    return(implode('', $rows));    
  }
  
  
  public function updateObject($values = null)
  {
  	if ($values['title_perso'] == '') $values['title_perso'] = $values['group_name'] ;
  	if ($this->getObject()->getMandatory()) $value['widget_choice'] = 'opened' ;
	parent::updateObject($values) ;
  }
  
}
