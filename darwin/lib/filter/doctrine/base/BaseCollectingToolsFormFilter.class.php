<?php

/**
 * CollectingTools filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectingToolsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['tool'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['tool'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tool_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['tool_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimens_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Specimens'));
    $this->validatorSchema['specimens_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Specimens', 'required' => false));

    $this->widgetSchema->setNameFormat('collecting_tools_filters[%s]');
  }

  public function addSpecimensListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.SpecimensTools SpecimensTools')
      ->andWhereIn('SpecimensTools.specimen_ref', $values)
    ;
  }

  public function getModelName()
  {
    return 'CollectingTools';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'tool' => 'Text',
      'tool_indexed' => 'Text',
      'specimens_list' => 'ManyKey',
    ));
  }
}
