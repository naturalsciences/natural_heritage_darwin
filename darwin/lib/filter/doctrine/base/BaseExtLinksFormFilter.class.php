<?php

/**
 * ExtLinks filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseExtLinksFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['url'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['url'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comment'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comment_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comment_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['category'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['contributor'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['contributor'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['disclaimer'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['disclaimer'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['license'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['license'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['display_order'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['display_order'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema->setNameFormat('ext_links_filters[%s]');
  }

  public function getModelName()
  {
    return 'ExtLinks';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'url' => 'Text',
      'comment' => 'Text',
      'comment_indexed' => 'Text',
      'category' => 'Text',
      'contributor' => 'Text',
      'disclaimer' => 'Text',
      'license' => 'Text',
      'display_order' => 'Number',
    ));
  }
}
