<?php

/**
 * DoctrineTaxonomicIdentifications filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDoctrineTaxonomicIdentificationsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['taxonomic_identification'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxonomic_identification'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('doctrine_taxonomic_identifications_filters[%s]');
  }

  public function getModelName()
  {
    return 'DoctrineTaxonomicIdentifications';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'record_id' => 'ForeignKey',
      'taxonomic_identification' => 'Text',
      'record_id' => 'ForeignKey',
    ));
  }
}
