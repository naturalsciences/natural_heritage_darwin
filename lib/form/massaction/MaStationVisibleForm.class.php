<?php

class MaStationVisibleForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['station_visible'] = new sfWidgetFormInputCheckbox();

    $this->widgetSchema['station_visible']->setLabel('Is Station Visible');
    $this->validatorSchema['station_visible'] = new sfValidatorBoolean();

  }

  public function doGroupedAction($query,$values, $items)
  {
    $value = $values['station_visible'];
    $query->set('s.station_visible', '?', $value);
    return $query;
  }

}
