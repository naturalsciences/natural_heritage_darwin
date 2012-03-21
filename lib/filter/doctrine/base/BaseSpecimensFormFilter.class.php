<?php

/**
 * Specimens filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSpecimensFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'category'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'collection_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true)),
      'expedition_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'add_empty' => true)),
      'gtu_ref'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true)),
      'taxon_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true)),
      'litho_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lithostratigraphy'), 'add_empty' => true)),
      'chrono_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chronostratigraphy'), 'add_empty' => true)),
      'lithology_ref'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lithology'), 'add_empty' => true)),
      'mineral_ref'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true)),
      'host_taxon_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('HostTaxon'), 'add_empty' => true)),
      'host_specimen_ref'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('HostSpecimen'), 'add_empty' => true)),
      'host_relationship'       => new sfWidgetFormFilterInput(),
      'acquisition_category'    => new sfWidgetFormFilterInput(),
      'acquisition_date_mask'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'acquisition_date'        => new sfWidgetFormFilterInput(),
      'station_visible'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'ig_ref'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'add_empty' => true)),
      'collecting_methods_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods')),
      'collecting_tools_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools')),
    ));

    $this->setValidators(array(
      'category'                => new sfValidatorPass(array('required' => false)),
      'collection_ref'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id')),
      'expedition_ref'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Expeditions'), 'column' => 'id')),
      'gtu_ref'                 => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id')),
      'taxon_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id')),
      'litho_ref'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Lithostratigraphy'), 'column' => 'id')),
      'chrono_ref'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Chronostratigraphy'), 'column' => 'id')),
      'lithology_ref'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Lithology'), 'column' => 'id')),
      'mineral_ref'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id')),
      'host_taxon_ref'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('HostTaxon'), 'column' => 'id')),
      'host_specimen_ref'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('HostSpecimen'), 'column' => 'id')),
      'host_relationship'       => new sfValidatorPass(array('required' => false)),
      'acquisition_category'    => new sfValidatorPass(array('required' => false)),
      'acquisition_date_mask'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'acquisition_date'        => new sfValidatorPass(array('required' => false)),
      'station_visible'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'ig_ref'                  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Igs'), 'column' => 'id')),
      'collecting_methods_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods', 'required' => false)),
      'collecting_tools_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('specimens_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addCollectingMethodsListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('SpecimensMethods.collecting_method_ref', $values)
    ;
  }

  public function addCollectingToolsListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('SpecimensTools.collecting_tool_ref', $values)
    ;
  }

  public function getModelName()
  {
    return 'Specimens';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'category'                => 'Text',
      'collection_ref'          => 'ForeignKey',
      'expedition_ref'          => 'ForeignKey',
      'gtu_ref'                 => 'ForeignKey',
      'taxon_ref'               => 'ForeignKey',
      'litho_ref'               => 'ForeignKey',
      'chrono_ref'              => 'ForeignKey',
      'lithology_ref'           => 'ForeignKey',
      'mineral_ref'             => 'ForeignKey',
      'host_taxon_ref'          => 'ForeignKey',
      'host_specimen_ref'       => 'ForeignKey',
      'host_relationship'       => 'Text',
      'acquisition_category'    => 'Text',
      'acquisition_date_mask'   => 'Number',
      'acquisition_date'        => 'Text',
      'station_visible'         => 'Boolean',
      'ig_ref'                  => 'ForeignKey',
      'collecting_methods_list' => 'ManyKey',
      'collecting_tools_list'   => 'ManyKey',
    );
  }
}
