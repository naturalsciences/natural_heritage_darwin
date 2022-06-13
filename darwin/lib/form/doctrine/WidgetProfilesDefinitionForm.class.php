<?php

/**
 * WidgetProfilesDefinition form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class WidgetProfilesDefinitionForm extends BaseWidgetProfilesDefinitionForm
{
  /**
   * @see DarwinModelForm
   */
  public function configure()
  {
    $this->useFields(array('title_perso','profile_ref','group_name', 'category'));
    $w = $this->getObject() ;

	$choices = array('unused'=> '', 'is_available' => '', 'visible' => '', 'opened' => '') ;
    $this->widgetSchema['widget_choice'] = new sfWidgetFormChoice(array(
	  'choices' => $choices, 
	  'expanded' => true,
	  'renderer_options' => array('formatter' => array($this, 'formatter'))     
    ));
    $this->widgetSchema['title_perso'] = new sfWidgetFormInputText() ;
    $this->widgetSchema['title_perso']->setAttributes(array('class' => 'medium_size')) ;
    $this->widgetSchema['profile_ref'] = new sfWidgetFormInputHidden() ;
    $this->widgetSchema['group_name'] = new sfWidgetFormInputHidden() ;
    $this->widgetSchema['category'] = new sfWidgetFormInputHidden() ;
    $this->validatorSchema['profile_ref'] = new sfValidatorInteger();
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
