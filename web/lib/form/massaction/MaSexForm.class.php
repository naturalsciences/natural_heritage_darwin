<?php

class MaSexForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['sex'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSexes',
      'method' => 'getSex',
      'key_method' => 'getSex',
      'add_empty' => true,
      'change_label' => 'Pick a sex in the list',
      'add_label' => 'Add another Sex',
    ));

    $this->widgetSchema['sex']->setLabel('Choose New Sex');
    $this->validatorSchema['sex'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['sex'];
    $query->set('s.sex', '?', $new_taxon);
    return $query;
  }

}
