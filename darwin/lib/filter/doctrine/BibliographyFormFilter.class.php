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
    $this->useFields(array('title', 'type', 'doi', 'reference', 'abstract','year'));
    $this->addPagerItems();
    $this->widgetSchema['title'] = new sfWidgetFormInputText();
    $this->widgetSchema->setNameFormat('searchBibliography[%s]');
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false, 'trim' => true));
    
    $this->widgetSchema['reference'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->validatorSchema['reference'] = new sfValidatorString(array('required' => false, 'trim' => true));
    
    $this->widgetSchema['doi'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->validatorSchema['doi'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->widgetSchema['doi']->setLabel('D.O.I.');
    
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
    
    if($values['title'] != "")
    {
        $query->andWhere("b.title_indexed LIKE '%'|| fulltoindex(?)||'%' ", $values['title']);
    }
    if($values['reference'] != "")
    {
        $query->andWhere("fulltoindex(reference) LIKE '%'|| fulltoindex(?)||'%' ", $values['reference']);
    }
    if($values['doi'] != "")
    {
        $query->andWhere("doi = ? ", $values['doi']);
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
    $query->groupBy("b.id, b.doi, b.year, b.title, b.reference");
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
