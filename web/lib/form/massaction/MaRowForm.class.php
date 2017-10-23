<?php

class MaRowForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['row'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctRows',
      'method' => 'getRows',
      'key_method' => 'getRows',
      'add_empty' => true,
      'change_label' => 'Pick a row in the list',
      'add_label' => 'Add another Row',
    ));

    $this->widgetSchema['row']->setLabel('Choose New Row');
    $this->validatorSchema['row'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['row'];
    $query->set('p.row', '?', $new_taxon);
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
