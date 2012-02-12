<?php

/**
 * Specimens form base class.
 *
 * @method Specimens getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimensForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'category'                => new sfWidgetFormTextarea(),
      'collection_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => false)),
      'expedition_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'add_empty' => true)),
      'gtu_ref'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true)),
      'taxon_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true)),
      'litho_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lithostratigraphy'), 'add_empty' => true)),
      'chrono_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chronostratigraphy'), 'add_empty' => true)),
      'lithology_ref'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lithology'), 'add_empty' => true)),
      'mineral_ref'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true)),
      'host_taxon_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('HostTaxon'), 'add_empty' => true)),
      'host_specimen_ref'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('HostSpecimen'), 'add_empty' => true)),
      'host_relationship'       => new sfWidgetFormTextarea(),
      'acquisition_category'    => new sfWidgetFormTextarea(),
      'acquisition_date_mask'   => new sfWidgetFormInputText(),
      'acquisition_date'        => new sfWidgetFormTextarea(),
      'station_visible'         => new sfWidgetFormInputCheckbox(),
      'multimedia_visible'      => new sfWidgetFormInputCheckbox(),
      'ig_ref'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'add_empty' => true)),
      'collecting_methods_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods')),
      'collecting_tools_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools')),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'category'                => new sfValidatorString(array('required' => false)),
      'collection_ref'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'))),
      'expedition_ref'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'required' => false)),
      'gtu_ref'                 => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'required' => false)),
      'taxon_ref'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'required' => false)),
      'litho_ref'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Lithostratigraphy'), 'required' => false)),
      'chrono_ref'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Chronostratigraphy'), 'required' => false)),
      'lithology_ref'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Lithology'), 'required' => false)),
      'mineral_ref'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'required' => false)),
      'host_taxon_ref'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('HostTaxon'), 'required' => false)),
      'host_specimen_ref'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('HostSpecimen'), 'required' => false)),
      'host_relationship'       => new sfValidatorString(array('required' => false)),
      'acquisition_category'    => new sfValidatorString(array('required' => false)),
      'acquisition_date_mask'   => new sfValidatorInteger(array('required' => false)),
      'acquisition_date'        => new sfValidatorString(array('required' => false)),
      'station_visible'         => new sfValidatorBoolean(array('required' => false)),
      'multimedia_visible'      => new sfValidatorBoolean(array('required' => false)),
      'ig_ref'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Igs'), 'required' => false)),
      'collecting_methods_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingMethods', 'required' => false)),
      'collecting_tools_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'CollectingTools', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('specimens[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Specimens';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['collecting_methods_list']))
    {
      $this->setDefault('collecting_methods_list', $this->object->CollectingMethods->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['collecting_tools_list']))
    {
      $this->setDefault('collecting_tools_list', $this->object->CollectingTools->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveCollectingMethodsList($con);
    $this->saveCollectingToolsList($con);

    parent::doSave($con);
  }

  public function saveCollectingMethodsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['collecting_methods_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->CollectingMethods->getPrimaryKeys();
    $values = $this->getValue('collecting_methods_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('CollectingMethods', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('CollectingMethods', array_values($link));
    }
  }

  public function saveCollectingToolsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['collecting_tools_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->CollectingTools->getPrimaryKeys();
    $values = $this->getValue('collecting_tools_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('CollectingTools', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('CollectingTools', array_values($link));
    }
  }

}
