<?php

class MaMineralogyRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['mineralogy_ref'] = new widgetFormCompleteButtonRef(array(
       'model' => 'Mineralogy',
       'link_url' => 'mineralogy/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Mineralogic unit'),
       'nullable' => true,
       'button_class'=>'',
       'complete_url' => 'catalogue/completeName?table=mineralogy',
     ));

    $this->widgetSchema['mineralogy_ref']->setLabel('Choose Mineralogic unit');
    $this->validatorSchema['mineralogy_ref'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Mineralogy',
      'required' => false
    ));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['mineralogy_ref'];
    if($new_taxon =='') $new_taxon = 0;
    $query->set('s.mineral_ref', '?', $new_taxon);
    return $query;
  }

}
