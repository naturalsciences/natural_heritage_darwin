<?php

/**
 * Chronostratigraphy form.
 *
 * @package    form
 * @subpackage Chronostratigraphy
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class ChronostratigraphyForm extends BaseChronostratigraphyForm
{
  public function configure()
  {
    unset($this['path']);

    $this->widgetSchema['name'] = new sfWidgetFormInput();
    $this->widgetSchema['name']->setAttributes(array('class'=>'large_size'));
    $this->widgetSchema['lower_bound'] = new sfWidgetFormInput();
    $this->widgetSchema['lower_bound']->setAttributes(array('class'=>'small_size datesNum'));
    $this->widgetSchema['upper_bound'] = new sfWidgetFormInput();
    $this->widgetSchema['upper_bound']->setAttributes(array('class'=>'small_size datesNum'));
    $statuses = array('valid'=>$this->getI18N()->__('valid'), 'invalid'=>$this->getI18N()->__('invalid'), 'deprecated'=>$this->getI18N()->__('deprecated'));
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));
    $this->widgetSchema['level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes', 'parameters'=>array(array('type'=>'chronostratigraphy'))),
        'add_empty' => true
      ));
    $this->widgetSchema['parent_ref'] = new widgetFormButtonRef(array(
       'model' => 'Chronostratigraphy',
       'method' => 'getName',
       'link_url' => 'chronostratigraphy/choose',
       'box_title' => $this->getI18N()->__('Choose Parent'),
     ));
    $this->widgetSchema->setLabels(array('level_ref' => 'Level',
                                         'lower_bound' => 'Low. bound (My)',
                                         'upper_bound' => 'Up. bound (My)'
                                        )
                                  );
    $this->validatorSchema['lower_bound'] = new sfValidatorNumber(array('required' => false, 'min' => -4600));
    $this->validatorSchema['upper_bound'] = new sfValidatorNumber(array('required' => false, 'max' => 1));
    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($statuses), 'required' => true));
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('lower_bound', 
                                                                          '<=', 
                                                                          'upper_bound', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>$this->getI18N()->__('The lower bound (%left_field%) cannot be above the upper bound (%right_field%).'))
                                                                         )
                                            );
    $this->addKeywordsRelation('chronostratigraphy');
    $subForm = new sfForm();
    $this->embedForm('newVal',$subForm);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bindKeywords($taintedValues,$taintedFiles);
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    parent::saveKeywordsEmbeddedForms($con, $forms);
    return parent::saveEmbeddedForms($con, $forms);
  }
}