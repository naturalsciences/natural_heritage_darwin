<?php

/**
 * ClassificationSynonymies form.
 *
 * @package    form
 * @subpackage ClassificationSynonymies
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class ClassificationSynonymiesForm extends BaseClassificationSynonymiesForm
{
  public function configure()
  {
    unset($this['id'], $this['is_basionym'], $this['group_id']);

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['group_name'] = new sfWidgetFormChoice(array(
    	'choices' => Doctrine::getTable('ClassificationSynonymies')->findGroupnames(),
	'expanded' => false)
    );

    $this->widgetSchema['record_id'] = new widgetFormJQueryDLookup(
      array(
	'model' => DarwinTable::getModelForTable($this->options['table']),
	'method' => 'getName',
	'nullable' => false,
        'fieldsHidders' => array('classification_synonymies_group_name',),
      ),
      array('class' => 'hidden',)
    );

    $this->widgetSchema['merge'] = new sfWidgetFormInputCheckbox();

    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required' => true));
    $this->validatorSchema['merge'] = new sfValidatorChoice(array('required' => true,'choices' => array('true', 't', 'yes', 'y', 'on', 1)));
  }
}
