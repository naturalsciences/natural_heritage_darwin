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
    $query->set('s.row', '?', $new_taxon);
    return $query;
  }

}
