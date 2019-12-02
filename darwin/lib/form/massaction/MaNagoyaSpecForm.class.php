<?php

class MaNagoyaSpecForm extends BaseForm
{

    
  public function configure() {
  
  
    static $nagoyaanswers = array(
            "yes" 		=> "Yes",
            "no" 		=> "No",
            "not defined"     	=> "Not defined"
        );
	 $this->widgetSchema['nagoya_specimen'] = new sfWidgetFormChoice(array(
      'choices' =>  $nagoyaanswers,
    ));
	$this->setDefault('nagoya_specimen', "not defined");
	$this->validatorSchema['nagoya_specimen'] = new sfValidatorChoice(array('choices' => array_keys($nagoyaanswers), 'required' => true));     

    $this->widgetSchema['nagoya_specimen']->setLabel('Choose new value for Nagoya in specimen');
  }

  public function doGroupedAction($query, $values, $items)
  { 
    $new_nagoya = $values['nagoya_specimen'];
    $query->set('s.nagoya', '?', $new_nagoya);
    return $query;
  }
}