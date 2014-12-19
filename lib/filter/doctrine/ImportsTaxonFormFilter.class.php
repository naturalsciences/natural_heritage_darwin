<?php

/**
 * Imports filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ImportsTaxonFormFilter extends BaseImportsFormFilter
{
  public function configure()
  {
    $this->useFields(array('state','filename')) ;
    $this->addPagerItems();    

    $state_list = Imports::getStateList() ;
    /* Widgets */

    $this->widgetSchema['state'] = new sfWidgetFormChoice(
      array(
        'choices' => $state_list
      )
    );
    $this->widgetSchema['filename'] = new sfWidgetFormInputText() ;
    $this->widgetSchema['filename']->setAttributes(array('class'=>'small_size'));
    /* Labels */
    $this->widgetSchema->setLabels(array('filename' => 'Filename',
                                         'state' => 'State',
                                        )
                                  );

    /* validators */

    $this->widgetSchema['show_finished']  = new sfWidgetFormInputCheckbox();
    $this->setDefault('show_finished', true);
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
      ->where('i.state != ?', 'deleted')
      ->andWhere('i.format = ?','taxon');
    $this->addShowFinishedColumnQuery($query, 'is_finished', $values['show_finished']);
    if($values['filename']) $query->addWhere('i.filename LIKE \'%'.$values['filename'].'%\'');
    if($values['state']) $query->addWhere('i.state = ?', $values['state']) ;
    if(!$this->options['user']->isA(Users::ADMIN)) $query->addWhere('i.user_ref = ?',$this->options['user']->getId()) ;
    return $query ;
  }
}
