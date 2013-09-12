<?php

/**
 * Users filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UsersFormFilter extends BaseUsersFormFilter
{
  public function configure()
  {
    $this->useFields(array('family_name','db_user_type','is_physical'));

    $this->addPagerItems();
    $db_user_type = array(''=>'All') ;
    foreach(Users::getTypes($this->options) as $flag => $name)
	    $db_user_type[strval($flag)] = $name;
    $status = array(''=>"All",'true'=>'Physical','false'=>'moral');
    $this->widgetSchema['family_name'] = new sfWidgetFormFilterInput(array('template' => '%input%'));
    $this->widgetSchema['db_user_type'] = new sfWidgetFormChoice(array('choices' => $db_user_type));
    $this->widgetSchema['is_physical'] = new sfWidgetFormChoice(array('choices' => array('' => 'All', 1 => 'Physical', 0 => 'Moral')));
    $this->validatorSchema['db_user_type'] = new sfValidatorInteger(array('required' => false)) ;
  }

  public function addFamilyNameColumnQuery($query, $field, $val)
  {
    return $this->addNamingColumnQuery($query, 'users', 'formated_name_indexed', $val['text']);
  }

  public function addDbUserTypeColumnQuery($query, $field, $val)
  {
    return $query->andWhere($field.' = ?',$val);
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $query->andWhere('id >0');
    if ($this->options['db_user_type']!=8) $query->addWhere('db_user_type <= 4') ;

    $query->select('*, (select max(last_seen) from users_login_infos where user_ref = '.$query->getRootAlias().'.id limit 1) as last_seen');
    return $query ;
  }
}
