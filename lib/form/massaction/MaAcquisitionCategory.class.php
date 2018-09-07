<?php

class MaAcquisitionCategoryForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['acquisition_category'] = new sfWidgetFormChoice(array(
      'choices' =>  SpecimensTable::getDistinctCategories(),
    ));

    $this->validatorSchema['acquisition_category'] = new sfValidatorChoice(array(
      'choices' => array_keys(SpecimensTable::getDistinctCategories()),
      'required' => false,
    ));
  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['acquisition_category'];
    $query->set('s.acquisition_category', '?', $new_taxon);
    return $query;
  }

}
