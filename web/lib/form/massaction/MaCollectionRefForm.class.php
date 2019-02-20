<?php

class MaCollectionRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['collection_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Collections',
      'link_url' => 'collection/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Collection'),
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=collections',
    ));

    $this->widgetSchema['collection_ref']->setLabel('Choose New Collection');
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Collections',
      'min' => 0,
      'required' => true
    ));

  }

  public function doGroupedAction($query, $values, $items)
  {
    $new_collection = $values['collection_ref'];
    $query->set('s.collection_ref', '?', $new_collection);
    return $query;
  }
}
