<?php

/**
 * MyWidgets form.
 *
 * @package    form
 * @subpackage MyWidgets
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class WidgetRightsForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema['widget_ref'] = new sfWidgetWidgetRights(array(
        'user_ref' => $this->options['user_ref'] ,
        'collection_ref' => $this->options['collection_ref'],
        'choices' => array()
      ));
    $this->validatorSchema['widget_ref'] = new sfValidatorPass();
    $this->widgetSchema->setNameFormat('widget_rights[%s]');
  }

  public function save()
  {
    $value = $this->getValue('widget_ref'); // checked by user values

    $widget_ref = $this->getWidget('widget_ref')->getChoices() ;
	  if(!$value) $value = array() ;
	  $insert_right = array() ;
	  $delete_right = array() ;
	  foreach($value as $id)
	  {
	    if(!in_array($id,$widget_ref['old_right'])) //then we have to add collection_ref to this widget
	      $insert_right[] = $id ;
	  }
	  foreach($widget_ref['old_right'] as $old)
	  {
	    if(!in_array($old,$value)) //then we have to remove collection_ref to this widget
	      $delete_right[] = $old ;
    }
    if (count($insert_right) > 0) {
      Doctrine::getTable('MyWidgets')->
        setUserRef($this->options['user_ref'])->
        doUpdateWidgetRight($this->options['collection_ref'], $insert_right,'insert');
    }
    if (count($delete_right) > 0) {
      Doctrine::getTable('MyWidgets')->
        setUserRef($this->options['user_ref'])->
        doUpdateWidgetRight($this->options['collection_ref'],$delete_right,'delete');
    }
  }
}
