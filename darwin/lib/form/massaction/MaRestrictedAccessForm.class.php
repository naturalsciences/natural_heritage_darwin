<?php

class MaRestrictedAccessForm extends BaseForm
{
	public function configure()
  {
    $this->widgetSchema['restricted_access'] = new sfWidgetFormInputCheckbox();

    $this->widgetSchema['restricted_access']->setLabel('Non-public');
    $this->validatorSchema['restricted_access'] = new sfValidatorBoolean(array('required' => false));

  }

  public function doGroupedAction($query, $values, $items)
  {
    $restricted_access = $values['restricted_access'];
    $query->set('s.restricted_access', '?', $restricted_access);
    return $query;
  }

}