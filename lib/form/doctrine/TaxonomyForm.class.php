<?php

/**
 * Taxonomy form.
 *
 * @package    form
 * @subpackage Taxonomy
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class TaxonomyForm extends BaseTaxonomyForm
{
  public function configure()
  {
    unset($this['path'],$this['name_indexed'],$this['name_indexed']);
    $this->widgetSchema['table'] = new sfWidgetFormInputHidden(array('default'=>'taxonomy'));
    $this->widgetSchema['name'] = new sfWidgetFormInput();
    $this->widgetSchema['name']->setAttributes(array('class'=>'large_size'));
    $this->validatorSchema['name']->setOption('trim', true);
    $statuses = array('valid'=>$this->getI18N()->__('valid'), 'invalid'=>$this->getI18N()->__('invalid'), 'deprecated'=>$this->getI18N()->__('deprecated'));
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));

    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'CatalogueLevels',
      'table_method' => array('method'=>'getLevelsByTypes', 'parameters'=>array(array('table'=>'taxonomy'))),
      'add_empty' => true
      ),
      array('class'=>'catalogue_level')
    );

    $this->widgetSchema['parent_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Taxonomy',
      'method' => 'getName',
      'link_url' => 'taxonomy/choose',
      'box_title' => $this->getI18N()->__('Choose Parent'),
      'button_is_hidden' => true,
      'complete_url' => 'catalogue/completeName?table=taxonomy&level=1',
    ));

    $this->validatorSchema['parent_ref']->setOption('required', true);

    $this->widgetSchema->setLabels(array(
      'level_ref' => 'Level',
      'parent_ref' => 'Parent'
    ));

    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($statuses), 'required' => true));
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => false));

  }

}
