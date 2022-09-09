<?php

/**
 * Bibliography filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseBibliographyFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['title'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['title'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['title_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['title_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['abstract'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['abstract'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['year'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['year'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['reference'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['reference'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['doi'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['doi'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'Bibliography', 'column' => 'id'));

    $this->widgetSchema->setNameFormat('bibliography_filters[%s]');
  }

  public function getModelName()
  {
    return 'Bibliography';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'title' => 'Text',
      'title_indexed' => 'Text',
      'type' => 'Text',
      'abstract' => 'Text',
      'year' => 'Number',
      'reference' => 'Text',
      'doi' => 'Text',
      'id' => 'Number',
    ));
  }
}
