<?php

/**
 * WidgetProfiles filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class WidgetProfilesFormFilter extends BaseWidgetProfilesFormFilter
{
  /**
   * @see DarwinModelFormFilter
   */
  public function configure()
  {
    
	$this->useFields(array('name', 'creator_ref'));
	$this->widgetSchema['name'] = new sfWidgetFormFilterInput(array('template' => '%input%'));
    $this->addPagerItems();
	$this->widgetSchema->setNameFormat('widgetprofiles[%s]');
  }
  
   public function addNameQuery($query, $field, $val)
  {
	  if(strlen(trim($val))>0)
	  {
		return $query->andWhere(" LOWER(w.name) LIKE '%'||LOWER(?)||'%'",$val);
	  }
	  else
      {
		  return $query;
	  }
  }
  
  public function addCreatorQuery($query, $field, $val)
  {
	  if(strlen(trim($val))>0)
	  {
		return $query->andWhere(" w.creator_ref =?",$val);
	  }
	  else
      {
		  return $query;
	  }
  }
  

   public function doBuildQuery(array $values)
  {

	 $query = parent::doBuildQuery($values);
	$query=DQ::create()
      ->select("DISTINCT w.*, u.formated_name as formated_name")->from("WidgetProfiles w");
     $query->leftJoin("w.Users u on w.creator_ref=u.id");
	$this->addNameQuery($query, $values["name"]["text"],   $values["name"]["text"]);
	$this->addCreatorQuery($query, $values["creator_ref"],   $values["creator_ref"]);
    return $query ;
  }
}
