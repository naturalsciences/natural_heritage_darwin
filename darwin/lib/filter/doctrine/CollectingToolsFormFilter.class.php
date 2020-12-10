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
    $query = parent::doBuildQuery($values);
    $this->addNamingColumnQuery($query, 'collecting_tools', 'tool_indexed', $values['tool']);
    $query->andWhere("id > 0 ");
    return $query;
  }
}
