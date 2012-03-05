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
    $this->useFields(array('expedition_name'));
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
  }

  public function doBuildQuery(array $values)
  {
    $query = DQ::create()
      ->select('DISTINCT ig_ref, expedition_ref, expedition_name, ig_num')
      ->from('IgsSearch i')
      ->orderBy('ig_ref, expedition_name')
      ->andWhere('expedition_ref != 0') ;
    if($values['ig_ref'])
    {
      $query->addWhere('ig_ref = ?', $values['ig_ref']) ;
      if($values['expedition_name']) $this->addNamingColumnQuery($query, 'expeditions', 'name_ts', $values['expedition_name'],null,'expedition_name_ts');
    
    }
    else
    {
      if(!$values['expedition_name']) $query->addWhere('ig_ref = ?', $values['ig_ref']) ;
      else $this->addNamingColumnQuery($query, 'expeditions', 'name_ts', $values['expedition_name'],null,'expedition_name_ts');
    }
    return $query;
  }  
}
