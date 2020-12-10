<?php

class MaColForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['col'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctCols',
      'method' => 'getCols',
      'key_method' => 'getCols',
      'add_empty' => true,
      'change_label' => 'Pick a column in the list',
      'add_label' => 'Add another column',
    ));

    $this->widgetSchema['col']->setLabel('Choose New Column');
    $this->validatorSchema['col'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['col'];
    $query->set('p.col', '?', $new_taxon);
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
