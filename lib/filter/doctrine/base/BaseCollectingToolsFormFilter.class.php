<?php

/**
 * CollectingTools filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCollectingToolsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'tool'                     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'tool_indexed'             => new sfWidgetFormFilterInput(),
      'specimens_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Specimens')),
      'specimens_maincodes_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'SpecimensMaincodes')),
    ));

    $this->setValidators(array(
      'tool'                     => new sfValidatorPass(array('required' => false)),
      'tool_indexed'             => new sfValidatorPass(array('required' => false)),
      'specimens_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Specimens', 'required' => false)),
      'specimens_maincodes_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'SpecimensMaincodes', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('collecting_tools_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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

  public function addSpecimensMaincodesListColumnQuery(Doctrine_Query $query, $field, $values)
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
    return array(
      'id'                       => 'Number',
      'tool'                     => 'Text',
      'tool_indexed'             => 'Text',
      'specimens_list'           => 'ManyKey',
      'specimens_maincodes_list' => 'ManyKey',
    );
  }
}
