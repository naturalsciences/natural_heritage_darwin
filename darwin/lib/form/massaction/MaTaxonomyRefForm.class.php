<?php

class MaTaxonomyRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['taxon_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Taxonomy',
      'link_url' => 'taxonomy/choose',
      'method' => 'getNameWithFormat',
      'box_title' => $this->getI18N()->__('Choose Taxon'),
      'nullable' => true,
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=taxonomy',
    ));

    $this->widgetSchema['taxon_ref']->setLabel('Choose New Taxon');
    $this->validatorSchema['taxon_ref'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Taxonomy',
      'required' => false
    ));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['taxon_ref'];
    if($new_taxon =='') $new_taxon = 0;
    $query->set('s.taxon_ref', '?', $new_taxon);
    return $query;
  }

}
