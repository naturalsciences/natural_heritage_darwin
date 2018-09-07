<?php
/**
 * Created by PhpStorm.
 * User: duchesne
 * Date: 10/03/16
 * Time: 11:43
 */

class MaExtLinksForm extends BaseExtLinksForm
{
  public function configure()
  {
    $this->useFields(array('url','type', 'comment'));

    $this->widgetSchema['url'] = new sfWidgetFormInputText();
    $this->widgetSchema['url']->setAttributes(array('class'=>'small_medium_size'));

    /* Validators */
    $this->validatorSchema['url'] = new sfValidatorString(array('required'=>false));
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->mergePostValidator(new ExtLinksValidatorSchema());

    $this->widgetSchema['type'] = new sfWidgetFormChoice(array(
                                                           'choices' => ExtLinks::getLinkTypes(),
                                                         ));

    $this->validatorSchema['type'] = new sfValidatorChoice(array('choices'=>array_keys(ExtLinks::getLinkTypes())));

  }

  public function doMassAction($user_id, $items, $values)
  {
    $query = Doctrine_Query::create()->select('id')->from('Specimens s');
    $query->andWhere('s.id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$items),'spec_ref', $user_id));
    $results = $query->execute();

    foreach($results as $result)
    {
      $ext_links = new ExtLinks();
      $ext_links->fromArray($values);
      $ext_links->setRecordId($result->getId());
      $ext_links->setReferencedRelation("specimens");
      $ext_links->save();
    }
  }

}
