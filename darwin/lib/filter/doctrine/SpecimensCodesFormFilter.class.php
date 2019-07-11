<?php

/**
 * SpecimensMaincodesFormFilter filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SpecimensMaincodesFormFilter extends SpecimensFormFilter
{
	  /**
   * @see SpecimensFormFilter
   */
   protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('specimens_maincodes_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensMaincodes';
  }
}