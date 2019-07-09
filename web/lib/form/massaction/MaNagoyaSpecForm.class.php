<?php

class MaNagoyaSpecForm extends BaseForm
{
  public function configure() {
	  $this->widgetSchema['nagoya_specimen'] = new sfWidgetFormChoice(array(
        'expanded' => true,
        'choices'  => array(True => 'true', False => 'false'),
        'default'=> true,
       
        ), array( 'style' => "display: inline-block;text-align:center; width: auto !important"));
    $this->validatorSchema['nagoya_specimen'] = new sfValidatorString(array('required' => true));     

    $this->widgetSchema['nagoya_specimen']->setLabel('Choose new value for Nagoya in specimen');
  }

  public function doGroupedAction($query, $values, $items)
  { 
    $new_nagoya = $values['nagoya_specimen'];
    $query->set('s.nagoya', '?', $new_nagoya);
    return $query;
  }
}