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
	
	$protocol_tmp=Doctrine_Core::getTable("Identifiers")->getDistinctProtocol();
    $this->widgetSchema['protocol'] = new sfWidgetFormChoice(array(
       "choices"=> $protocol_tmp
    ));

    $this->validatorSchema['protocol'] =  new sfValidatorChoice(
        array(
         "choices"=> $protocol_tmp,
         'multiple' => false,
         "required"=>false
         )
    );
	
	$this->widgetSchema['identifier'] = new sfWidgetFormInput();
	$this->validatorSchema['identifier'] = new sfValidatorPass();
  }

  public function addFamilyNameColumnQuery($query, $field, $val)
  {
    return $this->addNamingColumnQuery($query, 'users', 'formated_name_indexed', $val['text']);
  }

  public function addDbUserTypeColumnQuery($query, $field, $val)
  {
    return $query->andWhere($field.' = ?',$val);
  }
  
  public function addIdentifierQuery($query, $alias, $protocol, $identifier)
  {
	  if(strlen($protocol)>0&&strlen($identifier)>0)
	  {
		   $query->andWhere("EXISTS (select i.id  from Identifiers i where $alias.id = i.record_id AND referenced_relation = 'users' AND LOWER(i.protocol)=? AND i.value=?)",array(strtolower($protocol), $identifier));
	  }
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
	$alias = $query->getRootAlias() ;
	$query->select("$alias.*, string_agg(iu.protocol||' : '||iu.value,'; ') as identifiers, (select max(last_seen) from users_login_infos where user_ref = ".$query->getRootAlias().".id limit 1) as last_seen")->leftJoin("$alias.IdentifiersUsers iu ON $alias.id=iu.record_id");
    $query->andWhere('id >0');
    if ($this->options['db_user_type']!=8) $query->addWhere('db_user_type <= 4') ;
    $this->addIdentifierQuery($query, $alias, $values['protocol'],$values['identifier']);
    $query ->groupBy("$alias.id");
    return $query ;
  }
}
