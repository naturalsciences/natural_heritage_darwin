<?php

/**
 * Reports filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseReportsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['uri'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['uri'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['lang'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['format'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['format'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parameters'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['parameters'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comment'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('reports_filters[%s]');
  }

  public function getModelName()
  {
    return 'Reports';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'user_ref' => 'ForeignKey',
      'name' => 'Text',
      'uri' => 'Text',
      'lang' => 'Text',
      'format' => 'Text',
      'parameters' => 'Text',
      'comment' => 'Text',
      'user_ref' => 'ForeignKey',
    ));
  }
}
