<?php

/**
 * Staging filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class StagingFormFilter extends BaseStagingFormFilter
{
  public function configure()
  {
    $this->useFields(array());
    $this->addPagerItems();
    $this->widgetSchema['only_errors']  = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['only_errors'] = new sfValidatorBoolean(array('required' => false));
    $this->setDefault('only_errors', 1);

    $stype = array(
      'zoology'=> 'Zoology',
      'geology'=> 'Geology',
    );
    $this->widgetSchema['bio_geo']  = new sfWidgetFormChoice(array('expanded' =>true,'choices' => $stype));
    $this->validatorSchema['bio_geo'] =  new sfValidatorChoice(array('choices' => array_keys($stype), 'required'=>false,'empty_value'=>'zoology'));
    $this->setDefault('bio_geo', 'zoology');

    $this->widgetSchema->setLabels(array(
      'only_errors'=>'Show only row with errors',
      'bio_geo' => 'Display type',
    ));
  }

  public function addOnlyErrorsColumnQuery(Doctrine_Query $query, $field, $value)
  {
    if ($value != "")
    {
      $query->andWhere("status != '' ");
    }
  }

  public function  getQuery()
  {
    $query = parent::getQuery();
    $query->andWhere('import_ref = ?',$this->options['import']->getId())
        ->andWhere('to_import = false')
        ->orderBy('id asc');

    return $query;
  }
}
