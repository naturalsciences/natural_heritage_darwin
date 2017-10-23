<?php

class MaBuildingForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['building'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctBuildings',
      'method' => 'getBuildings',
      'key_method' => 'getBuildings',
      'add_empty' => true,
      'change_label' => 'Pick a building in the list',
      'add_label' => 'Add another building',
    ));

    $this->widgetSchema['building']->setLabel('Choose New Building');
    $this->validatorSchema['building'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['building'];
    $query->set('p.building', '?', $new_taxon);
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