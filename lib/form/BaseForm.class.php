<?php

/**
 * Base project form.
 * 
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be> 
 * @version    SVN: $Id: BaseForm.class.php 20147 2009-07-13 11:46:57Z FabianLange $
 */
class BaseForm extends sfFormSymfony
{
  protected function getI18N()
  {
     return sfContext::getInstance()->getI18N();
  }

  public function setup()
  {
    sfWidgetFormSchema::setDefaultFormFormatterName('list');
  }

  public function getCurrentCulture()
  {
    return sfContext::getInstance()->getUser()->getCulture();
  }

  public function translateValues($tbl)
  {
    foreach($tbl as $k => $v)
    {
      $tbl[$k] = $this->getI18N()->__($v);
    }
    return $tbl;
  }
}
