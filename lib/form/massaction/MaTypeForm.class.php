<?php

class MaTypeForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['type'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctTypes',
      'method' => 'getType',
      'key_method' => 'getType',
      'add_empty' => true,
      'change_label' => 'Pick a type in the list',
      'add_label' => 'Add another type',
    ));

    $this->widgetSchema['type']->setLabel('Choose New Type');
    $this->validatorSchema['type'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['type'];
    $query->set('s.type', '?', $new_taxon);
    return $query;
  }

}
