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
    $query->set('s.col', '?', $new_taxon);
    return $query;
  }

}
