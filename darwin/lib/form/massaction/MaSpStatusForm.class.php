<?php
/**
 * Created by PhpStorm.
 * User: duchesne
 * Date: 12/05/16
 * Time: 17:34
 */

class MaSpStatusForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['specimen_status'] = new widgetFormSelectComplete(
      array(
        'model' => 'Specimens',
        'table_method' => 'getDistinctSpecimenStatus',
        'method' => 'getSpecimenStatus',
        'key_method' => 'getSpecimenStatus',
        'add_empty' => true,
        'change_label' => 'Pick a status in the list',
        'add_label' => 'Add another Status',
      )
    );

    $this->widgetSchema['specimen_status']->setLabel('Choose New Status');
    $this->validatorSchema['specimen_status'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_status = $values['specimen_status'];
    $query->set('s.specimen_status', '?', $new_status);
    return $query;
  }

}
