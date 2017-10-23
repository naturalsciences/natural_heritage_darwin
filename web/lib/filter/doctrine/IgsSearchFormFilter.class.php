<?php

/**
 * IgsSearch filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class IgsSearchFormFilter extends BaseIgsSearchFormFilter
{
  public function configure()
  {
    $this->addPagerItems();
    /* IG number Reference */
    $this->widgetSchema['ig_ref'] = new widgetFormInputChecked(
      array(
        'model' => 'Igs',
        'method' => 'getIgNum',
        'nullable' => true,
        'link_url' => 'igs/searchFor',
        'notExistingAddDisplay' => false
      )
    );
    $this->widgetSchema['expedition_name'] = new sfWidgetFormInputText();
    $this->widgetSchema->setNameFormat('searchExpeditionIgs[%s]');
    $this->validatorSchema['ig_ref'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['expedition_name'] = new sfValidatorString(array('required' => false, 'trim' => true));

/** New Pagin System ***/
    $this->widgetSchema['order_dir'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['order_by'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['order_dir'] = new sfValidatorChoice(array('required' => false, 'choices'=> array('asc','desc'),'empty_value'=>'desc'));
    $this->validatorSchema['order_by'] = new sfValidatorString(array('required' => false,'empty_value'=>'collection_name'));
    
    $this->widgetSchema['current_page'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['current_page'] = new sfValidatorInteger(array('required'=>false,'empty_value'=>1));
/** New Pagin System ***/
  }
  
 public function doBuildQuery(array $values)
 {
    $query = DQ::create()
      ->select('ig_ref, expedition_ref, expedition_name, ig_num')
      ->from('IgsSearch')
      ->orderBy('ig_ref, expedition_name')
      ->andWhere('expedition_ref != 0')
      ->groupby('ig_ref, expedition_ref, expedition_name, ig_num');
      ;
    if($values['ig_ref'])
    {
      $query->addWhere('ig_ref = ?', $values['ig_ref']) ;
      if($values['expedition_name']) $this->addNamingColumnQuery($query, 'expeditions', 'expedition_name_indexed', $values['expedition_name'],null,'expedition_name_indexed');
    
    }
    else
    {
      if(!$values['expedition_name']) $query->addWhere('ig_ref = ?', $values['ig_ref']) ;
      else $this->addNamingColumnQuery($query, 'expeditions', 'expedition_name_indexed', $values['expedition_name'],null,'expedition_name_indexed');
    }
    return $query;

  } 


}
