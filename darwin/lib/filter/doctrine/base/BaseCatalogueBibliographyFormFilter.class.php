<?php

/**
 * CatalogueBibliography filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCatalogueBibliographyFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['bibliography_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'), 'add_empty' => true));
    $this->validatorSchema['bibliography_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Bibliography'), 'column' => 'id'));

    $this->widgetSchema   ['bibliography_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'), 'add_empty' => true));
    $this->validatorSchema['bibliography_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Bibliography'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('catalogue_bibliography_filters[%s]');
  }

  public function getModelName()
  {
    return 'CatalogueBibliography';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'bibliography_ref' => 'ForeignKey',
      'bibliography_ref' => 'ForeignKey',
    ));
  }
}
