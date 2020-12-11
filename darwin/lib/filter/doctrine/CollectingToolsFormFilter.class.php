<?php

/**
 * CollectingTools filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CollectingToolsFormFilter extends BaseCollectingToolsFormFilter
{
  public function configure()
  {
    $this->useFields(array('tool'));
    $this->addPagerItems();    
    $this->widgetSchema['tool'] = new sfWidgetFormInputText();
    $this->widgetSchema->setNameFormat('searchMethodsAndTools[%s]');
    $this->validatorSchema['tool'] = new sfValidatorString(array('required' => false, 'trim' => true));
  }

  public function doBuildQuery(array $values)
  {
    $query = DQ::create()
      ->select("DISTINCT t.*, COUNT(DISTINCT o.specimen_ref) as countspecimens, string_agg(distinct collection_name,'; ' order by collection_name) as collections"
      )
      ->from('CollectingTools t');
    $query->leftJoin("t.SpecimensTools o");
    $query->leftJoin("o.Specimens s");
    $this->addNamingColumnQuery($query, 'collecting_tools', 'tool_indexed', $values['tool']);
    $query->andWhere("id > 0 ")->groupBy("t.id, t.tool, t.tool_indexed");
    return $query;
  }
}
