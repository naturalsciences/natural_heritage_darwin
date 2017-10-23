<?php

/**
 * CatalogueBibliography form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CatalogueBibliographyForm extends BaseCatalogueBibliographyForm
{
  public function configure()
  {
    $this->useFields(array('referenced_relation', 'record_id','bibliography_ref'));

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['bibliography_ref'] = new widgetFormJQueryDLookup(
      array(
        'model' => 'Bibliography',
        'method' => 'getTitle',
        'nullable' => false,
        'fieldsHidders' => array(''),
      ),
      array('class' => 'hidden',)
    );
    $this->widgetSchema['bibliography_ref']->setLabel('Bibliography');
   
  }
}
