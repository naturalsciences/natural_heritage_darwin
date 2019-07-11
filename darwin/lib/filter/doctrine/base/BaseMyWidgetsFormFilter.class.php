<?php

/**
 * MyWidgets filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseMyWidgetsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema   ['category'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['group_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['order_by'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['order_by'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['col_num'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['col_num'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['mandatory'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['mandatory'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['visible'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['visible'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['is_available'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_available'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['opened'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['opened'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['color'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['color'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['icon_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['icon_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['title_perso'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['title_perso'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collections'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collections'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['all_public'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['all_public'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('my_widgets_filters[%s]');
  }

  public function getModelName()
  {
    return 'MyWidgets';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'user_ref' => 'ForeignKey',
      'category' => 'Text',
      'group_name' => 'Text',
      'order_by' => 'Number',
      'col_num' => 'Number',
      'mandatory' => 'Boolean',
      'visible' => 'Boolean',
      'is_available' => 'Boolean',
      'opened' => 'Boolean',
      'color' => 'Text',
      'icon_ref' => 'Number',
      'title_perso' => 'Text',
      'collections' => 'Text',
      'all_public' => 'Boolean',
      'user_ref' => 'ForeignKey',
    ));
  }
}
