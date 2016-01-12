<?php

/**
 * Imports filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ImportsFormFilter extends BaseImportsFormFilter
{
  public function configure()
  {
    $this->useFields(array('collection_ref', 'state','filename')) ;
    $this->addPagerItems();    
    $collection_list = Doctrine::getTable('Collections')->getAllAvailableCollectionsFor($this->options['user']->getId()) ;
    $state_list = Imports::getStateList() ;
    /* Widgets */
    $this->widgetSchema['collection_ref'] = new sfWidgetFormChoice(
      array(
        'choices' => $collection_list
      )
    );

    $this->widgetSchema['state'] = new sfWidgetFormChoice(
      array(
        'choices' => $state_list
      )
    );
    $this->widgetSchema['filename'] = new sfWidgetFormInputText() ;
    $this->widgetSchema['filename']->setAttributes(array('class'=>'small_size'));
    /* Labels */
    $this->widgetSchema->setLabels(array('collection_ref' => 'Collections',
                                         'filename' => 'Filename',
                                         'state' => 'State',
                                        )
                                  );

    /* validators */
    $this->validatorSchema['collection_ref'] = new sfValidatorChoice(
      array('choices'=> array_keys($collection_list)));

    $this->widgetSchema['show_finished']  = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['show_finished'] = new sfValidatorBoolean(array('required' => false));
  }

  public function addShowFinishedColumnQuery(Doctrine_Query $query, $field, $value)
  {
    if ($value == "")
    {
      $query->andWhere("is_finished = false");
    }
  }

  public function doBuildQuery(array $values)
  {
    $query = DQ::create()
      ->from('Imports i')
      ->innerJoin("i.Collections")
      ->where('i.state != ?', 'deleted')
      ->andWhere('i.format = ?','abcd');;
    $this->addShowFinishedColumnQuery($query, 'is_finished', $values['show_finished']);
    if($values['collection_ref'] != 0) $query->addWhere('i.collection_ref = ?', $values['collection_ref']) ;
    if($values['filename']) $query->addWhere('i.filename LIKE \'%'.$values['filename'].'%\'');
    if($values['state']) $query->addWhere('i.state = ?', $values['state']) ;
    // here, add where clause to look for import file only where the user have right on collection
    $query->andWhereIn('collection_ref',array_keys(
      Doctrine::getTable('Collections')->getAllAvailableCollectionsFor($this->options['user']->getId(),$this->options['user']->isA(Users::ADMIN)))
    );

    return $query ;
  }
}
