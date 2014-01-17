<?php

/**
 * Multimedia filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MultimediaFormFilter extends BaseMultimediaFormFilter
{
  public function configure()
  {
    $this->useFields(array('referenced_relation','title','type'));
    $this->addPagerItems();
    //Find smth for loans, loan_items, collections, spec?
    $this->allowed_relations = array('','specimens', 'expeditions','taxonomy', 'lithology','lithostratigraphy',
      'chronostratigraphy', 'mineralogy', 'people', 'insurances', 'igs', 'gtu', 'bibliography',
    );
    $this->allowed_relations = array_combine($this->allowed_relations, $this->allowed_relations );

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormChoice(array('choices'=> $this->allowed_relations));
    $this->validatorSchema['referenced_relation'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($this->allowed_relations)));

    $this->widgetSchema['title'] = new sfWidgetFormInput();
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false));

    $choices = array(''=>'', '.txt'=>'Text', '.jpg' => 'Jpeg', '.pdf' => 'Pdf');

    $this->widgetSchema['type'] = new sfWidgetFormChoice(array('choices'=> $choices));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required'=>false, 'choices'=> array_keys($choices)));

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

  public function addTitleColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere('search_indexed like concat(\'%\', fulltoindex(?), \'%\' )', $val);
    }
    return $query ;
  }

  public function addTypeColumnQuery($query, $field, $val)
  {
    if($val != '') {
      $query->andWhere("type = ?", $val);
    }
    return $query ;
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $this->encoding_collection = $this->getCollectionWithRights($this->options['user'],true);
    $this->addCustomReferencedRelationColumnQuery($query, 'referenced_relation', $values['referenced_relation']);
    $query->andWhere("case WHEN referenced_relation ='specimens' THEN EXISTS( select 1 from specimens ss where ss.id = record_id and collection_ref in (".implode(',',$this->encoding_collection).")) ELSE TRUE END");
    return $query ;
  }
}
