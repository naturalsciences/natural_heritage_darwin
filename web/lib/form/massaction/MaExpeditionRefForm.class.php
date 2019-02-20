<?php

class MaExpeditionRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['expedition_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Expeditions',
      'link_url' => 'expedition/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Expedition'),
      'nullable' => true,
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=expedetitions',
    ));

    $this->widgetSchema['expedition_ref']->setLabel('Choose New Expedition');
    $this->validatorSchema['expedition_ref'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Expeditions',
      'required' => false
    ));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_expedition = $values['expedition_ref'];
    if($new_expedition =='') $new_expedition = 0;
    $query->set('s.expedition_ref', '?', $new_expedition);
    return $query;
  }

}
