<?php

/**
 * CollectingMethods filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CollectingMethodsFormFilter extends BaseCollectingMethodsFormFilter
{
  public function configure()
  {
    $this->useFields(array('method'));
    $this->addPagerItems();
    $this->widgetSchema['method'] = new sfWidgetFormInputText();
    $this->widgetSchema->setNameFormat('searchMethodsAndTools[%s]');
    $this->validatorSchema['method'] = new sfValidatorString(array('required' => false, 'trim' => true));
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $this->addNamingColumnQuery($query, 'collecting_methods', 'method_indexed', $values['method']);
    $query->andWhere("id > 0 ");
    return $query;
  }
}
