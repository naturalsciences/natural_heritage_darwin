<?php

/**
 * TaxonomicIdentifications filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTaxonomicIdentificationsFormFilter extends IdentificationsFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('taxonomic_identifications_filters[%s]');
  }

  public function getModelName()
  {
    return 'TaxonomicIdentifications';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'record_id' => 'ForeignKey',
    ));
  }
}
