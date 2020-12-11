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
    $query = DQ::create()
      ->select("DISTINCT t.*, COUNT(DISTINCT o.specimen_ref) as countspecimens, string_agg(distinct collection_name,'; ' order by collection_name) as collections"
      )
      ->from('CollectingMethods t');
    $query->leftJoin("t.SpecimensMethods o ON t.id=o.collecting_method_ref");
    $query->leftJoin("o.Specimens s ON o.specimen_ref=s.id");
    //$this->addNamingColumnQuery($query, 'collecting_methods', 'method_indexed', $values['method']);
    $query->where("method_indexed LIKE '%'||fulltoindex(?)||'%'",$values['method']);
    $query->groupBy("t.id, t.method, t.method_indexed");;
    return $query;
  }
}
