<?php

/**
 * Bibliography filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class BibliographyFormFilter extends BaseBibliographyFormFilter
{
  public function configure() {
    $this->useFields(array('title'));
    $this->addPagerItems();
    $this->widgetSchema['title'] = new sfWidgetFormInputText();
    $this->widgetSchema->setNameFormat('searchBibliography[%s]');
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false, 'trim' => true));
	
	
	$this->widgetSchema['reference'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->validatorSchema['reference'] = new sfValidatorString(array('required' => false, 'trim' => true));
    
	
	$protocol_tmp=array_unique(array_merge(array(""=>"All"), array_map('strtolower',array_change_key_case(Doctrine_Core::getTable("Bibliography")->getDistinctUriProtocol()))));
    $this->widgetSchema['uri_protocol'] = new sfWidgetFormChoice(array(
       "choices"=> $protocol_tmp,
       'multiple' => false,
    ), array("size"=>1));

    $this->validatorSchema['uri_protocol'] =  new sfValidatorChoice(
        array(
         "choices"=> $protocol_tmp,
         'multiple' => false,
         "required"=>false
         )
    );
    $this->widgetSchema['uri'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->validatorSchema['uri'] = new sfValidatorString(array('required' => false, 'trim' => true));
	$this->widgetSchema['uri']->setAttributes(array('class'=>'medium_small_size'));
    $this->widgetSchema['uri']->setLabel('URI');
    
    $this->widgetSchema['author_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->validatorSchema['author_name'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->widgetSchema['author_name']->setLabel('Author (single person name)');

    $choices = array_merge(array(''=>''),Bibliography::getAvailableTypes());
    $this->widgetSchema['type'] =  new sfWidgetFormChoice(array(
      'choices' =>  $choices,
    ));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required'=>false,'choices'=>array_keys($choices)));
	
	$this->widgetSchema['year'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->validatorSchema['year'] = new sfValidatorString(array('required' => false, 'trim' => true));
  }

   public function doBuildQuery(array $values)
  {
    $query = DQ::create()
      ->select("b.*, string_agg(p.formated_name, ', ' ORDER BY c.id) as authors")->from('Bibliography b')->leftJoin("b.CataloguePeopleBibliography c ON b.id=c.record_id AND c.referenced_relation='bibliography' ")->leftJoin("c.People p ON c.people_ref=p.id AND c.referenced_relation='bibliography'");
     if($values['type'] != "")
    {
        $query->andWhere("type = ? ", $values['type']);
    }
    if($values['title'] != "")
    {
        $query->andWhere("b.title_indexed LIKE '%'|| fulltoindex(?)||'%' ", $values['title']);
    }
    if($values['reference'] != "")
    {
        $query->andWhere("fulltoindex(reference) LIKE '%'|| fulltoindex(?)||'%' ", $values['reference']);
    }
	if($values['uri_protocol'] != "")
    {
        $query->andWhere("LOWER(uri_protocol) = LOWER(?) ", $values['uri_protocol']);
    }
    if($values['uri'] != "")
    {
        $query->andWhere("LOWER(uri) = LOWER(?) ", $values['uri']);
    }
    if($values['year'] != "")
    {
        $query->andWhere("year = ? ", $values['year']);
    }
     if($values['author_name'] != "")
    {
        $query->andWhere("EXISTS (SELECT p2.id FROM  People p2 WHERE p2.formated_name_indexed LIKE '%'|| fulltoindex(?)||'%'  AND p2.id= p.id)  ", $values['author_name']);
    }
    $query->andWhere("b.id > 0 ");
    $query->groupBy("b.id, b.uri, b.uri_protocol, b.type, b.year, b.title, b.reference");
    return $query;
  }

  public function addTypeColumnQuery($query, $field, $val) {
    if($val != '') {
      $alias = $query->getRootAlias() ;
      $query->andWhere($alias.".type = ?",$val);
    }
    return $query;
  }
}
