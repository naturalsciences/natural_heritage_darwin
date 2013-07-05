<?php

class MaSocialStatusForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['social_status'] = new widgetFormSelectComplete(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSocialStatuses',
      'method' => 'getSocialStatus',
      'key_method' => 'getSocialStatus',
      'add_empty' => true,
      'change_label' => 'Pick a status in the list',
      'add_label' => 'Add another Status',
    ));

    $this->widgetSchema['social_status']->setLabel('Choose New Status');
    $this->validatorSchema['social_status'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['social_status'];
    $query->set('s.social_status', '?', $new_taxon);
    return $query;
  }

}
