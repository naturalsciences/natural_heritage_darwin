<?php

/**
 * Institutions filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class InstitutionsFormFilter extends BaseInstitutionsFormFilter
{
  public function configure()
  {
    $this->useFields(array('family_name','is_physical'));

    $this->addPagerItems();
    $this->widgetSchema['is_physical'] = new sfWidgetFormInputHidden();
    $this->setDefault('is_physical', 0); 

    $this->widgetSchema['family_name'] = new sfWidgetFormInput();
    $this->widgetSchema['family_name']->setAttributes(array('class'=>'medium_size'));
    $people_types = array(''=>'');
    $types = People::getTypes();
    foreach($types as $flag => $name)
      $people_types[strval($flag)] = $name;
    $this->widgetSchema['people_type'] = new sfWidgetFormChoice(array('choices' => $people_types )); 
    $this->widgetSchema['people_type']->setLabel('Role');         
    $this->validatorSchema['people_type'] = new sfValidatorChoice(array('choices' => $people_types, 'required' => false));
  }

  public function addFamilyNameColumnQuery($query, $field, $val)
  {
    return $this->addNamingColumnQuery($query, 'people', 'formated_name_ts', $val);
  }

  public function addOnlyRoleColumnQuery($query, $field, $val)
  {
    if($val != 0)
      $query->andWhere("(db_people_type &  ?) != 0 ", intval($val));
    return $query;
  }

}
