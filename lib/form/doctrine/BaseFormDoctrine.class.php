<?php

/**
 * Project form base class.
 *
 * @package    form
 * @version    SVN: $Id: sfDoctrineFormBaseTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class BaseFormDoctrine extends sfFormDoctrine
{
  public function setup()
  {
    sfWidgetFormSchema::setDefaultFormFormatterName('list');
  }

  protected function getI18N()
  {
     return sfContext::getInstance()->getI18N();
  }

  public function getCurrentCulture()
  {
    return sfContext::getInstance()->getUser()->getCulture();
  }

  /**
  * Bind
  * @see getFieldsByGroup
  */
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $fields_groups = $this->getFieldsByGroup();
    foreach($fields_groups as $group)
    {
      $cnt_unset = 0;
      foreach($group as $field)
      {
        if(!isset($taintedValues[$field]))
        {
          $cnt_unset++;
        }
        if($cnt_unset == count($group))
        {
          foreach($group as $ufield)
          {
            $this->offsetUnset($ufield);
          }
        }
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  /**
  * Get fields by widget
  * In the bind function if 1 field (or more) of the group is not sended in tainted values,
  * we remove the whole group from the form
  * @return array of widget name with array of widgets
  * @see bind
  */
  protected function getFieldsByGroup()
  {
    return array();
  }

}
