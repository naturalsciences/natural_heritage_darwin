<?php

class MaShelfForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['shelf'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctShelfs',
      'method' => 'getShelfs',
      'key_method' => 'getShelfs',
      'add_empty' => true,
      'change_label' => 'Pick a shelf in the list',
      'add_label' => 'Add another Shelf',
    ));

    $this->widgetSchema['shelf']->setLabel('Choose New Shelf');
    $this->validatorSchema['shelf'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['shelf'];
    $query->set('p.shelf', '?', $new_taxon);
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
