<?php

/**
 * CollectingMethods filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCollectingMethodsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'method'                   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'method_indexed'           => new sfWidgetFormFilterInput(),
      'specimens_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Specimens')),
      'specimens_maincodes_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'SpecimensMaincodes')),
    ));

    $this->setValidators(array(
      'method'                   => new sfValidatorPass(array('required' => false)),
      'method_indexed'           => new sfValidatorPass(array('required' => false)),
      'specimens_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Specimens', 'required' => false)),
      'specimens_maincodes_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'SpecimensMaincodes', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('collecting_methods_filters[%s]');

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
      ->leftJoin($query->getRootAlias().'.SpecimensMethods SpecimensMethods')
      ->andWhereIn('SpecimensMethods.specimen_ref', $values)
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
    return array(
      'id'                       => 'Number',
      'method'                   => 'Text',
      'method_indexed'           => 'Text',
      'specimens_list'           => 'ManyKey',
      'specimens_maincodes_list' => 'ManyKey',
    );
  }
}
