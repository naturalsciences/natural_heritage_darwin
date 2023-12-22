<?php

class MaSpecimenPartForm extends BaseForm
{
  public function configure()
  {
     $this->widgetSchema['specimen_part'] = new sfWidgetFormInput(array(),array('style'=> 'width:97%;'));
	//ftheeten 2017 01 12
    $this->widgetSchema['specimen_part']->setAttributes(array('class'=>'autocomplete_for_parts'));

    $this->widgetSchema['specimen_part']->setLabel('Change Specimen part');
    $this->validatorSchema['specimen_part'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['specimen_part'];
    $query->set('p.specimen_part', '?', $new_taxon);
    return $query;
  }
  
  //ftheeten 2017 07 27
    public function getTable()
    {
        $returned=Array();
        $returned['p']='StorageParts';
        return $returned;
    }    

}