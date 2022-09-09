<?php

/**
 * CollectingMethods filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectingMethodsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['method'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['method'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['method_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['method_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimens_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Specimens'));
    $this->validatorSchema['specimens_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Specimens', 'required' => false));

    $this->widgetSchema->setNameFormat('collecting_methods_filters[%s]');
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
      ->leftJoin($query->getRootAlias().'.SpecimensMethods SpecimensMethods')
      ->andWhereIn('SpecimensMethods.specimen_ref', $values)
    ;
  }

  public function getModelName()
  {
    return 'CollectingMethods';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'method' => 'Text',
      'method_indexed' => 'Text',
      'specimens_list' => 'ManyKey',
    ));
  }
}
