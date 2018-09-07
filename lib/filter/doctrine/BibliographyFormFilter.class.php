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

    $choices = array_merge(array(''=>''),Bibliography::getAvailableTypes());
    $this->widgetSchema['type'] =  new sfWidgetFormChoice(array(
      'choices' =>  $choices,
    ));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required'=>false,'choices'=>array_keys($choices)));
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    $this->addNamingColumnQuery($query, 'bibliography', 'title_indexed', $values['title']);
    $query->andWhere("id > 0 ");
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
