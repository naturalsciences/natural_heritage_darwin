<?php

/**
 * Comments filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CommentsFormFilter extends BaseCommentsFormFilter
{
  public function configure()
  {
    $this->useFields(array('referenced_relation','comment','notion_concerned'));
    $this->addPagerItems();
    //Find smth for loans, loan_items, collections, spec?
    $this->allowed_relations = array('','specimens', 'expeditions','taxonomy', 'lithology','lithostratigraphy',
      'chronostratigraphy', 'mineralogy', 'people', 'insurances', 'igs', 'gtu', 'bibliography',
    );
    $this->allowed_relations = array_combine($this->allowed_relations, $this->allowed_relations );

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormChoice(array('choices'=> $this->allowed_relations));
    $this->validatorSchema['referenced_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($this->allowed_relations)));

    $this->widgetSchema['comment'] = new sfWidgetFormInput();
    $this->validatorSchema['comment'] = new sfValidatorString(array('required' => false));

    $choices = array(''=>'');
    foreach($this->allowed_relations as $relation) {
      $choices = $choices + CommentsTable::getNotionsFor($relation);
    }

    $this->widgetSchema['notion_concerned'] = new sfWidgetFormChoice(array('choices'=> $choices));
    $this->validatorSchema['notion_concerned'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($choices)));

    $this->widgetSchema->setLabels(array(
      'referenced_relation' => 'Linked Info',
    ));
  }


  public function addCustomReferencedRelationColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere("referenced_relation = ?", $val);
    } else {
      $query->andWhereIn('referenced_relation', array_keys($this->allowed_relations));
    }
    return $query ;
  }

  public function addCommentColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere('comment_indexed like concat(\'%\', fulltoindex(?), \'%\' )', $val);
    }
    return $query ;
  }

  public function addNotionConcernedColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere("notion_concerned = ?", $val);
    }
    return $query ;
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $this->addCustomReferencedRelationColumnQuery($query, 'referenced_relation', $values['referenced_relation']);
    return $query ;
  }
}
